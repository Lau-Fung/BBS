@once
    <style>
    /* borders, spacing, simple colors used in this view only */
    .border-gray-200{border-color:#e5e7eb}
    .border-red-300{border-color:#fca5a5}
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
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start mb-3">
                    <div class="w-full">
                        <div>{{ __('messages.clients.sector') }}: <strong>{{ $client->sector }}</strong></div>
                        <div>{{ __('messages.clients.subscription') }}: <strong>{{ $client->subscription_type ?? 'yearly' }}</strong></div>
                    </div>
                    <div class="w-full buttons-grid">
                        {{-- New row (create client sheet row) --}}
                        @hasanyrole('Super Admin|Admin')
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
                            
                            {{-- Edit All button --}}
                            <button id="editAllBtn" 
                                    onclick="openEditAllModal()"
                                    class="inline-flex items-center justify-center w-full px-6 py-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
                                    style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);"
                                    onmouseover="this.style.background='linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%)'"
                                    onmouseout="this.style.background='linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)'"
                                    title="{{ __('messages.clients.edit_all') }}">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                                {{ __('messages.clients.edit_all') }}
                            </button>
                        @endhasanyrole
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
                    <table class="min-w-full text-sm" style="border-collapse: separate; border-spacing: 0; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                        <thead style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                            <tr>
                                @foreach ($headers as $h)
                                    <th class="px-3 py-3 text-right align-bottom whitespace-nowrap font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6; border-right: 1px solid #e5e7eb;">{{ $h }}</th>
                                @endforeach
                                @hasanyrole('Super Admin|Admin')
                                    <th class="px-3 py-3 text-center align-bottom whitespace-nowrap font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">{{ __('messages.common.actions') }}</th>
                                @endhasanyrole
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $index => $r)
                                <tr class="hover:bg-gray-50 transition-colors duration-150" style="border-bottom: 1px solid #e5e7eb;">
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['no'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['package_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['sim_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['sim_number'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['imei'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['plate'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['installed_on'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['year_model'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['company_manufacture'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['device_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['air'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['sensor_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['mechanic'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['tracking'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['system_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['calibration'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['color'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['crm'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['subscription_type'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['technician'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['vehicle_serial'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['vehicle_serial_number'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['vehicle_weight'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-900" style="border-right: 1px solid #e5e7eb;">{{ $r['user'] ?? '' }}</td>
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600" style="border-right: 1px solid #e5e7eb;">{{ $r['notes'] ?? '' }}</td>
                                    @hasanyrole('Super Admin|Admin')
                                        <td class="px-3 py-2 text-center whitespace-nowrap">
                                            @if(isset($clientSheetRows[$index]))
                                                <button onclick="openModal('{{ __('messages.clients.edit_row') }}', '{{ route('clients.sheet-rows.edit', [$client, $clientSheetRows[$index]]) }}')" 
                                                        class="px-2 py-1 rounded text-white text-xs font-medium transition-all duration-150"
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
                                    @endhasanyrole
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

    function openEditAllModal() {
        console.log('openEditAllModal called');
        
        const modal = document.getElementById('clientSheetRowModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        console.log('Modal elements:', { modal, modalTitle, modalContent });
        
        if (!modal) {
            console.error('Modal not found!');
            return;
        }
        
        modalTitle.textContent = '{{ __("messages.clients.edit_all") }}';
        modalContent.innerHTML = '<div class="flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
        
        modal.classList.add('show');
        console.log('Modal shown');
        
        // Load edit all form content
        const url = '{{ route("clients.sheet-rows.edit-all", $client) }}';
        console.log('Fetching URL:', url);
        
        fetch(url)
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                console.log('HTML received, length:', html.length);
                modalContent.innerHTML = html;
                
                // Initialize after content is loaded
                setTimeout(() => {
                    const inputs = document.querySelectorAll('#modalContent input, #modalContent textarea, #modalContent select');
                    inputs.forEach(input => {
                        const key = input.name;
                        window.editAllOriginalValues[key] = input.value;
                        
                        input.addEventListener('change', function() {
                            window.updateEditAllModifiedCount();
                        });
                    });
                    console.log('Edit All form initialized');
                }, 100);
            })
            .catch(error => {
                console.error('Error loading edit all form:', error);
                modalContent.innerHTML = '<div class="text-red-600 text-center py-8">Error loading form: ' + error.message + '</div>';
            });
    }
    
    // Make function globally accessible
    window.openEditAllModal = openEditAllModal;
    console.log('openEditAllModal function defined and attached to window');
    </script>

</x-app-layout>
