<?php

namespace App\Http\Controllers;

use App\Models\ClientSheetRow;
use App\Models\Device;
use App\Models\DeviceModel;
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

        // Devices per device model (e.g., FMC920, FMB920)
        $devicesPerModel = Device::query()
            ->with('deviceModel:id,name')
            ->whereNotNull('device_model_id')
            ->get(['id','device_model_id'])
            ->groupBy(fn ($d) => optional($d->deviceModel)->name ?: __('messages.common.not_specified'))
            ->map->count()
            ->sortDesc();

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
        $expiredWarrantyQuery = ClientSheetRow::query()
            ->with(['client:id,name'])
            ->whereNotNull('installed_on')
            ->whereDate('installed_on', '<=', now()->subYears(2));

        $expiredWarrantyCount = $expiredWarrantyQuery->count();
        $expiredWarrantyDevices = $expiredWarrantyQuery
            ->get(['id','client_id','plate','company_manufacture','device_type','installed_on']);

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
            'expiredWarrantyDevices',
            'technicianTotals'
        ));
    }
}


