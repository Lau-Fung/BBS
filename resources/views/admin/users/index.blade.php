<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Users') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full w-100 text-sm">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-2">#</th>
                                <th class="text-left p-2">{{ __('Name') }}</th>
                                <th class="text-left p-2">{{ __('Email') }}</th>
                                <th class="text-left p-2">{{ __('Roles') }}</th>
                                <th class="text-right p-2">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $u)
                            <tr class="border-b">
                                <td class="p-2">{{ $u->id }}</td>
                                <td class="p-2">{{ $u->name }}</td>
                                <td class="p-2">{{ $u->email }}</td>
                                <td class="p-2">{{ $u->getRoleNames()->implode(', ') }}</td>
                                <td class="p-2 text-right">
                                    <a href="{{ route('admin.users.edit',$u) }}"
                                       class="inline-flex items-center px-3 py-1.5 bg-white dark:bg-gray-700
                                              border border-gray-300 dark:border-gray-600 rounded-md text-xs">
                                        {{ __('Edit') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">{{ $users->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
