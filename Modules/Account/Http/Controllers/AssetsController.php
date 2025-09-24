<?php

namespace Modules\Account\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Models\AssetDepreciation;
use App\Models\JournalEntry;
use App\Models\JournalDetail;
use App\Models\JournalEntryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Asset;
use App\Models\AssetDep;
use Illuminate\Support\Facades\Log;
use TCPDF;
use Carbon\Carbon;

class AssetsController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AssetDepreciation::with(['employee', 'client', 'depreciation']);

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date_2')) {
            $query->whereDate('created_at', '>=', $request->from_date_2);
        }

        if ($request->filled('to_date_2')) {
            $query->whereDate('created_at', '<=', $request->to_date_2);
        }

        $assets = $query->latest()->paginate(10);

        $employees = Employee::all();

        return view('Accounts.asol.index', compact('assets', 'employees'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $accounts = Account::where('parent_id', 6)->get();
        $accounts_all = Account::whereNotIn('parent_id', [6])->get();
        $employees = Employee::all();
        $clients = Client::all();
        return view('Accounts.asol.create', compact('accounts', 'employees', 'accounts_all'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'code' => 'required|unique:asset_depreciations,code',
            'name' => 'required|string|max:255',
            'date_price' => 'required|date',
            'date_service' => 'required|date',
            'account_id' => 'nullable|exists:accounts,id',
            'place' => 'nullable|string|max:255',
            'region_age' => 'nullable|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'purchase_value' => 'required|numeric|min:0',
            'currency' => 'required|in:1,2',
            'cash_account' => 'nullable|exists:accounts,id',
            'tax1' => 'nullable|in:1,2,3',
            'tax2' => 'nullable|in:1,2,3',
            'employee_id' => 'nullable|exists:employees,id',
            'client_id' => 'nullable|exists:clients,id',
            'attachments' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
        ]);

        try {

            DB::beginTransaction();

            // إنشاء الأصل
            $asset = new AssetDepreciation();
            $asset->code = $request->code;
            $asset->name = $request->name;
            $asset->date_price = $request->date_price;
            $asset->date_service = $request->date_service;

            $asset->place = $request->place;
            $asset->region_age = $request->region_age;
            $asset->quantity = $request->quantity;
            $asset->description = $request->description;
            $asset->purchase_value = $request->purchase_value;
            $asset->currency = $request->currency;
            $asset->cash_account = $request->cash_account;
            $asset->tax1 = $request->tax1;
            $asset->tax2 = $request->tax2;
            $asset->cash_account = $request->cash_account;  // حساب النقدية
            $asset->account_id = $request->account_id;  // حساب الاصل
            $asset->employee_id = $request->employee_id;
            $asset->client_id = $request->client_id;
            $asset->dep_method = $request->dep_method;
            $asset->salvage_value = $request->salvage_value;

            // القسط الثابت
            $asset->dep_rate = $request->dep_rate;
            $asset->duration = $request->duration;
            $asset->period = $request->period;

            // القسط المتناقص
            $asset->depreciation_rate = $request->depreciation_rate;
            $asset->declining_duration = $request->declining_duration;
            $asset->declining_period = $request->declining_period;

            // وحدات الإنتاج
            $asset->unit_name = $request->unit_name;
            $asset->total_units = $request->total_units;
            $asset->depreciation_end_date = $request->depreciation_end_date;

            // تسجيل السجل
            ModelsLog::create([
                'type' => 'finance_log',
                'type_id' => $asset->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم إضافة اصل جديد **' . $request->name . '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

            // تحديد حالة الأصل (مهلك أم لا)
            if ($request->region_age && $request->region_age > 0) {
                $purchaseDate = Carbon::parse($request->date_price);
                $depreciationYears = $request->region_age;
                $fullyDepreciatedDate = $purchaseDate->copy()->addYears(intval($depreciationYears));


                if (Carbon::now()->greaterThan($fullyDepreciatedDate)) {
                    $asset->status = 3; // مهلك
                } else {
                    $asset->status = 1; // في الخدمة
                }
            } else {
                $asset->status = 1; // في الخدمة
            }

            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $file->storeAs('assets/attachments', $fileName, 'public');
                $asset->attachments = 'assets/attachments/' . $fileName;
            }

            $asset->save();

            $account = new Account();
            $account->name = $request->name;
            $account->type_accont = 0; // نوع الحساب (خزينة)
            $account->is_active = $request->is_active ?? 1; // حالة الحساب (افتراضي: نشط)
            $account->parent_id = $request->account_id; // الأب الافتراضي
            $account->code = $account->code = $account->code = $this->generateNextCode($request->account_id); // إنشاء الكود
            $account->balance_type = 'debit'; // نوع الرصيد (مدين)
            // $account->treasury_id = $treasury->id; // ربط الحساب بالخزينة
            $account->save();

            $asset->asset_account = $account->id;

            $asset->save();

            // 1. إنشاء قيد اليومية
            $journal = new JournalEntry();
            $journal->date = Carbon::now();
            $journal->description = 'إضافة أصل جديد: ' . $asset->name;
            $journal->reference_number = $asset->id;
            $journal->created_by_employee = auth()->id();
            $journal->save();

            // 2. مدين: حساب الأصل
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' =>  $account->id,
                'debit' => $asset->purchase_value,
                'credit' => 0,
            ]);
            // 3. تحديث رصيد حساب الأصل (يزيد)
            $assetAccount = Account::find($account->id);
            $assetAccount->balance += $asset->purchase_value;
            $assetAccount->save();

            // 3. دائن: حساب النقدية
            JournalEntryDetail::create([
                'journal_entry_id' => $journal->id,
                'account_id' => $asset->cash_account,
                'debit' => 0,
                'credit' => $asset->purchase_value,
            ]);
            // 5. تحديث رصيد حساب النقدية (ينقص)
            $cashAccount = Account::find($asset->cash_account);
            $cashAccount->balance -= $asset->purchase_value;
            $cashAccount->save();

            DB::commit();

            return redirect()->route('Assets.show', $asset->id)
                ->with('success_message', 'تم إضافة الأصل بنجاح')
                ->with('asset_id', $asset->id);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء إضافة الأصل. ' . $e->getMessage()]);
        }
    }
    public function generateNextCode($parentId)
    {
        if (!$parentId) {
            // جذر - الحساب الرئيسي
            $lastCode = Account::whereNull('parent_id')
                ->orderByRaw('CAST(code AS UNSIGNED) DESC')
                ->value('code');

            return $lastCode ? (intval($lastCode) + 1) : '1';
        }

        // جلب كود الأب
        $parent = Account::findOrFail($parentId);
        $prefix = $parent->code;

        // جلب الكود الأخير من الأبناء
        $lastChildCode = Account::where('parent_id', $parentId)
            ->where('code', 'LIKE', "$prefix.%")
            ->orderByRaw('CAST(SUBSTRING_INDEX(code, ".", -1) AS UNSIGNED) DESC')
            ->value('code');

        if ($lastChildCode) {
            $lastSegment = intval(substr(strrchr($lastChildCode, '.'), 1));
            return $prefix . '.' . ($lastSegment + 1);
        }

        return $prefix . '.1';
    }
    public function transactions($accountId)
    {

        $journalEntries = JournalEntryDetail::where('account_id', $accountId)
            ->with('account')
            ->get();

        // جلب بيانات الخزينة
        $treasury = $this->getTreasury($accountId);
        $branches = $this->getBranches();

        // جلب العمليات المالية
        $transactions = $this->getTransactions($accountId);
        $transfers = $this->getTransfers($accountId);
        $expenses = $this->getExpenses($accountId);
        $revenues = $this->getRevenues($accountId);

        // معالجة العمليات وحساب الرصيد
        $allOperations = $this->processOperations($transactions, $transfers, $expenses, $revenues, $treasury);

        // ترتيب العمليات حسب التاريخ
        usort($allOperations, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // تقسيم العمليات إلى صفحات
        $operationsPaginator = $this->paginateOperations($allOperations);

        // إرسال البيانات إلى الواجهة
        return view('Accounts.accounts_chart.tree_details', compact('treasury', 'operationsPaginator', 'branches'));
    }

    private function getTreasury($id)
    {
        return Account::findOrFail($id);
    }

    private function getBranches()
    {
        return Branch::all();
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
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getExpenses($id)
    {
        return Expense::where('treasury_id', $id)
            ->with(['expenses_category', 'vendor', 'employee', 'branch', 'client'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function getRevenues($id)
    {
        return Revenue::where('treasury_id', $id)
            ->with(['account', 'paymentVoucher', 'treasury', 'bankAccount', 'journalEntry'])
            ->orderBy('created_at', 'asc')
            ->get();
    }

    private function processOperations($transactions, $transfers, $expenses, $revenues, $treasury)
    {
        $currentBalance = 0;
        $allOperations = [];


        // معالجة المدفوعات
        foreach ($transactions as $transaction) {
            $amount = $transaction->debit > 0 ? $transaction->debit : $transaction->credit;
            $type = $transaction->debit > 0 ? 'إيداع' : 'سحب';

            $currentBalance = $this->updateBalance($currentBalance, $amount, $type);



            $allOperations[] = [
                'operation' => '  قبد رقم # ' . $transaction->journalEntry->id,
                'deposit' => $type === 'إيداع' ? $amount : 0,
                'withdraw' => $type === 'سحب' ? $amount : 0,
                'balance_after' => $currentBalance,
                'journalEntry' => $transaction->journalEntry->id,
                'date' => $transaction->journalEntry->date,
                'invoice' => $transaction->journalEntry->invoice,
                'client' => $transaction->journalEntry->client,
                'type' => 'transaction',
            ];
        }



        // معالجة سندات الصرف
        foreach ($expenses as $expense) {
            $currentBalance -= $expense->amount;


            $allOperations[] = [
                'operation' => 'سند صرف: ' . $expense->description,
                'deposit' => 0,
                'withdraw' => $expense->amount,
                'balance_after' => $currentBalance,
                'date' => $expense->date,
                'invoice' => null,
                'client' => $expense->client,
                'type' => 'expense',
            ];
        }


        // معالجة سندات القبض
        foreach ($revenues as $revenue) {
            $currentBalance += $revenue->amount;


            $allOperations[] = [
                'operation' => 'سند قبض: ' . $revenue->description,
                'deposit' => $revenue->amount,
                'withdraw' => 0,
                'balance_after' => $currentBalance,
                'date' => $revenue->date,
                'invoice' => null,
                'client' => null,
                'type' => 'revenue',
            ];
        }


        return $allOperations;
    }

    private function updateBalance($currentBalance, $amount, $type)
    {
        return $type === 'إيداع' ? $currentBalance + $amount : $currentBalance - $amount;
    }

    private function paginateOperations($allOperations)
    {
        $perPage = 15;
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
    public function show(string $id)
    {
        try {
            // البحث عن الأصل مع علاقاته
            $asset = AssetDepreciation::with(['employee', 'client'])->findOrFail($id);

            // البحث عن الحساب المرتبط
            $account = Account::find($asset->account_id);

            // البحث عن القيود المحاسبية المرتبطة
            $journalEntries = JournalEntry::with(['details' => function ($query) {
                $query->with('account');
            }])
                ->where('reference_number', 'ASSET-' . $asset->id)
                ->get();


            $journalEntries = JournalEntryDetail::where('account_id', $asset->asset_account)
                ->with('account')
                ->get();

            // جلب بيانات الخزينة
            $treasury = $this->getTreasury($asset->asset_account);
            $branches = $this->getBranches();

            // جلب العمليات المالية
            $transactions = $this->getTransactions($asset->asset_account);
            $transfers = $this->getTransfers($asset->asset_account);
            $expenses = $this->getExpenses($asset->asset_account);
            $revenues = $this->getRevenues($asset->asset_account);

            // معالجة العمليات وحساب الرصيد
            $allOperations = $this->processOperations($transactions, $transfers, $expenses, $revenues, $treasury);

            // ترتيب العمليات حسب التاريخ
            usort($allOperations, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });

            // تقسيم العمليات إلى صفحات
            $operationsPaginator = $this->paginateOperations($allOperations);

            return view('Accounts.asol.show', compact('asset', 'account', 'journalEntries','operationsPaginator'));
        } catch (\Exception $e) {
            return redirect()->route('Assets.index')
                ->with('error', 'حدث خطأ أثناء عرض الأصل: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $asset = AssetDepreciation::findOrFail($id);
        $accounts = Account::where('parent_id', 6)->get();
        $accounts_all = Account::whereNotIn('parent_id', [6])->get();
        $employees = Employee::all();
        $clients = Client::all();
        return view('Accounts.asol.edit', compact('asset', 'accounts', 'employees', 'clients', 'accounts_all'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'code' => 'required|unique:asset_depreciations,code,' . $id,
            'name' => 'required|string|max:255',
            'date_price' => 'required|date',
            'date_service' => 'required|date',
            'account_id' => 'nullable|exists:accounts,id',
            'place' => 'nullable|string|max:255',
            'region_age' => 'nullable|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'purchase_value' => 'required|numeric|min:0',
            'currency' => 'required|in:1,2',
            'cash_account' => 'nullable|exists:accounts,id',
            'tax1' => 'nullable|in:1,2,3',
            'tax2' => 'nullable|in:1,2,3',
            'employee_id' => 'nullable|exists:employees,id',
            'client_id' => 'nullable|exists:clients,id',
            'attachments' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'dep_method' => 'nullable|in:1,2,3,4',
            'salvage_value' => 'nullable|numeric',
            'dep_rate' => 'nullable|numeric',
            'duration' => 'nullable|integer',
            'period' => 'nullable|integer',
            'depreciation_rate' => 'nullable|numeric',
            'declining_duration' => 'nullable|integer',
            'declining_period' => 'nullable|integer',
            'unit_name' => 'nullable|string',
            'total_units' => 'nullable|integer',
            'depreciation_end_date' => 'nullable|date',

        ]);

        try {
            DB::beginTransaction();

            // البحث عن الأصل
            $asset = AssetDepreciation::findOrFail($id);
            $originalPurchaseValue = $asset->purchase_value;
            $originalCashAccount = $asset->cash_account;

            // تسجيل السجل
            ModelsLog::create([
                'type' => 'finance_log',
                'type_id' => $asset->id,
                'type_log' => 'log',
                'description' => 'تم تعديل اصل **' . $request->name . '**',
                'created_by' => auth()->id(),
            ]);

            // تحديث بيانات الأصل
            $asset->code = $request->code;
            $asset->name = $request->name;
            $asset->date_price = $request->date_price;
            $asset->date_service = $request->date_service;
            $asset->place = $request->place;
            $asset->region_age = $request->region_age;
            $asset->quantity = $request->quantity;
            $asset->description = $request->description;
            $asset->purchase_value = $request->purchase_value;
            $asset->currency = $request->currency;
            $asset->cash_account = $request->cash_account;
            $asset->tax1 = $request->tax1;
            $asset->tax2 = $request->tax2;
            $asset->employee_id = $request->employee_id;
            $asset->client_id = $request->client_id;
            $asset->dep_method = $request->dep_method;
            $asset->salvage_value = $request->salvage_value;
            $asset->dep_rate = $request->dep_rate;
            $asset->duration = $request->duration;
            $asset->period = $request->period;
            $asset->depreciation_rate = $request->depreciation_rate;
            $asset->declining_duration = $request->declining_duration;
            $asset->declining_period = $request->declining_period;
            $asset->unit_name = $request->unit_name;
            $asset->total_units = $request->total_units;
            $asset->depreciation_end_date = $request->depreciation_end_date;


            // تحديد حالة الأصل (مهلك أم لا)
            if ($request->region_age && $request->region_age > 0) {
                $purchaseDate = Carbon::parse($request->date_price);
                $depreciationYears = $request->region_age;
                $fullyDepreciatedDate = $purchaseDate->copy()->addYears(intval($depreciationYears));

                if (Carbon::now()->greaterThan($fullyDepreciatedDate)) {
                    $asset->status = 3; // مهلك
                } else {
                    $asset->status = 1; // في الخدمة
                }
            } else {
                $asset->status = 1; // في الخدمة
            }

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                if ($asset->attachments) {
                    Storage::disk('public')->delete($asset->attachments);
                }
                $file = $request->file('attachments');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('assets/attachments', $fileName, 'public');
                $asset->attachments = 'assets/attachments/' . $fileName;
            }

            // تحديث الحساب المرتبط إذا كان موجوداً
            $account = Account::find($asset->asset_account);
            if ($account) {
                $account->update([
                    'name' => $request->name,
                    'parent_id' => $request->account_id,
                    'is_active' => $request->is_active ?? $account->is_active
                ]);
            } else {
                // إذا لم يكن هناك حساب مرتبط، ننشئ واحداً جديداً (كما في store)
                $account = new Account();
                $account->name = $request->name;
                $account->type_account = 0;
                $account->is_active = $request->is_active ?? 1;
                $account->parent_id = $request->account_id;
                $account->code = $this->generateNextCode($request->account_id);
                $account->balance_type = 'debit';
                $account->save();
                $asset->asset_account = $account->id;
            }

            // حفظ التغييرات على الأصل
            $asset->save();

            // حساب الفرق في القيمة الشرائية
            $valueDifference = $request->purchase_value - $originalPurchaseValue;

            // البحث عن القيد المحاسبي
            $journalEntry = JournalEntry::where('reference_number', $asset->id)->first();

            if ($journalEntry) {
                // تحديث القيد المحاسبي
                $journalEntry->update([
                    'date' => $request->date_price,
                    'description' => 'تعديل الأصل: ' . $request->name
                ]);

                // حذف التفاصيل القديمة
                $journalEntry->details()->delete();

                // إضافة التفاصيل الجديدة
                // مدين - حساب الأصل
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $asset->asset_account,
                    'debit' => $request->purchase_value,
                    'credit' => 0,
                    'description' => 'تعديل قيمة الأصل: ' . $request->name
                ]);

                // دائن - حساب النقدية
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $request->cash_account,
                    'debit' => 0,
                    'credit' => $request->purchase_value,
                    'description' => 'تعديل دفع قيمة الأصل: ' . $request->name
                ]);

                // تحديث أرصدة الحسابات
                if ($account) {
                    $account->balance += $valueDifference;
                    $account->save();
                }

                if ($request->cash_account) {
                    $cashAccount = Account::find($request->cash_account);
                    if ($cashAccount) {
                        $cashAccount->balance -= $valueDifference;
                        $cashAccount->save();
                    }
                }

                // إذا تغير حساب النقدية، نرجع الرصيد للحساب القديم
                if ($originalCashAccount && $originalCashAccount != $request->cash_account) {
                    $oldCashAccount = Account::find($originalCashAccount);
                    if ($oldCashAccount) {
                        $oldCashAccount->balance += $originalPurchaseValue;
                        $oldCashAccount->save();
                    }
                }
            }

            DB::commit();

            return redirect()->route('Assets.index')
                ->with('success_message', 'تم تحديث الأصل بنجاح')
                ->with('asset_id', $asset->id);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث الأصل. ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            // البحث عن الأصل
            $asset = AssetDepreciation::findOrFail($id);
            ModelsLog::create([
                'type' => 'finance_log',
                'type_id' => $asset->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم حذف الاصل **' . $asset->name . '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
            // حذف الصورة إذا كانت موجودة
            if ($asset->attachments) {
                Storage::disk('public')->delete($asset->attachments);
            }

            // البحث عن الحساب المرتبط بالأصل وحذفه
            $account = ChartOfAccount::where('code', $asset->code)->first();
            if ($account) {
                $account->delete();
            }

            // حذف القيود المحاسبية المرتبطة
            $journalEntries = JournalEntry::where('reference_number', 'ASSET-' . $asset->id)->get();
            foreach ($journalEntries as $entry) {
                $entry->details()->delete(); // حذف تفاصيل القيد
                $entry->delete(); // حذف القيد
            }

            // حذف الأصل
            $asset->delete();

            DB::commit();

            return redirect()->route('Assets.index')
                ->with('success_message', 'تم حذف الأصل بنجاح')
                ->with('success_details', [
                    'حساب الأستاذ' => 'منى العليا #11204',
                    'مجمع إهلاك - منى العليا' => '#224001',
                    'مصروف إهلاك - منى العليا' => '#32001'
                ]);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حذف الأصل: ' . $e->getMessage());
        }
    }

    /**
     * بيع الأصل
     */
    public function sell(Request $request, $id)
    {
         $validated = $request->validate([
                'sale_price' => 'required|numeric|min:0',
                'sale_date' => 'required|date',
                'tax1' => 'nullable|in:1,2,3',
                'tax2' => 'nullable|in:1,2,3'
            ]);

          try {
            // التحقق من البيانات

            DB::beginTransaction();

            // البحث عن الأصل والعميل
            $asset = AssetDepreciation::with('depreciation')->findOrFail($id);


            // حساب الربح أو الخسارة
            $bookValue = $asset->depreciation ? $asset->depreciation->book_value : $asset->purchase_value;
            $profit = $validated['sale_price'] - $bookValue;

            // إنشاء رقم مرجعي للعملية
            $reference = 'ASSET-SALE-' . $asset->id;

            // إنشاء قيد محاسبي للبيع
            $journalEntry = new JournalEntry();
            $journalEntry->date = $validated['sale_date'];
            $journalEntry->description = "hgjj";
            $journalEntry->reference_number = $reference;
            $journalEntry->status = 0; // pending
            $journalEntry->save();

            // إضافة تفاصيل القيد
            // مدين - حساب العميل
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $request->cash_account,
                'debit' => $validated['sale_price'],
                'credit' => 0,
                'description' => "jjhjhy",
                'reference' => $reference
            ]);

            // دائن - حساب الأصل
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $asset->account_id,
                'debit' => 0,
                'credit' => $bookValue,
                'description' => 'بيع الأصل: ' . $asset->name,
                'reference' => $reference
            ]);

            // إذا كان هناك ربح أو خسارة
            if ($profit != 0) {
                $profitAccountId = $profit > 0 ?
                    config('accounts.asset_sale_profit_account') :
                    config('accounts.asset_sale_loss_account');

                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $profitAccountId,
                    'debit' => $profit < 0 ? abs($profit) : 0,
                    'credit' => $profit > 0 ? $profit : 0,
                    'description' => ($profit > 0 ? 'ربح' : 'خسارة') . ' بيع الأصل: ' . $asset->name,
                    'reference' => $reference
                ]);
            }

            // تحديث حالة الأصل
            $asset->update([
                'status' => 2, // تم البيع
                'sale_price' => $validated['sale_price'],
                'sale_date' => $validated['sale_date'],
                'cash_account' => $validated['cash_account'],
                'reference' => $reference
            ]);

            DB::commit();

            return redirect()->route('Assets.show', $asset->id)
                ->with('success_message', 'تم بيع الأصل بنجاح')
            ;
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء بيع الأصل: ' . $e->getMessage());
        }
    }

    /**
     * عرض صفحة بيع الأصل
     */
    public function showSellForm($id)
    {
        $asset = AssetDepreciation::with('depreciation')->findOrFail($id);
        $accounts_all = Account::whereNotIn('parent_id', [6])->get();

        return view('Accounts.asol.sell', compact('asset', 'accounts_all'));
    }

    /**
     * إنشاء تقرير PDF للأصل
     */
    public function generatePdf($id)
    {
        $asset = AssetDepreciation::findOrFail($id);

        // إنشاء PDF جديد
        $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // تعيين معلومات الوثيقة
        $pdf->SetCreator('Fawtra');
        $pdf->SetAuthor('Fawtra System');
        $pdf->SetTitle('تقرير الأصل التفصيلي');

        // تعيين الهوامش
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(0);
        $pdf->SetFooterMargin(0);

        // تعطيل رأس وتذييل الصفحة
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // إضافة صفحة جديدة
        $pdf->AddPage();

        // تعيين اتجاه النص من اليمين إلى اليسار
        $pdf->setRTL(true);

        // تعيين الخط
        $pdf->SetFont('aealarabiya', '', 14);

        // بداية المحتوى
        $html = view('Accounts.asol.pdf', compact('asset'))->render();

        // إضافة المحتوى للـ PDF
        $pdf->writeHTML($html, true, false, true, false, '');

        // إخراج الملف
        return $pdf->Output('asset-report.pdf', 'I');
    }

    /**
     * حساب تاريخ نهاية الإهلاك
     */
    private function calculateEndDate($startDate, $duration, $period)
    {
        $start = \Carbon\Carbon::parse($startDate);

        switch ($period) {
            case 1: // يومي
                return $start->addDays($duration);
            case 2: // شهري
                return $start->addMonths($duration);
            case 3: // سنوي
                return $start->addYears($duration);
            default:
                return $start->addYears($duration);
        }
    }
}
