<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientAdvancedExport;
use Maatwebsite\Excel\Excel as ExcelWriter;
use App\Support\Layouts\AdvancedLayout;

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
    public function show(Client $client)
    {
        $assignments = $client->assignments()
            ->with(['device.model', 'sim', 'vehicle'])
            ->where('is_active', true)
            ->orderBy('id')
            ->get();

        $headers = AdvancedLayout::headings();
        $rows    = AdvancedLayout::map($assignments);

        return view('clients.show', compact('client', 'headers', 'rows'));
    }

    // EXPORT (XLSX/CSV)
    public function export(\App\Models\Client $client)
    {
        $format = request('format', 'xlsx'); // 'xlsx' or 'csv'
        return new ClientAdvancedExport($client, $format);
    }

}
