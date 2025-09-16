@php
    /** @var \App\Models\Client $client */
    /** @var array $columns */   // key => Arabic label
    /** @var \Illuminate\Support\Collection $rows */
@endphp
<table>
    <thead>
        <tr>
            <th colspan="{{ count($columns) }}" style="font-weight:bold;font-size:14px;">
                {{ $client->name }} — {{ __('Advanced Layout') }}
            </th>
        </tr>
        <tr>
            @foreach($columns as $key => $label)
                <th>{{ $label }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $r)
            <tr>
                @foreach(array_keys($columns) as $key)
                    <td>{{ $r[$key] }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
