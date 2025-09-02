<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class GenericArrayImport extends DefaultValueBinder implements ToArray, WithCustomValueBinder
{
    // Keep long numeric-looking values as strings (IMEI, ICCID, MSISDN, etc.)
    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value)) {
            $raw = (string)$value;
            if (preg_match('/^0[0-9]+$/', $raw) || strlen($raw) >= 11) {
                $cell->setValueExplicit($raw, DataType::TYPE_STRING);
                return true;
            }
        }
        return parent::bindValue($cell, $value);
    }

    public function array(array $array)
    {
        return $array; // raw rows; controller handles mapping/validation
    }
}
