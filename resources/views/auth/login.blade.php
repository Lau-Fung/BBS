<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- Load v3 api.js with site key --}}
    <script src="https://www.google.com/recaptcha/api.js?render={{ config('captcha.sitekey') }}&hl={{ app()->getLocale() }}"></script>

    <form id="login-form" method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full"
                          type="email" name="email" :value="old('email')"
                          required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                          type="password" name="password"
                          required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        {{-- Hidden field to hold the v3 token --}}
        <input type="hidden" name="g-recaptcha-response" id="recaptcha_token">

        {{-- Show captcha validation error --}}
        @error('g-recaptcha-response')
            <div class="text-danger small mt-2">{{ $message }}</div>
        @enderror

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900"
                   href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3" id="login-btn">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        const form = document.getElementById('login-form');
        const btn  = document.getElementById('login-btn');

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            grecaptcha.ready(function () {
                grecaptcha.execute('{{ config('captcha.sitekey') }}', {action: 'login'})
                    .then(function (token) {
                        document.getElementById('recaptcha_token').value = token;
                        form.submit();
                    })
                    .catch(function (err) {
                        console.error('reCAPTCHA error:', err);
                        // Optionally show a friendly message to the user
                    });
            });
        });
    </script>
</x-guest-layout>
