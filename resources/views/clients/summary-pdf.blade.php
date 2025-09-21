<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Clients Summary</title>
    <style>
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f5f5f5; text-align: left; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <h3 style="margin:0 0 10px;">Clients Summary @if($q) — filter: “{{ $q }}” @endif</h3>
    <table>
        <thead>
        <tr>
            <th>#</th>
            <th>Company</th>
            <th>Sector</th>
            <th class="right">Vehicles</th>
            <th class="right">Devices (active)</th>
            <th>Devices by model</th>
        </tr>
        </thead>
        <tbody>
        @foreach($clients as $i => $c)
            <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ $c->name }}</td>
                <td>{{ $c->sector }}</td>
                <td class="right">{{ $c->vehicles_count }}</td>
                <td class="right">{{ $c->total_devices }}</td>
                <td>
                    {{ collect($c->models)->map(fn($m) => ($m['model'] ?? 'UNKNOWN').': '.($m['count'] ?? 0))->implode(', ') }}
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>
