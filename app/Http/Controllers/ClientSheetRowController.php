<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientSheetRow;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\ActivityLogService;

class ClientSheetRowController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:assignments.create')->only(['create', 'store']);
        $this->middleware('permission:assignments.update')->only(['edit', 'update']);
        $this->middleware('permission:assignments.delete')->only(['destroy']);
    }

    /**
     * Show the form for creating a new client sheet row
     */
    public function create(Client $client)
    {
        $this->authorize('create', ClientSheetRow::class);
        
        $clientSheetRow = new ClientSheetRow();
        $action = route('clients.sheet-rows.store', $client);
        
        return view('clients._client_sheet_row_form', compact('clientSheetRow', 'action'));
    }

    /**
     * Store a newly created client sheet row
     */
    public function store(Request $request, Client $client)
    {
        $this->authorize('create', ClientSheetRow::class);
        
        $validated = $request->validate([
            'data_package_type' => 'nullable|string|max:50',
            'sim_type' => 'nullable|string|max:50',
            'sim_number' => 'nullable|string|max:64',
            'imei' => 'required|string|max:32',
            'plate' => 'nullable|string|max:50',
            'installed_on' => 'nullable|date',
            'year_model' => 'nullable|string|max:16',
            'company_manufacture' => 'nullable|string|max:120',
            'device_type' => 'nullable|string|max:60',
            'air' => 'nullable|boolean',
            'mechanic' => 'nullable|boolean',
            'tracking' => 'nullable|string|max:60',
            'system_type' => 'nullable|string|max:60',
            'calibration' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:40',
            'crm_integration' => 'nullable|string|max:120',
            'technician' => 'nullable|string|max:120',
            'vehicle_serial_number' => 'nullable|string|max:120',
            'vehicle_weight' => 'nullable|string|max:60',
            'user' => 'nullable|string|max:120',
            'notes' => 'nullable|string',
        ]);

        $validated['client_id'] = $client->id;
        
        ClientSheetRow::create($validated);
        
        // return response()->json([
        //     'success' => true,
        //     'message' => __('messages.common.row_created'),
        //     'redirect' => route('clients.show', $client)
        // ]);

        // Otherwise → normal redirect
        return redirect()
            ->route('clients.show', $client)
            ->with('success', __('messages.common.row_created'));
    }

    /**
     * Show the form for editing a client sheet row
     */
    public function edit(Client $client, ClientSheetRow $clientSheetRow)
    {
        $this->authorize('update', $clientSheetRow);
        
        $action = route('clients.sheet-rows.update', [$client, $clientSheetRow]);
        
        return view('clients._client_sheet_row_form', compact('clientSheetRow', 'action'));
    }

    /**
     * Update the specified client sheet row
     */
    public function update(Request $request, Client $client, ClientSheetRow $clientSheetRow)
    {
        $this->authorize('update', $clientSheetRow);
        
        $validated = $request->validate([
            'data_package_type' => 'nullable|string|max:50',
            'sim_type' => 'nullable|string|max:50',
            'sim_number' => 'nullable|string|max:64',
            'imei' => 'required|string|max:32',
            'plate' => 'nullable|string|max:50',
            'installed_on' => 'nullable|date',
            'year_model' => 'nullable|string|max:16',
            'company_manufacture' => 'nullable|string|max:120',
            'device_type' => 'nullable|string|max:60',
            'air' => 'nullable|boolean',
            'mechanic' => 'nullable|boolean',
            'tracking' => 'nullable|string|max:60',
            'system_type' => 'nullable|string|max:60',
            'calibration' => 'nullable|string|max:60',
            'color' => 'nullable|string|max:40',
            'crm_integration' => 'nullable|string|max:120',
            'technician' => 'nullable|string|max:120',
            'vehicle_serial_number' => 'nullable|string|max:120',
            'vehicle_weight' => 'nullable|string|max:60',
            'user' => 'nullable|string|max:120',
            'notes' => 'nullable|string',
        ]);
        
        $clientSheetRow->update($validated);
        
        // return response()->json([
        //     'success' => true,
        //     'message' => __('messages.common.row_updated'),
        //     'redirect' => route('clients.show', $client)
        // ]);

        return redirect()
            ->route('clients.show', $client)
            ->with('success', __('messages.common.row_created'));
    }

    /**
     * Remove the specified client sheet row
     */
    public function destroy(Client $client, ClientSheetRow $clientSheetRow)
    {
        $this->authorize('delete', $clientSheetRow);
        
        $clientSheetRow->delete();
        
        // Check if request expects JSON (AJAX)
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('messages.common.row_deleted'),
                'redirect' => route('clients.show', $client)
            ]);
        }
        
        // Regular form submission
        return redirect()
            ->route('clients.show', $client)
            ->with('success', __('messages.common.row_deleted'));
    }

    /**
     * Show the form for editing all client sheet rows
     */
    public function editAll(Client $client)
    {
        $this->authorize('viewAny', ClientSheetRow::class);
        
        $clientSheetRows = $client->sheetRows()->orderBy('id')->get();
        
        return view('clients.edit-all', compact('client', 'clientSheetRows'));
    }

    /**
     * Update all client sheet rows
     */
    public function updateAll(Request $request, Client $client)
    {
        $this->authorize('updateAll', ClientSheetRow::class);
        
        $request->validate([
            'rows' => 'required|array',
            'rows.*.id' => 'required|exists:client_sheet_rows,id',
            'rows.*.data_package_type' => 'nullable|string|max:255',
            'rows.*.sim_type' => 'nullable|string|max:255',
            'rows.*.sim_number' => 'nullable|string|max:255',
            'rows.*.imei' => 'nullable|string|max:255',
            'rows.*.plate' => 'nullable|string|max:255',
            'rows.*.installed_on' => 'nullable|date',
            'rows.*.year_model' => 'nullable|string|max:255',
            'rows.*.company_manufacture' => 'nullable|string|max:255',
            'rows.*.device_type' => 'nullable|string|max:255',
            'rows.*.air' => 'nullable|boolean',
            'rows.*.sensor_type' => 'nullable|string|max:255',
            'rows.*.mechanic' => 'nullable|boolean',
            'rows.*.tracking' => 'nullable|string|max:255',
            'rows.*.system_type' => 'nullable|string|max:255',
            'rows.*.calibration' => 'nullable|string|max:255',
            'rows.*.color' => 'nullable|string|max:255',
            'rows.*.crm_integration' => 'nullable|string|max:255',
            'rows.*.subscription_type' => 'nullable|string|max:255',
            'rows.*.technician' => 'nullable|string|max:255',
            'rows.*.vehicle_serial_number' => 'nullable|string|max:255',
            'rows.*.vehicle_weight' => 'nullable|string|max:255',
            'rows.*.user' => 'nullable|string|max:255',
            'rows.*.notes' => 'nullable|string|max:1000',
        ]);

        $updatedCount = 0;
        
        foreach ($request->rows as $rowData) {
            $clientSheetRow = ClientSheetRow::find($rowData['id']);
            if (!$clientSheetRow) { continue; }

            $update = [];

            // Only update fields that are actually provided with a non-empty value
            $assignIfPresent = function (string $key) use (&$update, $rowData) {
                if (array_key_exists($key, $rowData)) {
                    $val = $rowData[$key];
                    if ($val !== '' && $val !== null) {
                        $update[$key] = $val;
                    }
                }
            };

            foreach ([
                'data_package_type','sim_type','sim_number','imei','plate','installed_on','year_model',
                'company_manufacture','device_type','tracking','system_type','calibration','color',
                'crm_integration','subscription_type', 'technician','vehicle_serial_number','vehicle_weight','user','notes'
            ] as $f) { $assignIfPresent($f); }

            // Booleans: explicitly set if present, otherwise keep original
            foreach (['air','mechanic'] as $b) {
                if (array_key_exists($b, $rowData)) {
                    $update[$b] = (bool) (is_string($rowData[$b]) ? (int)$rowData[$b] : $rowData[$b]);
                }
            }

            if (!empty($update)) {
                $clientSheetRow->update($update);
                $updatedCount++;
            }
        }

        // Log bulk operation
        if ($updatedCount > 0) {
            ActivityLogService::logBulkOperation('update', 'ClientSheetRow', $updatedCount, [
                'client_id' => $client->id,
                'client_name' => $client->name,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => __('messages.common.rows_updated', ['count' => $updatedCount]),
            'updated_count' => $updatedCount
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }
}
