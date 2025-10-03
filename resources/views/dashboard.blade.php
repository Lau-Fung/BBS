<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.auth.dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="p-6 rounded-lg shadow" style="background-color: #2563eb !important;">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2" style="color: white !important; border-color: #93c5fd !important;">{{ __('messages.dashboard.clients_per_sector') }}</h3>
                    <ul class="space-y-1 text-sm" style="color: white !important;">
                        @foreach($clientsPerSector as $sector => $count)
                            <li class="flex justify-between"><span>{{ $sector }}</span><span class="font-semibold">{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="p-6 rounded-lg shadow" style="background-color: #16a34a !important;">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2" style="color: white !important; border-color: #86efac !important;">{{ __('messages.dashboard.devices_per_model') }}</h3>
                    <ul class="space-y-1 text-sm" style="color: white !important;">
                        @foreach($devicesPerModel as $model => $count)
                            <li class="flex justify-between"><span>{{ $model }}</span><span class="font-semibold">{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="p-6 rounded-lg shadow" style="background-color: #ea580c !important;">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2" style="color: white !important; border-color: #fdba74 !important;">{{ __('messages.dashboard.sims_per_package') }}</h3>
                    <ul class="space-y-1 text-sm" style="color: white !important;">
                        @foreach($simsPerPackage as $pkg => $count)
                            <li class="flex justify-between"><span>{{ $pkg }}</span><span class="font-semibold">{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>

                <div class="p-6 rounded-lg shadow" style="background-color: #9333ea !important;">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2" style="color: white !important; border-color: #c4b5fd !important;">{{ __('messages.dashboard.sims_per_provider') }}</h3>
                    <ul class="space-y-1 text-sm" style="color: white !important;">
                        @foreach($simsPerProvider as $provider => $count)
                            <li class="flex justify-between"><span>{{ $provider }}</span><span class="font-semibold">{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>
                <div class="p-6 rounded-lg shadow" style="background-color: #dc2626 !important;">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2" style="color: white !important; border-color: #fca5a5 !important;">{{ __('messages.dashboard.expired_warranty') }}</h3>
                    <div class="text-3xl font-bold" style="color: white !important;">{{ $expiredWarrantyCount }}</div>
                </div>
                <div class="p-6 rounded-lg shadow" style="background-color: #0d9488 !important;">
                    <h3 class="text-lg font-bold mb-4 border-b pb-2" style="color: white !important; border-color: #5eead4 !important;">{{ __('messages.dashboard.technician_totals') }}</h3>
                    <ul class="space-y-1 text-sm max-h-64 overflow-auto" style="color: white !important;">
                        @foreach($technicianTotals as $tech => $count)
                            <li class="flex justify-between"><span>{{ $tech }}</span><span class="font-semibold">{{ $count }}</span></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
