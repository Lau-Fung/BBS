<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('messages.users.edit_role') }} — {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                @if(session('status'))
                    <div class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded px-3 py-2">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update',$user) }}">
                    @csrf @method('PUT')

                    <div class="space-y-2">
                        <label class="block text-sm font-medium">{{ __('messages.table.roles') }}</label>
                        <div class="flex flex-wrap gap-4">
                            @foreach($roles as $role)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           @checked($user->hasRole($role->name))
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span>{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles.*')
                            <div class="text-red-600 text-sm">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-6">
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
