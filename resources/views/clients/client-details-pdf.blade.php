<div dir="rtl" style="font-family:'DejaVu Sans', Arial, sans-serif; font-size:8px; line-height:1.2; color:#333; margin:0; padding:0;">
    <div style="text-align:center; margin-bottom:4px; border-bottom:1px solid #333; padding-bottom:2px;">
        <h1 style="font-size:12px; margin:0 0 2px 0; color:#2c3e50;">{{ $client->name }} - {{ __('messages.clients.client_details') }}</h1>
        <p style="font-size:8px; margin:0; color:#666;">{{ __('messages.clients.detailed_report') }}</p>
    </div>

    <div style="margin-bottom:3px; background-color:#f8f9fa; padding:2px; border:1px solid #ddd; border-radius:3px;">
        <h2 style="margin:0 0 2px 0; color:#2c3e50; font-size:8px;">{{ __('messages.clients.client_info') }}</h2>
        <p style="margin:1px 0; font-size:6px;"><strong>{{ __('messages.clients.name') }}:</strong> {{ $client->name }}</p>
        <p style="margin:1px 0; font-size:6px;"><strong>{{ __('messages.clients.sector') }}:</strong> {{ $client->sector ?? __('messages.clients.not_specified') }}</p>
        <p style="margin:1px 0; font-size:6px;"><strong>{{ __('messages.clients.subscription') }}:</strong> {{ $client->subscription_type ?? __('messages.clients.not_specified') }}</p>
        <p style="margin:1px 0; font-size:6px;"><strong>{{ __('messages.clients.total_records') }}:</strong> {{ $clientSheetRows->count() }}</p>
        <p style="margin:1px 0; font-size:6px;"><strong>{{ __('messages.clients.report_date') }}:</strong> {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <table style="width:100%; border-collapse:collapse; margin-top:2px; font-size:5px; table-layout:fixed;">
        <thead style="display:table-header-group;">
            <tr>
                @foreach($headers as $header)
                    <th style="background-color:#34495e; color:#fff; padding:1px 0.5px; text-align:center; font-weight:normal; border:1px solid #2c3e50; font-size:5px; white-space:normal; word-break:normal; line-height:1.1; overflow:visible;">{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $index => $row)
                @if($index > 0 && $index % 35 == 0)
                    <tr style="page-break-before:always; height:0; line-height:0;"></tr>
                @endif
                <tr>
                    @foreach($row as $cell)
                        <td style="padding:1px 0.5px; border:1px solid #ddd; text-align:center; vertical-align:middle; word-wrap:break-word; white-space:normal; overflow:visible; font-size:5.5px; line-height:1.0;">{{ $cell ?? '' }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:5px; text-align:center; font-size:6px; color:#666; border-top:1px solid #ddd; padding-top:3px;">
        <p>تم إنشاء هذا التقرير في {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>إجمالي السجلات: {{ count($rows) }}</p>
    </div>
</div>