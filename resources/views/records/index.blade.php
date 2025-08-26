<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('Records') }}</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 sm:rounded-lg p-6">
            @if(session('status'))
                <div class="mb-4 text-green-700 bg-green-100 border rounded px-3 py-2">{{ session('status') }}</div>
            @endif

            <div class="mb-4">
                @can('records.create')
                    <a href="{{ route('records.create') }}"
                       class="inline-flex px-4 py-2 bg-indigo-600 text-white rounded">{{ __('New Record') }}</a>
                @endcan
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="p-2 text-left">#</th>
                            <th class="p-2 text-left">{{ __('Title') }}</th>
                            <th class="p-2 text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                            <tr class="border-b">
                                <td class="p-2">{{ $rec->id }}</td>
                                <td class="p-2">{{ $rec->title }}</td>
                                <td class="p-2 text-right">
                                    @can('records.update')
                                        <a href="{{ route('records.edit',$rec) }}"
                                           class="inline-flex px-3 py-1.5 border rounded-md text-xs">{{ __('Edit') }}</a>
                                    @endcan
                                    @can('records.delete')
                                        <form method="POST" action="{{ route('records.destroy',$rec) }}" class="inline">
                                            @csrf @method('DELETE')
                                            <x-danger-button onclick="return confirm('{{ __('Delete?') }}')">
                                                {{ __('Delete') }}
                                            </x-danger-button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td class="p-4" colspan="3">{{ __('No records yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $records->links() }}</div>
        </div>
    </div>
</x-app-layout>
