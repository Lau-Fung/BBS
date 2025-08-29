@csrf

<!-- SIM Details -->
<div class="mb-8">
	<h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
		<svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
		</svg>
		SIM Details
	</h2>
	<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Carrier</label>
			<select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="carrier_id" required>
				@foreach($carriers as $c)
				<option value="{{ $c->id }}" @selected(old('carrier_id', $sim->carrier_id ?? '')==$c->id)>{{ $c->name }}</option>
				@endforeach
			</select>
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">MSISDN</label>
			<input class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="msisdn" value="{{ old('msisdn', $sim->msisdn ?? '') }}">
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">SIM Serial</label>
			<input class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="sim_serial" value="{{ old('sim_serial', $sim->sim_serial ?? '') }}">
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Plan Expiry</label>
			<input class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" type="date" name="plan_expiry_at" value="{{ old('plan_expiry_at', optional($sim->plan_expiry_at ?? null)->format('Y-m-d')) }}">
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Recharged</label>
			<select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="is_recharged">
				<option value="1" @selected(old('is_recharged', $sim->is_recharged ?? 0)==1)>Yes</option>
				<option value="0" @selected(old('is_recharged', $sim->is_recharged ?? 0)==0)>No</option>
			</select>
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Active</label>
			<select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="is_active">
				<option value="1" @selected(old('is_active', $sim->is_active ?? 1)==1)>Yes</option>
				<option value="0" @selected(old('is_active', $sim->is_active ?? 1)==0)>No</option>
			</select>
		</div>
	</div>
</div>

<!-- Actions -->
<div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
	<button class="px-6 py-3 bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition-colors duration-200 flex items-center">
		<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
		</svg>
		Save
	</button>
	<a href="{{ route('sims.index') }}" class="px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors duration-200">Cancel</a>
</div>
