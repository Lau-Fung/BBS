<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.activity_logs.details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.activity_logs.basic_information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.id') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $activity->id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.event') }}</dt>
                                    <dd class="text-sm">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                            {{ $activity->event === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $activity->event === 'updated' ? 'bg-blue-100 text-blue-800' : '' }}
                                            {{ $activity->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ !in_array($activity->event, ['created', 'updated', 'deleted']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                            {{ __('messages.activity_logs.' . $activity->event) }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.description') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $activity->description }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.log_name') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $activity->log_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.created_at') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $activity->created_at->format('M d, Y H:i:s') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- User Information -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.activity_logs.user_information') }}</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.user') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $activity->causer->name ?? __('messages.activity_logs.system') }}
                                        @if($activity->causer)
                                            <span class="text-gray-500">({{ $activity->causer->email }})</span>
                                        @endif
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.subject_type') }}</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $activity->subject_type ? class_basename($activity->subject_type) : __('messages.activity_logs.not_available') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">{{ __('messages.activity_logs.subject_id') }}</dt>
                                    <dd class="text-sm text-gray-900">{{ $activity->subject_id ?? __('messages.activity_logs.not_available') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Properties -->
                    @if($activity->properties && count($activity->properties) > 0)
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.activity_logs.properties') }}</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <pre class="text-sm text-gray-900 whitespace-pre-wrap">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        </div>
                    @endif

                    <!-- Changes (for updated events) -->
                    @if($activity->event === 'updated' && isset($activity->properties['old']) && isset($activity->properties['attributes']))
                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('messages.activity_logs.changes') }}</h3>
                            <div class="space-y-4">
                                @foreach($activity->properties['attributes'] as $key => $newValue)
                                    @php
                                        $oldValue = $activity->properties['old'][$key] ?? null;
                                    @endphp
                                    @if($oldValue !== $newValue)
                                        <div class="border rounded-lg p-4">
                                            <div class="font-medium text-gray-900 mb-2">{{ ucfirst(str_replace('_', ' ', $key)) }}</div>
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <div class="text-sm font-medium text-red-600 mb-1">{{ __('messages.activity_logs.old_value') }}</div>
                                                    <div class="text-sm text-gray-900 bg-red-50 p-2 rounded">
                                                        {{ is_array($oldValue) ? json_encode($oldValue) : ($oldValue ?? 'null') }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-green-600 mb-1">{{ __('messages.activity_logs.new_value') }}</div>
                                                    <div class="text-sm text-gray-900 bg-green-50 p-2 rounded">
                                                        {{ is_array($newValue) ? json_encode($newValue) : ($newValue ?? 'null') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="mt-8 flex justify-end">
                        <a href="{{ route('activity-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('messages.activity_logs.back_to_activity_logs') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
