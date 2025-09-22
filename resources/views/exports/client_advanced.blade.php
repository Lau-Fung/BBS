<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $client->name }} - Export</title>
    <style>
        @page { size: A3 landscape; margin: 10mm; }

        @font-face {
        font-family: 'Amiri';
        src: url('{{ public_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
        font-weight: normal; font-style: normal;
        }

        body { font-family: 'Amiri', 'DejaVu Sans', sans-serif; }

        /* If the whole document is Arabic, keep RTL globally. Otherwise apply
        only to the Arabic columns/cells. */
        .rtl { direction: rtl; unicode-bidi: embed; }

        /* table helpers so it paginates correctly */
        table { width:100%; border-collapse: collapse; table-layout: fixed; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        th, td { padding: 4px 6px; font-size: 11px; word-break: break-word; }
        tr { page-break-inside: avoid; }
    </style>
</head>
<body class="rtl">
    <h2>Client: {{ $client->name }}</h2>
    <p>Vehicles: {{ $client->vehicles()->count() }}</p>
    <p>Active Devices: {{ $rows->count() }}</p>

    <table>
        <thead>
            <tr>
                @foreach($headings as $h)
                    <th>{{ $h }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($row as $col)
                        <td>{{ $col }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
