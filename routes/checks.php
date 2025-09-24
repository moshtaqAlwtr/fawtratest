<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\ChecksController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('reports_checks')->middleware(['auth'])->group(function () {
            // صفحة الفهرس
            Route::get('/index', [ChecksController::class, 'index'])->name('reports_Checks.index');

            // صفحة الشيكات المسلمة
            Route::get('/delivered', [ChecksController::class, 'delivered'])->name('reports.Checks.delivered-checks');

            // صفحة الشيكات المستلمة
            Route::get('/received', [ChecksController::class, 'received'])->name('reports.Checks.received-checks');
        });
    }
);
