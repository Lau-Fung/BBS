<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        // If user has 2FA enabled and confirmed, redirect to Fortify's challenge screen
        $user = \Illuminate\Support\Facades\Auth::user();
        if ($user && !empty($user->two_factor_secret) && !is_null($user->two_factor_confirmed_at)) {
            // Prepare Fortify's expected session values for 2FA challenge
            $remember = $request->boolean('remember');
            \Illuminate\Support\Facades\Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // Start a fresh session to carry the login challenge state
            $request->session()->put('login.id', $user->getKey());
            $request->session()->put('login.remember', $remember);

            return redirect()->route('two-factor.login');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.index', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
