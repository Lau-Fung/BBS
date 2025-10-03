<?php

namespace App\Http\Controllers;

use App\Models\ClientSheetRow;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Clients per sector (count of distinct clients grouped by sector via related Client model if available)
        $clientsPerSector = ClientSheetRow::query()
            ->with('client')
            ->whereHas('client', function ($q) { $q->whereNotNull('sector'); })
            ->get()
            ->groupBy(fn ($row) => optional($row->client)->sector ?: __('messages.common.not_specified'))
            ->map->pluck('client_id')->map->unique()->map->count();

        // Devices per device model (company_manufacture used as model label)
        $devicesPerModel = ClientSheetRow::query()
            ->select('company_manufacture')
            ->whereNotNull('company_manufacture')
            ->get()
            ->groupBy('company_manufacture')
            ->map->count();

        // SIM totals per package (data_package_type)
        $simsPerPackage = ClientSheetRow::query()
            ->select('data_package_type')
            ->whereNotNull('data_package_type')
            ->get()
            ->groupBy('data_package_type')
            ->map->count();

        // SIM provider (sim_type or provider column if exists; fallback to sim_type)
        $simsPerProvider = ClientSheetRow::query()
            ->select('sim_type')
            ->whereNotNull('sim_type')
            ->get()
            ->groupBy('sim_type')
            ->map->count();

        // Devices out of warranty (2 years after installed_on)
        $expiredWarrantyCount = ClientSheetRow::query()
            ->whereNotNull('installed_on')
            ->whereDate('installed_on', '<=', now()->subYears(2))
            ->count();

        // Technician installations count
        $technicianTotals = ClientSheetRow::query()
            ->select('technician')
            ->whereNotNull('technician')
            ->get()
            ->groupBy('technician')
            ->map->count()
            ->sortDesc();

        return view('dashboard', compact(
            'clientsPerSector',
            'devicesPerModel',
            'simsPerPackage',
            'simsPerProvider',
            'expiredWarrantyCount',
            'technicianTotals'
        ));
    }
}


