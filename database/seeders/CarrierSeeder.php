<?php

// database/seeders/CarrierSeeder.php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrier;

class CarrierSeeder extends Seeder
{
    public function run(): void
    {
        $carriers = [
            'LEBARA',
            'STC',
            'Zain',
            'Mobily',
        ];

        foreach ($carriers as $name) {
            Carrier::firstOrCreate(['name' => $name]);
        }
    }
}
