<?php

use App\Http\Controllers\Reports\WorkflowController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Orders\OrdersController;
use App\Http\Controllers\Orders\SettingsController;
use App\Http\Controllers\Hr\EmployeeController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {

        Route::prefix('order')->middleware(['auth'])->group(function () {

            # employee routes
            Route::prefix('management')->group(function () {
                Route::get('/mangame', [OrdersController::class, 'mangame'])->name('orders.management.mangame');
                Route::get('/index',[OrdersController::class,'index'])->name('orders.management.index');
                Route::get('/create',[OrdersController::class,'create'])->name('orders.management.create');
                Route::get('/edit/{id}',[OrdersController::class,'edit'])->name('Order.edit');
                Route::get('/show/{id}',[OrdersController::class,'show'])->name('Order.show');
                Route::post('/store',[OrdersController::class,'store'])->name('Order.store');
                Route::post('/update/{id}',[OrdersController::class,'update'])->name('Order.update');
                Route::post('/updatePassword/{id}',[OrdersController::class,'updatePassword'])->name('Order.updatePassword');
                Route::get('/delete/{id}',[OrdersController::class,'delete'])->name('Order.delete');
                Route::get('/login/to/{id}', [OrdersController::class, 'login_to'])->name('Order.login_to');
                Route::get('/export/view',[OrdersController::class,'export_view'])->name('Order.export_view');
                Route::post('/export',[OrdersController::class,'export'])->name('Order.export');
            });

            # settings routes
            Route::prefix('Settings')->group(function () {
                Route::get('/index',[SettingsController::class,'Settings'])->name('orders.Settings.index');
                Route::get('/type',[SettingsController::class,'type'])->name('orders.Settings.type');
                Route::get('/create',[SettingsController::class,'create'])->name('orders.Settings.create');
                Route::get('/edit/{id}',[SettingsController::class,'edit'])->name('Order.edit');
                Route::get('/show/{id}',[SettingsController::class,'show'])->name('Order.show');
            });

        });

    }
);

// ✅ تم نقل هذا السطر إلى الخارج ليكون مستقلاً ويمكن الوصول إليه
Route::get('/employees/search', [EmployeeController::class, 'search'])->name('employees.search');
