@csrf
<div class="grid gap-3 md:grid-cols-2">
    <div>
        <label class="label">Plate</label>
        <input class="input" name="plate" value="{{ old('plate', $vehicle->plate ?? '') }}" required>
        @error('plate')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="label">Tank Capacity (L)</label>
        <input class="input" type="number" name="tank_capacity_liters" value="{{ old('tank_capacity_liters', $vehicle->tank_capacity_liters ?? '') }}">
    </div>
    <div>
        <label class="label">Status</label>
        <select class="input" name="status" required>
            @foreach($statuses as $s)
            <option value="{{ $s }}" @selected(old('status', $vehicle->status ?? '')===$s)>{{ $s }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">CRM No.</label>
        <input class="input" name="crm_no" value="{{ old('crm_no', $vehicle->crm_no ?? '') }}">
    </div>
    <div class="md:col-span-2">
        <label class="label">Notes</label>
        <input class="input" name="notes" value="{{ old('notes', $vehicle->notes ?? '') }}">
    </div>
</div>
<div class="mt-4">
    <button class="btn-primary">Save</button>
    <a href="{{ route('vehicles.index') }}" class="btn">Cancel</a>
</div>
