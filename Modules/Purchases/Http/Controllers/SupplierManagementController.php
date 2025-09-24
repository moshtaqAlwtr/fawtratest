<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierRequest;
use App\Models\Account;
use App\Models\AccountSetting;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Revenue;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Log as ModelsLog;
use App\Models\PaymentsProcess;
use App\Models\PurchaseInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierManagementController extends Controller
{
public function index(Request $request)
{
    $users = User::where('role', 'manager')->get();
    $query = Supplier::query();

    // جلب المستخدم الحالي
    $user = auth()->user();

    // التحقق مما إذا كان للمستخدم فرع أم لا
    if (isset($user->branch_id) && $user->branch_id) {
        // يمكن إضافة منطق الفروع هنا إذا لزم الأمر
        // $query->where('branch_id', $user->branch_id);
    }

    // البحث بواسطة اسم المورد أو الرقم التعريفي
    if ($request->filled('employee_search')) {
        $query->where(function($q) use ($request) {
            $q->where('id', $request->employee_search)
              ->orWhere('trade_name', 'LIKE', '%' . $request->employee_search . '%');
        });
    }

    // البحث برقم المورد
    if ($request->filled('supplier_number')) {
        $query->where('number_suply', 'LIKE', '%' . $request->supplier_number . '%');
    }

    // البحث بالبريد الإلكتروني
    if ($request->filled('email')) {
        $query->where('email', 'LIKE', '%' . $request->email . '%');
    }

    // البحث برقم الجوال
    if ($request->filled('mobile')) {
        $query->where('mobile', 'LIKE', '%' . $request->mobile . '%');
    }

    // البحث برقم الهاتف
    if ($request->filled('phone')) {
        $query->where('phone', 'LIKE', '%' . $request->phone . '%');
    }

    // البحث بالعنوان
    if ($request->filled('address')) {
        $query->where('full_address', 'LIKE', '%' . $request->address . '%');
    }

    // البحث بالرمز البريدي
    if ($request->filled('postal_code')) {
        $query->where('postal_code', 'LIKE', '%' . $request->postal_code . '%');
    }

    // البحث بالعملة
    if ($request->filled('currency')) {
        $query->where('currency', $request->currency);
    }

    // البحث بالحالة
    if ($request->filled('status')) {
        $status = $request->status == 'active' ? 1 : 0;
        $query->where('status', $status);
    }

    // البحث بالرقم الضريبي
    if ($request->filled('tax_number')) {
        $query->where('tax_number', 'LIKE', '%' . $request->tax_number . '%');
    }

    // البحث بالسجل التجاري
    if ($request->filled('commercial_registration')) {
        $query->where('commercial_registration', 'LIKE', '%' . $request->commercial_registration . '%');
    }

    // البحث بواسطة المستخدم الذي أضاف
    if ($request->filled('created_by')) {
        $query->where('created_by', $request->created_by);
    }

    // جلب الموردين مع الترتيب التصاعدي (الأحدث أولاً)
    $suppliers = $query->orderBy('created_at', 'desc')->paginate(10);

    // للحفاظ على parameters البحث في روابط الترقيم
    $suppliers->appends($request->all());

    // إذا كان الطلب عبر AJAX، إرجاع البيانات فقط
    if ($request->ajax()) {
        return response()->json([
            'html' => view('purchases::purchases.supplier_management.partials.suppliers_table', compact('suppliers'))->render(),
            'pagination' => view('purchases::purchases.supplier_management.partials.pagination', compact('suppliers'))->render()
        ]);
    }

    return view('purchases::purchases.supplier_management.index', compact('suppliers', 'users'));
}

    public function create()
    {
        $lastSupplier = Supplier::orderBy('id', 'desc')->first();
        $nextNumber = $lastSupplier ? $lastSupplier->id + 1 : 1;

        return view('purchases::purchases.supplier_management.create', compact('nextNumber'));
    }


    private function generateNextCode(string $lastChildCode): string
    {
        // استخراج الرقم الأخير من الكود
        $lastNumber = intval(substr($lastChildCode, -1));
        // زيادة الرقم الأخير بمقدار 1
        $newNumber = $lastNumber + 1;
        // إعادة بناء الكود مع الرقم الجديد
        return substr($lastChildCode, 0, -1) . $newNumber;
    }


    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        $nextNumber = Supplier::max('id');
        return view('purchases::purchases.supplier_management.edit', compact('supplier', 'nextNumber'));
    }
    public function store(SupplierRequest $request)
{
    try {
        DB::beginTransaction();

        // التحقق من البيانات
        $validated = $request->validated();
        $nextNumber = Supplier::max('id') + 1;

        // معالجة الحقول التي لا تقبل NULL
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['currency'] = $validated['currency'] ?? 'SAR';

        // إنشاء المورد
        $supplier = Supplier::create(
            array_merge($validated, [
                'number_suply' => $nextNumber,
                'created_by' => auth()->id(),
            ])
        );

        // إنشاء الحساب المحاسبي
        $this->createSupplierAccount($supplier);

        // معالجة المرفقات
        $this->handleAttachment($request, $supplier);

        // إضافة جهات الاتصال
        $this->addContacts($request, $supplier);

        // تسجيل اشعار نظام
        ModelsLog::create([
            'type' => 'Supplier',
            'type_id' => $supplier->id,
            'type_log' => 'log',
            'description' => 'تم اضافة مورد جديد **' . $supplier->trade_name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();
        return redirect()->route('SupplierManagement.index')->with('success', 'تم إضافة المورد بنجاح');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Supplier Store Error: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}
// دالة جلب حركة الحساب (نفس المنطق المستخدم في supplierStatement)


// استخدام نفس الدوال الموجودة في supplierStatement

public function update(SupplierRequest $request, $id)
{
    try {
        DB::beginTransaction();

        $supplier = Supplier::findOrFail($id);
        $validated = $request->validated();

        // معالجة الحقول التي لا تقبل NULL
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0;
        $validated['currency'] = $validated['currency'] ?? 'SAR';
        $validated['updated_by'] = auth()->id();

        // الاحتفاظ بالقيم القديمة إذا كانت الجديدة فارغة
        foreach ($validated as $key => $value) {
            if (is_null($value) && $supplier->$key) {
                $validated[$key] = $supplier->$key;
            }
        }

        // حفظ البيانات القديمة للمقارنة
        $oldTradeName = $supplier->trade_name;
        $oldOpeningBalance = $supplier->opening_balance ?? 0;

        // تحديث بيانات المورد
        $supplier->fill($validated);
        $supplier->save();

        // معالجة الحساب المحاسبي
        $this->handleSupplierAccount($supplier, $oldTradeName, $oldOpeningBalance);

        // معالجة المرفقات
        if ($request->hasFile('attachments')) {
            $this->handleAttachment($request, $supplier);
        }

        // تحديث جهات الاتصال
        if ($request->has('contacts')) {
            $supplier->contacts()->delete();
            $this->addContacts($request, $supplier);
        }

        // تسجيل اشعار نظام
        ModelsLog::create([
            'type' => 'Supplier',
            'type_id' => $supplier->id,
            'type_log' => 'log',
            'description' => 'تم تحديث المورد **' . $supplier->trade_name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();
        return redirect()->route('SupplierManagement.index')->with('success', 'تم تحديث المورد بنجاح');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Supplier Update Error: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'حدث خطأ: ' . $e->getMessage());
    }
}public function updateStatus(Request $request, $id)
{
    try {
        // التحقق من صحة البيانات
        $request->validate([
            'status' => 'required|in:0,1'
        ]);

        // العثور على المورد
        $supplier = Supplier::findOrFail($id);

        // حفظ الحالة القديمة للمقارنة
        $oldStatus = $supplier->status;

        // تحديث الحالة
        $supplier->status = $request->status;
        $supplier->updated_at = now();
        $supplier->save();

        // تحديد نص الرسالة حسب الحالة الجديدة
        $statusText = $request->status == 1 ? 'تم تفعيل' : 'تم إيقاف';
        $message = "{$statusText} المورد \"{$supplier->trade_name}\" بنجاح";

        // إذا كان الطلب عبر AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'supplier_id' => $supplier->id,
                'new_status' => $supplier->status,
                'supplier_name' => $supplier->trade_name
            ]);
        }

        // إذا لم يكن AJAX، إعادة توجيه عادية
        return redirect()->route('SupplierManagement.index')->with('success', $message);

    } catch (\Illuminate\Validation\ValidationException $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'البيانات المرسلة غير صحيحة'
            ], 422);
        }

        return redirect()->back()->withErrors($e->errors());

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'المورد غير موجود'
            ], 404);
        }

        return redirect()->route('SupplierManagement.index')->with('error', 'المورد غير موجود');

    } catch (\Exception $e) {
        // تسجيل الخطأ في اللوق
        Log::error('خطأ في تحديث حالة المورد: ' . $e->getMessage());

        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى'
            ], 500);
        }

        return redirect()->back()->with('error', 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى');
    }
}

// إضافة دالة للحصول على إحصائيات الموردين (اختيارية)
public function getStats()
{
    try {
        $activeCount = Supplier::where('status', 1)->count();
        $inactiveCount = Supplier::where('status', 0)->count();
        $totalCount = Supplier::count();

        return response()->json([
            'success' => true,
            'stats' => [
                'active' => $activeCount,
                'inactive' => $inactiveCount,
                'total' => $totalCount
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'خطأ في جلب الإحصائيات'
        ], 500);
    }
}
private function createSupplierAccount($supplier)
{
    // البحث عن حساب الدائنون
    $creditorsAccount = Account::where('name', 'الدائنون')->first();

    if (!$creditorsAccount) {
        Log::warning('لم يتم العثور على حساب الدائنون الرئيسي');
        return;
    }

    // إنشاء حساب المورد الجديد
    $supplierAccount = new Account();
    $supplierAccount->name = $supplier->trade_name;
    $supplierAccount->supplier_id = $supplier->id;
    $supplierAccount->balance = $supplier->opening_balance ?? 0;

    // توليد الكود الجديد
    $newCode = $this->generateSupplierAccountCode($creditorsAccount->id, $creditorsAccount->code);

    $supplierAccount->code = $newCode;
    $supplierAccount->balance_type = 'credit'; // دائن لأنها ديون علينا
    $supplierAccount->parent_id = $creditorsAccount->id;
    $supplierAccount->is_active = false;
    $supplierAccount->save();

    // تسجيل الرصيد الافتتاحي إذا كان أكبر من صفر
    if ($supplier->opening_balance > 0) {
        $this->createOpeningBalanceEntry($supplier, $supplierAccount);
    }
}


private function handleSupplierAccount($supplier, $oldTradeName, $oldOpeningBalance)
{
    // البحث عن الحساب المحاسبي للمورد
    $supplierAccount = Account::where('supplier_id', $supplier->id)->first();

    if ($supplierAccount) {
        // تحديث اسم الحساب إذا تغير الاسم التجاري
        if ($oldTradeName !== $supplier->trade_name) {
            $supplierAccount->name = $supplier->trade_name;
            $supplierAccount->save();
        }

        // التعامل مع تغيير الرصيد الافتتاحي
        $newOpeningBalance = $supplier->opening_balance ?? 0;
        if ($oldOpeningBalance != $newOpeningBalance) {
            $this->updateOpeningBalance($supplier, $supplierAccount, $oldOpeningBalance, $newOpeningBalance);
        }
    } else {
        // إذا لم يكن هناك حساب محاسبي، إنشاء واحد جديد
        $this->createSupplierAccount($supplier);
    }
}


private function updateOpeningBalance($supplier, $supplierAccount, $oldBalance, $newBalance)
{
    // حذف القيد القديم للرصيد الافتتاحي إن وجد
    $oldJournalEntry = JournalEntry::where('supplier_id', $supplier->id)
                                  ->where('reference_number', $supplier->number_suply)
                                  ->where('description', 'like', '%رصيد افتتاحي%')
                                  ->first();

    if ($oldJournalEntry) {
        // حذف تفاصيل القيد أولاً ثم القيد نفسه
        $oldJournalEntry->details()->delete();
        $oldJournalEntry->delete();
    }

    // تحديث رصيد الحساب
    $supplierAccount->balance = $newBalance;
    $supplierAccount->save();

    // إنشاء قيد جديد للرصيد الافتتاحي إذا كان أكبر من صفر
    if ($newBalance > 0) {
        $this->createOpeningBalanceEntry($supplier, $supplierAccount);
    }
}


private function createOpeningBalanceEntry($supplier, $supplierAccount)
{
    $journalEntry = JournalEntry::create([
        'reference_number' => $supplier->number_suply,
        'date' => now(),
        'description' => 'رصيد افتتاحي للمورد : ' . $supplier->trade_name,
        'status' => 1,
        'currency' => $supplier->currency ?? 'SAR',
        'supplier_id' => $supplier->id,
    ]);

    JournalEntryDetail::create([
        'journal_entry_id' => $journalEntry->id,
        'account_id' => $supplierAccount->id,
        'description' => 'رصيد افتتاحي للمورد : ' . $supplier->trade_name,
        'debit' => 0,
        'credit' => $supplier->opening_balance ?? 0,
        'is_debit' => false,
    ]);
}

public function updateSupplierOpeningBalance(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->opening_balance = $request->opening_balance;
        $supplier->save();

        $Account = Account::where('supplier_id', $id)->first();
        if ($Account) {
            $Account->balance += $supplier->opening_balance;
            $Account->save(); // حفظ التعديل في قاعدة البيانات
        }
        if ($supplier->opening_balance > 0) {
            $journalEntry = JournalEntry::create([
                'reference_number' => $supplier->code,
                'date' => now(),
                'description' => 'رصيد افتتاحي للمورد : ' . $supplier->trade_name,
                'status' => 1,
                'currency' => 'SAR',
                'supplier_id' => $supplier->id,

            ]);

            // // 1. حساب العميل (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $Account->id, // حساب العميل
                'description' => 'رصيد افتتاحي للمورد : ' . $supplier->trade_name,
                'debit' => $supplier->opening_balance ?? 0, // المبلغ الكلي للفاتورة (مدين)
                'credit' => 0,
                'is_debit' => true,
            ]);
        }

        return response()->json(['success' => true]);
    }


private function generateSupplierAccountCode($parentAccountId, $parentCode)
{
    // البحث عن آخر حساب فرعي تحت حساب الدائنون
    $lastChild = Account::where('parent_id', $parentAccountId)
                      ->orderBy('code', 'desc')
                      ->first();

    // إذا لم يوجد أي حساب فرعي، ابدأ بـ 1
    if (!$lastChild) {
        return $parentCode . '1';
    }

    // استخراج الرقم من آخر الكود وزيادته
    $lastNumber = (int) substr($lastChild->code, strlen($parentCode));
    $newNumber = $lastNumber + 1;
    $newCode = $parentCode . $newNumber;

    // التأكد من أن الكود غير مستخدم
    while (Account::where('code', $newCode)->exists()) {
        $newNumber++;
        $newCode = $parentCode . $newNumber;
    }

    return $newCode;
}

    /**
     * معالجة المرفقات للمورد
     */

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        ModelsLog::create([
            'type' => 'purchase_log',
            'type_id' => $supplier->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف المورد  **' . $supplier->trade_name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $supplier->delete();
        return redirect()->route('SupplierManagement.index')->with('success', 'تم حذف المورد بنجاح');
    }

    private function handleAttachment(Request $request, Supplier $supplier)
    {
        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            if ($file->isValid()) {
                // حذف الملف القديم إذا كان موجودًا
                if ($supplier->attachments && file_exists(public_path('assets/uploads/suppliers/' . $supplier->attachments))) {
                    unlink(public_path('assets/uploads/suppliers/' . $supplier->attachments));
                }

                // رفع الملف الجديد
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/suppliers'), $filename);
                $supplier->attachments = $filename;
                $supplier->save();
            }
        }
    }


    private function addContacts(Request $request, Supplier $supplier)
    {
        if ($request->has('contacts') && is_array($request->contacts)) {
            foreach ($request->contacts as $contact) {
                if (!empty($contact['first_name'])) {
                    $supplier->contacts()->create([
                        'first_name' => $contact['first_name'],
                        'last_name' => $contact['last_name'] ?? null,
                        'phone' => $contact['phone'] ?? null,
                        'mobile' => $contact['mobile'] ?? null,
                        'email' => $contact['email'] ?? null,
                    ]);
                }
            }
        }
    }


public function show($id)
{
    $supplier = Supplier::with(['account'])->findOrFail($id);

    // جلب فواتير الشراء
    $purchaseInvoices = PurchaseInvoice::where('supplier_id', $id)
        ->latest()
        ->paginate(10, ['*'], 'invoices');

    // جلب المدفوعات
    $payments = PaymentsProcess::where('supplier_id', $id)
        ->with(['purchase_invoice'])
        ->latest()
        ->paginate(10, ['*'], 'payments');

    $logs = ModelsLog::where('type', 'Supplier')
           ->where('type_id', $id)
           ->whereHas('Supplier')
           ->with('user')
           ->orderBy('created_at', 'desc')
           ->get()
           ->groupBy(function($item) {
               return $item->created_at->format('Y-m-d');
           });

    // جلب حركة الحساب للمورد
    $accountMovements = [];
    if ($supplier->account) {
        $account = $supplier->account;
        $accountId = $account->id;

        // جلب العمليات المالية
        $transactions = $this->getTransactions($accountId);
        $transfers = $this->getTransfers($accountId);
        $expenses = $this->getExpenses($accountId);
        $revenues = $this->getRevenues($accountId);

        // معالجة العمليات وحساب الرصيد
        $accountMovements = $this->processOperations($transactions, $transfers, $expenses, $revenues, $account);

        // ترتيب العمليات من الأحدث للأقدم
        usort($accountMovements, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
    }

    return view('purchases::purchases.supplier_management.show', compact(
        'supplier',
        'purchaseInvoices',
        'payments',
        'logs',
        'accountMovements'
    ));
}

public function supplierStatement($id)
{
    $supplier = Supplier::find($id);

    $account = Account::where('supplier_id', $id)->first();

    if (!$account) {
        return redirect()->back()->with('error', 'لا يوجد حساب مرتبط بهذا المورد.');
    }

    $accountId = $account->id;
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

    // ترتيب العمليات من الأحدث للأقدم
    usort($allOperations, function ($a, $b) {
        return strtotime($b['date']) - strtotime($a['date']);
    });

    // تقسيم العمليات إلى صفحات
    $operationsPaginator = $this->paginateOperations($allOperations);

    // إرسال البيانات إلى الواجهة
    return view('purchases::purchases.supplier_management.statement', compact('treasury', 'account', 'operationsPaginator', 'branches', 'supplier'));
}

private function getTreasury($id)
{
    return Account::findOrFail($id);
}

private function getBranches()
{
    return \App\Models\Branch::all();
}

private function getTransactions($id)
{
    return JournalEntryDetail::where('account_id', $id)
        ->with([
            'journalEntry' => function ($query) {
                $query->with('invoice', 'client');
            },
        ])
        ->orderBy('created_at', 'asc') // تغيير الترتيب للأحدث
        ->get();
}

private function getTransfers($id)
{
    return JournalEntry::whereHas('details', function ($query) use ($id) {
        $query->where('account_id', $id);
    })
        ->with(['details.account'])
        ->where('description', 'تحويل المالية')
        ->orderBy('created_at', 'asc') // تغيير الترتيب للأحدث
        ->get();
}

private function getExpenses($id)
{
    return \App\Models\Expense::where('treasury_id', $id)
        ->with(['expenses_category', 'vendor', 'employee', 'branch', 'client'])
        ->orderBy('created_at', 'asc') // تغيير الترتيب للأحدث
        ->get();
}

private function getRevenues($id)
{
    return \App\Models\Revenue::where('treasury_id', $id)
        ->with(['account', 'paymentVoucher', 'treasury', 'bankAccount', 'journalEntry'])
        ->orderBy('created_at', 'asc') // تغيير الترتيب للأحدث
        ->get();
}

private function processOperations($transactions, $transfers, $expenses, $revenues, $treasury)
{
    $currentBalance = $treasury->opening_balance ?? 0; // البدء بالرصيد الافتتاحي
    $allOperations = [];

    // جمع كل العمليات في مصفوفة واحدة مع التاريخ
    $allItems = [];

    // إضافة المعاملات
    foreach ($transactions as $transaction) {
        $allItems[] = [
            'type' => 'transaction',
            'data' => $transaction,
            'date' => $transaction->journalEntry->date ?? $transaction->created_at
        ];
    }

    // إضافة المصروفات
    foreach ($expenses as $expense) {
        $allItems[] = [
            'type' => 'expense',
            'data' => $expense,
            'date' => $expense->date ?? $expense->created_at
        ];
    }

    // إضافة الإيرادات
    foreach ($revenues as $revenue) {
        $allItems[] = [
            'type' => 'revenue',
            'data' => $revenue,
            'date' => $revenue->date ?? $revenue->created_at
        ];
    }

    // ترتيب العمليات حسب التاريخ من الأقدم للأحدث لحساب الرصيد بشكل صحيح
    usort($allItems, function ($a, $b) {
        return strtotime($a['date']) - strtotime($b['date']);
    });

    // معالجة العمليات
    foreach ($allItems as $item) {
        switch ($item['type']) {
            case 'transaction':
                $transaction = $item['data'];
                $amount = $transaction->debit > 0 ? $transaction->debit : $transaction->credit;
                $type = $transaction->debit > 0 ? 'إيداع' : 'سحب';

                $currentBalance = $this->updateBalance($currentBalance, $amount, $type);

                $allOperations[] = [
                    'operation' => $transaction->description,
                    'deposit' => $type === 'إيداع' ? $amount : 0,
                    'withdraw' => $type === 'سحب' ? $amount : 0,
                    'balance_after' => $currentBalance,
                    'journalEntry' => $transaction->journalEntry->id ?? null,
                    'date' => $transaction->journalEntry->date ?? $transaction->created_at,
                    'invoice' => $transaction->journalEntry->invoice ?? null,
                    'client' => $transaction->journalEntry->client ?? null,
                    'type' => 'transaction',
                    'reference_number' => $transaction->journalEntry->reference_number ?? null
                ];
                break;

            case 'expense':
                $expense = $item['data'];
                $currentBalance -= $expense->amount;

                $allOperations[] = [
                    'operation' => 'سند صرف: ' . $expense->description,
                    'deposit' => 0,
                    'withdraw' => $expense->amount,
                    'balance_after' => $currentBalance,
                    'date' => $expense->date ?? $expense->created_at,
                    'invoice' => null,
                    'client' => $expense->client ?? null,
                    'type' => 'expense',
                    'reference_number' => $expense->expense_number ?? null
                ];
                break;

            case 'revenue':
                $revenue = $item['data'];
                $currentBalance += $revenue->amount;

                $allOperations[] = [
                    'operation' => 'سند قبض: ' . $revenue->description,
                    'deposit' => $revenue->amount,
                    'withdraw' => 0,
                    'balance_after' => $currentBalance,
                    'date' => $revenue->date ?? $revenue->created_at,
                    'invoice' => null,
                    'client' => null,
                    'type' => 'revenue',
                    'reference_number' => $revenue->revenue_number ?? null
                ];
                break;
        }
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

        return new \Illuminate\Pagination\LengthAwarePaginator($paginatedOperations, count($allOperations), $perPage, $currentPage, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);
    }
}
