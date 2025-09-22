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
            table-layout: fixed;
        }
        
        th {
            background-color: #34495e;
            color: white;
            padding: 4px 2px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #2c3e50;
            font-size: 7px;
            white-space: nowrap;
            overflow: hidden;
        }
        
        td {
            padding: 3px 2px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
            word-wrap: break-word;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
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
            max-height: 20px;
            overflow: hidden;
        }
        
        /* Ensure Arabic text displays correctly */
        .arabic-text {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            direction: rtl;
        }
        
        /* Column widths for better distribution - Total: 100% */
        th:nth-child(1), td:nth-child(1) { width: 2%; }  /* No */
        th:nth-child(2), td:nth-child(2) { width: 4%; }  /* Package Type */
        th:nth-child(3), td:nth-child(3) { width: 4%; }  /* SIM Type */
        th:nth-child(4), td:nth-child(4) { width: 5%; }  /* SIM Number */
        th:nth-child(5), td:nth-child(5) { width: 5%; }  /* IMEI */
        th:nth-child(6), td:nth-child(6) { width: 4%; }  /* Plate */
        th:nth-child(7), td:nth-child(7) { width: 4%; }  /* Installed On */
        th:nth-child(8), td:nth-child(8) { width: 3%; }  /* Year Model */
        th:nth-child(9), td:nth-child(9) { width: 5%; }  /* Company Manufacture */
        th:nth-child(10), td:nth-child(10) { width: 4%; } /* Device Type */
        th:nth-child(11), td:nth-child(11) { width: 3%; } /* Air */
        th:nth-child(12), td:nth-child(12) { width: 4%; } /* Sensor Type */
        th:nth-child(13), td:nth-child(13) { width: 3%; } /* Mechanic */
        th:nth-child(14), td:nth-child(14) { width: 3%; } /* Tracking */
        th:nth-child(15), td:nth-child(15) { width: 4%; } /* System Type */
        th:nth-child(16), td:nth-child(16) { width: 3%; } /* Calibration */
        th:nth-child(17), td:nth-child(17) { width: 3%; } /* Color */
        th:nth-child(18), td:nth-child(18) { width: 3%; } /* CRM */
        th:nth-child(19), td:nth-child(19) { width: 4%; } /* Subscription Type */
        th:nth-child(20), td:nth-child(20) { width: 3%; } /* Technician */
        th:nth-child(21), td:nth-child(21) { width: 4%; } /* Vehicle Serial */
        th:nth-child(22), td:nth-child(22) { width: 3%; } /* Vehicle Weight */
        th:nth-child(23), td:nth-child(23) { width: 3%; } /* User */
        th:nth-child(24), td:nth-child(24) { width: 5%; } /* Notes */
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
                        <td class="arabic-text long-text" title="{{ $cell ?? '' }}">
                            @if(strlen($cell ?? '') > 15)
                                {{ substr($cell, 0, 15) }}...
                            @else
                                {{ $cell ?? '' }}
                            @endif
                        </td>
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
