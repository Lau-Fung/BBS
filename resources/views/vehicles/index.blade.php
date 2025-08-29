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
    <x-slot name="header"><h2 class="text-xl font-semibold">Vehicles</h2></x-slot>

    <form method="GET" class="grid gap-3 md:grid-cols-6 bg-white p-4 rounded-lg shadow mb-4">
        <input class="input" type="text" name="filter[q]" value="{{ request('filter.q') }}" placeholder="Search plate / CRM / notes">
        <select class="input" name="filter[status]">
            <option value="">Status</option>
            @foreach($statuses as $s)
            <option value="{{ $s }}" @selected(request('filter.status')===$s)>{{ $s }}</option>
            @endforeach
        </select>
        <input class="input" type="number" name="filter[capacity_min]" value="{{ request('filter.capacity_min') }}" placeholder="Capacity ≥">
        <input class="input" type="number" name="filter[capacity_max]" value="{{ request('filter.capacity_max') }}" placeholder="Capacity ≤">
        <select class="input" name="filter[trashed]">
            <option value="">All</option>
            <option value="with" @selected(request('filter.trashed')==='with')>With deleted</option>
            <option value="only" @selected(request('filter.trashed')==='only')>Only deleted</option>
        </select>
        <div class="md:col-span-6">
            <button class="btn-primary">Filter</button>
            <a href="{{ route('vehicles.index') }}" class="btn">Reset</a>
            <a href="{{ route('vehicles.create') }}" class="btn-success float-right">+ New</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2">{!! sort_link('plate','Plate') !!}</th>
                    <th class="px-3 py-2">{!! sort_link('tank_capacity_liters','Capacity (L)') !!}</th>
                    <th class="px-3 py-2">{!! sort_link('status','Status') !!}</th>
                    <th class="px-3 py-2">CRM</th>
                    <th class="px-3 py-2">Supervisor</th>
                    <th class="px-3 py-2">{!! sort_link('created_at','Created') !!}</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($vehicles as $v)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $v->plate }}</td>
                    <td class="px-3 py-2">{{ $v->tank_capacity_liters }}</td>
                    <td class="px-3 py-2">{{ $v->status }}</td>
                    <td class="px-3 py-2">{{ $v->crm_no }}</td>
                    <td class="px-3 py-2">{{ $v->supervisor?->name }}</td>
                    <td class="px-3 py-2">{{ $v->created_at->format('Y-m-d') }}</td>
                    <td class="px-3 py-2 text-right">
                        <a class="text-blue-600" href="{{ route('vehicles.edit',$v) }}">Edit</a>
                        @if($v->trashed())
                            <form action="{{ route('vehicles.restore',$v->id) }}" method="POST" class="inline">@csrf
                                <button class="text-amber-600 ml-2">Restore</button>
                            </form>
                        @else
                            <form action="{{ route('vehicles.destroy',$v) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 ml-2">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="px-3 py-4 text-center text-gray-500">No records.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $vehicles->links() }}</div>
</x-app-layout>
