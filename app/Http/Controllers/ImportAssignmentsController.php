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
use App\Exports\AssignmentsExport;
use Maatwebsite\Excel\Excel as ExcelWriter;

class ImportAssignmentsController extends Controller
{
    // Which columns are required per row in the preview
    private const REQUIRED = [
        'imei'       => true,
        'sim_serial' => false,
        'msisdn'     => false,
        'plate'      => false,
        'sensor'     => false,
    ];

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
        $defaultClientId = $request->integer('client_id') ?: null;

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

        // NEW: read banner values and persist for confirm()
        $sheetDefaults = $this->extractBannerDefaultsFromArray($raw);
        session()->put('import.assignments.defaults', $sheetDefaults);
        // allow an explicit client picked on the form to override
        $defaultClientId = $request->integer('client_id') ?: null;

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
                if (!$key) continue;
                $assoc[$key] = $r[$i] ?? null;
            }
            $rows[] = $this->coerceRow($assoc);
        }

        // 4) Fail fast if required columns are missing
        $requiredCols = ['imei']; // add more if they must exist in the sheet
        $missing = array_diff($requiredCols, $this->presentColumns($mappedKeys));
        Log::info('mappedKeys', $mappedKeys);

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
                'sheetDefaults' => $sheetDefaults,
                'defaultClientId' => $defaultClientId,
            ]);
        }

        // 5) Duplicate detection (in-file only)
        $dupCounts = $this->duplicateCounts($rows, ['imei','sim_serial','msisdn','plate','sensor']);
        // Prefetch existing values from DB (for unique/conflict flags)
        $vals = fn(string $k) => collect($rows)->pluck($k)->filter()->unique()->values();

        $exists = [
            'imei'       => Device::whereIn('imei', $vals('imei'))->pluck('imei')->flip()->all(),
            'sim_serial' => Sim::whereIn('sim_serial', $vals('sim_serial'))->pluck('sim_serial')->flip()->all(),
            'msisdn'     => Sim::whereIn('msisdn', $vals('msisdn'))->pluck('msisdn')->flip()->all(),
            'plate'      => Vehicle::whereIn('plate', $vals('plate'))->pluck('plate')->flip()->all(),
            'sensor'     => Sensor::whereIn('serial_or_bt_id', $vals('sensor'))->pluck('serial_or_bt_id')->flip()->all(),
        ];


        // 6) Row validation
        $valid = [];
        $issues = [];
        $cellErrors = []; // [rowIndex => [field => 'required'|'format'|'dup-file'|'dup-db']]

        foreach ($rows as $i => $row) {
            $excelRow = $headerRowNum + 1 + $i;

            [$ok, $clean, $errs, $fieldErrs] = $this->validateRow($row, $excelRow, $dupCounts, $exists);

            if ($ok) {
                $valid[] = $clean;
            }
            if (!empty($errs)) {
                $issues[] = ['row' => $excelRow, 'messages' => $errs];
            }

            $cellErrors[$i] = $fieldErrs;
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
            'cellErrors' => $cellErrors,
            'requiredMap'=> self::REQUIRED,
            'defaultClientId' => $defaultClientId,
            'sheetDefaults' =>$sheetDefaults
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

        // pick a default client from the form (select)
        $defaultClient = null;
        if ($request->filled('client_id')) {
            $defaultClient = \App\Models\Client::find($request->integer('client_id'));
        }

        // 1) read defaults from session (reliable), fallback to request hidden inputs
        $sheetDefaults = session()->pull('import.assignments.defaults', []);
        if (empty($sheetDefaults)) {
            $sheetDefaults = [
                'client_name'       => trim((string)$request->input('default_client_name', '')),
                'sector'            => trim((string)$request->input('default_sector', '')),
                'subscription_type' => trim((string)$request->input('default_subscription_type', '')),
            ];
        }

        $bannerClient = null;
        if ($sheetDefaults['client_name'] !== '') {
            $bannerClient = \App\Models\Client::firstOrCreate(
                ['name' => $sheetDefaults['client_name']],
                [
                    'sector'            => $sheetDefaults['sector'] ?: null,
                    'subscription_type' => $this->mapSubscription($sheetDefaults['subscription_type']),
                ]
            );
        }

        // whichever exists: banner default beats form default (you can flip this if you prefer)
        $globalDefaultClient = $bannerClient ?: $defaultClient;

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

        DB::transaction(function () use ($rows, $deviceModels, $carriers, &$devices, &$vehicles, &$sensors, &$created, &$updated, $globalDefaultClient) {
            $client = $globalDefaultClient;

            foreach ($rows as $row) {
                // row-level override?
                $nameFromRow = trim((string)($row['client_name'] ?? ''));
                if ($nameFromRow !== '') {
                    static $clientsCache; if ($clientsCache === null) $clientsCache = collect();
                    $client = $clientsCache->get($nameFromRow);
                    if (!$client) {
                        $client = \App\Models\Client::firstOrCreate(
                            ['name' => $nameFromRow],
                            [
                                'sector'            => $row['sector'] ?? null,
                                'subscription_type' => $this->mapSubscription($row['subscription_type'] ?? null),
                            ]
                        );
                        $clientsCache->put($nameFromRow, $client);
                    } else {
                        // Optionally hydrate missing meta (only if blank on record)
                        $dirty = false;
                        if (!$client->sector && !empty($row['sector'])) {
                            $client->sector = $row['sector']; $dirty = true;
                        }
                        $sub = $this->mapSubscription($row['subscription_type'] ?? null);
                        if (!$client->subscription_type && $sub) {
                            $client->subscription_type = $sub; $dirty = true;
                        }
                        if ($dirty) $client->save();
                    }
                }

                // ---------- DEVICE (required) ----------
                $imei = (string) $row['imei'];
                $modelName = $row['model'] ?? $row['device_type'] ?? $row['device_model'] ?? 'UNKNOWN';
                $modelName = $modelName ? strtoupper(trim($modelName)) : 'UNKNOWN';
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

                    // attach client if we have one
                    if ($client) $vehicle->client_id = $client->id;

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

    /**
     * Pick the longest non-empty text in a row.
     * Good for merged header rows where the client name/sector appear once.
     */
    private function pickLongestTextInRow(array $row): ?string
    {
        $best = '';
        foreach ($row as $cell) {
            $t = trim((string)$cell);
            if ($t !== '' && mb_strlen($t, 'UTF-8') > mb_strlen($best, 'UTF-8')) {
                $best = $t;
            }
        }
        return $best ?: null;
    }

    /** Map Arabic/English subscription text to our enum */
    private function mapSubscription(?string $v): ?string
    {
        if ($v === null) return null;
        $t = trim(mb_strtolower($v, 'UTF-8'));
        if ($t === '') return null;

        // Arabic
        if (preg_match('/شهري/u', $t))  return 'monthly';
        if (preg_match('/سنوي/u', $t))  return 'yearly';
        if (preg_match('/إ?يجار/u', $t)) return 'lease';

        // English
        if (str_contains($t, 'monthly')) return 'monthly';
        if (str_contains($t, 'yearly') || str_contains($t, 'annual')) return 'yearly';
        if (str_contains($t, 'lease')) return 'lease';

        return null;
    }

    /**
     * Extract banner defaults (client name, sector, subscription type)
     * from the first 2–3 rows of the first worksheet read by `Excel::toArray`.
     */
    private function extractBannerDefaultsFromArray(array $raw): array
    {
        $row1 = $raw[0] ?? [];  // first row in sheet (0-based)
        $row2 = $raw[1] ?? [];  // second row
        $row3 = $raw[2] ?? [];  // third row (sometimes labels end up here)

        // 1) client + sector as "longest text" on rows 1 and 2
        $client = $this->pickLongestTextInRow($row1);
        $sector = $this->pickLongestTextInRow($row2);

        // 2) subscription type — look for a label like "نوع الاشتراك / subscription type"
        // and take the cell to the right; otherwise detect value directly.
        $subscription = null;

        $scanRows = [$row1, $row2, $row3];
        foreach ($scanRows as $r) {
            foreach ($r as $i => $cell) {
                $t = trim((string)$cell);
                if ($t === '') continue;

                // label to the left?
                if (preg_match('/نوع\s*الاشتراك|subscription\s*type/i', $t)) {
                    $right = trim((string)($r[$i+1] ?? ''));
                    if ($right !== '') {
                        $subscription = $this->mapSubscription($right);
                        if ($subscription) break 2;
                    }
                }

                // the value itself?
                $maybe = $this->mapSubscription($t);
                if ($maybe) {
                    $subscription = $maybe;
                    break 2;
                }
            }
        }

        return [
            'client_name'       => $client,
            'sector'            => $sector,
            'subscription_type' => $subscription,
        ];
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
        // Prefer already-mapped canonical keys
        foreach (['carrier','sim_carrier','carrier_name'] as $k) {
            if (!empty($row[$k])) {
                $v = trim((string)$row[$k]);
                if ($v !== '') return $v;
            }
        }

        // Fall back to scanning raw headers (normalize to compare)
        $needles = ['carrier','sim_carrier','carrier_name','نوع_الشريحة','نوع_الباقة'];
        $needles = array_map(fn($n) => $this->normHeader($n), $needles);

        foreach ($row as $key => $value) {
            $nk = $this->normHeader((string)$key);
            if (in_array($nk, $needles, true)) {
                $v = trim((string)$value);
                if ($v !== '') return $v;
            }
        }

        return null; // let caller decide whether to use 'UNKNOWN'
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
        $c = $this->normHeader($column);

        $map = [
            'imei'           => ['imei','ايمي','رقم imei'],
            'sim_serial'     => ['sim_serial','رقم الشريحة','icc','iccid'],
            'msisdn'         => ['msisdn','رقم الهاتف','جوال','هاتف'],
            'plate'          => ['plate','لوحة','رقم اللوحة'],
            'sensor'         => ['sensor','حساس','الحساس'],

            // Device model / type
            'model' => [
                'model','device_model','device_type',
                'موديل الجهاز','نوع الجهاز',   // spaced versions are fine
                'موديل_الجهاز','نوع_الجهاز',   // just in case
            ],

            'carrier'        => [
                'carrier','carrier_name','sim_carrier',
                'نوع_الشريحة','نوع الشريحة',
                'نوع_الباقة','نوع الباقة',
                'شركة_الاتصالات','شركة الاتصالات'
            ],

            'manufacturer'   => ['manufacturer','اسم الشركة المصنعة للجهاز','اسم_الشركة_المصنعة_للجهاز'],
            'subscription'   => ['subscription_type','نوع الاشتراك','نظام البيع','نوع_الاشتراك','نظام_البيع'],
            'note'           => ['note','notes','ملاحظات','ملاحظة'],
            'installed_on'   => ['installed_on','تاريخ التركيب','تاريخ التثبيت','تاريخ_التركيب','تاريخ_التثبيت'],
            'removed_on'     => ['removed_on','تاريخ الازالة','تاريخ الإزالة','تاريخ_الازالة','تاريخ_الإزالة'],
            'year'           => ['year','السنة'],
            'serial'         => ['serial','رقم الدرجة','رقم الجهاز','رقم_الدرجة','رقم_الجهاز'],
            'count'          => ['count','qty','العدد'],
        ];

        foreach ($map as $std => $aliases) {
            foreach ($aliases as $a) {
                if ($c === $this->normHeader($a)) {   // <-- normalize alias
                    return $std;
                }
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

    // Put these private helpers in your controller

    /** remove NBSP, tatweel, diacritics; collapse spaces; lower-case */
    private function normHeader(string $s): string
    {
        // normalize common invisible chars
        $s = str_replace(["\xC2\xA0", "\xE2\x80\x8B"], ' ', $s); // NBSP, ZWSP
        $s = str_replace('ـ', '', $s);                            // tatweel
        // unify whitespace -> single underscore
        $s = preg_replace('/\s+/u', '_', trim($s));
        // drop Arabic diacritics
        $s = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $s);
        return mb_strtolower($s, 'UTF-8');
    }

    /** upper-case A–Z/0–9, strip weird punctuation */
    private function normModel(?string $v): ?string
    {
        if ($v === null) return null;
        $v = trim($v);
        // Some sheets have spaces or soft hyphens inside the code
        $v = str_replace(["\xC2\xA0", "\xE2\x80\x8B", ' '], '', $v);
        // Keep only letters/numbers, then upper-case
        $v = preg_replace('/[^0-9a-z]+/i', '', $v);
        return $v ? strtoupper($v) : null;
    }


    private function coerceRow(array $row): array
    {
        // Always treat identifiers as strings to avoid scientific notation
        foreach (['imei','sim_serial','msisdn','serial','plate'] as $k) {
            if (array_key_exists($k, $row)) {
                $row[$k] = isset($row[$k]) ? (string)$row[$k] : '';
            }
        }

        if (array_key_exists('model', $row)) {
            $row['model'] = $this->normModel((string) $row['model']);
        }
        if (array_key_exists('device_type', $row)) {
            $row['device_type'] = $this->normModel((string) $row['device_type']);
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

    private function validateRow(
        array $row,
        int $rowNumber,
        array $dupCounts = [],
        array $exists = []
    ): array {
        $messages  = [];
        $fieldErrs = []; // field => 'required'|'format'|'dup-file'|'dup-db'

        // ----- REQUIRED -----
        foreach (self::REQUIRED as $field => $req) {
            if ($req && empty($row[$field])) {
                $fieldErrs[$field] = 'required';
                $messages[] = strtoupper($field) . " is required.";
            }
        }
        // If IMEI missing, we can short-circuit (nothing else matters for that row)
        if (!empty($fieldErrs['imei'])) {
            return [false, [], $messages, $fieldErrs];
        }

        // ----- FORMAT -----
        if (!empty($row['imei'])) {
            $s = (string)$row['imei'];
            if (!preg_match('/^\d{14,17}$/', $s)) {
                $fieldErrs['imei'] = 'format';
                $messages[] = "IMEI must be 14–17 digits.";
            }
        }
        if (!empty($row['msisdn'])) {
            $s = (string)$row['msisdn'];
            if (!preg_match('/^\d{5,20}$/', $s)) {
                $fieldErrs['msisdn'] = 'format';
                $messages[] = "MSISDN should be 5–20 digits (digits only).";
            }
        }
        if (!empty($row['sim_serial'])) {
            $s = trim((string)$row['sim_serial']);
            if (!preg_match('/^[A-Za-z0-9\-]+$/', $s)) {
                $fieldErrs['sim_serial'] = 'format';
                $messages[] = "SIM serial allows letters, numbers, and dashes only.";
            }
        }

        // ----- DUPLICATES inside this file -----
        foreach (['imei','sim_serial','msisdn','plate','sensor'] as $k) {
            $v = trim((string)($row[$k] ?? ''));
            if ($v !== '' && ($dupCounts[$k][$v] ?? 0) > 1) {
                $fieldErrs[$k] = $fieldErrs[$k] ?? 'dup-file';
                $messages[] = strtoupper($k) . " '$v' appears more than once in this file.";
            }
        }

        // ----- ALREADY EXISTS in DB (unique conflicts) -----
        if (!empty($row['imei']) && isset($exists['imei'][$row['imei']])) {
            $fieldErrs['imei'] = 'dup-db';
            $messages[] = "IMEI '{$row['imei']}' already exists.";
        }
        if (!empty($row['sim_serial']) && isset($exists['sim_serial'][$row['sim_serial']])) {
            $fieldErrs['sim_serial'] = 'dup-db';
            $messages[] = "SIM serial '{$row['sim_serial']}' already exists.";
        }
        if (!empty($row['msisdn']) && isset($exists['msisdn'][$row['msisdn']])) {
            $fieldErrs['msisdn'] = 'dup-db';
            $messages[] = "MSISDN '{$row['msisdn']}' already exists.";
        }
        if (!empty($row['plate']) && isset($exists['plate'][$row['plate']])) {
            $fieldErrs['plate'] = 'dup-db';
            $messages[] = "Plate '{$row['plate']}' already exists.";
        }
        if (!empty($row['sensor']) && isset($exists['sensor'][$row['sensor']])) {
            $fieldErrs['sensor'] = 'dup-db';
            $messages[] = "Sensor '{$row['sensor']}' already exists.";
        }

        $ok = empty($messages); // strict: any message blocks this row
        return [$ok, $row, $messages, $fieldErrs];
    }


    public function export(Request $request)
    {
        // Accept ?format=xlsx|csv and simple filters
        $format = strtolower($request->get('format', 'xlsx'));
        $writer = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;

        $filters = $request->only([
            'is_active',        // 1 or 0
            'carrier',          // e.g., LEBARA
            'device_model',     // e.g., FMC920
            'installed_from',   // YYYY-MM-DD
            'installed_to',     // YYYY-MM-DD
        ]);

        $file = 'assignments_' . now()->format('Ymd_His') . '.' . $format;

        return Excel::download(
            new AssignmentsExport($filters),
            $file,
            $writer
        );
    }
}
