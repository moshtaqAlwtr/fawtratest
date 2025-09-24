<?php

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Account\Http\Controllers\AccountsChartController;
use Modules\Account\Http\Controllers\AccountsSettingsController;
use Modules\Account\Http\Controllers\AssetsController;
use Modules\Account\Http\Controllers\Cheques\CheckBooksController;
use Modules\Account\Http\Controllers\Cheques\PayableChequesController;
use Modules\Account\Http\Controllers\Cheques\ReceivedChequesController;
use Modules\Account\Http\Controllers\CostCentersController;
use Modules\Account\Http\Controllers\Finance\ExpensesController;
use Modules\Account\Http\Controllers\Finance\FinanceSettingsController;
use Modules\Account\Http\Controllers\Finance\IncomesController;
use Modules\Account\Http\Controllers\Finance\TreasuryController;
use Modules\Account\Http\Controllers\Finance\TreasuryEmployeeController;
use Modules\Account\Http\Controllers\JournalEntryController;


Route::middleware(['auth'])->group(function () {

Route::get('/accounts/tree', [AccountsChartController::class, 'getTree']);
Route::get('/accounts/{id}/children', [AccountsChartController::class, 'getChildren']);
Route::get('/accounts/parents', [AccountsChartController::class, 'getParents']);
Route::get('/accounts/{parent}/next-code', [AccountsChartController::class, 'getNextCode']);
Route::post('/set-error', function (Illuminate\Http\Request $request) {
    session()->flash('error', $request->message);
    return response()->json(['success' => true]);
});

Route::delete('/accounts/{parentId}/delete', [AccountsChartController::class, 'destroy']);
Route::post('/accounts/store_account', [AccountsChartController::class, 'store_account']);
Route::get('/accounts/{id}/edit', [AccountsChartController::class, 'edit']);
Route::put('/accounts/{id}/update', [AccountsChartController::class, 'update']);

Route::get('/cost_centers/tree', [CostCentersController::class, 'getTree']);
Route::get('/cost_centers/parents', [CostCentersController::class, 'getParents']);
Route::get('/cost_centers/{id}/children', [CostCentersController::class, 'getChildren']);
Route::get('/cost_centers/{parent}/next-code', [CostCentersController::class, 'getNextCode']);
Route::get('/cost_centers/{parentId}/details', [CostCentersController::class, 'getAccountDetails']);
Route::delete('/cost_centers/{parentId}/delete', [CostCentersController::class, 'destroy']);
Route::post('/cost_centers/store_account', [CostCentersController::class, 'store_account']);
Route::get('/cost_centers/{id}/edit', [CostCentersController::class, 'edit']);
Route::put('/cost_centers/{id}/update', [CostCentersController::class, 'update']);

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath','check.branch'],
    ],
    function () {
        Route::prefix('Accounts')
            ->middleware(['auth'])
            ->group(function () {
                Route::prefix('journal')->group(function () {
                    Route::get('/index', [JournalEntryController::class, 'index'])->name('journal.index');
                    Route::get('/create', [JournalEntryController::class, 'create'])->name('journal.create');
                    Route::post('/store', [JournalEntryController::class, 'store'])->name('journal.store');
                    Route::get('/show/{id}', [JournalEntryController::class, 'show'])->name('journal.show');
                    Route::get('/edit/{id}', [JournalEntryController::class, 'edit'])->name('journal.edit');
                    Route::put('/update/{id}', [JournalEntryController::class, 'update'])->name('journal.update');
                    Route::delete('/destroy/{id}', [JournalEntryController::class, 'destroy'])->name('journal.destroy');
                    Route::get('/generalLedger', [AccountsChartController::class, 'generalLedger'])->name('journal.generalLedger');
                });

                Route::prefix('cost_centers')->group(function () {
                    Route::get('/index', [CostCentersController::class, 'index'])->name('cost_centers.index');
                });
                Route::prefix('accounts_chart')->group(function () {
                    Route::get('/index', [AccountsChartController::class, 'index'])->name('accounts_chart.index');
                    Route::get('/testone/{accountId}', [AccountsChartController::class, 'testone'])->name('accounts_chart.testone');
                    Route::get('/getJournalEntries/{accountId}/journal-entries', [AccountsChartController::class, 'getJournalEntries'])->name('accounts_chart.getJournalEntries');
                    Route::get('/accounts/{accountId}/balance', [AccountsChartController::class, 'getAccountWithBalance']);
                    Route::get('/accounts/search', [AccountsChartController::class, 'search'])->name('accounts_chart.search');
                    Route::delete('/destroy/{id}', [AccountsChartController::class, 'destroy'])->name('accounts_chart.destroy');
                    Route::get('/showDetails/{id}', [AccountsChartController::class, 'showDetails'])->name('accounts_chart.showDetails');
                                        Route::get('/chart/details/{accountId}', [AccountsChartController::class, 'getAccountDetails'])->name('accounts_chart.details');
                    Route::post('/store', [AccountsChartController::class, 'store_account'])->name('accounts_chart.store_account');
                });

                Route::prefix('Assets')->group(function () {
                    Route::get('/index', [AssetsController::class, 'index'])->name('Assets.index');
                    Route::get('/create', [AssetsController::class, 'create'])->name('Assets.create');
                    Route::post('/store', [AssetsController::class, 'store'])->name('Assets.store');
                    Route::get('/show/{id}', [AssetsController::class, 'show'])->name('Assets.show');
                    Route::get('/edit/{id}', [AssetsController::class, 'edit'])->name('Assets.edit');
                    Route::put('/update/{id}', [AssetsController::class, 'update'])->name('Assets.update');
                    Route::delete('/destroy/{id}', [AssetsController::class, 'destroy'])->name('Assets.destroy');
                    Route::get('Assets/pdf/{id}', [AssetsController::class, 'generatePdf'])->name('assets.generatePdf');
                });

               
                  Route::prefix('accounts_settings')->group(function () {
                    Route::get('/index', [AccountsSettingsController::class, 'index'])->name('accounts_settings.index');
                    Route::get('/financial_years', [AccountsSettingsController::class, 'financial_years'])->name('accounts_settings.financial_years');
                    Route::get('/closed_periods', [AccountsSettingsController::class, 'closed_periods'])->name('accounts_settings.closed_periods');
                    Route::get('/accounts_routing', [AccountsSettingsController::class, 'accounts_routing'])->name('accounts_settings.accounts_routing');
                    Route::get('/accounting_general', [AccountsSettingsController::class, 'accounting_general'])->name('accounts_settings.accounting_general');
                    
                    
                    
                    //AccountsChartController::class,
                });

                   Route::prefix('expenses')->group(function () {
                    Route::get('/index', [ExpensesController::class, 'index'])->name('expenses.index');
                    Route::get('/expenses/data', [ExpensesController::class, 'getData'])->name('expenses.data');
                    Route::get('/create', [ExpensesController::class, 'create'])->name('expenses.create');
                    Route::post('/store', [ExpensesController::class, 'store'])->name('expenses.store');
                    Route::get('/show/{id}', [ExpensesController::class, 'show'])->name('expenses.show');
                    Route::get('/edit/{id}', [ExpensesController::class, 'edit'])->name('expenses.edit');
                    Route::post('/update/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
                    Route::get('/delete/{id}', [ExpensesController::class, 'delete'])->name('expenses.delete');
                    Route::post('/expenses/{id}/cancel', [ExpensesController::class, 'cancel'])->name('expenses.cancel');
                    Route::get('/expenses/print/{id}/{type}', [ExpensesController::class, 'print'])->name('expenses.print');
                });

                #incomes routes
                Route::prefix('incomes')->group(function () {
                    Route::get('/index', [IncomesController::class, 'index'])->name('incomes.index');
                    Route::get('/data', [IncomesController::class, 'getData'])->name('incomes.data');
                    Route::get('/create', [IncomesController::class, 'create'])->name('incomes.create');

                    Route::post('/store', [IncomesController::class, 'store'])->name('incomes.store');
                    Route::get('/show/{id}', [IncomesController::class, 'show'])->name('incomes.show');
                    Route::get('/edit/{id}', [IncomesController::class, 'edit'])->name('incomes.edit');
                    Route::put('/update/{id}', [IncomesController::class, 'update'])->name('incomes.update');
                    Route::get('/delete/{id}', [IncomesController::class, 'delete'])->name('incomes.delete');
                    Route::get('/incomes/print/{id}/{type}', [IncomesController::class, 'print'])->name('incomes.print');
                    Route::post('/incomes/{id}/cancel', [IncomesController::class, 'cancel'])->name('incomes.cancel');
                });

                #treasury routes
                Route::prefix('treasury')->group(function () {
                    Route::get('/index', [TreasuryController::class, 'index'])->name('treasury.index');
                    Route::post('/transfer', [TreasuryController::class, 'transfer'])->name('treasury.transfer');
                    Route::post('/transferTreasuryStore', [TreasuryController::class, 'transferTreasuryStore'])->name('treasury.transferTreasuryStore');
                    Route::get('/transferCreate', [TreasuryController::class, 'transferCreate'])->name('treasury.transferCreate');
                    Route::get('/treasury/transfer/{id}/edit', [TreasuryController::class, 'transferEdit'])->name('treasury.transferEdit');
                    Route::put('/treasury/transfer/{id}/update', [TreasuryController::class, 'transferTreasuryUpdate'])->name('treasury.transferUpdate');
                    Route::get('/treasury/update-type/{id}', [TreasuryController::class, 'updateType'])->name('treasury.updateType');

                    Route::get('/create', [TreasuryController::class, 'create'])->name('treasury.create');
                    Route::get('/updateStatus/{id}', [TreasuryController::class, 'updateStatus'])->name('treasury.updateStatus');

                    Route::get('/create/account_bank', [TreasuryController::class, 'create_account_bank'])->name('treasury.create_account_bank');
                    Route::post('/store', [TreasuryController::class, 'store'])->name('treasury.store');
                    Route::post('/store/account_bank', [TreasuryController::class, 'store_account_bank'])->name('treasury.store_account_bank');
                    Route::get('/show/{id}', [TreasuryController::class, 'show'])->name('treasury.show');
                    Route::get('/operations/{id}', [TreasuryController::class, 'getOperationsData'])->name('treasury.operations');
                    Route::get('/transfers/{id}', [TreasuryController::class, 'getTransfersData'])->name('treasury.transfers');
                    Route::get('/edit/{id}', [TreasuryController::class, 'edit'])->name('treasury.edit');
                    Route::get('/edit/account_bank/{id}', [TreasuryController::class, 'edit_account_bank'])->name('treasury.edit_account_bank');
                    Route::post('/update/{id}', [TreasuryController::class, 'update'])->name('treasury.update');
                    Route::post('/update/update_account_bank/{id}', [TreasuryController::class, 'update_account_bank'])->name('treasury.update_account_bank');
                    Route::delete('/destroy/{id}', [TreasuryController::class, 'destroy'])->name('treasury.destroy');
                });

                #finance_settings routes
                Route::prefix('finance_settings')->group(function () {
                    Route::get('/index', [FinanceSettingsController::class, 'index'])->name('finance_settings.index');
                    Route::get('/expenses_category', [FinanceSettingsController::class, 'expenses_category'])->name('finance_settings.expenses_category');
                    Route::post('/expenses_category/store', [FinanceSettingsController::class, 'expenses_category_store'])->name('finance_settings.expenses_category_store');
                    Route::post('/expenses_category/update/{id}', [FinanceSettingsController::class, 'expenses_category_update'])->name('finance_settings.expenses_category_update');
                    Route::get('/expenses_category/delete/{id}', [FinanceSettingsController::class, 'expenses_category_delete'])->name('finance_settings.expenses_category_delete');

                    #receipt_category
                    Route::get('/receipt_category', [FinanceSettingsController::class, 'receipt_category'])->name('finance_settings.receipt_category');
                    Route::post('/receipt_category/store', [FinanceSettingsController::class, 'receipt_category_store'])->name('finance_settings.receipt_category_store');
                    Route::post('/receipt_category/update/{id}', [FinanceSettingsController::class, 'receipt_category_update'])->name('finance_settings.receipt_category_update');
                    Route::get('/receipt_category/delete/{id}', [FinanceSettingsController::class, 'receipt_category_delete'])->name('finance_settings.receipt_category_delete');

                    #treasury_employee
                    Route::get('/treasury_employee', [TreasuryEmployeeController::class, 'index'])->name('finance_settings.treasury_employee');
                    Route::get('/treasury_employee/create', [TreasuryEmployeeController::class, 'create'])->name('treasury_employee.create');
                    Route::get('/treasury_employee/delete/{id}', [TreasuryEmployeeController::class, 'delete'])->name('treasury_employee.delete');
                    Route::get('/treasury_employee/edit/{id}', [TreasuryEmployeeController::class, 'edit'])->name('treasury_employee.edit');
                    Route::post('/treasury_employee/update/{id}', [TreasuryEmployeeController::class, 'update'])->name('treasury_employee.update');
                    Route::post('/treasury_employee/store', [TreasuryEmployeeController::class, 'store'])->name('treasury_employee.store');
                });
                            Route::prefix('payable_cheques')->group(function () {
                Route::get('/index',[PayableChequesController::class,'index'])->name('payable_cheques.index');
                Route::get('/create',[PayableChequesController::class,'create'])->name('payable_cheques.create');
                Route::get('/show/{id}',[PayableChequesController::class,'show'])->name('payable_cheques.show');
                Route::get('/edit/{id}',[PayableChequesController::class,'edit'])->name('payable_cheques.edit');
                Route::post('/store',[PayableChequesController::class,'store'])->name('payable_cheques.store');
                Route::post('/update/{id}',[PayableChequesController::class,'update'])->name('payable_cheques.update');
                Route::get('/search', [PayableChequesController::class, 'search'])->name('payable_cheques.search');
                Route::get('/get-checkbooks/{bankId}', [PayableChequesController::class, 'getCheckbooks'])->name('get.checkbooks');
            });

            #Received Cheques
            Route::prefix('received_cheques')->group(function () {
                Route::get('/index',[ReceivedChequesController::class,'index'])->name('received_cheques.index');
                Route::get('/create',[ReceivedChequesController::class,'create'])->name('received_cheques.create');
                Route::get('/show/{id}',[ReceivedChequesController::class,'show'])->name('received_cheques.show');
                Route::get('/edit/{id}',[ReceivedChequesController::class,'edit'])->name('received_cheques.edit');
                Route::post('/store',[ReceivedChequesController::class,'store'])->name('received_cheques.store');
                Route::post('/update/{id}',[ReceivedChequesController::class,'update'])->name('received_cheques.update');
                Route::get('/search', [ReceivedChequesController::class, 'search'])->name('received_cheques.search');
                Route::get('/get-checkbooks/{bankId}', [ReceivedChequesController::class, 'getCheckbooks'])->name('get.checkbooks');
            });

            #Check books
            Route::prefix('check_books')->group(function () {
                Route::get('/index',[CheckBooksController::class,'index'])->name('check_books.index');
                Route::get('/create',[CheckBooksController::class,'create'])->name('check_books.create');
                Route::get('/show/{id}',[CheckBooksController::class,'show'])->name('check_books.show');
                Route::get('/edit/{id}',[CheckBooksController::class,'edit'])->name('check_books.edit');
                Route::post('/store',[CheckBooksController::class,'store'])->name('check_books.store');
                Route::post('/update/{id}',[CheckBooksController::class,'update'])->name('check_books.update');
                Route::get('/delete/{id}',[CheckBooksController::class,'delete'])->name('check_books.delete');
                Route::get('/search', [CheckBooksController::class, 'search'])->name('check_books.search');
            });

            });
    },
);

});
