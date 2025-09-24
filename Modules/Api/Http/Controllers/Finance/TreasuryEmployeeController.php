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
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TreasuryEmployeeController extends Controller
{
    use ApiResponseTrait;
    /**
     * Display a listing of the resource.
     */
   
public function index(Request $request)
{
    try {
        $query = TreasuryEmployee::with([
            'employee:id,first_name,middle_name,nickname',
            'treasury:id,name'
        ]);

        // فلترة حسب اسم الموظف
        if ($request->filled('employee_name')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $name = $request->employee_name;
                $q->where('first_name', 'like', "%$name%")
                    ->orWhere('middle_name', 'like', "%$name%")
                    ->orWhere('nickname', 'like', "%$name%");
            });
        }

        // فلترة حسب اسم الخزينة
        if ($request->filled('treasury_name')) {
            $query->whereHas('treasury', function ($q) use ($request) {
                $q->where('name', 'like', "%" . $request->treasury_name . "%");
            });
        }

        $treasuryEmployees = $query->orderBy('id', 'desc')->paginate(10);

        $employees = Employee::select('id', 'first_name', 'middle_name', 'nickname')->get();

        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'desc')
            ->get(['id', 'name']);

      return response()->json([
    'status' => true,
    'data' => [
        'treasury_employees' => $treasuryEmployees->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'employee' => $item->employee?->full_name ?? '-', // تأكد full_name موجود
                'treasury' => $item->treasury?->name ?? '-',
            ];
        }),
        'pagination' => [
            'current_page' => $treasuryEmployees->currentPage(),
            'last_page' => $treasuryEmployees->lastPage(),
            'total' => $treasuryEmployees->total(),
            'per_page' => $treasuryEmployees->perPage(),
        ]
    ]
]);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في تحميل بيانات ربط الخزائن بالموظفين.',
            'error' => $e->getMessage()
        ], 500);
    }
}



    /**
     * Store a newly created resource in storage.
     */
public function createData()
{
    try {
        $employees = Employee::select('id', 'first_name', 'middle_name', 'nickname')->get();

        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'desc')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data' => [
                'employees' => $employees,
                'treasuries' => $treasuries
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في تحميل البيانات.',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function store(Request $request)
{
    try {
        // تحقق من صحة الطلب
        $request->validate([
            'treasury_id' => 'required|exists:accounts,id',
            'employee_id' => 'required|exists:employees,id',
        ]);

        // إنشاء السجل
        $default = TreasuryEmployee::create([
            'treasury_id' => $request->treasury_id,
            'employee_id' => $request->employee_id,
        ]);

        // تسجيل اللوق
        ModelsLog::create([
            'type' => 'product_log',
            'type_id' => $default->id,
            'type_log' => 'log',
            'description' => sprintf(
                'تم تعيين الخزينة **%s** كخزينة افتراضية للموظف **%s %s %s**',
                $default->treasury->name,
                $default->employee->first_name,
                $default->employee->middle_name,
                $default->employee->nickname
            ),
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم ربط الخزينة بالموظف بنجاح.',
            'data' => [
                'id' => $default->id,
                'employee' => $default->employee->full_name ?? null,
                'treasury' => $default->treasury->name,
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في تعيين الخزينة الافتراضية.',
            'error' => $e->getMessage()
        ], 500);
    }
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
    try {
        $treasuryEmployee = TreasuryEmployee::with(['employee', 'treasury'])->findOrFail($id);

        $employees = Employee::select('id', 'first_name', 'middle_name', 'nickname')->get();
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'DESC')
            ->get(['id', 'name']);

        return response()->json([
            'status' => true,
            'data' => [
                'record' => [
                    'id' => $treasuryEmployee->id,
                    'employee_id' => $treasuryEmployee->employee_id,
                    'treasury_id' => $treasuryEmployee->treasury_id,
                    'employee_name' => $treasuryEmployee->employee->first_name . ' ' .
                        $treasuryEmployee->employee->middle_name . ' ' .
                        $treasuryEmployee->employee->nickname,
                    'treasury_name' => $treasuryEmployee->treasury->name,
                ],
                'employees' => $employees,
                'treasuries' => $treasuries
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في تحميل بيانات الخزينة.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
    try {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'treasury_id' => 'required|exists:accounts,id',
        ]);

        $record = TreasuryEmployee::findOrFail($id);

        $oldTreasuryId = $record->treasury_id;
        $oldEmployeeId = $record->employee_id;
        
                
        $oldTreasury = Account::find($oldTreasuryId);
        $oldEmployee = Employee::find($oldEmployeeId);
        $record_name = $record->employee->full_name ?? null;
        $record_treasury = $record->treasury->name;
        // التحديث
        $record->update([
            'treasury_id' => $request->treasury_id,
            'employee_id' => $request->employee_id,
        ]);

        // الجدد
        $newTreasury = Account::find($request->treasury_id);
        $newEmployee = Employee::find($request->employee_id);

        // توليد الوصف
        if ($oldTreasuryId != $request->treasury_id && $oldEmployeeId != $request->employee_id) {
            $description = sprintf(
                'تم تغيير الخزينة الافتراضية والموظف من **%s** (الموظف: **%s %s %s**) إلى **%s** (الموظف: **%s %s %s**)',
                $oldTreasury->name,
                $oldEmployee->first_name, $oldEmployee->middle_name, $oldEmployee->nickname,
                $newTreasury->name,
                $newEmployee->first_name, $newEmployee->middle_name, $newEmployee->nickname
            );
        } elseif ($oldTreasuryId != $request->treasury_id) {
            $description = sprintf(
                'تم تغيير الخزينة الافتراضية من **%s** إلى **%s** للموظف **%s %s %s**',
                $oldTreasury->name,
                $newTreasury->name,
                $newEmployee->first_name, $newEmployee->middle_name, $newEmployee->nickname
            );
        } elseif ($oldEmployeeId != $request->employee_id) {
            $description = sprintf(
                'تم تغيير الموظف للمستودع الافتراضي **%s** من **%s %s %s** إلى **%s %s %s**',
                $newTreasury->name,
                $oldEmployee->first_name, $oldEmployee->middle_name, $oldEmployee->nickname,
                $newEmployee->first_name, $newEmployee->middle_name, $newEmployee->nickname
            );
        } else {
            $description = 'لم يتم تغيير أي شيء.';
        }

        ModelsLog::create([
            'type' => 'product_log',
            'type_id' => $record->id,
            'type_log' => 'log',
            'description' => $description,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'تم تعديل خزينة الموظف بنجاح',
            'data' => [
                'id' => $record->id,
                'emoloyee' => $record_name,
                'treasury' => $record_treasury,
            ]
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في تعديل البيانات.',
            'error' => $e->getMessage()
        ], 500);
    }
}


    /**
     * Remove the specified resource from storage.
     */
   public function destroy($id)
{
    try {
        // جلب الخزينة
        $treasury = Treasury::findOrFail($id);

        // جلب الحساب المرتبط
        $account = Account::where('treasury_id', $id)->first();

        // تسجيل الحذف في السجلات
        ModelsLog::create([
            'type' => 'finance_log',
            'type_id' => $treasury->id,
            'type_log' => 'log',
            'description' => 'تم حذف خزينة **' . mb_convert_encoding($treasury->name, 'UTF-8', 'UTF-8') . '**',
            'created_by' => auth()->id(),
        ]);

        // حذف الحساب إذا موجود
        if ($account) {
            $account->delete();
        }

        // حذف الخزينة
        $treasury->delete();

        return response()->json([
            'status' => true,
            'message' => 'تم حذف الخزينة بنجاح.'
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status' => false,
            'message' => 'فشل في حذف الخزينة.',
            'error' => $e->getMessage()
        ], 500);
    }
}

}




















