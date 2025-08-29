<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use App\Models\SensorModel;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Http\Requests\StoreSensorRequest;
use App\Http\Requests\UpdateSensorRequest;

class SensorController extends Controller
{
    public function index(Request $request)
    {
        $qFilter = AllowedFilter::callback('q', function ($query, $value) {
            $value = trim($value);
            $query->where('serial_or_bt_id','like',"%{$value}%")
                  ->orWhere('notes','like',"%{$value}%");
        });

        $sensors = QueryBuilder::for(Sensor::query()->with('model:id,name'))
            ->allowedFilters([
                $qFilter,
                AllowedFilter::exact('sensor_model_id'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['serial_or_bt_id','created_at'])
            ->defaultSort('created_at')
            ->paginate(20)
            ->withQueryString();

        $sensorModels = SensorModel::orderBy('name')->get(['id','name']);
        return view('sensors.index', compact('sensors','sensorModels'));
    }

    public function create()
    {
        $sensorModels = SensorModel::orderBy('name')->get(['id','name']);
        return view('sensors.create', compact('sensorModels'));
    }

    public function store(StoreSensorRequest $request)
    {
        Sensor::create($request->validated());
        return redirect()->route('sensors.index')->with('success','Sensor created.');
    }

    public function edit(Sensor $sensor)
    {
        $sensorModels = SensorModel::orderBy('name')->get(['id','name']);
        return view('sensors.edit', compact('sensor','sensorModels'));
    }

    public function update(UpdateSensorRequest $request, Sensor $sensor)
    {
        $sensor->update($request->validated());
        return redirect()->route('sensors.index')->with('success','Sensor updated.');
    }

    public function destroy(Sensor $sensor)
    {
        $sensor->delete();
        return back()->with('success','Sensor deleted.');
    }

    public function restore($id)
    {
        $s = Sensor::withTrashed()->findOrFail($id);
        $s->restore();
        return back()->with('success','Sensor restored.');
    }
}
