<?php

namespace Modules\HR\Http\Controllers\Salaries;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\SalaryAdvance;
use App\Models\Employee;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\InstallmentPayment;
use App\Models\AccountSetting;
use App\Models\User;

class AncestorController extends Controller
{
    public function index(Request $request)
    {
        // استعلام أساسي مع علاقات الموظف والخزينة
        $query = SalaryAdvance::query()->with(['employee', 'treasury', 'payments']);

        // البحث حسب رقم السلفة أو المبلغ
        if ($request->filled('advance_search')) {
            $query->where(function ($q) use ($request) {
                $q->where('id', 'LIKE', "%{$request->advance_search}%")->orWhere('amount', 'LIKE', "%{$request->advance_search}%");
            });
        }

        // البحث حسب فترة القسط
        if ($request->filled('payment_rate')) {
            $query->where('payment_rate', $request->payment_rate);
        }

        // البحث حسب اسم الموظف
        if ($request->filled('employee_search')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('first_name', 'LIKE', "%{$request->employee_search}%");
            });
        }

        // البحث حسب تاريخ الدفعة القادمة
        if ($request->filled('next_payment_from')) {
            $query->where('installment_start_date', '>=', $request->next_payment_from);
        }
        if ($request->filled('next_payment_to')) {
            $query->where('installment_start_date', '<=', $request->next_payment_to);
        }

        // البحث حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // البحث حسب الفرع
        if ($request->filled('branch_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // البحث حسب الوسم
        if ($request->filled('tag')) {
            $query->where('tag', 'LIKE', "%{$request->tag}%");
        }

        // تحديد عدد العناصر في الصفحة مع قيمة افتراضية 10
        $perPage = $request->per_page ?? 10;
        $ancestors = $query->latest()->paginate($perPage);

        // حساب الإحصائيات
        $displayedTotalAmount = $ancestors->sum('amount');
        $displayedTotalPaid = $ancestors->sum(function ($ancestor) {
            return $ancestor->payments->where('status', 'paid')->sum('amount');
        });
        $displayedTotalInstallments = $ancestors->sum(function ($ancestor) {
            return $ancestor->payments->where('status', 'paid')->count();
        });
        $displayedProgressPercentage = $displayedTotalAmount > 0 ? ($displayedTotalPaid / $displayedTotalAmount) * 100 : 0;

        // إضافة معلومات الدفعة التالية لكل سلفة
        $ancestors->each(function ($ancestor) {
            $ancestor->next_payment = $ancestor->payments()->where('status', 'unpaid')->orderBy('due_date')->first();
        });

        // تحضير البيانات للواجهة
        $paymentRates = [
            1 => 'شهري',
            2 => 'اسبوعي',
            3 => 'يومي',
        ];

        $statuses = [
            'active' => 'نشط',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
        ];

        $branches = Branch::all();
        $employees = User::whereIn('role', ['manager', 'employee'])->get();
        $tags = SalaryAdvance::distinct('tag')->whereNotNull('tag')->pluck('tag');
        $account_setting = AccountSetting::where('user_id', auth()->id())->first();

        return view('hr::salaries.ancestor.index', compact('ancestors', 'paymentRates', 'statuses', 'branches', 'tags', 'employees', 'account_setting', 'displayedTotalAmount', 'displayedTotalPaid', 'displayedTotalInstallments', 'displayedProgressPercentage', 'request'));
    }

    public function create()
    {
        $employees = User::whereIn('role', ['manager', 'employee'])->get();
        $treasuries = Treasury::all();
        $mainTreasuryAccount = null;
        $user = Auth::user();

        if ($user && $user->employee_id) {
            // البحث عن الخزينة المرتبطة بالموظف
            $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

            if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                // إذا كان الموظف لديه خزينة مرتبطة
                $mainTreasuryAccount = Account::where('id', $treasuryEmployee->treasury_id)->first();
            } else {
                // إذا لم يكن لدى الموظف خزينة مرتبطة، استخدم الخزينة الرئيسية
                $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            }
        } else {
            // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، استخدم الخزينة الرئيسية
            $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
        }
        return view('hr::salaries.ancestor.create', compact('employees', 'treasuries', 'mainTreasuryAccount'));
    }

    protected function calculateInstallments($salaryAdvance)
    {
        $installments = [];
        $installmentAmount = $salaryAdvance->installment_amount;
        $totalInstallments = $salaryAdvance->total_installments;
        $startDate = Carbon::parse($salaryAdvance->installment_start_date);

        for ($i = 1; $i <= $totalInstallments; $i++) {
            $dueDate = clone $startDate;

            switch ($salaryAdvance->payment_rate) {
                case 1:
                    $dueDate->addMonths($i - 1);
                    break; // شهري
                case 2:
                    $dueDate->addWeeks($i - 1);
                    break; // أسبوعي
                case 3:
                    $dueDate->addMonths(($i - 1) * 3);
                    break; // ربع سنوي
            }

            $installments[] = [
                'number' => $i,
                'amount' => $installmentAmount,
                'due_date' => $dueDate->format('Y-m-d'),
            ];
        }

        return $installments;
    }

    //
    public function pay($id)
    {
       
        $salaryAdvance = SalaryAdvance::with(['payments'])->findOrFail($id);
        $accounts = Account::where('is_active', true)->get();
        // في الكونترولر
        $InstallmentPayments = InstallmentPayment::select('id', 'amount', DB::raw("DATE_FORMAT(due_date, '%Y-%m-%d') as due_date"), 'status')->where('salary_advance_id', $id)->where('status', 'unpaid')->get();
        $mainTreasuryAccount = null;
        $user = Auth::user();

        if ($user && $user->employee_id) {
            // البحث عن الخزينة المرتبطة بالموظف
            $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

            if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                // إذا كان الموظف لديه خزينة مرتبطة
                $mainTreasuryAccount = Account::where('id', $treasuryEmployee->treasury_id)->first();
            } else {
                // إذا لم يكن لدى الموظف خزينة مرتبطة، استخدم الخزينة الرئيسية
                $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            }
        } else {
            // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، استخدم الخزينة الرئيسية
            $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
        }

        // حساب الأقساط الغير مدفوعة فقط
        $unpaidInstallments = $this->getUnpaidInstallments($salaryAdvance);

        return view('hr::salaries.ancestor.pay', compact('salaryAdvance', 'accounts', 'unpaidInstallments', 'InstallmentPayments', 'mainTreasuryAccount', 'id'));
    }

    protected function getUnpaidInstallments($salaryAdvance)
    {
        // الأقساط المدفوعة (من جدول installment_payments)
        $paidInstallments = $salaryAdvance->payments->pluck('installment_number')->toArray();

        // جميع الأقساط المفترضة
        $allInstallments = $this->calculateInstallments($salaryAdvance);

        // تصفية الأقساط الغير مدفوعة
        return array_filter($allInstallments, function ($installment) use ($paidInstallments) {
            return !in_array($installment['number'], $paidInstallments);
        });
    }

   public function storePayments(Request $request, $id)
{
    // فاليدشن أساسي
    $request->validate([
        'installmentId' => ['required', 'integer', 'exists:installment_payments,id'],
        'payment_date'  => ['required', 'date'],
    ]);

    DB::beginTransaction();

    try {
        $user = auth()->user();
        if (! $user) {
            return redirect()->back()->with('error', 'غير مصرح');
        }

        // بيانات السلفة والقسط
        $salaryAdvance = SalaryAdvance::findOrFail($id);

        /** @var InstallmentPayment $installment */
        $installment = InstallmentPayment::lockForUpdate()->findOrFail($request->installmentId);

        if ($installment->status === 'paid') {
            return redirect()->back()->with('error', 'تم سداد هذا القسط مسبقًا.');
        }

        // تحديث القسط
        $installment->update([
            'status'       => 'paid',
            'payment_date' => $request->payment_date,
        ]);

        // تحديد الخزينة
        $mainTreasury = null;
        if ($user->employee_id) {
            $treEmp = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
            if ($treEmp && $treEmp->treasury_id) {
                $mainTreasury = Account::find($treEmp->treasury_id);
            }
        }
        if (! $mainTreasury) {
            $mainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
        }
        if (! $mainTreasury) {
            throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
        }

        // حساب السلف
        $salaryAdvanceAccount = Account::where('name', 'السلف')->first();
        if (! $salaryAdvanceAccount) {
            throw new \Exception('لا يوجد حساب "السلف" في شجرة الحسابات.');
        }

        // تحقق من صلاحية ربط الموظف (FK)
        $employeeId = $salaryAdvance->employee_id ?: null;
        if ($employeeId && ! Employee::whereKey($employeeId)->exists()) {
            $employeeId = null; // لا تكسر FK
        }

        // created_by_employee يجب يكون employee_id للمستخدم (إن وجد)
        $createdByEmployeeId = $user->employee_id ?: null;
        if ($createdByEmployeeId && ! Employee::whereKey($createdByEmployeeId)->exists()) {
            $createdByEmployeeId = null;
        }

        // تحديث الأرصدة (آمن على التزامن)
        $amount = (float) $installment->amount;
        $mainTreasury->increment('balance', $amount);
        $salaryAdvanceAccount->decrement('balance', $amount);

        // إنشاء قيد اليومية
        $journalEntry = JournalEntry::create([
            'reference_number'    => $installment->id,
            'date'                => $request->payment_date ?? now(),
            'description'         => 'تحصيل قسط سلفة رقم ' . $salaryAdvance->id,
            'status'              => 1,
            'currency'            => 'SAR',
            'employee_id'         => $employeeId,          // FK -> employees.id (nullable)
            'created_by_employee' => $createdByEmployeeId, // FK -> employees.id (nullable)
        ]);

        // تفاصيل القيد: خزينة (مدين)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id'       => $mainTreasury->id,
            'description'      => 'تحصيل قسط سلفة - خزينة',
            'debit'            => $amount,
            'credit'           => 0,
            'is_debit'         => true,
        ]);

        // تفاصيل القيد: حساب السلف (دائن)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id'       => $salaryAdvanceAccount->id,
            'description'      => 'تحصيل قسط سلفة - حساب السلف',
            'debit'            => 0,
            'credit'           => $amount,
            'is_debit'         => false,
        ]);

        DB::commit();

        // غيّر اسم المسار لو اسم Route مختلف عندك
        return redirect()->route('ancestor.show', $id)->with('success', 'تم حفظ الدفعة بنجاح');

    } catch (\Throwable $e) {
        DB::rollBack();
        return redirect()
            ->back()
            ->with('error', 'فشل في حفظ الدفعة: ' . $e->getMessage());
    }
}


    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $ancestor = SalaryAdvance::with('payments')->findOrFail($id);

            // التحقق من البيانات
            $request->validate([
                'employee_id' => 'required|exists:users,id',
                'submission_date' => 'nullable|date',
                'amount' => 'nullable|numeric|min:0.01',
                'installment_amount' => 'nullable|numeric|min:0.01',
                'currency' => 'nullable|integer',
                'payment_rate' => 'nullable|in:monthly,weekly,daily',
                'installment_start_date' => 'nullable|date',
                'treasury_id' => 'nullable|integer',
                'tag' => 'nullable|string',
                'note' => 'nullable|string',
            ]);

            // نجمع البيانات المرسلة في الطلب
            $data = [];

            // معالجة الحقول الأساسية
            $fields = ['employee_id', 'currency', 'treasury_id', 'tag', 'note'];
            foreach ($fields as $field) {
                if ($request->filled($field)) {
                    $data[$field] = $request->input($field);
                }
            }

            // معالجة التواريخ
            if ($request->filled('submission_date')) {
                $data['submission_date'] = date('Y-m-d', strtotime($request->submission_date));
            }
            if ($request->filled('installment_start_date')) {
                $data['installment_start_date'] = date('Y-m-d', strtotime($request->installment_start_date));
            }

            // معالجة المبلغ والأقساط
            $amountChanged = false;
            $installmentAmountChanged = false;
            $paymentRateChanged = false;
            $startDateChanged = false;

            if ($request->filled('amount')) {
                $data['amount'] = $request->amount;
                $amountChanged = true;
            }

            if ($request->filled('installment_amount')) {
                if ($request->installment_amount > ($data['amount'] ?? $ancestor->amount)) {
                    return back()
                        ->withInput()
                        ->withErrors(['installment_amount' => 'مبلغ القسط يجب أن يكون أقل من أو يساوي المبلغ الإجمالي']);
                }
                $data['installment_amount'] = $request->installment_amount;
                $installmentAmountChanged = true;
            }

            // حساب عدد الأقساط إذا تم تحديث المبلغ أو قيمة القسط
            $oldTotalInstallments = $ancestor->total_installments;
            if ($amountChanged || $installmentAmountChanged) {
                $amount = $data['amount'] ?? $ancestor->amount;
                $installmentAmount = $data['installment_amount'] ?? $ancestor->installment_amount;

                if ($installmentAmount > 0) {
                    $data['total_installments'] = ceil($amount / $installmentAmount);
                }
            }

            // معالجة payment_rate
            if ($request->filled('payment_rate')) {
                $paymentRateMap = [
                    'monthly' => 1,
                    'weekly' => 2,
                    'daily' => 3,
                ];
                $data['payment_rate'] = $paymentRateMap[$request->payment_rate] ?? 1;
                $paymentRateChanged = true;
            }

            // معالجة pay_from_salary
            $data['pay_from_salary'] = $request->has('pay_from_salary') ? 1 : 0;

            // تحديث السلفة
            if (!empty($data)) {
                $ancestor->update($data);
            }

            // إذا تم تغيير مبلغ القسط أو عدد الأقساط أو معدل الدفع أو تاريخ البدء
            if ($installmentAmountChanged || $amountChanged || $paymentRateChanged || $startDateChanged) {
                $this->updateInstallments($ancestor);
            }

            DB::commit();
            return redirect()->route('ancestor.index')->with('success', 'تم تحديث السلفة والأقساط بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating advance:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث السلفة: ' . $e->getMessage()]);
        }
    }

    // دالة مساعدة لتحديث الأقساط
    protected function updateInstallments($salaryAdvance)
    {
        // حذف الأقساط المستقبلية الغير مدفوعة
        $salaryAdvance->payments()->where('status', 'unpaid')->where('due_date', '>', now())->delete();

        // حساب الأقساط الجديدة
        $remainingAmount = $salaryAdvance->amount - $salaryAdvance->payments()->where('status', 'paid')->sum('amount');
        $installmentAmount = $salaryAdvance->installment_amount;
        $totalInstallments = ceil($remainingAmount / $installmentAmount);
        $startDate = Carbon::parse($salaryAdvance->installment_start_date);
        $installments = [];

        // إنشاء الأقساط الجديدة
        for ($i = 1; $i <= $totalInstallments; $i++) {
            $dueDate = clone $startDate;

            switch ($salaryAdvance->payment_rate) {
                case 1: // شهري
                    $dueDate->addMonths($i - 1);
                    break;
                case 2: // أسبوعي
                    $dueDate->addWeeks($i - 1);
                    break;
                case 3: // يومي
                    $dueDate->addDays($i - 1);
                    break;
            }

            // تعديل مبلغ القسط الأخير لضمان المساواة مع المبلغ المتبقي
            $currentAmount = $i == $totalInstallments ? $remainingAmount - $installmentAmount * ($totalInstallments - 1) : $installmentAmount;

            $installments[] = [
                'salary_advance_id' => $salaryAdvance->id,
                'installment_number' => $salaryAdvance->payments()->count() + $i,
                'amount' => $currentAmount,
                'due_date' => $dueDate,
                'status' => 'unpaid',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // إدخال الأقساط الجديدة
        if (!empty($installments)) {
            DB::table('installment_payments')->insert($installments);
        }
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // التحقق من البيانات
            $validated = $request->validate(
                [
                    'employee_id' => 'required|exists:users,id',
                    'submission_date' => 'required|date',
                    'amount' => 'required|numeric|min:0.01',
                    'installment_amount' => 'required|numeric|min:0.01|lte:amount',
                    'currency' => 'required|integer',
                    'payment_rate' => 'required|in:monthly,weekly,daily',
                    'installment_start_date' => 'required|date',
                    'treasury_id' => 'required|integer',
                    'tag' => 'nullable|string',
                    'note' => 'nullable|string',
                ],
                [
                    'employee_id.required' => 'يرجى اختيار الموظف',
                    'submission_date.required' => 'يرجى تحديد تاريخ التقديم',
                    'amount.required' => 'يرجى إدخال مبلغ السلفة',
                    'amount.numeric' => 'مبلغ السلفة يجب أن يكون رقماً',
                    'amount.min' => 'مبلغ السلفة يجب أن يكون أكبر من صفر',
                    'installment_amount.required' => 'يرجى إدخال مبلغ القسط',
                    'installment_amount.numeric' => 'مبلغ القسط يجب أن يكون رقماً',
                    'installment_amount.min' => 'مبلغ القسط يجب أن يكون أكبر من صفر',
                    'installment_amount.lte' => 'قيمة القسط يجب أن تكون أقل من أو تساوي قيمة السلفة',
                    'payment_rate.required' => 'يرجى اختيار معدل الدفع',
                    'payment_rate.in' => 'معدل الدفع المحدد غير صالح',
                ],
            );

            // تحضير البيانات
            $data = $request->all();

            // حساب عدد الأقساط الكلي
            $amount = (float) $request->amount;
            $installmentAmount = (float) $request->installment_amount;
            $totalInstallments = ceil($amount / $installmentAmount);

            // تخزين القيم المحسوبة
            $data['total_installments'] = $totalInstallments;
            $data['paid_installments'] = 0; // لا يوجد أقساط مدفوعة عند الإنشاء
            $data['paid_amount'] = 0.0; // المبلغ المدفوع عند الإنشاء يساوي صفر
            $data['pay_from_salary'] = $request->has('pay_from_salary') ? 1 : 0;
            $data['status'] = 'unpaid'; // حالة السلفة الافتراضية

            // تحويل payment_rate إلى رقم
            $paymentRateMap = [
                'monthly' => 1,
                'weekly' => 2,
                'daily' => 3,
            ];
            $data['payment_rate'] = $paymentRateMap[$data['payment_rate']] ?? 1;

            // تنسيق التواريخ
            $data['submission_date'] = date('Y-m-d', strtotime($data['submission_date']));
            $data['installment_start_date'] = date('Y-m-d', strtotime($data['installment_start_date']));

            $MainTreasury = null;
            $user = Auth::user();

            // إنشاء السلفة
            $advance = SalaryAdvance::create($data);

            // إنشاء سجلات الأقساط
            $this->createInstallments($advance, $totalInstallments, $installmentAmount);

            if ($user && $user->employee_id) {
                // البحث عن الخزينة المرتبطة بالموظف
                $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                    // إذا كان الموظف لديه خزينة مرتبطة
                    $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
                } else {
                    // إذا لم يكن لدى الموظف خزينة مرتبطة، استخدم الخزينة الرئيسية
                    $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                }
            } else {
                // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، استخدم الخزينة الرئيسية
                $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
            }

            // إذا لم يتم العثور على خزينة، توقف العملية وأظهر خطأ
            if (!$MainTreasury) {
                throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
            }

            $salaryadvanc = Account::where('name', 'السلف')->first();
            if (!$salaryadvanc) {
                throw new \Exception('لا يوجد حساب سلف يرجى التحقق من شجرة الحسابات.');
            }

            // تحديث رصيد الخزينة
            $MainTreasury->balance -= $advance->amount;
            $MainTreasury->save();

            $salaryadvanc->balance += $advance->amount;
            $salaryadvanc->save();

            // إنشاء قيد محاسبي لسند القبض
            $journalEntry = JournalEntry::create([
                'reference_number' => $advance->id,
                'date' => $advance->created_at,
                'description' => 'سلفية رقم ' . $advance->id,
                'status' => 1,
                'currency' => 'SAR',
                // 'employee_id' => $advance->employee_id,
                'created_by_employee' => Auth::id(),
            ]);

            // إضافة تفاصيل القيد المحاسبي لسند القبض
            // 1. حساب الخزينة المستهدفة (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $MainTreasury->id,
                'description' => 'سلفة من الخزينة',
                'debit' => 0,
                'credit' => $advance->amount,
                'is_debit' => true,
            ]);

            // 2. حساب الإيرادات (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salaryadvanc->id,
                'description' => 'سلفة',
                'debit' => $advance->amount,
                'credit' => 0,
                'is_debit' => false,
            ]);

            DB::commit();
            return redirect()->route('ancestor.index')->with('success', 'تم إضافة السلفة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            return back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إضافة السلفة: ' . $e->getMessage());
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // دالة مساعدة لإنشاء الأقساط
    protected function createInstallments($salaryAdvance, $totalInstallments, $installmentAmount)
    {
        $startDate = Carbon::parse($salaryAdvance->installment_start_date);
        $installments = [];

        for ($i = 1; $i <= $totalInstallments; $i++) {
            $dueDate = clone $startDate;

            // تحديد تاريخ الاستحقاق بناء على معدل الدفع
            switch ($salaryAdvance->payment_rate) {
                case 1: // شهري
                    $dueDate->addMonths($i - 1);
                    break;
                case 2: // أسبوعي
                    $dueDate->addWeeks($i - 1);
                    break;
                case 3: // يومي
                    $dueDate->addDays($i - 1);
                    break;
            }

            // تعديل مبلغ القسط الأخير لضمان المساواة مع المبلغ الكلي
            $currentInstallmentAmount = $i == $totalInstallments ? $salaryAdvance->amount - $installmentAmount * ($totalInstallments - 1) : $installmentAmount;

            $installments[] = [
                'salary_advance_id' => $salaryAdvance->id,
                'installment_number' => $i,
                'amount' => $currentInstallmentAmount,
                'due_date' => $dueDate,
                'account_id' => $salaryAdvance->treasury_id,
                'status' => 'unpaid', // حالة غير مدفوعة
                'created_by' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // إدخال جميع الأقساط دفعة واحدة
        DB::table('installment_payments')->insert($installments);
    }

    public function show($id)
    {
        try {
            $ancestor = SalaryAdvance::with(['employee', 'treasury', 'payments'])->findOrFail($id);

            // حساب إجمالي المدفوعات من جدول installment_payments
            $totalPaid = $ancestor->payments()->where('status', 'paid')->sum('amount');
            $paidInstallments = $ancestor->payments()->where('status', 'paid')->count();

            // حساب نسبة التقدم
            $progressPercentage = $ancestor->amount > 0 ? ($totalPaid / $ancestor->amount) * 100 : 0;
            $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

            return view('hr::salaries.ancestor.show', compact('ancestor', 'id', 'totalPaid', 'paidInstallments', 'progressPercentage', 'account_setting'));
        } catch (\Exception $e) {
            return redirect()->route('ancestor.index')->with('error', 'السلفة غير موجودة');
        }
    }

    public function edit($id)
    {
        try {
            $hasPaidInstallments = InstallmentPayment::where('salary_advance_id', $id)->where('status', 'paid')->exists();

            if ($hasPaidInstallments) {
                return redirect()->route('ancestor.index')->with('error', 'لا يمكن التعديل لأنه تم سداد من السلفة');
            }
            $ancestor = SalaryAdvance::findOrFail($id);
            $employees = Employee::find($ancestor->employee_id);
            $treasure = Account::find($ancestor->treasury_id);

            return view('salaries.ancestor.edit', compact('ancestor', 'employee', 'treasure'));
        } catch (\Exception $e) {
            return redirect()->route('ancestor.index')->with('error', 'السلفة غير موجودة');
        }
    }
    public function destroy($id)
    {
        try {
            $ancestor = SalaryAdvance::findOrFail($id);
            $ancestor->delete();

            return redirect()->route('ancestor.index')->with('success', 'تم حذف السلفة بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('ancestor.index')->with('error', 'حدث خطأ أثناء حذف السلفة');
        }
    }

    public function copy($id)
    {
        try {
            $original = SalaryAdvance::findOrFail($id);
            $copy = $original->replicate();
            $copy->save();

            return redirect()->route('ancestor.index')->with('success', 'تم نسخ السلفة بنجاح');
        } catch (\Exception $e) {
            return redirect()->route('ancestor.index')->with('error', 'حدث خطأ أثناء نسخ السلفة');
        }
    }
}
