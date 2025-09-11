{{-- resources/views/profile/security.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-weight-bold">
            {{ __('Two-Factor Authentication') }}
        </h2>
    </x-slot>

    <div class="container mt-4" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

        {{-- flash messages --}}
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                </ul>
            </div>
        @endif

        {{-- Not enabled yet --}}
        @if (! auth()->user()->two_factor_secret)
            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                @csrf
                <button class="btn btn-primary">{{ __('Enable 2FA') }}</button>
            </form>

        @else
            {{-- Enabled --}}
            <div class="alert alert-success">{{ __('2FA is enabled.') }}</div>

            {{-- QR code (INLINE SVG, not <img>) --}}
            <div class="mb-4">
                <h5 class="mb-2">{{ __('Scan this QR code in Google Authenticator') }}</h5>
                <div class="p-3 border rounded bg-white d-inline-block" style="max-width: 280px;">
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                </div>
            </div>

            {{-- Recovery codes --}}
            <div class="mb-4">
                <h5 class="mb-2">{{ __('Recovery Codes') }}</h5>

                @php
                    $codes = null;
                    try {
                        $codes = json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true);
                    } catch (\Throwable $e) {
                        $codes = null;
                    }
                @endphp

                @if (is_array($codes) && count($codes))
                    <pre class="bg-light p-3 rounded" style="white-space: pre-line;">
{{ implode(PHP_EOL, $codes) }}
                    </pre>
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
