<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Validator;
use Anhskohbo\NoCaptcha\NoCaptcha;

use Laravel\Fortify\Contracts\ConfirmPasswordViewResponse;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Use your Breeze Blade views
        Fortify::loginView(fn() => view('auth.login'));
        Fortify::registerView(fn() => view('auth.register'));
        Fortify::requestPasswordResetLinkView(fn() => view('auth.forgot-password'));
        Fortify::resetPasswordView(fn ($req) => view('auth.reset-password', ['request' => $req]));
        Fortify::twoFactorChallengeView(fn() => view('auth.two-factor-challenge'));
        // Add confirm password binding
        $this->app->singleton(ConfirmPasswordViewResponse::class, function () {
            return new class implements ConfirmPasswordViewResponse {
                public function toResponse($request)
                {
                    return response()->view('auth.confirm-password');
                }
            };
        });

        Fortify::authenticateUsing(function (\Illuminate\Http\Request $request) {
            Validator::make($request->all(), [
                'g-recaptcha-response' => ['required','captcha'],
            ])->validate();

            // return null to let Fortify continue with default auth,
            // or handle manual auth here if you prefer.
        });

        // Login rate limit (email + IP)
        RateLimiter::for('login', function (Request $request) {
            $key = mb_strtolower((string)$request->input('email')).'|'.$request->ip();
            return [Limit::perMinute(5)->by($key)];
        });

        // Two-factor rate limit (per pending login id)
        RateLimiter::for('two-factor', function (Request $request) {
            $loginId = (string) $request->session()->get('login.id', 'guest');
            return [Limit::perMinute(10)->by('2fa|'.$loginId)];
        });
    }
}
