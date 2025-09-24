<?php

namespace Modules\Account\Http\Controllers\Finance;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Client;
use App\Models\Employee;
use App\Models\AccountSetting;
use App\Models\EmployeeClientVisit;
use App\Models\EmployeeGroup;
use App\Models\Invoice;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Receipt;

use App\Models\Log as ModelsLog;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\ReceiptCategory;
use App\Models\Supplier;
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IncomesController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        // جلب البيانات المرتبطة للعرض الأولي
        $categories = ReceiptCategory::all();
        $suppliers = Supplier::all();
        $users = User::select('id', 'name')->where('role', 'employee')->get();
        $account_setting = AccountSetting::where('user_id', $user->id)->first();

        // فلترة الحسابات حسب دور المستخدم
        if ($user->role == 'employee') {
            $employeeGroupIds = EmployeeGroup::where('employee_id', $user->employee_id)->pluck('group_id');

            if ($employeeGroupIds->isNotEmpty()) {
                $Accounts = Account::whereNotNull('client_id')
                    ->whereHas('client.Neighborhoodname.Region', function ($q) use ($employeeGroupIds) {
                        $q->whereIn('id', $employeeGroupIds);
                    })
                    ->get();
            } else {
                $Accounts = collect();
            }
        } else {
            $Accounts = Account::whereNotNull('client_id')->get();
        }

        return view('account::finance.incomes.index', compact('categories', 'Accounts', 'users', 'account_setting'));
    }

    public function getData(Request $request)
    {
        $user = auth()->user();

        // جلب البيانات مع تطبيق شروط البحث
        $query = Receipt::with(['account', 'user'])
            ->orderBy('id', 'DESC')
            ->when($request->keywords, function ($query, $keywords) {
                return $query->where('code', 'like', '%' . $keywords . '%')->orWhere('description', 'like', '%' . $keywords . '%');
            })
            ->when($request->from_date, function ($query, $from_date) {
                return $query->where('date', '>=', $from_date);
            })
            ->when($request->to_date, function ($query, $to_date) {
                return $query->where('date', '<=', $to_date);
            })
            ->when($request->created_by, function ($query, $created_by) {
                return $query->where('created_by', $created_by);
            })
            ->when($request->sub_account, function ($query, $sub_account) {
                return $query->where('account_id', $sub_account);
            });

        // فلترة حسب دور المستخدم
        if ($user->role == 'employee') {
            $query->where('created_by', $user->id);
        }

        $incomes = $query->paginate(20);

        // حساب الإجماليات مع مراعاة دور المستخدم
        $totalQuery = Receipt::query();
        $totalLast7DaysQuery = Receipt::where('date', '>=', now()->subDays(7));
        $totalLast30DaysQuery = Receipt::where('date', '>=', now()->subDays(30));
        $totalLast365DaysQuery = Receipt::where('date', '>=', now()->subDays(365));

        if ($user->role == 'employee') {
            $totalQuery->where('created_by', $user->id);
            $totalLast7DaysQuery->where('created_by', $user->id);
            $totalLast30DaysQuery->where('created_by', $user->id);
            $totalLast365DaysQuery->where('created_by', $user->id);
        }

        // تطبيق فلتر الحساب على الإجماليات إذا كان موجوداً
        if ($request->sub_account) {
            $totalQuery->where('account_id', $request->sub_account);
            $totalLast7DaysQuery->where('account_id', $request->sub_account);
            $totalLast30DaysQuery->where('account_id', $request->sub_account);
            $totalLast365DaysQuery->where('account_id', $request->sub_account);
        }

        $totalLast7Days = $totalLast7DaysQuery->sum('amount');
        $totalLast30Days = $totalLast30DaysQuery->sum('amount');
        $totalLast365Days = $totalLast365DaysQuery->sum('amount');

        $account_setting = AccountSetting::where('user_id', $user->id)->first();

        return response()->json([
            'incomes' => $incomes,
            'totals' => [
                'totalLast7Days' => $totalLast7Days,
                'totalLast30Days' => $totalLast30Days,
                'totalLast365Days' => $totalLast365Days,
            ],
            'account_setting' => $account_setting,
            'pagination' => [
                'current_page' => $incomes->currentPage(),
                'last_page' => $incomes->lastPage(),
                'per_page' => $incomes->perPage(),
                'total' => $incomes->total(),
                'has_more_pages' => $incomes->hasMorePages(),
                'on_first_page' => $incomes->onFirstPage(),
                'next_page_url' => $incomes->nextPageUrl(),
                'prev_page_url' => $incomes->previousPageUrl(),
            ]
        ]);
    }


    public function create()
{
    $user = auth()->user();

    $incomes_categories = ReceiptCategory::select('id', 'name')->get();
    $treas = Treasury::select('id', 'name')->get();

    // فلترة الحسابات حسب دور المستخدم
    if ($user->role == 'employee') {
        $employeeGroupIds = EmployeeGroup::where('employee_id', $user->employee_id)->pluck('group_id');

        if ($employeeGroupIds->isNotEmpty()) {
            $accounts = Account::whereNotNull('client_id')
                ->whereHas('client.Neighborhoodname.Region', function ($q) use ($employeeGroupIds) {
                    $q->whereIn('id', $employeeGroupIds);
                })
                ->get();
        } else {
            $accounts = collect(); // إرجاع مجموعة فارغة إذا لم يكن هناك مجموعات
        }
    } else {
        // إذا لم يكن موظفاً، عرض جميع الحسابات
        $accounts = Account::whereNotNull('client_id')->get();
    }

    $account_storage = Account::where('parent_id', 13)->get();

    // حساب الرقم التلقائي
    $nextCode = Receipt::max('code') ?? 0;
    while (Receipt::where('code', $nextCode)->exists()) {
        $nextCode++;
    }

    $MainTreasury = null;
    $treasury_id = null; // إضافة متغير لتخزين معرف الخزينة

    if ($user && $user->employee_id) {
        $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

        if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
            $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
            $treasury_id = $TreasuryEmployee->treasury_id; // تعيين معرف الخزينة
        } else {
            $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
            $treasury_id = $MainTreasury->id ?? null; // تعيين معرف الخزينة الرئيسية
        }
    } else {
        $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
        $treasury_id = $MainTreasury->id ?? null; // تعيين معرف الخزينة الرئيسية
    }

    if (!$MainTreasury) {
        throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
    }

    $taxs = TaxSitting::all();
    $account_setting = AccountSetting::where('user_id', $user->id)->first();

    return view('account::finance.incomes.create', compact(
        'incomes_categories',
        'account_storage',
        'taxs',
        'treas',
        'accounts',
        'account_setting',
        'nextCode',
        'MainTreasury',
        'treasury_id' // إضافة المتغير الجديد للعرض
    ));
}

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // إنشاء سند القبض
            $income = new Receipt();
            $income->code = $request->input('code');
            $income->amount = $request->input('amount');
            $income->description = $request->input('description');
            $income->date = $request->input('date');
            $income->incomes_category_id = $request->input('incomes_category_id');
            $income->seller = $request->input('seller');
            $income->account_id = $request->input('account_id');
            $income->treasury_id = $request->input('treasury_id');
            $income->is_recurring = $request->has('is_recurring') ? 1 : 0;
            $income->recurring_frequency = $request->input('recurring_frequency');
            $income->end_date = $request->input('end_date');
            $income->tax1 = $request->input('tax1');
            $income->tax2 = $request->input('tax2');
            $income->created_by = auth()->id();
            $income->tax1_amount = $request->input('tax1_amount');
            $income->tax2_amount = $request->input('tax2_amount');
            $income->cost_centers_enabled = $request->has('cost_centers_enabled') ? 1 : 0;

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $income->attachments = $this->UploadImage('assets/uploads/incomes', $request->file('attachments'));
            }

            // حفظ سند القبض
            $income->save();

            // تحديث حالة الزيارة الحالية
            $account = Account::with('client')->find($income->account_id);
            if ($account && $account->client) {
                $visit = EmployeeClientVisit::where('employee_id', auth()->id())
                    ->where('client_id', $account->client->id)
                    ->latest()
                    ->first();

                if ($visit) {
                    $visit->update([
                        'status' => 'active',
                        'updated_at' => now()
                    ]);
                }
            }

            // إشعار الإنشاء
            $user = auth()->user();
            $income_account_name = Account::find($income->account_id);

            notifications::create([
                'user_id' => $user->id,
                'type' => 'Receipt',
                'title' => $user->name . ' أنشأ سند قبض',
                'description' => 'سند قبض رقم ' . $income->code . ' لـ ' . $income_account_name->name . ' بقيمة ' . number_format($income->amount, 2) . ' ر.س',
            ]);

            // تسجيل النشاط
            ModelsLog::create([
                'type' => 'finance_log',
                'type_id' => $income->id,
                'type_log' => 'log',
                'description' => sprintf('تم انشاء سند قبض رقم **%s** بقيمة **%d**', $income->code, $income->amount),
                'created_by' => auth()->id(),
            ]);

            // تحديث رصيد الخزينة
            $MainTreasury = Account::find($income->treasury_id);
            if ($MainTreasury) {
                $MainTreasury->balance += $income->amount;
                $MainTreasury->save();
            }

            // تحديث رصيد حساب العميل
            $clientAccount = Account::find($income->account_id);
            if ($clientAccount) {
                $clientAccount->balance -= $income->amount;
                $clientAccount->save();
            }

            // إنشاء القيد المحاسبي
            $journalEntry = JournalEntry::create([
                'reference_number' => $income->code,
                'date' => $income->date,
                'description' => 'سند قبض رقم ' . $income->code,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $clientAccount->client_id ?? null,
                'created_by_employee' => $user->id,
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $MainTreasury->id,
                'description' => 'استلام مبلغ من سند قبض',
                'debit' => $income->amount,
                'credit' => 0,
                'is_debit' => true,
            ]);

            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $income->account_id,
                'description' => 'إيرادات من سند قبض',
                'debit' => 0,
                'credit' => $income->amount,
                'is_debit' => false,
            ]);

            DB::commit();

            return redirect()->route('incomes.index')->with('success', 'تم إضافة سند القبض بنجاح!');

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in IncomeController@store: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء إضافة سند القبض: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $request->validate([
                'code' => 'required',
                'amount' => 'required|numeric',
                'date' => 'required|date',
                'account_id' => 'required',

                'incomes_category_id' => 'required',
            ]);

            // البحث باستخدام النموذج الصحيح (Receipt بدلاً من Income)
            $income = Receipt::findOrFail($id);

            // حفظ القيم القديمة
            $oldAmount = $income->amount;
            $oldAccountId = $income->account_id;
            $oldTreasuryId = $income->treasury_id;

            // تحديث البيانات
            $income->code = $request->input('code');
            $income->amount = $request->input('amount');
            $income->description = $request->input('description');
            $income->date = $request->input('date');
            $income->incomes_category_id = $request->input('incomes_category_id');
            $income->seller = $request->input('seller');
            $income->account_id = $request->input('account_id');
            $income->is_recurring = $request->has('is_recurring') ? 1 : 0;
            $income->recurring_frequency = $request->input('recurring_frequency');
            $income->end_date = $request->input('end_date');
            $income->tax1 = $request->input('tax1');
            $income->tax2 = $request->input('tax2');
            $income->tax1_amount = $request->input('tax1_amount');
            $income->tax2_amount = $request->input('tax2_amount');
            $income->cost_centers_enabled = $request->has('cost_centers_enabled') ? 1 : 0;

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $income->attachments = $this->UploadImage('assets/uploads/incomes', $request->file('attachments'));
            }

            $income->save();

            // تحديث أرصدة الحسابات
            $this->updateAccountBalances($income, $oldAmount, $oldAccountId, $oldTreasuryId);

            // تحديث القيد المحاسبي
            $this->updateJournalEntry($income);

            DB::commit();

            return redirect()->route('incomes.index')->with('success', 'تم تعديل سند القبض بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تعديل سند قبض: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء تعديل سند القبض: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function updateAccountBalances($income, $oldAmount, $oldAccountId, $oldTreasuryId)
    {
        // تحديث رصيد الحساب (العميل)
        if ($oldAccountId == $income->account_id) {
            $account = Account::find($income->account_id);
            if ($account) {
                $account->balance += $oldAmount; // نرجع المبلغ القديم
                $account->balance -= $income->amount; // نطرح المبلغ الجديد
                $account->save();
            }
        } else {
            // رجع المبلغ للحساب القديم
            $oldAccount = Account::find($oldAccountId);
            if ($oldAccount) {
                $oldAccount->balance += $oldAmount;
                $oldAccount->save();
            }

            // خصم المبلغ من الحساب الجديد
            $newAccount = Account::find($income->account_id);
            if ($newAccount) {
                $newAccount->balance -= $income->amount;
                $newAccount->save();
            }
        }

        // تحديث الخزينة
        if ($oldTreasuryId == $income->treasury_id) {
            $treasury = Account::find($income->treasury_id);
            if ($treasury) {
                $treasury->balance -= $oldAmount; // نقص القديم
                $treasury->balance += $income->amount; // أضف الجديد
                $treasury->save();
            }
        } else {
            // طرح المبلغ من الخزينة القديمة
            $oldTreasury = Account::find($oldTreasuryId);
            if ($oldTreasury) {
                $oldTreasury->balance -= $oldAmount;
                $oldTreasury->save();
            }

            // إضافة المبلغ للخزينة الجديدة
            $newTreasury = Account::find($income->treasury_id);
            if ($newTreasury) {
                $newTreasury->balance += $income->amount;
                $newTreasury->save();
            }
        }
    }

    private function updateJournalEntry($income)
    {
        // البحث عن القيد المحاسبي المرتبط
        $journalEntry = JournalEntry::where('reference_number', $income->code)->first();

        if ($journalEntry) {
            // تحديث بيانات القيد الأساسي
            $journalEntry->date = $income->date;
            $journalEntry->description = 'سند قبض رقم ' . $income->code;
            $journalEntry->save();

            // تحديث التفاصيل (الحساب المدين - الخزينة)
            $debitEntry = JournalEntryDetail::where('journal_entry_id', $journalEntry->id)->where('is_debit', true)->first();
            if ($debitEntry) {
                $debitEntry->account_id = $income->treasury_id;
                $debitEntry->debit = $income->amount;
                $debitEntry->save();
            }

            // تحديث التفاصيل (الحساب الدائن - العميل)
            $creditEntry = JournalEntryDetail::where('journal_entry_id', $journalEntry->id)->where('is_debit', false)->first();
            if ($creditEntry) {
                $creditEntry->account_id = $income->account_id;
                $creditEntry->credit = $income->amount;
                $creditEntry->save();
            }
        }
    }
    public function show($id)
    {
        $income = Receipt::findOrFail($id);
        return view('account::finance.incomes.show', compact('income'));
    }

    public function edit($id)
    {
        $income = Receipt::findOrFail($id);

        $incomes_categories = ReceiptCategory::select('id', 'name')->get();
        $treas = Treasury::select('id', 'name')->get();
        $accounts = Account::all();
        $account_storage = Account::where('parent_id', 13)->get();
        $taxs = TaxSitting::all();

        $user = Auth::user();
        $MainTreasury = null;

        if ($user && $user->employee_id) {
            $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

            if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
            } else {
                $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
            }
        } else {
            $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
        }

        if (!$MainTreasury) {
            throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
        }

        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        return view('account::finance.incomes.edit', compact('income', 'incomes_categories', 'treas', 'accounts', 'account_storage', 'taxs', 'account_setting', 'MainTreasury'));
    }

    public function delete($id)
    {
        $incomes = Receipt::findOrFail($id);
        ModelsLog::create([
            'type' => 'finance_log',
            'type_id' => $id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم  حذف سند قبض رقم  **' . $id . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $incomes->delete();
        return redirect()
            ->route('incomes.index')
            ->with(['error' => 'تم حذف سند قبض بنجاج !!']);
    }

    function uploadImage($folder, $image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time() . rand(1, 99) . '.' . $fileExtension;
        $image->move($folder, $fileName);

        return $fileName;
    } //end of uploadImage
    public function print($id, $type = 'normal')
    {
        $income = Receipt::findOrFail($id);

        if ($type == 'thermal') {
            // عرض نسخة حرارية
            return view('account::finance.incomes.print_thermal', compact('income'));
        } else {
            // عرض نسخة عادية
            return view('account::finance.incomes.print_normal', compact('income'));
        }
    }
    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $income = Receipt::findOrFail($id);

            // 1. استعادة رصيد الخزينة
            $treasury = Account::find($income->treasury_id);
            if ($treasury) {
                $treasury->balance -= $income->amount;
                $treasury->save();
            }

            // 2. استعادة رصيد العميل
            $clientAccount = Account::find($income->account_id);
            if ($clientAccount) {
                $clientAccount->balance += $income->amount;
                $clientAccount->save();
            }

            // 3. معالجة الفواتير المرتبطة بالسند
            $payments = PaymentsProcess::where('reference_number', $income->code)->get();
            foreach ($payments as $payment) {
                $invoice = Invoice::find($payment->invoice_id);
                if ($invoice) {
                    // استعادة المبلغ المدفوع
                    $invoice->advance_payment -= $payment->amount;

                    // حساب المبلغ المستحق بدقة
                    $invoice->due_value = $invoice->grand_total - $invoice->advance_payment;

                    // تحديث حالة الفاتورة حسب القيم الجديدة (باستخدام الأرقام الصحيحة لديك)
                    if ($invoice->advance_payment == 0) {
                        $invoice->is_paid = false;
                        $invoice->payment_status = 3; // غير مدفوعة
                    } elseif ($invoice->advance_payment == $invoice->grand_total) {
                        $invoice->is_paid = true;
                        $invoice->payment_status = 1; // مدفوعة بالكامل
                    } else {
                        $invoice->is_paid = false;
                        $invoice->payment_status = 2; // مدفوعة جزئياً
                    }

                    $invoice->save();
                }

                $payment->delete();
            }

            // 4. حذف القيد المحاسبي
            $journalEntry = JournalEntry::where('reference_number', $income->code)->first();
            if ($journalEntry) {
                JournalEntryDetail::where('journal_entry_id', $journalEntry->id)->delete();
                $journalEntry->delete();
            }

            // 5. حذف الإشعارات
            notifications::where('description', 'like', '%سند قبض رقم ' . $income->code . '%')->delete();

            // 6. تسجيل النشاط
            ModelsLog::create([
                'type' => 'finance_log',
                'type_id' => $income->id,
                'type_log' => 'log',
                'description' => sprintf('تم إلغاء سند قبض رقم **%s** بقيمة **%s** ريال', $income->code, number_format($income->amount, 2)),
                'created_by' => auth()->id(),
            ]);

            // 7. حذف سند القبض
            $income->delete();

            DB::commit();

            return redirect()->route('incomes.index')->with('success', 'تم إلغاء سند القبض بنجاح، وتم استعادة الفواتير والحسابات كما كانت!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('فشل في إلغاء سند القبض: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء الإلغاء: ' . $e->getMessage());
        }
    }
}
