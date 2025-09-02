<?php

namespace App\Exports;

use App\Models\Assignment;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class AssignmentsExport implements
    FromQuery,
    WithMapping,
    WithHeadings,
    WithColumnFormatting,
    WithCustomCsvSettings,
    WithStrictNullComparison,
    ShouldAutoSize
{
    use Exportable;

    public function __construct(private array $filters = [])
    {
    }

    public function query()
    {
        $q = Assignment::query()
            ->with([
                'device.deviceModel',
                'sim.carrier',
                'vehicle',
                'sensor',
            ]);

        // Filters (all optional)
        if (isset($this->filters['is_active'])) {
            $q->where('is_active', (bool)$this->filters['is_active']);
        }
        if (!empty($this->filters['carrier'])) {
            $carrier = $this->filters['carrier'];
            $q->whereHas('sim.carrier', fn($qq) => $qq->where('name', $carrier));
        }
        if (!empty($this->filters['device_model'])) {
            $model = $this->filters['device_model'];
            $q->whereHas('device.deviceModel', fn($qq) => $qq->where('name', $model));
        }
        if (!empty($this->filters['installed_from'])) {
            $q->whereDate('installed_on', '>=', $this->filters['installed_from']);
        }
        if (!empty($this->filters['installed_to'])) {
            $q->whereDate('installed_on', '<=', $this->filters['installed_to']);
        }

        return $q->orderBy('id');
    }

    public function headings(): array
    {
        return [
            'IMEI',
            'Device Model / نوع الجهاز',
            'Carrier / شركة الاتصالات',
            'MSISDN',
            'SIM Serial',
            'Vehicle Plate / رقم اللوحة',
            'Vehicle Status / حالة المركبة',
            'Sensor ID',
            'Installed? / مثبت؟',
            'Installed On / تاريخ التركيب',
            'Removed On / تاريخ الإزالة',
            'Install Note / ملاحظة',
            'Active Mapping / نشط',
            'Created At',
        ];
    }

    public function map($a): array
    {
        // $a is Assignment
        $device   = $a->device;
        $model    = $device?->deviceModel?->name;
        $sim      = $a->sim;
        $carrier  = $sim?->carrier?->name;
        $vehicle  = $a->vehicle;
        $sensor   = $a->sensor;

        return [
            (string)($device?->imei ?? ''),
            (string)($model ?? ''),
            (string)($carrier ?? ''),
            (string)($sim?->msisdn ?? ''),
            (string)($sim?->sim_serial ?? ''),
            (string)($vehicle?->plate ?? ''),
            (string)($vehicle?->status ?? ''),
            (string)($sensor?->serial_or_bt_id ?? ''),
            $a->is_installed ? 'Yes' : 'No',
            optional($a->installed_on)->format('Y-m-d'),
            optional($a->removed_on)->format('Y-m-d'),
            (string)($a->install_note ?? ''),
            $a->is_active ? 'Yes' : 'No',
            $a->created_at?->format('Y-m-d'),
        ];
    }

    // XLSX date formatting (J, K, N)
    public function columnFormats(): array
    {
        return [
            'J' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'K' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'N' => NumberFormat::FORMAT_DATE_YYYYMMDD2,
        ];
    }

    // CSV settings: UTF-8 BOM for Arabic
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => ',',
            'enclosure' => '"',
            'line_ending' => PHP_EOL,
            'use_bom' => true,
            'include_separator_line' => false,
            'excel_compatibility' => true,
        ];
    }
}
