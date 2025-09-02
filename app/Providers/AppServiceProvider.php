<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Database\Eloquent\Relations\Relation;
use App\Models\{Assignment, Device, Vehicle, Sim, Sensor};
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

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
    }
}
