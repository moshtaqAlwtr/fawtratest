<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\SalesReportsController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('sales_reports')->middleware(['auth'])->group(function () {
            // تقارير الفواتير
            Route::get('/index', [SalesReportsController::class, 'index'])->name('sals_reports.index');
            Route::get('/byCustomer', [SalesReportsController::class, 'byCustomer'])->name('sals_reports.byCustomer');
            Route::get('/byEmployee', [SalesReportsController::class, 'byEmployee'])->name('sals_reports.byEmployee');
            Route::get('/byRepresentative', [SalesReportsController::class, 'byRepresentative'])->name('sals_reports.byRepresentative');
            Route::get('/byProduct', [SalesReportsController::class,    'byProduct'])->name('reports.sals.by_Product');
            Route::get('/Weekly_by_Product', [SalesReportsController::class,  'WeeklybyProduct'])->name('reports.sals.Weekly_by_Product');
            Route::get('/Monthly_by_Product', [SalesReportsController::class,   'MonthlybyProduct'])->name('reports.sals.Monthly_by_Product');
            Route::get('/Annual_by_Product', [SalesReportsController::class,    'AnnualbyProduct'])->name('reports.sals.Annual_by_Product');
            Route::get('/D_Sales', [SalesReportsController::class,    'Dsales'])->name('reports.sals.D_Sales');
            Route::get('/W_Sales', [SalesReportsController::class,    'Wsales'])->name('reports.sals.W_Sales');
            Route::get('/M_Sales', [SalesReportsController::class,    'Msales'])->name('reports.sals.M_Sales');
            Route::get('/A_Sales', [SalesReportsController::class,    'Asales'])->name('reports.sals.A_Sales');
            Route::get('/Payments_by_Customer', [SalesReportsController::class,    'byCust'])->name('reports.sals.Payments_by_Customer');
            Route::get('/Payments_by_Employee', [SalesReportsController::class,    'byembl'])->name('reports.sals.Payments_by_Employee');
            Route::get('/Payments_by_Payment_Method', [SalesReportsController::class,    'bypay'])->name('reports.sals.Payments_by_Payment_Method');
            Route::get('/Daily_Payments', [SalesReportsController::class,    'DailyPayments'])->name('reports.sals.Daily_Payments');
            Route::get('/Weekly_Payments', [SalesReportsController::class,    'WeeklyPayments'])->name('reports.sals.Weekly_Payments');
            Route::get('/Monthly_Payments', [SalesReportsController::class,    'MonthlyPayments'])->name('reports.sals.Monthly_Payments');
            Route::get('/Annual_Payments', [SalesReportsController::class,    'AnnualPayments'])->name('reports.sals.Annual_Payments');
            Route::get('/products_profit', [SalesReportsController::class, 'productsprofit'])->name('reports.sals.products_profit');
            Route::get('/Customer_Profit', [SalesReportsController::class, 'CustomerProfit'])->name('reports.sals.Customer_Profit');
            Route::get('/Employee_Profit', [SalesReportsController::class, 'EmployeeProfit'])->name('reports.sals.Employee_Profit');
            Route::get('/Manager_Profit', [SalesReportsController::class, 'ManagerProfit'])->name('reports.sals.Manager_Profit');
            Route::get('/Daily_Profits', [SalesReportsController::class, 'DailyProfits'])->name('reports.sals.Daily_Profits');
            Route::get('/Weekly_Profits', [SalesReportsController::class, 'WeeklyProfits'])->name('reports.sals.Weekly_Profits');
            Route::get('/Annual_Profits', [SalesReportsController::class, 'AnnualProfits'])->name('reports.sals.Annual_Profits');
            Route::get('/Item_Sales_By_Item', [SalesReportsController::class, 'ItemSalesByItem'])->name('reports.sals.Sales_By_Item');
            Route::get('/Item_Sales_By_Category', [SalesReportsController::class, 'ItemSalesByCategory'])->name('reports.sals.Sales_By_Category');
            Route::get('/Item_Sales_By_Brand', [SalesReportsController::class, 'ItemSalesByBrand'])->name('reports.sals.Sales_By_Brand');
            Route::get('/Item_Sales_By_Employee', [SalesReportsController::class, 'ItemSalesByEmployee'])->name('reports.sals.Sales_By_Employee');
            Route::get('/Item_Sales_By_SalesRep', [SalesReportsController::class, 'ItemSalesBySalesRep'])->name('reports.sals.Sales_By_SalesRep');
            Route::get('/Item_Sales_By_Customer', [SalesReportsController::class, 'ItemSalesByCustomer'])->name('reports.sals.Sales_By_Customer');

        });
    });
