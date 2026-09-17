<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

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
        // Baseline limit applied to every API route via throttle:api
        // (see bootstrap/app.php).
        RateLimiter::for('api', function ($request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Admin login: brute-force protection, keyed by IP + the email being
        // guessed so one attacker can't lock out the real admin's IP either.
        RateLimiter::for('admin-login', function ($request) {
            return Limit::perMinute(5)->by($request->ip().'|'.$request->input('email'));
        });

        // Checkout creates real bookings and calls the NOWPayments API on
        // every hit — cap it well below anything a real customer would do.
        RateLimiter::for('checkout', function ($request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        // NOWPayments' own server calls this; keep it generous but bounded
        // so a spoofed flood of IPN requests can't hammer the database.
        RateLimiter::for('payment-ipn', function ($request) {
            return Limit::perMinute(60)->by($request->ip());
        });
    }
}
