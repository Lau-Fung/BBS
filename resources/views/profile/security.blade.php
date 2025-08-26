<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">
            {{ __('Two-Factor Authentication') }}
        </h2>
    </x-slot>

    <div class="container mt-4" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

        @if (! auth()->user()->two_factor_secret)
            {{-- Enable 2FA --}}
            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                @csrf
                <button class="btn btn-primary">{{ __('Enable 2FA') }}</button>
            </form>
        @else
            {{-- 2FA Enabled --}}
            <div class="alert alert-success">{{ __('2FA is enabled.') }}</div>

            {{-- QR Code --}}
            <div class="mb-4">
                <h5>{{ __('Scan this QR code in Google Authenticator') }}</h5>
                <div class="p-3 border rounded bg-white d-inline-block">
                    <img src="{{ route('two-factor.qr-code') }}" alt="2FA QR Code" width="200">
                </div>
            </div>

            {{-- Recovery Codes --}}
            <div class="mb-4">
                <h5>{{ __('Recovery Codes') }}</h5>

                @php
                    $codes = null;
                    try {
                        $codes = json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true);
                    } catch (\Throwable $e) {
                        $codes = null;
                    }
                @endphp

                @if (is_array($codes) && count($codes))
                    <pre class="bg-light p-3 rounded">{{ implode(PHP_EOL, $codes) }}</pre>
                @else
                    <p class="text-muted">{{ __('No recovery codes found.') }}</p>
                @endif

                <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}" class="mt-2">
                    @csrf
                    <button class="btn btn-outline-secondary">{{ __('Regenerate Codes') }}</button>
                </form>
            </div>

            {{-- Disable 2FA --}}
            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger">{{ __('Disable 2FA') }}</button>
            </form>
        @endif

    </div>
</x-app-layout>
