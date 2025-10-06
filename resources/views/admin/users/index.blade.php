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
                        class="inline-flex items-center px-6 py-3 mb-2 text-white font-medium rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl"
                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
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
                {{-- Privilege Details --}}
                <div class="mb-6 bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6" style="border: 1px solid #e5e7eb;">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Privilege Details</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Admin</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Has full access to the system, including deleting clients and data.</p>
                            <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 list-disc ms-5">
                                <li>All permissions (manage users, reference data, activity logs)</li>
                                <li>Create, update, delete, restore, export</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-lg border border-gray-2 00 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Data Entry</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Can add, upload and view only. Edit requires Manager permission.</p>
                            <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 list-disc ms-5">
                                <li>View + Create (no edit/delete)</li>
                                <li>No exports unless explicitly granted</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Manager</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Can add, upload, view, edit and grant edit permission to Data Entry.</p>
                            <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 list-disc ms-5">
                                <li>Create + Update (no destructive deletes on clients/data)</li>
                                <li>Can grant edit capability to Data Entry users</li>
                            </ul>
                        </div>

                        <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Viewer</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Can view only and export. Cannot upload or modify data.</p>
                            <ul class="mt-2 text-sm text-gray-600 dark:text-gray-400 list-disc ms-5">
                                <li>View + Export</li>
                                <li>No create/edit/delete</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg p-6" style="border: 1px solid #e5e7eb;">
                    <div class="overflow-x-auto">
                        <table class="min-w-full w-100 text-sm" style="border-collapse: separate; border-spacing: 0;">
                            <thead class="ltr:text-left rtl:text-right">
                                <tr style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);">
                                    <th class="text-left p-3 font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">#</th>
                                    <th class="text-left p-3 font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">{{ __('messages.table.name') }}</th>
                                    <th class="text-left p-3 font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">{{ __('messages.table.email') }}</th>
                                    <th class="text-left p-3 font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">{{ __('messages.table.roles') }}</th>
                                    <th class="text-right p-3 font-semibold text-gray-700" style="border-bottom: 2px solid #3b82f6;">{{ __('messages.table.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="ltr:text-left rtl:text-right">
                            @foreach($users as $u)
                                <tr class="hover:bg-gray-50 transition-colors duration-150" style="border-bottom: 1px solid #e5e7eb;">
                                    <td class="p-3 text-gray-600">{{ $u->id }}</td>
                                    <td class="p-3 text-gray-900 font-medium">{{ $u->name }}</td>
                                    <td class="p-3 text-gray-600">{{ $u->email }}</td>
                                    <td class="p-3">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" style="background: linear-gradient(90deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af;">
                                            {{ $u->getRoleNames()->implode(', ') }}
                                        </span>
                                    </td>
                                    <td class="p-2 text-right">
                                        @if(auth()->id() !== $u->id)
                                            <form method="POST"
                                                action="{{ route('admin.users.destroy', $u) }}"
                                                class="d-inline"
                                                onsubmit="return confirm('Delete this user? This can be undone only if soft deletes are enabled.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-3 py-1.5 text-xs font-medium rounded-md text-white transition-all duration-150"
                                                        style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);"
                                                        onmouseover="this.style.background='linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)'"
                                                        onmouseout="this.style.background='linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'">
                                                    Delete
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.users.edit',$u) }}"
                                        class="inline-flex items-center px-3 py-1.5 rounded-md text-xs font-medium text-white transition-all duration-150"
                                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                                        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                                            {{ __('messages.common.edit') }}
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
    </div>
</x-app-layout>
