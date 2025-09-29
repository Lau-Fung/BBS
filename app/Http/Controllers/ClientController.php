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
use App\Services\ActivityLogService;

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
                'Total Records'   => $c->vehicles_count, // Using sheet rows count
                'Devices (active)' => $c->total_devices,
                'Devices by model' => $models,
            ];
        })->toArray();

        // Log export activity
        ActivityLogService::logExport('clients_summary', 'xlsx', count($rows));

        return Excel::download(new ArrayExport(
            ['Company','Sector','Total Records','Devices (active)','Devices by model'],
            $rows
        ), 'clients-summary.xlsx');
    }

    public function exportPdf(Request $request)
    {
        [$clients, $q] = $this->buildSummary($request);

        // Log export activity
        ActivityLogService::logExport('clients_summary', 'pdf', count($clients));

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
            ->withCount('sheetRows')
            ->orderBy('name')
            ->get();

        // Get device counts from client_sheet_rows table
        $deviceCounts = DB::table('client_sheet_rows')
            ->select('client_id', 'device_type', DB::raw('count(*) as count'))
            ->whereNotNull('device_type')
            ->where('device_type', '!=', '')
            ->groupBy('client_id', 'device_type')
            ->get()
            ->groupBy('client_id');

        // Get total devices per client
        $totalDevices = DB::table('client_sheet_rows')
            ->select('client_id', DB::raw('count(*) as total'))
            ->groupBy('client_id')
            ->pluck('total', 'client_id');

        foreach ($clients as $c) {
            $c->total_devices = (int) ($totalDevices[$c->id] ?? 0);
            $c->vehicles_count = $c->sheet_rows_count; // Use sheet rows count as vehicles count
            $c->models = collect($deviceCounts[$c->id] ?? [])
                ->map(fn($row) => ['model' => $row->device_type, 'count' => (int) $row->count])
                ->values();
        }

        return [$clients, $q];
    }

    // DETAIL PAGE
    public function show(Client $client)
    {
        // Get client sheet rows for display
        $clientSheetRows = $client->sheetRows()
            ->orderBy('id')
            ->get();

        // Create headers based on client_sheet_rows structure
        $headers = [
            'العدد', // Count/Number
            'نوع الباقة', // Package Type
            'نوع الشريحة', // SIM Type
            'رقم الشريحة', // SIM Number
            'IMEI',
            'رقم اللوحة', // Plate
            'تاريخ التركيب', // Installation Date
            'موديل المركبة', // Year Model
            'اسم الشركة المصنعة للمركبة', // Company Manufacture
            'نوع الجهاز', // Device Type
            'منافيخ', // Air
            'سست', // Mechanic
            'تتبع', // Tracking
            'نظام التتبع', // System Type
            'المعايرة', // Calibration
            'لون المركبة', // Vehicle Color
            'رقم الطلبcrm', // CRM Integration
            'الفني', // Technician
            'الرقم التسلسلي للسيارة', // Vehicle Serial Number
            'وزن المركبة', // Vehicle Weight
            'USER', // User
            'ملاحظات', // Notes
        ];

        // Convert client sheet rows to display format
        $rows = $clientSheetRows->map(function ($row, $index) {
            return [
                'no' => $row->no ?? ($index + 1),
                'package_type' => $row->data_package_type ?? '',
                'sim_type' => $row->sim_type ?? '',
                'sim_number' => $row->sim_number ?? '',
                'imei' => $row->imei ?? '',
                'plate' => $row->plate ?? '',
                'installed_on' => $row->installed_on ? $row->installed_on->format('Y-m-d') : '',
                'year_model' => $row->year_model ?? '',
                'company_manufacture' => $row->company_manufacture ?? '',
                'device_type' => $row->device_type ?? '',
                'air' => $row->air ? 'نعم' : 'لا',
                'mechanic' => $row->mechanic ? 'نعم' : 'لا',
                'tracking' => $row->tracking ?? '',
                'system_type' => $row->system_type ?? '',
                'calibration' => $row->calibration ?? '',
                'color' => $row->color ?? '',
                'crm' => $row->crm_integration ?? '',
                'subscription_type' => $row->subscription_type ?? '',
                'technician' => $row->technician ?? '',
                'vehicle_serial_number' => $row->vehicle_serial_number ?? '',
                'vehicle_weight' => $row->vehicle_weight ?? '',
                'user' => $row->user ?? '',
                'notes' => $row->notes ?? '',
            ];
        });

        return view('clients.show', compact('client', 'headers', 'rows', 'clientSheetRows'));
    }

    // EXPORT (XLSX/CSV/PDF)
    public function export(\App\Models\Client $client)
    {
        $format = request('format', 'xlsx'); // 'xlsx' or 'csv', 'pdf'
        
        // Get client sheet rows for export
        $clientSheetRows = $client->sheetRows()
            ->orderBy('id')
            ->get();

        // Create headers for export
        $headers = [
            'العدد', // Count/Number
            'نوع الباقة', // Package Type
            'نوع الشريحة', // SIM Type
            'رقم الشريحة', // SIM Number
            'IMEI',
            'رقم اللوحة', // Plate
            'تاريخ التركيب', // Installation Date
            'موديل المركبة', // Year Model
            'اسم الشركة المصنعة للمركبة', // Company Manufacture
            'نوع الجهاز', // Device Type
            'منافيخ', // Air
            'سست', // Mechanic
            'تتبع', // Tracking
            'نظام التتبع', // System Type
            'المعايرة', // Calibration
            'لون المركبة', // Vehicle Color
            'رقم الطلبcrm', // CRM Integration
            'الفني', // Technician
            'الرقم التسلسلي للسيارة', // Vehicle Serial Number
            'وزن المركبة', // Vehicle Weight
            'USER', // User
            'ملاحظات', // Notes
        ];

        // Convert client sheet rows to export format
        $rows = $clientSheetRows->map(function ($row, $index) {
            return [
                $row->no ?? ($index + 1),
                $row->data_package_type ?? '',
                $row->sim_type ?? '',
                $row->sim_number ?? '',
                $row->imei ?? '',
                $row->plate ?? '',
                $row->installed_on ? $row->installed_on->format('Y-m-d') : '',
                $row->year_model ?? '',
                $row->company_manufacture ?? '',
                $row->device_type ?? '',
                $row->air ? 'نعم' : 'لا',
                $row->mechanic ? 'نعم' : 'لا',
                $row->tracking ?? '',
                $row->system_type ?? '',
                $row->calibration ?? '',
                $row->color ?? '',
                $row->crm_integration ?? '',
                $row->technician ?? '',
                $row->vehicle_serial_number ?? '',
                $row->vehicle_weight ?? '',
                $row->user ?? '',
                $row->notes ?? '',
            ];
        });

        // Log export activity
        ActivityLogService::logExport('client_details', $format, count($rows), ['client_id' => $client->id]);

        // Handle PDF export differently
        if ($format === 'pdf') {
            $pdf = Pdf::loadView('clients.client-details-pdf', [
                'client' => $client,
                'headers' => $headers,
                'rows' => $rows->toArray(),
                'clientSheetRows' => $clientSheetRows
            ])->setPaper('a4', 'landscape'); // Use landscape for better table display
            
            return $pdf->download('client_' . $client->id . '_data.pdf');
        }
        
        // Create a simple array export for Excel/CSV
        return Excel::download(new ArrayExport($headers, $rows->toArray()), 'client_' . $client->id . '_data.' . $format);
    }

     public function createAssignment(Client $client)
    {
        // redirect to your assignments.create with the client preselected
        return redirect()->route('assignments.create', ['client_id' => $client->id]);
    }

}
