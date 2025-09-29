<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200" style="min-width: 1200px;">
    <h3 class="text-lg font-semibold text-gray-800 mb-4">
        {{ __('messages.clients.edit_all') }} - {{ $client->name }} ({{ $clientSheetRows->count() }} {{ __('messages.clients.total_records') }})
    </h3>

    <div class="mb-4 flex justify-between items-center">
        <div class="text-sm text-gray-600">
            <span id="modifiedCount">0</span> {{ __('messages.common.modified') }}
        </div>
        <div class="flex gap-4">
            <button type="button" onclick="saveAllChanges()" 
                    class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #059669 0%, #047857 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #10b981 0%, #059669 100%)'">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ __('messages.common.save_all') }}
            </button>
            <button type="button" onclick="resetAllChanges()" 
                    class="inline-flex items-center px-6 py-3 text-sm font-medium rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                    style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #4b5563 0%, #374151 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ __('messages.common.reset') }}
            </button>
        </div>
    </div>

    <form id="editAllForm">
        @csrf
        @method('POST')
        
        <div class="space-y-4" style="min-width: 1100px;">
            @foreach($clientSheetRows as $index => $row)
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-700">Row #{{ $index + 1 }}</h4>
                        <span class="text-xs text-gray-500">ID: {{ $row->id }}</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Hidden ID field -->
                        <input type="hidden" name="rows[{{ $index }}][id]" value="{{ $row->id }}">
                        

                        <!-- Package Type -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.package_type') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][data_package_type]" 
                                   value="{{ $row->data_package_type ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->data_package_type ?? '' }}">
                        </div>

                        <!-- SIM Type -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.sim_type') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][sim_type]" 
                                   value="{{ $row->sim_type ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->sim_type ?? '' }}">
                        </div>

                        <!-- SIM Number -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.sim_number') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][sim_number]" 
                                   value="{{ $row->sim_number ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->sim_number ?? '' }}">
                        </div>

                        <!-- IMEI -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                IMEI <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][imei]" 
                                   value="{{ $row->imei ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->imei ?? '' }}">
                        </div>

                        <!-- Plate -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.plate') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][plate]" 
                                   value="{{ $row->plate ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->plate ?? '' }}">
                        </div>

                        <!-- Installed On -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.installed_on') }}
                            </label>
                            <input type="date" 
                                   name="rows[{{ $index }}][installed_on]" 
                                   value="{{ $row->installed_on ? $row->installed_on->format('Y-m-d') : '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->installed_on ? $row->installed_on->format('Y-m-d') : '' }}">
                        </div>

                        <!-- Year Model -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.year_model') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][year_model]" 
                                   value="{{ $row->year_model ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->year_model ?? '' }}">
                        </div>

                        <!-- Manufacturer -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.manufacturer') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][company_manufacture]" 
                                   value="{{ $row->company_manufacture ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->company_manufacture ?? '' }}">
                        </div>

                        <!-- Device Type -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.device_type') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][device_type]" 
                                   value="{{ $row->device_type ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->device_type ?? '' }}">
                        </div>

                        <!-- Air -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.air') }}
                            </label>
                            <select name="rows[{{ $index }}][air]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    data-original="{{ $row->air ? '1' : '0' }}">
                                <option value="0" {{ !$row->air ? 'selected' : '' }}>{{ __('messages.common.no') }}</option>
                                <option value="1" {{ $row->air ? 'selected' : '' }}>{{ __('messages.common.yes') }}</option>
                            </select>
                        </div>

                        <!-- Sensor Type -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.sensor_type') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][sensor_type]" 
                                   value="{{ $row->sensor_type ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->sensor_type ?? '' }}">
                        </div>

                        <!-- Mechanic -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.mechanic') }}
                            </label>
                            <select name="rows[{{ $index }}][mechanic]" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    data-original="{{ $row->mechanic ? '1' : '0' }}">
                                <option value="0" {{ !$row->mechanic ? 'selected' : '' }}>{{ __('messages.common.no') }}</option>
                                <option value="1" {{ $row->mechanic ? 'selected' : '' }}>{{ __('messages.common.yes') }}</option>
                            </select>
                        </div>

                        <!-- Tracking -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.tracking') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][tracking]" 
                                   value="{{ $row->tracking ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->tracking ?? '' }}">
                        </div>

                        <!-- System Type -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.system_type') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][system_type]" 
                                   value="{{ $row->system_type ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->system_type ?? '' }}">
                        </div>

                        <!-- Calibration -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.calibration') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][calibration]" 
                                   value="{{ $row->calibration ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->calibration ?? '' }}">
                        </div>

                        <!-- Color -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.color') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][color]" 
                                   value="{{ $row->color ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->color ?? '' }}">
                        </div>

                        <!-- CRM -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.crm') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][crm_integration]" 
                                   value="{{ $row->crm_integration ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->crm_integration ?? '' }}">
                        </div>

                        <!-- Subscription Type -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.subscription') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][subscription_type]" 
                                   value="{{ $row->subscription_type ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->subscription_type ?? '' }}">
                        </div>

                        <!-- Technician -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.technician') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][technician]" 
                                   value="{{ $row->technician ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->technician ?? '' }}">
                        </div>

                        <!-- Vehicle Serial -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.vehicle_serial') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][vehicle_serial_number]" 
                                   value="{{ $row->vehicle_serial_number ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->vehicle_serial_number ?? '' }}">
                        </div>

                        <!-- Vehicle Weight -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.vehicle_weight') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][vehicle_weight]" 
                                   value="{{ $row->vehicle_weight ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->vehicle_weight ?? '' }}">
                        </div>

                        <!-- User -->
                        <div class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.user') }}
                            </label>
                            <input type="text" 
                                   name="rows[{{ $index }}][user]" 
                                   value="{{ $row->user ?? '' }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   data-original="{{ $row->user ?? '' }}">
                        </div>

                        <!-- Notes -->
                        <div class="space-y-1 md:col-span-2 lg:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">
                                {{ __('messages.clients.notes') }}
                            </label>
                            <textarea name="rows[{{ $index }}][notes]" 
                                      rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      data-original="{{ $row->notes ?? '' }}">{{ $row->notes ?? '' }}</textarea>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

