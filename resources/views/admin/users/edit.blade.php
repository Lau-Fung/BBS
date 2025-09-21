<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User') }} — {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                {{-- Flash --}}
                @if(session('status'))
                    <div class="mb-4 text-green-700 bg-green-100 border border-green-300 rounded px-3 py-2">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Errors --}}
                @if ($errors->any())
                    <div class="mb-4 text-red-700 bg-red-100 border border-red-300 rounded px-3 py-2">
                        <ul class="list-disc ms-5">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('Name') }}</label>
                        <input name="name" type="text"
                               value="{{ old('name', $user->name) }}"
                               class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 @error('name') border-red-500 @enderror"
                               required>
                        @error('name') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email (Admin can change, others read-only) --}}
                    <div>
                        <label class="block text-sm font-medium mb-1">{{ __('Email') }}</label>
                        <input name="email" type="email"
                               value="{{ old('email', $user->email) }}"
                               @unless(auth()->user()->hasRole('Admin')) readonly class="w-full rounded border-gray-300 bg-gray-100 text-gray-600" @else class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 @error('email') border-red-500 @enderror" @endunless
                               required>
                        @unless(auth()->user()->hasRole('Admin'))
                            <p class="text-xs text-gray-500 mt-1">{{ __('Only administrators can change the email address.') }}</p>
                        @endunless
                        @error('email') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">
                                {{ __('New Password') }} <span class="text-gray-500">({{ __('leave blank to keep') }})</span>
                            </label>
                            <input name="password" type="password"
                                   class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100 @error('password') border-red-500 @enderror">
                            @error('password') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-1">{{ __('Confirm Password') }}</label>
                            <input name="password_confirmation" type="password"
                                   class="w-full rounded border-gray-300 dark:bg-gray-900 dark:text-gray-100">
                        </div>
                    </div>

                    {{-- Roles (Admins only) --}}
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium mb-2">{{ __('Roles') }}</label>

                        <div class="flex flex-wrap gap-4">
                            @foreach($roles as $role)
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                                           @checked($user->hasRole($role->name))
                                           @unless(auth()->user()->hasRole('Admin')) disabled @endunless
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span>{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('roles.*')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                        @unless(auth()->user()->hasRole('Admin'))
                            <p class="text-xs text-gray-500 mt-1">{{ __('Only administrators can modify roles.') }}</p>
                        @endunless
                    </div>

                    <div>
                        <x-primary-button>{{ __('Save') }}</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
