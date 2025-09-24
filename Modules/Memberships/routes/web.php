<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Memberships\Http\Controllers\MembershipsController;
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch'],
    ],
    function () {
        Route::prefix('Memberships')
            ->middleware(['auth', 'role:manager'])
            ->group(function () {
                Route::prefix('Memberships')->group(function () {
                    Route::get('/index', [MembershipsController::class, 'index'])->name('Memberships.index');
                    Route::get('/create', [MembershipsController::class, 'create'])->name('Memberships.create');
                    Route::post('/create', [MembershipsController::class, 'store'])->name('Memberships.store');
                    Route::get('/show/{id}', [MembershipsController::class, 'show'])->name('Memberships.show');
                    Route::get('/edit/{id}', [MembershipsController::class, 'edit'])->name('Memberships.edit');
                    Route::put('/update/{id}', [MembershipsController::class, 'update'])->name('Memberships.update');
                    Route::get('/delete/{id}', [MembershipsController::class, 'delete'])->name('Memberships.delete');
                    Route::get('/renew/{id}', [MembershipsController::class, 'renew'])->name('Memberships.renew');
                    Route::post('/renew/update/{id}', [MembershipsController::class, 'renew_update'])->name('Memberships.renew_update');
                    Route::get('/be_active/{id}', [MembershipsController::class, 'be_active'])->name('Memberships.be_active'); //تنشيط
                    Route::get('/deactive/{id}', [MembershipsController::class, 'deactive'])->name('Memberships.deactive');// الغاء التنشيط
                    Route::get('/create_invoice/{id}/{type?}', [MembershipsController::class, 'create_invoice'])->name('Memberships.create_invoice');
                    Route::get('/subscriptions', [MembershipsController::class, 'subscriptions'])->name('Memberships.subscriptions');
                    Route::get('/subscriptions/show/{id}', [MembershipsController::class, 'show_subscription'])->name('Memberships.show_subscription');
                });

                Route::prefix('SittingMemberships')->group(function () {
                    Route::get('/index', [SittingController::class, 'index'])->name('SittingMemberships.index');
                    Route::get('/sitting', [SittingController::class, 'sitting'])->name('SittingMemberships.sitting');
                    Route::post('/store', [SittingController::class, 'store'])->name('SittingMemberships.store');


                });
                Route::prefix('Subscription')->group(function () {
                    Route::get('/index', [SubscriptionController::class, 'index'])->name('Memberships.subscriptions.index');
                    Route::get('/sitting', [SubscriptionController::class, 'sitting'])->name('Memberships.subscriptions.create');

                });
            });
    },
);
