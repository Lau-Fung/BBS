@php
use App\Services\ActivityLogService;
$stats = ActivityLogService::getActivityStats(7);
@endphp

<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity (Last 7 Days)</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $stats['logins'] }}</div>
                <div class="text-sm text-gray-500">Logins</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $stats['creates'] }}</div>
                <div class="text-sm text-gray-500">Created</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600">{{ $stats['updates'] }}</div>
                <div class="text-sm text-gray-500">Updated</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-red-600">{{ $stats['deletes'] }}</div>
                <div class="text-sm text-gray-500">Deleted</div>
            </div>
        </div>
        <div class="mt-4 text-center">
            <a href="{{ route('activity-logs.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                View All Activity Logs →
            </a>
        </div>
    </div>
</div>
