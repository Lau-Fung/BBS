@once
    <style>
    /* borders, spacing, simple colors used in this view only */
    .border-gray-200{border-color:#e5e7eb}
    .border-red-300{border-color:#ef4444}
    .ring-red{box-shadow: 0 0 0 2px rgba(239,68,68,.35)}
    .border-amber-300{border-color:#fcd34d}
    .bg-red-50{background:#fef2f2}
    .bg-amber-50{background:#fffbeb}
    .bg-white{background:#fff}
    .bg-gray-50{background:#f9fafb}
    .text-red-800{color:#991b1b}
    .text-amber-800{color:#92400e}
    .text-gray-600{color:#4b5563}
    .text-gray-700{color:#374151}
    .text-gray-800{color:#1f2937}
    .odd\:bg-white:nth-child(odd){background:#fff}
    .even\:bg-gray-50:nth-child(even){background:#f9fafb}
    .scroll{max-height:220px;overflow:auto}
    /* Buttons responsive grid: 1,2,3,5 columns */
    .buttons-grid{display:grid;grid-template-columns:1fr;gap:12px}
    @media (min-width:640px){.buttons-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (min-width:768px){.buttons-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
    @media (min-width:1024px){.buttons-grid{grid-template-columns:repeat(5,minmax(0,1fr))}}
    /* Inline edit: make inputs wider so the table can scroll horizontally */
    .inline-input{min-width: 200px}
    .inline-input.sm{min-width: 120px}
    </style>
@endonce

<meta name="csrf-token" content="{{ csrf_token() }}">
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            {{ $client->name }} — {{ __('messages.clients.details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg p-6" style="border: 1px solid #e5e7eb;">
                <style>
                    .filters-bar{background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 6px 16px rgba(0,0,0,.05);padding:14px 16px;margin-bottom:16px}
                    .filters-grid{display:grid;grid-template-columns:1fr;gap:12px}
                    @media (min-width:640px){.filters-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
                    @media (min-width:1024px){.filters-grid{grid-template-columns:repeat(6,minmax(0,1fr))}}
                    .filters-bar label{display:block;font-size:12px;color:#6b7280;margin-bottom:6px}
                    .filters-bar input,.filters-bar select{width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 12px;outline:none}
                    .filters-actions{display:flex;gap:8px;align-items:end;justify-content:flex-end}
                    /* On lg screens place actions in the right half of the grid */
                    @media (min-width:1024px){.filters-actions{grid-column:4 / 7}}
                    /* Keep table cells on a single line and allow horizontal scroll */
                    .table-nowrap td{white-space:nowrap}
                    /* Widen specific columns that were wrapping */
                    td[data-key="plate"],
                    td[data-key="installed_on"],
                    td[data-key="company_manufacture"]{min-width:160px}
                    td[data-key="imei"], td[data-key="sim_number"]{min-width:180px}
                </style>
                <form method="get" class="filters-bar">
                    <div class="filters-grid">
                    <div>
                        <label class="text-xs text-gray-600">{{ __('messages.common.search') }}</label>
                        <input name="q" value="{{ $q ?? '' }}" placeholder="{{ __('messages.clients.filters_search_ph') }}" class="">
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">{{ __('messages.devices.type') ?? 'Device Type' }}</label>
                        <input name="device_type" value="{{ $filters['device_type'] ?? '' }}" class=""/>
                    </div>
                    <div>
                        <label class="text-xs text-gray-600">SIM</label>
                        <input name="sim_type" value="{{ $filters['sim_type'] ?? '' }}" class=""/>
                    </div>
                    {{-- Removed global Sort/Direction controls; sorting is now per-column in table headers --}}
                    <div class="filters-actions mt-3">
                        <button class="px-4 py-2 rounded-lg text-white font-medium transition-all duration-150"
                                style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                                onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                                onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                            {{ __('messages.clients.filter') }}
                        </button>
                        <a href="{{ route('clients.show', $client) }}" class="px-4 py-2 rounded-lg text-white font-medium transition-all duration-150"
                           style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);"
                           onmouseover="this.style.background='linear-gradient(135deg, #4b5563 0%, #374151 100%)'"
                           onmouseout="this.style.background='linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'">{{ __('messages.common.reset') ?? 'Reset' }}</a>
                    </div>
                    </div>
                </form>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start mb-3">
                    <div class="w-full" id="clientMetaBox">
                        <div id="clientMetaDisplay">
                            <div>{{ __('messages.clients.sector') }}: <strong class="arabic-text" dir="auto">{{ $client->sector }}</strong></div>
                        </div>
                        <div id="clientMetaEdit" style="display:none">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('messages.clients.name') }}</label>
                                    <input type="text" id="editClientName" class="w-full px-3 py-2 border border-gray-300 rounded" value="{{ $client->name }}" />
                                </div>
                                <div>
                                    <label class="text-xs text-gray-600">{{ __('messages.clients.sector') }}</label>
                                    <input type="text" id="editClientSector" class="w-full px-3 py-2 border border-gray-300 rounded" value="{{ $client->sector }}" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="w-full buttons-grid">
                        {{-- New row (create client sheet row) --}}
                        @can('assignments.create')
                            <button onclick="openModal('{{ __('messages.clients.new_row') }}', '{{ route('clients.sheet-rows.create', $client) }}')" 
                                    class="inline-flex items-center justify-center w-full px-6 py-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
                                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"
                                    onmouseover="this.style.background='linear-gradient(135deg, #059669 0%, #047857 100%)'"
                                    onmouseout="this.style.background='linear-gradient(135deg, #10b981 0%, #059669 100%)'">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                {{ __('messages.clients.new_row') }}
                            </button>
                        @endcan
                        
                        {{-- Edit All button --}}
                        @can('assignments.update')
                            <button id="editAllBtn" 
                                    onclick="enableInlineEditAll()"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
                                    style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);"
                                    onmouseover="this.style.background='linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)'"
                                    onmouseout="this.style.background='linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)'"
                                    title="{{ __('messages.clients.edit_all') }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                <span id="editAllBtnLabel">{{ __('messages.clients.edit_all') }}</span>
                            </button>
                        @endcan
                        <a href="{{ route('clients.export', [$client, 'format'=>'xlsx', 'template'=>'advanced']) }}" 
                        class="inline-flex items-center justify-center w-full px-6 py-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                            </svg>

                            {{ __('messages.clients.export_xlsx') }}
                        </a>
                        <a href="{{ route('clients.export', [$client, 'format'=>'csv', 'template'=>'advanced']) }}" 
                        class="inline-flex items-center justify-center w-full px-6 py-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                            </svg>

                            {{ __('messages.clients.export_csv') }} 
                        </a>

                        <a href="{{ route('clients.export', [$client, 'format'=>'pdf', 'template'=>'advanced']) }}" 
                        class="inline-flex items-center justify-center w-full px-6 py-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                            </svg>

                            {{ __('messages.clients.export_pdf') }} 
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm table-nowrap" style="border-collapse: separate; border-spacing: 0; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                        <thead style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                            <tr>
                                @foreach ($headers as $i => $h)
                                    @php
                                        $col = $columns[$i] ?? null;
                                        $isSortable = !is_null($col);
                                        $isActive = $isSortable && ($sort['by'] ?? '') === $col;
                                        $nextDir = $isActive && ($sort['dir'] ?? 'asc') === 'asc' ? 'desc' : 'asc';
                                        $query = array_merge(request()->except(['page']), ['sort' => $col, 'dir' => $nextDir]);
                                    @endphp
                                    @php $requiredCols = ['data_package_type','sim_type','sim_number','imei','plate','installed_on','year_model','company_manufacture','device_type','crm_integration','technician','vehicle_serial_number']; @endphp
                                    <th class="px-3 py-3 text-right align-bottom whitespace-nowrap font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6; border-right: 1px solid #e5e7eb;">
                                        @if($isSortable)
                                            <a href="{{ route('clients.show', [$client] + $query) }}" class="inline-flex items-center gap-1 text-gray-700 hover:text-blue-700">
                                                <span>
                                                    {{ $h }}
                                                    @if($col && in_array($col,$requiredCols))
                                                        <span class="text-red-600">*</span>
                                                    @endif
                                                </span>
                                                @if($isActive)
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                        @if(($sort['dir'] ?? 'asc') === 'asc')
                                                            <path fill-rule="evenodd" d="M14.707 12.293a1 1 0 01-1.414 0L10 9l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                                        @else
                                                            <path fill-rule="evenodd" d="M5.293 7.707a1 1 0 011.414 0L10 11l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        @endif
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-40" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M5 8h10l-5-5-5 5zm0 4h10l-5 5-5-5z" clip-rule="evenodd" />
                                                    </svg>
                                                @endif
                                            </a>
                                        @else
                                            {{ $h }}
                                        @endif
                                    </th>
                                @endforeach
                                @can('assignments.update')
                                <th class="px-3 py-3 text-center align-bottom whitespace-nowrap font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">{{ __('messages.common.edit') }}</th>
                                <th class="px-3 py-3 text-center align-bottom whitespace-nowrap font-semibold text-gray-700" style="border-bottom: 2px solid #f31f1f;">{{ __('messages.common.delete') }}</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                                @foreach ($rows as $index => $r)
                                <tr class="hover:bg-gray-50 transition-colors duration-150" style="border-bottom: 1px solid #e5e7eb;" data-row-id="{{ $clientSheetRows[$index]->id ?? '' }}">
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['no'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="data_package_type">{{ $r['package_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="sim_type">{{ $r['sim_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="sim_number">{{ $r['sim_number'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="imei">{{ $r['imei'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="plate">{{ $r['plate'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="installed_on">{{ $r['installed_on'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="year_model">{{ $r['year_model'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="company_manufacture">{{ $r['company_manufacture'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="device_type">{{ $r['device_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="air">{{ $r['air'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="mechanic">{{ $r['mechanic'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="tracking">{{ $r['tracking'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="system_type">{{ $r['system_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="calibration">{{ $r['calibration'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="color">{{ $r['color'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="crm_integration">{{ $r['crm'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="technician">{{ $r['technician'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="vehicle_serial_number">{{ $r['vehicle_serial_number'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="vehicle_weight">{{ $r['vehicle_weight'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;" data-key="user">{{ $r['user'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;" data-key="notes">{{ $r['notes'] ?? '' }}</td>
                                    @can('assignments.update')
                                        <td class="px-3 py-2 text-center whitespace-nowrap">
                                            @if(isset($clientSheetRows[$index]))
                                                <button onclick="openModal('{{ __('messages.clients.edit_row') }}', '{{ route('clients.sheet-rows.edit', [$client, $clientSheetRows[$index]]) }}')" 
                                                        class="mt-1 px-2 py-1 rounded text-white text-xs font-medium transition-all duration-150 mr-1"
                                                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                                                        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                                                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'"
                                                        title="{{ __('messages.common.edit') }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </td>
                                    @endcan
                                    @can('assignments.delete')
                                        <td class="px-3 py-2 text-center whitespace-nowrap">
                                            @if(isset($clientSheetRows[$index]))
                                                <form method="POST" action="{{ route('clients.sheet-rows.destroy', [$client, $clientSheetRows[$index]]) }}" class="inline mt-3" onsubmit="return confirm('{{ __('messages.clients.confirm_delete_row') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-2 py-1 rounded text-white text-xs font-medium transition-all duration-150"
                                                            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);"
                                                            onmouseover="this.style.background='linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)'"
                                                            onmouseout="this.style.background='linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'"
                                                            title="{{ __('messages.common.delete') }}">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

    @include('clients._client_sheet_row_modal')

    <script>
    // Define Edit All functions in global scope
    window.editAllModifiedCount = 0;
    window.editAllOriginalValues = {};
    
    window.updateEditAllModifiedCount = function() {
        let count = 0;
        const inputs = document.querySelectorAll('#modalContent input, #modalContent textarea, #modalContent select');
        
        inputs.forEach(input => {
            const key = input.name;
            const currentValue = input.value;
            const originalValue = window.editAllOriginalValues[key];
            
            if (currentValue !== originalValue) {
                count++;
            }
        });
        
        window.editAllModifiedCount = count;
        const modifiedCountElement = document.getElementById('modifiedCount');
        if (modifiedCountElement) {
            modifiedCountElement.textContent = count;
        }
    };

    window.saveAllChanges = function() {
        console.log('saveAllChanges called, modifiedCount:', window.editAllModifiedCount);
        
        if (window.editAllModifiedCount === 0) {
            window.showEditAllNotification('{{ __("messages.common.no_changes") }}', 'warning');
            return;
        }
        
        const form = document.getElementById('editAllForm');
        const formData = new FormData(form);
        const saveButton = document.querySelector('button[onclick="saveAllChanges()"]');
        const originalText = saveButton.innerHTML;
        
        // Show loading state
        saveButton.disabled = true;
        saveButton.innerHTML = '<svg class="w-4 h-4 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>{{ __("messages.common.saving") }}...';
        
        fetch('{{ route("clients.sheet-rows.update-all", $client) }}', {
            method: 'POST',
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
                window.showEditAllNotification(data.message, 'success');
                
                // Close modal immediately
                const modal = document.getElementById('clientSheetRowModal');
                if (modal) {
                    modal.classList.remove('show');
                    console.log('Modal closed after successful save');
                }
                
                // Reset modified count
                window.editAllModifiedCount = 0;
                const modifiedCountElement = document.getElementById('modifiedCount');
                if (modifiedCountElement) {
                    modifiedCountElement.textContent = '0';
                }
                
                // Update original values
                const inputs = document.querySelectorAll('#modalContent input, #modalContent textarea, #modalContent select');
                inputs.forEach(input => {
                    const key = input.name;
                    window.editAllOriginalValues[key] = input.value;
                });
                
                // Show loading state and reload page to show updated data
                setTimeout(() => {
                    console.log('Reloading page to show updated data');
                    // Show loading state in modal content
                    const modalContent = document.getElementById('modalContent');
                    if (modalContent) {
                        modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-green-600"></div><span class="ml-3 text-green-600">Updating data...</span></div>';
                    }
                    window.location.reload();
                }, 1000);
            } else {
                window.showEditAllNotification(data.message || '{{ __("messages.common.error_occurred") }}', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            window.showEditAllNotification('{{ __("messages.common.error_occurred") }}', 'error');
        })
        .finally(() => {
            // Reset button state
            saveButton.disabled = false;
            saveButton.innerHTML = originalText;
        });
    };

    window.resetAllChanges = function() {
        console.log('resetAllChanges called');
        
        if (confirm('{{ __("messages.common.confirm_reset") }}')) {
            console.log('Reset confirmed, resetting values...');
            const inputs = document.querySelectorAll('#modalContent input, #modalContent textarea, #modalContent select');
            inputs.forEach(input => {
                const key = input.name;
                const originalValue = window.editAllOriginalValues[key];
                input.value = originalValue;
            });
            
            window.editAllModifiedCount = 0;
            const modifiedCountElement = document.getElementById('modifiedCount');
            if (modifiedCountElement) {
                modifiedCountElement.textContent = '0';
            }
            window.showEditAllNotification('{{ __("messages.common.changes_reset") }}', 'success');
        }
    };

    window.showEditAllNotification = function(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg text-white ${
            type === 'success' ? 'bg-green-500' : 
            type === 'error' ? 'bg-red-500' : 
            type === 'warning' ? 'bg-yellow-500' :
            'bg-blue-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    };

    window.closeEditAllModal = function() {
        console.log('closeEditAllModal called');
        const modal = document.getElementById('clientSheetRowModal');
        if (modal) {
            modal.classList.remove('show');
            console.log('Modal closed');
        } else {
            console.error('Modal not found for closing');
        }
    };

    // Inline edit-all (no modal)
    function enableInlineEditAll(){
        const rows = document.querySelectorAll('tbody tr[data-row-id]');
        let idx = 0;
        rows.forEach(tr => {
            const rowId = tr.getAttribute('data-row-id');
            if (!rowId) return;
            tr.setAttribute('data-idx', String(idx));
            const mandatory = ['data_package_type','sim_type','sim_number','imei','plate','installed_on','year_model','company_manufacture','device_type','crm_integration','technician','vehicle_serial_number'];
            tr.querySelectorAll('[data-key]').forEach(td => {
                const key = td.getAttribute('data-key');
                const value = td.textContent.trim();
                td.innerHTML = '';
                let input;
                // Yes/No dropdowns
                if (['air','mechanic','tracking','calibration'].includes(key)) {
                    // boolean select 0/1 to satisfy validation
                    input = document.createElement('select');
                    input.name = `rows[${idx}][${key}]`;
                    input.className = 'w-full px-2 py-1 border border-gray-300 rounded inline-input sm';
                    const optNo = document.createElement('option'); optNo.value = '0'; optNo.textContent = '{{ __('messages.common.no') }}';
                    const optYes = document.createElement('option'); optYes.value = '1'; optYes.textContent = '{{ __('messages.common.yes') }}';
                    input.appendChild(optNo); input.appendChild(optYes);
                    const normalized = value.toLowerCase();
                    const isYes = ['yes','1','true','{{ __('messages.common.yes') }}'].some(v => normalized.indexOf(v.toLowerCase()) !== -1);
                    input.value = isYes ? '1' : '0';
                // Carrier dropdown
                } else if (key === 'sim_type') {
                    input = document.createElement('select');
                    input.name = `rows[${idx}][${key}]`;
                    input.className = 'w-full px-2 py-1 border border-gray-300 rounded inline-input sm';
                    ['LEBARA','STC','Mobily','Zain'].forEach(opt => {
                        const o = document.createElement('option'); o.value = opt; o.textContent = opt; input.appendChild(o);
                    });
                    // try match existing value ignoring case
                    const current = value.toLowerCase();
                    let matched = Array.from(input.options).find(o => o.value.toLowerCase() === current);
                    input.value = matched ? matched.value : 'LEBARA';
                // Year dropdown
                } else if (key === 'year_model') {
                    input = document.createElement('select');
                    input.name = `rows[${idx}][${key}]`;
                    input.className = 'w-full px-2 py-1 border border-gray-300 rounded inline-input sm';
                    const thisYear = new Date().getFullYear();
                    for (let y = thisYear; y >= 1990; y--) {
                        const o = document.createElement('option'); o.value = String(y); o.textContent = String(y); input.appendChild(o);
                    }
                    if (value) input.value = value;
                // CRM field normalize (free text kept as input)
                } else {
                    input = document.createElement('input');
                    input.type = key === 'installed_on' ? 'date' : 'text';
                    input.name = `rows[${idx}][${key}]`;
                    input.value = value;
                    input.className = 'w-full px-2 py-1 border border-gray-300 rounded inline-input';
                }
                // Mark mandatory fields as required
                if (mandatory.includes(key)) {
                    input.required = true;
                    input.addEventListener('invalid', function(){
                        this.classList.add('border-red-300');
                    });
                    input.addEventListener('input', function(){
                        if (this.value) this.classList.remove('border-red-300');
                    });
                }
                td.appendChild(input);
            });
            // hidden id input for this row (aligns with idx)
            const hiddenId = document.createElement('input');
            hiddenId.type = 'hidden';
            hiddenId.name = `rows[${idx}][id]`;
            hiddenId.value = rowId;
            tr.appendChild(hiddenId);
            idx++;
        });

        // Switch header button to Save All
        const editBtnLabel = document.getElementById('editAllBtnLabel');
        if (editBtnLabel) editBtnLabel.textContent = '{{ __('messages.common.save_all') }}';
        const editBtn = document.getElementById('editAllBtn');
        if (editBtn) {
            editBtn.onclick = saveInlineAll;
            editBtn.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';
        }
        // Toggle client meta to edit mode
        const metaDisplay = document.getElementById('clientMetaDisplay');
        const metaEdit = document.getElementById('clientMetaEdit');
        if (metaDisplay && metaEdit){ metaDisplay.style.display = 'none'; metaEdit.style.display = 'block'; }
    }

    async function saveInlineAll(){
        const rows = document.querySelectorAll('tbody tr[data-idx]');
        const mandatory = ['data_package_type','sim_type','sim_number','imei','plate','installed_on','year_model','company_manufacture','device_type','crm_integration','technician','vehicle_serial_number'];
        // Client-side required validation with visible highlight
        let firstInvalid = null;
        rows.forEach(tr => {
            tr.querySelectorAll('input,select').forEach(inp => {
                const m = inp.name.match(/\[(\w+)\]$/);
                const field = m ? m[1] : inp.name;
                const isEmpty = !String(inp.value ?? '').trim();
                if (mandatory.includes(field) && isEmpty){
                    inp.classList.add('border-red-300');
                    // Also force inline styles so Bootstrap/Tailwind borders don't mask the color
                    try { inp.style.borderColor = '#ef4444'; inp.style.boxShadow = '0 0 0 2px rgba(239,68,68,.35)'; } catch(_) {}
                    inp.setAttribute('aria-invalid','true');
                    if (!firstInvalid) firstInvalid = inp;
                } else {
                    inp.classList.remove('border-red-300');
                    inp.removeAttribute('aria-invalid');
                    try { inp.style.boxShadow = ''; } catch(_) {}
                }
            });
        });
        if (firstInvalid){
            firstInvalid.scrollIntoView({behavior:'smooth', block:'center'});
            window.showEditAllNotification('{{ __('messages.common.error_occurred') }}: {{ __('messages.common.required') ?? 'Required fields are missing' }}','error');
            return;
        }
        const formData = new FormData();
        // Include client meta edits
        const n = document.getElementById('editClientName');
        const s = document.getElementById('editClientSector');
        if (n) formData.append('client[name]', n.value);
        if (s) formData.append('client[sector]', s.value);
        rows.forEach(tr => {
            const idx = tr.getAttribute('data-idx');
            const rowId = tr.getAttribute('data-row-id');
            if (rowId) formData.append(`rows[${idx}][id]`, rowId);
            tr.querySelectorAll('input,select').forEach(inp => {
                const m = inp.name.match(/\[(\w+)\]$/);
                const field = m ? m[1] : inp.name;
                formData.append(`rows[${idx}][${field}]`, inp.value);
            });
        });
        if ([...formData.keys()].length === 0) return;
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const btn = document.getElementById('editAllBtn');
        if (btn) btn.disabled = true;
        // CSRF: required in production (419 otherwise). Send token header and include credentials for cookies.
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        formData.append('_token', token);
        let res = await fetch('{{ route('clients.sheet-rows.update-all', $client) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'include'
        });
        // If a proxy/cookie quirk causes a 419, try to refresh the session and retry once
        if (res.status === 419) {
            try {
                await fetch('/', {credentials:'include'}); // refresh session cookie
            } catch (_) {}
            res = await fetch('{{ route('clients.sheet-rows.update-all', $client) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'include'
            });
        }
        if (btn) btn.disabled = false;
        if (res.ok){
            window.showEditAllNotification('{{ __('messages.common.save_all') }} {{ __('messages.common.success') ?? 'done' }}','success');
            setTimeout(() => window.location.reload(), 600);
        } else {
            window.showEditAllNotification('{{ __('messages.common.error_occurred') }}','error');
        }
    }

    window.enableInlineEditAll = enableInlineEditAll;
    window.saveInlineAll = saveInlineAll;
    </script>

</x-app-layout>
