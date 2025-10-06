<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Events\TwoFactorAuthenticationConfirmed;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\{Assignment, Device, Vehicle, Sim, Sensor};
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Cell::setValueBinder(new StringValueBinder());
        Password::defaults(function () {
            return Password::min(12)->mixedCase()->numbers()->symbols()->uncompromised();
        });
        Relation::morphMap([
            'assignment' => Assignment::class,
            'device'     => Device::class,
            'vehicle'    => Vehicle::class,
            'sim'        => Sim::class,
            'sensor'     => Sensor::class,
        ]);

        // Apply locale from session for Arabic/English switch
        $locale = session('locale');
        if (in_array($locale, ['ar','en'])) {
            App::setLocale($locale);
        }

        // Log Fortify 2FA events for troubleshooting
        Event::listen(TwoFactorAuthenticationChallenged::class, function ($event) {
            try {
                Log::info('2FA challenged', [
                    'user_id' => $event->user?->id,
                    'email'   => $event->user?->email,
                    'ip'      => request()->ip(),
                ]);
            } catch (\Throwable $e) {
                // ignore
            }
        });

        Event::listen(TwoFactorAuthenticationConfirmed::class, function ($event) {
            try {
                Log::info('2FA confirmed', [
                    'user_id' => $event->user?->id,
                    'email'   => $event->user?->email,
                    'at'      => now()->toDateTimeString(),
                ]);
            } catch (\Throwable $e) {
                // ignore
            }
        });
    }
}
