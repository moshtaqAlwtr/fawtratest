<?php

use App\Http\Controllers\CustomerAttendance\CustomerAttendanceController;
use App\Http\Controllers\Reports\WorkflowController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\OrdersController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'check.branch']
    ],
    function () {
        Route::prefix('Customer_Attendance')->group(function () {
            Route::get('/', [CustomerAttendanceController::class, 'index'])->name('customer_attendance.index');
            Route::post('/store', [CustomerAttendanceController::class, 'store'])->name('customer_attendance.store');
            Route::put('/update/{id}', [CustomerAttendanceController::class, 'update'])->name('customer_attendance.update');
            Route::delete('/delete/{id}', [CustomerAttendanceController::class, 'destroy'])->name('customer_attendance.destroy');
        });
    }
);
