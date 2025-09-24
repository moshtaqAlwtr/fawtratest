<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use Modules\Reports\Http\Controllers\ChecksController;
use Modules\Reports\Http\Controllers\Customers\CustomerReportController;
use Modules\Reports\Http\Controllers\EmployeeReportController;
use Modules\Reports\Http\Controllers\GeneralAccountsController;
use Modules\Reports\Http\Controllers\Inventory\InventoryReportController;
use Modules\Reports\Http\Controllers\PurchasesReportController;
use Modules\Reports\Http\Controllers\RentalsController;
use Modules\Reports\Http\Controllers\SalesReportsController;

use Modules\Reports\Http\Controllers\SupplyOrdersReportController;
use Modules\Reports\Http\Controllers\TrialBalanceController;
use Modules\Reports\Http\Controllers\UnitTrackReport;

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'check.branch'],
    ],
    function () {
        Route::prefix('Reports')
            ->middleware(['auth'])
            ->group(function () {
                Route::get('/index', [PurchasesReportController::class, 'index'])->name('ReportsPurchases.index');
            });
        Route::prefix('ClientReport')->group(function () {
            Route::get('/', [CustomerReportController::class, 'index'])->name('ClientReport.index');
            Route::get('/debtReconstructionInv', [CustomerReportController::class, 'debtReconstructionInv'])->name('ClientReport.debtReconstructionInv');

            Route::get('/clients/search', [CustomerReportController::class, 'searchClients'])->name('ClientReport.searchClients');
            Route::get('/get-employee-groups', [CustomerReportController::class, 'getEmployeeGroups'])->name('ClientReport.getEmployeeGroups');

            Route::get('/get-group-clients', [CustomerReportController::class, 'getGroupClients'])->name('ClientReport.getGroupClients');

            Route::get('/invoice-debt-aging/data', [CustomerReportController::class, 'invoiceDebtAgingAjax'])->name('ClientReport.invoiceDebtAgingAjax');

            Route::post('/invoice-debt-aging/export', [CustomerReportController::class, 'exportInvoiceDebtAging'])->name('ClientReport.exportInvoiceDebtAging');

            Route::get('/debt-aging-general-ledger', [CustomerReportController::class, 'debtAgingGeneralLedger'])->name('ClientReport.debtAgingGeneralLedger');

            // AJAX endpoint لجلب بيانات التقرير
            Route::get('/debt-aging-general-ledger/ajax', [CustomerReportController::class, 'debtAgingGeneralLedgerAjax'])->name('ClientReport.debtAgingGeneralLedgerAjax');
            Route::get('/customerGuide', [CustomerReportController::class, 'customerGuide'])->name('ClientReport.customerGuide');
            Route::get('/customer-guide-ajax', [CustomerReportController::class, 'customerGuideAjax'])->name('ClientReport.customerGuideAjax');

            Route::get('/get-neighborhoods', [CustomerReportController::class, 'getNeighborhoods'])->name('ClientReport.getNeighborhoods');
            Route::get('/customerBalances', [CustomerReportController::class, 'customerBalances'])->name('ClientReport.customerBalance');

            // روت AJAX لجلب بيانات أرصدة العملاء
            Route::get('/customer-balances-ajax', [CustomerReportController::class, 'customerBalancesAjax'])->name('ClientReport.customerBalancesAjax');

            Route::get('/get-region-groups', [CustomerReportController::class, 'getRegionGroups'])->name('ClientReport.getRegionGroups');

            Route::get('/customerAppointments', [CustomerReportController::class, 'customerAppointments'])->name('ClientReport.customerAppointments');

            // Ajax endpoint لتحميل بيانات تقرير المواعيد
            Route::get('customer-appointments-ajax', [CustomerReportController::class, 'customerAppointmentsAjax'])->name('ClientReport.customerAppointmentsAjax');
            Route::get('/invoices/month/{month}', [CustomerReportController::class, 'getInvoicesByMonth']);
            Route::get('/customerAccountStatement', [CustomerReportController::class, 'customerAccountStatement'])->name('ClientReport.customerAccountStatement');
            Route::get('/customer-account-statement-ajax', [CustomerReportController::class, 'customerAccountStatementAjax'])->name('ClientReport.customerAccountStatementAjax');
            Route::get('/reports/customers/installments', [CustomerReportController::class, 'customerInstallments'])->name('ClientReport.customerInstallments');

            // AJAX endpoint لبيانات تقرير أقساط العملاء
            Route::get('/reports/customers/installments/ajax', [CustomerReportController::class, 'customerInstallmentsAjax'])->name('ClientReport.customerInstallmentsAjax');

            Route::get('/rechargeBalancesReport', [CustomerReportController::class, 'rechargeBalancesReport'])->name('ClientReport.rechargeBalancesReport');
            Route::get('/rechargeBalancesReportAjax', [CustomerReportController::class, 'rechargeBalancesReportAjax'])->name('ClientReport.rechargeBalancesReportAjax');

            Route::get('/balance-consumption', [CustomerReportController::class, 'balanceConsumptionReport'])->name('ClientReport.balanceConsumptionReport');

            Route::get('/balance-consumption-ajax', [CustomerReportController::class, 'balanceConsumptionReportAjax'])->name('ClientReport.balanceConsumptionReportAjax');

            Route::get('/BalancesClient', [CustomerReportController::class, 'BalancesClient'])->name('ClientReport.BalancesClient');
        });


        Route::prefix('ReportsPurchases')->group(function () {
            Route::get('/', [PurchasesReportController::class, 'index'])->name('ReportsPurchases.index');
            Route::get('/bySupplier', [PurchasesReportController::class, 'bySupplier'])->name('ReportsPurchases.bySupplier');
            Route::get('/purchase-reports/by-employee', [PurchasesReportController::class, 'purchaseByEmployee'])->name('ReportsPurchases.purchaseByEmployee');
            Route::get('/purchase-reports/by-employee-ajax', [PurchasesReportController::class, 'purchaseByEmployeeAjax'])->name('ReportsPurchases.purchaseByEmployeeAjax');
            Route::get('/SuppliersDirectory', [PurchasesReportController::class, 'SuppliersDirectory'])->name('ReportsPurchases.SuppliersDirectory');

            // AJAX لجلب بيانات دليل الموردين
            Route::get('/directory-ajax', [PurchasesReportController::class, 'suppliersDirectoryAjax'])->name('ReportsPurchases.SuppliersDirectoryAjax');

            Route::get('/supplier-debt-aging', [PurchasesReportController::class, 'supplierDebtAging'])->name('ReportsPurchases.supplierDebtAging');

            // AJAX لتقرير أعمار ديون الموردين
            Route::get('/supplier-debt-aging-ajax', [PurchasesReportController::class, 'supplierDebtAgingAjax'])->name('ReportsPurchases.supplierDebtAgingAjax');
            Route::get('/by-product', [PurchasesReportController::class, 'byProduct'])->name('ReportsPurchases.byProduct');

            Route::get('/by-product-ajax', [PurchasesReportController::class, 'byProductReportAjax'])->name('ReportsPurchases.byProductReportAjax');

            Route::get('/employee/supplier-payments', [PurchasesReportController::class, 'employeeSupplierPaymentsReport'])->name('ReportsPurchases.employeeSupplierPayments');

            Route::get('/supplier-payments-data', [PurchasesReportController::class, 'employeeSupplierPaymentsReportAjax'])->name('ReportsPurchases.supplierPaymentsData');

            Route::get('/balnceSuppliers', [PurchasesReportController::class, 'balnceSuppliers'])->name('ReportsPurchases.balnceSuppliers');
            Route::get('/supplierPayments', [PurchasesReportController::class, 'supplierPayments'])->name('ReportsPurchases.supplierPayments');
            Route::get('/supplierPaymentsData', [PurchasesReportController::class, 'supplierPaymentsData'])->name('ReportsPurchases.supplierPaymentsData');
            Route::get('/purchaseSupplier', [PurchasesReportController::class, 'purchaseSupplier'])->name('ReportsPurchases.purchaseSupplier');
            Route::get('/paymentPurchases', [PurchasesReportController::class, 'paymentPurchases'])->name('ReportsPurchases.paymentPurchases');
            Route::get('/prodectPurchases', [PurchasesReportController::class, 'prodectPurchases'])->name('ReportsPurchases.prodectPurchases');
        });

        Route::prefix('salesReports')
            ->middleware(['auth'])
            ->group(function () {
                // تقارير الفواتير
                Route::get('/index', [SalesReportsController::class, 'index'])->name('salesReports.index');
                Route::get('/byCustomer', [SalesReportsController::class, 'byCustomer'])->name('salesReports.byCustomer');
                Route::get('/byEmployee', [SalesReportsController::class, 'byEmployee'])->name('salesReports.byEmployee');
                Route::get('/sales-reports/by-employee-ajax', [SalesReportsController::class, 'byEmployeeAjax'])->name('salesReports.byEmployeeAjax');
                Route::get('/byInvoice', [SalesReportsController::class, 'byInvoice'])->name('salesReports.byInvoice');
                Route::get('/sales/by-employee/export', [SalesReportsController::class, 'exportByEmployeeToExcel'])->name('salesReports.byEmployee.export');
                Route::get('/exportByCustomerToExcel', [SalesReportsController::class, 'exportByCustomerToExcel'])->name('salesReports.exportByCustomerToExcel');
                Route::get('/sales/by-employee/export', [SalesReportsController::class, 'clientPaymentReport'])->name('salesReports.clientPaymentReport');
                Route::get('/sales-reports/export-by-product', [SalesReportsController::class, 'exportByProductToExcel'])->name('salesReports.exportByProduct');
                Route::get('/sales-reports/ProfitReportTime', [SalesReportsController::class, 'ProfitReportTime'])->name('salesReports.ProfitReportTime');
                Route::get('/payments-receipts/employee-report', [SalesReportsController::class, 'employeePaymentsReceiptsReport'])->name('salesReports.employeePaymentsReceiptsReport');

                Route::get('/payments-receipts/employee-report-ajax', [SalesReportsController::class, 'employeePaymentsReceiptsReportAjax'])->name('salesReports.employeePaymentsReceiptsReportAjax');
                Route::get('/payments-receipts/payment-method-report', [SalesReportsController::class, 'paymentMethodReport'])->name('salesReports.paymentMethodReport');

                Route::get('/payment-method-report', [SalesReportsController::class, 'paymentMethodReportAjax'])->name('salesReports.paymentMethodReportAjax');

                Route::get('/sales-reports/by-item', [SalesReportsController::class, 'byItemReport'])->name('salesReports.byItem');
                Route::get('/sales-reports/by-item-ajax', [SalesReportsController::class, 'byItemReportAjax'])->name('salesReports.byItemReportAjax');
                Route::get('/papatyment', [SalesReportsController::class, 'patyment'])->name('salesReports.patyment');
                Route::get('/reports/profits', [SalesReportsController::class, 'profits'])->name('salesReports.profits');
                Route::get('/sales-reports/profit-timeline', [SalesReportsController::class, 'profitTimeline'])->name('salesReports.profitTimeline');
                Route::get('/reports/customerProfits', [SalesReportsController::class, 'customerProfits'])->name('salesReports.customerProfits');
                Route::get('/employee-comprehensive-profits', [SalesReportsController::class, 'employeeComprehensiveProfitsReport'])->name('salesReports.employeeProfits');

                // AJAX لتحميل بيانات تقرير الأرباح الشامل
                Route::get('/employee-comprehensive-profits-ajax', [SalesReportsController::class, 'employeeComprehensiveProfitsReportAjax'])->name('salesReports.employeeComprehensiveProfitsReportAjax');
                Route::get('/salaryRep', [SalesReportsController::class, 'salaryRep'])->name('salesReports.salaryRep');
                Route::get('/byProduct', [SalesReportsController::class, 'byProduct'])->name('salesReports.byProduct');

                Route::get('/sales/by-product-ajax', [SalesReportsController::class, 'byProductReportAjax'])->name('salesReports.byProductReportAjax');
            });
        Route::prefix('StorHouseReport')->group(function () {
            Route::get('/', [InventoryReportController::class, 'index'])->name('StorHouseReport.index');
            Route::get('/inventorySheet', [InventoryReportController::class, 'inventorySheet'])->name('StorHouseReport.inventorySheet');

            // جلب بيانات ورقة الجرد عبر AJAX
            Route::get('/inventory-sheet-ajax', [InventoryReportController::class, 'inventorySheetAjax'])->name('StorHouseReport.inventorySheetAjax');
            Route::get('/summaryInventory', [InventoryReportController::class, 'summaryInventory'])->name('StorHouseReport.summaryInventory');

            Route::get('/summary-inventory-ajax', [InventoryReportController::class, 'summaryInventoryAjax'])->name('StorHouseReport.summaryInventoryAjax');
            Route::get('/detailedMovementInventory', [InventoryReportController::class, 'detailedMovementInventory'])->name('StorHouseReport.detailedMovementInventory');
            Route::get('/detailed-movement-inventory-ajax', [InventoryReportController::class, 'detailedMovementInventoryAjax'])->name('StorHouseReport.detailedMovementInventoryAjax');
            Route::get('/detailed-movement-inventory-ajax', [InventoryReportController::class, 'Inventory_mov_det_product_ajax'])->name('StorHouseReport.Inventory_mov_det_product_ajax');

            Route::get('/search/products', [InventoryReportController::class, 'searchProducts'])->name('StorHouseReport.searchProducts');

            Route::get('/valueInventory', [InventoryReportController::class, 'valueInventory'])->name('StorHouseReport.valueInventory');
            Route::get('/value-inventory-ajax', [InventoryReportController::class, 'valueInventoryAjax'])->name('StorHouseReport.valueInventoryAjax');

            Route::get('/inventoryBlance', [InventoryReportController::class, 'inventoryBlance'])->name('StorHouseReport.inventoryBlance');
            Route::get('/inventory-balance-ajax', [InventoryReportController::class, 'inventoryBlanceAjax'])->name('StorHouseReport.inventoryBlanceAjax');
            Route::get('/trialBalance', [InventoryReportController::class, 'trialBalance'])->name('StorHouseReport.trialBalance');
            Route::get('/trial-balance-ajax', [InventoryReportController::class, 'trialBalanceAjax'])->name('StorHouseReport.trialBalanceAjax');

            Route::get('/Inventory_mov_det_product', [InventoryReportController::class, 'Inventory_mov_det_product'])->name('StorHouseReport.Inventory_mov_det_product');
        });

        Route::prefix('reportsProduction')->group(function () {
            Route::get('/production-index', [RentalsController::class, 'production'])->name('ReportsProduction.index');
        });
        Route::prefix('checksReports')->group(function () {
            Route::get('/checks-index', [ChecksController::class, 'index'])->name('checksReports.index');
            Route::get('/checks-delivered', [ChecksController::class, 'deliveredChecks'])->name('checksReports.deliveredChecks');
            Route::get('/checks-delivered-ajax', [ChecksController::class, 'deliveredChequesReportAjax'])->name('checksReports.deliveredChequesReportAjax');
            Route::get('/received', [ChecksController::class, 'received'])->name('checksReports.received');

            // جلب بيانات تقرير الشيكات المستلمة عبر AJAX
            Route::get('/received-ajax', [ChecksController::class, 'receivedChequesReportAjax'])->name('checksReports.receivedChequesReportAjax');
        });
        Route::prefix('employee')->group(function () {
            Route::get('/employee-index', [EmployeeReportController::class, 'index'])->name('employeeReports.index');
        });
    },
);

Route::prefix('GeneralAccountReports')->group(function () {
    Route::get('/', [GeneralAccountsController::class, 'index'])->name('GeneralAccountReports.index');
    Route::get('/taxReport', [GeneralAccountsController::class, 'taxReport'])->name('GeneralAccountReports.taxReport');
    Route::get('/taxDeclaration', [GeneralAccountsController::class, 'taxDeclaration'])->name('GeneralAccountReports.taxDeclaration');
    Route::get('/taxDeclarationAjax', [GeneralAccountsController::class, 'taxDeclarationAjax'])->name('GeneralAccountReports.taxDeclarationAjax');

    Route::get('/declaration', [GeneralAccountsController::class, 'taxDeclaration'])->name('GeneralAccountReports.declaration');

    Route::get('/splitExpensesByCategory', [GeneralAccountsController::class, 'splitExpensesByCategory'])->name('GeneralAccountReports.splitExpensesByCategory');
    Route::get('/splitExpensesBySeller', [GeneralAccountsController::class, 'splitExpensesBySeller'])->name('GeneralAccountReports.splitExpensesBySeller');
    Route::get('/splitExpensesByEmployee', [GeneralAccountsController::class, 'splitExpensesByEmployee'])->name('GeneralAccountReports.splitExpensesByEmployee');
    Route::get('/by-employee', [GeneralAccountsController::class, 'journalEntriesByEmployee']);
    Route::get('/ReceiptByEmployee', [GeneralAccountsController::class, 'receiptsReportAjax'])->name('GeneralAccountReports.ReceiptByEmployeeAjax');

    // AJAX لتحديث بيانات التقرير
    // AJAX لتحديث بيانات التقرير

    Route::get('/by-employee', [GeneralAccountsController::class, 'journalEntriesByEmployee'])->name('GeneralAccountReports.JournalEntriesByEmployee');

    Route::get('/by-employee/ajax', [GeneralAccountsController::class, 'journalEntriesByEmployeeAjax'])->name('GeneralAccountReports.JournalEntriesByEmployeeAjax');
    Route::get('/splitExpensesByEmployeeAjax', [GeneralAccountsController::class, 'expensesByEmployeeAjax'])->name('GeneralAccountReports.expensesByEmployeeAjax');
    Route::get('/splitExpensesByClient', [GeneralAccountsController::class, 'splitExpensesByClient'])->name('GeneralAccountReports.splitExpensesByClient');
    Route::get('/splitExpensesByTimePeriod', [GeneralAccountsController::class, 'splitExpensesByTimePeriod'])->name('GeneralAccountReports.splitExpensesByTimePeriod');
    Route::get('/ReceiptByCategory', [GeneralAccountsController::class, 'ReceiptByCategory'])->name('GeneralAccountReports.ReceiptByCategory');
    Route::get('/ReceiptBySeller', [GeneralAccountsController::class, 'ReceiptBySeller'])->name('GeneralAccountReports.ReceiptBySeller');
    Route::get('/ReceiptByEmployee', [GeneralAccountsController::class, 'receiptsReport'])->name('GeneralAccountReports.ReceiptByEmployee');
    Route::get('/ReceiptByEmployeeAjax', [GeneralAccountsController::class, 'receiptsReportAjax'])->name('GeneralAccountReports.ReceiptByEmployeeAjax');
    Route::get('/ReceiptByClient', [GeneralAccountsController::class, 'ReceiptByClient'])->name('GeneralAccountReports.ReceiptByClient');
    Route::get('/ReceiptByTimePeriod', [GeneralAccountsController::class, 'ReceiptByTimePeriod'])->name('GeneralAccountReports.ReceiptByTimePeriod');
    Route::get('/trialBalance', [TrialBalanceController::class, 'trialBalance'])->name('GeneralAccountReports.trialBalance');

    // Route للـ Ajax لجلب بيانات ميزان المراجعة
    Route::get('trial-balance-ajax', [TrialBalanceController::class, 'trialBalanceAjax'])->name('GeneralAccountReports.trialBalanceAjax');
    Route::get('/incomeStatement', [GeneralAccountsController::class, 'incomeStatement'])->name('GeneralAccountReports.incomeStatement');

    // قائمة الدخل - جلب البيانات عبر AJAX
    Route::get('/income-statement-ajax', [GeneralAccountsController::class, 'incomeStatementAjax'])->name('GeneralAccountReports.incomeStatementAjax');

    // قائمة الدخل - تصدير إلى Excel
    Route::get('/income-statement-excel', [GeneralAccountsController::class, 'exportIncomeStatementExcel'])->name('GeneralAccountReports.incomeStatementExcel');

    // قائمة الدخل - تصدير إلى PDF
    Route::get('/income-statement-pdf', [GeneralAccountsController::class, 'exportIncomeStatementPdf'])->name('GeneralAccountReports.incomeStatementPdf');

    // قائمة الدخل - طباعة
    Route::get('/income-statement-print', [GeneralAccountsController::class, 'printIncomeStatement'])->name('GeneralAccountReports.incomeStatementPrint');

    Route::get('/accountBalanceReview', [TrialBalanceController::class, 'accountBalanceReview'])->name('GeneralAccountReports.accountBalanceReview');
    Route::get('account-balance-review-ajax', [TrialBalanceController::class, 'accountBalanceReviewAjax'])->name('GeneralAccountReports.accountBalanceReviewAjax');

    Route::get('/generalLedger', [GeneralAccountsController::class, 'generalLedger'])->name('GeneralAccountReports.generalLedger');
    Route::get('/CostCentersReport', [TrialBalanceController::class, 'CostCentersReport'])->name('GeneralAccountReports.CostCentersReport');
    Route::get('/CostCentersReportAjax', [TrialBalanceController::class, 'CostCentersReportAjax'])->name('GeneralAccountReports.CostCentersReportAjax');

    Route::get('/ReportJournal', [GeneralAccountsController::class, 'ReportJournal'])->name('GeneralAccountReports.ReportJournal');
    Route::get('/ChartOfAccounts', [GeneralAccountsController::class, 'ChartOfAccounts'])->name('GeneralAccountReports.ChartOfAccounts');
});


        Route::prefix('supply-order')->group(function () {
            Route::get('/index', [SupplyOrdersReportController::class, 'index'])->name('supply-order.index');
    Route::get('/supply-orders', [SupplyOrdersReportController::class, 'supplyOrdersReport'])
         ->name('supply-orders.supplyOrdersReport');

    // AJAX endpoint لجلب بيانات التقرير
    Route::get('/supply-orders/ajax', [SupplyOrdersReportController::class, 'supplyOrdersReportAjax'])
         ->name('supply-orders.supplyOrdersReportAjax');

    // التحقق من حالة الأوامر (للإشعارات)
    Route::get('/supply-orders/check-status', [SupplyOrdersReportController::class, 'checkOrdersStatus'])
         ->name('supply-orders.checkOrdersStatus');

        });

                Route::prefix('unit-track')->group(function () {
            Route::get('/index', [UnitTrackReport::class, 'index'])->name('unit-track.index');
  Route::get('/unit-track', [UnitTrackReport::class, 'unitsReport'])
         ->name('unit-track.unitsReport');
    // AJAX endpoint لجلب بيانات التقرير
    Route::get('/unit-track/ajax', [UnitTrackReport::class, 'unitsReportAjax'])
         ->name('unit-track.unitsReportAjax');

    // التحقق من حالة الأوامر (للإشعارات)
    Route::get('/supply-orders/check-status', [SupplyOrdersReportController::class, 'checkOrdersStatus'])
         ->name('supply-orders.checkOrdersStatus');

        });

