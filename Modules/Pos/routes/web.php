<?php

use App\Http\Controllers\Orders\SettingsController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Pos\Http\Controllers\DevicesController;
use Modules\Pos\Http\Controllers\ReportsController;
use Modules\Pos\Http\Controllers\SalesStartController;
use Modules\Pos\Http\Controllers\SessionsController;
use Modules\Pos\Http\Controllers\ShiftController;
use Modules\Sales\Http\Controllers\InvoicesController;

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

// Route::get('/', function () {
//     return view('master');
// });

//require __DIR__ . '/auth.php'


Route::get('/search', [SalesStartController::class, 'search'])->name('sales_start.search');


Route::group(

    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'check.branch']
    ],
    function () {

        Route::prefix('POS')->middleware(['auth'])->group(function () {

            # employee routes
            Route::prefix('pos_reports')->group(function () {
                Route::get('/index', [ReportsController::class, 'index'])->name('pos_reports.index');
                Route::get('/Category', [ReportsController::class, 'Category'])->name('pos_reports.Category');
                Route::get('/category-sales/data', [ReportsController::class, 'getCategorySalesReport'])
                    ->name('pos.reports.category.data');
                Route::post('/category-sales/export', [ReportsController::class, 'exportCategorySalesReport'])
                    ->name('pos.reports.category.export');
                Route::get('/quickTest', [ReportsController::class, 'quickTest'])
                    ->name('pos.reports.category.quickTest');
                // أضف هذا Route في web.php:
                Route::get('/test-report', [ReportsController::class, 'testCategoryReport'])
                    ->name('pos.test.report');
                Route::get('/category-sales/print', [SalesStartController::class, 'printCategorySalesReport'])
                    ->name('pos.reports.category.print'); 
                Route::get('/Product_Sales', [ReportsController::class, 'Product'])->name('pos_reports.Product');
                Route::get('/Shift', [ReportsController::class, 'Shift'])->name('pos.reports.shift');
                Route::get('/shift-sales/export', [ReportsController::class, 'exportShiftReport'])->name('pos.reports.shift.export');
                Route::get('/shift-sales/data', [ReportsController::class, 'getShiftData'])->name('pos.reports.shift.data');

                Route::get('/Detailed_Shift_Transactions', [ReportsController::class, 'Detailed'])->name('pos_reports.Detailed');
                Route::get('/Detailed_Shift', [ReportsController::class, 'exportDetailedShiftReport'])->name('pos.reports.detailed.export');
                
                Route::post('/Shift_Profitability', [ReportsController::class, 'Prof'])->name('pos_reports.Shift_Profitability');
                Route::post('/CategoryProfitability', [ReportsController::class, 'Cate'])->name('pos_reports.Category_Profitability');
                Route::post('/ProductProfitability', [ReportsController::class, 'Prod'])->name('pos_reports.Product_Profitabilit');
            });

            // الصفحة الرئيسية لنقطة البيع
            Route::get('/sales-start', [SalesStartController::class, 'index'])
                ->name('sales_start.index');

            // البحث المتقدم
            Route::get('/search', [SalesStartController::class, 'search'])
                ->name('search');

            // جلب المنتجات بناءً على التصنيف
            Route::get('/products-by-category', [SalesStartController::class, 'getProductsByCategory'])
                ->name('products_by_category');

            // حفظ الفاتورة
            Route::post('/sales-start/store', [SalesStartController::class, 'store'])
                ->name('sales_start.store');

            Route::get('/invoices/{id}/details', [SalesStartController::class, 'getInvoiceDetails'])
                ->name('invoices.details');


            // إدارة الفواتير المعلقة
            Route::get('/held-invoices', [SalesStartController::class, 'getHeldInvoices'])
                ->name('held_invoices.index');

            Route::get('/held-invoices/{id}/resume', [SalesStartController::class, 'resumeHeldInvoice'])
                ->name('held_invoices.resume');

            Route::delete('/held-invoices/{id}', [SalesStartController::class, 'deleteHeldInvoice'])
                ->name('held_invoices.delete');

            // طباعة الفاتورة

            // مسارات الاسترداد في POS
            Route::get('/available-invoices-for-return', [SalesStartController::class, 'getAvailableInvoicesForReturn']);
            Route::get('/invoice-details-for-return/{id}', [SalesStartController::class, 'getInvoiceDetailsForReturn']);
            Route::post('/process-return', [SalesStartController::class, 'processReturn']);
            Route::get('/return_invoices/{id}/print', [SalesStartController::class, 'printReturnInvoice']);

            // إحصائيات اليوم
            Route::get('/daily-stats', [SalesStartController::class, 'getDailyStats'])
                ->name('daily_stats');
            # employee managing employee roles
            Route::prefix('Sales_Start')->group(function () {
                Route::get('/index', [SalesStartController::class, 'index'])->name('POS.sales_start.index');
                Route::post('/store', [SalesStartController::class, 'store'])->name('POS.sales_start.store');
                Route::get('/invoices/{id}/print', [SalesStartController::class, 'print'])->name('POS.invoices.print');
            });



            Route::prefix('session')->group(function () {
                Route::get('/create', [SessionsController::class, 'create'])->name('pos.sessions.create');
                Route::get('/', [SessionsController::class, 'index'])->name('pos.sessions.index');
                Route::post('/', [SessionsController::class, 'store'])->name('pos.session.store');
                Route::get('/{id}', [SessionsController::class, 'show'])->name('pos.sessions.show');
                Route::get('/{id}/close', [SessionsController::class, 'closeForm'])->name('pos.sessions.close-form');
                Route::post('/{id}/close', [SessionsController::class, 'close'])->name('pos.sessions.close');
                Route::get('/{id}/summary', [SessionsController::class, 'summary'])->name('pos.sessions.summary');


                // طباعة وتصدير
                Route::get('{id}/print', [SessionsController::class, 'print'])->name('pos.sessions.print');

                // مسار للحصول على أحدث المعاملات (AJAX)
                Route::get('{id}/transactions', [SessionsController::class, 'getTransactions'])->name('transactions');

                // مسار لتحديث إحصائيات الجلسة (AJAX)
                Route::get('{id}/refresh-stats', [SessionsController::class, 'refreshStats'])->name('refresh-stats');

                Route::get('{id}/details', [SessionsController::class, 'details'])->name('details');
            });

            # employee routes
            Route::prefix('Settings')->group(function () {
                Route::get('/index', [SettingsController::class, 'index'])->name('pos.settings.index');
                Route::get('/general', [SettingsController::class, 'general'])->name('pos.settings.general');
                Route::post('/store', [SettingsController::class, 'store'])->name('pos.settings.store');


                // في web.php أو routes الخاصة بك
                Route::get('/categories-by-type', [SettingsController::class, 'getCategoriesByType'])->name('pos.settings.categories.by-type');
                Route::get('/active-payment-methods', [SettingsController::class, 'getActivePaymentMethods'])->name('pos.active-payment-methods');
            });
            //  Route::resource('devices', DevicesController::class);


            Route::prefix('Devices')->group(function () {
                // Route::resource('devices', CashierDeviceController::class);
                Route::get('/index', [DevicesController::class, 'index'])->name('pos.settings.devices.index');
                Route::get('/Create', [DevicesController::class, 'create'])->name('pos.settings.devices.create');
                Route::post('/Create', [DevicesController::class, 'store'])->name('pos.settings.devices.store');
                Route::get('/show/{id}', [DevicesController::class, 'show'])->name('pos.settings.devices.show');
                Route::get('/edit/{id}', [DevicesController::class, 'edit'])->name('pos.settings.devices.edit');
                Route::post('/toggle-status/{id}', [DevicesController::class, 'toggleStatus'])
                    ->name('pos.settings.devices.toggle-status');
                Route::get('/edit/{id}', [DevicesController::class, 'edit'])->name('pos.settings.devices.edit');
                Route::put('/update/{id}', [DevicesController::class, 'update'])->name('pos.settings.devices.update');
                Route::delete('/delete/{id}', [DevicesController::class, 'destroy'])->name('pos.settings.devices.destroy');
            });
            Route::prefix('Shift')->group(function () {
                Route::get('/index', [ShiftController::class, 'index'])->name('pos.settings.shift.index');
                Route::get('/create', [ShiftController::class, 'create'])->name('pos.settings.shift.create');
                Route::post('/create', [ShiftController::class, 'store'])->name('pos.settings.shift.store');
                Route::get('/show/{id}', [ShiftController::class, 'show'])->name('pos.settings.shift.show');
                Route::get('/edit/{id}', [ShiftController::class, 'edit'])->name('pos.settings.shift.edit');
                Route::put('/update/{id}', [ShiftController::class, 'update'])->name('pos.settings.shift.update');
                Route::delete('/delete/{id}', [ShiftController::class, 'destroy'])->name('pos.settings.shift.destroy');
            });
        });
    }
);
