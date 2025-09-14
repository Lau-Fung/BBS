<?php

namespace App\Exports;

use App\Models\Client;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class ClientDetailExport implements FromView
{
    public function __construct(public Client $client) {}

    public function view(): View
    {
        $client = $this->client;

        $rows = DB::table('assignments as a')
            ->leftJoin('devices as d', 'd.id', '=', 'a.device_id')
            ->leftJoin('device_models as dm', 'dm.id', '=', 'd.device_model_id')
            ->leftJoin('sims as s', 's.id', '=', 'a.sim_id')
            ->leftJoin('vehicles as v', 'v.id', '=', 'a.vehicle_id')
            ->where('a.is_active', true)
            ->where('v.client_id', $client->id)
            ->orderBy('v.plate')
            ->select([
                'v.plate',
                'dm.name as device_model',
                'd.imei',
                's.sim_serial',
                's.msisdn',
                'a.installed_on',
                'a.install_note',
            ])
            ->get();

        return view('clients.export', compact('client','rows'));
    }
}
