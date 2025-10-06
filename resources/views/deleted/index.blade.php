<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">{{ __('messages.common.deleted') ?? 'Deleted' }}</h2>
    </x-slot>

    <div class="card shadow-sm mb-4">
        <div class="card-header fw-normal p-3">{{ __('messages.clients.title') }} ({{ __('messages.common.deleted') ?? 'Deleted' }})</div>
        <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.clients.name') ?? 'Name' }}</th>
                    <th>{{ __('messages.clients.sector') ?? 'Sector' }}</th>
                    <th>{{ __('messages.common.deleted_at') ?? 'Deleted at' }}</th>
                    <th class="text-center">{{ __('messages.common.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $i => $c)
                    <tr>
                        <td>{{ $i+1 }}</td>
                        <td>{{ $c->name }}</td>
                        <td>{{ $c->sector }}</td>
                        <td>{{ optional($c->deleted_at)->format('Y-m-d H:i') }}</td>
                        <td class="d-flex gap-2 justify-content-center">
                            <form method="post" action="{{ route('deleted.clients.restore',$c->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor" class="me-1" aria-hidden="true" style="vertical-align:-0.125em;display:inline-block;">
                                        <path d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 1 1 .894-.448A4 4 0 1 0 8 4H5.5a.5.5 0 0 1 0-1H8a.5.5 0 0 1 .5.5v2.5a.5.5 0 0 1-1 0V3z"/>
                                    </svg>
                                    {{ __('messages.common.restore') ?? 'Restore' }}
                                </button>
                            </form>
                            <form method="post" action="{{ route('deleted.clients.force',$c->id) }}" onsubmit="return confirm('{{ __('messages.common.confirm_delete') ?? 'Delete permanently?' }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor" class="me-1" aria-hidden="true" style="vertical-align:-0.125em;display:inline-block;">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V6H6v7.5a.5.5 0 0 1-1 0v-8z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2h3a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1h3a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118z"/>
                                    </svg>
                                    {{ __('messages.common.delete') ?? 'Delete' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">{{ __('messages.common.no_results') ?? 'No deleted clients' }}</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header fw-normal p-3">{{ __('messages.clients.rows') ?? 'Client Rows' }} ({{ __('messages.common.deleted') ?? 'Deleted' }})</div>
        <div class="card-body p-0">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.clients.name') ?? 'Client' }}</th>
                    <th>IMEI</th>
                    <th>{{ __('messages.clients.plate') ?? 'Plate' }}</th>
                    <th>{{ __('messages.common.deleted_at') ?? 'Deleted at' }}</th>
                    <th class="text-center">{{ __('messages.common.actions') ?? 'Actions' }}</th>
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
                        <td class="d-flex gap-2 justify-content-center">
                            <form method="post" action="{{ route('deleted.rows.restore',$r->id) }}">
                                @csrf
                                <button class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor" class="me-1" aria-hidden="true" style="vertical-align:-0.125em;display:inline-block;">
                                        <path d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 1 1 .894-.448A4 4 0 1 0 8 4H5.5a.5.5 0 0 1 0-1H8a.5.5 0 0 1 .5.5v2.5a.5.5 0 0 1-1 0V3z"/>
                                    </svg>
                                    {{ __('messages.common.restore') ?? 'Restore' }}
                                </button>
                            </form>
                            <form method="post" action="{{ route('deleted.rows.force',$r->id) }}" onsubmit="return confirm('{{ __('messages.common.confirm_delete') ?? 'Delete permanently?' }}')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 16" fill="currentColor" class="me-1" aria-hidden="true" style="vertical-align:-0.125em;display:inline-block;">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-1 0V6H6v7.5a.5.5 0 0 1-1 0v-8z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1 0-2h3a1 1 0 0 1 1-1h3a1 1 0 0 1 1 1h3a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118z"/>
                                    </svg>
                                    {{ __('messages.common.delete') ?? 'Delete' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">{{ __('messages.common.no_results') ?? 'No deleted rows' }}</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</x-app-layout>


