<?php

namespace Modules\Api\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use Illuminate\Http\Request;
use Modules\Api\Http\Resources\Finance\ExpenseResource;
use App\Models\Expense;
use App\Traits\ApiResponseTrait;
use App\Models\ExpensesCategory;
use App\Models\User;
use App\Models\Log as ModelsLog;
use App\Models\AccountSetting;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Supplier;
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpensesController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
   
public function index(Request $request)
{
    try {
        $user = auth()->user();

        $query = Expense::query()->orderByDesc('id');

        $query->when($request->keywords, fn($q, $v) =>
            $q->where(function ($sub) use ($v) {
                $sub->where('code', 'like', "%$v%")
                    ->orWhere('description', 'like', "%$v%");
            })
        );

        $query->when($request->from_date, fn($q, $v) => $q->where('date', '>=', $v));
        $query->when($request->to_date, fn($q, $v) => $q->where('date', '<=', $v));
        $query->when($request->category, fn($q, $v) => $q->where('expenses_category_id', $v));
        $query->when($request->status, fn($q, $v) => $q->where('status', $v));
        $query->when($request->description, fn($q, $v) => $q->where('description', 'like', "%$v%"));
        $query->when($request->vendor, fn($q, $v) => $q->where('supplier_id', $v));
        $query->when($request->amount_from, fn($q, $v) => $q->where('amount', '>=', $v));
        $query->when($request->amount_to, fn($q, $v) => $q->where('amount', '<=', $v));
        $query->when($request->sub_account, fn($q, $v) => $q->where('account_id', $v));
        $query->when($request->added_by, fn($q, $v) => $q->where('created_by', $v));

        if ($user->role === 'employee') {
            $query->where('created_by', $user->id);
        }

        $expenses = $query->paginate(40);

        $total7 = Expense::where('date', '>=', now()->subDays(7));
        $total30 = Expense::where('date', '>=', now()->subDays(30));
        $total365 = Expense::where('date', '>=', now()->subDays(365));

        if ($user->role === 'employee') {
            $total7->where('created_by', $user->id);
            $total30->where('created_by', $user->id);
            $total365->where('created_by', $user->id);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب سندات الصرف بنجاح',
            'data' => [
                'expenses' => ExpenseResource::collection($expenses),
                'pagination' => [
                   'total'         => $expenses->total(),
                'count'         => $expenses->count(),
                'per_page'      => $expenses->perPage(),
                'current_page'  => $expenses->currentPage(),
                'total_pages'   => $expenses->lastPage(),
                'next_page_url' => $expenses->nextPageUrl(),
                'prev_page_url' => $expenses->previousPageUrl(),
                'from'          => $expenses->firstItem(),
                'to'            => $expenses->lastItem(),
                'path'          => $expenses->path(),
                ],
                'total_7_days' => $total7->sum('amount'),
                'total_30_days' => $total30->sum('amount'),
                'total_365_days' => $total365->sum('amount'),
            ]
        ],200);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في جلب سندات الصرف',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Show the form for creating a new resource.
     */
 public function create()
{
    try {
        $user = auth()->user();

        $accounts = Account::all(['id', 'name']);
        $treasuries = Treasury::all(['id', 'name']);
        $suppliers = Supplier::all(['id', 'trade_name']);
        $expenses_categories = ExpensesCategory::all(['id', 'name']);
        $taxs = TaxSitting::all(['id', 'name', 'tax', 'type']);
        $account_setting = AccountSetting::where('user_id', $user->id)->first();
        $code = Expense::generateCode();

        $MainTreasury = null;
        if ($user && $user->employee_id) {
            $treasuryEmp = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
            $MainTreasury = $treasuryEmp && $treasuryEmp->treasury_id
                ? Account::find($treasuryEmp->treasury_id)
                : Account::where('name', 'الخزينة الرئيسية')->first();
        } else {
            $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب بيانات إنشاء سند صرف بنجاح',
            'data' => [
                'code' => $code,
                'accounts' => $accounts,
                'treasuries' => $treasuries,
                'suppliers' => $suppliers,
                'expenses_categories' => $expenses_categories,
                'taxs' => $taxs,
                'main_treasury' => $MainTreasury,
                'account_setting' => $account_setting,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في جلب بيانات إنشاء سند صرف',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $validated = $request->validate([
            'code' => 'nullable|string|unique:expenses,code',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'date' => 'required|date',
          
            'expenses_category_id' => 'nullable|exists:expenses_categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'seller' => 'nullable|string',
            'treasury_id' => 'nullable|exists:accounts,id',
            'account_id' => 'required|exists:accounts,id',
            'is_recurring' => 'nullable|boolean',
            'recurring_frequency' => 'nullable|string',
            'end_date' => 'nullable|date',
            'tax1' => 'nullable|numeric',
            'tax2' => 'nullable|numeric',
            'tax1_amount' => 'nullable|numeric',
            'tax2_amount' => 'nullable|numeric',
            'cost_centers_enabled' => 'nullable|boolean',
            'attachments' => 'nullable|file|mimes:jpg,jpeg,png,pdf'
        ]);

        $expense = new Expense($validated);
        $expense->created_by = auth()->id();

        if ($request->hasFile('attachments')) {
            $expense->attachments = uploadFile('expenses', $request->file('attachments')); // اكتب uploadFile بنفسك أو استخدم helper مناسب
        }

        $expense->save();

        // سجل العملية
        ModelsLog::create([
            'type' => 'expense',
            'type_id' => $expense->id,
            'type_log' => 'log',
            'description' => "تم إنشاء سند صرف رقم **{$expense->code}** بقيمة **{$expense->amount}**",
            'created_by' => auth()->id(),
        ]);

        // تحديد الخزنة
        $user = auth()->user();
        $mainTreasury = Account::find(optional($user->treasury)->id) 
                        ?? Account::where('name', 'الخزينة الرئيسية')->first();

        if (!$mainTreasury) {
            throw new \Exception('لا توجد خزينة متاحة.');
        }

        if ($mainTreasury->balance < $expense->amount) {
            throw new \Exception('رصيد الخزنة غير كافٍ.');
        }

        $mainTreasury->decrement('balance', $expense->amount);

        // القيود اليومية
        $journalEntry = JournalEntry::create([
            'reference_number' => $expense->code,
            'date' => $expense->date,
            'description' => 'سند صرف رقم ' . $expense->code,
            'status' => 1,
            'currency' => 'SAR',
            'created_by_employee' => $user->id,
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $mainTreasury->id,
            'description' => 'صرف مبلغ من الخزنة',
            'credit' => $expense->amount,
            'debit' => 0,
            'is_debit' => false,
        ]);

        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $expense->account_id,
            'description' => 'صرف مبلغ لمصروفات',
            'credit' => 0,
            'debit' => $expense->amount,
            'is_debit' => true,
        ]);

        Account::where('id', $expense->account_id)->increment('balance', $expense->amount);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'تم إنشاء سند صرف بنجاح.',
            'data' => new ExpenseResource($expense)
        ]);

    } catch (\Exception $e) {
        DB::rollBack();

        return response()->json([
            'status' => false,
            'message' => 'فشل في إنشاء سند الصرف: ' . $e->getMessage(),
        ], 500);
    }
}


    /**
     * Show the specified resource.
     */
   public function show($id)
{
    try {
        $user = auth()->user();

        $expense = Expense::with(['supplier', 'account', 'createdBy'])
            ->where('id', $id)
            ->when($user->role === 'employee', function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->first();

        if (!$expense) {
            return response()->json([
                'status' => false,
                'message' => 'سند الصرف غير موجود أو لا تملك صلاحية عرضه',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب تفاصيل سند الصرف بنجاح',
            'data' => new ExpenseResource($expense),
        ],200);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في جلب سند الصرف',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('api::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}




















