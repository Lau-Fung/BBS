<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        $qFilter = AllowedFilter::callback('q', function ($query, $value) {
            $value = trim($value);
            $query->where(function($q) use ($value){
                $q->where('plate','like',"%{$value}%")
                  ->orWhere('crm_no','like',"%{$value}%")
                  ->orWhere('notes','like',"%{$value}%");
            });
        });

        $vehicles = QueryBuilder::for(Vehicle::query()->with('supervisor:id,name'))
            ->allowedFilters([
                $qFilter,
                AllowedFilter::exact('status'),
                AllowedFilter::scope('capacity_min'),
                AllowedFilter::scope('capacity_max'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts([
                'plate','tank_capacity_liters','status','created_at'
            ])
            ->defaultSort('created_at')
            ->paginate(20)
            ->withQueryString();

        $statuses = ['جاهز','صالح','خارج الخدمة','معلق'];
        $supervisors = User::orderBy('name')->get(['id','name']);
        return view('vehicles.index', compact('vehicles','statuses','supervisors'));
    }

    // scopes used by query builder
    // app/Models/Vehicle.php -> add:
    // public function scopeCapacityMin($q,$v){$q->where('tank_capacity_liters','>=',(int)$v);}
    // public function scopeCapacityMax($q,$v){$q->where('tank_capacity_liters','<=',(int)$v);}

    public function create()
    {
        $statuses = ['جاهز','صالح','خارج الخدمة','معلق'];
        $supervisors = User::orderBy('name')->get(['id','name']);
        return view('vehicles.create', compact('statuses','supervisors'));
    }

    public function store(StoreVehicleRequest $request)
    {
        Vehicle::create($request->validated());
        return redirect()->route('vehicles.index')->with('success','Vehicle created.');
    }

    public function edit(Vehicle $vehicle)
    {
        $statuses = ['جاهز','صالح','خارج الخدمة','معلق'];
        $supervisors = User::orderBy('name')->get(['id','name']);
        return view('vehicles.edit', compact('vehicle','statuses','supervisors'));
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle)
    {
        $vehicle->update($request->validated());
        return redirect()->route('vehicles.index')->with('success','Vehicle updated.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $vehicle->delete();
        return back()->with('success','Vehicle deleted.');
    }

    public function restore($id)
    {
        $v = Vehicle::withTrashed()->findOrFail($id);
        $v->restore();
        return back()->with('success','Vehicle restored.');
    }
}

