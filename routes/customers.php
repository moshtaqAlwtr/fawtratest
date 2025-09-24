<?php

use App\Http\Controllers\Reports\WorkflowController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\Customers\CustomerReportController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('reports/customers')->middleware('auth')->group(function () {
            Route::get('/', [CustomerReportController::class, 'index'])->name('reports.customers.index');
            Route::get('/aging-invoices', [CustomerReportController::class, 'agingInvoices'])->name('reports.customers.agingInvoices');
            Route::get('/aging-ledger', [CustomerReportController::class, 'agingLedger'])->name('reports.customers.agingLedger');
            Route::get('/customer-directory', [CustomerReportController::class, 'customerDirectory'])->name('reports.customers.customerDirectory');
            Route::get('/customer-balances', [CustomerReportController::class, 'customerBalances'])->name('reports.customers.customerBalances');
            Route::get('/customer-sales', [CustomerReportController::class, 'customerSales'])->name('reports.customers.customerSales');
            Route::get('/customer-payments', [CustomerReportController::class, 'customerPayments'])->name('reports.customers.customerPayments');
            Route::get('/customer-statements', [CustomerReportController::class, 'customerStatements'])->name('reports.customers.customerStatements');
            Route::get('/customer-appointments', [CustomerReportController::class, 'customerAppointments'])->name('reports.customers.customerAppointments');
            Route::get('/customer-installments', [CustomerReportController::class, 'customerInstallments'])->name('reports.customers.customerInstallments');
        });
    }
);
