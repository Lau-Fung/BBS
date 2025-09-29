<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('messages.security.title') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash messages --}}
            @if (session('status'))
                <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('status') }}
                </div>
            @endif
            
            @if ($errors->any())
                <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-red-800">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg" style="border: 1px solid #e5e7eb;">
                <div class="p-8 text-gray-900">
                    
                    {{-- Two-Factor Authentication Section --}}
                    <div class="mb-8 m-3">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">
                                {{ __('messages.security.two_factor_authentication') }}
                            </h3>
                            @if (auth()->user()->two_factor_secret)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold text-white shadow-lg"
                                      style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                                    {{ __('messages.security.enabled') }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold text-white shadow-lg"
                                      style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);">
                                    {{ __('messages.security.disabled') }}
                                </span>
                            @endif
                        </div>

                        @if (! auth()->user()->two_factor_secret)
                            {{-- Enable 2FA --}}
                            <div class="rounded-lg p-6 mb-6 shadow-lg" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border: 1px solid #3b82f6;">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-blue-800">
                                            {{ __('messages.security.enable_2fa_title') }}
                                        </h4>
                                        <div class="mt-2 text-sm text-blue-700">
                                            <p>{{ __('messages.security.enable_2fa_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <form method="POST" action="{{ url('/user/two-factor-authentication') }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center px-6 py-3 font-semibold text-sm rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                                        style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);"
                                        onmouseover="this.style.background='linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)'"
                                        onmouseout="this.style.background='linear-gradient(135deg, #3b82f6 0%, #2563eb 100%)'">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                    </svg>
                                    {{ __('messages.security.actions_enable') }}
                                </button>
                            </form>

                        @else
                            {{-- 2FA is enabled --}}
                            <div class="rounded-lg p-6 mb-6 shadow-lg" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); border: 1px solid #10b981;">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-medium text-green-800">
                                            {{ __('messages.security.status_enabled') }}
                                        </h4>
                                        <div class="mt-2 text-sm text-green-700">
                                            <p>{{ __('messages.security.status_enabled_description') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- QR Code Section --}}
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">
                                    {{ __('messages.security.qr_title') }}
                                </h4>
                                <div class="bg-white border border-gray-200 rounded-lg p-4 inline-block">
                                    <div class="text-center">
                                        {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                        <p class="mt-2 text-sm text-gray-600">
                                            {{ __('messages.security.qr_help') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Recovery Codes Section --}}
                            <div class="mb-6">
                                <h4 class="text-lg font-medium text-gray-900 mb-3">
                                    {{ __('messages.security.recovery_code') }}
                                </h4>
                                
                                @php
                                    $codes = null;
                                    try {
                                        $codes = json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true);
                                    } catch (\Throwable $e) {
                                        $codes = null;
                                    }
                                @endphp

                                @if (is_array($codes) && count($codes))
                                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <p class="text-sm font-medium text-gray-700">
                                                {{ __('messages.security.recovery_codes_description') }}
                                            </p>
                                            <button onclick="copyRecoveryCodes()" class="text-sm text-blue-600 hover:text-blue-800">
                                                {{ __('messages.security.copy_codes') }}
                                            </button>
                                        </div>
                                        <pre id="recovery-codes" class="text-xs text-gray-600 whitespace-pre-line font-mono">{{ implode(PHP_EOL, $codes) }}</pre>
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                        <p class="text-sm text-yellow-800">
                                            {{ __('messages.security.recovery_code_no') }}
                                        </p>
                                    </div>
                                @endif

                                <form method="POST" action="{{ url('/user/two-factor-recovery-codes') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                                            style="background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);"
                                            onmouseover="this.style.background='linear-gradient(135deg, #4b5563 0%, #374151 100%)'"
                                            onmouseout="this.style.background='linear-gradient(135deg, #6b7280 0%, #4b5563 100%)'">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                        </svg>
                                        {{ __('messages.security.recovery_regenerate') }}
                                    </button>
                                </form>
                            </div>

                            {{-- Disable 2FA Section --}}
                            <div class="border-t border-gray-200 pt-6">
                                <h4 class="text-lg font-medium text-red-900 mb-4">
                                    {{ __('messages.security.danger_zone') }}
                                </h4>
                                <div class="rounded-lg p-6 shadow-lg" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border: 1px solid #ef4444;">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h4 class="text-sm font-medium text-red-800">
                                                {{ __('messages.security.disable_2fa_title') }}
                                            </h4>
                                            <div class="mt-2 text-sm text-red-700">
                                                <p>{{ __('messages.security.disable_2fa_description') }}</p>
                                            </div>
                                            <div class="mt-4">
                                                <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('{{ __('messages.security.confirm_disable') }}')"
                                                            class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 text-white shadow-lg hover:shadow-xl"
                                                            style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);"
                                                            onmouseover="this.style.background='linear-gradient(135deg, #dc2626 0%, #b91c1c 100%)'"
                                                            onmouseout="this.style.background='linear-gradient(135deg, #ef4444 0%, #dc2626 100%)'">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        {{ __('messages.security.actions_disable') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyRecoveryCodes() {
            const codes = document.getElementById('recovery-codes').textContent;
            navigator.clipboard.writeText(codes).then(function() {
                // Show success message
                const button = event.target;
                const originalText = button.textContent;
                button.textContent = '{{ __("messages.security.copied") }}';
                button.classList.add('text-green-600');
                
                setTimeout(function() {
                    button.textContent = originalText;
                    button.classList.remove('text-green-600');
                }, 2000);
            });
        }
    </script>
</x-app-layout>
