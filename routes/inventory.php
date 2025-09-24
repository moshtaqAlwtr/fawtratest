<?php

use App\Http\Controllers\Reports\Inventory\InventoryReportController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch']
    ],
    function() {
        Route::prefix('reports/inventory')->middleware('auth')->group(function () {
            Route::get('/', [InventoryReportController::class,  'index'])->name('reports.inventory.index');
            Route::get('/inventory-count', [InventoryReportController::class, 'inventoryCount'])->name('reports.inventory.inventoryCount');
            Route::get('/inventory-summary', [InventoryReportController::class, 'inventorySummary'])->name('reports.inventory.inventorySummary');
            Route::get('/inventory-movement', [InventoryReportController::class, 'inventoryMovement'])->name('reports.inventory.inventoryMovement');
            Route::get('/inventory-value', [InventoryReportController::class, 'inventoryValue'])->name('reports.inventory.inventoryValue');
            Route::get('/inventory-balance-summary', [InventoryReportController::class, 'inventoryBalanceSummary'])->name('reports.inventory.inventoryBalanceSummary');
            Route::get('/product-trial-balance', [InventoryReportController::class, 'productTrialBalance'])->name('reports.inventory.productTrialBalance');
            Route::get('/product-movement-details', [InventoryReportController::class, 'productMovementDetails'])->name('reports.inventory.productMovementDetails');
            Route::get('/track-products-by-batch-expiry', [InventoryReportController::class, 'trackProductsByBatchAndExpiry'])->name('reports.inventory.trackProductsByBatchAndExpiry');
            Route::get('/track-products-by-serial-number', [InventoryReportController::class, 'trackProductsBySerialNumber'])->name('reports.inventory.trackProductsBySerialNumber');
            Route::get('/track-products-by-batch', [InventoryReportController::class, 'trackProductsByBatch'])->name('reports.inventory.trackProductsByBatch');
            Route::get('/track-products-by-expiry', [InventoryReportController::class, 'trackProductsByExpiry'])->name('reports.inventory.trackProductsByExpiry');
        });
    }
);
