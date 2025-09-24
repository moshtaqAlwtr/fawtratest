<?php

namespace App\Providers;

use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
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
    {  View::composer('*', function ($view) {
        $today = Carbon::today();
        $todayVisits = Visit::with(['employee', 'client'])
            ->whereDate('visit_date', $today)
            ->orderBy('visit_date', 'desc')
            ->get();

        $view->with('todayVisits', $todayVisits);
    });
    }
}
