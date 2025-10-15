<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ActivityLogService;
use Spatie\Activitylog\Models\Activity;
use App\Exports\ActivityLogsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:activity_logs.view')->only(['index', 'show', 'exportCsv', 'exportPdf']);
    }

    /**
     * Display a listing of activity logs
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'user_id',
            'subject_type', 
            'event',
            'date_from',
            'date_to',
            'search'
        ]);

        // Load all activities for client-side DataTables processing
        $activities = ActivityLogService::getActivityLogs($filters, 1000);
        $stats = ActivityLogService::getActivityStats(30);

        return view('activity-logs.index', compact('activities', 'stats', 'filters'));
    }

    /**
     * Display the specified activity log
     */
    public function show(Activity $activity)
    {
        $this->authorize('view', $activity);
        
        $activity->load(['causer', 'subject']);
        
        return view('activity-logs.show', compact('activity'));
    }

    /**
     * Get activity statistics for dashboard
     */
    public function stats(Request $request)
    {
        $days = $request->get('days', 30);
        $stats = ActivityLogService::getActivityStats($days);
        
        return response()->json($stats);
    }

    /**
     * Export activity logs to CSV
     */
    public function exportCsv(Request $request)
    {
        $filters = $request->only([
            'user_id',
            'subject_type', 
            'event',
            'date_from',
            'date_to',
            'search'
        ]);

        $activities = ActivityLogService::getActivityLogsForExport($filters);
        
        // Log the export activity
        ActivityLogService::logExport('activity_logs', 'CSV', $activities->count(), $filters);

        return Excel::download(new ActivityLogsExport($activities), 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv');
    }

    /**
     * Export activity logs to PDF
     */
    public function exportPdf(Request $request)
    {
        $filters = $request->only([
            'user_id',
            'subject_type', 
            'event',
            'date_from',
            'date_to',
            'search'
        ]);

        $activities = ActivityLogService::getActivityLogsForExport($filters);
        $stats = ActivityLogService::getActivityStats(30);
        
        // Log the export activity
        ActivityLogService::logExport('activity_logs', 'PDF', $activities->count(), $filters);

        $pdf = Pdf::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'defaultFont'          => 'Amiri',
            ])->loadView('activity-logs.export-pdf', compact('activities', 'stats', 'filters'));
        
        return $pdf->download('activity_logs_' . now()->format('Y-m-d_H-i-s') . '.pdf');
    }

    /**
     * Get quick stats for dashboard widget
     */
    public function quickStats(Request $request)
    {
        $days = $request->get('days', 7);
        $stats = ActivityLogService::getQuickStats($days);
        
        return response()->json($stats);
    }
}
