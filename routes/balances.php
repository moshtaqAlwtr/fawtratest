<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\BalancesController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;


require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('reports_Balances')->middleware(['auth'])->group(function () {
            // صفحة الفهرس
            Route::get('/index', [BalancesController::class, 'index'])->name('reports.Balances.index');

            // صفحة الشيكات المسلمة
            Route::get('/Campaigns', [BalancesController::class, 'consume'])->name('reports.Balances.consume_balance');

            // صفحة الشيكات المستلمة
            Route::get('/add_balance', [BalancesController::class, 'add'])->name('reports.Balances.add_balance');
        });
    }
);
