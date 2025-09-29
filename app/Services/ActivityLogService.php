<?php

namespace App\Services;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class ActivityLogService
{
    /**
     * Log import activity
     */
    public static function logImport(string $type, int $recordCount, ?string $fileName = null, ?array $metadata = []): void
    {
        activity()
            ->event('import')
            ->withProperties(array_merge([
                'import_type' => $type,
                'record_count' => $recordCount,
                'file_name' => $fileName,
                'import_time' => now(),
                'ip_address' => request()->ip(),
            ], $metadata))
            ->log("Import {$type} - {$recordCount} records" . ($fileName ? " from {$fileName}" : ''));
    }

    /**
     * Log export activity
     */
    public static function logExport(string $type, string $format, int $recordCount = null, ?array $filters = []): void
    {
        activity()
            ->event('export')
            ->withProperties([
                'export_type' => $type,
                'format' => $format,
                'record_count' => $recordCount,
                'filters' => $filters,
                'export_time' => now(),
                'ip_address' => request()->ip(),
            ])
            ->log("Export {$type} as {$format}" . ($recordCount ? " - {$recordCount} records" : ''));
    }

    /**
     * Log bulk operation
     */
    public static function logBulkOperation(string $operation, string $model, int $count, ?array $metadata = []): void
    {
        activity()
            ->event('bulk_operation')
            ->withProperties(array_merge([
                'operation' => $operation,
                'model' => $model,
                'count' => $count,
                'operation_time' => now(),
                'ip_address' => request()->ip(),
            ], $metadata))
            ->log("Bulk {$operation} {$model} - {$count} records");
    }

    /**
     * Log system activity
     */
    public static function logSystemActivity(string $action, ?array $metadata = []): void
    {
        activity()
            ->event('system')
            ->withProperties(array_merge([
                'action' => $action,
                'timestamp' => now(),
                'ip_address' => request()->ip(),
            ], $metadata))
            ->log("System: {$action}");
    }

    /**
     * Get activity logs with filters
     */
    public static function getActivityLogs(array $filters = [], int $perPage = 20)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        if (isset($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (isset($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        if (isset($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Get activity statistics
     */
    public static function getActivityStats(int $days = 30): array
    {
        $startDate = now()->subDays($days);

        return [
            'total_activities' => Activity::where('created_at', '>=', $startDate)->count(),
            'logins' => Activity::where('description', 'like', '%logged in%')
                ->where('created_at', '>=', $startDate)->count(),
            'imports' => Activity::where('description', 'like', '%Import%')
                ->where('created_at', '>=', $startDate)->count(),
            'exports' => Activity::where('description', 'like', '%Export%')
                ->where('created_at', '>=', $startDate)->count(),
            'creates' => Activity::where('event', 'created')
                ->where('created_at', '>=', $startDate)->count(),
            'updates' => Activity::where('event', 'updated')
                ->where('created_at', '>=', $startDate)->count(),
            'deletes' => Activity::where('event', 'deleted')
                ->where('created_at', '>=', $startDate)->count(),
        ];
    }

    /**
     * Get activity logs for export (without pagination)
     */
    public static function getActivityLogsForExport(array $filters = [])
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        if (isset($filters['user_id'])) {
            $query->where('causer_id', $filters['user_id']);
        }

        if (isset($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        if (isset($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('log_name', 'like', "%{$search}%");
            });
        }

        return $query->get();
    }

    /**
     * Get quick stats for dashboard widget
     */
    public static function getQuickStats(int $days = 7): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        // Get daily activity counts
        $dailyActivities = Activity::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill missing dates with 0
        $dates = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dates[] = $date;
        }

        $chartData = [];
        foreach ($dates as $date) {
            $chartData[] = [
                'date' => $date,
                'count' => $dailyActivities[$date] ?? 0
            ];
        }

        return [
            'total_activities' => Activity::whereBetween('created_at', [$startDate, $endDate])->count(),
            'today_activities' => Activity::whereDate('created_at', today())->count(),
            'yesterday_activities' => Activity::whereDate('created_at', now()->subDay())->count(),
            'this_week_activities' => Activity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'last_week_activities' => Activity::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count(),
            'chart_data' => $chartData,
            'top_users' => Activity::whereBetween('created_at', [$startDate, $endDate])
                ->whereNotNull('causer_id')
                ->with('causer')
                ->get()
                ->groupBy('causer_id')
                ->map(function ($activities) {
                    return [
                        'user' => $activities->first()->causer->name ?? 'Unknown',
                        'count' => $activities->count()
                    ];
                })
                ->sortByDesc('count')
                ->take(5)
                ->values(),
            'top_events' => Activity::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('event, COUNT(*) as count')
                ->groupBy('event')
                ->orderByDesc('count')
                ->take(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'event' => $item->event,
                        'count' => $item->count
                    ];
                }),
        ];
    }
}
