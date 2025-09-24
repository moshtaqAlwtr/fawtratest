<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\SMSController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('reports_SMS')->group(function () {
            // صفحة الفهرس
            Route::get('/index', [SMSController::class, 'index'])->name('reports.sms.index');

            // صفحة الشيكات المسلمة
            Route::get('/Campaigns', [SMSController::class, 'Campaigns'])->name('reports.sms.Campaigns');

            // صفحة الشيكات المستلمة
            Route::get('/log', [SMSController::class, 'log'])->name('reports.sms.log');
        });
    }
);
