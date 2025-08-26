<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class SetLocaleFromSession
{
    public function handle($request, Closure $next)
    {
        $locale = session('locale', config('app.locale'));
        if (! in_array($locale, ['ar','en'])) {
            $locale = config('app.locale');
        }

        App::setLocale($locale);
        // Optional: make Carbon dates translate month/day names
        Carbon::setLocale($locale);

        return $next($request);
    }
}
