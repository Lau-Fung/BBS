<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class ArrayExport implements FromArray, WithHeadings, ShouldAutoSize, WithCustomCsvSettings
{
    public function __construct(private array $headings, private array $rows) {}

    public function headings(): array { return $this->headings; }

    public function array(): array { return $this->rows; }

    public function getCsvSettings(): array
    {
        return [
            'use_bom' => true,
            'input_encoding' => 'UTF-8',
            'output_encoding' => 'UTF-8',
        ];
    }
}

