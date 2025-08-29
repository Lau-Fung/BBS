@php
    function sort_link($field, $label) {
        $current = request('sort');
        $dir = $current === $field ? "-$field" : $field;
        $params = array_merge(request()->query(), ['sort' => $dir]);
        $url = url()->current().'?'.http_build_query($params);
        return "<a href=\"$url\" class=\"hover:underline\">$label</a>";
    }
@endphp

<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold">Sensors</h2></x-slot>

    <form method="GET" class="grid gap-3 md:grid-cols-5 bg-white p-4 rounded-lg shadow mb-4">
        <input class="input" type="text" name="filter[q]" value="{{ request('filter.q') }}" placeholder="Search Serial/BT ID / Notes">
        <select class="input" name="filter[sensor_model_id]">
            <option value="">Model</option>
            @foreach($sensorModels as $m)
            <option value="{{ $m->id }}" @selected(request('filter.sensor_model_id')==$m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
        <div class="md:col-span-2">
            <button class="btn-primary">Filter</button>
            <a href="{{ route('sensors.index') }}" class="btn">Reset</a>
            <a href="{{ route('sensors.create') }}" class="btn-success float-right">+ New</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2">{!! sort_link('serial_or_bt_id','Serial/BT ID') !!}</th>
                    <th class="px-3 py-2">Model</th>
                    <th class="px-3 py-2">Notes</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sensors as $s)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $s->serial_or_bt_id }}</td>
                    <td class="px-3 py-2">{{ $s->model?->name }}</td>
                    <td class="px-3 py-2">{{ $s->notes }}</td>
                    <td class="px-3 py-2 text-right">
                        <a class="text-blue-600" href="{{ route('sensors.edit',$s) }}">Edit</a>
                        @if($s->trashed())
                            <form action="{{ route('sensors.restore',$s->id) }}" method="POST" class="inline">@csrf
                                <button class="text-amber-600 ml-2">Restore</button>
                            </form>
                        @else
                            <form action="{{ route('sensors.destroy',$s) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 ml-2">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-3 py-4 text-center text-gray-500">No records.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $sensors->links() }}</div>
</x-app-layout>


