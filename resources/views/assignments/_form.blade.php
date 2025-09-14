@csrf

<!-- Device & SIM Section -->
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path>
        </svg>
        {{ __('messages.assignments.device_and_connectivity') }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Device Selection -->
        <div class="space-y-2">
            <label for="device_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.assignments.device') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select name="device_id" id="device_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200" required>
                    <option value="">{{ __('messages.assignments.select_device') }}</option>
                    @foreach($devices as $device)
                        <option value="{{ $device->id }}" @selected(old('device_id', $assignment->device_id ?? null) == $device->id)>
                            {{ $device->imei }}
                        </option>
                    @endforeach
                </select>
            </div>
            @error('device_id')
                <p class="text-red-600 text-sm">{{ $message }}</p>
            @enderror
        </div>

        <!-- SIM Selection -->
        <div class="space-y-2">
            <label for="sim_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.assignments.sim_card') }}
            </label>
            <div class="relative">
                <select name="sim_id" id="sim_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200">
                    <option value="">{{ __('messages.assignments.select_sim_card') }}</option>
                    @foreach($sims as $sim)
                        <option value="{{ $sim->id }}" @selected(old('sim_id', $assignment->sim_id ?? null) == $sim->id)>
                            {{ $sim->msisdn }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Vehicle & Sensor Section -->
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        {{ __('messages.assignments.vehicle_and_sensor_assignment') }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Vehicle Selection -->
        <div class="space-y-2">
            <label for="vehicle_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.assignments.vehicle') }}
            </label>
            <div class="relative">
                <select name="vehicle_id" id="vehicle_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200">
                    <option value="">{{ __('messages.assignments.select_vehicle') }}</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $assignment->vehicle_id ?? null) == $vehicle->id)>
                            {{ $vehicle->plate }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Sensor Selection -->
        <div class="space-y-2">
            <label for="sensor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.assignments.sensor') }}
            </label>
            <div class="relative">
                <select name="sensor_id" id="sensor_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200">
                    <option value="">{{ __('messages.assignments.select_sensor') }}</option>
                    @foreach($sensors as $sensor)
                        <option value="{{ $sensor->id }}" @selected(old('sensor_id', $assignment->sensor_id ?? null) == $sensor->id)>
                            {{ $sensor->serial_or_bt_id }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Installation Details Section -->
<div class="mb-8">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
        <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
        </svg>
        {{ __('messages.assignments.installation_details') }}
    </h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Installation Status -->
        <div class="space-y-2">
            <label for="is_installed" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.assignments.installation_status') }} <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select name="is_installed" id="is_installed" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200" required>
                    <option value="0" @selected(old('is_installed', $assignment->is_installed ?? 0) == 0)>{{ __('messages.assignments.filters_not_installed') }}</option>
                    <option value="1" @selected(old('is_installed', $assignment->is_installed ?? 0) == 1)>{{ __('messages.assignments.filters_installed') }}</option>
                </select>
            </div>
        </div>

        <!-- Installation Date -->
        <div class="space-y-2">
            <label for="installed_on" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.assignments.installation_date') }}
            </label>
            <input type="date" 
                   name="installed_on" 
                   id="installed_on"
                   value="{{ old('installed_on', optional($assignment->installed_on ?? null)->format('Y-m-d')) }}" 
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200">
        </div>

        <!-- Installation Note -->
        <div class="space-y-2">
            <label for="install_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                {{ __('messages.assignments.installation_note') }}
            </label>
            <input type="text" 
                   name="install_note" 
                   id="install_note"
                   value="{{ old('install_note', $assignment->install_note ?? '') }}" 
                   placeholder="{{ __('messages.assignments.enter_installation_notes') }}"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white transition-colors duration-200">
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
    <button type="submit" 
            class="px-6 py-3 bg-blue-600 hover:bg-blue-700 font-medium rounded-lg transition-colors duration-200 flex items-center">
        @if(isset($assignment) && $assignment->exists)
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ __('messages.assignments.update_assignment') }}
        @else
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            {{ __('messages.assignments.create_assignment') }}
        @endif
    </button>
    <a href="{{ route('assignments.index') }}" 
       class="px-6 py-3 text-gray-700 dark:text-gray-300 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 rounded-lg font-medium transition-colors duration-200">
        {{ __('messages.common.cancel') }}
    </a>
</div>

<!-- JavaScript for enhanced UX -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-fill installation date when "Installed" is selected
        const isInstalledSelect = document.getElementById('is_installed');
        const installedOnInput = document.getElementById('installed_on');
        
        if (isInstalledSelect && installedOnInput) {
            isInstalledSelect.addEventListener('change', function() {
                if (this.value === '1' && !installedOnInput.value) {
                    installedOnInput.value = new Date().toISOString().split('T')[0];
                }
            });
        }

        // Add visual feedback for form interactions
        const form = document.querySelector('form');
        if (form) {
            const inputs = form.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('ring-2', 'ring-blue-500');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('ring-2', 'ring-blue-500');
                });
            });
        }
    });
</script>
