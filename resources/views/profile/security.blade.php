@extends('layouts.app')

@section('content')
<div class="container" dir="{{ app()->getLocale()==='ar'?'rtl':'ltr' }}">
    <h1 class="mb-4">{{ __('Two-Factor Authentication') }}</h1>

    @if (! auth()->user()->two_factor_secret)
        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
            @csrf
            <button class="btn btn-primary">{{ __('Enable 2FA') }}</button>
        </form>
    @else
        <p class="text-success">{{ __('2FA is enabled.') }}</p>

        <div class="mb-3">
            <h5>{{ __('Scan this QR code in Google Authenticator') }}</h5>
            <img src="{{ url('/user/two-factor-qr-code') }}" alt="2FA QR">
        </div>

        <div class="mb-3">
            <h5>{{ __('Recovery Codes') }}</h5>
            <pre>@foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code){{ $code."\n" }}@endforeach</pre>
            <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}">
                @csrf
                <button class="btn btn-outline-secondary">{{ __('Regenerate Codes') }}</button>
            </form>
        </div>

        <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
            @csrf @method('DELETE')
            <button class="btn btn-danger">{{ __('Disable 2FA') }}</button>
        </form>
    @endif
</div>
@endsection
