<?php

use App\Http\Controllers\Client\ClientController;

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use Illuminate\Http\Request;
use App\Http\Controllers\Accounts\AssetsController;
use App\Http\Controllers\Accounts\AccountsChartController;

use App\Http\Controllers\Commission\CommissionController;
use App\Http\Controllers\EmployeeTargetController;
use App\Http\Controllers\Logs\LogController;
use App\Http\Controllers\StatisticsController;
use App\Models\Client;
use App\Models\ClientRelation;
use App\Models\Invoice;
use App\Models\Offer;
use Illuminate\Support\Facades\Http;
use Modules\Client\Http\Controllers\ClientSettingController;
use Modules\Client\Http\Controllers\VisitController;

Route::get('/test/send', [ClientSettingController::class, 'test'])->name('clients.test_send');

Route::get('/send-daily-report', [VisitController::class, 'sendDailyReport']);
Route::get('/send-weekly-report', [VisitController::class, 'sendWeeklyReport']);
Route::get('/send-monthly-report', [VisitController::class, 'sendMonthlyReport']);


// routes/web.php
Route::get('/client-data/{clientId}', function ($clientId) {
    $client = Client::with(['latestStatus'])->findOrFail($clientId);

    $invoices = Invoice::where('client_id', $clientId)
        ->with(['items', 'payments_process'])
        ->orderBy('created_at', 'desc')
        ->get();

    $notes = ClientRelation::with(['employee', 'location'])
        ->where('client_id', $clientId)
        ->latest()
        ->get();

    return response()->json([
        'client' => $client,
        'invoices' => $invoices,
        'notes' => $notes,
    ]);
})->name('client.data');

require __DIR__ . '/auth.php';


Route::get('/text/editor', function () {
    return view('text_editor');
});
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'check.branch'],
    ],

    function () {
        Route::middleware(['auth', 'client.access'])->group(function () {
            Route::get('/personal', [ClientSettingController::class, 'personal'])->name('clients.personal');
            Route::get('/invoice/client', [ClientSettingController::class, 'invoice_client'])->name('clients.invoice_client'); // الفواتير
            Route::get('/appointments/client', [ClientSettingController::class, 'appointments_client'])->name('clients.appointments_client'); // المواعيد
            Route::get('/SupplyOrders/client', [ClientSettingController::class, 'SupplyOrders_client'])->name('clients.SupplyOrders_client'); // أوامر الشغل
            Route::get('/questions/client', [ClientSettingController::class, 'questions_client'])->name('clients.questions_client'); // عروض الأسعار
            Route::get('/edit/profile', [ClientSettingController::class, 'profile'])->name('clients.profile');
            Route::get('/employee-targets', [EmployeeTargetController::class, 'index'])->name('employee_targets.index');
            Route::post('/employee-targets', [EmployeeTargetController::class, 'storeOrUpdate'])->name('employee_targets.store');
            Route::get('/general-target', [EmployeeTargetController::class, 'showGeneralTarget'])->name('target.show');
            Route::post('/general-target', [EmployeeTargetController::class, 'updateGeneralTarget'])->name('target.update');
            Route::get('/client-target', [EmployeeTargetController::class, 'client_target'])->name('target.client');
            // التحصيل اليومي
            Route::get('/daily_closing_entry', [EmployeeTargetController::class, 'daily_closing_entry'])->name('daily_closing_entry');

            // احصائيات الزيارات
            Route::get('/visitTarget', [EmployeeTargetController::class, 'visitTarget'])->name('visitTarget');
            Route::post('/visitTarget', [EmployeeTargetController::class, 'updatevisitTarget'])->name('target.visitTarget');
            //احصائيات الفروع

            Route::get('/statistics_branch', [StatisticsController::class, 'StatisticsGroup'])->name('statistics.group');

            //احصائيات المجموعات

            Route::get('/statistics_group', [StatisticsController::class, 'Group'])->name('statistics.groupall');

            // احصائيات الاحياء

            Route::get('/statistics_neighborhood', [StatisticsController::class, 'neighborhood'])->name('statistics.neighborhood');
        });
        Route::prefix('sales')
            ->middleware(['auth', 'check.branch'])
            ->group(function () {
                # invoices routes

                Route::prefix('account')
                    ->middleware(['auth'])
                    ->group(function () {
                        Route::resource('Assets', AssetsController::class);
                        Route::get('Assets/{id}/pdf', [AssetsController::class, 'generatePdf'])->name('Assets.generatePdf');
                        Route::get('Assets/{id}/sell', [AssetsController::class, 'showSellForm'])->name('Assets.showSell');
                        Route::post('Assets/{id}/sell', [AssetsController::class, 'sell'])->name('Assets.sell');
                        Route::get('/chart/details/{accountId}', [AccountsChartController::class, 'getAccountDetails'])->name('account.details');
                        Route::post('/set-error', function (Illuminate\Http\Request $request) {
                            session()->flash('error', $request->message);
                            return response()->json(['success' => true]);
                        });
                    });
            });


        Route::prefix('accounts')
            ->middleware(['auth'])
            ->group(function () {
                Route::get('/tree', [AccountsChartController::class, 'getTree'])->name('accounts.tree');
                Route::get('/showDetails/{id}', [AccountsChartController::class, 'showDetails'])->name('account.showDetails');
                Route::get('/chart/details/{accountId}', [AccountsChartController::class, 'getAccountDetails'])->name('accounts.details');
                Route::get('/{id}/children', [AccountsChartController::class, 'getChildren'])->name('accounts.children');
            });
        // إضافة هذه الـ routes الجديدة مع الـ routes الموجودة للـ visits

        Route::prefix('visits')->group(function () {
            Route::post('/visits', [VisitController::class, 'storeEmployeeLocation'])->name('visits.storeEmployeeLocation');
            Route::get('/visits/today', [VisitController::class, 'getTodayVisits'])
                ->middleware('auth')
                ->name('visits.today');

            Route::get('/traffic-analysis', [VisitController::class, 'tracktaff'])->name('traffic.analysis');
            Route::post('/get-weeks-data', [VisitController::class, 'getWeeksData'])->name('get.weeks.data');
            Route::post('/get-traffic-data', [VisitController::class, 'getTrafficData'])->name('get.traffic.data');

            Route::post('/visits/location-enhanced', [VisitController::class, 'storeLocationEnhanced'])->name('visits.storeLocationEnhanced');

            Route::post('/visits/location-enhanced', [VisitController::class, 'storeLocationEnhanced'])->name('visits.storeLocationEnhanced');

            Route::get('/tracktaff', [VisitController::class, 'tracktaff'])->name('visits.tracktaff');

            // إضافة هذا المسار للانصراف التلقائي
            Route::get('/process-auto-departures', [VisitController::class, 'checkAndProcessAutoDepartures'])->name('visits.processAutoDepartures');
            Route::get('/send-daily-report', [VisitController::class, 'sendDailyReport']);
            // إضافة مسار للانصراف اليدوي
            Route::post('/manual-departure/{visitId}', [VisitController::class, 'manualDeparture'])->name('visits.manualDeparture');
            // Routes جديدة للتحسين
            Route::post('/clear-visits-data', [VisitController::class, 'clearVisitsData'])->name('visits.clearData');
            Route::post('/clear-cache', function() {
                cache()->forget('traffic_analytics_' . date('Y-m-d-H'));
                return response()->json(['success' => true]);
            })->name('visits.clearCache');
        });

        Route::prefix('logs')
            ->middleware(['auth'])
            ->group(function () {
                Route::get('/index', [LogController::class, 'index'])->name('logs.index');
            });

    },
);
