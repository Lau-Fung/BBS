@php
    // codes we expect: 'required' | 'format' | 'dup-file' | 'dup-db'
    $cellClass = function($err) {
        return match($err) {
            'required','format','dup-db' => 'bg-red-50 text-red-800 border-red-300',
            'dup-file'                   => 'bg-amber-50 text-amber-800 border-amber-300',
            default                     => '',
        };
    };
@endphp

{{-- Fallback styles so this works without Tailwind --}}
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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Preview Import
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-12xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="mb-4 text-sm text-gray-700 flex gap-6">
                    <div>Total rows: <strong>{{ $summary['total'] ?? 0 }}</strong></div>
                    <div>Valid rows: <strong>{{ $summary['valid'] ?? 0 }}</strong></div>
                    <div>Invalid rows: <strong>{{ $summary['invalid'] ?? 0 }}</strong></div>
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
                    <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 p-3 text-amber-800 scroll">
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
                                    @php $key = $mappedKeys[$i] ?? null; @endphp
                                    <th class="border border-gray-200 px-2 py-1 text-right align-bottom whitespace-nowrap">
                                        <div>
                                            {{ $h }}
                                            @if($key && !empty($requiredMap[$key]))
                                                <span class="text-red-600 font-bold">*</span>
                                            @endif
                                        </div>
                                        @if($key)
                                            <div class="text-[11px] text-gray-500 rtl:italic ltr:italic">{{ $key }}</div>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(($rows ?? []) as $row)
                                @php $ri = $loop->index; @endphp
                                <tr class="odd:bg-white even:bg-gray-50">
                                    @for($cj = 0; $cj < count($mappedKeys ?? []); $cj++)
                                        @php
                                            $key  = $mappedKeys[$cj] ?? null;                 // normalized key for this column
                                            $cell = $key ? ($row[$key] ?? null) : null;       // cell value
                                            $err  = $key ? ($cellErrors[$ri][$key] ?? null) : null; // 'required'|'format'|'dup-file'|'dup-db'
                                        @endphp
                                        <td class="border border-gray-200 px-2 py-1 whitespace-nowrap {{ $cellClass($err) }}">
                                            {{ is_scalar($cell) ? $cell : '' }}
                                        </td>
                                    @endfor
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 text-sm text-gray-600 space-x-3 rtl:space-x-reverse">
                    <span class="inline-block px-2 py-1 border rounded bg-red-50 text-red-800 border-red-300">
                        Error / Required / Already exists
                    </span>
                    <span class="inline-block px-2 py-1 border rounded bg-amber-50 text-amber-800 border-amber-300">
                        Duplicate in this file
                    </span>
                    <span class="inline-block px-2 py-1 border rounded">No issues</span>
                </div>

                <div class="mt-6 flex items-center gap-3">
                    <a href="{{ route('imports.assignments.form') }}" class="text-gray-600 hover:underline">
                        Back to upload
                    </a>

                    @if(($canConfirm ?? false) === true)
                        <form method="post" action="{{ route('imports.assignments.confirm') }}">
                            @csrf
                            <x-primary-button>Confirm import</x-primary-button>
                        </form>
                    @else
                        <button disabled
                                class="inline-flex items-center px-4 py-2 rounded bg-gray-400 cursor-not-allowed"
                                title="Fix the highlighted errors to enable import">
                            Confirm import
                        </button>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
