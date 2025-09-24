<?php

use App\Http\Controllers\Sitting\PaymentMethodsController;
use App\Http\Controllers\Sitting\SequenceNumberingController;
use Illuminate\Support\Facades\Route;
use Modules\Sitting\Http\Controllers\ApplicationManagementController;
use Modules\Sitting\Http\Controllers\CurrencyRatesController;
use Modules\Sitting\Http\Controllers\SittingAccountController;
use Modules\Sitting\Http\Controllers\SittingController;
use Modules\Sitting\Http\Controllers\SittingInfoController;
use Modules\Sitting\Http\Controllers\SMPTController;
use Modules\Sitting\Http\Controllers\SmsController;
use Modules\Sitting\Http\Controllers\TaxSittingController;

Route::middleware(['auth', ])->group(function () {
    Route::prefix('accountInfo')->group(function () {
                    Route::get('/index', [SittingInfoController::class, 'index'])->name('AccountInfo.index');
                    Route::get('/create', [SittingInfoController::class, 'create'])->name('AccountInfo.create');
                    Route::post('/store', [SittingInfoController::class, 'store'])->name('AccountInfo.store');
                    Route::get('/backup', [SittingInfoController::class, 'backup'])->name('AccountInfo.backup');
                    Route::post('/backup/download', [SittingInfoController::class, 'download'])->name('AccountInfo.download');
                });

                Route::prefix('SittingAccount')->group(function () {
                    Route::get('/index', [SittingAccountController::class, 'index'])->name('SittingAccount.index');
                    Route::post('/create', [SittingAccountController::class, 'store'])->name('SittingAccount.store');
                    Route::post('/Change_email', [SittingAccountController::class, 'Change_email'])->name('SittingAccount.Change_email');
                    Route::post('/change_password', [SittingAccountController::class, 'change_password'])->name('SittingAccount.change_password');
                    Route::get('/backgroundColor', [SittingAccountController::class, 'color'])->name('AccountInfo.backgroundColor');
                    Route::post('/updateColor', [SittingAccountController::class, 'updateColor'])->name('AccountInfo.updateColor');
                });

                Route::prefix('CurrencyRates')->group(function () {
                    Route::get('/index', [CurrencyRatesController::class, 'index'])->name('CurrencyRates.index');
                    Route::get('/create', [CurrencyRatesController::class, 'create'])->name('CurrencyRates.create');
                    Route::get('/edit/{id}', [CurrencyRatesController::class, 'edit'])->name('CurrencyRates.edit');
                });
                Route::prefix('SMPT')->group(function () {
                    Route::get('/index', [SMPTController::class, 'index'])->name('SMPT.index');
                    Route::get('/sendTestMail', [SMPTController::class, 'sendTestMail'])->name('SMPT.sendTestMail');
                });
                Route::prefix('PaymentMethods')->group(function () {
                    Route::get('/index', [PaymentMethodsController::class, 'index'])->name('PaymentMethods.index');
                    Route::get('/create', [PaymentMethodsController::class, 'create'])->name('PaymentMethods.create');
                    Route::post('/store', [PaymentMethodsController::class, 'store'])->name('PaymentMethods.store');
                    Route::put('/payments/update-status', [PaymentMethodsController::class, 'updatePaymentStatus'])->name('update.payment.status');
                });
                Route::prefix('ApplicationManagement')->group(function () {
                    Route::get('/index', [ApplicationManagementController::class, 'index'])->name('Application.index');
                    Route::post('/update', [ApplicationManagementController::class, 'update'])->name('Application.update');
                });
                Route::prefix('TaxSitting')->group(function () {
                    Route::get('/index', [TaxSittingController::class, 'index'])->name('TaxSitting.index');
                    // Route::post('/update/{id}', [TaxSittingController::class, 'update'])->name('TaxSitting.update');
                    Route::post('/tax-setting/update', [TaxSittingController::class, 'updateAll'])->name('TaxSitting.updateAll');
                    Route::delete('/tax-sittings/{id}', [TaxSittingController::class, 'destroy'])->name('tax-sittings.destroy');
                });
                Route::prefix('Sms')->group(function () {
                    Route::get('/index', [SmsController::class, 'index'])->name('Sms.index');
                });

                Route::prefix('SequenceNumbering')->group(function () {
                    Route::get('/index/{section}', [SequenceNumberingController::class, 'index'])->name('SequenceNumbering.index');

                    // تحديث إعدادات قسم معين
                    Route::post('/sequence-numbering', [SequenceNumberingController::class, 'store'])->name('SequenceNumbering.store');

                    Route::get('/get-current-number/{section}', [SequenceNumberingController::class, 'getCurrentNumber'])->name('SequenceNumbering.current.number');

                    // توليد رقم تسلسلي جديد
                    Route::get('/sequence-numbering/{section}/generate', [SequenceNumberingController::class, 'generate']);
                });
});
