<?php

namespace App\Http\Controllers;

use App\Models\Carrier;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use App\Http\Requests\StoreCarrierRequest;
use App\Http\Requests\UpdateCarrierRequest;

class CarrierController extends Controller
{
    public function index(Request $request)
    {
        $qFilter = AllowedFilter::callback('q', function($query, $value){
            $query->where('name','like',"%{$value}%");
        });

        $carriers = QueryBuilder::for(Carrier::query())
            ->allowedFilters([$qFilter, AllowedFilter::trashed()])
            ->allowedSorts(['name','created_at'])
            ->defaultSort('name')
            ->paginate(20)
            ->withQueryString();

        return view('carriers.index', compact('carriers'));
    }

    public function create()
    {
        return view('carriers.create');
    }

    public function store(StoreCarrierRequest $request)
    {
        Carrier::create($request->validated());
        return redirect()->route('carriers.index')->with('success','Carrier created.');
    }

    public function edit(Carrier $carrier)
    {
        return view('carriers.edit', compact('carrier'));
    }

    public function update(UpdateCarrierRequest $request, Carrier $carrier)
    {
        $carrier->update($request->validated());
        return redirect()->route('carriers.index')->with('success','Carrier updated.');
    }

    public function destroy(Carrier $carrier)
    {
        $carrier->delete();
        return back()->with('success','Carrier deleted.');
    }

    public function restore($id)
    {
        $c = Carrier::withTrashed()->findOrFail($id);
        $c->restore();
        return back()->with('success','Carrier restored.');
    }
}
