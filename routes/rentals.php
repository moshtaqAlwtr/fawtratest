<?php




use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\RentalsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('reports')->middleware(['auth'])->group(function () {
            // صفحة الفهرس
            Route::get('/index', [RentalsController::class, 'index'])->name('Rentals.index');

            Route::get('/Available_Units', [RentalsController::class, 'AvailableUnits'])->name('reports.Rentals.Available_Units');

            Route::get('/Unit_Pricing', [RentalsController::class, 'UnitPricing'])->name('reports.Rentals.Unit_Pricing');

            Route::get('/New_Subscriptions', [RentalsController::class, 'Subscriptions'])->name('reports.Rentals.New_Subscriptions');

            Route::get('/Unit_Type', [RentalsController::class, 'UnitType'])->name('reports.Rentals.Unit_Type');

            Route::get('/Main_Unit_Name', [RentalsController::class, 'MainUnitName'])->name('reports.Rentals.Main_Unit_Name');

            Route::get('/Daily_for_Units', [RentalsController::class, 'Daily'])->name('reports.Rentals.Daily_for_Units');

            Route::get('/Weekly_for_Units', [RentalsController::class, 'Weekly'])->name('reports.Rentals.Weekly_for_Units');

            Route::get('/Monthly_for_Units', [RentalsController::class, 'Monthly'])->name('reports.Rentals.Monthly_for_Units');
        });
    }
);

