<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Carrier;
use App\Models\DeviceModel;
use App\Models\SensorModel;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        Carrier::firstOrCreate(['name' => 'LEBARA']);
        DeviceModel::firstOrCreate(['name' => 'FMC920','manufacturer' => 'Teltonika']);
        SensorModel::firstOrCreate(['name' => 'Dominator BT','brand'=>'—']);
    }
}
