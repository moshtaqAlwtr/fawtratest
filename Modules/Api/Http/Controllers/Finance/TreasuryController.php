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
use App\Models\Branch;
use App\Models\Revenue;
use App\Models\EmployeeClientVisit;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\ReceiptCategory;
use App\Models\Receipt;
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TreasuryController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
   
public function index(Request $request)
{
    $user = auth()->user();

    if ($user->role == 'employee') {
        $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

        if ($treasuryEmployee) {
            $treasuries = Account::where('id', $treasuryEmployee->treasury_id)
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $treasuries = Account::where('id', -1)->paginate(10);
        }
    } else {
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'desc')
            ->paginate(10);
    }

    // تحويل البيانات حسب المطلوب
    $data = $treasuries->getCollection()->transform(function ($treasury) {
        return [
            'id' => $treasury->id,
            'name' => $treasury->name,
            'balance' => $treasury->balance,
            'status' => $treasury->is_active == 0 ? 'نشط' : 'متوقف',
        ];
    });

    // إعادة تعيين المجموعة المعدلة داخل الـ paginator
    $treasuries->setCollection($data);

    return response()->json([
        'status' => true,
        'data' => $treasuries,
    ]);
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
    try {
        // ✅ التحقق من البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deposit_permissions' => 'required|integer',
            'withdraw_permissions' => 'required|integer',
            'v_employee_id' => 'nullable|integer',
            'v_functional_role_id' => 'nullable|integer',
            'v_branch_id' => 'nullable|integer',
            'c_employee_id' => 'nullable|integer',
            'c_functional_role_id' => 'nullable|integer',
            'c_branch_id' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        // ✅ إنشاء الخزينة
        $treasury = new Treasury();
        $treasury->name = $request->name;
        $treasury->type = 0;
        $treasury->status = 1;
        $treasury->description = $request->description ?? 'خزينة جديدة';
        $treasury->deposit_permissions = $request->deposit_permissions;
        $treasury->withdraw_permissions = $request->withdraw_permissions;

        // صلاحيات الإيداع
        $treasury->value_of_deposit_permissions =
            $request->deposit_permissions == 1 ? $request->v_employee_id :
            ($request->deposit_permissions == 2 ? $request->v_functional_role_id : $request->v_branch_id);

        // صلاحيات السحب
        $treasury->value_of_withdraw_permissions =
            $request->withdraw_permissions == 1 ? $request->c_employee_id :
            ($request->withdraw_permissions == 2 ? $request->c_functional_role_id : $request->c_branch_id);

        $treasury->save();

        // ✅ إنشاء الحساب المرتبط
        $account = new Account();
        $account->name = $request->name;
        $account->type_accont = 0;
        $account->is_active = $request->is_active ?? 1;
        $account->parent_id = 13;
        $account->balance_type = 'debit';
        $account->code = 0;
        $account->save();

        $account->code = $account->id;
        $account->save();

        // ✅ سجل النشاط
        ModelsLog::create([
            'type' => 'finance_log',
            'type_id' => $treasury->id,
            'type_log' => 'log',
            'description' => 'تم اضافة خزينة  **' . $request->name . '**',
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم إضافة الخزينة بنجاح',
            'data' => [
                'treasury_id' => $treasury->id,
                'account_id' => $account->id,
                 'name' => $treasury->name,
            'balance' => $treasury->balance,
            'status' => $treasury->is_active == 0 ? 'نشط' : 'متوقف',
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء إضافة الخزينة',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function employees()
{
    $employees = User::where('role', 'employee')
        ->select('id', 'name')
        ->get();

    return response()->json([
        'status'  => true,
        'message' => 'تم جلب الموظفين بنجاح',
        'data'    => $employees
    ]);
}


public function branchs()
{
    $employees = Branch::
        select('id', 'name')
        ->get();

    return response()->json([
        'status'  => true,
        'message' => 'تم جلب الفروع بنجاح',
        'data'    => $employees
    ]);
}

public function list_transfer()
{
    try {
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'desc')
            ->get(['id', 'name', 'balance', 'is_active']);

        $formatted = $treasuries->map(function ($treasury) {
            return [
                'id' => $treasury->id,
                'name' => $treasury->name,
                'balance' => number_format($treasury->balance, 2),
                'status' => $treasury->is_active == 0 ? 'نشط' : 'غير نشط'
            ];
        });

        return response()->json([
            'status' => true,
            'data' => $formatted
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء جلب الخزائن.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function transfer(Request $request)
{
    try {
        // ✅ التحقق من المدخلات
        $request->validate([
            'from_treasury_id' => 'required|exists:accounts,id',
            'to_treasury_id' => 'required|exists:accounts,id|different:from_treasury_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $amount = $request->amount;

        $fromTreasury = Account::findOrFail($request->from_treasury_id);
        $toTreasury = Account::findOrFail($request->to_treasury_id);

        // ✅ التحقق من الرصيد
        if ($fromTreasury->balance < $amount) {
            return response()->json([
                'status' => false,
                'message' => 'الرصيد غير كافٍ في الخزينة المرسلة.',
            ], 422);
        }

        // ✅ تحديث الأرصدة
        $fromTreasury->updateBalance($amount, 'subtract');
        $toTreasury->updateBalance($amount, 'add');

        // ✅ إنشاء القيد المحاسبي
        $journalEntry = JournalEntry::create([
            'reference_number' => $fromTreasury->id,
            'date' => now(),
            'description' => 'تحويل المالية',
            'status' => 1,
            'currency' => 'SAR',
            'created_by_employee' => auth()->id(),
        ]);

        // دائن - من الخزينة
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $fromTreasury->id,
            'description' => 'تحويل المالية من ' . $fromTreasury->code,
            'debit' => 0,
            'credit' => $amount,
            'is_debit' => false,
        ]);

        // مدين - إلى الخزينة
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $toTreasury->id,
            'description' => 'تحويل المالية إلى ' . $toTreasury->code,
            'debit' => $amount,
            'credit' => 0,
            'is_debit' => true,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم التحويل بنجاح.',
            'data' => [
                'journal_entry_id' => $journalEntry->id,
                'from' => $fromTreasury->name,
                'to' => $toTreasury->name,
                'amount' => $amount
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء تنفيذ التحويل.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Show the specified resource.
     */
 
public function show($id, Request $request)
{
    try {
        // جلب الخزينة
        $treasury = $this->getTreasury($id);

        // البيانات الأساسية
        $treasuryData = [
            'id' => $treasury->id,
            'name' => $treasury->name,
            'type' => $treasury->type_accont == 0 ? 'خزينة' : 'حساب بنكي',
            'status' => $treasury->is_active == 0 ? 'نشط' : 'غير نشط',
            'balance' => number_format($treasury->balance, 2),
            'description' => $treasury->description,
        ];

        // العمليات
        $operationsResponse = $this->getOperationsData($id);

        if ($operationsResponse instanceof \Illuminate\Http\JsonResponse) {
            $operations = $operationsResponse->getData(true);
        } elseif (is_array($operationsResponse)) {
            $operations = $operationsResponse;
        } else {
            $operations = ['operations' => [], 'pagination' => []];
        }

        // التحويلات
        $transfersResponse = $this->getTransfersData($id);
        $transfers = $transfersResponse->getData(true)['transfers'] ?? [];

        return response()->json([
            'status' => true,
            'data' => [
                'treasury' => $treasuryData,
                'operations' => $operations['operations'] ?? [],
                'pagination' => $operations['pagination'] ?? [],
                'transfers' => $transfers,
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'حدث خطأ أثناء تحميل بيانات الخزينة.',
            'error' => $e->getMessage(),
        ], 500);
    }
}




public function getOperationsData($id)
{
    $treasury = $this->getTreasury($id);

    // جلب جميع العمليات
    $transactions = $this->getTransactions($id);
    $transfers = $this->getTransfers($id)->load(['details.account']);
    $expenses = $this->getExpenses($id);
    $revenues = $this->getRevenues($id);
    $receipts = $this->getReceipts($id);
    $payments = $this->getPayments($id);

    // معالجة جميع العمليات مع التحويلات
    $allOperations = $this->processAllOperations($transactions, $transfers, $expenses, $revenues, $receipts, $payments, $treasury);

    // ترتيب العمليات حسب التاريخ
    usort($allOperations, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    // تطبيق الفلاتر
    $allOperations = $this->applyFilters($allOperations);

    // تقسيم إلى صفحات
    $operationsPaginator = $this->paginateOperations($allOperations);

  // نكشف نوع الرد حسب إذا كان استدعاء من واجهة أو API
if (request()->wantsJson() || request()->is('api/*')) {
    return [
        'operations' => $operationsPaginator->items(),
        'pagination' => [
            'current_page' => $operationsPaginator->currentPage(),
            'last_page' => $operationsPaginator->lastPage(),
            'per_page' => $operationsPaginator->perPage(),
            'total' => $operationsPaginator->total(),
            'has_more_pages' => $operationsPaginator->hasMorePages(),
            'prev_page_url' => $operationsPaginator->previousPageUrl(),
            'next_page_url' => $operationsPaginator->nextPageUrl(),
        ]
    ];
}



    return compact('operationsPaginator');
}
 private function getTreasury($id)
    {
        return Account::findOrFail($id);
    }



    private function getTransactions($id)
    {
        return JournalEntryDetail::where('account_id', $id)
            ->with(['journalEntry' => function ($query) {
                $query->with('invoice', 'client');
            }])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getTransfers($id)
{
    return JournalEntry::whereHas('details', function ($query) use ($id) {
            $query->where('account_id', $id);
        })
        ->with(['details.account'])
        ->where('description', 'تحويل المالية')
        ->orderBy('created_at', 'desc') // تغيير الترتيب ليكون من الأحدث للأقدم
        ->get();
}


    private function getExpenses($id)
    {
        return Expense::where('treasury_id', $id)
            ->with(['expenses_category', 'branch', 'client'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getRevenues($id)
    {
        return Revenue::where('account_id', $id)
            ->with(['account', 'paymentVoucher', 'treasury', 'bankAccount', 'journalEntry'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getReceipts($id)
    {
        return Receipt::where('treasury_id', $id)
            ->with(['client', 'incomes_category', 'account'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getPayments($id)
    {
        return PaymentsProcess::where('treasury_id', $id)
            ->with(['client', 'invoice', 'employee'])
            ->orderBy('created_at', 'asc')
            ->get();
    }


private function processAllOperations($transactions, $transfers, $expenses, $revenues, $receipts, $payments, $treasury)
{
    $allOperations = [];

    // معالجة التحويلات
    foreach ($transfers as $transfer) {
        $fromAccount = null;
        $toAccount = null;
        $amount = $transfer->details->sum('debit');

        foreach ($transfer->details as $detail) {
            if ($detail->is_debit) {
                $toAccount = $detail->account;
            } else {
                $fromAccount = $detail->account;
            }
        }

        if ($fromAccount && $toAccount && ($fromAccount->id == $treasury->id || $toAccount->id == $treasury->id)) {
            $isDeposit = $toAccount->id == $treasury->id;

            $allOperations[] = [
                'operation' => 'تحويل مالي',
                'description' => $isDeposit ?
                    'تحويل من: ' . $fromAccount->name :
                    'تحويل إلى: ' . $toAccount->name,
                'deposit' => $isDeposit ? $amount : 0,
                'withdraw' => $isDeposit ? 0 : $amount,
                'balance_after' => 0, // سيتم حسابه لاحقاً
                'date' => $transfer->date,
                'type' => 'transfer',
                'reference_number' => $transfer->reference_number,
                'from_account' => $fromAccount,
                'to_account' => $toAccount,
                'amount' => $amount,
                'id' => $transfer->id,
                'journal_entry_id' => $transfer->id,
                'color_class' => 'text-warning',
                'icon' => 'fa-exchange-alt',
                'timestamp' => strtotime($transfer->date),
                'operation_amount' => $isDeposit ? $amount : -$amount,
                'created_at' => $transfer->created_at ?? now(),
                'sort_key' => strtotime($transfer->date) . '_' . $transfer->id
            ];
        }
    }

    // معالجة المصروفات (سحب من الخزينة)
    foreach ($expenses as $expense) {
        $allOperations[] = [
            'operation' => 'سند صرف',
            'description' => $expense->description,
            'deposit' => 0,
            'withdraw' => $expense->amount,
            'balance_after' => 0,
            'date' => $expense->date,
            'invoice' => null,
            'client' => $expense->client,
            'type' => 'expense',
            'journal_entry_id' => $expense->journal_entry_id ?? null,
            'color_class' => 'text-danger',
            'icon' => 'fa-minus-circle',
            'timestamp' => strtotime($expense->date),
            'operation_amount' => -$expense->amount,
            'created_at' => $expense->created_at ?? now(),
            'sort_key' => strtotime($expense->date) . '_' . $expense->id
        ];
    }

    // معالجة سندات القبض (إيداع في الخزينة)
    foreach ($receipts as $receipt) {
        $allOperations[] = [
            'operation' => 'سند قبض',
            'description' => $receipt->description,
            'deposit' => $receipt->amount,
            'withdraw' => 0,
            'balance_after' => 0,
            'date' => $receipt->date,
            'invoice' => null,
            'client' => $receipt->client,
            'type' => 'receipt',
            'journal_entry_id' => $receipt->journal_entry_id ?? null,
            'color_class' => 'text-success',
            'icon' => 'fa-file-invoice',
            'timestamp' => strtotime($receipt->date),
            'operation_amount' => $receipt->amount,
            'created_at' => $receipt->created_at ?? now(),
            'sort_key' => strtotime($receipt->date) . '_' . $receipt->id
        ];
    }

    // معالجة المدفوعات (إيداع في الخزينة)
    foreach ($payments as $payment) {
        $allOperations[] = [
            'operation' => 'عملية دفع',
            'description' => $payment->invoice ?
                'دفعة فاتورة رقم: ' . $payment->invoice->invoice_number :
                'عملية دفع',
            'deposit' => $payment->amount,
            'withdraw' => 0,
            'balance_after' => 0,
            'date' => $payment->payment_date,
            'invoice' => $payment->invoice,
            'client' => $payment->client,
            'type' => 'payment',
            'journal_entry_id' => $payment->journal_entry_id ?? null,
            'color_class' => 'text-primary',
            'icon' => 'fa-credit-card',
            'timestamp' => strtotime($payment->payment_date),
            'operation_amount' => $payment->amount,
            'created_at' => $payment->created_at ?? now(),
            'sort_key' => strtotime($payment->payment_date) . '_' . $payment->id
        ];
    }

    // معالجة الإيرادات (إيداع في الخزينة)
    foreach ($revenues as $revenue) {
        $allOperations[] = [
            'operation' => 'إيراد',
            'description' => $revenue->description ?? 'إيراد',
            'deposit' => $revenue->amount,
            'withdraw' => 0,
            'balance_after' => 0,
            'date' => $revenue->date ?? $revenue->created_at,
            'invoice' => null,
            'client' => null,
            'type' => 'revenue',
            'journal_entry_id' => $revenue->journal_entry_id ?? null,
            'color_class' => 'text-success',
            'icon' => 'fa-plus-circle',
            'timestamp' => strtotime($revenue->date ?? $revenue->created_at),
            'operation_amount' => $revenue->amount,
            'created_at' => $revenue->created_at ?? now(),
            'sort_key' => strtotime($revenue->date ?? $revenue->created_at) . '_' . $revenue->id
        ];
    }

    // ترتيب العمليات حسب التاريخ والـ ID (من الأقدم للأحدث) لحساب الرصيد بشكل صحيح
    usort($allOperations, function ($a, $b) {
        // إذا كان التاريخ مختلف، رتب حسب التاريخ
        if ($a['timestamp'] != $b['timestamp']) {
            return $a['timestamp'] - $b['timestamp'];
        }
        // إذا كان نفس التاريخ، رتب حسب الـ ID (created_at ثم ID)
        return strcmp($a['sort_key'], $b['sort_key']);
    });

    // حساب الرصيد التراكمي بدءاً من رصيد صفر
    $currentBalance = 0;

    // حساب الرصيد بعد كل عملية
    foreach ($allOperations as &$operation) {
        // إضافة المبلغ للرصيد الحالي
        $currentBalance += $operation['operation_amount'];

        // تحديث الرصيد بعد العملية
        $operation['balance_after'] = $currentBalance;

        // تحديد نوع التغيير في الرصيد
        if ($operation['operation_amount'] > 0) {
            $operation['balance_change'] = '+' . number_format($operation['operation_amount'], 2);
        } else {
            $operation['balance_change'] = number_format($operation['operation_amount'], 2);
        }
    }

    // إعادة ترتيب العمليات للعرض (من الأحدث للأقدم) بعد حساب الرصيد
    usort($allOperations, function ($a, $b) {
        // إذا كان التاريخ مختلف، رتب حسب التاريخ (تنازلي)
        if ($a['timestamp'] != $b['timestamp']) {
            return $b['timestamp'] - $a['timestamp'];
        }
        // إذا كان نفس التاريخ، رتب حسب الـ ID (تنازلي)
        return strcmp($b['sort_key'], $a['sort_key']);
    });

    return $allOperations;
}
    private function applyFilters($operations)
    {
        $fromDate = request('from_date');
        $toDate = request('to_date');
        $operationType = request('operation_type');
        $amountFrom = request('amount_from');
        $amountTo = request('amount_to');

        if ($fromDate) {
            $operations = array_filter($operations, function($op) use ($fromDate) {
                return strtotime($op['date']) >= strtotime($fromDate);
            });
        }

        if ($toDate) {
            $operations = array_filter($operations, function($op) use ($toDate) {
                return strtotime($op['date']) <= strtotime($toDate);
            });
        }

        if ($operationType) {
            $operations = array_filter($operations, function($op) use ($operationType) {
                return $op['type'] === $operationType;
            });
        }

        if ($amountFrom) {
            $operations = array_filter($operations, function($op) use ($amountFrom) {
                $amount = max($op['deposit'], $op['withdraw']);
                return $amount >= $amountFrom;
            });
        }

        if ($amountTo) {
            $operations = array_filter($operations, function($op) use ($amountTo) {
                $amount = max($op['deposit'], $op['withdraw']);
                return $amount <= $amountTo;
            });
        }

        return array_values($operations);
    }

    private function updateBalance($currentBalance, $amount, $type)
    {
        return $type === 'إيداع' ? $currentBalance + $amount : $currentBalance - $amount;
    }

    private function paginateOperations($allOperations)
    {
        $perPage = 100;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedOperations = array_slice($allOperations, $offset, $perPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedOperations,
            count($allOperations),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );
    }


    public function updateStatus($id)
    {
        // البحث عن العنصر باستخدام الـ ID
        $treasury = Account::find($id);

        // إذا لم يتم العثور على العنصر
        if (!$treasury) {
            return redirect()
                ->back()
                ->with(['error' => 'الخزينة غير موجود!']);
        }

        // تحديث حالة العنصر
        $treasury->update(['is_active' => !$treasury->is_active]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()
            ->back()
            ->with(['success' => 'تم تغيير حالة الخزينة بنجاح!']);
        ModelsLog::create([
            'type' => 'finance_log',
            'type_id' => $treasury->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف خزينة **' . $treasury->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // حذف الحساب المرتبط بالخزينة إذا وجد
        if ($account) {
            $account->delete();
        }

        // حذف الخزينة
        $treasury->delete();

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('treasury.index')->with('success', 'تم حذف الخزينة بنجاح!');
    }

public function getTransfersData($id)
{
    $transfers = $this->getTransfers($id);
    $formattedTransfers = [];

    foreach ($transfers as $transfer) {
        $fromAccount = null;
        $toAccount = null;
        $amount = $transfer->details->sum('debit');

        foreach ($transfer->details as $detail) {
            if ($detail->is_debit) {
                $toAccount = $detail->account;
            } else {
                $fromAccount = $detail->account;
            }
        }

        if ($fromAccount && $toAccount) {
            $formattedTransfers[] = [
                'id' => $transfer->id,
                'reference_number' => $transfer->reference_number,
                'date' => $transfer->date,
                'from_account' => $fromAccount,
                'to_account' => $toAccount,
                'amount' => $amount
            ];
        }
    }

    return response()->json([
        'transfers' => $formattedTransfers
    ]);
}



  private function getBranches()
    {
        return Branch::all();
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




















