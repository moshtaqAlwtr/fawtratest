<?php

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Route;
use App\Models\ChartOfAccount;
use App\Models\Sales\Invoice;
use App\Models\JournalEntry;
use Modules\Client\Http\Controllers\ItineraryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// جلب العملاء حسب المجموعة والمندوب
Route::get('/groups/{id}/clients', [ItineraryController::class, 'getClientsForGroup']);

// الحصول على الأبناء المباشرين للحساب
Route::get('/accounts/{code}/direct-children', function ($code) {
    $account = ChartOfAccount::where('code', $code)->first();
    if (!$account) {
        return response()->json([]);
    }
    return response()->json($account->allDescendants);
});

// الحصول على بيانات الشجرة بتنسيق JSTree
Route::get('/accounts/tree', function () {
    $accounts = ChartOfAccount::whereNull('parent_id')->get();

    function formatForJSTree($account) {
        $node = [
            'id' => $account->id,
            'text' => $account->code . ' - ' . $account->name,
            'state' => ['opened' => true],
            'children' => []
        ];

        foreach ($account->childAccounts as $child) {
            $node['children'][] = formatForJSTree($child);
        }

        return $node;
    }

    $treeData = [];
    foreach ($accounts as $account) {
        $treeData[] = formatForJSTree($account);
    }

    return response()->json($treeData);
});

// الحصول على تفاصيل حساب معين
Route::get('/accounts/{id}/details', function ($id) {
    $account = ChartOfAccount::find($id);
    if (!$account) {
        return response()->json(['error' => 'الحساب غير موجود'], 404);
    }

    return response()->json([
        'id' => $account->id,
        'code' => $account->code,
        'name' => $account->name,
        'type' => $account->type,
        'balance' => $account->balance ?? 0,
        'parent_id' => $account->parent_id
    ]);
});

// الحصول على تفاصيل الفاتورة
Route::get('/invoice-details/{code}', function ($code) {
    try {
        $account = ChartOfAccount::where('code', $code)->first();
        if (!$account) {
            return response()->json(['error' => 'الفاتورة غير موجودة']);
        }

        // جلب الفاتورة المرتبطة بهذا الحساب
        $invoice = Invoice::where('account_code', $code)->first();
        if (!$invoice) {
            return response()->json(['error' => 'لا توجد فاتورة مرتبطة بهذا الحساب']);
        }

        // جلب القيود المحاسبية للفاتورة
        $entries = JournalEntry::with(['details', 'employee'])
            ->where('invoice_id', $invoice->id)
            ->get()
            ->map(function ($entry) {
                return [
                    'reference_no' => $entry->reference_number,
                    'date' => $entry->date,
                    'debit' => $entry->details->sum('debit'),
                    'credit' => $entry->details->sum('credit'),
                    'balance_after' => 0, // سيتم حسابه لاحقاً
                    'created_by' => optional($entry->employee)->name ?? 'غير معروف',
                    'branch_name' => 'الفرع الرئيسي' // يمكن تغييره حسب هيكل البيانات لديك
                ];
            });

        // حساب الرصيد التراكمي
        $balance = 0;
        $entries = $entries->map(function ($entry) use (&$balance) {
            $balance += ($entry['debit'] - $entry['credit']);
            $entry['balance_after'] = $balance;
            return $entry;
        });

        return response()->json([
            'invoice' => $invoice,
            'entries' => $entries,
            'account' => $account
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});

// Itinerary API Routes
Route::get('/employees/{employee}/groups', [ItineraryController::class, 'getGroupsForEmployee']);
Route::get('/groups/{group}/clients', [ItineraryController::class, 'getClientsForGroup']);
