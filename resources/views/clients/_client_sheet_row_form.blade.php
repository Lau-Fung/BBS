@once
    <style>
    .border-gray-200{border-color:#e5e7eb}
    .bg-white{background:#fff}
    .bg-gray-50{background:#f9fafb}
    .text-gray-700{color:#374151}
    .text-gray-800{color:#1f2937}
    .text-red-500{color:#ef4444}
    /* Force two columns on >=640px (tablet/desktop), single column on mobile */
    .two-col-grid{display:grid;grid-template-columns:1fr;gap:1.5rem}
    @media(min-width:640px){.two-col-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    </style>
@endonce

<form method="POST" action="{{ $action }}" class="space-y-6">
    @csrf
    @if(isset($clientSheetRow) && $clientSheetRow->exists)
        @method('PUT')
    @endif

    <div class="bg-white p-6 rounded-lg shadow-lg border border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            @if(isset($clientSheetRow) && $clientSheetRow->exists)
                {{ __('messages.clients.edit_row') }}
            @else
                {{ __('messages.clients.new_row') }}
            @endif
        </h3>

        <div class="two-col-grid">
            <!-- Package Type -->
            <div class="space-y-1">
                <label for="data_package_type" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.package_type') }}
                </label>
                <input type="text" 
                       name="data_package_type" 
                       id="data_package_type"
                       value="{{ old('data_package_type', $clientSheetRow->data_package_type ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- SIM Type -->
            <div class="space-y-1">
                <label for="sim_type" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.sim_type') }}
                </label>
                <input type="text" 
                       name="sim_type" 
                       id="sim_type"
                       value="{{ old('sim_type', $clientSheetRow->sim_type ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- SIM Number -->
            <div class="space-y-1">
                <label for="sim_number" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.sim_number') }}
                </label>
                <input type="text" 
                       name="sim_number" 
                       id="sim_number"
                       value="{{ old('sim_number', $clientSheetRow->sim_number ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- IMEI -->
            <div class="space-y-1">
                <label for="imei" class="block text-sm font-medium text-gray-700">
                    IMEI <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       name="imei" 
                       id="imei"
                       value="{{ old('imei', $clientSheetRow->imei ?? '') }}" 
                       required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Plate -->
            <div class="space-y-1">
                <label for="plate" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.plate') }}
                </label>
                <input type="text" 
                       name="plate" 
                       id="plate"
                       value="{{ old('plate', $clientSheetRow->plate ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Installation Date -->
            <div class="space-y-1">
                <label for="installed_on" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.installed_on') }}
                </label>
                <input type="date" 
                       name="installed_on" 
                       id="installed_on"
                       value="{{ old('installed_on', optional($clientSheetRow->installed_on ?? null)->format('Y-m-d')) }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Year Model -->
            <div class="space-y-1">
                <label for="year_model" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.year_model') }}
                </label>
                <input type="text" 
                       name="year_model" 
                       id="year_model"
                       value="{{ old('year_model', $clientSheetRow->year_model ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Company Manufacture -->
            <div class="space-y-1">
                <label for="company_manufacture" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.manufacturer') }}
                </label>
                <input type="text" 
                       name="company_manufacture" 
                       id="company_manufacture"
                       value="{{ old('company_manufacture', $clientSheetRow->company_manufacture ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Device Type -->
            <div class="space-y-1">
                <label for="device_type" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.device_type') }}
                </label>
                <input type="text" 
                       name="device_type" 
                       id="device_type"
                       value="{{ old('device_type', $clientSheetRow->device_type ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Air -->
            <div class="space-y-1">
                <label for="air" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.air') }}
                </label>
                <select name="air" id="air" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    <option value="">{{ __('messages.common.select') }}</option>
                    <option value="1" @selected(old('air', $clientSheetRow->air ?? null) == 1)>{{ __('messages.common.yes') }}</option>
                    <option value="0" @selected(old('air', $clientSheetRow->air ?? null) == 0)>{{ __('messages.common.no') }}</option>
                </select>
            </div>

            <!-- Mechanic -->
            <div class="space-y-1">
                <label for="mechanic" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.mechanic') }}
                </label>
                <select name="mechanic" id="mechanic" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                    <option value="">{{ __('messages.common.select') }}</option>
                    <option value="1" @selected(old('mechanic', $clientSheetRow->mechanic ?? null) == 1)>{{ __('messages.common.yes') }}</option>
                    <option value="0" @selected(old('mechanic', $clientSheetRow->mechanic ?? null) == 0)>{{ __('messages.common.no') }}</option>
                </select>
            </div>

            <!-- Tracking -->
            <div class="space-y-1">
                <label for="tracking" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.tracking') }}
                </label>
                <input type="text" 
                       name="tracking" 
                       id="tracking"
                       value="{{ old('tracking', $clientSheetRow->tracking ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- System Type -->
            <div class="space-y-1">
                <label for="system_type" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.system_type') }}
                </label>
                <input type="text" 
                       name="system_type" 
                       id="system_type"
                       value="{{ old('system_type', $clientSheetRow->system_type ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Calibration -->
            <div class="space-y-1">
                <label for="calibration" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.calibration') }}
                </label>
                <input type="text" 
                       name="calibration" 
                       id="calibration"
                       value="{{ old('calibration', $clientSheetRow->calibration ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Color -->
            <div class="space-y-1">
                <label for="color" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.color') }}
                </label>
                <input type="text" 
                       name="color" 
                       id="color"
                       value="{{ old('color', $clientSheetRow->color ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- CRM Integration -->
            <div class="space-y-1">
                <label for="crm_integration" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.crm') }}
                </label>
                <input type="text" 
                       name="crm_integration" 
                       id="crm_integration"
                       value="{{ old('crm_integration', $clientSheetRow->crm_integration ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Technician -->
            <div class="space-y-1">
                <label for="technician" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.technician') }}
                </label>
                <input type="text" 
                       name="technician" 
                       id="technician"
                       value="{{ old('technician', $clientSheetRow->technician ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Vehicle Serial Number -->
            <div class="space-y-1">
                <label for="vehicle_serial_number" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.vehicle_serial') }}
                </label>
                <input type="text" 
                       name="vehicle_serial_number" 
                       id="vehicle_serial_number"
                       value="{{ old('vehicle_serial_number', $clientSheetRow->vehicle_serial_number ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Vehicle Weight -->
            <div class="space-y-1">
                <label for="vehicle_weight" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.vehicle_weight') }}
                </label>
                <input type="text" 
                       name="vehicle_weight" 
                       id="vehicle_weight"
                       value="{{ old('vehicle_weight', $clientSheetRow->vehicle_weight ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- User -->
            <div class="space-y-1">
                <label for="user" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.user') }}
                </label>
                <input type="text" 
                       name="user" 
                       id="user"
                       value="{{ old('user', $clientSheetRow->user ?? '') }}" 
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
            </div>

            <!-- Notes -->
            <div class="space-y-1 md:col-span-2 lg:col-span-3">
                <label for="notes" class="block text-sm font-medium text-gray-700">
                    {{ __('messages.clients.notes') }}
                </label>
                <textarea name="notes" 
                          id="notes" 
                          rows="3"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 resize-none">{{ old('notes', $clientSheetRow->notes ?? '') }}</textarea>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 mt-6">
            <button type="submit" 
                    class="px-6 py-3 font-medium rounded-lg transition-all duration-200 flex items-center text-white shadow-lg hover:shadow-xl"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                @if(isset($clientSheetRow) && $clientSheetRow->exists)
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ __('messages.common.update') }}
                @else
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ __('messages.common.create') }}
                @endif
            </button>
            <button type="button" 
                    onclick="closeModal()"
                    class="ml-3 px-6 py-3 font-medium rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                    style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #4b5563 0%, #374151 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'">
                {{ __('messages.common.cancel') }}
            </button>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Processing...';
            
            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showNotification(data.message, 'success');
                    
                    // Close modal and reload page
                    setTimeout(() => {
                        closeModal();
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.reload();
                        }
                    }, 1000);
                } else {
                    showNotification(data.message || 'An error occurred', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            })
            .finally(() => {
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        });
    }
});

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg  ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
</script>
