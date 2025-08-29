<?php
// app/Http/Controllers/AssignmentController.php
namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Carrier;
use App\Models\DeviceModel;
use App\Models\Vehicle;
use App\Models\Sim;
use App\Models\Device;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Assignment::class);

        // Build filterable/sortable listing
        $qFilter = AllowedFilter::callback('q', function ($query, $value) {
            $value = trim($value);
            $query->where(function ($q) use ($value) {
                $q->whereHas('vehicle', fn($s) => $s->where('plate', 'like', "%{$value}%"))
                  ->orWhereHas('device',  fn($s) => $s->where('imei',  'like', "%{$value}%"))
                  ->orWhereHas('sim',     fn($s) => $s->where('msisdn','like', "%{$value}%"))
                  ->orWhereHas('sensor',  fn($s) => $s->where('serial_or_bt_id','like', "%{$value}%"));
            });
        });

        $assignments = QueryBuilder::for(Assignment::query()->with([
                'vehicle:id,plate,tank_capacity_liters,status,crm_no',
                'device:id,imei,device_model_id',
                'device.model:id,name',
                'sim:id,msisdn,carrier_id,plan_expiry_at',
                'sim.carrier:id,name',
                'sensor:id,serial_or_bt_id',
            ]))
            ->allowedFilters([
                $qFilter,
                AllowedFilter::exact('is_installed'),
                AllowedFilter::exact('device_model_id', 'device.device_model_id'),
                AllowedFilter::exact('carrier_id', 'sim.carrier_id'),
                AllowedFilter::exact('vehicle_status', 'vehicle.status'),
                AllowedFilter::scope('expiry_from'), // scopes shown below
                AllowedFilter::scope('expiry_to'),
                AllowedFilter::scope('capacity_min'),
                AllowedFilter::scope('capacity_max'),
                AllowedFilter::trashed(), // if you use SoftDeletes
            ])
            ->allowedSorts([
                AllowedSort::field('plate','vehicles.plate'),
                AllowedSort::field('imei','devices.imei'),
                AllowedSort::field('msisdn','sims.msisdn'),
                'is_installed',
                'created_at',
            ])
            // Optional default sort:
            ->defaultSort('created_at')
            // Join tables for sort columns:
            ->leftJoin('devices', 'assignments.device_id', '=', 'devices.id')
            ->leftJoin('sims', 'assignments.sim_id', '=', 'sims.id')
            ->leftJoin('vehicles', 'assignments.vehicle_id', '=', 'vehicles.id')
            ->select('assignments.*')
            ->paginate(20)
            ->withQueryString();

        // Filter dropdown data
        $deviceModels = DeviceModel::orderBy('name')->get(['id','name']);
        $carriers     = Carrier::orderBy('name')->get(['id','name']);
        $vehicleStatuses = ['جاهز','صالح','خارج الخدمة','معلق'];

        return view('assignments.index', compact('assignments','deviceModels','carriers','vehicleStatuses'));
    }

    public function create()
    {
        $this->authorize('create', Assignment::class);
        return view('assignments.create', [
            'assignment' => new Assignment(),
            'devices'  => Device::orderBy('imei')->get(['id','imei']),
            'sims'     => Sim::orderBy('msisdn')->get(['id','msisdn']),
            'vehicles' => Vehicle::orderBy('plate')->get(['id','plate']),
            'sensors'  => Sensor::orderBy('serial_or_bt_id')->get(['id','serial_or_bt_id']),
        ]);
    }

    public function store(StoreAssignmentRequest $request)
    {
        $this->authorize('create', Assignment::class);
        $assignment = Assignment::create($request->validated());
        return redirect()->route('assignments.index')
            ->with('success', 'Assignment created.');
    }

    public function edit(Assignment $assignment)
    {
        $this->authorize('update', $assignment);
        $assignment->load(['vehicle','device','sim','sensor']);
        return view('assignments.edit', [
            'assignment'=> $assignment,
            'devices'  => Device::orderBy('imei')->get(['id','imei']),
            'sims'     => Sim::orderBy('msisdn')->get(['id','msisdn']),
            'vehicles' => Vehicle::orderBy('plate')->get(['id','plate']),
            'sensors'  => Sensor::orderBy('serial_or_bt_id')->get(['id','serial_or_bt_id']),
        ]);
    }

    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        $this->authorize('update', $assignment);
        $assignment->update($request->validated());
        return redirect()->route('assignments.index')
            ->with('success', 'Assignment updated.');
    }

    public function destroy(Assignment $assignment)
    {
        $this->authorize('delete', $assignment);
        $assignment->delete(); // use SoftDeletes on the model for safety
        return back()->with('success', 'Assignment deleted.');
    }

    public function restore($assignmentId)
    {
        $assignment = Assignment::withTrashed()->findOrFail($assignmentId);
        $this->authorize('restore', $assignment);
        $assignment->restore();
        return back()->with('success', 'Assignment restored.');
    }
}
