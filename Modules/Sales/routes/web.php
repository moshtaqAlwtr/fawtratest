<?php

use App\Models\Client;
use Illuminate\Support\Facades\Route;
use Modules\Sales\Http\Controllers\CreditNotificationController;
use Modules\Sales\Http\Controllers\GiftOfferController;
use Modules\Sales\Http\Controllers\Installments\InstallmentsController;
use Modules\Sales\Http\Controllers\InvoicesController;
use Modules\Sales\Http\Controllers\OffersController;
use Modules\Sales\Http\Controllers\OrderSourceController;

use Modules\Sales\Http\Controllers\PeriodicInvoicesController;
use Modules\Sales\Http\Controllers\QuoteController;
use Modules\Sales\Http\Controllers\ReturnInvoiceController;
use Modules\Sales\Http\Controllers\RevolvingInvoicesController;
use Modules\Sales\Http\Controllers\SalesPaymentController;
use Modules\Sales\Http\Controllers\ShippingOptionsController;
use Modules\Sales\Http\Controllers\SittingInvoiceController;
use Modules\Sales\Http\Controllers\SupplyOrders\SupplyOrdersController;
use Modules\Sales\Http\Controllers\SupplyOrders\SupplySittingController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('sales')
        ->middleware(['check.branch'])
        ->group(function () {
            // Existing sales routes...

            // GiftOfferController routes
            Route::prefix('gift-offers')->group(function () {
                Route::get('/', [GiftOfferController::class, 'index'])->name('gift-offers.index');
                Route::get('/create', [GiftOfferController::class, 'create'])->name('gift-offers.create');
                Route::post('/create', [GiftOfferController::class, 'store'])->name('gift_offers.store');
                Route::get('/edit/{giftOffer}', [GiftOfferController::class, 'edit'])->name('gift-offers.edit');
                Route::put('/update/{giftOffer}', [GiftOfferController::class, 'update'])->name('gift_offers.update');
                Route::get('/status/{id}', [GiftOfferController::class, 'status'])->name('gift-offers.status');
                Route::get('/show/{id}', [GiftOfferController::class, 'show'])->name('gift-offers.show');
                Route::delete('/destroy/{id}', [GiftOfferController::class, 'destroy'])->name('gift-offers.destroy');
            });

            // OffersController routes
            Route::prefix('offers')->group(function () {
                Route::get('/', [OffersController::class, 'index'])->name('offers.index');
                Route::get('/create', [OffersController::class, 'create'])->name('offers.create');
                Route::post('/store', [OffersController::class, 'store'])->name('offers.store');
                Route::get('/edit/{id}', [OffersController::class, 'edit'])->name('offers.edit');
                Route::put('/update/{id}', [OffersController::class, 'update'])->name('offers.update');
                Route::get('/show/{id}', [OffersController::class, 'show'])->name('offers.show');
                Route::delete('/destroy/{id}', [OffersController::class, 'destroy'])->name('offers.destroy');
            });

            // PaymentProcessController routes
            Route::prefix('payments-client')->group(function () {
                Route::get('/', [SalesPaymentController::class, 'index'])->name('paymentsClient.index');
                Route::get('/create/{id}/{type?}', [SalesPaymentController::class, 'create'])->name('paymentsClient.create');
                Route::post('/store', [SalesPaymentController::class, 'store'])->name('paymentsClient.store');
                Route::get('/show/{id}', [SalesPaymentController::class, 'show'])->name('paymentsClient.show');
                                Route::post('/cancel/{id}', [SalesPaymentController::class, 'cancel'])->name('paymentsClient.cancel');
                Route::get('/edit/{id}', [SalesPaymentController::class, 'edit'])->name('paymentsClient.edit');
                Route::get('/rereceipt/{id}', [SalesPaymentController::class, 'rereceipt'])->name('paymentsClient.rereceipt');
                Route::get('/receipt/pdf/{id}', [SalesPaymentController::class, 'pdfReceipt'])->name('paymentsClient.pdf');
                Route::delete('/destroy/{id}', [SalesPaymentController::class, 'destroy'])->name('paymentsClient.destroy');
                Route::put('/update/{id}', [SalesPaymentController::class, 'update'])->name('paymentsClient.update');
                Route::get('invoice-details/{invoice_id}', [SalesPaymentController::class, 'getInvoiceDetails'])->name('paymentsClient.invoice-details');
            });

            // PeriodicInvoicesController routes
            Route::prefix('periodic-invoices')->group(function () {
                Route::get('/', [PeriodicInvoicesController::class, 'index'])->name('periodic_invoices.index');
                Route::get('/create', [PeriodicInvoicesController::class, 'create'])->name('periodic_invoices.create');
                Route::post('/store', [PeriodicInvoicesController::class, 'store'])->name('periodic_invoices.store');
                Route::get('/show/{id}', [PeriodicInvoicesController::class, 'show'])->name('periodic_invoices.show');
                Route::get('/edit/{id}', [PeriodicInvoicesController::class, 'edit'])->name('periodic_invoices.edit');
                Route::put('/update/{id}', [PeriodicInvoicesController::class, 'update'])->name('periodic_invoices.update');
                Route::delete('/destroy/{id}', [PeriodicInvoicesController::class, 'destroy'])->name('periodic_invoices.destroy');
            });

            // QuoteController routes
            Route::prefix('quotes')->group(function () {
                Route::get('/', [QuoteController::class, 'index'])->name('questions.index');
                Route::get('/send/{id}', [QuoteController::class, 'sendQuoteLink'])->name('questions.email');
                Route::get('/create', [QuoteController::class, 'create'])->name('questions.create');
                Route::get('/show/{id}', [QuoteController::class, 'show'])->name('questions.show');
                Route::get('/edit/{id}', [QuoteController::class, 'edit'])->name('questions.edit');
                Route::get('/logs', [QuoteController::class, 'logsaction'])->name('questions.logs');
                Route::post('/store', [QuoteController::class, 'store'])->name('questions.store');
                Route::put('/update/{id}', [QuoteController::class, 'update'])->name('questions.update');
                Route::post('/convert-to-invoice/{id}', [QuoteController::class, 'convertToInvoice'])->name('questions.convert-to-invoice');
                Route::delete('/delete/{id}', [QuoteController::class, 'destroy'])->name('questions.destroy');
                Route::get('/{id}/pdf', [QuoteController::class, 'downloadPdf'])->name('questions.pdf');
                Route::get('/{quote}/edit-template', [QuoteController::class, 'editTemplate'])->name('questions.template.edit');
                Route::put('/{quote}/update-template', [QuoteController::class, 'updateTemplate'])->name('questions.template.update');
            });
            Route::prefix('invoices')
                ->middleware(['auth'])
                ->group(function () {
                    Route::get('/index', [InvoicesController::class, 'index'])->name('invoices.index');
                    Route::get('/create', [InvoicesController::class, 'create'])->name('invoices.create');
                    Route::post('/verify/code', [InvoicesController::class, 'verify_code'])->name('invoice.verify_code');
                    Route::get('/get-client/{id}', function ($id) {
                        $client = Client::find($id);
                        return response()->json($client);
                    });
                    Route::get('/send/invoice/{id}', [InvoicesController::class, 'send_invoice'])->name('invoices.send');
                    Route::post('/send/verification', [InvoicesController::class, 'sendVerificationCode']);
                    Route::post('/verify-code', [InvoicesController::class, 'verifyCode']);
                    Route::get('/invoices/{id}/label', [InvoicesController::class, 'label'])->name('invoices.label');
                    Route::get('/invoices/{id}/picklist', [InvoicesController::class, 'picklist'])->name('invoices.picklist');
                    Route::get('/invoices/{id}/shipping_label', [InvoicesController::class, 'shipping_label'])->name('invoices.shipping_label');
                    Route::get('/show/{id}', [InvoicesController::class, 'show'])->name('invoices.show');
                    Route::post('/invoices/import', [InvoicesController::class, 'import'])->name('invoices.import');
                    Route::get('/edit/{id}', [InvoicesController::class, 'edit'])->name('invoices.edit');
                    Route::post('/store', [InvoicesController::class, 'store'])->name('invoices.store');
                    Route::delete('/delete/{id}', [InvoicesController::class, 'destroy'])->name('invoices.destroy');
                    Route::get('/{id}/generatePdf', [InvoicesController::class, 'generatePdf'])->name('invoices.generatePdf');

Route::get('/{id}/print', [InvoicesController::class, 'print'])->name('invoices.print');
Route::get('/{id}/markAsPaidSilently', [InvoicesController::class, 'markAsPaidSilently'])->name('invoices.markAsPaidSilently');






                    Route::get('/get-price', [InvoicesController::class, 'getPrice'])->name('get-price');
                    Route::get('/notifications/unread', [InvoicesController::class, 'getUnreadNotifications'])->name('notifications.unread');
                    Route::post('/notifications/mark', [InvoicesController::class, 'markAsRead'])->name('notifications.markAsRead');

                    Route::get('/notifications/mark/show/{id}', [InvoicesController::class, 'markAsReadid'])->name('notifications.markAsReadid');
                    Route::get('/notifications', [InvoicesController::class, 'notifications'])->name('notifications.index');
                    Route::post('/invoices/{invoice}/signatures', [InvoicesController::class, 'storeSignatures'])->name('invoices.signatures.store');
                });

            // ReturnInvoiceController routes
            Route::prefix('return-invoices')->group(function () {
                Route::get('/', [ReturnInvoiceController::class, 'index'])->name('ReturnIInvoices.index');
                Route::get('/create/{id}', [ReturnInvoiceController::class, 'create'])->name('ReturnIInvoices.create');
                Route::get('/show/{id}', [ReturnInvoiceController::class, 'show'])->name('ReturnIInvoices.show');
                Route::get('/{id}/print', [ReturnInvoiceController::class, 'print'])->name('ReturnIInvoices.print');
                Route::get('/send-email/{id}', [ReturnInvoiceController::class, 'sendReturnInvoiceEmail'])->name('ReturnIInvoices.send-email');
                Route::get('/edit/{id}', [ReturnInvoiceController::class, 'edit_brand'])->name('ReturnIInvoices.edit');
                Route::post('/store', [ReturnInvoiceController::class, 'store'])->name('ReturnIInvoices.store');
                Route::put('/update/{id}', [ReturnInvoiceController::class, 'update'])->name('ReturnIInvoices.update');
                Route::delete('/destroy/{id}', [ReturnInvoiceController::class, 'destroy'])->name('ReturnIInvoices.destroy');
            });

            // RevolvingInvoicesController routes
            Route::prefix('revolving-invoices')->group(function () {
                Route::get('/', [RevolvingInvoicesController::class, 'index'])->name('revolving-invoices.index');
                Route::get('/create', [RevolvingInvoicesController::class, 'create'])->name('revolving-invoices.create');
                Route::post('/store', [RevolvingInvoicesController::class, 'store'])->name('revolving-invoices.store');
                Route::get('/show/{id}', [RevolvingInvoicesController::class, 'show'])->name('revolving-invoices.show');
                Route::get('/edit/{id}', [RevolvingInvoicesController::class, 'edit'])->name('revolving-invoices.edit');
                Route::put('/update/{id}', [RevolvingInvoicesController::class, 'update'])->name('revolving-invoices.update');
                Route::delete('/destroy/{id}', [RevolvingInvoicesController::class, 'destroy'])->name('revolving-invoices.destroy');
            });

            // ShippingOptionsController routes
            Route::prefix('shipping-options')->group(function () {
                Route::get('/', [ShippingOptionsController::class, 'index'])->name('shippingOptions.index');
                Route::get('/create', [ShippingOptionsController::class, 'create'])->name('shippingOptions.create');
                Route::post('/store', [ShippingOptionsController::class, 'store'])->name('shippingOptions.store');
                Route::get('/edit/{id}', [ShippingOptionsController::class, 'edit'])->name('shippingOptions.edit');
                Route::put('/update/{id}', [ShippingOptionsController::class, 'update'])->name('shippingOptions.update');
                Route::delete('/destroy/{id}', [ShippingOptionsController::class, 'destroy'])->name('shippingOptions.destroy');
            });

            // SittingInvoiceController routes
            Route::prefix('sitting')->group(function () {
                Route::get('/settingsInvoice', [SittingInvoiceController::class, 'index'])->name('sittingInvoice.index');
                Route::get('/settings/electronic-invoice', [SittingInvoiceController::class, 'electronic_invoice'])->name('settings.electronic_invoice');
                Route::get('/bill-designs', [SittingInvoiceController::class, 'bill_designs'])->name('SittingInvoice.bill-designs');
                Route::get('/create', [SittingInvoiceController::class, 'create'])->name('SittingInvoice.create');
                // Route::get(uri: '/create', [SittingInvoiceController::class, 'create'])->name('templates.create');
                Route::put('/update-sitting', [SittingInvoiceController::class, 'update_invoices'])->name('settings.update_invoices');


                Route::post('/', [SittingInvoiceController::class, 'store'])->name('templates.store');
                Route::get('/{template}/edit', [SittingInvoiceController::class, 'edit'])->name('templates.edit');
                Route::get('/', [SittingInvoiceController::class, 'bill_designs'])->name('SittingInvoice.bill_designs');

                Route::get('/test_print', [SittingInvoiceController::class, 'test_print'])->name('templates.test_print');

                Route::put('/{template}', [SittingInvoiceController::class, 'update'])->name('templates.update');
                Route::post('/template/preview', [SittingInvoiceController::class, 'preview'])->name('template.preview');
                Route::post('/{template}/reset', [SittingInvoiceController::class, 'reset'])->name('templates.reset');
                Route::delete('/{template}', [SittingInvoiceController::class, 'destroy'])->name('templates.destroy');

                Route::post('/', [SittingInvoiceController::class, 'store'])->name('SittingInvoice.store');

                Route::put('/{template}', [SittingInvoiceController::class, 'update'])->name('SittingInvoice.update');
                Route::post('/template/preview', [SittingInvoiceController::class, 'preview'])->name('SittingInvoice.preview');
                Route::post('/{template}/reset', [SittingInvoiceController::class, 'reset'])->name('SittingInvoice.reset');
                Route::delete('/{template}', [SittingInvoiceController::class, 'destroy'])->name('SittingInvoice.destroy');
                Route::get('/invoice', [SittingInvoiceController::class, 'invoice'])->name('SittingInvoice.invoice');
                Route::get('/test-print', [SittingInvoiceController::class, 'test_print'])->name('SittingInvoice.test-print');
                Route::get('/test-print/{id}', [SittingInvoiceController::class, 'print'])->name('SittingInvoice.print');

                // Order Sources routes
                Route::get('/order-sources', [OrderSourceController::class, 'index'])->name('order_sources.index');
                Route::post('/order-sources', [OrderSourceController::class, 'storeOrUpdate'])->name('order_sources.storeOrUpdate');
                Route::delete('/order-sources/{id}', [OrderSourceController::class, 'destroy'])->name('order_sources.destroy');
                Route::post('/order-sources/sort', [OrderSourceController::class, 'sort'])->name('order_sources.sort');
            });

            // CreditNotificationController routes
            Route::prefix('credit-notes')->group(function () {
                Route::get('/', [CreditNotificationController::class, 'index'])->name('CreditNotes.index');
                Route::get('/create', [CreditNotificationController::class, 'create'])->name('CreditNotes.create');
                Route::get('/show/{id}', [CreditNotificationController::class, 'show'])->name('CreditNotes.show');
                Route::get('/send-email/{id}', [CreditNotificationController::class, 'sendCreditNotification'])->name('CreditNotes.send');
                Route::get('/edit/{id}', [CreditNotificationController::class, 'edit'])->name('CreditNotes.edit');
                Route::post('/store', [CreditNotificationController::class, 'store'])->name('CreditNotes.store');
                Route::put('/update/{id}', [CreditNotificationController::class, 'update'])->name('CreditNotes.update');
                Route::delete('/destroy/{id}', [CreditNotificationController::class, 'destroy'])->name('CreditNotes.destroy');
                Route::get('/print/{id}', [CreditNotificationController::class, 'print'])->name('CreditNotes.print');
            });
            Route::prefix('Installments')
                ->middleware(['auth', 'role:manager'])
                ->group(function () {
                    // صفحة الفهرس
                    Route::get('/index', [InstallmentsController::class, 'index'])->name('installments.index');
                    Route::get('/create', [InstallmentsController::class, 'create'])->name('installments.create');
                    Route::post('/store', [InstallmentsController::class, 'store'])->name('installments.store');
                    Route::get('/edit/{id}', [InstallmentsController::class, 'edit'])->name('installments.edit');
                    Route::get('/edit_amount/{id}', [InstallmentsController::class, 'edit_amount'])->name('installments.edit_amount');
                    Route::get('/show_amount/{id}', [InstallmentsController::class, 'show_amount'])->name('installments.show_amount');
                    Route::put('/update/{id}', [InstallmentsController::class, 'update'])->name('installments.update');
                    Route::delete('/destroy/{id}', [InstallmentsController::class, 'destroy'])->name('installments.destroy');
                    Route::get('/show/{id}', [InstallmentsController::class, 'show'])->name('installments.show');
                    Route::get('agreement_installments', [InstallmentsController::class, 'agreement'])->name('installments.agreement_installments');
                });
        });

Route::prefix('SupplyOrders')->group(function () {
            Route::get('/index',[SupplyOrdersController::class,'index'])->name('SupplyOrders.index');
            Route::get('/create',[SupplyOrdersController::class,'create'])->name('SupplyOrders.create');
            Route::get('/show/{id}',[SupplyOrdersController::class,'show'])->name('SupplyOrders.show');
            Route::get('/edit/{id}',[SupplyOrdersController::class,'edit'])->name('SupplyOrders.edit');

            Route::get('/edit_status',[SupplyOrdersController::class,'edit_status'])->name('SupplyOrders.edit_status');
            Route::post('/storeStatus',[SupplyOrdersController::class,'storeStatus'])->name('SupplyOrders.storeStatus');

            Route::post('/store',[SupplyOrdersController::class,'store'])->name('SupplyOrders.store');
            Route::put('/update/{id}',[SupplyOrdersController::class,'update'])->name('SupplyOrders.update');
            Route::delete('/destroy/{id}',[SupplyOrdersController::class,'destroy'])->name('SupplyOrders.destroy');



        });
        Route::prefix('SupplySittings')->group(function () {
            Route::get('/index',[SupplySittingController::class,'index'])->name('SupplySittings.index');
            Route::get('/edit_procedures',[SupplySittingController::class,'edit_procedures'])->name('SupplySittings.edit_procedures');
            Route::get('/edit_supply_number',[SupplySittingController::class,'edit_supply_number'])->name('SupplySittings.edit_supply_number');
            Route::get('/sitting_serial_number',[SupplySittingController::class,'sitting_serial_number'])->name('SupplySittings.sitting_serial_number');




        });
});
