@csrf
<div class="grid gap-3 md:grid-cols-2">
    <div>
        <label class="label">Serial / BT ID</label>
        <input class="input" name="serial_or_bt_id" value="{{ old('serial_or_bt_id', $sensor->serial_or_bt_id ?? '') }}" required>
        @error('serial_or_bt_id')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="label">Model</label>
        <select class="input" name="sensor_model_id">
            <option value="">—</option>
            @foreach($sensorModels as $m)
            <option value="{{ $m->id }}" @selected(old('sensor_model_id', $sensor->sensor_model_id ?? '')==$m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="md:col-span-2">
        <label class="label">Notes</label>
        <input class="input" name="notes" value="{{ old('notes', $sensor->notes ?? '') }}">
    </div>
</div>
<div class="mt-4">
    <button class="btn-primary">Save</button>
    <a href="{{ route('sensors.index') }}" class="btn">Cancel</a>
</div>
