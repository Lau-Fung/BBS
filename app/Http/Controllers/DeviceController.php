<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceModel;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Http\Requests\StoreDeviceRequest;
use App\Http\Requests\UpdateDeviceRequest;

class DeviceController extends Controller
{
    public function index(Request $request)
    {
        $qFilter = AllowedFilter::callback('q', function ($query, $value) {
            $value = trim($value);
            $query->where('imei','like',"%{$value}%");
        });

        $devices = QueryBuilder::for(Device::query()->with('model:id,name'))
            ->allowedFilters([
                $qFilter,
                AllowedFilter::exact('device_model_id'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['imei','is_active','created_at'])
            ->defaultSort('created_at')
            ->paginate(20)
            ->withQueryString();

        $deviceModels = DeviceModel::orderBy('name')->get(['id','name']);
        return view('devices.index', compact('devices','deviceModels'));
    }

    public function create()
    {
        $deviceModels = DeviceModel::orderBy('name')->get(['id','name']);
        return view('devices.create', compact('deviceModels'));
    }

    public function store(StoreDeviceRequest $request)
    {
        Device::create($request->validated());
        return redirect()->route('devices.index')->with('success','Device created.');
    }

    public function edit(Device $device)
    {
        $deviceModels = DeviceModel::orderBy('name')->get(['id','name']);
        return view('devices.edit', compact('device','deviceModels'));
    }

    public function update(UpdateDeviceRequest $request, Device $device)
    {
        $device->update($request->validated());
        return redirect()->route('devices.index')->with('success','Device updated.');
    }

    public function destroy(Device $device)
    {
        $device->delete();
        return back()->with('success','Device deleted.');
    }

    public function restore($id)
    {
        $d = Device::withTrashed()->findOrFail($id);
        $d->restore();
        return back()->with('success','Device restored.');
    }
}
