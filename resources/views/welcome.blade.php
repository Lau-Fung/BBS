<!DOCTYPE html>
<html lang="{{ str_replace('_','-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Excel Management System</title>

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background: url('{{ asset('images/background.jpg') }}') no-repeat center center fixed;
            background-size: cover;
        }
        .overlay {
            background: rgba(0,0,0,0.55); /* dark overlay */
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: #fff;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="overlay">
        <h1 class="display-4 fw-bold">{{ __('messages.excel_management_system') }}</h1>
        <p class="lead">{{ __('messages.welcome') }}</p>

        <div class="mt-4">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-success btn-lg">
                        <i class="bi bi-speedometer2"></i> {{ __('messages.auth.dashboard') }}
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg me-2">
                        <i class="bi bi-box-arrow-in-right"></i> {{ __('messages.auth.login') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-person-plus"></i> {{ __('messages.auth.register') }}
                        </a>
                    @endif
                @endauth
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
