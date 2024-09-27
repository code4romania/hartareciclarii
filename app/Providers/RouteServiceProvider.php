<?php

declare(strict_types=1);

namespace App\Providers;

use App\DataTransferObjects\MapCoordinates;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        Route::bind('coordinates', fn (string $coordinates) => new MapCoordinates($coordinates));

        $this->routes(function () {
            Route::middleware(['web', HandleInertiaRequests::class])
                ->name('auth.')
                ->group(base_path('routes/auth.php'));

            Route::middleware(['web', HandleInertiaRequests::class])
                ->name('front.')
                ->group(base_path('routes/web.php'));
        });
    }

    protected function configureRateLimiting(): void
    {
        RateLimiter::for(
            'register',
            fn (Request $request) => Limit::perMinute(config('throttle.register'))
                ->by($request->ip())
        );

        RateLimiter::for(
            'login',
            fn (Request $request) => Limit::perMinute(config('throttle.login'))
                ->by($request->ip())
        );

        RateLimiter::for(
            'forgot-password',
            fn (Request $request) => Limit::perMinute(config('throttle.forgot-password'))
                ->by($request->ip())
        );

        RateLimiter::for(
            'reset-password',
            fn (Request $request) => Limit::perMinute(config('throttle.reset-password'))
                ->by($request->ip())
        );

        RateLimiter::for(
            'submit',
            fn (Request $request) => Limit::perMinute(config('throttle.submit'))
                ->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for(
            'media',
            fn (Request $request) => Limit::perMinute(config('throttle.media'))
                ->by($request->user()?->id ?: $request->ip())
        );

        RateLimiter::for(
            'map',
            fn (Request $request) => Limit::perMinute(config('throttle.map'))
                ->by($request->user()?->id ?: $request->ip())
        );
    }

    public static function getDashboardUrl(): string
    {
        return route('front.account.dashboard');
    }
}
