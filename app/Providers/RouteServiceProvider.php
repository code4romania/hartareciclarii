<?php

declare(strict_types=1);

namespace App\Providers;

use App\DataTransferObjects\MapCoordinates;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public static function getDashboardUrl(): string
    {
        return route('front.dashboard');
    }

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->name('front.')
                ->group(base_path('routes/web.php'));
        });

        Route::bind('coordinates', fn (string $coordinates) => new MapCoordinates($coordinates));
    }
}
