<table>
    <thead>
    <tr>
        <th colspan="7" style="font-weight:bold; font-size:14px;">
            {{ $client->name }} — {{ __('Assets') }}
        </th>
    </tr>
    <tr>
        <th>{{ __('Plate') }}</th>
        <th>{{ __('Device Model') }}</th>
        <th>IMEI</th>
        <th>{{ __('SIM Serial') }}</th>
        <th>{{ __('MSISDN') }}</th>
        <th>{{ __('Installed On') }}</th>
        <th>{{ __('Note') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach($rows as $r)
        <tr>
            <td>{{ $r->plate }}</td>
            <td>{{ $r->device_model }}</td>
            <td>{{ $r->imei }}</td>
            <td>{{ $r->sim_serial }}</td>
            <td>{{ $r->msisdn }}</td>
            <td>{{ $r->installed_on }}</td>
            <td>{{ $r->install_note }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
