<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}"
      dir="{{ app()->getLocale()==='ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>{{ config('app.name', 'Laravel') }}</title> --}}
    <title>Excel Management</title>

    {{-- Fonts (optional) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Bootstrap CSS (RTL when Arabic) --}}
    @if(app()->getLocale()==='ar')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css">
    @else
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    @endif

    {{-- Your own assets if any --}}
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>

    {{-- Top navigation --}}
    @include('layouts.navigation')

    {{-- Optional page header (slot) --}}
    @isset($header)
        <header class="bg-white border-bottom shadow-sm">
            <div class="container py-3">
                {!! $header !!}
            </div>
        </header>
    @endisset

    {{-- Page content --}}
    <main class="container py-4">
        {{ $slot }}
    </main>

    {{-- Bootstrap JS bundle --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
