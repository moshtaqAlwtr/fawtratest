<?php

use App\Http\Controllers\OnlineStore\ContentManagementController;
use App\Http\Controllers\OnlineStore\SettingsStoreController;
use Illuminate\Support\Facades\Route;
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

    Route::prefix('online_store')->middleware(['auth'])->group(function () {
        #Online Store Routes
        Route::prefix('content-management')->group(function () {
            Route::get('/index', [ContentManagementController::class,'index'])->name('content_management.index');
            Route::get('/media',[ContentManagementController::class,'media'])->name('content_management.media');
            Route::get('/edit/media',[ContentManagementController::class,'media_edit'])->name('content_management.media_edit');
            Route::get('/page/management',[ContentManagementController::class,'page_management'])->name(name: 'content_management.page_management');
            Route::get('/page/management/create',[ContentManagementController::class,'page_management_create'])->name('content_management.page_management_create');
        });
        #Online Store Routes
        Route::prefix('store_settings')->group(function () {
            Route::get('/index', [SettingsStoreController::class,'index'])->name('store_settings.index');
            Route::get('/template', [SettingsStoreController::class,'template'])->name('store_settings.template');
            Route::get('/preview', [SettingsStoreController::class,'preview'])->name('store_settings.template.preview');
        });

    });

});


