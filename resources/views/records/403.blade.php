<x-guest-layout>
    <div class="text-center space-y-3 py-16">
        <h1 class="text-3xl font-bold">403 — {{ __('Forbidden') }}</h1>
        <p>{{ __('You do not have permission to access this page.') }}</p>
        <a href="{{ url()->previous() }}" class="underline">{{ __('Go back') }}</a>
    </div>
</x-guest-layout>
