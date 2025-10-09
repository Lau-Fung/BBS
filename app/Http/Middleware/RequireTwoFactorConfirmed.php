<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorConfirmed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        // If user has not enabled 2FA, or enabled but not confirmed, force them to security page
        $needs2FA = empty($user->two_factor_secret) || is_null($user->two_factor_confirmed_at);

        // Allow Fortify 2FA routes and security page while enforcing
        if ($needs2FA) {
            $allowed = [
                'profile.security',
                'two-factor.login',
                'login',
                'logout',
            ];

            if ($request->route() && in_array($request->route()->getName(), $allowed, true)) {
                return $next($request);
            }

            // Allow POST to enable/confirm 2FA endpoints
            $uri = $request->path();
            if (str_starts_with($uri, 'user/two-factor')) {
                return $next($request);
            }

            return redirect()->route('profile.security')
                ->withErrors(['two_factor' => __('messages.security.mandatory_notice')]);
        }

        return $next($request);
    }
}


