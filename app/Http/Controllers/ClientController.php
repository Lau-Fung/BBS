<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientDetailExport;
use Maatwebsite\Excel\Excel as ExcelWriter;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));

        $clients = Client::query()
            ->when($q !== '', fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->withCount('vehicles')
            ->orderBy('name')
            ->get();

        // precompute per-client totals (#active assignments = devices installed)
        $totals = Assignment::query()
            ->select('vehicles.client_id', DB::raw('count(*) as total_devices'))
            ->join('vehicles', 'vehicles.id', '=', 'assignments.vehicle_id')
            ->where('assignments.is_active', true)
            ->groupBy('vehicles.client_id')
            ->pluck('total_devices', 'vehicles.client_id');

        // per-device-model counts (FMC920, FMB920, ...)
        $modelCounts = DB::table('assignments')
            ->join('vehicles', 'vehicles.id', '=', 'assignments.vehicle_id')
            ->join('devices', 'devices.id', '=', 'assignments.device_id')
            ->join('device_models', 'device_models.id', '=', 'devices.device_model_id')
            ->where('assignments.is_active', true)
            ->groupBy('vehicles.client_id', 'device_models.name')
            ->select('vehicles.client_id', 'device_models.name as model', DB::raw('count(*) as c'))
            ->get()
            ->groupBy('client_id');

        // attach computed stats for the view
        foreach ($clients as $c) {
            $c->total_devices = (int) ($totals[$c->id] ?? 0);
            $c->models = collect($modelCounts[$c->id] ?? [])
                ->map(fn($row) => ['model' => $row->model, 'count' => (int) $row->c])
                ->values();
        }

        return view('clients.index', compact('clients','q'));
    }

    // DETAIL PAGE
    public function show(Client $client, Request $request)
    {
        // show all active assignments for this client (you can add date filters if needed)
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

        return view('clients.show', compact('client','rows'));
    }

    // EXPORT (XLSX/CSV)
    public function export(Client $client, Request $request)
    {
        $format = strtolower($request->get('format', 'xlsx'));
        $writer = $format === 'csv' ? ExcelWriter::CSV : ExcelWriter::XLSX;

        $file = 'client_'.$client->id.'_'.str_slug($client->name).'_'.now()->format('Ymd_His').'.'.$format;

        return Excel::download(new ClientDetailExport($client), $file, $writer);
    }
}
