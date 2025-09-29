<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Spatie\Activitylog\Models\Activity;

class LogAuthenticationActivity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        match (true) {
            $event instanceof Login => $this->logLogin($event),
            $event instanceof Logout => $this->logLogout($event),
            $event instanceof Failed => $this->logFailedLogin($event),
            default => null,
        };
    }

    protected function logLogin(Login $event): void
    {
        activity()
            ->event('login')
            ->causedBy($event->user->id)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'login_time' => now(),
            ])
            ->log('User logged in');
    }

    protected function logLogout(Logout $event): void
    {
        activity()
            ->event('logout')
            ->causedBy($event->user->id)
            ->withProperties([
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'logout_time' => now(),
            ])
            ->log('User logged out');
    }

    protected function logFailedLogin(Failed $event): void
    {
        activity()
            ->event('failed_login')
            ->withProperties([
                'email' => $event->credentials['email'] ?? 'unknown',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'attempt_time' => now(),
            ])
            ->log('Failed login attempt');
    }
}
