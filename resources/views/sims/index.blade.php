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
    <x-slot name="header"><h2 class="text-xl font-semibold">SIMs</h2></x-slot>

    <form method="GET" class="grid gap-3 md:grid-cols-6 bg-white p-4 rounded-lg shadow mb-4">
        <input class="input" type="text" name="filter[q]" value="{{ request('filter.q') }}" placeholder="Search MSISDN / Serial">
        <select class="input" name="filter[carrier_id]">
            <option value="">Carrier</option>
            @foreach($carriers as $c)
            <option value="{{ $c->id }}" @selected(request('filter.carrier_id')==$c->id)>{{ $c->name }}</option>
            @endforeach
        </select>
        <input class="input" type="date" name="filter[expiry_from]" value="{{ request('filter.expiry_from') }}">
        <input class="input" type="date" name="filter[expiry_to]" value="{{ request('filter.expiry_to') }}">
        <select class="input" name="filter[is_recharged]">
            <option value="">Recharged?</option>
            <option value="1" @selected(request('filter.is_recharged')==='1')>Yes</option>
            <option value="0" @selected(request('filter.is_recharged')==='0')>No</option>
        </select>
        <div class="md:col-span-6">
            <button class="btn-primary">Filter</button>
            <a href="{{ route('sims.index') }}" class="btn">Reset</a>
            <a href="{{ route('sims.create') }}" class="btn-success float-right">+ New</a>
        </div>
    </form>

    <div class="bg-white rounded-lg shadow overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2">{!! sort_link('msisdn','MSISDN') !!}</th>
                    <th class="px-3 py-2">Carrier</th>
                    <th class="px-3 py-2">{!! sort_link('plan_expiry_at','Expiry') !!}</th>
                    <th class="px-3 py-2">{!! sort_link('is_active','Active') !!}</th>
                    <th class="px-3 py-2">Recharged</th>
                    <th class="px-3 py-2 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
            @forelse($sims as $s)
                <tr class="border-t">
                    <td class="px-3 py-2">{{ $s->msisdn }}</td>
                    <td class="px-3 py-2">{{ $s->carrier?->name }}</td>
                    <td class="px-3 py-2">{{ optional($s->plan_expiry_at)->format('Y-m-d') }}</td>
                    <td class="px-3 py-2">{{ $s->is_active ? 'Yes' : 'No' }}</td>
                    <td class="px-3 py-2">{{ $s->is_recharged ? 'Yes' : 'No' }}</td>
                    <td class="px-3 py-2 text-right">
                        <a class="text-blue-600" href="{{ route('sims.edit',$s) }}">Edit</a>
                        @if($s->trashed())
                            <form action="{{ route('sims.restore',$s->id) }}" method="POST" class="inline">@csrf
                                <button class="text-amber-600 ml-2">Restore</button>
                            </form>
                        @else
                            <form action="{{ route('sims.destroy',$s) }}" method="POST" class="inline" onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 ml-2">Delete</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">No records.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $sims->links() }}</div>
</x-app-layout>
