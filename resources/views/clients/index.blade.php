<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('messages.clients.title') }}</h2>
    </x-slot>

    <div class="max-w-7xl mx-auto p-6">
        <form method="get" class="mb-4 flex items-center gap-2">
            <input name="q" value="{{ $q }}" placeholder="{{ __('messages.clients.filters_search_ph') }}"
                   class="border rounded px-3 py-2 w-72">
            <button class="px-3 py-2 rounded bg-gray-700">{{ __('messages.clients.filter') }}</button>
        </form>

        <div class="flex flex-row gap-3 justify-center items-center">
            <a href="{{ route('imports.assignments.form') }}" 
            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                        d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                </svg>
                {{ __('messages.assignments.import_from_excel') }}
            </a>

            <a href="{{ route('assignments.index') }}" 
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
            @endcan

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
                        <div>{{ __('messages.clients.vehicles') }}: <strong>{{ $c->vehicles_count }}</strong></div>
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
