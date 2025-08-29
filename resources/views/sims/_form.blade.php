@csrf
<div class="grid gap-3 md:grid-cols-2">
    <div>
        <label class="label">Carrier</label>
        <select class="input" name="carrier_id" required>
            @foreach($carriers as $c)
            <option value="{{ $c->id }}" @selected(old('carrier_id', $sim->carrier_id ?? '')==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="label">MSISDN</label>
        <input class="input" name="msisdn" value="{{ old('msisdn', $sim->msisdn ?? '') }}">
    </div>
    <div>
        <label class="label">SIM Serial</label>
        <input class="input" name="sim_serial" value="{{ old('sim_serial', $sim->sim_serial ?? '') }}">
    </div>
    <div>
        <label class="label">Plan Expiry</label>
        <input class="input" type="date" name="plan_expiry_at" value="{{ old('plan_expiry_at', optional($sim->plan_expiry_at ?? null)->format('Y-m-d')) }}">
    </div>
    <div>
        <label class="label">Recharged?</label>
        <select class="input" name="is_recharged">
            <option value="1" @selected(old('is_recharged', $sim->is_recharged ?? 0)==1)>Yes</option>
            <option value="0" @selected(old('is_recharged', $sim->is_recharged ?? 0)==0)>No</option>
        </select>
    </div>
    <div>
        <label class="label">Active?</label>
        <select class="input" name="is_active">
            <option value="1" @selected(old('is_active', $sim->is_active ?? 1)==1)>Yes</option>
            <option value="0" @selected(old('is_active', $sim->is_active ?? 1)==0)>No</option>
        </select>
    </div>
</div>
<div class="mt-4">
    <button class="btn-primary">Save</button>
    <a href="{{ route('sims.index') }}" class="btn">Cancel</a>
</div>
