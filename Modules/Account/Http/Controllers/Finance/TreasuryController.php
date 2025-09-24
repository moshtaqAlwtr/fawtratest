<?php

namespace Modules\Account\Http\Controllers\Finance;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\Log as ModelsLog;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\PaymentsProcess;
use App\Models\Receipt;
use App\Models\Revenue;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TreasuryController extends Controller
{
    public function index()
{
    $user = auth()->user();

    if ($user->role == 'employee') {
        // الحصول على خزائن الموظف فقط
        $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

        if ($treasuryEmployee) {
            $treasuries = Account::where('id', $treasuryEmployee->treasury_id)
                ->orderBy('id', 'DESC')
                ->paginate(10);
        } else {
            // إذا لم يكن للموظف خزينة معينة، لا نعرض أي شيء
            $treasuries = Account::where('id', -1)->paginate(10);
        }
    } else {
        // عرض جميع الخزائن للمستخدمين الآخرين
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }

    return view('account::finance.treasury.index', compact('treasuries'));
}

    public function create()
    {
        $employees = Employee::select()->get();
        return view('account::finance.treasury.carate', compact('employees'));
    }

    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        // $request->validate([
        //     'name' => 'required|string|max:255',
        //     'description' => 'nullable|string',
        //     'deposit_permissions' => 'required|integer',
        //     'withdraw_permissions' => 'required|integer',
        //     'value_of_deposit_permissions' => 'nullable|string',
        //     'value_of_withdraw_permissions' => 'nullable|string',
        //     'is_active' => 'nullable|boolean',
        // ]);

        // إنشاء الخزينة في جدول Treasury
        $treasury = new Treasury();
        $treasury->name = $request->name;
        $treasury->type = 0; // نوع الحساب (خزينة)
        $treasury->status = 1; // حالة الخزينة
        $treasury->description = $request->description ?? 'خزينة جديدة'; // وصف الخزينة
        $treasury->deposit_permissions = $request->deposit_permissions;
        $treasury->withdraw_permissions = $request->withdraw_permissions;

        #permissions-----------------------------------

        # view employee
        if ($request->deposit_permissions == 1) {
            $treasury->value_of_deposit_permissions = $request->v_employee_id;
        }
        # view functional_role
        elseif ($request->deposit_permissions == 2) {
            $treasury->value_of_deposit_permissions = $request->v_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_deposit_permissions = $request->v_branch_id;
        }

        # view employee
        if ($request->withdraw_permissions == 1) {
            $treasury->value_of_withdraw_permissions = $request->c_employee_id;
        }
        # view functional_role
        elseif ($request->withdraw_permissions == 2) {
            $treasury->value_of_withdraw_permissions = $request->c_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_withdraw_permissions = $request->c_branch_id;
        }

        // حفظ الخزينة مرة واحدة فقط
        $treasury->save();

        // إنشاء الحساب المرتبط في جدول Account
        $account = new Account();
        $account->name = $request->name;
        $account->type_accont = 0; // نوع الحساب (خزينة)
        $account->is_active = $request->is_active ?? 1; // حالة الحساب (افتراضي: نشط)
        $account->parent_id = 13; // الأب الافتراضي


        // $account->code = $this->generateNextCode(13);

        $account->balance_type = 'debit'; // نوع الرصيد (مدين)

      $account->balance_type = 'debit'; // نوع الرصيد (مدين)
      $account->code = 0;

        // $account->treasury_id = $treasury->id; // ربط الحساب بالخزينة
        $account->save();

        $account->code = $account->id;
           $account->save();


        // تسجيل النشاط في جدول السجلات
        ModelsLog::create([
            'type' => 'finance_log',
            'type_id' => $treasury->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم اضافة خزينة  **' . $request->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('treasury.index')->with('success', 'تم إضافة الخزينة بنجاح!');
    }

  public function generateNextCode($parentId)
{
    do {
        $lastCode = Account::where('parent_id', $parentId)->orderBy('code', 'DESC')->value('code');
        $newCode = $lastCode ? $lastCode + 1 : 1;
    } while (Account::where('code', $newCode)->exists()); // تأكيد عدم التكرار

    return $newCode;
}


    public function transferCreate()
    {
        // جلب الخزائن من جدول الحسابات (accounts) حيث parent_id هو 13 أو 15
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'DESC')
            ->get();

        return view('account::finance.treasury.transferCreate', compact('treasuries'));
    }

    public function transferTreasuryStore(Request $request)
    {
        $request->validate([
            'from_treasury_id' => 'required|exists:accounts,id',
            'to_treasury_id' => 'required|exists:accounts,id|different:from_treasury_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $fromTreasury = Account::find($request->from_treasury_id);
        $toTreasury = Account::find($request->to_treasury_id);

        // تحقق من توفر الرصيد
        if ($fromTreasury->balance < $request->amount) {
            return back()->withErrors(['error' => 'الرصيد غير كافٍ في الخزينة المختارة.']);
        }

        // خصم من الخزينة المرسلة
        $fromTreasury->updateBalance($request->amount, 'subtract');

        // إضافة إلى الخزينة المستقبلة
        $toTreasury->updateBalance($request->amount, 'add');

        // # القيد
        // إنشاء القيد المحاسبي للتحويل
        $journalEntry = JournalEntry::create([
            'reference_number' => $fromTreasury->id,
            'date' => now(),
            'description' => 'تحويل المالية',
            'status' => 1,
            'currency' => 'SAR',

            'created_by_employee' => Auth::id(),
        ]);

        // إضافة تفاصيل القيد المحاسبي
        // 1. حساب المورد (دائن)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $fromTreasury->id, // حساب المورد
            'description' => 'تحويل المالية من ' . $fromTreasury->code,
            'debit' => 0,
            'credit' => $request->amount, //دائن
            'is_debit' => false,
        ]);

        // 2. حساب الخزينة (مدين)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $toTreasury->id, // حساب المبيعات
            'description' => 'تحوي المالية الى' . $toTreasury->code,
            'debit' => $request->amount, //مدين
            'credit' => 0,
            'is_debit' => true,
        ]);

        return redirect()->route('treasury.index')->with('success', 'تم التحويل بنجاح!');
    }

    public function transferEdit($id)
    {
        // جلب التحويل المطلوب
        $transfer = JournalEntry::findOrFail($id);

        // جلب الخزائن
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'DESC')
            ->get();

        return view('account::finance.treasury.transferEdit', compact('transfer', 'treasuries'));
    }

    public function transferTreasuryUpdate(Request $request, $id)
    {
        $request->validate([
            'from_treasury_id' => 'required|exists:accounts,id',
            'to_treasury_id' => 'required|exists:accounts,id|different:from_treasury_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // جلب التحويل المطلوب
        $journalEntry = JournalEntry::findOrFail($id);

        // جلب الخزينة المصدر والهدف
        $fromTreasury = Account::find($request->from_treasury_id);
        $toTreasury = Account::find($request->to_treasury_id);

        // تحقق من توفر الرصيد في الخزينة المصدر
        if ($fromTreasury->balance < $request->amount) {
            return back()->withErrors(['error' => 'الرصيد غير كافٍ في الخزينة المختارة.']);
        }

        // التراجع عن التحويل السابق
        foreach ($journalEntry->details as $detail) {
            if ($detail->is_debit) {
                // التراجع عن الإضافة إلى الخزينة المستقبلة
                $toTreasury->updateBalance($detail->debit, 'subtract');
            } else {
                // التراجع عن الخصم من الخزينة المرسلة
                $fromTreasury->updateBalance($detail->credit, 'add');
            }
        }

        // تحديث القيد المحاسبي
        $journalEntry->update([
            'reference_number' => $fromTreasury->id,
            'date' => now(),
            'description' => 'تحويل المالية',
            'status' => 1,
            'currency' => 'SAR',
            'created_by_employee' => Auth::id(),
        ]);

        // تحديث تفاصيل القيد المحاسبي
        foreach ($journalEntry->details as $detail) {
            if ($detail->is_debit) {
                // تحديث الخزينة المستقبلة
                $detail->update([
                    'account_id' => $toTreasury->id,
                    'description' => 'تحويل المالية إلى ' . $toTreasury->code,
                    'debit' => $request->amount,
                    'credit' => 0,
                ]);
            } else {
                // تحديث الخزينة المرسلة
                $detail->update([
                    'account_id' => $fromTreasury->id,
                    'description' => 'تحويل المالية من ' . $fromTreasury->code,
                    'debit' => 0,
                    'credit' => $request->amount,
                ]);
            }
        }

        // تطبيق التحويل الجديد
        $fromTreasury->updateBalance($request->amount, 'subtract');
        $toTreasury->updateBalance($request->amount, 'add');

        return redirect()->route('treasury.index')->with('success', 'تم تحديث التحويل بنجاح!');
    }

    public function edit($id)
    {
        $treasury = Account::findOrFail($id);
        $employees = Employee::select()->get();
        return view('account::finance.treasury.edit', compact('treasury', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $treasury = Account::findOrFail($id);

        $treasury->name = $request->name;
        $oldName = $treasury->name;

        $treasury->deposit_permissions = $request->deposit_permissions;
        $treasury->withdraw_permissions = $request->withdraw_permissions;
        $treasury->value_of_deposit_permissions = $request->value_of_deposit_permissions;
        $treasury->value_of_withdraw_permissions = $request->value_of_withdraw_permissions;

        ModelsLog::create([
            'type' => 'finance_log',
            'type_id' => $treasury->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم تعديل الخزينة من **' . $oldName . '** إلى **' . $treasury->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        #permissions-----------------------------------

        # view employee
        if ($request->deposit_permissions == 1) {
            $treasury->value_of_deposit_permissions = $request->v_employee_id;
        }
        # view functional_role
        elseif ($request->deposit_permissions == 2) {
            $treasury->value_of_deposit_permissions = $request->v_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_deposit_permissions = $request->v_branch_id;
        }

        # view employee
        if ($request->withdraw_permissions == 1) {
            $treasury->value_of_withdraw_permissions = $request->c_employee_id;
        }
        # view functional_role
        elseif ($request->withdraw_permissions == 2) {
            $treasury->value_of_withdraw_permissions = $request->c_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_withdraw_permissions = $request->c_branch_id;
        }

        $treasury->update();
        return redirect()->route('treasury.index')->with(key: ['success' => 'تم تحديث الخزينة بنجاج !!']);
    }

    public function create_account_bank()
    {
        $employees = Employee::select()->get();
        return view('account::finance.treasury.create_account_bank', compact('employees'));
    }

    public function store_account_bank(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'currency' => 'required|string|max:255',
            'status' => 'required|integer',
            'description' => 'nullable|string',
            'deposit_permissions' => 'required|integer',
            'withdraw_permissions' => 'required|integer',
            'value_of_deposit_permissions' => 'nullable|string',
            'value_of_withdraw_permissions' => 'nullable|string',
        ]);

        // إنشاء الحساب البنكي في جدول Treasury
        $treasury = new Treasury();
        $treasury->name = $request->name;
        $treasury->type = 1; // نوع الحساب (بنكي)
        $treasury->bank_name = $request->bank_name;
        $treasury->account_number = $request->account_number;
        $treasury->currency = $request->currency;
        $treasury->status = $request->status;
        $treasury->description = $request->description ?? 'حساب بنكي جديد';
        $treasury->deposit_permissions = $request->deposit_permissions;
        $treasury->withdraw_permissions = $request->withdraw_permissions;

        #permissions-----------------------------------

        # view employee
        if ($request->deposit_permissions == 1) {
            $treasury->value_of_deposit_permissions = $request->v_employee_id;
        }
        # view functional_role
        elseif ($request->deposit_permissions == 2) {
            $treasury->value_of_deposit_permissions = $request->v_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_deposit_permissions = $request->v_branch_id;
        }

        # view employee
        if ($request->withdraw_permissions == 1) {
            $treasury->value_of_withdraw_permissions = $request->c_employee_id;
        }
        # view functional_role
        elseif ($request->withdraw_permissions == 2) {
            $treasury->value_of_withdraw_permissions = $request->c_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_withdraw_permissions = $request->c_branch_id;
        }

        // حفظ الحساب البنكي مرة واحدة فقط
        $treasury->save();

        // إنشاء الحساب المرتبط في جدول Account
        $account = new Account();
        $account->name = $request->name;
        $account->type_accont = 1; // نوع الحساب (بنكي)
        $account->is_active = $request->status; // حالة الحساب (افتراضي: نشط)
        $account->parent_id = 13; // الأب الافتراضي
       $account->code = $this->generateNextCode(13);
        $account->balance_type = 'debit'; // نوع الرصيد (مدين)
        // $account->treasury_id = $treasury->id; // ربط الحساب بالخزينة
        $account->save();

        // تسجيل النشاط في جدول السجلات
        ModelsLog::create([
            'type' => 'finance_log',
            'type_id' => $treasury->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم اضافة حساب بنكي  **' . $request->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('treasury.index')->with('success', 'تم إضافة الحساب البنكي بنجاح!');
    }

    public function edit_account_bank($id)
    {
        $treasury = Treasury::findOrFail($id);
        $employees = Employee::select()->get();
        return view('account::finance.treasury.edit_account_bank', compact('treasury', 'employees'));
    }

    public function update_account_bank(Request $request, $id)
    {
        $treasury = Treasury::findOrFail($id);

        $treasury->name = $request->name;
        $treasury->type = 1; # حساب بنكي
        $treasury->status = $request->status;
        $treasury->bank_name = $request->bank_name;
        $treasury->account_number = $request->account_number;
        $treasury->currency = $request->currency;
        $treasury->description = $request->description;
        $treasury->deposit_permissions = $request->deposit_permissions;
        $treasury->withdraw_permissions = $request->withdraw_permissions;
        $treasury->value_of_deposit_permissions = $request->value_of_deposit_permissions;
        $treasury->value_of_withdraw_permissions = $request->value_of_withdraw_permissions;

        #permissions-----------------------------------

        # view employee
        if ($request->deposit_permissions == 1) {
            $treasury->value_of_deposit_permissions = $request->v_employee_id;
        }
        # view functional_role
        elseif ($request->deposit_permissions == 2) {
            $treasury->value_of_deposit_permissions = $request->v_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_deposit_permissions = $request->v_branch_id;
        }

        # view employee
        if ($request->withdraw_permissions == 1) {
            $treasury->value_of_withdraw_permissions = $request->c_employee_id;
        }
        # view functional_role
        elseif ($request->withdraw_permissions == 2) {
            $treasury->value_of_withdraw_permissions = $request->c_functional_role_id;
        }
        # view branch
        else {
            $treasury->value_of_withdraw_permissions = $request->c_branch_id;
        }

        $treasury->update();
        return redirect()->route('treasury.index')->with(key: ['success' => 'تم تحديث الحساب بنجاج !!']);
    }

public function show($id)
{
    $treasury = $this->getTreasury($id);
    $branches = $this->getBranches();

    // إذا كان الطلب AJAX، إرجاع البيانات فقط
    if (request()->ajax()) {
        return $this->getOperationsData($id);
    }

    return view('account::finance.treasury.show', compact('treasury', 'branches'));
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

    if (request()->ajax()) {
        return response()->json([
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
        ]);
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
}
