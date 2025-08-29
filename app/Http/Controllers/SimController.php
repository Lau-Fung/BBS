<?php
// app/Http/Controllers/SimController.php
namespace App\Http\Controllers;

use App\Models\Sim;
use App\Models\Carrier;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Http\Requests\StoreSimRequest;
use App\Http\Requests\UpdateSimRequest;

class SimController extends Controller
{
    public function index(Request $request)
    {
        $qFilter = AllowedFilter::callback('q', function ($query, $value) {
            $value = trim($value);
            $query->where(function($q) use ($value){
                $q->where('msisdn','like',"%{$value}%")
                  ->orWhere('sim_serial','like',"%{$value}%");
            });
        });

        $sims = QueryBuilder::for(Sim::query()->with('carrier:id,name'))
            ->allowedFilters([
                $qFilter,
                AllowedFilter::exact('carrier_id'),
                AllowedFilter::scope('expiry_from'),
                AllowedFilter::scope('expiry_to'),
                AllowedFilter::exact('is_recharged'),
                AllowedFilter::exact('is_active'),
                AllowedFilter::trashed(),
            ])
            ->allowedSorts(['msisdn','plan_expiry_at','is_active','created_at'])
            ->defaultSort('created_at')
            ->paginate(20)
            ->withQueryString();

        $carriers = Carrier::orderBy('name')->get(['id','name']);
        return view('sims.index', compact('sims','carriers'));
    }

    // scopes on Sim model:
    // public function scopeExpiryFrom($q,$d){$q->whereDate('plan_expiry_at','>=',$d);}
    // public function scopeExpiryTo($q,$d){$q->whereDate('plan_expiry_at','<=',$d);}

    public function create()
    {
        $carriers = Carrier::orderBy('name')->get(['id','name']);
        return view('sims.create', compact('carriers'));
    }

    public function store(StoreSimRequest $request)
    {
        Sim::create($request->validated());
        return redirect()->route('sims.index')->with('success','SIM created.');
    }

    public function edit(Sim $sim)
    {
        $carriers = Carrier::orderBy('name')->get(['id','name']);
        return view('sims.edit', compact('sim','carriers'));
    }

    public function update(UpdateSimRequest $request, Sim $sim)
    {
        $sim->update($request->validated());
        return redirect()->route('sims.index')->with('success','SIM updated.');
    }

    public function destroy(Sim $sim)
    {
        $sim->delete();
        return back()->with('success','SIM deleted.');
    }

    public function restore($id)
    {
        $s = Sim::withTrashed()->findOrFail($id);
        $s->restore();
        return back()->with('success','SIM restored.');
    }
}
