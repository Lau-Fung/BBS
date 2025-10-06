<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $client->name }} - {{ __('messages.clients.client_details') }}</title>
    <style>
        @page {
            margin: 5mm;
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
            margin-bottom: 4px;
            border-bottom: 1px solid #333;
            padding-bottom: 2px;
        }
        
        .header h1 {
            font-size: 12px;
            margin: 0 0 2px 0;
            color: #2c3e50;
        }
        
        .header p {
            font-size: 8px;
            margin: 0;
            color: #666;
        }
        
        .client-info {
            margin-bottom: 3px;
            background-color: #f8f9fa;
            padding: 2px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .client-info h2 {
            margin: 0 0 2px 0;
            color: #2c3e50;
            font-size: 8px;
        }
        
        .client-info p {
            margin: 1px 0;
            font-size: 6px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2px;
            font-size: 5px;
            table-layout: fixed;
        }
        
        thead { 
            display: table-header-group; 
        }
        
        tfoot { 
            display: table-row-group; 
        }
        
        tr { 
            page-break-inside: avoid; 
        }
        
        th {
            background-color: #34495e;
            color: white;
            padding: 1px 0.5px;
            text-align: center;
            font-weight: normal;
            border: 1px solid #2c3e50;
            font-size: 5px;
            white-space: normal;
            word-break: normal;
            line-height: 1.1;
            overflow: visible;
        }
        
        td {
            padding: 1px 0.5px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            white-space: normal;
            overflow: visible;
            font-size: 5.5px;
            line-height: 1.0;
        }
        
        tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        tr:nth-child(odd) {
            background-color: white;
        }
        
        .footer {
            margin-top: 5px;
            text-align: center;
            font-size: 6px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 3px;
        }
        
        .page-break {
            page-break-before: always;
            height: 0;
            line-height: 0;
        }
        
        .long-text {
            white-space: normal;
            word-wrap: break-word;
            overflow: visible;
        }
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
                @if($index > 0 && $index % 35 == 0)
                    <tr class="page-break"></tr>
                @endif
                <tr>
                    @foreach($row as $cell)
                        <td class="long-text">{{ $cell ?? '' }}</td>
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