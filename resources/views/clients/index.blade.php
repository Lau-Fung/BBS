<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('messages.clients.title') }}</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        <style>
            .client-actions { display: grid; grid-template-columns: 1fr; gap: 12px; }
            @media (min-width: 640px) { .client-actions { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (min-width: 1024px) { .client-actions { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
            /* Filters card */
            .filters-bar{background:#fff;border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 6px 16px rgba(0,0,0,.05);padding:14px 16px;margin-bottom:16px}
            .filters-grid{display:grid;grid-template-columns:1fr;gap:12px}
            @media (min-width:640px){.filters-grid{grid-template-columns:repeat(1,minmax(0,1fr))}}
            @media (min-width:1024px){.filters-grid{grid-template-columns:repeat(3,minmax(0,1fr))}}
            .filters-bar label{display:block;font-size:12px;color:#6b7280;margin-bottom:6px}
            .filters-bar input,.filters-bar select{width:100%;border:1px solid #d1d5db;border-radius:10px;padding:8px 12px;outline:none}
            .filters-actions{display:flex;gap:8px;align-items:end;justify-content:flex-end}
            @media (min-width:1024px){.filters-actions{grid-column:3 / 6}}
        </style>
        <form method="get" class="filters-bar">
            <div class="filters-grid">
            <div>
                <label class="text-xs text-gray-600">{{ __('messages.common.search') }}</label>
                <input name="q" value="{{ $q }}" placeholder="{{ __('messages.clients.filters_search_ph') }}"
                       class="">
            </div>
            {{-- Removed sector and device_type filters to keep a single global search --}}
            {{-- Removed Sort/Direction selects; sorting happens on table headers below --}}
            </div>
            <div class="filters-actions mt-3">
                <button class="px-4 py-2 rounded-lg text-white font-medium transition-all duration-150"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                    {{ __('messages.clients.filter') }}
                </button>
                <a href="{{ route('clients.index') }}" class="px-4 py-2 rounded-lg text-white font-medium transition-all duration-150"
                style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);"
                onmouseover="this.style.background='linear-gradient(135deg, #4b5563 0%, #374151 100%)'"
                onmouseout="this.style.background='linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'">{{ __('messages.common.reset') ?? 'Reset' }}</a>
            </div>
        </form>

        <div class="client-actions">            
            <a href="{{ route('imports.assignments.form') }}" 
            class="inline-flex items-center justify-center w-full px-6 py-3 mb-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
            style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);"
            onmouseover="this.style.background='linear-gradient(135deg, #059669 0%, #047857 100%)'"
            onmouseout="this.style.background='linear-gradient(135deg, #10b981 0%, #059669 100%)'">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                </svg>
                {{ __('messages.clients.import_from_excel') }}
            </a>
            
            <a  href="{{ route('clients.export.xlsx', ['q' => $q]) }}"
            class="inline-flex items-center justify-center w-full px-6 py-3 mb-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
            style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
            onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
            onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                </svg>
                {{ __('messages.clients.export_xlsx') }}
            </a>

            <a href="{{ route('clients.export.pdf', ['q' => $q]) }}" 
            class="inline-flex items-center justify-center w-full px-6 py-3 mb-3 font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl text-white"
            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);"
            onmouseover="this.style.background='linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)'"
            onmouseout="this.style.background='linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                </svg>
                {{ __('messages.clients.export_pdf') }}
            </a>

            {{-- <a href="{{ route('assignments.index') }}" 
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                {{ __('messages.nav.assignments') }}
            </a>
            
            <a href="{{ route('vehicles.index') }}" 
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                {{ __('messages.nav.vehicles') }}
            </a>

            <a href="{{ route('devices.index') }}" 
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                {{ __('messages.nav.devices') }}
            </a>

            <a href="{{ route('sims.index') }}" 
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                {{ __('messages.nav.sims') }}
            </a>

            <a href="{{ route('sensors.index') }}" 
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                {{ __('messages.nav.sensors') }}
            </a>
            
            @can('admin.reference.manage')
                <a href="{{ route('carriers.index') }}" 
                class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                    {{ __('messages.nav.carriers') }}
                </a>
            @endcan --}}

        </div>

        @php
            $headers = [
                __('messages.clients.company') ?? 'Company',
                __('messages.clients.sector'),
                __('messages.clients.total_records'),
                __('messages.clients.devices_active'),
            ];
            $columns = ['name','sector','records', null]; // last column not sortable
            $currentBy = $sort['by'] ?? 'name';
            $currentDir = $sort['dir'] ?? 'asc';
        @endphp

        <div class="overflow-x-auto mt-4">
            <table class="w-100 min-w-full text-sm" style="border-collapse: separate; border-spacing: 0; border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden;">
                <thead style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                    <tr>
                        @foreach($headers as $i => $h)
                            @php
                                $col = $columns[$i] ?? null;
                                $isSortable = !is_null($col);
                                $isActive = $isSortable && $currentBy === $col;
                                $nextDir = $isActive && $currentDir === 'asc' ? 'desc' : 'asc';
                                $query = array_merge(request()->except(['page']), ['sort'=>$col, 'dir'=>$nextDir]);
                            @endphp
                            <th class="px-4 py-3 text-right font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6; border-right: 1px solid #e5e7eb;">
                                @if($isSortable)
                                    <a href="{{ route('clients.index', $query) }}" class="inline-flex items-center gap-1 text-gray-700 hover:text-blue-700">
                                        <span>{{ $h }}</span>
                                        @if($isActive)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                @if($currentDir === 'asc')
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
                        <th class="px-4 py-3 text-center font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">{{ __('messages.common.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clients as $c)
                        <tr class="hover:bg-gray-50 transition-colors duration-150" style="border-bottom: 1px solid #e5e7eb;">
                            <td class="px-4 py-2 text-indigo-700 font-semibold" style="border-right: 1px solid #e5e7eb;"><a href="{{ route('clients.show',$c) }}" class="hover:underline">{{ $c->name }}</a></td>
                            <td class="px-4 py-2 text-gray-700" style="border-right: 1px solid #e5e7eb;">{{ $c->sector ?? '—' }}</td>
                            <td class="px-4 py-2 text-gray-700" style="border-right: 1px solid #e5e7eb;">{{ $c->vehicles_count }}</td>
                            <td class="px-4 py-2 text-gray-700" style="border-right: 1px solid #e5e7eb;">{{ $c->total_devices }}</td>
                            <td class="px-4 py-2 text-center d-flex d-flex justify-content-between" style="border-right: 1px solid #e5e7eb;">
                                <a href="{{ route('clients.show',$c) }}" class="text-indigo-600 hover:text-indigo-800 font-medium mr-3">{{ __('messages.common.edit') }}</a>
                                @can('clients.delete')
                                    <form method="POST" action="{{ route('clients.destroy', $c) }}" class="inline" onsubmit="return confirm('{{ __('messages.clients.confirm_delete') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">
                                            {{ __('messages.common.delete') }}
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td class="px-4 py-3 text-gray-600" colspan="{{ auth()->user()->can('clients.delete') ? '5' : '4' }}">{{ __('messages.clients.no_client_found') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
