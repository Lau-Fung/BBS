<div dir="rtl" style="font-family:'DejaVu Sans', Tahoma, Arial, sans-serif; font-size:11px; line-height:1.4; color:#333;">
    <div style="text-align:center; margin-bottom:25px; border-bottom:3px solid #6366f1; padding-bottom:15px;">
        <h1 style="font-size:20px; margin:0 0 8px 0; color:#1f2937; font-weight:bold;">{{ __('messages.clients.summary_report') }}</h1>
        <p style="font-size:12px; margin:0; color:#6b7280;">Clients Summary Report</p>
        @if($q)
            <p style="font-size:12px; margin:0; color:#6b7280;">فلتر البحث: "{{ $q }}" | Search Filter: "{{ $q }}"</p>
        @endif
    </div>

    <div style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:15px; margin-bottom:20px;">
        <h2 style="margin:0 0 10px 0; color:#374151; font-size:14px; font-weight:bold;">{{ __('messages.clients.general_statistics') }}</h2>
        <div>
            <div style="text-align:center; padding:10px; background-color:#fff; border-radius:6px; border:1px solid #e5e7eb; min-width:120px; display:inline-block; vertical-align:top; width:24%;">
                <div style="font-size:18px; font-weight:bold; color:#6366f1; margin-bottom:5px;">{{ $clients->count() }}</div>
                <div style="font-size:10px; color:#6b7280;">{{ __('messages.clients.total_clients') }}</div>
            </div>
            <div style="text-align:center; padding:10px; background-color:#fff; border-radius:6px; border:1px solid #e5e7eb; min-width:120px; display:inline-block; vertical-align:top; width:24%;">
                <div style="font-size:18px; font-weight:bold; color:#6366f1; margin-bottom:5px;">{{ $clients->sum('vehicles_count') }}</div>
                <div style="font-size:10px; color:#6b7280;">{{ __('messages.clients.total_records') }}</div>
            </div>
            <div style="text-align:center; padding:10px; background-color:#fff; border-radius:6px; border:1px solid #e5e7eb; min-width:120px; display:inline-block; vertical-align:top; width:24%;">
                <div style="font-size:18px; font-weight:bold; color:#6366f1; margin-bottom:5px;">{{ $clients->sum('total_devices') }}</div>
                <div style="font-size:10px; color:#6b7280;">{{ __('messages.clients.total_devices') }}</div>
            </div>
            <div style="text-align:center; padding:10px; background-color:#fff; border-radius:6px; border:1px solid #e5e7eb; min-width:120px; display:inline-block; vertical-align:top; width:24%;">
                <div style="font-size:18px; font-weight:bold; color:#6366f1; margin-bottom:5px;">{{ now()->format('Y-m-d') }}</div>
                <div style="font-size:10px; color:#6b7280;">{{ __('messages.clients.report_date') }}</div>
            </div>
        </div>
    </div>

    @php
        $wrapMixedRtl = function (?string $text): string {
            $t = e($text ?? '');
            $t = preg_replace('/([A-Za-z0-9][A-Za-z0-9._-]*)/u', '<span dir="ltr" style="unicode-bidi:embed;">$1</span>', $t);
            $t = preg_replace('/\s*-\s*/u', '&lrm; - &lrm;', $t);
            return $t;
        };
    @endphp

    <table style="width:100%; border-collapse:collapse; margin-top:10px; background-color:#fff; table-layout:fixed; direction:rtl; unicode-bidi:embed;">
        <thead>
            <tr>
                <th style="background:#6366f1; color:#fff; padding:12px 8px; text-align:center; font-weight:bold; font-size:10px; white-space:nowrap;">#</th>
                <th style="background:#6366f1; color:#fff; padding:12px 8px; text-align:center; font-weight:bold; font-size:10px; white-space:nowrap;" dir="ltr">{{ __('messages.clients.company_name') }}</th>
                <th style="background:#6366f1; color:#fff; padding:12px 8px; text-align:center; font-weight:bold; font-size:10px; white-space:nowrap;" dir="ltr">{{ __('messages.clients.sector') }}</th>
                <th style="background:#6366f1; color:#fff; padding:12px 8px; text-align:center; font-weight:bold; font-size:10px; white-space:nowrap;">{{ __('messages.clients.total_records') }}</th>
                <th style="background:#6366f1; color:#fff; padding:12px 8px; text-align:center; font-weight:bold; font-size:10px; white-space:nowrap;">{{ __('messages.clients.active_devices') }}</th>
                <th style="background:#6366f1; color:#fff; padding:12px 8px; text-align:center; font-weight:bold; font-size:10px; white-space:nowrap;">{{ __('messages.clients.devices_by_model') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $i => $c)
                <tr>
                    <td style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:center; vertical-align:middle; font-size:10px; font-weight:bold; color:#059669;" dir="ltr"><span dir="ltr">{{ $i+1 }}</span></td>
                    @php
                        $LRM = html_entity_decode('&lrm;', ENT_QUOTES, 'UTF-8');
                        $companyOut = '';
                        if (preg_match('/^(.*?)[\s]*-[\s]*(\p{Latin}[\p{Latin}0-9._\-\s]*)$/u', $c->name, $m)) {
                            $arabicPart = e($m[1]);
                            $latinPart  = e($m[2]);
                            $companyOut = '<span dir="ltr" style="unicode-bidi:isolate">'.$latinPart.'</span>'
                                .' - '
                                .'<span dir="rtl" style="unicode-bidi:isolate-override">'.$arabicPart.'</span>';
                        } else {
                            $companyOut = e($c->name);
                        }
                    @endphp
                    <td dir="rtl" style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:right; font-size:10px; font-weight:bold; color:#1f2937; white-space:nowrap;">{!! $companyOut !!}</td>
                    @php $sectorOut = e($c->sector ?? __('messages.clients.not_specified')); @endphp
                    <td dir="rtl" style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:center; font-size:9px; color:#6b7280;">{!! $sectorOut !!}</td>
                    <td style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:center; font-size:10px; font-weight:bold; color:#059669;" dir="ltr"><span dir="ltr">{{ $c->vehicles_count }}</span></td>
                    <td style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:center; font-size:10px; font-weight:bold; color:#059669;" dir="ltr"><span dir="ltr">{{ $c->total_devices }}</span></td>
                    <td style="padding:10px 8px; border-bottom:1px solid #e5e7eb; text-align:right; font-size:9px; color:#4b5563; line-height:1.3;">
                        @if($c->models->isNotEmpty())
                            @foreach($c->models as $model)
                                @php
                                    $modelOut = e($model['model'] ?? 'UNKNOWN');
                                    $modelOut = preg_replace('/([A-Za-z0-9][A-Za-z0-9._\/\-]*)/u', '<span dir="ltr" style="unicode-bidi:embed;">$1</span>', $modelOut);
                                    $modelOut = str_replace('-', $LRM.'-'.$LRM, $modelOut);
                                @endphp
                                <div dir="rtl">{!! $modelOut !!}: {{ $model['count'] ?? 0 }}</div>
                            @endforeach
                        @else
                            <span style="color:#9ca3af;">{{ __('messages.clients.no_data_available') }}</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:30px; text-align:center; font-size:9px; color:#9ca3af; border-top:1px solid #e5e7eb; padding-top:15px;">
        <p>{{ __('messages.clients.generated_on') }} {{ now()->format('Y-m-d H:i:s') }}</p>
        <p>{{ __('messages.clients.total_clients') }}: {{ $clients->count() }}</p>
    </div>
</div>
