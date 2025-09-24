<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reports\TimeTrackingController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function () {
        Route::prefix('reports/time-tracking')->middleware(['auth', 'role:manager'])->group(function () {
            // صفحة الفهرس (غير مستخدمة في الوقت الحالي)
            Route::get('/index', [TimeTrackingController::class, 'index'])->name('reports.time_tracking.index');

            // تقارير حسب الموظف
            Route::get('/employee', [TimeTrackingController::class, 'reportByEmployee'])->name('reports.time_tracking.report_by_employee');

            // تقارير حسب المشروع
            Route::get('/project', [TimeTrackingController::class, 'reportByProject'])->name('reports.time_tracking.report_by_project');

            // تقارير حسب الحالة
            Route::get('/status', [TimeTrackingController::class, 'reportByStatus'])->name('reports.time_tracking.report_by_status');

            // التقارير اليومية
            Route::get('/daily', [TimeTrackingController::class, 'dailyReport'])->name('reports.time_tracking.daily_report');

            // التقارير الأسبوعية
            Route::get('/weekly', [TimeTrackingController::class, 'weeklyReport'])->name('reports.time_tracking.weekly_report');

            // التقارير الشهرية
            Route::get('/monthly', [TimeTrackingController::class, 'monthlyReport'])->name('reports.time_tracking.monthly_report');

            // التقارير السنوية
            Route::get('/yearly', [TimeTrackingController::class, 'yearlyReport'])->name('reports.time_tracking.yearly_report');
        });
    }
);
