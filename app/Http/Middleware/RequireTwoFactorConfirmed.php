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

        // Enforce 2FA: allow browsing (GET/HEAD) everywhere, block state changes
        if ($needs2FA) {
            // Allow all read-only navigation so users can reach Security page and view content
            if (in_array($request->method(), ['GET', 'HEAD'], true)) {
                return $next($request);
            }

            // Always allow auth + 2FA endpoints
            $allowed = [
                'profile.security',
                'two-factor.login',
                'login',
                'logout',
            ];
            // Temporarily allow high-priority actions while 2FA rollout is in progress
            $temporarilyAllowedActions = [
                // Imports
                'imports.assignments.preview',
                'imports.assignments.confirm',
                // Deleted (Recycle Bin)
                'deleted.clients.restore',
                'deleted.rows.restore',
                'deleted.rows.force',
                'deleted.clients.force',
                // Clients destructive
                'clients.destroy',
            ];
            $allowed = array_merge($allowed, $temporarilyAllowedActions);
            if ($request->route() && in_array($request->route()->getName(), $allowed, true)) {
                return $next($request);
            }

            $uri = $request->path();
            if (str_starts_with($uri, 'user/two-factor')) {
                return $next($request);
            }

            // Block all state-changing actions until 2FA is confirmed
            abort(403, __('messages.security.mandatory_notice'));
        }

        return $next($request);
    }
}


