<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        RateLimiter::for('saqr-test-ride', fn (Request $request) => [
            Limit::perMinute(3)->by('ride-minute:' . $request->ip()),
            Limit::perHour(10)->by('ride-hour:' . $request->ip()),
        ]);

        RateLimiter::for('saqr-contact', fn (Request $request) => [
            Limit::perMinute(3)->by('contact-minute:' . $request->ip()),
            Limit::perHour(10)->by('contact-hour:' . $request->ip()),
        ]);
    }
}
