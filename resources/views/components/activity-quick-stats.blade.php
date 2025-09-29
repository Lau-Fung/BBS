<div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
    <div class="px-6 py-4 bg-gradient-to-r from-indigo-50 to-indigo-100 border-b border-indigo-200">
        <h3 class="text-lg font-semibold text-indigo-800">{{ __('messages.activity_logs.quick_stats') }}</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['today_activities'] }}</div>
                <div class="text-sm text-gray-600">{{ __('messages.activity_logs.today') }}</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['yesterday_activities'] }}</div>
                <div class="text-sm text-gray-600">{{ __('messages.activity_logs.yesterday') }}</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['this_week_activities'] }}</div>
                <div class="text-sm text-gray-600">{{ __('messages.activity_logs.this_week') }}</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900">{{ $stats['last_week_activities'] }}</div>
                <div class="text-sm text-gray-600">{{ __('messages.activity_logs.last_week') }}</div>
            </div>
        </div>

        @if(!empty($stats['top_users']))
        <div class="mb-6">
            <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('messages.activity_logs.top_users') }}</h4>
            <div class="space-y-2">
                @foreach($stats['top_users'] as $user)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ $user['user'] }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $user['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if(!empty($stats['top_events']))
        <div>
            <h4 class="text-sm font-semibold text-gray-700 mb-3">{{ __('messages.activity_logs.top_events') }}</h4>
            <div class="space-y-2">
                @foreach($stats['top_events'] as $event)
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">{{ __('messages.activity_logs.' . $event['event']) }}</span>
                    <span class="text-sm font-medium text-gray-900">{{ $event['count'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
