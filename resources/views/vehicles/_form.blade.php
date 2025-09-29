@csrf

<!-- Vehicle Details -->
<div class="mb-8">
	<h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
		<svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
		</svg>
		{{ __('messages.vehicles.details') }}
	</h2>
	<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
		@php($clients = \App\Models\Client::orderBy('name')->get())
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.clients.title') }}</label>
			<select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="client_id" required>
				@foreach($clients as $client)
					<option value="{{ $client->id }}" @selected(old('client_id', $client->id ?? '')===$client->id)>{{ $client->name }}</option>
				@endforeach
			</select>
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.vehicles.plate') }}</label>
			<input class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="plate" value="{{ old('plate', $vehicle->plate ?? '') }}" required>
			@error('plate')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.vehicles.tank_capacity') }}</label>
			<input class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" type="number" name="tank_capacity_liters" value="{{ old('tank_capacity_liters', $vehicle->tank_capacity_liters ?? '') }}">
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.table.status') }}</label>
			<select class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="status" required>
				@foreach($statuses as $s)
				<option value="{{ $s }}" @selected(old('status', $vehicle->status ?? '')===$s)>{{ $s }}</option>
				@endforeach
			</select>
		</div>
		<div class="space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.vehicles.crm_no') }}</label>
			<input class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="crm_no" value="{{ old('crm_no', $vehicle->crm_no ?? '') }}">
		</div>
		<div class="md:col-span-2 space-y-2">
			<label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('messages.vehicles.notes') }}</label>
			<input class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" name="notes" value="{{ old('notes', $vehicle->notes ?? '') }}">
		</div>
	</div>
</div>

<!-- Actions -->
<div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
	<button class="px-6 py-3 font-medium rounded-lg transition-all duration-200 flex items-center text-white shadow-lg hover:shadow-xl"
	        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
	        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
	        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
		<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
		</svg>
		{{ __('messages.common.save') }}
	</button>
	<a href="{{ route('vehicles.index') }}" 
	   class="px-6 py-3 font-medium rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
	   style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);"
	   onmouseover="this.style.background='linear-gradient(135deg, #4b5563 0%, #374151 100%)'"
	   onmouseout="this.style.background='linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'">{{ __('messages.common.cancel') }}</a>
</div>
