<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.clients.title') }}</title>
    <style>
        @page { margin: 18px; }
        body { direction: {{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}; font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color:#111827; }
        .header { text-align:center; margin-bottom:10px; }
        .title { font-size:18px; font-weight:bold; }
        table { width:100%; border-collapse:collapse; }
        th, td { border:1px solid #d1d5db; padding:6px 5px; }
        th { background:#f3f4f6; text-align: {{ app()->getLocale()==='ar' ? 'right' : 'left' }}; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">{{ __('messages.clients.title') }}</div>
    </div>
    <table>
        <thead>
            <tr>
                @foreach(($headers ?? []) as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach(($rows ?? []) as $r)
                <tr>
                    @foreach($r as $cell)
                        <td>{{ is_scalar($cell) ? $cell : '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.clients.summary_report') }}</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 portrait;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
            direction: rtl;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 3px solid #6366f1;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 20px;
            margin: 0 0 8px 0;
            color: #1f2937;
            font-weight: bold;
        }
        
        .header p {
            font-size: 12px;
            margin: 0;
            color: #6b7280;
        }
        
        .summary-info {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .summary-info h2 {
            margin: 0 0 10px 0;
            color: #374151;
            font-size: 14px;
            font-weight: bold;
        }
        
        .summary-stats {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .stat-item {
            text-align: center;
            padding: 10px;
            background-color: white;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
            min-width: 120px;
        }
        
        .stat-number {
            font-size: 18px;
            font-weight: bold;
            color: #6366f1;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        th {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            padding: 12px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }
        
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        
        tr:hover {
            background-color: #f3f4f6;
        }
        
        .company-name {
            font-weight: bold;
            color: #1f2937;
            text-align: right;
        }
        
        .sector {
            color: #6b7280;
            font-size: 9px;
        }
        
        .number {
            font-weight: bold;
            color: #059669;
        }
        
        .models-list {
            text-align: right;
            font-size: 9px;
            color: #4b5563;
            line-height: 1.3;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 15px;
        }
        
        .arabic-text {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            direction: rtl;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ __('messages.clients.summary_report') }}</h1>
        <p>Clients Summary Report</p>
        @if($q)
            <p>فلتر البحث: "{{ $q }}" | Search Filter: "{{ $q }}"</p>
        @endif
    </div>
    
    <div class="summary-info">
        <h2>{{ __('messages.clients.general_statistics') }}</h2>
        <div class="summary-stats">
            <div class="stat-item">
                <div class="stat-number">{{ $clients->count() }}</div>
                <div class="stat-label">{{ __('messages.clients.total_clients') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $clients->sum('vehicles_count') }}</div>
                <div class="stat-label">{{ __('messages.clients.total_records') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ $clients->sum('total_devices') }}</div>
                <div class="stat-label">{{ __('messages.clients.total_devices') }}</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">{{ now()->format('Y-m-d') }}</div>
                <div class="stat-label">{{ __('messages.clients.report_date') }}</div>
            </div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>{{ __('messages.clients.company_name') }}</th>
                <th>{{ __('messages.clients.sector') }}</th>
                <th>{{ __('messages.clients.total_records') }}</th>
                <th>{{ __('messages.clients.active_devices') }}</th>
                <th>{{ __('messages.clients.devices_by_model') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $i => $c)
                <tr>
                    <td class="number">{{ $i+1 }}</td>
                    <td class="company-name arabic-text">{{ $c->name }}</td>
                    <td class="sector arabic-text">{{ $c->sector ?? __('messages.clients.not_specified') }}</td>
                    <td class="number">{{ $c->vehicles_count }}</td>
                    <td class="number">{{ $c->total_devices }}</td>
                    <td class="models-list">
                        @if($c->models->isNotEmpty())
                            @foreach($c->models as $model)
                                <div>{{ $model['model'] ?? 'UNKNOWN' }}: {{ $model['count'] ?? 0 }}</div>
                            @endforeach
                        @else
                            <span style="color: #9ca3af;">{{ __('messages.clients.no_data_available') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>{{ __('messages.clients.generated_on') }} {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>{{ __('messages.clients.total_clients') }}: {{ $clients->count() }}</p>
    </div>
</body>
</html>
