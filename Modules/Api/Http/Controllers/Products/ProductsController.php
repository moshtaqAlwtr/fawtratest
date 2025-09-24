<?php

namespace Modules\Api\Http\Controllers\Products;

use App\Http\Controllers\Controller;
use App\Models\Account;           
use Illuminate\Http\Request;
use Modules\Api\Http\Resources\Finance\ReceiptResource;
use App\Models\Expense;
use App\Traits\ApiResponseTrait;
use App\Models\ExpensesCategory;
use App\Models\User;
use App\Models\Log as ModelsLog;
use App\Models\AccountSetting;
use App\Models\Branch;
use App\Models\Client;
use App\Models\ClientEmployee;
use App\Models\Target;

use App\Models\Employee;
use App\Models\Revenue;
use App\Models\EmployeeClientVisit;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\ReceiptCategory;
use App\Models\Receipt;
use Illuminate\Support\Arr;
use App\Models\TaxSitting;
use App\Models\Product;
use App\Models\TreasuryEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Api\Http\Resources\Products\ProductsResource;
class ProductsController extends Controller
{
    use ApiResponseTrait;
   

public function index(Request $request)
{

     
  try {
        $products = Product::all();

        // التحقق إذا ما فيه منتجات
        if ($products->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'لا توجد منتجات حالياً',
                'data'    => [],
            ], 200);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب المنتجات بنجاح',
            'data'    => ProductsResource::collection($products),
        ], 200);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب المنتجات',
            'error'   => $e->getMessage(),
        ], 500);
    }
   
}



}
















