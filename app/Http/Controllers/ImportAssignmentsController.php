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

    public function __construct()
    {
        $this->middleware('permission:assignments.view')->only(['form']);
        $this->middleware('permission:assignments.create')->only(['preview','confirm']);
        $this->middleware('permission:assignments.export')->only(['export']);
    }
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
            'file' => ['required','file','mimes:xlsx,csv,xls,xlsm'],
        ]);

        $file = $request->file('file');
        $defaultClientId = $request->integer('client_id') ?: null;

        // Read ALL sheets (keeps your current GenericArrayImport)
        $allSheets = Excel::toArray(new \App\Imports\GenericArrayImport, $file);
        if (empty($allSheets)) {
            return back()->withErrors(['file' => 'Could not read the spreadsheet.']);
        }

        $perSheet = [];
        $allValid = [];
        $combinedDefaults = ['client_name'=>null,'sector'=>null,'subscription_type'=>null];

        foreach ($allSheets as $si => $raw) {
            $parsed = $this->parseOneSheet($raw);
            $perSheet[$si] = $parsed;
            $allValid = array_merge($allValid, $parsed['valid']);

            // remember first non-empty banner values across sheets (for display only)
            foreach (['client_name','sector','subscription_type'] as $k) {
                if (!$combinedDefaults[$k] && !empty($parsed['defaults'][$k])) {
                    $combinedDefaults[$k] = $parsed['defaults'][$k];
                }
            }
        }

        // Keep backward compatibility with your current preview view: show SHEET #0
        $first = $perSheet[0] ?? [
            'headers'=>[], 'mappedKeys'=>[], 'rows'=>[], 'issues'=>[], 'fatal'=>[], 'cellErrors'=>[], 'valid'=>[]
        ];

        // Store valid rows: first sheet (old key) + ALL sheets (new key)
        session()->put('import.assignments.valid', $first['valid']);      // old behavior
        session()->put('import.assignments.valid_all', $allValid);        // NEW: all sheets
        session()->put('import.assignments.defaults', $combinedDefaults); // for confirm fallback

        // (Optional) a compact summary per sheet you can show in the view if you want
        $sheetsSummary = collect($perSheet)->map(function ($s, $idx) {
            return [
                'index'   => $idx,
                'total'   => count($s['rows']),
                'valid'   => count($s['valid']),
                'invalid' => count($s['issues']),
                'hasFatal'=> !empty($s['fatal']),
            ];
        })->all();

        return view('imports.assignments.preview', [
            'fileName'       => $file->getClientOriginalName(),
            'summary'        => ['total' => count($first['rows']), 'valid' => count($first['valid']), 'invalid' => count($first['issues'])],
            'headers'        => $first['headers'],
            'mappedKeys'     => $first['mappedKeys'],
            'rows'           => $first['rows'],
            'issues'         => $first['issues'],
            'fatal'          => $first['fatal'],
            'canConfirm'     => empty($first['fatal']) && empty($first['issues']), // same behavior for the first sheet UI
            'cellErrors'     => $first['cellErrors'],
            'requiredMap'    => self::REQUIRED,
            'defaultClientId'=> $defaultClientId,
            'sheetDefaults'  => $combinedDefaults,

            // NEW (optional in your blade)
            'sheetsSummary'  => $sheetsSummary,
        ]);
    }
    
    public function confirm(Request $request)
    {
        // Prefer the multi-sheet rows; fall back to the old key if needed
        $rows = session()->pull('import.assignments.valid_all', []);
        if (empty($rows)) {
            $rows = session()->pull('import.assignments.valid', []);
        }
        if (empty($rows)) {
            return redirect()
                ->route('imports.assignments.form')
                ->withErrors(['file' => 'Nothing to import or the preview had issues.']);
        }

        // default client (optional select on the form)
        $defaultClient = null;
        if ($request->filled('client_id')) {
            $defaultClient = \App\Models\Client::find($request->integer('client_id'));
        }

        // sheet-level defaults (first non-empty values saved during preview)
        $sheetDefaults = session()->pull('import.assignments.defaults', []);
        if (empty($sheetDefaults)) {
            $sheetDefaults = [
                'client_name'       => trim((string)$request->input('default_client_name', '')),
                'sector'            => trim((string)$request->input('default_sector', '')),
                'subscription_type' => trim((string)$request->input('default_subscription_type', '')),
            ];
        }

        $bannerClient = null;
        if (!empty($sheetDefaults['client_name'])) {
            $bannerClient = \App\Models\Client::firstOrCreate(
                ['name' => $sheetDefaults['client_name']],
                [
                    'sector'            => $sheetDefaults['sector'] ?: null,
                    'subscription_type' => $this->mapSubscription($sheetDefaults['subscription_type']),
                ]
            );
        }
        $globalDefaultClient = $bannerClient ?: $defaultClient;

        // ==== from here on your original import logic is unchanged ====
        // (it works because every row already contains client_name/sector/subscription_type
        //  coming from its own sheet; those override the global default when present)

        // ---- Precollect keys ----
        $imeiSet     = collect($rows)->pluck('imei')->filter()->unique()->values();
        $plateSet    = collect($rows)->pluck('plate')->filter()->unique()->values();
        $sensorIdSet = collect($rows)->pluck('sensor')->filter()->unique()->values();
        $modelNames  = collect($rows)->map(fn($r) => $r['model'] ?? $r['device_type'] ?? null)->filter()->unique()->values();
        $carrierNames= collect($rows)->map(fn($r) => $this->pickCarrierName($r))->filter()->unique()->values();

        if ($modelNames->isEmpty())   $modelNames   = collect(['UNKNOWN']);
        if ($carrierNames->isEmpty()) $carrierNames = collect(['UNKNOWN']);

        $deviceModels = DeviceModel::whereIn('name', $modelNames)->get()->keyBy('name');
        $carriers     = Carrier::whereIn('name', $carrierNames)->get()->keyBy('name');

        foreach ($modelNames as $mn) {
            if (!$deviceModels->has($mn)) {
                $dm = DeviceModel::create(['name' => $mn]);
                $deviceModels->put($mn, $dm);
            }
        }
        foreach ($carrierNames as $cn) {
            if (!$carriers->has($cn)) {
                $c = Carrier::create(['name' => $cn]);
                $carriers->put($cn, $c);
            }
        }

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
                // row-level override based on the row’s (sheet’s) client information
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

                // === the rest is identical to your original confirm() ===
                $imei = (string) $row['imei'];
                $modelName = $row['model'] ?? $row['device_type'] ?? $row['device_model'] ?? 'UNKNOWN';
                $modelName = $modelName ? strtoupper(trim($modelName)) : 'UNKNOWN';
                $deviceModel = $deviceModels->get($modelName)
                    ?: tap(DeviceModel::firstOrCreate(['name'=>$modelName]), fn($dm) => $deviceModels->put($modelName,$dm));

                $device = $devices->get($imei) ?: new Device(['imei'=>$imei]);
                $device->device_model_id = $deviceModel->id;
                if (!empty($row['firmware'])) $device->firmware = (string)$row['firmware'];
                if (array_key_exists('is_active',$row)) $device->is_active = $this->toBool($row['is_active']);
                $existed = $device->exists; $device->save();
                $existed ? $updated['devices']++ : $created['devices']++;
                $devices->put($imei,$device);

                $sim = null;
                $simSerial = isset($row['sim_serial']) ? trim((string)$row['sim_serial']) : '';
                $msisdn    = isset($row['msisdn'])     ? trim((string)$row['msisdn'])     : '';
                if ($simSerial !== '' || $msisdn !== '') {
                    $carrierName = $this->pickCarrierName($row) ?? 'UNKNOWN';
                    $carrier = $carriers->get($carrierName) ?: tap(Carrier::firstOrCreate(['name'=>$carrierName]), fn($c) => $carriers->put($carrierName,$c));
                    if ($simSerial !== '') $sim = Sim::where('sim_serial',$simSerial)->first();
                    if (!$sim && $msisdn !== '') $sim = Sim::where('msisdn',$msisdn)->first();
                    if (!$sim) $sim = new Sim(['carrier_id'=>$carrier->id]);
                    if ($simSerial !== '') $sim->sim_serial = $simSerial;
                    if ($msisdn    !== '') $sim->msisdn     = $msisdn;
                    $sim->carrier_id = $carrier->id;
                    if (array_key_exists('is_active',$row)) $sim->is_active = $this->toBool($row['is_active']);
                    $ex2 = $sim->exists; $sim->save();
                    $ex2 ? $updated['sims']++ : $created['sims']++;
                }

                $vehicle = null;
                if (!empty($row['plate'])) {
                    $plate   = trim((string)$row['plate']);
                    $vehicle = $vehicles->get($plate) ?? new Vehicle(['plate'=>$plate]);
                    if ($client) $vehicle->client_id = $client->id;
                    if (!empty($row['vehicle_status'])) $vehicle->status = $row['vehicle_status'];
                    if (!empty($row['vehicle_notes']))  $vehicle->notes  = $row['vehicle_notes'];
                    $ex3 = $vehicle->exists; $vehicle->save();
                    $ex3 ? $updated['vehicles']++ : $created['vehicles']++;
                    $vehicles->put($plate,$vehicle);
                }

                $sensor = null;
                if (!empty($row['sensor'])) {
                    $sid = trim((string)$row['sensor']);
                    $sensor = $sensors->get($sid) ?? new Sensor(['serial_or_bt_id'=>$sid]);
                    if (!empty($row['sensor_note'])) $sensor->notes = $row['sensor_note'];
                    $ex4 = $sensor->exists; $sensor->save();
                    $ex4 ? $updated['sensors']++ : $created['sensors']++;
                    $sensors->put($sid,$sensor);
                }

                $assignment = Assignment::withTrashed()
                    ->where('device_id',$device->id)->where('is_active',true)->first();
                if ($assignment && $assignment->trashed()) $assignment->restore();
                if (!$assignment) $assignment = new Assignment(['device_id'=>$device->id,'is_active'=>true]);

                $assignment->sim_id     = $sim?->id;
                $assignment->vehicle_id = $vehicle?->id;
                $assignment->sensor_id  = $sensor?->id;

                $assignment->installed_on = $this->normalizeDate($row['installed_on'] ?? null);
                $assignment->removed_on   = $this->normalizeDate($row['removed_on']   ?? null);
                $assignment->install_note = $row['note'] ?? $row['install_note'] ?? $assignment->install_note;
                $assignment->is_installed = !empty($assignment->installed_on) && empty($assignment->removed_on);

                $ex5 = $assignment->exists; 
                // Advanced layout extras we want to keep:
                $extraKeys = [
                    'package_type','sim_type','air','mechanic','tracking','system_type',
                    'calibration','color','crm','technician','vehicle_number','vehicle_serial',
                    'vehicle_weight','user','year_model','manufacturer','device_type'
                ];

                // Merge without losing existing extras:
                $existingExtras = (array)($assignment->extras ?? []);
                $newExtras = [];
                foreach ($extraKeys as $ek) {
                    if (!empty($row[$ek])) {
                        $newExtras[$ek] = (string)$row[$ek];
                    }
                }
                $assignment->extras = array_replace($existingExtras, $newExtras);

                // Keep the note in extras if you also read it from sheet
                if (!empty($row['note']) && empty($assignment->install_note)) {
                    $assignment->install_note = (string)$row['note'];
                }

                $existed = $assignment->exists;
                $assignment->save();
                $ex5 ? $updated['assignments']++ : $created['assignments']++;
            }
        });

        $msg = sprintf(
            'Import completed (all sheets): devices +%d/%d, sims +%d/%d, vehicles +%d/%d, sensors +%d/%d, assignments +%d/%d.',
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
            'msisdn'         => ['msisdn','رقم الهاتف','جوال','هاتف'],
            'plate'          => ['plate','لوحة','رقم اللوحة'],
            'sensor'         => ['sensor','حساس','الحساس'],

            'carrier'        => [
                'carrier','carrier_name','sim_carrier',
                'نوع_الشريحة','نوع الشريحة',
                'شركة_الاتصالات','شركة الاتصالات'
            ],

            'subscription'   => ['subscription_type','نوع الاشتراك','نظام البيع','نوع_الاشتراك','نظام_البيع'],
            'note'           => ['note','notes','ملاحظات','ملاحظة'],
            'installed_on'   => ['installed_on','تاريخ التركيب','تاريخ التثبيت','تاريخ_التركيب','تاريخ_التثبيت'],
            'removed_on'     => ['removed_on','تاريخ الازالة','تاريخ الإزالة','تاريخ_الازالة','تاريخ_الإزالة'],
            'serial'         => ['serial','رقم الدرجة','رقم الجهاز','رقم_الدرجة','رقم_الجهاز'],
            'no'          => ['count','qty','العدد'],
            'year'          => ['year','موديل المركبة'],

            'package_type'   => ['data package type','نوع الباقة','نوع الباقة (data package type)'],
            'sim_type'       => ['sim type','رقم الشريحة'],
            'manufacturer'   => ['company manufacture','اسم الشركة المصنعة للمركبة','الشركة المصنعة للمركبة'],
            'device_type'    => ['device type','نوع الجهاز','موديل الجهاز'],
            'air'            => ['air','منافيخ'],
            'mechanic'       => ['mechanic','تتبع'],
            'tracking'       => ['tracking','نظام التتبع'],
            'system_type'    => ['system type','نوع النظام'],
            'calibration'    => ['calibration','المعيار'],
            'color'          => ['color','لون','لون المركبة'],
            'crm'            => ['crm','crm integration','تكامل crm','رقم الطلب crm','رقم الطلب'],
            'technician'     => ['technician','الفني'],
            'vehicle_number' => ['vehicle number','رقم المركبة'],
            'vehicle_serial' => ['vehicle serial number','الرقم التسلسلي للمركبة'],
            'vehicle_weight' => ['vehicle weight','وزن المركبة'],
            'user'           => ['user','USER','المستخدم'],
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

    private function toLatinDigits(string $s): string {
        $arabic = ['٠','١','٢','٣','٤','٥','٦','٧','٨','٩','٫','،'];
        $latin  = ['0','1','2','3','4','5','6','7','8','9','. ', ', ']; // last two are placeholders but keep them
        $s = str_replace($arabic, str_split('0123456789..'), $s);
        return $s;
    }
    private function parseDateFlexible($v): ?string {
        if ($v === null || $v === '') return null;
        if (is_numeric($v)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float)$v)->format('Y-m-d');
        }
        $s = $this->toLatinDigits((string)$v);
        $s = str_replace(['.', '٫'], '/', $s);
        $s = str_replace('-', '/', $s);
        foreach (['Y/m/d','d/m/Y','m/d/Y','Y-m-d','d-m-Y','m-d-Y'] as $fmt) {
            try { return \Carbon\Carbon::createFromFormat($fmt, $s)->format('Y-m-d'); } catch (\Throwable $e) {}
        }
        $ts = strtotime($s);
        return $ts ? date('Y-m-d', $ts) : null;
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

    private function expandSci($v): string
    {
        $s = trim((string)$v);
        if (!preg_match('/^([0-9]+(?:\.[0-9]+)?)e\+?(-?\d+)$/i', $s, $m)) {
            return $s;
        }
        $mant = $m[1]; $exp = (int)$m[2];
        $mant = ltrim($mant, '+');
        if (str_contains($mant, '.')) {
            [$int, $frac] = explode('.', $mant, 2);
        } else {
            $int = $mant; $frac = '';
        }
        $digits = $int.$frac;
        $shift  = $exp - strlen($frac);
        return $shift >= 0 ? $digits.str_repeat('0', $shift)
                        : substr($digits, 0, strlen($digits)+$shift); // not expected here
    }


    private function coerceRow(array $row): array
    {
        // Always treat identifiers as strings to avoid scientific notation
        foreach (['imei','sim_serial','msisdn','serial','plate'] as $k) {
            if (array_key_exists($k, $row) && is_string($row[$k]) && stripos($row[$k], 'e+') !== false) {
                $row[$k] = $this->expandSci($row[$k]);
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
                $row[$k] = $this->parseDateFlexible($row[$k]);
            }
        }

        return $row;
    }

    /**
     * Normalize many date shapes to Y-m-d (or null).
     * Supports Excel serials, d/m/Y, d-m-Y, d.m.Y, Y-m-d, m/d/Y (fallback), etc.
     */
    private function normalizeDate($value): ?string
    {
        if ($value === null || $value === '') return null;

        // Excel serial number
        if (is_numeric($value)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
                // fall through
            }
        }

        $s = trim((string) $value);
        if ($s === '') return null;

        // Unify separators for regex tests
        $u = str_replace(['.', '\\'], ['-', '/'], $s);

        // Y-m-d or Y/m/d
        if (preg_match('/^(\d{4})[-\/](\d{1,2})[-\/](\d{1,2})$/', $u, $m)) {
            [$y,$mth,$d] = [(int)$m[1], (int)$m[2], (int)$m[3]];
            if (checkdate($mth, $d, $y)) return sprintf('%04d-%02d-%02d', $y, $mth, $d);
        }

        // d-m-Y or d/m/Y (day-first — your case "14/7/2025")
        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{2,4})$/', $u, $m)) {
            [$d,$mth,$y] = [(int)$m[1], (int)$m[2], (int)$m[3]];
            if ($y < 100) $y += 2000;
            if (checkdate($mth, $d, $y)) return sprintf('%04d-%02d-%02d', $y, $mth, $d);
        }

        // Last resort: strtotime()
        $ts = strtotime($s);
        if ($ts !== false) return date('Y-m-d', $ts);

        return null;
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

    /**
     * Parse ONE sheet (raw 2D array) using the same logic you already had.
     * Returns: headers/mappedKeys/all rows/valid rows/issues/fatal and the sheet-level defaults.
     */
    private function parseOneSheet(array $raw): array
    {
        if (empty($raw)) {
            return [
                'headers' => [], 'mappedKeys' => [], 'rows' => [], 'valid' => [],
                'issues' => [], 'cellErrors' => [], 'fatal' => ['Empty sheet'], 'defaults' => []
            ];
        }

        // banner defaults for this sheet
        $sheetDefaults = $this->extractBannerDefaultsFromArray($raw);

        // detect header row and map columns (your original logic)
        $headerRowNum = $this->findHeaderRow($raw, ['IMEI', 'ايمي', 'رقم IMEI']);
        $headerIdx    = max(0, $headerRowNum - 1);

        $headersRaw  = $raw[$headerIdx] ?? [];
        $mappedKeys  = [];
        foreach ($headersRaw as $cell) {
            $mappedKeys[] = $this->translateHeader((string) $cell) ?? trim((string) $cell);
        }

        // rows after header
        $dataRows = array_slice($raw, $headerIdx + 1);
        $rows = [];
        foreach ($dataRows as $r) {
            if ($this->isEmptyRow($r)) continue;
            $r = array_values($r);
            $assoc = [];
            foreach ($mappedKeys as $i => $key) {
                if (!$key) continue;
                $assoc[$key] = $r[$i] ?? null;
            }
            // normalize + keep row-level defaults (client, sector, subscription) if not present
            $assoc = $this->coerceRow($assoc);
            foreach (['client_name','sector','subscription_type'] as $k) {
                if (!isset($assoc[$k]) || $assoc[$k] === null || $assoc[$k] === '') {
                    if (!empty($sheetDefaults[$k])) $assoc[$k] = $sheetDefaults[$k];
                }
            }
            $rows[] = $assoc;
            // Consider a row "useful" only if at least one of these fields has data
            $signalFields = ['imei','sim_serial','msisdn','plate','sensor','model','device_type','installed_on'];

            // Drop trailing/blank rows so they don't show in preview or generate errors
            $rows = array_values(array_filter($rows, function ($r) use ($signalFields) {
                foreach ($signalFields as $f) {
                    if (!empty(trim((string)($r[$f] ?? '')))) {
                        return true; // keep this row
                    }
                }
                return false; // remove empty row
            }));
        }

        // fail fast if IMEI not present on this sheet
        $missing = array_diff(['imei'], $this->presentColumns($mappedKeys));
        if (!empty($missing)) {
            return [
                'headers' => $headersRaw, 'mappedKeys' => $mappedKeys, 'rows' => $rows, 'valid' => [],
                'issues'  => [], 'cellErrors' => [],
                'fatal'   => ['Missing required column(s): ' . implode(', ', array_map('strtoupper',$missing))],
                'defaults'=> $sheetDefaults,
            ];
        }

        // duplicates / already-exists
        $dupCounts = $this->duplicateCounts($rows, ['imei','sim_serial','msisdn','plate','sensor']);
        $vals = fn(string $k) => collect($rows)->pluck($k)->filter()->unique()->values();
        $exists = [
            'imei'       => Device::whereIn('imei', $vals('imei'))->pluck('imei')->flip()->all(),
            'sim_serial' => Sim::whereIn('sim_serial', $vals('sim_serial'))->pluck('sim_serial')->flip()->all(),
            'msisdn'     => Sim::whereIn('msisdn', $vals('msisdn'))->pluck('msisdn')->flip()->all(),
            'plate'      => Vehicle::whereIn('plate', $vals('plate'))->pluck('plate')->flip()->all(),
            'sensor'     => Sensor::whereIn('serial_or_bt_id', $vals('sensor'))->pluck('serial_or_bt_id')->flip()->all(),
        ];

        $valid = []; $issues = []; $cellErrors = [];
        foreach ($rows as $i => $row) {
            $excelRow = $headerRowNum + 1 + $i;
            [$ok, $clean, $errs, $fieldErrs] = $this->validateRow($row, $excelRow, $dupCounts, $exists);
            if ($ok) {
                // ensure banner defaults survive into confirm()
                foreach (['client_name','sector','subscription_type'] as $k) {
                    if (!isset($clean[$k]) || $clean[$k] === null || $clean[$k] === '') {
                        if (!empty($sheetDefaults[$k])) $clean[$k] = $sheetDefaults[$k];
                    }
                }
                $valid[] = $clean;
            }
            if (!empty($errs)) $issues[] = ['row' => $excelRow, 'messages' => $errs];
            $cellErrors[$i] = $fieldErrs;
        }

        return [
            'headers'    => $headersRaw,
            'mappedKeys' => $mappedKeys,
            'rows'       => $rows,
            'valid'      => $valid,
            'issues'     => $issues,
            'cellErrors' => $cellErrors,
            'fatal'      => [],
            'defaults'   => $sheetDefaults,
        ];
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
