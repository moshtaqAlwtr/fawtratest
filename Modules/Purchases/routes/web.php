<?php

use Illuminate\Support\Facades\Route;
use Modules\Purchases\Http\Controllers\CityNoticesController;
use Modules\Purchases\Http\Controllers\InvoiceSettingsController;
use Modules\Purchases\Http\Controllers\InvoicesPurchaseController;
use Modules\Purchases\Http\Controllers\OrdersPurchaseController;
use Modules\Purchases\Http\Controllers\PurchaseInvoiceSettingsController;
use Modules\Purchases\Http\Controllers\PurchaseOrdersRequestsController;
use Modules\Purchases\Http\Controllers\PurchasePaymentController;
use Modules\Purchases\Http\Controllers\PurchasesController;
use Modules\Purchases\Http\Controllers\QuotationsController;
use Modules\Purchases\Http\Controllers\ReturnsInvoiceController;
use Modules\Purchases\Http\Controllers\SupplierManagementController;
use Modules\Purchases\Http\Controllers\SupplierSettingsController;
use Modules\Purchases\Http\Controllers\ViewPurchasePriceController;


Route::middleware(['auth'])->group(function () {
    //طلبات الشراء
    Route::prefix('Orders')->group(function () {
        Route::get('/index', [OrdersPurchaseController::class, 'index'])->name('OrdersPurchases.index');
        Route::get('/create', [OrdersPurchaseController::class, 'create'])->name('OrdersPurchases.create');
        Route::post('/store', [OrdersPurchaseController::class, 'store'])->name('OrdersPurchases.store');
        Route::get('edit/{id}', [OrdersPurchaseController::class, 'edit'])->name('OrdersPurchases.edit');
        Route::put('update/{id}', [OrdersPurchaseController::class, 'update'])->name('OrdersPurchases.update');
        Route::get('/show/{id}', [OrdersPurchaseController::class, 'show'])->name('OrdersPurchases.show');
        Route::get('/purchase-orders/{id}/pdf', [OrdersPurchaseController::class, 'generatePdf'])->name('OrdersPurchases.pdf');
        Route::post('OrdersPurchases/{id}/approve', [OrdersPurchaseController::class, 'approve'])->name('OrdersPurchases.approve');
        Route::post('OrdersPurchases/{id}/reject', [OrdersPurchaseController::class, 'reject'])->name('OrdersPurchases.reject');
        Route::post('OrdersPurchases/{id}/cancel', [OrdersPurchaseController::class, 'cancelApproval'])->name('OrdersPurchases.cancelApproval');
        Route::post('OrdersPurchases/{id}/undo', [OrdersPurchaseController::class, 'undoRejection'])->name('OrdersPurchases.undoRejection');
        Route::delete('/destroy/{id}', [OrdersPurchaseController::class, 'destroy'])->name('OrdersPurchases.destroy');
    });

    //طلبات عروض الأسعار
    Route::prefix('Quotations')->group(function () {
        Route::get('/index', [QuotationsController::class, 'index'])->name('Quotations.index');
        Route::get('/create', [QuotationsController::class, 'create'])->name('Quotations.create');
        Route::post('/store', [QuotationsController::class, 'store'])->name('Quotations.store');
        Route::get('/edit/{id}', [QuotationsController::class, 'edit'])->name('Quotations.edit');
        Route::put('/update/{id}', [QuotationsController::class, 'update'])->name('Quotations.update');
        Route::post('  Quotations/{id}/approve', [QuotationsController::class, 'approve'])->name('Quotations.approve');
        Route::post('Quotations/{id}/reject', [QuotationsController::class, 'reject'])->name('Quotations.reject');
        Route::delete('/destroy/{id}', [QuotationsController::class, 'destroy'])->name('Quotations.destroy');
        Route::get('/show/{id}', [QuotationsController::class, 'show'])->name('Quotations.show');
        Route::get('/duplicate/{id}', [QuotationsController::class, 'duplicate'])->name('Quotations.duplicate');
    });

    # عروض أسعار المشتريات
    Route::prefix('pricesPurchase')->group(function () {
        Route::get('/index', [ViewPurchasePriceController::class, 'index'])->name('pricesPurchase.index');
        Route::get('/create', [ViewPurchasePriceController::class, 'create'])->name('pricesPurchase.create');
        Route::post('/store', [ViewPurchasePriceController::class, 'store'])->name('pricesPurchase.store');
        Route::get('/edit/{id}', [ViewPurchasePriceController::class, 'edit'])->name('pricesPurchase.edit');
        Route::put('/update/{id}', [ViewPurchasePriceController::class, 'update'])->name('pricesPurchase.update');
        Route::delete('/destroy/{id}', [ViewPurchasePriceController::class, 'destroy'])->name('pricesPurchase.destroy');
        Route::get('/show/{id}', [ViewPurchasePriceController::class, 'show'])->name('pricesPurchase.show');
        Route::get('pricesPurchase/{id}/pdf', [ViewPurchasePriceController::class, 'exportPDF'])->name('pricesPurchase.pdf');

        // Route::get('/purchase-orders/{id}/pdf', [OrdersPurchaseController::class, 'generatePdf'])->name('OrdersPurchases.pdf');
        Route::post('view-purchase-price/{id}/approve', [ViewPurchasePriceController::class, 'approve'])->name('pricesPurchase.approve');
        Route::post('view-purchase-price/{id}/reject', [ViewPurchasePriceController::class, 'reject'])->name('pricesPurchase.reject');
        Route::post('view-purchase-price/{id}/cancel', [ViewPurchasePriceController::class, 'cancelApproval'])->name('pricesPurchase.cancelApproval');
        Route::post('view-purchase-price/{id}/undo', [ViewPurchasePriceController::class, 'undoRejection'])->name('pricesPurchase.undoRejection');
        Route::post('view-purchase-price/{id}/add-note', [ViewPurchasePriceController::class, 'addNote'])->name('pricesPurchase.addNote');
        Route::get('view-purchase-price/{id}/notes', [ViewPurchasePriceController::class, 'getNotes'])->name('pricesPurchase.getNotes');
        Route::delete('view-purchase-price/note/{noteId}', [ViewPurchasePriceController::class, 'destroyNote'])->name('pricesPurchase.deleteNote');
        Route::post('pricesPurchase/{id}/convert-to-po', [ViewPurchasePriceController::class, 'convertToPurchaseOrder'])->name('pricesPurchase.convertToPurchaseOrder');
        Route::put('pricesPurchase/{id}/status', [ViewPurchasePriceController::class, 'updateStatus'])->name('pricesPurchase.updateStatus');
    });

    //  أوامر الشراء
    Route::prefix('OrdersRequest')->group(function () {
        Route::get('/index', [PurchaseOrdersRequestsController::class, 'index'])->name('OrdersRequests.index');
        Route::get('/create', [PurchaseOrdersRequestsController::class, 'create'])->name('OrdersRequests.create');
        Route::post('/store', [PurchaseOrdersRequestsController::class, 'store'])->name('OrdersRequests.store');
        Route::get('/edit/{id}', [PurchaseOrdersRequestsController::class, 'edit'])->name('OrdersRequests.edit');
        Route::put('/update/{id}', [PurchaseOrdersRequestsController::class, 'update'])->name('OrdersRequests.update');
        Route::delete('/destroy/{id}', [PurchaseOrdersRequestsController::class, 'destroy'])->name('OrdersRequests.destroy');
        Route::put('orders-requests/{id}/update-status', [PurchaseOrdersRequestsController::class, 'updateStatus'])->name('OrdersRequests.updateStatus');
        Route::get('/show/{id}', [PurchaseOrdersRequestsController::class, 'show'])->name('OrdersRequests.show');
        Route::post('orders-requests/{id}/approve', [PurchaseOrdersRequestsController::class, 'approve'])->name('OrdersRequests.approve');
        Route::post('orders-requests/{id}/reject', [PurchaseOrdersRequestsController::class, 'reject'])->name('OrdersRequests.reject');
        Route::post('orders-requests/{id}/cancel', [PurchaseOrdersRequestsController::class, 'cancelApproval'])->name('OrdersRequests.cancelApproval');
        Route::post('orders-requests/{id}/undo', [PurchaseOrdersRequestsController::class, 'undoRejection'])->name('OrdersRequests.undoRejection');
        Route::post('orders-requests/{id}/convert', [PurchaseOrdersRequestsController::class, 'convertToInvoice'])->name('OrdersRequests.convertToInvoice');
 Route::post('/{id}/notes', [PurchaseOrdersRequestsController::class, 'addNote'])->name('OrdersRequests.addNote');
    Route::get('/{id}/notes', [PurchaseOrdersRequestsController::class, 'getNotes'])->name('OrdersRequests.getNotes');
    Route::delete('/notes/{noteId}', [PurchaseOrdersRequestsController::class, 'deleteNote'])->name('OrdersRequests.deleteNote');

    });
    //  فواتير الشراء
    Route::prefix('invoicePurchases.index')->group(function () {
        Route::get('/index', [InvoicesPurchaseController::class, 'index'])->name('invoicePurchases.index');
        Route::get('/create', [InvoicesPurchaseController::class, 'create'])->name('invoicePurchases.create');
        Route::post('/store', [InvoicesPurchaseController::class, 'store'])->name('invoicePurchases.store');
        Route::get('/edit/{id}', [InvoicesPurchaseController::class, 'edit'])->name('invoicePurchases.edit');
        Route::put('/update/{id}', [InvoicesPurchaseController::class, 'update'])->name('invoicePurchases.update');
        Route::delete('/destroy/{id}', [InvoicesPurchaseController::class, 'destroy'])->name('invoicePurchases.destroy');
        Route::get('/show/{id}', [InvoicesPurchaseController::class, 'show'])->name('invoicePurchases.show');

    Route::post('/{id}/mark-as-pending', [InvoicesPurchaseController::class, 'markAsPending'])->name('invoicePurchases.markAsPending');
    Route::post('/{id}/mark-as-approved', [InvoicesPurchaseController::class, 'markAsApproved'])->name('invoicePurchases.markAsApproved');
    Route::post('/{id}/mark-as-paid', [InvoicesPurchaseController::class, 'markAsPaid'])->name('invoicePurchases.markAsPaid');
    Route::post('/{id}/mark-as-cancelled', [InvoicesPurchaseController::class, 'markAsCancelled'])->name('invoicePurchases.markAsCancelled');
    Route::post('/{id}/update-payment-status', [InvoicesPurchaseController::class, 'updatePaymentStatus'])->name('invoicePurchases.updatePaymentStatus');;

    // مسارات الملاحظات
    Route::post('/{id}/add-note', [InvoicesPurchaseController::class, 'addNote'])->name('invoicePurchases.addNote');
    Route::get('/{id}/get-notes', [InvoicesPurchaseController::class, 'getNotes'])->name('invoicePurchases.getNotes');
    Route::delete('/note/{noteId}', [InvoicesPurchaseController::class, 'deleteNote'])->name('invoicePurchases.deleteNote');

    // مسارات إضافية
    Route::get('/{id}/copy', [InvoicesPurchaseController::class, 'copy'])->name('copy');
    Route::get('/{id}/assign-cost-center', [InvoicesPurchaseController::class, 'assignCostCenter'])->name('invoicePurchases.assignCostCenter');
    Route::get('/{id}/export-pdf', [InvoicesPurchaseController::class, 'exportPDF'])->name('exportPDF');
    Route::get('/{id}/export-excel', [InvoicesPurchaseController::class, 'exportExcel'])->name('exportExcel');
    Route::get('/{id}/print', [InvoicesPurchaseController::class, 'print'])->name('print');

        Route::get('invoicePurchases/{id}/pdf', [InvoicesPurchaseController::class, 'exportPDF'])->name('invoicePurchases.pdf');
        Route::post('invoicePurchases/{id}/convert-to-credit-memo', [InvoicesPurchaseController::class, 'convertToCreditMemo'])->name('invoicePurchases.convertToCreditMemo');
    });
    //  مرتجعات المشتريات
    Route::prefix('ReturnsInvoice')->group(function () {
        Route::get('/index', [ReturnsInvoiceController::class, 'index'])->name('ReturnsInvoice.index');
        Route::get('/create', [ReturnsInvoiceController::class, 'create'])->name('ReturnsInvoice.create');
        Route::get('/create-from-invoice/{invoiceId}', [ReturnsInvoiceController::class, 'createFromInvoice'])->name('ReturnsInvoice.createFromInvoice');
        Route::get('/get-invoice-details/{invoiceId}', [ReturnsInvoiceController::class, 'getInvoiceDetails'])->name('ReturnsInvoice.getInvoiceDetails');
        Route::post('/store', [ReturnsInvoiceController::class, 'store'])->name('ReturnsInvoice.store');
        Route::get('/edit/{id}', [ReturnsInvoiceController::class, 'edit'])->name('ReturnsInvoice.edit');
        Route::put('/update/{id}', [ReturnsInvoiceController::class, 'update'])->name('ReturnsInvoice.update');
        Route::delete('/destroy/{id}', [ReturnsInvoiceController::class, 'destroy'])->name('ReturnsInvoice.destroy');
        Route::get('/show/{id}', [ReturnsInvoiceController::class, 'show'])->name('ReturnsInvoice.show');
    });
    //  الأشعارات المدينة
    Route::prefix('CityNotices')->group(function () {
        Route::get('/index', [CityNoticesController::class, 'index'])->name('CityNotices.index');
        Route::get('/create', [CityNoticesController::class, 'create'])->name('CityNotices.create');
        Route::post('/store', [CityNoticesController::class, 'store'])->name('CityNotices.store');
        Route::get('/edit/{id}', [CityNoticesController::class, 'edit'])->name('CityNotices.edit');
        Route::put('/update/{id}', [CityNoticesController::class, 'update'])->name('CityNotices.update');
        Route::delete('/destroy/{id}', [CityNoticesController::class, 'destroy'])->name('CityNotices.destroy');
        Route::get('/show/{id}', [CityNoticesController::class, 'show'])->name('CityNotices.show');
        Route::get('/create-from-invoice/{invoiceId}', [CityNoticesController::class, 'createFromInvoice'])->name('CreditNotes.createFromInvoice');
        Route::get('/get-invoice-details/{invoiceId}', [CityNoticesController::class, 'getInvoiceDetails'])->name('CityNotices.getInvoiceDetails');
    });
    //  أدارة الموردين
    Route::prefix('SupplierManagement')->group(function () {
        Route::get('/index', [SupplierManagementController::class, 'index'])->name('SupplierManagement.index');
        Route::get('/create', [SupplierManagementController::class, 'create'])->name('SupplierManagement.create');
        Route::post('/store', [SupplierManagementController::class, 'store'])->name('SupplierManagement.store');
        Route::get('/edit/{id}', [SupplierManagementController::class, 'edit'])->name('SupplierManagement.edit');
        Route::put('/update/{id}', [SupplierManagementController::class, 'update'])->name('SupplierManagement.update');
        Route::delete('/destroy/{id}', [SupplierManagementController::class, 'destroy'])->name('SupplierManagement.destroy');
        Route::get('/show/{id}', [SupplierManagementController::class, 'show'])->name('SupplierManagement.show');

        Route::post('/suppliers/{id}/update-opening-balance', [SupplierManagementController::class, 'updateSupplierOpeningBalance'])->name('SupplierManagement.updateOpeningBalance');
        Route::get('/suppliers/{id}/statement', [SupplierManagementController::class, 'supplierStatement'])->name('SupplierManagement.statement');
        Route::post('/{id}/update-status', [SupplierManagementController::class, 'updateStatus'])->name('SupplierManagement.updateStatus');

        // Route للإحصائيات (اختياري)
        Route::get('/stats/overview', [SupplierManagementController::class, 'getStats'])->name('stats');
    });
    //  مدفوعات الموردين
    // Route::prefix('Supplier_Payments')->group(function () {
    //     Route::get('/index', [SupplierPaymentsController::class, 'index'])->name('purchases.supplier_payments.index');
    //     Route::get('/create', [SupplierPaymentsController::class, 'create'])->name('purchases.supplier_payments.create');
    // });
    //  أعدادات فواتير الشراء
    Route::prefix('invoice_settings')->group(function () {
        Route::get('/index', [InvoiceSettingsController::class, 'index'])->name('purchases.invoice_settings.index');
        Route::get('/create', [InvoiceSettingsController::class, 'create'])->name('purchases.invoice_settings.create');
    });
    //  أعدادات الموردين
    Route::prefix('Supplier_Settings')->group(function () {
        Route::get('/index', [SupplierSettingsController::class, 'index'])->name('purchases.supplier_settings.index');
        Route::get('/create', [SupplierSettingsController::class, 'create'])->name('purchases.supplier_settings.create');
    });
    Route::prefix('PaymentSupplier')->group(function () {
        Route::get('/indexPurchase', [PurchasePaymentController::class, 'index'])->name('PaymentSupplier.indexPurchase');
        Route::get('/createPurchase/{id}', [PurchasePaymentController::class, 'create'])->name('PaymentSupplier.createPurchase');
        Route::post('/storePurchase', [PurchasePaymentController::class, 'store'])->name('PaymentSupplier.storePurchase');
        Route::get('/showSupplierPayment/{id}', [PurchasePaymentController::class, 'show'])->name('PaymentSupplier.showSupplierPayment');
        Route::get('/editSupplierPayment/{id}', [PurchasePaymentController::class, 'edit'])->name('PaymentSupplier.editSupplierPayment');
        Route::put('/updateSupplierPayment/{id}', [PurchasePaymentController::class, 'update'])->name('PaymentSupplier.updateSupplierPayment');
        Route::delete('/destroySupplierPayment/{id}', [PurchasePaymentController::class, 'destroy'])->name('PaymentSupplier.destroySupplierPayment');
        Route::get('/get-invoice-details/{invoice_id}', [PurchasePaymentController::class, 'getInvoiceDetails'])->name('PaymentSupplier.getInvoiceDetails');
    });

    Route::get('/purchase-invoices/settings', [PurchaseInvoiceSettingsController::class, 'index'])
     ->name('purchase_invoices.settings');

Route::put('/purchase-invoices/settings', [PurchaseInvoiceSettingsController::class, 'update'])
     ->name('purchase_invoices.settings.update');
});
