<?php

namespace App\Support\Layouts;

use App\Models\Assignment;
use Illuminate\Support\Collection;

final class AdvancedLayout
{
    /** Column order exactly like the sample */
    public const ORDER = [
        'no',
        'package_type',        // نوع الباقة (data package type)
        'sim_type',            // نوع الشريحة (SIM type)
        'sim_serial',          // رقم الشريحة (SIM Number)
        'imei',                // IMEI
        'plate',               // رقم اللوحة (Plate Number)
        'installed_on',        // تاريخ التركيب (date of installation)
        'year_model',          // موديل السنة (year model)
        'manufacturer',        // اسم الشركة المُصنعة للمركبة (company manufacture)
        'device_type',         // نوع الجهاز (device Type)
        'air',                 // (Air)
        'mechanic',            // (mechanic)
        'tracking',            // (tracking)
        'system_type',         // (system type)
        'calibration',         // (calibration)
        'color',               // (color)
        'crm',                 // (CRM integration)
        'technician',          // (technician)
        'vehicle_number',      // (vehicle number)
        'vehicle_serial',      // (vehicle serial number)
        'vehicle_weight',      // (vehicle weight)
        'user',                // USER
        'note',                // ملاحظات
    ];

    /** Human labels (keep Arabic where you want it on the UI) */
    public const LABELS = [
        'no'             => 'العدد',
        'package_type'   => 'نوع الباقة',
        'sim_type'       => 'نوع الشريحة',
        'sim_serial'     => 'رقم الشريحة',
        'imei'           => 'IMEI',
        'plate'          => 'رقم اللوحة',
        'installed_on'   => 'تاريخ التركيب',
        'year_model'     => 'موديل السنة',
        'manufacturer'   => 'اسم الشركة المُصنعة للمركبة',
        'device_type'    => 'نوع الجهاز',
        'air'            => 'هواء',
        'mechanic'       => 'ميكانيك',
        'tracking'       => 'تتبع',
        'system_type'    => 'نوع النظام',
        'calibration'    => 'المعيار',
        'color'          => 'لون المركبة',
        'crm'            => 'CRM',
        'technician'     => 'الفني',
        'vehicle_number' => 'رقم المركبة',
        'vehicle_serial' => 'الرقم التسلسلي للمركبة',
        'vehicle_weight' => 'وزن المركبة',
        'user'           => 'USER',
        'note'           => 'ملاحظات',
    ];

    public static function headings(): array
    {
        return array_map(fn ($k) => self::LABELS[$k], self::ORDER);
    }

    public static function rowFromAssignment(Assignment $a, int $index): array
    {
        $e = (array)($a->extras ?? []);

        return [
            'no'             => $index,
            'package_type'   => $e['package_type']   ?? '',
            'sim_type'       => $e['sim_type']       ?? '',
            'sim_serial'     => $a->sim?->sim_serial ?? ($e['sim_serial'] ?? ''),
            'imei'           => $a->device?->imei    ?? '',
            'plate'          => $a->vehicle?->plate  ?? '',
            'installed_on'   => optional($a->installed_on)->format('Y-m-d'),
            'year_model'     => $e['year_model']     ?? ($a->vehicle->year ?? ''),
            'manufacturer'   => $e['manufacturer']   ?? ($a->device?->manufacturer ?? ''),
            'device_type'    => $a->device?->model?->name ?? ($e['device_type'] ?? ''),
            'air'            => $e['air']            ?? '',
            'mechanic'       => $e['mechanic']       ?? '',
            'tracking'       => $e['tracking']       ?? '',
            'system_type'    => $e['system_type']    ?? '',
            'calibration'    => $e['calibration']    ?? '',
            'color'          => $e['color']          ?? '',
            'crm'            => $e['crm']            ?? '',
            'technician'     => $e['technician']     ?? '',
            'vehicle_number' => $e['vehicle_number'] ?? '',
            'vehicle_serial' => $e['vehicle_serial'] ?? '',
            'vehicle_weight' => $e['vehicle_weight'] ?? '',
            'user'           => $e['user']           ?? '',
            'note'           => $a->install_note     ?? ($e['note'] ?? ''),
        ];
    }

    public static function map(Collection $assignments): array
    {
        $rows = [];
        foreach ($assignments as $i => $a) {
            $rows[] = self::rowFromAssignment($a, $i + 1);
        }
        return $rows;
    }
}
