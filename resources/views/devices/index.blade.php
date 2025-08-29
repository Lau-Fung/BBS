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
    <x-slot name="header"><h2 class="text-xl font-semibold">Devices</h2></x-slot>

    <form method="GET" class="grid gap-3 md:grid-cols-5 bg-white p-4 rounded-lg shadow mb-4">
        <input class="input" type="text" name="filter[q]" value="{{ request('filter.q') }}" placeholder="Search IMEI">
        <select class="input" name="filter[device_model_id]">
            <option value="">Model</option>
            @foreach($deviceModels as $m)
            <option value="{{ $m->id }}" @selected(request('filter.device_model_id')==$m->id)>{{ $m->name }}</option>
            @endforeach
        </select>
        <select class="input" name="filter[is_active]">
            <option value="">Active?</option>
            <option value="1" @selected(request('filter.is_active')==='1')>Yes</option>
            <option value="0" @selected(request('filter.is_active')==='0')>No</option>
        </select>
        <div class="md:col-span-2">
            <button class="btn-primary">Filter</button>
            <a href="{{ route('devices.index') }}" class="btn">Reset</a>
            <a href="{{ route('devices.create') }}" class="btn-success float-right">+ New</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2">{!! sort_link('imei','IMEI') !!}</th>
                    <th class="px-3 py-2">Model</th>
                    <th class="px-3 py-2">Firmware</th>
                    <th class="px-3 py-2">{!! sort_link('is_active','Active') !!}</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($devices as $d)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $d->imei }}</td>
                    <td class="px-3 py-2">{{ $d->model?->name }}</td>
                    <td class="px-3 py-2">{{ $d->firmware }}</td>
                    <td class="px-3 py-2">{{ $d->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-3 py-2 text-right">
                        <a class="text-blue-600" href="{{ route('devices.edit',$d) }}">Edit</a>
                        @if($d->trashed())
                            <form action="{{ route('devices.restore',$d->id) }}" method="POST" class="inline">@csrf
                                <button class="text-amber-600 ml-2">Restore</button>
                            </form>
                        @else
                            <form action="{{ route('devices.destroy',$d) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 ml-2">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">No records.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $devices->links() }}</div>
</x-app-layout>

