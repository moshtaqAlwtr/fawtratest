<?php

namespace Modules\Api\Http\Controllers\Finance;

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
use App\Models\EmployeeClientVisit;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\notifications;
use App\Models\ReceiptCategory;
use App\Models\Receipt;
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReceiptController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
   
public function index(Request $request)
{
    try {
        $user = auth()->user();

        // per_page آمن (افتراضي 30 وحد أعلى 100)
        $perPage = (int) $request->input('per_page', 30);
        $perPage = ($perPage > 0 && $perPage <= 100) ? $perPage : 30;

        // الاستعلام + الفلاتر
        $query = Receipt::query()->orderByDesc('id');

        // keywords (مجمّعة داخل where(function) لتفادي تعارض OR)
        $query->when($request->keywords, function ($q, $v) {
            $q->where(function ($sub) use ($v) {
                $sub->where('code', 'like', "%{$v}%")
                    ->orWhere('description', 'like', "%{$v}%");
            });
        });

        $query->when($request->from_date, fn($q, $v) => $q->where('date', '>=', $v));
        $query->when($request->to_date, fn($q, $v) => $q->where('date', '<=', $v));
        $query->when($request->created_by, fn($q, $v) => $q->where('created_by', $v));
        $query->when($request->sub_account, fn($q, $v) => $q->where('account_id', $v));

        // تقييد الموظف
        if ($user->role === 'employee') {
            $query->where('created_by', $user->id);
        }

        // تنفيذ الباقينشن
        $receipts = $query->paginate($perPage);

        // --- المجاميع (نفس منطق التقييد + فلتر الحساب) ---
        $totalAll   = Receipt::query();
        $total7     = Receipt::where('date', '>=', now()->subDays(7));
        $total30    = Receipt::where('date', '>=', now()->subDays(30));
        $total365   = Receipt::where('date', '>=', now()->subDays(365));

        if ($user->role === 'employee') {
            $totalAll->where('created_by', $user->id);
            $total7->where('created_by', $user->id);
            $total30->where('created_by', $user->id);
            $total365->where('created_by', $user->id);
        }

        if ($request->filled('sub_account')) {
            $totalAll->where('account_id', $request->sub_account);
            $total7->where('account_id', $request->sub_account);
            $total30->where('account_id', $request->sub_account);
            $total365->where('account_id', $request->sub_account);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم جلب سندات القبض بنجاح',
            'data' => ReceiptResource::collection($receipts)->resolve(),
            'pagination' => [
                'total'         => $receipts->total(),
                'count'         => $receipts->count(),
                'per_page'      => $receipts->perPage(),
                'current_page'  => $receipts->currentPage(),
                'total_pages'   => $receipts->lastPage(),
                'next_page_url' => $receipts->nextPageUrl(),
                'prev_page_url' => $receipts->previousPageUrl(),
                'from'          => $receipts->firstItem(),
                'to'            => $receipts->lastItem(),
                'path'          => $receipts->path(),
            ],
            'totals' => [
                'all_time'      => (float) $totalAll->sum('amount'),
                'last_7_days'   => (float) $total7->sum('amount'),
                'last_30_days'  => (float) $total30->sum('amount'),
                'last_365_days' => (float) $total365->sum('amount'),
            ],
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل في جلب سندات القبض',
            'error'   => $e->getMessage(),
        ], 500);
    }
}





    /**
     * Show the form for creating a new resource.
     */
public function createData(Request $request)
{
    try {
        $user = auth()->user();

        $incomes_categories = ReceiptCategory::select('id', 'name')->get();
        $treasuries = Treasury::select('id', 'name')->get();

        // الحسابات حسب صلاحيات الموظف
        if ($user->role === 'employee') {
            $employeeGroupIds = EmployeeGroup::where('employee_id', $user->employee_id)->pluck('group_id');

            $accounts = $employeeGroupIds->isNotEmpty()
                ? Account::whereNotNull('client_id')
                    ->whereHas('client.Neighborhoodname.Region', function ($q) use ($employeeGroupIds) {
                        $q->whereIn('id', $employeeGroupIds);
                    })->get()
                : collect(); // فارغ إذا مافي مجموعات
        } else {
            $accounts = Account::whereNotNull('client_id')->get();
        }

        $account_storage = Account::where('parent_id', 13)->get();

        // كود تلقائي بدون تكرار
        $nextCode = (int) (Receipt::max('code') ?? 0) + 1;
        while (Receipt::where('code', $nextCode)->exists()) {
            $nextCode++;
        }

        // الحصول على الخزينة الأساسية
        $mainTreasury = null;
        if ($user && $user->employee_id) {
            $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
            $mainTreasury = $treasuryEmployee && $treasuryEmployee->treasury_id
                ? Account::find($treasuryEmployee->treasury_id)
                : Account::where('name', 'الخزينة الرئيسية')->first();
        } else {
            $mainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
        }

        if (!$mainTreasury) {
            return response()->json([
                'status' => false,
                'message' => 'لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.',
            ], 422);
        }

        $taxes = TaxSitting::all();
        $account_setting = AccountSetting::where('user_id', $user->id)->first();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب بيانات إنشاء سند القبض',
            'data' => [
                'next_code' => str_pad($nextCode, 5, '0', STR_PAD_LEFT),
                'categories' => $incomes_categories,
                'treasuries' => $treasuries,
                'accounts' => $accounts,
                'account_storage' => $account_storage,
                'main_treasury' => $mainTreasury,
                'taxes' => $taxes,
                'account_setting' => $account_setting,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء تحميل البيانات',
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
        $user = auth()->user();

        // التحقق من وجود حساب العميل
        $clientAccount = Account::with('client')->findOrFail($request->account_id);

        if (!$clientAccount->client) {
            return response()->json([
                'status' => false,
                'message' => 'الحساب غير مرتبط بعميل.'
            ], 422);
        }

        // التحقق من وجود فواتير غير مسددة
        $hasUnpaid = Invoice::where('client_id', $clientAccount->client->id)
            ->where('payment_status', '!=', 1)->exists();

        if ($hasUnpaid) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إنشاء سند قبض لوجود فواتير غير مسددة.'
            ], 422);
        }

        // التحقق من الرصيد
        if ($clientAccount->balance <= 0) {
            return response()->json([
                'status' => false,
                'message' => 'لا يمكن إصدار السند بسبب عدم وجود رصيد.'
            ], 422);
        }

        // إنشاء السند
        $income = new Receipt();
        $income->fill([
            'code' => $request->code,
            'amount' => $request->amount,
            'description' => $request->description,
            'date' => $request->date,
            'incomes_category_id' => $request->incomes_category_id,
            'seller' => $request->seller,
            'account_id' => $request->account_id,
            'treasury_id' => $request->treasury_id,
            'is_recurring' => $request->boolean('is_recurring'),
            'recurring_frequency' => $request->recurring_frequency,
            'end_date' => $request->end_date,
            'tax1' => $request->tax1,
            'tax2' => $request->tax2,
            'tax1_amount' => $request->tax1_amount,
            'tax2_amount' => $request->tax2_amount,
            'cost_centers_enabled' => $request->boolean('cost_centers_enabled'),
            'created_by' => $user->id,
        ]);

        // رفع المرفقات
        if ($request->hasFile('attachments')) {
            $income->attachments = $this->UploadImage('assets/uploads/incomes', $request->file('attachments'));
        }

        $income->save();

        // تحديث الزيارة
        EmployeeClientVisit::where('employee_id', $user->id)
            ->where('client_id', $clientAccount->client->id)
            ->latest()->first()?->update([
                'status' => 'active',
                'updated_at' => now()
            ]);

        // إرسال إشعار
        notifications::create([
            'user_id' => $user->id,
            'type' => 'Receipt',
            'title' => $user->name . ' أنشأ سند قبض',
            'description' => 'سند قبض رقم ' . $income->code . ' لـ ' . $clientAccount->name . ' بقيمة ' . number_format($income->amount, 2) . ' ر.س',
        ]);

        // تسجيل في سجل العمليات
        ModelsLog::create([
            'type' => 'incomes',
            'type_id' => $income->id,
            'type_log' => 'log',
            'description' => sprintf('تم انشاء سند قبض رقم **%s** بقيمة **%d**', $income->code, $income->amount),
            'created_by' => $user->id,
        ]);

        // تحديث رصيد الخزنة
        $MainTreasury = Account::findOrFail($request->treasury_id);
        $MainTreasury->increment('balance', $income->amount);

        // تحديث رصيد العميل
        $clientAccount->decrement('balance', $income->amount);

        // إنشاء قيد اليومية
        $journalEntry = JournalEntry::create([
            'reference_number' => $income->code,
            'date' => $income->date,
            'description' => 'سند قبض رقم ' . $income->code,
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $clientAccount->client->id,
            'created_by_employee' => $user->id,
        ]);

        JournalEntryDetail::insert([
            [
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $MainTreasury->id,
                'description' => 'استلام مبلغ من سند قبض',
                'debit' => $income->amount,
                'credit' => 0,
                'is_debit' => true,
            ],
            [
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $income->account_id,
                'description' => 'إيرادات من سند قبض',
                'debit' => 0,
                'credit' => $income->amount,
                'is_debit' => false,
            ]
        ]);

        DB::commit();

        return response()->json([
            'status' => true,
            'message' => 'تم إصدار سند القبض بنجاح',
            'data' => new ReceiptResource($income)
        ], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('خطأ في إضافة سند قبض: ' . $e->getMessage());

        return response()->json([
            'status' => false,
            'message' => 'فشل إصدار سند القبض: ' . $e->getMessage()
        ], 500);
    }
}



    /**
     * Show the specified resource.
     */
  public function show($id)
{
    try {
        $receipt = Receipt::with([
            'account.client',
            'user',
            'incomes_category',
            'treasury',
        ])->findOrFail($id);

        return response()->json([
            'status' => true,
            'message' => 'تم جلب سند القبض بنجاح',
            'data' => new ReceiptResource($receipt)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'تعذر العثور على السند',
            'error' => $e->getMessage()
        ], 404);
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




















