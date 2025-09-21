<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:justify-between">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ __('messages.users.title') }}</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">{{ __('messages.users.manage_roles') }}</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                    <a href="{{ route('admin.users.create') }}" 
                        class="inline-flex items-center px-6 py-3 mb-2 bg-blue-600 hover:bg-blue-700 text-gray-700 font-medium rounded-lg transition-colors duration-200 shadow-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        {{ __('messages.users.new_user') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="overflow-x-auto">
                        <table class="min-w-full w-100 text-sm">
                            <thead class="ltr:text-left rtl:text-right">
                                <tr class="border-b">
                                    <th class="text-left p-2">#</th>
                                    <th class="text-left p-2">{{ __('messages.table.name') }}</th>
                                    <th class="text-left p-2">{{ __('messages.table.email') }}</th>
                                    <th class="text-left p-2">{{ __('messages.table.roles') }}</th>
                                    <th class="text-right p-2">{{ __('messages.table.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="ltr:text-left rtl:text-right">
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
                                            {{ __('messages.common.edit') }}
                                        </a>
                                        @if(auth()->id() !== $u->id)
                                        <form method="POST"
                                            action="{{ route('admin.users.destroy', $u) }}"
                                            class="d-inline"
                                            onsubmit="return confirm('Delete this user? This can be undone only if soft deletes are enabled.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
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
    </div>
</x-app-layout>
