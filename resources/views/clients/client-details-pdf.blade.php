<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ __('messages.clients.details') }} - {{ $client->name ?? '' }}</title>
    <style>
        @page { margin: 18px; }
        body {
            direction: {{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }};
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #111827;
        }
        .header { text-align: center; margin-bottom: 10px; }
        .title { font-size: 18px; font-weight: bold; }
        .subtitle { color: #6b7280; font-size: 12px; }
        .info-box {
            border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; margin: 12px 0;
            background: #f9fafb;
        }
        .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 6px 12px; }
        .label { color:#374151; font-weight: bold; }
        .value { color:#111827; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 5px; }
        th { background: #f3f4f6; text-align: {{ app()->getLocale()==='ar' ? 'right' : 'left' }}; font-weight: bold; }
        td { vertical-align: top; }
        .muted { color:#6b7280; }
    </style>
    </head>
<body>
    <div class="header">
        <div class="title">{{ __('messages.clients.details') }}</div>
        <div class="subtitle">{{ __('messages.common.report') }} - {{ now()->format('Y-m-d H:i') }}</div>
    </div>

    <div class="info-box">
        <div class="info-grid">
            <div><span class="label">{{ __('messages.clients.name') }}:</span> <span class="value">{{ $client->name ?? '' }}</span></div>
            <div><span class="label">{{ __('messages.clients.sector') }}:</span> <span class="value">{{ $client->sector ?? __('messages.common.not_specified') }}</span></div>
            <div><span class="label">{{ __('messages.clients.subscription') }}:</span> <span class="value">{{ $client->subscription_type ?? __('messages.common.not_specified') }}</span></div>
            <div><span class="label">{{ __('messages.activity_logs.total_records') }}:</span> <span class="value">{{ count($rows ?? []) }}</span></div>
        </div>
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

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $client->name }} - {{ __('messages.clients.client_details') }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }
        
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 8px;
            line-height: 1.2;
            color: #333;
            direction: rtl;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 8px;
        }
        
        .header h1 {
            font-size: 14px;
            margin: 0 0 5px 0;
            color: #2c3e50;
        }
        
        .header p {
            font-size: 10px;
            margin: 0;
            color: #666;
        }
        
        .client-info {
            margin-bottom: 10px;
            background-color: #f8f9fa;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .client-info h2 {
            margin: 0 0 8px 0;
            color: #2c3e50;
            font-size: 12px;
        }
        
        .client-info p {
            margin: 3px 0;
            font-size: 9px;
        }
        
         table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
             font-size: 7px;
             table-layout: auto; /* allow columns to size to content */
        }
         thead { display: table-header-group; }
         tfoot { display: table-row-group; }
         tr    { page-break-inside: avoid; }
        
         th {
            background-color: #34495e;
            color: white;
             padding: 3px 2px;
            text-align: center;
            font-weight: normal; /* bold can break Arabic shaping in some PDF engines */
            border: 1px solid #2c3e50;
             font-size: 6.5px; /* slightly smaller to fit multi-line */
             white-space: normal;        /* allow wrapping */
             word-break: normal;         /* keep Arabic letters intact */
             line-height: 1.35;
            overflow: visible;
            direction: rtl;                /* ensure RTL shaping */
            unicode-bidi: isolate-override; /* stronger bidi handling for Arabic */
        }
        
         td {
            padding: 3px 2px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
             word-wrap: break-word;
            white-space: normal;
            overflow: visible;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:nth-child(odd) {
            background-color: white;
        }
        
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 8px;
        }
        
        .page-break {
            page-break-before: always;
            height: 0;
            line-height: 0;
        }
        
        /* Better text handling for long content */
        .long-text {
            white-space: normal;
            word-wrap: break-word;
            overflow: visible;
        }
        
        /* Ensure Arabic text displays correctly */
        .arabic-text {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            direction: rtl;
        }
        
         /* remove fixed widths to allow full wrapping of long Arabic headers */
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $client->name }} - {{ __('messages.clients.client_details') }}</h1>
        <p>{{ __('messages.clients.detailed_report') }}</p>
    </div>
    
    <div class="client-info">
        <h2>{{ __('messages.clients.client_info') }}</h2>
        <p><strong>{{ __('messages.clients.name') }}:</strong> {{ $client->name }}</p>
        <p><strong>{{ __('messages.clients.sector') }}:</strong> {{ $client->sector ?? __('messages.clients.not_specified') }}</p>
        <p><strong>{{ __('messages.clients.subscription') }}:</strong> {{ $client->subscription_type ?? __('messages.clients.not_specified') }}</p>
        <p><strong>{{ __('messages.clients.total_records') }}:</strong> {{ $clientSheetRows->count() }}</p>
        <p><strong>{{ __('messages.clients.report_date') }}:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                @foreach($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $index => $row)
                @if($index > 0 && $index % 20 == 0)
                    <tr class="page-break"></tr>
                @endif
                <tr>
                    @foreach($row as $cell)
                        <td class="arabic-text long-text">{{ $cell ?? '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>تم إنشاء هذا التقرير في {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>إجمالي السجلات: {{ count($rows) }}</p>
    </div>
</body>
</html>
