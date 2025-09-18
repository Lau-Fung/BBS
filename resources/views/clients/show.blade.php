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
    </style>
@endonce
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            {{ $client->name }} — {{ __('messages.clients.details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <div>{{ __('messages.clients.sector') }}: <strong>{{ $client->sector }}</strong></div>
                        <div>{{ __('messages.clients.subscription') }}: <strong>{{ $client->subscription_type ?? 'yearly' }}</strong></div>
                    </div>
                    <div class="flex flex-row gap-3 justify-center items-center">
                        <a href="{{ route('clients.export', [$client, 'format'=>'xlsx', 'template'=>'advanced']) }}" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                            </svg>

                            {{ __('messages.assignments.export_xlsx') }}
                        </a>
                        <a href="{{ route('clients.export', [$client, 'format'=>'csv', 'template'=>'advanced']) }}" 
                        class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 mb-3 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2M7 14l5-5m0 0l5 5m-5-5v12" />
                            </svg>

                            {{ __('messages.assignments.export_csv') }} 
                        </a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach ($headers as $h)
                                    <th class="border border-gray-200 px-2 py-1 text-right align-bottom whitespace-nowrap">{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="odd:bg-white even:bg-gray-50">
                                    @foreach (\App\Support\Layouts\AdvancedLayout::ORDER as $k)
                                        <td class="border border-gray-200 px-2 py-1 whitespace-nowrap">{{ $r[$k] ?? '' }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        </div>

</x-app-layout>
