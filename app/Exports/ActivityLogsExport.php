<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActivityLogsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $activities;

    public function __construct($activities)
    {
        $this->activities = $activities;
    }

    public function collection()
    {
        return $this->activities;
    }

    public function headings(): array
    {
        return [
            'ID',
            'User',
            'Event',
            'Subject Type',
            'Subject ID',
            'Description',
            'Log Name',
            'Properties',
            'IP Address',
            'User Agent',
            'Created At',
        ];
    }

    public function map($activity): array
    {
        return [
            $activity->id,
            $activity->causer->name ?? 'System',
            $activity->event,
            $activity->subject_type ? class_basename($activity->subject_type) : 'N/A',
            $activity->subject_id ?? 'N/A',
            $activity->description,
            $activity->log_name,
            $activity->properties ? json_encode($activity->properties) : '',
            $activity->properties['ip_address'] ?? '',
            $activity->properties['user_agent'] ?? '',
            $activity->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 20,  // User
            'C' => 15,  // Event
            'D' => 20,  // Subject Type
            'E' => 15,  // Subject ID
            'F' => 40,  // Description
            'G' => 20,  // Log Name
            'H' => 30,  // Properties
            'I' => 15,  // IP Address
            'J' => 30,  // User Agent
            'K' => 20,  // Created At
        ];
    }
}
