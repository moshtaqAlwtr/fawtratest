<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\PointsAndBlances\Http\Controllers\BalanceTypeController;
use Modules\PointsAndBlances\Http\Controllers\ManagingBalanceConsumptionController;
use Modules\PointsAndBlances\Http\Controllers\MangRechargeBalancesController;
use Modules\PointsAndBlances\Http\Controllers\PackageManagementController;

use Modules\PointsAndBlances\Http\Controllers\SittingController;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch'],
    ],
    function () {
        Route::prefix('RechargeBalances')
            ->middleware(['auth', 'role:manager'])
            ->group(function () {
                Route::prefix('MangRechargeBalances')->group(function () {
                    Route::get('/index', [MangRechargeBalancesController::class, 'index'])->name('MangRechargeBalances.index');
                    Route::get('/create', [MangRechargeBalancesController::class, 'create'])->name('MangRechargeBalances.create');
                    Route::post('/store', [MangRechargeBalancesController::class,'store'])->name('MangRechargeBalances.store');
                    Route::get('/show/{id}', [MangRechargeBalancesController::class, 'show'])->name('MangRechargeBalances.show');
                    Route::get('/edit/{id}', [MangRechargeBalancesController::class, 'edit'])->name('MangRechargeBalances.edit');
                    Route::put('/update/{id}', [MangRechargeBalancesController::class,'update'])->name('MangRechargeBalances.update');
                    Route::delete('/destroy/{id}', [MangRechargeBalancesController::class,'destroy'])->name('MangRechargeBalances.destroy');
                    Route::get('/updateStatus/{id}', [MangRechargeBalancesController::class,'updateStatus'])->name('MangRechargeBalances.updateStatus');
                });
                Route::prefix('PackageManagement')->group(function () {
                    Route::get('/index', [PackageManagementController::class, 'index'])->name('PackageManagement.index');
                    Route::get('/create', [PackageManagementController::class, 'create'])->name('PackageManagement.create');
                    Route::post('/store', [PackageManagementController::class,'store'])->name('PackageManagement.store');
                    Route::get('/show/{id}', [PackageManagementController::class, 'show'])->name('PackageManagement.show');
                    Route::get('/edit/{id}', [PackageManagementController::class, 'edit'])->name('PackageManagement.edit');
                    Route::put('/update/{id}', [PackageManagementController::class,'update'])->name('PackageManagement.update');
                    Route::delete('/destroy/{id}', [PackageManagementController::class,'destroy'])->name('PackageManagement.destroy');
                    Route::get('/updateStatus/{id}', [PackageManagementController::class,'updateStatus'])->name('PackageManagement.updateStatus');
                });
                Route::prefix('BalanceType')->group(function () {
                    Route::get('/index', [BalanceTypeController::class, 'index'])->name('BalanceType.index');
                    Route::get('/create', [BalanceTypeController::class, 'create'])->name('BalanceType.create');
                    Route::post('/store', [BalanceTypeController::class,'store'])->name('BalanceType.store');
                    Route::get('/show/{id}', [BalanceTypeController::class, 'show'])->name('BalanceType.show');
                    Route::get('/edit/{id}', [BalanceTypeController::class, 'edit'])->name('BalanceType.edit');
                    Route::put('/update/{id}', [BalanceTypeController::class,'update'])->name('BalanceType.update');
                    Route::delete('/destroy/{id}', [BalanceTypeController::class,'destroy'])->name('BalanceType.destroy');
                    Route::get('/updateStatus/{id}', [BalanceTypeController::class,'updateStatus'])->name('BalanceType.updateStatus');

                });
                Route::prefix('ManagingBalanceConsumption')->group(function () {
                    Route::get('/index', [ManagingBalanceConsumptionController::class, 'index'])->name('ManagingBalanceConsumption.index');
                    Route::get('/create', [ManagingBalanceConsumptionController::class, 'create'])->name('ManagingBalanceConsumption.create');
                    Route::post('/store', [ManagingBalanceConsumptionController::class, 'store'])->name('ManagingBalanceConsumption.store');
                    Route::get('/edit/{id}', [ManagingBalanceConsumptionController::class, 'edit'])->name('ManagingBalanceConsumption.edit');
                    Route::put('/update/{id}', [ManagingBalanceConsumptionController::class, 'update'])->name('ManagingBalanceConsumption.update');
                    Route::delete('/destroy/{id}', [ManagingBalanceConsumptionController::class, 'destroy'])->name('ManagingBalanceConsumption.destroy');
                    Route::get('/show/{id}', [ManagingBalanceConsumptionController::class, 'show'])->name('ManagingBalanceConsumption.show');
                    Route::get('/updateStatus/{id}', [ManagingBalanceConsumptionController::class, 'updateStatus'])->name('ManagingBalanceConsumption.updateStatus');

                });
                Route::prefix('Sitting')->group(function () {
                    Route::get('/index', [SittingController::class, 'index'])->name('sitting.index');
                });
            });
    },
);

