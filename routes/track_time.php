<?php

use App\Http\Controllers\Task\TaskController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hr\EmployeeController;
use App\Http\Controllers\Hr\ManagingEmployeeRolesController;
use App\Http\Controllers\Hr\ShiftManagementController;
use App\Http\Controllers\Reports\TimeTrackingController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';


Route::group(

    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ], function(){

        Route::prefix('TimeTracking')->middleware(['auth'])->group(function () {

            # employee routes
            Route::prefix('TimeTracking')->group(function () {
                Route::get('/index',[TimeTrackingController::class,'index'])->name('TrackTime.index');
                Route::get('/create_invoice_time',[TimeTrackingController::class,'create_invoice_time'])->name('TrackTime.create_invoice_time');
                // Route::get('/index',[TimeTrackingController::class,'index'])->name('SittingTrackTime.index');



            });



        });



});
