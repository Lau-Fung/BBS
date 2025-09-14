<table>
    <thead>
    <tr>
        <th colspan="10" style="font-weight:bold;font-size:14px;">
            Client: {{ $client->name }}
            @if($client->sector) | Sector: {{ $client->sector }} @endif
            @if($client->subscription_type) | Subscription: {{ strtoupper($client->subscription_type) }} @endif
        </th>
    </tr>
    <tr>
        <th>#</th>
        <th>IMEI</th>
        <th>Model</th>
        <th>Vehicle Plate</th>
        <th>SIM Serial</th>
        <th>MSISDN</th>
        <th>Installed On</th>
        <th>Removed On</th>
        <th>Installed?</th>
        <th>Notes</th>
    </tr>
    </thead>
    <tbody>
    @foreach($assignments as $i => $a)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $a->device?->imei }}</td>
            <td>{{ $a->device?->deviceModel?->name }}</td>
            <td>{{ $a->vehicle?->plate }}</td>
            <td>{{ $a->sim?->sim_serial }}</td>
            <td>{{ $a->sim?->msisdn }}</td>
            <td>{{ $a->installed_on }}</td>
            <td>{{ $a->removed_on }}</td>
            <td>{{ $a->is_installed ? 'Yes' : 'No' }}</td>
            <td>{{ $a->install_note }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
