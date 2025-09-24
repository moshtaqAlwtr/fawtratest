<?php


use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Account\Http\Controllers\AccountsChartController;
use Modules\Account\Http\Controllers\AssetsController;
use Modules\Stock\Http\Controllers\Manufacturing\BOMController;
use Modules\Stock\Http\Controllers\Manufacturing\IndirectCostsController;
use Modules\Stock\Http\Controllers\Manufacturing\ManufacturingOrdersController;
use Modules\Stock\Http\Controllers\Manufacturing\SettingsController;
use Modules\Stock\Http\Controllers\Manufacturing\WorkstationsController;
use Modules\Stock\Http\Controllers\Stock\CategoryController;
use Modules\Stock\Http\Controllers\Stock\InventoryManagementController;
use Modules\Stock\Http\Controllers\Stock\InventorySettingsController;
use Modules\Stock\Http\Controllers\Stock\PriceListController;
use Modules\Stock\Http\Controllers\Stock\ProductsController;
use Modules\Stock\Http\Controllers\Stock\ProductsSettingsController;
use Modules\Stock\Http\Controllers\Stock\StorehouseController;
use Modules\Stock\Http\Controllers\Stock\StorePermitsManagementController;
use Modules\Stock\Http\Controllers\Stock\TemplateUnitController;

Route::middleware(['auth'])->group(function () {
    Route::get('/get-product-stock/{storeId}/{productId}', [StorePermitsManagementController::class, 'getProductStock']);

    Route::group(
        [
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'check.branch'],
        ],
        function () {
            Route::prefix('stock')
                ->middleware(['auth'])
                ->group(function () {
                    Route::prefix('products')->group(function () {
                        Route::get('/index', [ProductsController::class, 'index'])
                            ->name('products.index')
                            ->middleware('permission:products_view_all_products');
                        Route::get('/traking/products', [ProductsController::class, 'traking'])->name('products.traking');
                        Route::get('/create', [ProductsController::class, 'create'])
                            ->name('products.create')
                            ->middleware('permission:products_add_product');

                        Route::get('/activity-logs/{id}', [ProductsController::class, 'getActivityLogs'])->name('products.activity_logs');
                        Route::get('timeline/{id}', [ProductsController::class, 'getTimeline'])->name('products.timeline');

                        Route::get('/stock-movements/{id}', [ProductsController::class, 'getStockMovements'])->name('products.stock_movements');
 Route::post('indirectcosts/get-restrictions-by-date', [IndirectCostsController::class, 'getRestrictionsByDate'])
        ->name('indirectcosts.getRestrictionsByDate');
                        Route::get('/get-sub-units', [ProductsController::class, 'getSubUnits'])->name('products.getSubUnits');
                        Route::get('/compiled', [ProductsController::class, 'compiled'])->name('products.compiled'); // عرض اضاقة منتج تجميعي
                        Route::post('/compiled', [ProductsController::class, 'compiled_store'])->name('products.compiled_store'); // اضافة منتج تجميعي
                        Route::get('/create/services', [ProductsController::class, 'create_services'])
                            ->name('products.create_services')
                            ->middleware('permission:products_add_product'); // عرض صفحة اضافة خدمة
                        Route::get('/show/{id}', [ProductsController::class, 'show'])
                            ->name('products.show')
                            ->middleware('permission:products_view_all_products');
                        Route::get('/edit/{id}', [ProductsController::class, 'edit'])
                            ->name('products.edit')
                            ->middleware('permission:products_edit_delete_all_products');
                        Route::get('/manual_stock_adjust/{id}', [ProductsController::class, 'manual_stock_adjust'])->name('products.manual_stock_adjust');
                        Route::post('/store', [ProductsController::class, 'store'])
                            ->name('products.store')
                            ->middleware('permission:products_add_product');
                        Route::post('/add/manual_stock_adjust/{id}', [ProductsController::class, 'add_manual_stock_adjust'])->name('products.add_manual_stock_adjust');
                        Route::put('/update/{id}', [ProductsController::class, 'update'])
                            ->name('products.update')
                            ->middleware('permission:products_edit_delete_all_products');
                        Route::delete('/delete/{id}', [ProductsController::class, 'delete'])
                            ->name('products.delete')
                            ->middleware('permission:products_edit_delete_all_products');
                        Route::get('/search', [ProductsController::class, 'search'])->name('products.search');
                        Route::post('/import', [ProductsController::class, 'import'])->name('products.import');
                        Route::get('/getcategories', [ProductsController::class, 'categories'])->name('get-categories');
                    });

                    #category routes
                    Route::prefix('category')->group(function () {
                        Route::get('/create', [CategoryController::class, 'create'])->name('category.create');
                        Route::get('/edit/{id}', [CategoryController::class, 'edit'])->name('category.edit');
                        Route::post('/store', [CategoryController::class, 'store'])->name('category.store');
                        Route::put('/update/{id}', [CategoryController::class, 'update'])->name('category.update');
                        Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('category.delete');
                    });

                    #storehouse routes
                    Route::prefix('storehouse')->group(function () {
                        Route::get('/index', [StorehouseController::class, 'index'])->name('storehouse.index');
                        Route::get('/create', [StorehouseController::class, 'create'])->name('storehouse.create');
                        Route::get('/show/{id}', [StorehouseController::class, 'show'])->name('storehouse.show');
                        Route::get('/edit/{id}', [StorehouseController::class, 'edit'])->name('storehouse.edit');
                        Route::post('/store', [StorehouseController::class, 'store'])->name('storehouse.store');
                        Route::post('/update/{id}', [StorehouseController::class, 'update'])->name('storehouse.update');
                        Route::get('/delete/{id}', [StorehouseController::class, 'delete'])->name('storehouse.delete');
                        Route::get('/summary/inventory_operations/{id}', [StorehouseController::class, 'summary_inventory_operations'])->name('storehouse.summary_inventory_operations');
                        Route::get('/inventory_value/{id}', [StorehouseController::class, 'inventory_value'])->name('storehouse.inventory_value');
                        Route::get('/inventory_sheet/{id}', [StorehouseController::class, 'inventory_sheet'])->name('storehouse.inventory_sheet');
                    });

                    #price lists routes
                    Route::prefix('price_list')->group(function () {
                        Route::get('/index', [PriceListController::class, 'index'])
                            ->name('price_list.index')
                            ->middleware('permission:products_view_price_groups');
                        Route::get('/create', [PriceListController::class, 'create'])
                            ->name('price_list.create')
                            ->middleware('permission:products_add_edit_price_groups');
                        Route::get('/show/{id}', [PriceListController::class, 'show'])
                            ->name('price_list.show')
                            ->middleware('permission:products_add_edit_price_groups');
                        Route::get('/edit/{id}', [PriceListController::class, 'edit'])
                            ->name('price_list.edit')
                            ->middleware('permission:products_add_edit_price_groups');
                        Route::post('/store', [PriceListController::class, 'store'])
                            ->name('price_list.store')
                            ->middleware('permission:products_add_edit_price_groups');
                        Route::post('/update/{id}', [PriceListController::class, 'update'])
                            ->name('price_list.update')
                            ->middleware('permission:products_add_edit_price_groups');
                        Route::get('/delete/{id}', [PriceListController::class, 'delete'])
                            ->name('price_list.delete')
                            ->middleware('permission:products_delete_price_groups');
                        Route::get('/delete_product/{id}', [PriceListController::class, 'delete_product'])
                            ->name('price_list.delete_product')
                            ->middleware('permission:products_delete_price_groups');
                        Route::post('/add_product/{id}', [PriceListController::class, 'add_product'])
                            ->name('price_list.add_product')
                            ->middleware('permission:products_add_product');
                    });

                    #price inventory settings routes
                    Route::prefix('inventory_settings')->group(function () {
                        Route::get('/index', [InventorySettingsController::class, 'index'])->name('inventory_settings.index');
                        Route::get('/general', [InventorySettingsController::class, 'general'])->name('inventory_settings.general');
                        Route::post('/store', [InventorySettingsController::class, 'store'])->name('inventory_settings.store');
                        Route::get('/employee_default_warehouse', [InventorySettingsController::class, 'employee_default_warehouse'])->name('inventory_settings.employee_default_warehouse');
                        Route::get('/employee_default_warehouse_create', [InventorySettingsController::class, 'employee_default_warehouse_create'])->name('inventory_settings.employee_default_warehouse_create');
                        Route::post('/employee_default_warehouse_store', [InventorySettingsController::class, 'employee_default_warehouse_store'])->name('inventory_settings.employee_default_warehouse_store');
                        Route::get('/employee_default_warehouse_delete/{id}', [InventorySettingsController::class, 'employee_default_warehouse_delete'])->name('inventory_settings.employee_default_warehouse_delete');
                        Route::get('/employee_default_warehouse_show/{id}', [InventorySettingsController::class, 'employee_default_warehouse_show'])->name('inventory_settings.employee_default_warehouse_show');
                        Route::get('/employee_default_warehouse_edit/{id}', [InventorySettingsController::class, 'employee_default_warehouse_edit'])->name('inventory_settings.employee_default_warehouse_edit');
                        Route::post('/employee_default_warehouse_update/{id}', [InventorySettingsController::class, 'employee_default_warehouse_update'])->name('inventory_settings.employee_default_warehouse_update');
                    });

                    #price product settings routes
                    Route::prefix('product_settings')->group(function () {
                        Route::get('/index', [ProductsSettingsController::class, 'index'])->name('product_settings.index');
                        Route::get('/category', [ProductsSettingsController::class, 'category'])->name('product_settings.category');
                        Route::get('/default-taxes', [ProductsSettingsController::class, 'default_taxes'])->name('product_settings.default_taxes');
                        Route::get('/barcode-settings', [ProductsSettingsController::class, 'barcode_settings'])->name('product_settings.barcode_settings');
                    });

                    #template unit
                    Route::prefix('template_unit')->group(function () {
                        Route::get('/index', [TemplateUnitController::class, 'index'])->name('template_unit.index');
                        Route::get('/create', [TemplateUnitController::class, 'create'])->name('template_unit.create');
                        Route::post('/store', [TemplateUnitController::class, 'store'])->name('template_unit.store');
                        Route::get('/edit/{id}', [TemplateUnitController::class, 'edit'])->name('template_unit.edit');
                        Route::post('/update/{id}', [TemplateUnitController::class, 'update'])->name('template_unit.update');
                        Route::get('/delete/{id}', [TemplateUnitController::class, 'delete'])->name('template_unit.delete');
                        Route::get('/show/{id}', [TemplateUnitController::class, 'show'])->name('template_unit.show');
                        Route::get('/updateStatus/{id}', [TemplateUnitController::class, 'updateStatus'])->name('template_unit.updateStatus');
                    });

                    #price inventory Management routes
                    Route::prefix('inventory_management')->group(function () {
                        Route::get('/index', [InventoryManagementController::class, 'index'])->name('inventory_management.index');
                        Route::get('/create', [InventoryManagementController::class, 'create'])->name('inventory_management.create');
                        Route::get('/inventory/do/{id}', [InventoryManagementController::class, 'doStock'])->name('inventory.do_stock');
                        Route::post('/inventory/store', [InventoryManagementController::class, 'store'])->name('inventory.store');
                        Route::post('/inventory/save/{id}', [InventoryManagementController::class, 'saveFinal'])->name('inventory.save_final');
                        Route::get('/inventory/show/{id}', [InventoryManagementController::class, 'show'])->name('inventory.show');
                        Route::get('/inventory/adjustment/{id}', [InventoryManagementController::class, 'adjustment'])->name('inventory.adjustment');
                        Route::get('/inventory/cancel/adjustment/{id}', [InventoryManagementController::class, 'Canceladjustment'])->name('inventory.Canceladjustment');
                        Route::get('/inventory/edit/{id}', [InventoryManagementController::class, 'edit'])->name('inventory.edit');
                        Route::put('/inventory/edit/{id}', [InventoryManagementController::class, 'update'])->name('inventory.update');
                    });

                    #price Store permits Management routes
                    Route::prefix('store_permits_management')->group(function () {
                        // الروتات الأساسية الموجودة
                        Route::get('/index', [StorePermitsManagementController::class, 'index'])->name('store_permits_management.index');
                        Route::get('/show/{id}', [StorePermitsManagementController::class, 'show'])->name('store_permits_management.show');
                        Route::get('/create', [StorePermitsManagementController::class, 'create'])->name('store_permits_management.create');
                        Route::get('/manual_disbursement', [StorePermitsManagementController::class, 'manual_disbursement'])->name('store_permits_management.manual_disbursement');
                        Route::get('/manual_conversion', [StorePermitsManagementController::class, 'manual_conversion'])->name('store_permits_management.manual_conversion');
                        Route::post('/store', [StorePermitsManagementController::class, 'store'])->name('store_permits_management.store');
                        Route::get('/edit/{id}', [StorePermitsManagementController::class, 'edit'])->name('store_permits_management.edit');
                        Route::post('/update/{id}', [StorePermitsManagementController::class, 'update'])->name('store_permits_management.update');
                        Route::delete('/delete/{id}', [StorePermitsManagementController::class, 'delete'])->name('store_permits_management.delete');
                        Route::post('/{id}/approve', [StorePermitsManagementController::class, 'approve'])->name('store_permits_management.approve');

                        Route::post('/{id}/reject', [StorePermitsManagementController::class, 'reject'])->name('store_permits_management.reject');

                        Route::get('/go', [StorePermitsManagementController::class, 'go'])->name('store_permits_management.go');

                        // AJAX للمخزون
                        Route::get('/get-product-stock/{storeId}/{productId}', [StorePermitsManagementController::class, 'getProductStock'])->name('store_permits_management.getProductStock');

                        // ✨ Routes الجديدة للموافقة والرفض
                        Route::post('/approve/{id}', [StorePermitsManagementController::class, 'approve'])->name('store_permits_management.approve');
                        Route::post('/reject/{id}', [StorePermitsManagementController::class, 'reject'])->name('store_permits_management.reject');

                        // Routes إضافية للتقارير والإحصائيات
                        Route::get('/pending', [StorePermitsManagementController::class, 'pendingPermits'])->name('store_permits_management.pending');
                        Route::get('/approved', [StorePermitsManagementController::class, 'approvedPermits'])->name('store_permits_management.approved');
                        Route::get('/rejected', [StorePermitsManagementController::class, 'rejectedPermits'])->name('store_permits_management.rejected');

                        // تصدير البيانات
                        Route::get('/export', [StorePermitsManagementController::class, 'export'])->name('store_permits_management.export');
                        Route::get('/print/{id}', [StorePermitsManagementController::class, 'print'])->name('store_permits_management.print');

                        // إحصائيات سريعة
                        Route::get('/stats', [StorePermitsManagementController::class, 'getStats'])->name('store_permits_management.stats');
                    });
                    # قوائم مواد الأنتاج
                    Route::prefix('BOM')->group(function () {
                        Route::get('/index', [BOMController::class, 'index'])->name('BOM.index');
 Route::get('/data', [BOMController::class, 'getData'])->name('BOM.getData');
                        Route::get('/create', [BOMController::class, 'create'])->name('BOM.create');
                        Route::get('/edit/{id}', [BOMController::class, 'edit'])->name('Bom.edit');
                        Route::get('/show/{id}', [BOMController::class, 'show'])->name('Bom.show');
                        Route::post('/store', [BOMController::class, 'store'])->name('Bom.store');

                        Route::put('/update/{id}', [BOMController::class, 'update'])->name('Bom.update');
                        Route::post('/updatePassword/{id}', [BOMController::class, 'updatePassword'])->name('Bom.updatePassword');
                        Route::delete('/delete/{id}', [BOMController::class, 'destroy'])->name('Bom.destroy');
                        Route::get('/login/to/{id}', [BOMController::class, 'login_to'])->name('Bom.login_to');
                        Route::get('/export/view', [BOMController::class, 'export_view'])->name('Bom.export_view');
                        Route::post('/export', [BOMController::class, 'export'])->name('Bom.export');
                    });

                    # أوامر التصنيع
                    Route::prefix('manufacturing/orders')->group(function () {
                        Route::get('/index', [ManufacturingOrdersController::class, 'index'])->name('manufacturing.orders.index');
                        Route::get('/create', [ManufacturingOrdersController::class, 'create'])->name('manufacturing.orders.create');
  Route::get('/ajax/data', [ManufacturingOrdersController::class, 'getData'])->name('manufacturing.orders.data');
 Route::get('/finish/{id}', [ManufacturingOrdersController::class, 'finish'])->name('manufacturing.orders.finish');

    // Route لمعالجة الإنهاء
    Route::post('/finish/{id}', [ManufacturingOrdersController::class, 'finishOrder'])->name('manufacturing.orders.finish.process');

    // باقي الـ routes
    Route::post('/{id}/undo-completion', [ManufacturingOrdersController::class, 'undoCompletion'])->name('manufacturing.orders.undo-completion');
    Route::post('/{id}/close', [ManufacturingOrdersController::class, 'closeOrder'])->name('manufacturing.orders.close');
    Route::post('/{id}/reopen', [ManufacturingOrdersController::class, 'reopenOrder'])->name('manufacturing.orders.reopen');
        Route::post('/{id}/add-note', [ManufacturingOrdersController::class, 'addNote'])->name('manufacturing.orders.addNote');
    Route::get('/{id}/get-notes', [ManufacturingOrdersController::class, 'getNotes'])->name('manufacturing.orders.getNotes');
    Route::delete('/note/{noteId}', [ManufacturingOrdersController::class, 'deleteNote'])->name('manufacturing.orders.deleteNote');
 // ملاحظات الأوامر المحددة
    Route::post('/{id}/add-note', [ManufacturingOrdersController::class, 'addNote'])->name('manufacturing.orders.addNote');
    Route::get('/{id}/get-notes', [ManufacturingOrdersController::class, 'getNotes'])->name('manufacturing.orders.getNotes');
    Route::delete('/delete-note/{noteId}', [ManufacturingOrdersController::class, 'deleteNote'])->name('manufacturing.orders.deleteNote');

    // الملاحظات العامة
    Route::post('/add-general-note', [ManufacturingOrdersController::class, 'addNote'])->name('manufacturing.orders.addGeneralNote');
    Route::get('/get-general-notes', [ManufacturingOrdersController::class, 'getNotes'])->name('manufacturing.orders.getGeneralNotes');
    Route::delete('/delete-general-note/{noteId}', [ManufacturingOrdersController::class, 'deleteNote'])->name('manufacturing.orders.deleteGeneralNote');
        Route::get('/ajax/filter-options', [ManufacturingOrdersController::class, 'getFilterOptions'])->name('manufacturing.orders.filter-options');
                        Route::post('/store', [ManufacturingOrdersController::class, 'store'])->name('manufacturing.orders.store');
                        Route::get('/edit/{id}', [ManufacturingOrdersController::class, 'edit'])->name('manufacturing.orders.edit');
                        Route::get('/show/{id}', [ManufacturingOrdersController::class, 'show'])->name('manufacturing.orders.show');
                        Route::post('/update/{id}', [ManufacturingOrdersController::class, 'update'])->name('manufacturing.orders.update');
                        Route::get('/delete/{id}', [ManufacturingOrdersController::class, 'delete'])->name('manufacturing.orders.delete');
                    });

                    # التكاليف غير المباشرة
                    Route::prefix('IndirectCosts')->group(function () {
                        Route::get('/index', [IndirectCostsController::class, 'index'])->name('manufacturing.indirectcosts.index');
                        Route::get('/create', [IndirectCostsController::class, 'create'])->name('manufacturing.indirectcosts.create');
                        Route::post('/store', [IndirectCostsController::class, 'store'])->name('manufacturing.indirectcosts.store');
                        Route::get('/edit/{id}', [IndirectCostsController::class, 'edit'])->name('manufacturing.indirectcosts.edit');
                        Route::put('/update/{id}', [IndirectCostsController::class, 'update'])->name('manufacturing.indirectcosts.update');
                        Route::delete('/delete/{id}', [IndirectCostsController::class, 'destroy'])->name('manufacturing.indirectcosts.destroy');
 Route::get('indirectcosts-data', [IndirectCostsController::class, 'getData'])
        ->name('manufacturing.indirectcosts.getData');

    Route::get('indirectcosts-stats', [IndirectCostsController::class, 'getStats'])
        ->name('manufacturing.indirectcosts.getStats');

    Route::post('indirectcosts-export', [IndirectCostsController::class, 'export'])
        ->name('manufacturing.indirectcosts.export');
 Route::post('indirectcosts/get-restrictions-by-date', [IndirectCostsController::class, 'getRestrictionsByDate'])
        ->name('manufacturing.indirectcosts.getRestrictionsByDate');
                        Route::get('/show/{id}', [IndirectCostsController::class, 'show'])->name('manufacturing.indirectcosts.show');
                    });

                    # محطات العمل
                    Route::prefix('Workstations')->group(function () {
                        Route::get('/index', [WorkstationsController::class, 'index'])->name('manufacturing.workstations.index');
                        Route::get('/create', [WorkstationsController::class, 'create'])->name('manufacturing.workstations.create');
  Route::get('/data', [WorkStationsController::class, 'getData'])->name('manufacturing.workstations.getData');
                        Route::post('/store', [WorkstationsController::class, 'store'])->name('manufacturing.workstations.store');
                        Route::get('/edit/{id}', [WorkstationsController::class, 'edit'])->name('manufacturing.workstations.edit');
                        Route::post('/update/{id}', [WorkstationsController::class, 'update'])->name('manufacturing.workstations.update');
                        Route::delete('/delete/{id}', [WorkstationsController::class, 'destroy'])->name('manufacturing.workstations.delete');
                        Route::get('/show/{id}', [WorkstationsController::class, 'show'])->name('manufacturing.workstations.show');
                    });
                    # الأعدادات
                    Route::prefix('Settings')->group(function () {
                        Route::get('/index', [SettingsController::class, 'index'])->name('Manufacturing.settings.index');
                        Route::get('/General', [SettingsController::class, 'General'])->name('manufacturing.settings.general');
                        Route::post('general_settings/update', [SettingsController::class, 'general_settings'])->name('Manufacturing.general_settings.update');
                        Route::get('/Manual', [SettingsController::class, 'Manual'])->name('Manufacturing.settings.Manual');
                        Route::post('order_manual_status/update', [SettingsController::class, 'order_manual_status'])->name('Manufacturing.order_manual_status.update');

                        # الأعدادات مسارات الأنتاج
                        Route::prefix('Paths')->group(function () {
                            Route::get('/index', [SettingsController::class, 'paths_index'])->name('manufacturing.paths.index');
                            Route::post('/manufacturing/paths/ajax', [SettingsController::class, 'paths_ajax'])
    ->name('manufacturing.paths.ajax');
                            Route::get('/create', [SettingsController::class, 'paths_create'])->name('manufacturing.paths.create');
                            Route::post('/store', [SettingsController::class, 'paths_store'])->name('manufacturing.paths.store');
                            Route::get('/edit/{id}', [SettingsController::class, 'paths_edit'])->name('manufacturing.paths.edit');
                            Route::put('/update/{id}', [SettingsController::class, 'paths_update'])->name('manufacturing.paths.update');
                            Route::get('/delete/{id}', [SettingsController::class, 'paths_destroy'])->name('manufacturing.paths.delete');
                            Route::get('/show/{id}', [SettingsController::class, 'paths_show'])->name('manufacturing.paths.show');
                        });
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
                });
        },
    );

    Route::fallback(function () {
        return view('errors.404');
    });
});
