<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('messages.clients.title') }}</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        <style>
            .client-actions { display: grid; grid-template-columns: 1fr; gap: 12px; }
            @media (min-width: 640px) { .client-actions { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
            @media (min-width: 1024px) { .client-actions { grid-template-columns: repeat(3, minmax(0, 1fr)); } }
        </style>
        <form method="get" class="mb-4 flex items-center gap-2">
            <input name="q" value="{{ $q }}" placeholder="{{ __('messages.clients.filters_search_ph') }}"
                   class="border border-gray-300 rounded-lg px-3 py-2 w-72 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <button class="px-4 py-2 rounded-lg text-white font-medium transition-all duration-150"
                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                    onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                    onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                {{ __('messages.clients.filter') }}
            </button>
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

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($clients as $c)
                <div class="rounded border bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <a href="{{ route('clients.show',$c) }}"
                           class="text-lg font-semibold text-indigo-700 hover:underline">
                            {{ $c->name }}
                        </a>
                        <span class="text-sm px-2 py-1 rounded bg-gray-100">
                            {{ ucfirst($c->subscription_type) }}
                        </span>
                    </div>

                    <div class="mt-2 text-sm text-gray-600">
                        <div>{{ __('messages.clients.sector') }}: <strong>{{ $c->sector ?? '—' }}</strong></div>
                        <div>{{ __('messages.clients.total_records') }}: <strong>{{ $c->vehicles_count }}</strong></div>
                        <div>{{ __('messages.clients.devices_active') }}: <strong>{{ $c->total_devices }}</strong></div>
                    </div>

                    @if($c->models->isNotEmpty())
                        <div class="mt-3">
                            <div class="text-xs font-semibold text-gray-500 mb-1">{{ __('messages.clients.devices_by_model') }}</div>
                            <table class="text-sm border w-full">
                                <tbody class="divide-y">
                                @foreach($c->models as $m)
                                    <tr>
                                        <td class="px-2 py-1">{{ $m['model'] }}</td>
                                        <td class="px-2 py-1 text-right">{{ $m['count'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-gray-600">{{ __('messages.clients.no_client_found') }}</div>
            @endforelse
        </div>
    </div>
</x-app-layout>
