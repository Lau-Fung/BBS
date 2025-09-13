<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Assignments</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage device assignments and installations</p>
                </div>
                <div class="flex flex-row gap-3 justify-center items-center">
                    <a href="{{ route('assignments.create') }}" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        New Assignment
                    </a>
                    <a href="{{ route('imports.assignments.form') }}" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                        </svg>
                        Import from Excel
                    </a>
                    <a href="{{ route('exports.assignments', ['format' => 'xlsx']) }}" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                        </svg>

                        Export XLSX
                    </a>
                    <a href="{{ route('exports.assignments', ['format' => 'csv']) }}" 
                    class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                        </svg>

                        Export CSV
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                    </svg>
                    Filters
                </h2>
                
                <form method="GET" class="space-y-4">
                    <!-- Search and Basic Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                        <div class="md:col-span-2 lg:col-span-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.common.search') }}</label>
                            <input type="text" 
                                   name="filter[q]" 
                                   value="{{ request('filter.q') }}" 
                                   placeholder="Plate, IMEI, MSISDN, Sensor..." 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Device Model</label>
                            <select name="filter[device_model_id]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Models</option>
                                @foreach($deviceModels as $model)
                                    <option value="{{ $model->id }}" @selected(request('filter.device_model_id') == $model->id)>{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Carrier</label>
                            <select name="filter[carrier_id]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Carriers</option>
                                @foreach($carriers as $carrier)
                                    <option value="{{ $carrier->id }}" @selected(request('filter.carrier_id') == $carrier->id)>{{ $carrier->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Vehicle Status</label>
                            <select name="filter[vehicle_status]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All Statuses</option>
                                @foreach($vehicleStatuses as $status)
                                    <option value="{{ $status }}" @selected(request('filter.vehicle_status') == $status)>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Installation</label>
                            <select name="filter[is_installed]" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="">All</option>
                                <option value="1" @selected(request('filter.is_installed') === '1')>Installed</option>
                                <option value="0" @selected(request('filter.is_installed') === '0')>Not Installed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Date and Capacity Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SIM Expiry From</label>
                            <input type="date" 
                                   name="filter[expiry_from]" 
                                   value="{{ request('filter.expiry_from') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">SIM Expiry To</label>
                            <input type="date" 
                                   name="filter[expiry_to]" 
                                   value="{{ request('filter.expiry_to') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacity Min (L)</label>
                            <input type="number" 
                                   name="filter[capacity_min]" 
                                   value="{{ request('filter.capacity_min') }}" 
                                   placeholder="Min capacity" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Capacity Max (L)</label>
                            <input type="number" 
                                   name="filter[capacity_max]" 
                                   value="{{ request('filter.capacity_max') }}" 
                                   placeholder="Max capacity" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>

                    <!-- Filter Actions -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-blue-600 hover:bg-blue-700 bg-gray-100 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"></path>
                            </svg>
                            Apply Filters
                        </button>
                        <a href="{{ route('assignments.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-6 py-3 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Results Summary -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="text-sm text-gray-600 dark:text-gray-400">
                Showing {{ $assignments->firstItem() ?? 0 }} to {{ $assignments->lastItem() ?? 0 }} of {{ $assignments->total() }} assignments
            </div>
            <div class="mt-2 sm:mt-0">
                <select id="viewMode" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white">
                    <option value="table">Table View</option>
                    <option value="cards">Card View</option>
                </select>
            </div>
        </div>

        <!-- Table View (Desktop) -->
        <div id="tableView" class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 m-auto" style="width: 90%;">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        @php
                            function sort_link($field, $label) {
                                $current = request('sort');
                                $dir = $current === $field ? "-$field" : $field;
                                $params = array_merge(request()->query(), ['sort' => $dir]);
                                $url = url()->current().'?'.http_build_query($params);
                                return "<a href=\"$url\" class=\"hover:underline flex items-center\">$label</a>";
                            }
                        @endphp
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {!! sort_link('plate','Vehicle') !!}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {!! sort_link('imei','Device') !!}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {!! sort_link('msisdn','SIM') !!}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Device Model
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Carrier
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {!! sort_link('is_installed','Status') !!}
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {!! sort_link('created_at','Created') !!}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($assignments as $assignment)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $assignment->vehicle?->plate ?? '—' }}
                                    </div>
                                    @if($assignment->vehicle?->tank_capacity_liters)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $assignment->vehicle->tank_capacity_liters }}L
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $assignment->device?->imei ?? '—' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        {{ $assignment->sim?->msisdn ?? '—' }}
                                    </div>
                                    @if($assignment->sim?->plan_expiry_at)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            Expires: {{ $assignment->sim->plan_expiry_at->format('Y-m-d') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $assignment->device?->model?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $assignment->sim?->carrier?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->is_installed ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                                        {{ $assignment->is_installed ? 'Installed' : 'Not Installed' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $assignment->created_at->format('Y-m-d') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('assignments.edit', $assignment) }}" 
                                           class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" style="padding-right: 10px;">
                                            Edit
                                        </a>
                                        <form action="{{ route('assignments.destroy', $assignment) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </form>
                                        @if(method_exists($assignment, 'trashed') && $assignment->trashed())
                                            <form action="{{ route('assignments.restore', $assignment->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-amber-600 hover:text-amber-900 dark:text-amber-400 dark:hover:text-amber-300">
                                                    Restore
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-9 w-9 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No assignments found</h3>
                                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new assignment.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('assignments.create') }}" class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                New Assignment
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Card View (Mobile) -->
        <div id="cardView" class="hidden space-y-4">
            @forelse($assignments as $assignment)
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $assignment->vehicle?->plate ?? 'No Vehicle' }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Device: {{ $assignment->device?->imei ?? 'No Device' }}
                            </p>
                        </div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $assignment->is_installed ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200' }}">
                            {{ $assignment->is_installed ? 'Installed' : 'Not Installed' }}
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">SIM</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->sim?->msisdn ?? '—' }}</p>
                            @if($assignment->sim?->carrier?->name)
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $assignment->sim->carrier->name }}</p>
                            @endif
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Model</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->device?->model?->name ?? '—' }}</p>
                        </div>
                        @if($assignment->vehicle?->tank_capacity_liters)
                            <div>
                                <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Capacity</p>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->vehicle->tank_capacity_liters }}L</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('messages.table.created') }}</p>
                            <p class="text-sm text-gray-900 dark:text-white">{{ $assignment->created_at->format('Y-m-d') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('assignments.edit', $assignment) }}" 
                           class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200">
                            Edit
                        </a>
                        <form action="{{ route('assignments.destroy', $assignment) }}" 
                              method="POST" 
                              class="inline"
                              onsubmit="return confirm('Are you sure you want to delete this assignment?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 transition-colors duration-200">
                                Delete
                            </button>
                        </form>
                        @if(method_exists($assignment, 'trashed') && $assignment->trashed())
                            <form action="{{ route('assignments.restore', $assignment->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-amber-600 hover:bg-amber-700 transition-colors duration-200">
                                    Restore
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-xl border border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div class="flex flex-col items-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No assignments found</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-sm">Get started by creating your first assignment to manage devices and vehicles.</p>
                        <a href="{{ route('assignments.create') }}" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create New Assignment
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $assignments->links() }}
        </div>
    </div>

    <!-- JavaScript for view switching -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const viewModeSelect = document.getElementById('viewMode');
            const tableView = document.getElementById('tableView');
            const cardView = document.getElementById('cardView');
            
            // Check screen size and set initial view
            function setInitialView() {
                if (window.innerWidth < 768) { // Mobile
                    viewModeSelect.value = 'cards';
                    tableView.classList.add('hidden');
                    cardView.classList.remove('hidden');
                } else { // Desktop
                    viewModeSelect.value = 'table';
                    tableView.classList.remove('hidden');
                    cardView.classList.add('hidden');
                }
            }
            
            // Set initial view
            setInitialView();
            
            // Handle view mode change
            viewModeSelect.addEventListener('change', function() {
                if (this.value === 'cards') {
                    tableView.classList.add('hidden');
                    cardView.classList.remove('hidden');
                } else {
                    tableView.classList.remove('hidden');
                    cardView.classList.add('hidden');
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', setInitialView);
        });
    </script>
</x-app-layout>
