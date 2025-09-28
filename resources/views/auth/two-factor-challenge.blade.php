<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.security.two_factor_challenge') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4 text-sm text-gray-600">
                        {{ __('messages.security.two_factor_challenge_description') }}
                    </div>

                    <form method="POST" action="{{ route('two-factor.login') }}">
                        @csrf

                        <div class="mt-4">
                            <x-input-label for="code" :value="__('messages.security.fields_code')" />
                            <x-text-input id="code" 
                                          class="block mt-1 w-full" 
                                          type="text" 
                                          inputmode="numeric" 
                                          name="code" 
                                          autofocus 
                                          autocomplete="one-time-code" />
                            <x-input-error :messages="$errors->get('code')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" 
                               href="{{ route('login') }}">
                                {{ __('messages.common.back') }}
                            </a>

                            <x-primary-button class="ml-3">
                                {{ __('messages.security.verify') }}
                            </x-primary-button>
                        </div>
                    </form>

                    @if (Route::has('two-factor.recovery'))
                        <div class="mt-4">
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" 
                               href="{{ route('two-factor.recovery') }}">
                                {{ __('messages.security.use_recovery_code') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
