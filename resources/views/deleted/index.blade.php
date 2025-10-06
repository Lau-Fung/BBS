<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('messages.common.deleted') ?? 'Deleted' }}</h2>
    </x-slot>

    <div class="bg-white shadow-sm rounded p-4">
        <h4 class="mb-3">{{ __('messages.clients.title') }} ({{ __('messages.common.deleted') ?? 'Deleted' }})</h4>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.clients.name') ?? 'Name' }}</th>
                    <th>{{ __('messages.clients.sector') ?? 'Sector' }}</th>
                    <th>{{ __('messages.common.deleted_at') ?? 'Deleted at' }}</th>
                    <th>{{ __('messages.common.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $i => $c)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->sector }}</td>
                        <td>{{ optional($c->deleted_at)->format('Y-m-d H:i') }}</td>
                        <td class="d-flex gap-2">
                            <form method="post" action="{{ route('deleted.clients.restore',$c->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success">{{ __('messages.common.restore') ?? 'Restore' }}</button>
                            </form>
                            <form method="post" action="{{ route('deleted.clients.force',$c->id) }}" onsubmit="return confirm('{{ __('messages.common.confirm_delete') ?? 'Delete permanently?' }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">{{ __('messages.common.delete') ?? 'Delete' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">{{ __('messages.common.no_results') ?? 'No deleted clients' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white shadow-sm rounded p-4 mt-4">
        <h4 class="mb-3">{{ __('messages.clients.rows') ?? 'Client Rows' }} ({{ __('messages.common.deleted') ?? 'Deleted' }})</h4>
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.clients.name') ?? 'Client' }}</th>
                    <th>IMEI</th>
                    <th>{{ __('messages.clients.plate') ?? 'Plate' }}</th>
                    <th>{{ __('messages.common.deleted_at') ?? 'Deleted at' }}</th>
                    <th>{{ __('messages.common.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rows as $i => $r)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $r->client->name ?? '-' }}</td>
                        <td>{{ $r->imei }}</td>
                        <td>{{ $r->plate }}</td>
                        <td>{{ optional($r->deleted_at)->format('Y-m-d H:i') }}</td>
                        <td class="d-flex gap-2">
                            <form method="post" action="{{ route('deleted.rows.restore',$r->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-success">{{ __('messages.common.restore') ?? 'Restore' }}</button>
                            </form>
                            <form method="post" action="{{ route('deleted.rows.force',$r->id) }}" onsubmit="return confirm('{{ __('messages.common.confirm_delete') ?? 'Delete permanently?' }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger">{{ __('messages.common.delete') ?? 'Delete' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ __('messages.common.no_results') ?? 'No deleted rows' }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</x-app-layout>


