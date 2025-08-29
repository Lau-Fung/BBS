@csrf
<div class="grid gap-3 md:grid-cols-2">
    <div>
        <label class="label">IMEI</label>
        <input class="input" name="imei" value="{{ old('imei', $device->imei ?? '') }}" required>
        @error('imei')<p class="text-red-600 text-xs">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="label">Model</label>
        <select class="input" name="device_model_id" required>
            @foreach($deviceModels as $m)
            <option value="{{ $m->id }}" @selected(old('device_model_id', $device->device_model_id ?? '')==$m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">Firmware</label>
        <input class="input" name="firmware" value="{{ old('firmware', $device->firmware ?? '') }}">
    </div>
    <div>
        <label class="label">Active?</label>
        <select class="input" name="is_active">
            <option value="1" @selected(old('is_active', $device->is_active ?? 1)==1)>Yes</option>
            <option value="0" @selected(old('is_active', $device->is_active ?? 1)==0)>No</option>
        </select>
    </div>
</div>
<div class="mt-4">
    <button class="btn-primary">Save</button>
    <a href="{{ route('devices.index') }}" class="btn">Cancel</a>
</div>
