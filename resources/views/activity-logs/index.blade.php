<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.activity_logs.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards - Modern Style -->
            <div class="grid grid-cols-1 mb-3 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- Total Activities Card -->
                <div class="rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
                    <div class="p-6 ">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-blue-100 text-sm font-medium uppercase tracking-wide">{{ __('messages.activity_logs.total_activities') }}</p>
                                <p class="text-3xl font-bold mt-2 text-white">{{ $stats['total_activities'] }}</p>
                            </div>
                            <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Logins Card -->
                <div class="rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="p-6 ">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-green-100 text-sm font-medium uppercase tracking-wide">{{ __('messages.activity_logs.logins') }}</p>
                                <p class="text-3xl font-bold mt-2 text-white">{{ $stats['logins'] }}</p>
                            </div>
                            <div class="bg-green-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Imports Card -->
                <div class="rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
                    <div class="p-6 ">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-purple-100 text-sm font-medium uppercase tracking-wide">{{ __('messages.activity_logs.imports') }}</p>
                                <p class="text-3xl font-bold mt-2 text-white">{{ $stats['imports'] }}</p>
                            </div>
                            <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Exports Card -->
                <div class="rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);">
                    <div class="p-6 ">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-orange-100 text-sm font-medium uppercase tracking-wide">{{ __('messages.activity_logs.exports') }}</p>
                                <p class="text-3xl font-bold mt-2 text-white">{{ $stats['exports'] }}</p>
                            </div>
                            <div class="bg-orange-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Creates Card -->
                <div class="rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <div class="p-6 ">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium uppercase tracking-wide">{{ __('messages.activity_logs.creates') }}</p>
                                <p class="text-3xl font-bold mt-2 text-white">{{ $stats['creates'] }}</p>
                            </div>
                            <div class="bg-emerald-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Updates Card -->
                <div class="rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);">
                    <div class="p-6 ">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-cyan-100 text-sm font-medium uppercase tracking-wide">{{ __('messages.activity_logs.updates') }}</p>
                                <p class="text-3xl font-bold mt-2 text-white">{{ $stats['updates'] }}</p>
                            </div>
                            <div class="bg-cyan-400 bg-opacity-30 rounded-full p-3">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters and Export Actions -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 mb-6">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.activity_logs.filters') }}</h3>
                        <div class="flex space-x-3">
                            <a href="{{ route('activity-logs.export.csv', request()->query()) }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg font-semibold text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-150 shadow-lg hover:shadow-xl"
                               style="background: linear-gradient(90deg, #10b981 0%, #059669 100%);"
                               onmouseover="this.style.background='linear-gradient(90deg, #059669 0%, #047857 100%)'"
                               onmouseout="this.style.background='linear-gradient(90deg, #10b981 0%, #059669 100%)'">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.common.export') }} CSV
                            </a>
                            <a href="{{ route('activity-logs.export.pdf', request()->query()) }}" 
                               class="inline-flex items-center px-4 py-2 ml-3 border border-transparent rounded-lg font-semibold text-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-150 shadow-lg hover:shadow-xl"
                               style="background: linear-gradient(90deg, #ef4444 0%, #dc2626 100%);"
                               onmouseover="this.style.background='linear-gradient(90deg, #dc2626 0%, #b91c1c 100%)'"
                               onmouseout="this.style.background='linear-gradient(90deg, #ef4444 0%, #dc2626 100%)'">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.common.export') }} PDF
                            </a>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700">{{ __('messages.activity_logs.search') }}</label>
                            <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                   placeholder="{{ __('messages.activity_logs.search_placeholder') }}">
                        </div>
                        <div>
                            <label for="user_id" class="block text-sm font-medium text-gray-700">{{ __('messages.activity_logs.user') }}</label>
                            <select name="user_id" id="user_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('messages.activity_logs.all_users') }}</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}" {{ ($filters['user_id'] ?? '') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="event" class="block text-sm font-medium text-gray-700">{{ __('messages.activity_logs.event') }}</label>
                            <select name="event" id="event" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="">{{ __('messages.activity_logs.all_events') }}</option>
                                <option value="created" {{ ($filters['event'] ?? '') === 'created' ? 'selected' : '' }}>{{ __('messages.activity_logs.created') }}</option>
                                <option value="updated" {{ ($filters['event'] ?? '') === 'updated' ? 'selected' : '' }}>{{ __('messages.activity_logs.updated') }}</option>
                                <option value="deleted" {{ ($filters['event'] ?? '') === 'deleted' ? 'selected' : '' }}>{{ __('messages.activity_logs.deleted') }}</option>
                                <option value="login" {{ ($filters['event'] ?? '') === 'login' ? 'selected' : '' }}>{{ __('messages.activity_logs.login') }}</option>
                                <option value="logout" {{ ($filters['event'] ?? '') === 'logout' ? 'selected' : '' }}>{{ __('messages.activity_logs.logout') }}</option>
                                <option value="import" {{ ($filters['event'] ?? '') === 'import' ? 'selected' : '' }}>{{ __('messages.activity_logs.import') }}</option>
                                <option value="export" {{ ($filters['event'] ?? '') === 'export' ? 'selected' : '' }}>{{ __('messages.activity_logs.export') }}</option>
                                <option value="bulk_operation" {{ ($filters['event'] ?? '') === 'bulk_operation' ? 'selected' : '' }}>{{ __('messages.activity_logs.bulk_operation') }}</option>
                                <option value="failed_login" {{ ($filters['event'] ?? '') === 'failed_login' ? 'selected' : '' }}>{{ __('messages.activity_logs.failed_login') }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="date_from" class="block text-sm font-medium text-gray-700">{{ __('messages.activity_logs.from_date') }}</label>
                            <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label for="date_to" class="block text-sm font-medium text-gray-700">{{ __('messages.activity_logs.to_date') }}</label>
                            <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}" 
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div class="lg:col-span-5 flex space-x-4">
                            <button type="submit" class="inline-flex items-center px-6 py-3 font-semibold text-sm rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                                    style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                                    onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                                    onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.activity_logs.filter') }}
                            </button>
                            <a href="{{ route('activity-logs.index') }}" class="ml-3 inline-flex items-center px-6 py-3 font-semibold text-sm rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                               style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);"
                               onmouseover="this.style.background='linear-gradient(135deg, #4b5563 0%, #374151 100%)'"
                               onmouseout="this.style.background='linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                                {{ __('messages.activity_logs.clear') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Activity Logs Table -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">{{ __('messages.activity_logs.recent_activities') }}</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 w-100">
                        <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('messages.activity_logs.user_col') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('messages.activity_logs.event_col') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('messages.activity_logs.subject_col') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('messages.activity_logs.description_col') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('messages.activity_logs.date_col') }}</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">{{ __('messages.activity_logs.actions_col') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                                @forelse($activities as $activity)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8">
                                                    <div class="h-8 w-8 rounded-full bg-gradient-to-r from-blue-400 to-blue-600 flex items-center justify-center">
                                                        <span class="text-xs font-bold ">
                                                            {{-- {{ substr($activity->causer->name ?? 'S', 0, 1) }} --}}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">{{ $activity->causer->name ?? __('messages.activity_logs.system') }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full shadow-sm border"
                                                @if($activity->event === 'created')
                                                    style="background: linear-gradient(90deg, #dcfce7 0%, #bbf7d0 100%); color: #166534; border-color: #86efac;"
                                                @elseif($activity->event === 'updated')
                                                    style="background: linear-gradient(90deg, #dbeafe 0%, #bfdbfe 100%); color: #1e40af; border-color: #93c5fd;"
                                                @elseif($activity->event === 'deleted')
                                                    style="background: linear-gradient(90deg, #fecaca 0%, #fca5a5 100%); color: #991b1b; border-color: #f87171;"
                                                @elseif($activity->event === 'login')
                                                    style="background: linear-gradient(90deg, #d1fae5 0%, #a7f3d0 100%); color: #065f46; border-color: #6ee7b7;"
                                                @elseif($activity->event === 'logout')
                                                    style="background: linear-gradient(90deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151; border-color: #d1d5db;"
                                                @elseif($activity->event === 'import')
                                                    style="background: linear-gradient(90deg, #e9d5ff 0%, #ddd6fe 100%); color: #6b21a8; border-color: #c4b5fd;"
                                                @elseif($activity->event === 'export')
                                                    style="background: linear-gradient(90deg, #fed7aa 0%, #fdba74 100%); color: #9a3412; border-color: #fb923c;"
                                                @elseif($activity->event === 'bulk_operation')
                                                    style="background: linear-gradient(90deg, #e0e7ff 0%, #c7d2fe 100%); color: #3730a3; border-color: #a5b4fc;"
                                                @else
                                                    style="background: linear-gradient(90deg, #f3f4f6 0%, #e5e7eb 100%); color: #374151; border-color: #d1d5db;"
                                                @endif>
                                                {{ __('messages.activity_logs.' . $activity->event) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $activity->subject_type ? class_basename($activity->subject_type) : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $activity->description }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="{{ route('activity-logs.show', $activity) }}" 
                                               class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-150"
                                               style="background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);"
                                               onmouseover="this.style.background='linear-gradient(90deg, #4f46e5 0%, #4338ca 100%)'"
                                               onmouseout="this.style.background='linear-gradient(90deg, #6366f1 0%, #4f46e5 100%)'">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ __('messages.activity_logs.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                            {{ __('messages.activity_logs.no_activities') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6 flex items-center justify-between m-3">
                        <div class="flex-1 flex justify-between sm:hidden">
                            @if ($activities->onFirstPage())
                                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed rounded-md">
                                    {{ __('messages.activity_logs.previous') }}
                                </span>
                            @else
                                <a href="{{ $activities->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors duration-150">
                                    {{ __('messages.activity_logs.previous') }}
                                </a>
                            @endif

                            @if ($activities->hasMorePages())
                                <a href="{{ $activities->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 rounded-md transition-colors duration-150">
                                    {{ __('messages.activity_logs.next') }}
                                </a>
                            @else
                                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-500 cursor-not-allowed rounded-md">
                                    {{ __('messages.activity_logs.next') }}
                                </span>
                            @endif
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    {{ __('messages.activity_logs.showing') }}
                                    <span class="font-medium">{{ $activities->firstItem() }}</span>
                                    {{ __('messages.activity_logs.to') }}
                                    <span class="font-medium">{{ $activities->lastItem() }}</span>
                                    {{ __('messages.activity_logs.of') }}
                                    <span class="font-medium">{{ $activities->total() }}</span>
                                    {{ __('messages.activity_logs.results') }}
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    {{-- Previous Page Link --}}
                                    @if ($activities->onFirstPage())
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @else
                                        <a href="{{ $activities->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-150">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Pagination Elements --}}
                                    @foreach ($activities->getUrlRange(1, $activities->lastPage()) as $page => $url)
                                        @if ($page == $activities->currentPage())
                                            <span class="relative inline-flex items-center px-4 py-2 border border-indigo-500 bg-indigo-50 text-sm font-medium text-indigo-600">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors duration-150">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach

                                    {{-- Next Page Link --}}
                                    @if ($activities->hasMorePages())
                                        <a href="{{ $activities->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 transition-colors duration-150">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    @else
                                        <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-400 cursor-not-allowed">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    @endif
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
