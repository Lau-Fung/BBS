<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">{{ __('messages.users.new_user') }}</h2>
    </x-slot>

    <div class="container py-3">
        <form method="POST" action="{{ route('admin.users.store') }}" class="card p-4">
            @csrf

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.table.name') }}</label>
                    <input name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.table.email') }}</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required>
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        {{ __('messages.users.password') }}
                    </label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                    @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">{{ __('messages.table.roles') }}</label>
                    <select name="roles[]" class="form-select" multiple>
                        @foreach($roles as $id => $label)
                            <option value="{{ $id }}" @selected(collect(old('roles'))->contains($id))>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">{{ __('messages.common.cancel') }}</a>
                <button class="btn btn-primary">{{ __('messages.common.create') }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
