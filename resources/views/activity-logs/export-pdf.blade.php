<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('messages.activity_logs.title') }} - {{ __('messages.common.export') }}</title>
    <style>
        /* Use DejaVu Sans for Arabic; set document direction based on locale */
        @page { margin: 20px; }
        body { direction: {{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 20px;
        }
        .header h1 {
            color: #1f2937;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            color: #6b7280;
            margin: 5px 0 0 0;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
        }
        .stat-number {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin: 0;
        }
        .stat-label {
            font-size: 12px;
            color: #6b7280;
            margin: 5px 0 0 0;
        }
        .filters {
            background: #f3f4f6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
        }
        .filters h3 {
            margin: 0 0 10px 0;
            color: #1f2937;
            font-size: 16px;
        }
        .filter-item {
            display: inline-block;
            margin-right: 20px;
            margin-bottom: 5px;
        }
        .filter-label {
            font-weight: bold;
            color: #374151;
        }
        .filter-value {
            color: #6b7280;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th {
            background: #f3f4f6;
            color: #374151;
            font-weight: bold;
            padding: 12px 8px;
            text-align: {{ app()->getLocale()==='ar' ? 'right' : 'left' }};
            border: 1px solid #d1d5db;
            font-size: 11px;
        }
        .table td {
            padding: 10px 8px;
            border: 1px solid #d1d5db;
            font-size: 10px;
            vertical-align: top;
        }
        .table tr:nth-child(even) {
            background: #f9fafb;
        }
        .event-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .event-created { background: #dcfce7; color: #166534; }
        .event-updated { background: #dbeafe; color: #1e40af; }
        .event-deleted { background: #fecaca; color: #991b1b; }
        .event-login { background: #dcfce7; color: #166534; }
        .event-logout { background: #f3f4f6; color: #374151; }
        .event-import { background: #e9d5ff; color: #7c3aed; }
        .event-export { background: #fed7aa; color: #ea580c; }
        .event-bulk_operation { background: #c7d2fe; color: #4338ca; }
        .event-failed_login { background: #fecaca; color: #991b1b; }
        .event-system { background: #f3f4f6; color: #374151; }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            color: #6b7280;
            font-size: 10px;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('messages.activity_logs.title') }}</h1>
        <p>{{ __('messages.common.export') }} - {{ now()->format('M d, Y H:i:s') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number">{{ $stats['total_activities'] }}</div>
            <div class="stat-label">{{ __('messages.activity_logs.total_activities') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['logins'] }}</div>
            <div class="stat-label">{{ __('messages.activity_logs.logins') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['imports'] }}</div>
            <div class="stat-label">{{ __('messages.activity_logs.imports') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['exports'] }}</div>
            <div class="stat-label">{{ __('messages.activity_logs.exports') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['creates'] }}</div>
            <div class="stat-label">{{ __('messages.activity_logs.creates') }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">{{ $stats['updates'] }}</div>
            <div class="stat-label">{{ __('messages.activity_logs.updates') }}</div>
        </div>
    </div>

    <!-- Filters Applied -->
    @if(!empty(array_filter($filters)))
    <div class="filters">
        <h3>{{ __('messages.activity_logs.filters') }}</h3>
        @if(!empty($filters['search']))
            <div class="filter-item">
                <span class="filter-label">{{ __('messages.activity_logs.search') }}:</span>
                <span class="filter-value">{{ $filters['search'] }}</span>
            </div>
        @endif
        @if(!empty($filters['event']))
            <div class="filter-item">
                <span class="filter-label">{{ __('messages.activity_logs.event') }}:</span>
                <span class="filter-value">{{ __('messages.activity_logs.' . $filters['event']) }}</span>
            </div>
        @endif
        @if(!empty($filters['date_from']))
            <div class="filter-item">
                <span class="filter-label">{{ __('messages.activity_logs.from_date') }}:</span>
                <span class="filter-value">{{ \Carbon\Carbon::parse($filters['date_from'])->format('M d, Y') }}</span>
            </div>
        @endif
        @if(!empty($filters['date_to']))
            <div class="filter-item">
                <span class="filter-label">{{ __('messages.activity_logs.to_date') }}:</span>
                <span class="filter-value">{{ \Carbon\Carbon::parse($filters['date_to'])->format('M d, Y') }}</span>
            </div>
        @endif
    </div>
    @endif

    <!-- Activity Logs Table -->
    <table class="table">
        <thead>
            <tr>
                <th>{{ __('messages.activity_logs.id') }}</th>
                <th>{{ __('messages.activity_logs.user_col') }}</th>
                <th>{{ __('messages.activity_logs.event_col') }}</th>
                <th>{{ __('messages.activity_logs.subject_col') }}</th>
                <th>{{ __('messages.activity_logs.description_col') }}</th>
                <th>{{ __('messages.activity_logs.date_col') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $activity)
                <tr>
                    <td>{{ $activity->id }}</td>
                    <td>{{ $activity->causer->name ?? __('messages.activity_logs.system') }}</td>
                    <td>
                        <span class="event-badge event-{{ $activity->event }}">
                            {{ __('messages.activity_logs.' . $activity->event) }}
                        </span>
                    </td>
                    <td>{{ $activity->subject_type ? class_basename($activity->subject_type) : __('messages.activity_logs.not_available') }}</td>
                    <td>{{ Str::limit($activity->description, 50) }}</td>
                    <td>{{ $activity->created_at->format('M d, Y H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        {{ __('messages.activity_logs.no_activities') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ __('messages.activity_logs.title') }} - {{ __('messages.common.export') }} | {{ now()->format('M d, Y H:i:s') }}</p>
        <p>{{ __('messages.activity_logs.showing') }} {{ $activities->count() }} {{ __('messages.activity_logs.results') }}</p>
    </div>
</body>
</html>
