<?php

use Illuminate\Support\Facades\Route;
use Modules\Api\Http\Controllers\ApiController;
use Modules\Api\Http\Controllers\ClientController;
use Modules\Api\Http\Controllers\ItineraryController;
use Modules\Api\Http\Controllers\AuthController;
use Modules\Api\Http\Controllers\ClientRelationController;
use Modules\Api\Http\Controllers\TrafficAnalysisController;
use Modules\Api\Http\Controllers\GroupController;
use Modules\Api\Http\Controllers\QuoteController;
use Modules\Api\Http\Controllers\SalesController;
use Modules\Api\Http\Controllers\ReturnInvoiceController;
use Modules\Api\Http\Controllers\PaymentProcessController;
use Modules\Api\Http\Controllers\CreditNotificationController;
use Modules\Api\Http\Controllers\Finance\ExpensesController;
use Modules\Api\Http\Controllers\Finance\ReceiptController;
use Modules\Api\Http\Controllers\Finance\TreasuryController;
use Modules\Api\Http\Controllers\Finance\TreasuryEmployeeController;
use Modules\Api\Http\Controllers\Statstic\StatsticController;
use Modules\Api\Http\Controllers\Products\ProductsController;



Route::post('login', [AuthController::class, 'login']);

  
Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('return')->group(function () {
    Route::get('/', [ReturnInvoiceController::class, 'index']);
    Route::get('/{id}', [ReturnInvoiceController::class, 'show']);
    Route::get('/print/{id}', [ReturnInvoiceController::class, 'print']);
    
    Route::post('/create', [ReturnInvoiceController::class, 'store']);
   
});
Route::prefix('clients')->group(function () {
    Route::post('/note/add', [ClientRelationController::class, 'store']);
    Route::get('/', [ClientController::class, 'index']);
      Route::get('/test', [ClientController::class, 'test']);
    
     Route::get('/map', [ClientController::class, 'map']);
    Route::post('/{id}/update-opening-balance', [ClientController::class, 'updateOpeningBalance']);

    Route::post('/store', [ClientController::class, 'store']);
    Route::get('/create/data', [ClientController::class, 'createData']);
    Route::get('/{id}', [ClientController::class, 'showFull']);
    Route::post('/{id}', [ClientController::class, 'update']);
   
    
   
    Route::get('/{id}/edit', [ClientController::class, 'showBasic']);
    Route::delete('/{id}', [ClientController::class, 'destroy']);
    
  
});

   Route::prefix('contacts')->group(function () {
   Route::get('/', [ClientController::class, 'contacts']);
});
 Route::prefix('visit')->group(function () {
   Route::get('/routes', [ItineraryController::class, 'apiItineraryFull']);
   Route::post('/route', [ItineraryController::class, 'store']);
   Route::get('/create/data', [ItineraryController::class, 'createWithClients']); // عرض بيانات اضافة خط السير 
   Route::get('/groups/{id}/clients', [ItineraryController::class, 'getClientsForGroup']); // بعد عرض المعلومات في ال cratr ارسال id المجموعه لعرض العملاء المرتبطين بالمجموعه
   Route::delete('routes/{id}', [ItineraryController::class, 'destroyVisit']);
});

  Route::prefix('traffic')->group(function () {
   Route::get('/', [TrafficAnalysisController::class, 'apiTrafficAnalysis']);
});



Route::prefix('sales')->group(function () {
    Route::get('/', [SalesController::class, 'index']);
    Route::post('/create', [SalesController::class, 'storeee']);
    Route::get('/invoices/form-data', [SalesController::class, 'createFormData']);
    Route::get('/invoices/{id}', [SalesController::class, 'show']);
    Route::post('/invoices/{id}/send', [SalesController::class, 'sendInvoice']);
      Route::get('/invoices/{id}/pdf', [SalesController::class, 'generatePdf']);
      Route::get('/invoices/{id}/print', [SalesController::class, 'print']);
  

});

Route::prefix('group')->group(function () {
    Route::get('/', [GroupController::class, 'groupClient']);
    Route::post('/', [GroupController::class, 'storeGroupClient']);
    Route::put('/update/{id}', [GroupController::class, 'update']); 
    Route::delete('/delete/{id}', [GroupController::class, 'destroy']); 
});

Route::prefix('payment')->group(function () {
    Route::get('/', [PaymentProcessController::class, 'index']);
Route::post('/store', [PaymentProcessController::class, 'storeApi']);
  Route::get('/{id}/create', [PaymentProcessController::class, 'createPaymentData']);
  Route::get('/{id}', [PaymentProcessController::class, 'show']);
});

Route::prefix('quote')->group(function () {
    Route::get('/', [QuoteController::class, 'index']);
    Route::get('/{id}', [QuoteController::class, 'show']);
    Route::post('/create', [QuoteController::class, 'store']);
    Route::delete('/delete/{id}', [QuoteController::class, 'destroy']); 
});


Route::prefix('credit')->group(function () {
    Route::get('/', [CreditNotificationController::class, 'index']);
    Route::get('/{id}', [CreditNotificationController::class, 'show']);
    Route::post('/create', [CreditNotificationController::class, 'store']);
     Route::get('/{id}/print', [CreditNotificationController::class, 'print']);
    
});

// routes/api.php أو routes داخل Modules/Api
Route::prefix('expenses')->group(function () {
    Route::get('/', [ExpensesController::class, 'index']);
    Route::get('/{id}', [ExpensesController::class, 'show']);
    Route::post('/create', [ExpensesController::class, 'store']);
    Route::get('/create/data', [ExpensesController::class, 'create']);
});

Route::prefix('receipt')->group(function () {
    Route::get('/', [ReceiptController::class, 'index']);              
    Route::get('/{id}', [ReceiptController::class, 'show']);
    Route::post('/create', [ReceiptController::class, 'store']);
    Route::get('/create/data', [ReceiptController::class, 'createData']);
});
Route::prefix('treasury')->group(function () {
    Route::get('/', [TreasuryController::class, 'index']); 
  
    Route::post('/create', [TreasuryController::class, 'store']);
    Route::post('/transfer', [TreasuryController::class, 'transfer']);
    Route::get('/create/data-transfer', [TreasuryController::class, 'list_transfer']);
    Route::get('/create/data', [TreasuryController::class, 'createData']);
    Route::get('/{id}', [TreasuryController::class, 'show']);

});
Route::prefix('employees')->group(function () {
      Route::get('/', [TreasuryController::class, 'employees']); 
}); 

Route::prefix('branchs')->group(function () {
      Route::get('/', [TreasuryController::class, 'branchs']); 
}); 
Route::prefix('treasury_employee')->group(function () {
    Route::get('/', [TreasuryEmployeeController::class, 'index']); 
    Route::post('/create', [TreasuryEmployeeController::class, 'store']);

    Route::get('/create/data', [TreasuryEmployeeController::class, 'createData']);
    Route::put('/update/{id}', [TreasuryEmployeeController::class, 'update']);
    Route::get('/edit/data/{id}', [TreasuryEmployeeController::class, 'edit']);
    Route::delete('/{id}', [TreasuryEmployeeController::class, 'destroy']);

});


Route::prefix('statstic')->group(function () {
    Route::get('/', [StatsticController::class, 'index']); 
});


Route::prefix('products')->group(function () {
    Route::get('/', [ProductsController::class, 'index']); 
});

});
