<?php


use Illuminate\Support\Facades\Route;
use Modules\RentalManagement\Http\Controllers\AddTypeController;
use Modules\RentalManagement\Http\Controllers\GeneralController;
use Modules\RentalManagement\Http\Controllers\OrdersController;
use Modules\RentalManagement\Http\Controllers\RentalPriceRuleController;
use Modules\RentalManagement\Http\Controllers\ReservationStatusController;
use Modules\RentalManagement\Http\Controllers\SeasonalPricesController;
use Modules\RentalManagement\Http\Controllers\SettingsController;
use Modules\RentalManagement\Http\Controllers\UnitsController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('Units')->group(function () {
        Route::get('/index', [UnitsController::class, 'index'])->name('rental_management.units.index');
        Route::get('/create', [UnitsController::class, 'create'])->name('rental_management.units.create');
        Route::get('/show/{id}', [UnitsController::class, 'show'])->name('rental_management.units.show');
        Route::post('/store', [UnitsController::class, 'store'])->name('rental_management.units.store');
        Route::get('/edit/{id}', [UnitsController::class, 'edit'])->name('rental_management.units.edit');
        Route::put('/update/{id}', [UnitsController::class, 'update'])->name('rental_management.units.update');
        Route::delete('/delete/{id}', [UnitsController::class, 'delete'])->name('rental_management.units.delete');
    });

    # أوامر الحجز
    Route::prefix('orders')->group(function () {
        Route::get('/index', [OrdersController::class, 'index'])->name('rental_management.orders.index');
        Route::get('/create', [OrdersController::class, 'create'])->name('rental_management.orders.create');
        Route::post('/store', [OrdersController::class, 'store'])->name('rental_management.orders.store');
        Route::get('/edit/{id}', [OrdersController::class, 'edit'])->name('rental_management.orders.edit');
        Route::post('/update/{id}', [OrdersController::class, 'update'])->name('rental_management.orders.update');
        Route::get('/delete/{id}', [OrdersController::class, 'delete'])->name('rental_management.orders.delete');
    });
    # قواعد التسعير
    Route::prefix('rental_price_rule')->group(function () {
        Route::get('/index', [RentalPriceRuleController::class, 'index'])->name('rental_management.rental_price_rule.index');
        Route::get('/create', [RentalPriceRuleController::class, 'create'])->name('rental_management.rental_price_rule.create');
        Route::post('/store', [RentalPriceRuleController::class, 'store'])->name('rental_management.rental_price_rule.store');
        Route::get('/show/{id}', [RentalPriceRuleController::class, 'show'])->name('rental_management.rental_price_rule.show');
        Route::get('/edit/{id}', [RentalPriceRuleController::class, 'edit'])->name('rental_management.rental_price_rule.edit');
        Route::put('/update/{id}', [RentalPriceRuleController::class, 'update'])->name('rental_management.rental_price_rule.update');
        Route::delete('/destroy/{id}', [RentalPriceRuleController::class, 'destroy'])->name('rental_management.rental_price_rule.destroy');
    });
    # الأسعار الموسمية
    Route::prefix('seasonal-prices')->group(function () {
        Route::get('/index', [SeasonalPricesController::class, 'index'])->name('rental_management.seasonal-prices.index');
        Route::get('/create', [SeasonalPricesController::class, 'create'])->name('rental_management.seasonal-prices.create');
        Route::post('/store', [SeasonalPricesController::class, 'store'])->name('rental_management.seasonal-prices.store');
        Route::get('/show/{id}', [SeasonalPricesController::class, 'show'])->name('rental_management.seasonal-prices.show');
        Route::get('/edit/{id}', [SeasonalPricesController::class, 'edit'])->name('rental_management.seasonal-prices.edit');
        Route::put('/update/{id}', [SeasonalPricesController::class, 'update'])->name('rental_management.seasonal-prices.update');
        Route::delete('/delete/{id}', [SeasonalPricesController::class, 'delete'])->name('rental_management.seasonal-prices.delete');
    });
    # الأسعار الموسمية
    Route::prefix('Settings')->group(function () {
        Route::get('/index', [SettingsController::class, 'index'])->name('rental_management.settings.index');
        Route::get('/create', [SettingsController::class, 'create'])->name('rental_management.settings.create');
        Route::post('/store', [SettingsController::class, 'store'])->name('rental_management.settings.store');
        Route::get('/edit/{id}', [SettingsController::class, 'edit'])->name('rental_management.settings.edit');
        Route::post('/update/{id}', [SettingsController::class, 'update'])->name('rental_management.settings.update');
        Route::delete('seasonal-prices/{id}', [SeasonalPricesController::class, 'destroy'])->name('rental_management.seasonal-prices.destroy');
    });
    # الأعدادات العامة
    Route::prefix('General')->group(function () {
        Route::get('/index', [GeneralController::class, 'index'])->name('rental_management.Settings.general.index');
        Route::get('/create', [GeneralController::class, 'create'])->name('rental_management.general.create');
        Route::post('/store', [GeneralController::class, 'store'])->name('rental_management.general.store');
        Route::get('/edit/{id}', [GeneralController::class, 'edit'])->name('rental_management.general.edit');
        Route::post('/update/{id}', [GeneralController::class, 'update'])->name('rental_management.general.update');
        Route::get('/delete/{id}', [GeneralController::class, 'delete'])->name('rental_management.general.delete');
    });
    # الأعدادات العامة
    Route::prefix('reservation-status')->group(function () {
        Route::get('/index', [ReservationStatusController::class, 'index'])->name('rental_management.Settings.reservation-status.index');
        Route::get('/create', [ReservationStatusController::class, 'create'])->name('rental_management.reservation-status.create');
        Route::post('/store', [ReservationStatusController::class, 'store'])->name('rental_management.reservation-status.store');
        Route::get('/edit/{id}', [ReservationStatusController::class, 'edit'])->name('rental_management.reservation-status.edit');
        Route::post('/update/{id}', [ReservationStatusController::class, 'update'])->name('rental_management.reservation-status.update');
        Route::get('/delete/{id}', [ReservationStatusController::class, 'delete'])->name('rental_management.reservation-status.delete');
    });
    # أضف فرع
    Route::prefix('Settings\Add_Type')->group(function () {
        Route::get('/index', [AddTypeController::class, 'index'])->name('rental_management.Settings.Add_Type.index');
        Route::get('/create', [AddTypeController::class, 'create'])->name('rental_management.Settings.Add_Type.create');
        Route::post('/store', [AddTypeController::class, 'store'])->name('rental_management.Settings.Add_Type.store');
        Route::get('/edit/{id}', [AddTypeController::class, 'edit'])->name('rental_management.Settings.Add_Type.edit');
        Route::get('/show/{id}', [AddTypeController::class, 'show'])->name('rental_management.Settings.Add_Type.show');
        Route::post('/update/{id}', [AddTypeController::class, 'update'])->name('rental_management.Settings.Add_Type.update');
        Route::delete('/delete/{id}', [AddTypeController::class, 'destroy'])->name('rental_management.Settings.Add_Type.delete');
    });
});
