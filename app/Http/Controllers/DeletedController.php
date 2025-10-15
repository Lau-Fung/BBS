<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientSheetRow;
use Illuminate\Http\Request;

class DeletedController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // View Deleted page allowed for viewers of clients (Data Entry included)
        $this->middleware('permission:clients.view')->only(['index']);
        // Restores are privileged (Data Entry should NOT restore)
        $this->middleware('permission:clients.delete')->only(['restoreClient']);
        $this->middleware('permission:assignments.restore')->only(['restoreRow']);
        // Force deletes remain restricted
        $this->middleware('permission:clients.delete')->only(['forceDeleteClient']);
        $this->middleware('permission:assignments.delete')->only(['forceDeleteRow']);
    }

    public function index()
    {
        $clients = Client::onlyTrashed()->orderBy('deleted_at','desc')->get();
        // Show deleted rows only if their client is not deleted (client currently active)
        $rows    = ClientSheetRow::onlyTrashed()
            ->whereHas('client', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->with('client')
            ->orderBy('deleted_at','desc')
            ->get();
        return view('deleted.index', compact('clients','rows'));
    }

    public function restoreClient($clientId)
    {
        $client = Client::onlyTrashed()->findOrFail($clientId);
        $client->restore();
        return back()->with('success', __('messages.common.restored_successfully') ?? 'Restored');
    }

    public function restoreRow($rowId)
    {
        $row = ClientSheetRow::onlyTrashed()->findOrFail($rowId);
        $row->restore();
        return back()->with('success', __('messages.common.restored_successfully') ?? 'Restored');
    }

    public function forceDeleteRow($rowId)
    {
        $row = ClientSheetRow::onlyTrashed()->findOrFail($rowId);
        $row->forceDelete();
        return back()->with('success', __('messages.common.deleted_successfully'));
    }

    public function forceDeleteClient($clientId)
    {
        $client = Client::onlyTrashed()->findOrFail($clientId);
        // Permanently delete rows first
        ClientSheetRow::onlyTrashed()->where('client_id', $client->id)->forceDelete();
        $client->forceDelete();
        return back()->with('success', __('messages.common.deleted_successfully'));
    }
}


