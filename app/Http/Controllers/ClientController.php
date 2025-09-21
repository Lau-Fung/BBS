<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ClientAdvancedExport;
use App\Exports\ArrayExport;
use Maatwebsite\Excel\Excel as ExcelWriter;
use App\Support\Layouts\AdvancedLayout;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        [$clients, $q] = $this->buildSummary($request);

        return view('clients.index', compact('clients','q'));
    }

    /** ---------- Exports ---------- */

    public function exportXlsx(Request $request)
    {
        [$clients, $q] = $this->buildSummary($request);

        // Flatten rows for Excel
        $rows = $clients->map(function ($c) {
            $models = collect($c->models)->map(fn($m) => "{$m['model']}: {$m['count']}")->implode(', ');
            return [
                'Company'         => $c->name,
                'Sector'          => $c->sector,
                'Vehicles'        => $c->vehicles_count,
                'Devices (active)' => $c->total_devices,
                'Devices by model' => $models,
            ];
        })->toArray();

        return Excel::download(new ArrayExport(
            ['Company','Sector','Vehicles','Devices (active)','Devices by model'],
            $rows
        ), 'clients-summary.xlsx');
    }

    public function exportPdf(Request $request)
    {
        [$clients, $q] = $this->buildSummary($request);

        $pdf = Pdf::loadView('clients.summary-pdf', [
            'clients' => $clients,
            'q'       => $q,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('clients-summary.pdf');
    }

    /** ---------- Shared summary builder ---------- */
    private function buildSummary(Request $request): array
    {
        $q = trim((string) $request->get('q', ''));

        $clients = Client::query()
            ->when($q !== '', fn ($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->withCount('vehicles')
            ->orderBy('name')
            ->get();

        // active devices per client (active assignments)
        $totals = Assignment::query()
            ->select('vehicles.client_id', DB::raw('count(*) as total_devices'))
            ->join('vehicles', 'vehicles.id', '=', 'assignments.vehicle_id')
            ->where('assignments.is_active', true)
            ->groupBy('vehicles.client_id')
            ->pluck('total_devices', 'vehicles.client_id');

        // device model counts per client
        $modelCounts = DB::table('assignments')
            ->join('vehicles', 'vehicles.id', '=', 'assignments.vehicle_id')
            ->join('devices', 'devices.id', '=', 'assignments.device_id')
            ->join('device_models', 'device_models.id', '=', 'devices.device_model_id')
            ->where('assignments.is_active', true)
            ->groupBy('vehicles.client_id', 'device_models.name')
            ->select('vehicles.client_id', 'device_models.name as model', DB::raw('count(*) as c'))
            ->get()
            ->groupBy('client_id');

        foreach ($clients as $c) {
            $c->total_devices = (int) ($totals[$c->id] ?? 0);
            $c->models = collect($modelCounts[$c->id] ?? [])
                ->map(fn($row) => ['model' => $row->model, 'count' => (int) $row->c])
                ->values();
        }

        return [$clients, $q];
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
