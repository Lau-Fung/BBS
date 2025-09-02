<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Preview Import
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-4 text-sm text-gray-700">
                    <div>Valid rows: <strong>{{ $summary['valid'] ?? 0 }}</strong></div>
                    <div>Total rows: <strong>{{ $summary['total'] ?? 0 }}</strong></div>
                </div>

                @if(!empty($fatal ?? []))
                    <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-3 text-red-700">
                        <ul class="list-disc ps-5">
                            @foreach($fatal as $msg)
                                <li>{{ $msg }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(!empty($issues ?? []))
                    <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 p-3 text-amber-800">
                        <div class="font-semibold mb-1">Issues detected:</div>
                        <ul class="list-disc ps-5 max-h-64 overflow-auto">
                            @foreach($issues as $issue)
                                @foreach(($issue['messages'] ?? []) as $m)
                                    <li>Row {{ $issue['row'] }}: {{ $m }}</li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full border border-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                @foreach(($headers ?? []) as $i => $h)
                                    <th class="border border-gray-200 px-2 py-1 text-right">
                                        <div>{{ $h }}</div>
                                        @if(!empty($mappedKeys[$i]))
                                            <div class="text-[11px] text-gray-500 ltr:italic rtl:italic">
                                                {{ $mappedKeys[$i] }}
                                            </div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($rows ?? []) as $r)
                                <tr>
                                    @foreach($r as $cell)
                                        <td class="border border-gray-200 px-2 py-1">
                                            {{ is_scalar($cell) ? $cell : '' }}
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('imports.assignments.form') }}" class="text-gray-600 hover:underline">Back to upload</a>

                    @if(($canConfirm ?? false) === true)
                        <form method="post" action="{{ route('imports.assignments.confirm') }}">
                            @csrf
                            <x-primary-button>Confirm import</x-primary-button>
                        </form>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

