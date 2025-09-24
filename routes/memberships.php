<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\MembershipsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('reports_Memberships')->middleware(['auth'])->group(function () {
            // صفحة الفهرس
            Route::get('/index', [MembershipsController::class, 'index'])->name('reports.Memberships.index');

            // صفحة الشيكات المسلمة
            Route::get('/Expired', [MembershipsController::class, 'Expired'])->name('reports.Memberships.Expired');

            // صفحة الشيكات المستلمة
            Route::get('/Renewals', [MembershipsController::class, 'Renewals'])->name('reports.Memberships.Renewals');

            Route::get('/New_Subscriptions', [MembershipsController::class, 'Subscriptions'])->name('reports.Memberships.New_Subscriptions');
        });
    }
);
