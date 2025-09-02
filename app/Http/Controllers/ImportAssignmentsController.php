<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

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
        $rows = session()->pull('import.assignments.valid', []);

        if (empty($rows)) {
            return back()->withErrors(['file' => 'Nothing to import or the preview had issues.']);
        }

        // TODO: Save to DB (Devices, SIMs, Assignments, etc.)
        // Example (pseudo):
        // foreach ($rows as $row) {
        //     $device = Device::firstOrCreate(['imei' => (string)$row['imei']]);
        //     if (!empty($row['sim_serial'])) {
        //         $sim = Sim::firstOrCreate(
        //             ['sim_serial' => (string)$row['sim_serial']],
        //             ['msisdn' => (string)($row['msisdn'] ?? '')]
        //         );
        //     }
        //     // ... create/update assignments, notes, dates, etc.
        // }

        return redirect()
            ->route('imports.assignments.form')
            ->with('status', 'Import completed successfully.');
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
