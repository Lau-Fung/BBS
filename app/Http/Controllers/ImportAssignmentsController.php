<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use App\Models\{
    Assignment, Carrier, Device, DeviceModel, Sensor, SensorModel, Sim, Vehicle
};

class ImportAssignmentsController extends Controller
{
    public function form()
    {
        return view('imports.assignments.form');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'file' => ['required','file','mimes:xlsx,csv,xls'],
        ]);

        $file = $request->file('file');

        // Read first sheet as raw 2D array (no heading row mapping here)
        $sheets = Excel::toArray(new \App\Imports\GenericArrayImport, $file);
        $raw = $sheets[0] ?? [];

        if (empty($raw)) {
            return back()->withErrors(['file' => 'Could not read the spreadsheet.']);
        }

        // 1) Find actual header row (rows 1..15), by looking for IMEI (Arabic/English)
        $headerRowNum = $this->findHeaderRow($raw, ['IMEI', 'ايمي', 'رقم IMEI']);
        $headerIdx = max(0, $headerRowNum - 1);

        $headersRaw = $raw[$headerIdx] ?? [];
        // Build mapped keys for each header cell
        $mappedKeys = [];
        foreach ($headersRaw as $cell) {
            $mappedKeys[] = $this->translateHeader((string) $cell) ?? trim((string) $cell);
        }

        // 2) Slice data rows after header
        $dataRows = array_slice($raw, $headerIdx + 1);

        // 3) Convert each row to assoc by mapped keys, drop empty rows, coerce types
        $rows = [];
        foreach ($dataRows as $r) {
            if ($this->isEmptyRow($r)) {
                continue;
            }
            // Normalize column count mismatch
            $r = array_values($r);
            $assoc = [];
            foreach ($mappedKeys as $i => $key) {
                $assoc[$key] = $r[$i] ?? null;
            }
            $rows[] = $this->coerceRow($assoc);
        }

        // 4) Fail fast if required columns are missing
        $requiredCols = ['imei']; // add more if they must exist in the sheet
        $missing = array_diff($requiredCols, $this->presentColumns($mappedKeys));
        if (!empty($missing)) {
            return view('imports.assignments.preview', [
                'fileName'   => $file->getClientOriginalName(),
                'summary'    => ['total' => count($rows), 'valid' => 0, 'invalid' => 0],
                'headers'    => $headersRaw,
                'mappedKeys' => $mappedKeys,
                'rows'       => $rows,
                'fatal'      => ['Missing required column(s): ' . implode(', ', array_map('strtoupper', $missing))],
                'issues'     => [],
                'canConfirm' => false,
            ]);
        }

        // 5) Duplicate detection (in-file only)
        $dupCounts = $this->duplicateCounts($rows, ['imei','sim_serial','msisdn']);

        // 6) Row validation
        $valid = [];
        $issues = [];
        foreach ($rows as $i => $row) {
            // Excel-like row number (headerRowNum + 1 = first data row)
            $excelRow = $headerRowNum + 1 + $i;
            [$ok, $clean, $errs] = $this->validateRow($row, $excelRow, $dupCounts);
            if ($ok) {
                $valid[] = $clean;
            } else {
                $issues[] = ['row' => $excelRow, 'messages' => $errs];
            }
        }

        // Store $valid in session (or cache) for confirm step
        session()->put('import.assignments.valid', $valid);

        return view('imports.assignments.preview', [
            'fileName'   => $file->getClientOriginalName(),
            'summary'    => ['total' => count($rows), 'valid' => count($valid), 'invalid' => count($issues)],
            'headers'    => $headersRaw,
            'mappedKeys' => $mappedKeys,
            'rows'       => $rows,
            'issues'     => $issues,
            'fatal'      => [],
            'canConfirm' => empty($issues),
        ]);
    }
    
    public function confirm(Request $request)
    {
        // Validated rows from preview()
        $rows = session()->pull('import.assignments.valid', []);
        if (empty($rows)) {
            return redirect()
                ->route('imports.assignments.form')
                ->withErrors(['file' => 'Nothing to import or the preview had issues.']);
        }

        // ---- Precollect keys to prefetch/create base lookups ----
        $imeiSet       = collect($rows)->pluck('imei')->filter()->unique()->values();
        $plateSet      = collect($rows)->pluck('plate')->filter()->unique()->values();
        $sensorIdSet   = collect($rows)->pluck('sensor')->filter()->unique()->values(); // serial_or_bt_id
        $modelNames    = collect($rows)->map(fn($r) => $r['model'] ?? $r['device_type'] ?? null)->filter()->unique()->values();
        $carrierNames  = collect($rows)->map(fn($r) => $this->pickCarrierName($r))->filter()->unique()->values();

        // Ensure a fallback model/carrier exists if needed
        if ($modelNames->isEmpty()) {
            $modelNames = collect(['UNKNOWN']);
        }
        if ($carrierNames->isEmpty()) {
            $carrierNames = collect(['UNKNOWN']);
        }

        // ---- Prefetch core lookups ----
        $deviceModels = DeviceModel::whereIn('name', $modelNames)->get()->keyBy('name');
        $carriers     = Carrier::whereIn('name', $carrierNames)->get()->keyBy('name');

        // Create missing device models quickly (manufacturer optional)
        foreach ($modelNames as $mn) {
            if (!$deviceModels->has($mn)) {
                $dm = DeviceModel::create(['name' => $mn]);
                $deviceModels->put($mn, $dm);
            }
        }
        // Create missing carriers
        foreach ($carrierNames as $cn) {
            if (!$carriers->has($cn)) {
                $c = Carrier::create(['name' => $cn]);
                $carriers->put($cn, $c);
            }
        }

        // Prefetch existing entities keyed on their natural keys
        $devices  = Device::whereIn('imei', $imeiSet)->get()->keyBy('imei');
        $vehicles = $plateSet->isNotEmpty()
            ? Vehicle::whereIn('plate', $plateSet)->get()->keyBy('plate')
            : collect();
        $sensors  = $sensorIdSet->isNotEmpty()
            ? Sensor::whereIn('serial_or_bt_id', $sensorIdSet)->get()->keyBy('serial_or_bt_id')
            : collect();

        $created = ['devices'=>0,'sims'=>0,'vehicles'=>0,'sensors'=>0,'assignments'=>0];
        $updated = ['devices'=>0,'sims'=>0,'vehicles'=>0,'sensors'=>0,'assignments'=>0];

        DB::transaction(function () use ($rows, $deviceModels, $carriers, &$devices, &$vehicles, &$sensors, &$created, &$updated) {

            foreach ($rows as $row) {
                // ---------- DEVICE (required) ----------
                $imei = (string) $row['imei'];
                $modelName = $row['model'] ?? $row['device_type'] ?? 'UNKNOWN';
                /** @var \App\Models\DeviceModel $deviceModel */
                $deviceModel = $deviceModels->get($modelName);
                if (!$deviceModel) {
                    $deviceModel = DeviceModel::firstOrCreate(['name' => $modelName]);
                    $deviceModels->put($modelName, $deviceModel);
                }

                /** @var \App\Models\Device $device */
                $device = $devices->get($imei);
                if (!$device) {
                    $device = new Device(['imei' => $imei]);
                }
                // Required FK
                $device->device_model_id = $deviceModel->id;
                // Optional device fields (present in your schema)
                if (!empty($row['firmware'])) {
                    $device->firmware = (string)$row['firmware'];
                }
                if (array_key_exists('is_active', $row)) {
                    $device->is_active = $this->toBool($row['is_active']);
                }

                $wasExisting = $device->exists;
                $device->save();
                $wasExisting ? $updated['devices']++ : $created['devices']++;
                $devices->put($imei, $device);

                // ---------- SIM (optional) ----------
                $sim = null;
                $simSerial = isset($row['sim_serial']) ? trim((string)$row['sim_serial']) : '';
                $msisdn    = isset($row['msisdn'])     ? trim((string)$row['msisdn'])     : '';

                if ($simSerial !== '' || $msisdn !== '') {
                    // Carrier is required on sims table
                    $carrierName = $this->pickCarrierName($row) ?? 'UNKNOWN';
                    $carrier = $carriers->get($carrierName);
                    if (!$carrier) {
                        $carrier = Carrier::firstOrCreate(['name' => $carrierName]);
                        $carriers->put($carrierName, $carrier);
                    }

                    // Match existing SIM by sim_serial first, then msisdn
                    if ($simSerial !== '') {
                        $sim = Sim::where('sim_serial', $simSerial)->first();
                    }
                    if (!$sim && $msisdn !== '') {
                        $sim = Sim::where('msisdn', $msisdn)->first();
                    }

                    if (!$sim) {
                        $sim = new Sim(['carrier_id' => $carrier->id]);
                    }
                    // Assign natural keys/fields if provided
                    if ($simSerial !== '') $sim->sim_serial = $simSerial;
                    if ($msisdn    !== '') $sim->msisdn     = $msisdn;

                    $sim->carrier_id = $carrier->id;
                    if (array_key_exists('is_active', $row)) {
                        $sim->is_active = $this->toBool($row['is_active']);
                    }

                    $existed = $sim->exists;
                    $sim->save();
                    $existed ? $updated['sims']++ : $created['sims']++;
                }

                // ---------- VEHICLE (optional) ----------
                $vehicle = null;
                if (!empty($row['plate'])) {
                    $plate = trim((string)$row['plate']);
                    $vehicle = $vehicles->get($plate) ?? new Vehicle(['plate' => $plate]);

                    // Optional vehicle fields present in your schema
                    if (!empty($row['vehicle_status'])) {
                        $vehicle->status = $row['vehicle_status']; // must be one of the enum values
                    }
                    if (!empty($row['vehicle_notes'])) {
                        $vehicle->notes = $row['vehicle_notes'];
                    }

                    $existed = $vehicle->exists;
                    $vehicle->save();
                    $existed ? $updated['vehicles']++ : $created['vehicles']++;
                    $vehicles->put($plate, $vehicle);
                }

                // ---------- SENSOR (optional) ----------
                $sensor = null;
                if (!empty($row['sensor'])) {
                    $sid = trim((string)$row['sensor']); // treat as serial_or_bt_id from sheet
                    $sensor = $sensors->get($sid) ?? new Sensor(['serial_or_bt_id' => $sid]);

                    // Optional sensor model notes (if you map them later)
                    if (!empty($row['sensor_note'])) {
                        $sensor->notes = $row['sensor_note'];
                    }

                    $existed = $sensor->exists;
                    $sensor->save();
                    $existed ? $updated['sensors']++ : $created['sensors']++;
                    $sensors->put($sid, $sensor);
                }

                // ---------- ASSIGNMENT (single active per device) ----------
                // Try to find the current active assignment; include trashed to avoid unique(index) conflicts
                /** @var \App\Models\Assignment|null $assignment */
                $assignment = Assignment::withTrashed()
                    ->where('device_id', $device->id)
                    ->where('is_active', true)
                    ->first();

                if ($assignment && $assignment->trashed()) {
                    // If a soft-deleted row still has is_active=true, restore and reuse it
                    $assignment->restore();
                }

                if (!$assignment) {
                    $assignment = new Assignment(['device_id' => $device->id, 'is_active' => true]);
                }

                // Link everything
                $assignment->sim_id     = $sim?->id;
                $assignment->vehicle_id = $vehicle?->id;
                $assignment->sensor_id  = $sensor?->id;

                // Dates & flags from sheet
                $assignment->installed_on = $row['installed_on'] ?? $assignment->installed_on;
                $assignment->removed_on   = $row['removed_on']   ?? $assignment->removed_on;
                $assignment->install_note = $row['note'] ?? $row['install_note'] ?? $assignment->install_note;

                // is_installed: true when installed_on present and removed_on empty
                $assignment->is_installed = !empty($assignment->installed_on) && empty($assignment->removed_on);

                $existed = $assignment->exists;
                $assignment->save();
                $existed ? $updated['assignments']++ : $created['assignments']++;
            }
        });

        $msg = sprintf(
            'Import completed: devices +%d/%d, sims +%d/%d, vehicles +%d/%d, sensors +%d/%d, assignments +%d/%d.',
            $created['devices'], $updated['devices'],
            $created['sims'],    $updated['sims'],
            $created['vehicles'],$updated['vehicles'],
            $created['sensors'], $updated['sensors'],
            $created['assignments'], $updated['assignments']
        );

        return redirect()->route('imports.assignments.form')->with('status', $msg);
    }

    /** Convert Arabic/English yes/no-ish to bool(0/1) */
    private function toBool($v): int
    {
        $t = trim(mb_strtolower((string)$v,'UTF-8'));
        return in_array($t, ['نعم','yes','y','1','true','فعال','نشط'], true) ? 1 : 0;
    }

    /** Prefer an explicit carrier name from the row; fall back to 'UNKNOWN' */
    private function pickCarrierName(array $row): ?string
    {
        foreach (['carrier','sim_carrier','carrier_name','نوع_الشريحة','نوع_الباقة'] as $k) {
            if (!empty($row[$k])) return trim((string)$row[$k]);
        }
        // Sometimes carrier is embedded in notes/columns — return null to avoid creating "UNKNOWN" unless a SIM exists
        return null;
    }


    /* ========================= Helpers ========================= */

    private function findHeaderRow(array $sheetRows, array $requiredAnyOf): int
    {
        $maxScan = min(15, count($sheetRows));
        for ($i = 0; $i < $maxScan; $i++) {
            $row = $sheetRows[$i] ?? [];
            foreach ($row as $cell) {
                if ($cell === null) continue;
                $c = trim(mb_strtolower((string)$cell, 'UTF-8'));
                foreach ($requiredAnyOf as $needle) {
                    $n = trim(mb_strtolower($needle, 'UTF-8'));
                    if ($c === $n) {
                        return $i + 1; // 1-based
                    }
                }
            }
        }
        return 1; // fallback
    }

    private function presentColumns(array $keys): array
    {
        return array_values(array_filter(array_map(function ($k) {
            return $k ? trim((string)$k) : null;
        }, $keys)));
    }

    private function translateHeader(string $column): ?string
    {
        $c = trim(mb_strtolower($column, 'UTF-8'));
        $c = preg_replace('/\s+/u', '_', $c);

        $map = [
            'imei'           => ['imei','ايمي','رقم_imei'],
            'sim_serial'     => ['sim_serial','رقم_الشريحة','icc','iccid'],
            'msisdn'         => ['msisdن','msisdn','رقم_الهاتف','جوال','هاتف'],
            'plate'          => ['plate','لوحة','رقم_اللوحة'],
            'sensor'         => ['sensor','حساس','الحساس'],
            'model'          => ['model','موديل_الجهاز'],
            'manufacturer'   => ['manufacturer','اسم_الشركة_المصنعة_للجهاز'],
            'subscription'   => ['subscription_type','نوع_الاشتراك','نظام_البيع'],
            'note'           => ['note','notes','ملاحظات','ملاحظة'],
            'installed_on'   => ['installed_on','تاريخ_التركيب','تاريخ_التثبيت'],
            'removed_on'     => ['removed_on','تاريخ_الازالة','تاريخ_الإزالة'],
            'year'           => ['year','السنة'],
            'serial'         => ['serial','رقم_الدرجة','رقم_الجهاز'],
            'count'          => ['count','qty','العدد'],
        ];

        foreach ($map as $std => $aliases) {
            foreach ($aliases as $a) {
                if ($c === $a) return $std;
            }
        }
        return null;
    }

    private function isEmptyRow(array $row): bool
    {
        foreach ($row as $v) {
            if (trim((string)$v) !== '') return false;
        }
        return true;
    }

    private function coerceRow(array $row): array
    {
        // Always treat identifiers as strings to avoid scientific notation
        foreach (['imei','sim_serial','msisdn','serial','plate'] as $k) {
            if (array_key_exists($k, $row)) {
                $row[$k] = isset($row[$k]) ? (string)$row[$k] : '';
            }
        }

        // Dates -> Y-m-d
        foreach (['installed_on','removed_on'] as $k) {
            if (!empty($row[$k])) {
                if (is_numeric($row[$k])) {
                    $row[$k] = ExcelDate::excelToDateTimeObject($row[$k])->format('Y-m-d');
                } else {
                    $ts = strtotime((string)$row[$k]);
                    if ($ts !== false) {
                        $row[$k] = date('Y-m-d', $ts);
                    }
                }
            }
        }

        return $row;
    }


    private function duplicateCounts(array $rows, array $keys): array
    {
        $counts = [];
        foreach ($keys as $k) $counts[$k] = [];
        foreach ($rows as $row) {
            foreach ($keys as $k) {
                $v = trim((string)($row[$k] ?? ''));
                if ($v === '') continue;
                $counts[$k][$v] = ($counts[$k][$v] ?? 0) + 1;
            }
        }
        return $counts;
    }

    private function validateRow(array $row, int $rowNumber, array $dupCounts = []): array
    {
        $rules = [
            'imei'         => ['required','string','size:15'], // adjust if needed
            'msisdn'       => ['nullable','string'],
            'sim_serial'   => ['nullable','string'],
            'installed_on' => ['nullable','date'],
            'removed_on'   => ['nullable','date'],
        ];

        $v = Validator::make($row, $rules);
        $messages = $v->fails() ? $v->errors()->all() : [];

        foreach (['imei','sim_serial','msisdn'] as $k) {
            $val = trim((string)($row[$k] ?? ''));
            if ($val !== '' && ($dupCounts[$k][$val] ?? 0) > 1) {
                $messages[] = strtoupper($k)." '$val' appears more than once in this file.";
            }
        }

        if (!empty($messages)) return [false, [], $messages];

        return [true, $row, []];
    }
}
