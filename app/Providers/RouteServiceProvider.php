<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/dashboard/sales/index';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')->prefix('api')->group(base_path('routes/api.php'));
            Route::middleware('web')->group(base_path('routes/web.php'));
            Route::middleware('web')->group(base_path('routes/auth.php'));
            Route::middleware('web')->group(base_path('routes/sales_reports.php'));
            Route::middleware('web')->group(base_path('routes/balances.php'));
            Route::middleware('web')->group(base_path('routes/rentals.php'));
            Route::middleware('web')->group(base_path('routes/workflow.php'));
            Route::middleware('web')->group(base_path('routes/orders.php'));
            Route::middleware('web')->group(base_path('routes/customers.php'));
            Route::middleware('web')->group(base_path('routes/inventory.php'));
            Route::middleware('web')->group(base_path('routes/dashboard.php'));
            Route::middleware('web')->group(base_path('routes/time-tracking.php'));
            Route::middleware('web')->group(base_path('routes/orders.php'));
            Route::middleware('web')->group(base_path('routes/customers.php'));
            Route::middleware('web')->group(base_path('routes/inventory.php'));
            Route::middleware('web')->group(base_path('routes/track_time.php'));

            Route::middleware('web')->group(base_path('routes/dashboard.php'));
            Route::middleware('web')->group(base_path('routes/time-tracking.php'));
            Route::middleware('web')->group(base_path('routes/orders.php'));
            Route::middleware('web')->group(base_path('routes/customers.php'));
            Route::middleware('web')->group(base_path('routes/inventory.php'));
            Route::middleware('web')->group(base_path('routes/activity.php'));
            Route::middleware('web')->group(base_path('routes/track_time.php'));
            Route::middleware('web')->group(base_path('routes/memberships_subscriptions.php'));
            Route::middleware('web')->group(base_path('routes/customer_attendance.php'));

            Route::middleware('web')->group(base_path('routes/online_store.php'));
            Route::middleware('web')->group(base_path('routes/orders.php'));
        });
    }


    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
