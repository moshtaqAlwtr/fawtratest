<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\ClientPaymentRequest;
use App\Models\Account;
use App\Models\AccountSetting;
use App\Models\Employee;
use App\Models\Installment;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;

use App\Models\Log as ModelsLog;
use App\Models\notifications;
use App\Models\PaymentMethod;
use App\Models\PaymentsProcess;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\Treasury;
use App\Models\TreasuryEmployee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchasePaymentController extends Controller
{
    public function index(Request $request)
    {
        $employees = User::where('role', ['employee', 'manager'])->get();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        $suppliers = Supplier::all();

        // إذا كان الطلب Ajax، نعيد البيانات فقط
        if ($request->ajax()) {
            return $this->getFilteredPayments($request);
        }

        // في البداية نعيد الصفحة مع البيانات الأولية
        $payments = $this->getFilteredPayments($request, false);

        return view('purchases::purchases.supplier_payments.index', compact('payments', 'employees', 'suppliers', 'account_setting'));
    }

    private function getFilteredPayments(Request $request, $returnJson = true)
    {
        try {
            $query = PaymentsProcess::with(['purchase_invoice.supplier', 'employee', 'supplier'])->where('type', 'supplier payments');

            // فلتر بناءً على صلاحية المستخدم
            if (auth()->user()->role == 'employee') {
                $query->where('employee_id', auth()->user()->id);
            }

            // تطبيق فلاتر البحث
            $this->applyPaymentFilters($query, $request);
            $this->applyQuickFilter($query, $request);

            // ترتيب النتائج
            $query->orderBy('created_at', 'DESC');

            // الحصول على النتائج مع الترقيم
            $payments = $query->paginate(25);

            if ($returnJson) {
                $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

                $tableHtml = view('purchases::purchases.supplier_payments.partials.table', compact('payments', 'account_setting'))->render();

                $paginationHtml = view('purchases::purchases.supplier_payments.partials.pagination', compact('payments'))->render();

                // دمج HTML الجدول والترقيم معاً
                $combinedHtml = $tableHtml . $paginationHtml;

                return response()->json([
                    'success' => true,
                    'html' => $combinedHtml,
                    'data' => $tableHtml, // للتوافق مع الكود الأمامي
                    'pagination' => $paginationHtml,
                    'total' => $payments->total(),
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'from' => $payments->firstItem(),
                    'to' => $payments->lastItem(),
                ]);
            }

            return $payments;
        } catch (\Exception $e) {
            Log::error('Error in getFilteredPayments: ' . $e->getMessage());

            if ($returnJson) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'حدث خطأ أثناء تحميل البيانات: ' . $e->getMessage(),
                    ],
                    500,
                );
            }

            // إرجاع نتائج فارغة في حالة الخطأ
            return PaymentsProcess::where('id', 0)->paginate(25);
        }
    }

    private function applyPaymentFilters($query, $request)
    {
        // فلتر رقم الفاتورة
        if ($request->filled('invoice_number')) {
            $invoiceNumber = trim($request->invoice_number);
            $query->whereHas('purchase_invoice', function ($q) use ($invoiceNumber) {
                $q->where('code', 'like', '%' . $invoiceNumber . '%')->orWhere('reference_number', 'like', '%' . $invoiceNumber . '%');
            });
        }

        // فلتر رقم عملية الدفع
        if ($request->filled('payment_number')) {
            $paymentNumber = trim($request->payment_number);
            $query->where(function ($q) use ($paymentNumber) {
                $q->where('payment_number', 'like', '%' . $paymentNumber . '%')
                    ->orWhere('reference_number', 'like', '%' . $paymentNumber . '%')
                    ->orWhere('id', 'like', '%' . $paymentNumber . '%');
            });
        }

        // فلتر المورد
        if ($request->filled('supplier')) {
            $supplierId = $request->supplier;
            $query->where(function ($q) use ($supplierId) {
                $q->where('supplier_id', $supplierId)->orWhereHas('purchase_invoice', function ($subQ) use ($supplierId) {
                    $subQ->where('supplier_id', $supplierId);
                });
            });
        }

        // فلتر "أضيفت بواسطة"
        if ($request->filled('added_by')) {
            $query->where('employee_id', $request->added_by);
        }

        // فلتر حالة الدفع
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // فلتر التخصيص (التواريخ المحددة مسبقاً)
        if ($request->filled('customization')) {
            switch ($request->customization) {
                case '1': // شهرياً
                    $query->whereMonth('payment_date', now()->month)->whereYear('payment_date', now()->year);
                    break;
                case '0': // أسبوعياً
                    $query->whereBetween('payment_date', [now()->startOfWeek()->format('Y-m-d'), now()->endOfWeek()->format('Y-m-d')]);
                    break;
                case '2': // يومياً
                    $query->whereDate('payment_date', now()->format('Y-m-d'));
                    break;
            }
        }

        // فلتر التاريخ من
        if ($request->filled('from_date')) {
            $query->whereDate('payment_date', '>=', $request->from_date);
        }

        // فلتر التاريخ إلى
        if ($request->filled('to_date')) {
            $query->whereDate('payment_date', '<=', $request->to_date);
        }

        // فلتر الرقم التعريفي
        if ($request->filled('identifier')) {
            $identifier = trim($request->identifier);
            $query->where(function ($q) use ($identifier) {
                $q->where('reference_number', 'like', '%' . $identifier . '%')
                    ->orWhere('payment_number', 'like', '%' . $identifier . '%')
                    ->orWhere('id', 'like', '%' . $identifier . '%');
            });
        }

        // فلتر رقم معرف التحويل
        if ($request->filled('transfer_id')) {
            $transferId = trim($request->transfer_id);
            $query->where(function ($q) use ($transferId) {
                $q->where('reference_number', 'like', '%' . $transferId . '%')->orWhere('transaction_id', 'like', '%' . $transferId . '%');
            });
        }

        // فلتر المبلغ أكبر من
        if ($request->filled('total_greater_than') && is_numeric($request->total_greater_than)) {
            $query->where('amount', '>', floatval($request->total_greater_than));
        }

        // فلتر المبلغ أصغر من
        if ($request->filled('total_less_than') && is_numeric($request->total_less_than)) {
            $query->where('amount', '<', floatval($request->total_less_than));
        }

        // فلتر الحقل المخصص (البحث في الملاحظات)
        if ($request->filled('custom_field')) {
            $customField = trim($request->custom_field);
            $query->where(function ($q) use ($customField) {
                $q->where('notes', 'like', '%' . $customField . '%')->orWhere('payment_data', 'like', '%' . $customField . '%');
            });
        }

        // فلتر مصدر الفاتورة
        if ($request->filled('invoice_origin')) {
            $query->whereHas('purchase_invoice', function ($q) use ($request) {
                $q->where('origin', $request->invoice_origin);
            });
        }
    }

    private function applyQuickFilter($query, $request)
    {
        $filter = $request->get('filter', 'all');

        switch ($filter) {
            case 'completed':
                $query->where('payment_status', 1);
                break;
            case 'pending':
                $query->where('payment_status', 2);
                break;
            case 'failed':
                $query->where('payment_status', 5);
                break;
            case 'draft':
                $query->where('payment_status', 3);
                break;
            case 'review':
                $query->where('payment_status', 4);
                break;
            case 'all':
            default:
                // لا نضيف أي شرط - عرض الكل
                break;
        }
    }

    // دالة البحث السريع
    public function quickSearch(Request $request)
    {
        if (!$request->ajax()) {
            return redirect()->route('PaymentSupplier.indexPurchase');
        }

        try {
            $searchTerm = trim($request->get('term', ''));

            $query = PaymentsProcess::with(['purchase_invoice.supplier', 'employee', 'supplier'])->where('type', 'supplier payments');

            if (auth()->user()->role == 'employee') {
                $query->where('employee_id', auth()->user()->id);
            }

            if (!empty($searchTerm)) {
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('payment_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('reference_number', 'like', '%' . $searchTerm . '%')
                        ->orWhere('amount', 'like', '%' . $searchTerm . '%')
                        ->orWhere('notes', 'like', '%' . $searchTerm . '%')
                        ->orWhere('id', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('purchase_invoice', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('code', 'like', '%' . $searchTerm . '%')->orWhere('reference_number', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('purchase_invoice.supplier', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('trade_name', 'like', '%' . $searchTerm . '%')->orWhere('phone', 'like', '%' . $searchTerm . '%');
                        })
                        ->orWhereHas('employee', function ($subQuery) use ($searchTerm) {
                            $subQuery->where('name', 'like', '%' . $searchTerm . '%');
                        });
                });
            }

            $payments = $query->orderBy('created_at', 'DESC')->paginate(25);
            $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

            return response()->json([
                'success' => true,
                'html' => view('purchases::purchases.supplier_payments.partials.table', compact('payments', 'account_setting'))->render(),
                'data' => view('purchases::purchases.supplier_payments.partials.table', compact('payments', 'account_setting'))->render(),
                'pagination' => view('purchases::purchases.supplier_payments.partials.pagination', compact('payments'))->render(),
                'total' => $payments->total(),
                'current_page' => $payments->currentPage(),
                'last_page' => $payments->lastPage(),
                'from' => $payments->firstItem(),
                'to' => $payments->lastItem(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in quickSearch: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء البحث: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // دالة التصدير
    public function export(Request $request)
    {
        try {
            $query = PaymentsProcess::with(['purchase_invoice.supplier', 'employee', 'supplier'])->where('type', 'supplier payments');

            if (auth()->user()->role == 'employee') {
                $query->where('employee_id', auth()->user()->id);
            }

            $this->applyPaymentFilters($query, $request);
            $this->applyQuickFilter($query, $request);

            $payments = $query->orderBy('created_at', 'DESC')->get();

            // يمكنك إضافة منطق التصدير هنا (Excel, PDF, إلخ)

            return response()->json([
                'success' => true,
                'message' => 'تم تصدير البيانات بنجاح',
                'count' => $payments->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error in export: ' . $e->getMessage());
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء التصدير: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function create($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        $amount = $invoice->due_value;
        $invoiceId = $invoice->id;

        $treasury = Treasury::all();
        $user = User::find(auth()->user()->id);

        if ($user->role != 'manager') {
            $employees = Employee::where('id', $user->employee_id)->get();
        } else {
            $employees = Employee::all();
        }

        $payments = PaymentMethod::where('type', 'normal')->where('status', 'active')->get();

        // تحديد الخزينة بنفس طريقة سندات القبض
        $mainTreasuryAccount = null;
        $treasury_id = null;
        $user = auth()->user();

        if ($user && $user->employee_id) {
            $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

            if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                $mainTreasuryAccount = Account::where('id', $treasuryEmployee->treasury_id)->first();
                $treasury_id = $treasuryEmployee->treasury_id;
            } else {
                $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
                $treasury_id = $mainTreasuryAccount->id ?? null;
            }
        } else {
            $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            $treasury_id = $mainTreasuryAccount->id ?? null;
        }

        if (!$mainTreasuryAccount) {
            throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
        }

        return view('purchases::purchases.supplier_payments.create', compact('invoiceId', 'payments', 'amount', 'treasury', 'employees', 'mainTreasuryAccount', 'user', 'treasury_id'));
    }

    public function show($id)
    {
        $payment = PaymentsProcess::with(['purchase_invoice', 'employee'])
            ->where('type', 'supplier payments')
            ->findOrFail($id);

        $employees = User::all();

        return view('purchases::purchases.supplier_payments.show', compact('payment', 'employees'));
    }

    public function edit($id)
    {
        // جلب بيانات الدفعة الحالية مع العلاقات
        $payment = PaymentsProcess::with(['purchase_invoice', 'employee'])->findOrFail($id);

        // جلب الفاتورة المرتبطة
        $invoice = PurchaseInvoice::findOrFail($payment->invoice_id);

        // تحديد البيانات الأساسية
        $invoiceId = $payment->invoice_id;
        $amount = $payment->amount; // المبلغ الحالي للدفعة

        // جلب جميع الخزائن
        $treasury = Treasury::all();

        // جلب المستخدم الحالي
        $user = User::find(auth()->user()->id);

        // تحديد الموظفين بناءً على دور المستخدم
        if ($user->role != 'manager') {
            $employees = Employee::where('id', $user->employee_id)->get();
        } else {
            $employees = Employee::all();
        }

        // جلب طرق الدفع النشطة
        $payments = PaymentMethod::where('type', 'normal')->where('status', 'active')->get();

        // تحديد الخزينة الحالية للدفعة أو الافتراضية
        $mainTreasuryAccount = null;
        $treasury_id = $payment->treasury_id; // استخدام خزينة الدفعة الحالية أولاً

        // إذا لم تكن هناك خزينة محددة في الدفعة، استخدم خزينة الموظف
        if (!$treasury_id) {
            $user = auth()->user();

            if ($user && $user->employee_id) {
                $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                    $treasury_id = $treasuryEmployee->treasury_id;
                } else {
                    $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
                    $treasury_id = $mainTreasuryAccount->id ?? null;
                }
            } else {
                $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
                $treasury_id = $mainTreasuryAccount->id ?? null;
            }
        }

        // جلب بيانات الخزينة المحددة
        if ($treasury_id && !$mainTreasuryAccount) {
            $mainTreasuryAccount = Account::where('id', $treasury_id)->first();
        }

        // التأكد من وجود خزينة افتراضية
        if (!$mainTreasuryAccount) {
            $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            $treasury_id = $mainTreasuryAccount->id ?? null;
        }

        // التحقق من وجود خزينة متاحة
        if (!$mainTreasuryAccount) {
            throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
        }

        // إضافة بيانات إضافية للعرض
        $installmentId = $payment->installment_id ?? null;

        return view(
            'purchases::purchases.supplier_payments.edit',
            compact(
                'payment', // بيانات الدفعة الحالية
                'invoice', // بيانات الفاتورة
                'invoiceId', // معرف الفاتورة
                'payments', // طرق الدفع المتاحة
                'amount', // المبلغ الحالي
                'treasury', // جميع الخزائن
                'employees', // الموظفين المتاحين
                'mainTreasuryAccount', // الخزينة الرئيسية/المحددة
                'user', // المستخدم الحالي
                'treasury_id', // معرف الخزينة المحددة
                'installmentId', // معرف القسط إن وجد
                'id', // معرف الدفعة للاستخدام في النموذج
            ),
        );
    }

    public function update(ClientPaymentRequest $request, PaymentsProcess $payment)
    {
        try {
            DB::beginTransaction();

            // استرجاع البيانات المصادق عليها
            $data = $request->validated();

            // التحقق من وجود الفاتورة وجلب تفاصيلها
            $invoice = PurchaseInvoice::findOrFail($payment->invoice_id);

            // ✅ حفظ القيم القديمة قبل التحديث
            $oldAmount = $payment->amount;
            $oldPaymentStatus = $payment->payment_status;

            // حساب إجمالي المدفوعات السابقة (باستثناء الدفعة الحالية والمدفوعات الفاشلة)
            $totalPreviousPayments = PaymentsProcess::where('invoice_id', $invoice->id)
                ->where('type', 'supplier payments')
                ->where('payment_status', '!=', 5) // استثناء المدفوعات الفاشلة
                ->where('id', '!=', $payment->id) // استثناء الدفعة الحالية
                ->sum('amount');

            // حساب المبلغ المتبقي للدفع (مع احتساب المبلغ الجديد)
            $remainingAmount = $invoice->grand_total - $totalPreviousPayments;

            // التحقق من أن مبلغ الدفع الجديد لا يتجاوز المبلغ المتبقي
            if ($data['amount'] > $remainingAmount) {
                return back()
                    ->with('error', 'مبلغ الدفع يتجاوز المبلغ المتبقي للفاتورة. المبلغ المتبقي هو: ' . number_format($remainingAmount, 2))
                    ->withInput();
            }

            // ✅ تحديد الخزينة المستهدفة بناءً على الموظف (نفس منطق إنشاء الفاتورة)
            $mainTreasuryAccount = null;
            $treasury_id = null;
            $user = Auth::user();

            if ($user && $user->employee_id) {
                // البحث عن الخزينة المرتبطة بالموظف
                $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                    $mainTreasuryAccount = Account::where('id', $treasuryEmployee->treasury_id)->first();
                    $treasury_id = $treasuryEmployee->treasury_id;
                } else {
                    $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
                    $treasury_id = $mainTreasuryAccount->id ?? null;
                }
            } else {
                $mainTreasuryAccount = Account::where('name', 'الخزينة الرئيسية')->first();
                $treasury_id = $mainTreasuryAccount->id ?? null;
            }

            // إذا لم يتم العثور على خزينة، توقف العملية وأظهر خطأ
            if (!$mainTreasuryAccount) {
                throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
            }

            // ✅ حساب الفرق في المبلغ للتحقق من الرصيد
            $amountDifference = $data['amount'] - $oldAmount;

            // التحقق من وجود رصيد كافي في الخزينة إذا كان المبلغ الجديد أكبر
            if ($amountDifference > 0 && $mainTreasuryAccount->balance < $amountDifference) {
                throw new \Exception('رصيد الخزينة غير كافي للزيادة المطلوبة. الرصيد الحالي: ' . number_format($mainTreasuryAccount->balance, 2) . ' والمطلوب إضافيًا: ' . number_format($amountDifference, 2));
            }

            // تعيين حالة الدفع الافتراضية كمسودة
            $payment_status = 3; // مسودة

            // تحديد حالة الدفع بناءً على المبلغ المدفوع والمبلغ المتبقي
            $newTotalPayments = $totalPreviousPayments + $data['amount'];

            if ($newTotalPayments >= $invoice->grand_total) {
                $payment_status = 1; // مكتمل
                $invoice->is_paid = true;
                $invoice->payment_status = 'paid'; // ✅ تحديث حالة الدفع الجديدة
                $invoice->due_value = 0;
            } else {
                $payment_status = 2; // غير مكتمل
                $invoice->is_paid = false;
                $invoice->payment_status = 'partially_paid'; // ✅ تحديث حالة الدفع الجديدة
                $invoice->due_value = $invoice->grand_total - $newTotalPayments;
            }

            // ✅ إضافة البيانات الإضافية للدفعة المحدثة
            $data['payment_status'] = $payment_status;
            $data['treasury_id'] = $treasury_id;
            $data['supplier_id'] = $invoice->supplier_id;
            $data['purchases_id'] = $invoice->id;
            $data['updated_by'] = Auth::id(); // ✅ إضافة معرف من قام بالتحديث

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    // حذف المرفق القديم إذا كان موجوداً
                    if ($payment->attachments && file_exists(public_path('assets/uploads/' . $payment->attachments))) {
                        unlink(public_path('assets/uploads/' . $payment->attachments));
                    }

                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $data['attachments'] = $filename;
                }
            }

            // ✅ تحديث ملاحظة تتضمن معلومات الخزينة والتحديث
            $updateNote = "\nتم التحديث في: " . now()->format('Y-m-d H:i:s') . ' - الخزينة المستخدمة: ' . $mainTreasuryAccount->name;
            $data['notes'] = ($data['notes'] ?? '') . $updateNote;

            // ✅ تحديث سجل الدفع
            $payment->update($data);

            // تحديث المبلغ المدفوع في الفاتورة
            $invoice->advance_payment = $newTotalPayments;
            $invoice->save();

            // ✅ إنشاء قيد محاسبي جديد للتحديث (بدون حذف القديم)
            $this->createUpdatePaymentAccountingEntries($invoice, $oldAmount, $data['amount'], $mainTreasuryAccount, $payment->id);

            // ✅ تسجيل إشعار النظام للتحديث
            $supplier = Supplier::find($invoice->supplier_id);
            ModelsLog::create([
                'type' => 'payment_supplier_update',
                'type_id' => $payment->id,
                'type_log' => 'log',
                'icon' => 'edit',
                'description' => sprintf('تم تحديث دفعة للمورد **%s** - فاتورة رقم **%s** - المبلغ من **%s** إلى **%s** ر.س - الفرق: **%s** ر.س - حالة الدفع: **%s** - المتبقي: **%s** ر.س', $supplier->trade_name ?? '', $invoice->code ?? '', number_format($oldAmount, 2), number_format($data['amount'], 2), number_format($amountDifference, 2), $this->getPaymentStatusText($payment_status), number_format($invoice->due_value, 2)),
                'created_by' => auth()->id(),
            ]);

            // ✅ إنشاء إشعار للنظام للتحديث
            notifications::create([
                'type' => 'payment_update',
                'title' => $user->name . ' قام بتحديث دفعة',
                'description' => 'تم تحديث دفعة للمورد ' . ($supplier->trade_name ?? '') . ' - فاتورة رقم ' . $invoice->code . ' - المبلغ من ' . number_format($oldAmount, 2) . ' إلى ' . number_format($data['amount'], 2) . ' ر.س - الفرق: ' . number_format($amountDifference, 2) . ' ر.س - حالة الدفع: ' . $this->getPaymentStatusText($payment_status),
            ]);

            DB::commit();

            // إعداد رسالة النجاح مع تفاصيل التحديث
            $paymentStatusText = $this->getPaymentStatusText($payment_status);
            $changeType = $amountDifference > 0 ? 'زيادة' : ($amountDifference < 0 ? 'تقليل' : 'بدون تغيير في المبلغ');

            $successMessage = sprintf('تم تحديث عملية الدفع بنجاح - %s بمقدار %s ر.س. المبلغ الجديد: %s ر.س، المبلغ المتبقي: %s ر.س - حالة الدفع: %s', $changeType, number_format(abs($amountDifference), 2), number_format($data['amount'], 2), number_format($invoice->due_value, 2), $paymentStatusText);

            return redirect()->route('PaymentSupplier.indexPurchase')->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث عملية الدفع: ' . $e->getMessage());
            return back()
                ->with('error', 'حدث خطأ أثناء تحديث عملية الدفع: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * ✅ دالة إنشاء القيود المحاسبية لتحديث الدفعة (بدون حذف القديم)
     */
    private function createUpdatePaymentAccountingEntries($invoice, $oldAmount, $newAmount, $mainTreasuryAccount, $paymentId)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // حساب الفرق في المبلغ
        $amountDifference = $newAmount - $oldAmount;

        // إذا لم يكن هناك فرق، لا حاجة لإنشاء قيد
        if ($amountDifference == 0) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
            return;
        }

        // حساب المورد
        $supplierAccount = Account::where('supplier_id', $invoice->supplier_id)->first();
        if (!$supplierAccount) {
            $supplier = Supplier::find($invoice->supplier_id);
            $supplierAccount = Account::create([
                'name' => 'حساب المورد - ' . ($supplier->trade_name ?? 'مورد غير معروف'),
                'supplier_id' => $invoice->supplier_id,
                'account_type' => 'supplier',
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // تحديد نوع القيد بناءً على الفرق
        $entryType = $amountDifference > 0 ? 'زيادة' : 'تقليل';
        $absoluteDifference = abs($amountDifference);

        // إنشاء القيد المحاسبي لتحديث الدفعة
        $journalEntry = JournalEntry::create([
            'reference_number' => $invoice->code . '_تحديث_دفعة_' . $paymentId . '_' . time(),
            'purchase_invoice_id' => $invoice->id,
            'date' => now(),
            'description' => $entryType . ' دفعة للمورد بمقدار ' . number_format($absoluteDifference, 2) . ' ر.س - فاتورة # ' . $invoice->code . ' (المبلغ من ' . number_format($oldAmount, 2) . ' إلى ' . number_format($newAmount, 2) . ')',
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $invoice->supplier_id,
            'created_by_employee' => Auth::id(),
        ]);

        if ($amountDifference > 0) {
            // زيادة المبلغ - قيد إضافي

            // حساب المورد/المؤسسة (مدين) - زيادة تسديد الدين
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $supplierAccount->id,
                'description' => 'زيادة دفعة للمورد - فاتورة # ' . $invoice->code,
                'debit' => $absoluteDifference,
                'credit' => 0,
                'is_debit' => true,
            ]);

            // حساب الخزينة الأساسية (دائن) - خروج نقد إضافي
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $mainTreasuryAccount->id,
                'description' => 'زيادة دفعة للمورد - فاتورة # ' . $invoice->code,
                'debit' => 0,
                'credit' => $absoluteDifference,
                'is_debit' => false,
            ]);

            // تحديث رصيد الخزينة (سحب المبلغ الإضافي)
            $mainTreasuryAccount->balance -= $absoluteDifference;
            $mainTreasuryAccount->save();

            // تحديث رصيد المورد (تقليل الدين الإضافي)
            $supplierAccount->balance -= $absoluteDifference;
            $supplierAccount->save();
        } else {
            // تقليل المبلغ - قيد عكسي

            // حساب الخزينة الأساسية (مدين) - إرجاع نقد
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $mainTreasuryAccount->id,
                'description' => 'تقليل دفعة للمورد - فاتورة # ' . $invoice->code,
                'debit' => $absoluteDifference,
                'credit' => 0,
                'is_debit' => true,
            ]);

            // حساب المورد/المؤسسة (دائن) - تقليل تسديد الدين
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $supplierAccount->id,
                'description' => 'تقليل دفعة للمورد - فاتورة # ' . $invoice->code,
                'debit' => 0,
                'credit' => $absoluteDifference,
                'is_debit' => false,
            ]);

            // تحديث رصيد الخزينة (إضافة المبلغ المُرجع)
            $mainTreasuryAccount->balance += $absoluteDifference;
            $mainTreasuryAccount->save();

            // تحديث رصيد المورد (زيادة الدين)
            $supplierAccount->balance += $absoluteDifference;
            $supplierAccount->save();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
    public function destroy($id)
    {
        PaymentsProcess::destroy($id);
        return redirect()->route('paymentsPurchase.index')->with('success', 'تم حذف عملية الدفع بنجاح');
    }

    public function getInvoiceDetails($invoice_id)
    {
        try {
            $invoice = PurchaseInvoice::findOrFail($invoice_id);

            // حساب المبلغ المدفوع والمتبقي
            $totalPayments = PaymentsProcess::where('invoice_id', $invoice->id)->where('type', 'supplier payments')->sum('amount');

            $remainingAmount = $invoice->grand_total - $totalPayments;

            return response()->json([
                'success' => true,
                'data' => [
                    'invoice' => $invoice,
                    'total_paid' => $totalPayments,
                    'remaining_amount' => $remainingAmount,
                    'client_name' => $invoice->supplier->name ?? 'غير محدد',
                    'invoice_date' => $invoice->invoice_date,
                    'grand_total' => $invoice->grand_total,
                    'payment_status' => $invoice->payment_status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء جلب تفاصيل الفاتورة: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // دالة مساعدة لإنشاء القيد المحاسبي للدفعة
    private function createPaymentJournalEntry($invoice, $amount)
    {
        // إنشاء قيد محاسبي للدفعة
        $journalEntry = JournalEntry::create([
            'reference_number' => 'PAY-' . time(),
            'date' => now(),
            'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
            'status' => 1,
            'currency' => 'SAR',
            'supplier_id' => $invoice->supplier_id,
            'invoice_id' => $invoice->id,
        ]);

        // إضافة تفاصيل القيد
        // مدين - حساب النقدية
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $invoice->account_id, // حساب الصندوق أو البنك
            'description' => 'دفعة نقدية',
            'debit' => $amount,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // دائن - حساب المورد
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $invoice->supplier->account_id,
            'description' => 'تسديد دفعة',
            'debit' => 0,
            'credit' => $amount,
            'is_debit' => false,
        ]);
    }

    public function rereceipt($id)
    {
        $receipt = PaymentsProcess::findOrFail($id);
        $type = request()->query('type', 'a4'); // افتراضي A4 إذا لم يتم تحديد النوع

        return view('purchases::purchases.supplier_payments.receipt.index_repeat', [
            'receipt' => $receipt,
            'receiptType' => $type,
        ]);
    }

    public function pdfReceipt($id)
    {
        $receipt = PaymentsProcess::findOrFail($id);

        $pdf = Pdf::loadView('purchases::purchases.supplier_payments.receipt.pdf_receipt', compact('receipt'));

        return $pdf->stream('receipt_' . $id . '.pdf');
    }

    public function cancel($id)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $payment = PaymentsProcess::findOrFail($id);

            // ✅ تصحيح التحقق من نوع الدفعة
            if ($payment->type !== 'supplier payments') {
                return back()->with('error', 'لا يمكن إلغاء هذا النوع من العمليات');
            }

            // ✅ تصحيح الحصول على الفاتورة
            $invoice = PurchaseInvoice::findOrFail($payment->invoice_id); // استخدام invoice_id بدلاً من purchase_id
            $supplier = Supplier::find($invoice->supplier_id); // تصحيح اسم المتغير

            // ✅ التأكد من أن الدفعة لم يتم إلغاؤها مسبقاً
            if ($payment->payment_status == 5) {
                // 5 = فاشلة/ملغية
                return back()->with('error', 'هذه الدفعة تم إلغاؤها مسبقاً');
            }

            // ✅ الحصول على الخزينة المستخدمة
            $mainTreasuryAccount = null;
            if ($payment->treasury_id) {
                // استخدام الخزينة المحفوظة في الدفعة
                $mainTreasuryAccount = Account::find($payment->treasury_id);
            } else {
                // البحث عن الخزينة بنفس منطق الإنشاء
                if ($user && $user->employee_id) {
                    $treasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
                    if ($treasuryEmployee && $treasuryEmployee->treasury_id) {
                        $mainTreasuryAccount = Account::find($treasuryEmployee->treasury_id);
                    }
                }
                $mainTreasuryAccount = $mainTreasuryAccount ?? Account::where('name', 'الخزينة الرئيسية')->first();
            }

            if (!$mainTreasuryAccount) {
                throw new \Exception('لا توجد خزينة متاحة لإجراء عملية الإلغاء');
            }

            // ✅ إنشاء قيد عكسي بدلاً من حذف القيد القديم
            $this->createReversalAccountingEntry($invoice, $payment, $mainTreasuryAccount);

            // ✅ تحديث أرصدة الحسابات
            // استرداد المبلغ للخزينة
            $mainTreasuryAccount->balance += $payment->amount;
            $mainTreasuryAccount->save();

            // زيادة رصيد المورد (زيادة الدين)
            $supplierAccount = Account::where('supplier_id', $invoice->supplier_id)->first();
            if ($supplierAccount) {
                $supplierAccount->balance += $payment->amount;
                $supplierAccount->save();
            }

            // ✅ تحديث بيانات الفاتورة
            $totalPreviousPayments = PaymentsProcess::where('invoice_id', $invoice->id)
                ->where('type', 'supplier payments')
                ->where('payment_status', '!=', 5) // استثناء المدفوعات الفاشلة/الملغية
                ->where('id', '!=', $payment->id) // استثناء الدفعة الحالية
                ->sum('amount');

            $invoice->advance_payment = $totalPreviousPayments;
            $invoice->due_value = $invoice->grand_total - $totalPreviousPayments;

            if ($totalPreviousPayments == 0) {
                $invoice->is_paid = false;
                $invoice->payment_status = 'unpaid'; // ✅ استخدام النظام الجديد
            } elseif ($totalPreviousPayments >= $invoice->grand_total) {
                $invoice->is_paid = true;
                $invoice->payment_status = 'paid'; // ✅ استخدام النظام الجديد
                $invoice->due_value = 0;
            } else {
                $invoice->is_paid = false;
                $invoice->payment_status = 'partially_paid'; // ✅ استخدام النظام الجديد
            }
            $invoice->save();

            // ✅ تحديث حالة الدفعة إلى ملغية
            $payment->payment_status = 5; // 5 = فاشلة/ملغية
            $payment->notes = ($payment->notes ?? '') . "\n[تم الإلغاء في " . now()->format('Y-m-d H:i:s') . ' بواسطة ' . $user->name . ']';
            $payment->save();

            // ✅ تسجيل إشعار النظام
            ModelsLog::create([
                'type' => 'payment_cancellation',
                'type_id' => $payment->id,
                'type_log' => 'log',
                'icon' => 'cancel',
                'description' => sprintf('تم إلغاء دفعة بمبلغ **%s** ر.س لفاتورة الشراء رقم **%s** للمورد **%s** من خزينة **%s** - المتبقي الآن: **%s** ر.س', number_format($payment->amount, 2), $invoice->code ?? '', $supplier->trade_name ?? '', $mainTreasuryAccount->name ?? '', number_format($invoice->due_value, 2)),
                'created_by' => auth()->id(),
            ]);

            // ✅ إنشاء إشعار إلغاء
            notifications::create([
                'type' => 'payment_cancellation',
                'title' => $user->name . ' ألغى عملية دفع',
                'description' => 'تم إلغاء دفعة بمبلغ ' . number_format($payment->amount, 2) . ' ر.س لفاتورة الشراء رقم ' . $invoice->code . ' للمورد ' . ($supplier->trade_name ?? '') . ' من خزينة ' . $mainTreasuryAccount->name . ' - المبلغ المستحق الآن: ' . number_format($invoice->due_value, 2) . ' ر.س',
            ]);

            DB::commit();

            return redirect()
                ->route('PaymentSupplier.indexPurchase')
                ->with('success', sprintf('تم إلغاء عملية الدفع بنجاح وإنشاء قيد عكسي. تم استرداد مبلغ %s ر.س إلى خزينة %s، المبلغ المستحق للفاتورة الآن: %s ر.س', number_format($payment->amount, 2), $mainTreasuryAccount->name, number_format($invoice->due_value, 2)));
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في إلغاء الدفع: ' . $e->getMessage());
            return back()->with('error', 'حدث خطأ أثناء إلغاء الدفع: ' . $e->getMessage());
        }
    }

    /**
     * ✅ دالة إنشاء القيد المحاسبي العكسي لإلغاء الدفعة
     */
    private function createReversalAccountingEntry($invoice, $payment, $mainTreasuryAccount)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // حساب المورد
        $supplierAccount = Account::where('supplier_id', $invoice->supplier_id)->first();
        if (!$supplierAccount) {
            $supplier = Supplier::find($invoice->supplier_id);
            $supplierAccount = Account::create([
                'name' => 'حساب المورد - ' . ($supplier->trade_name ?? 'مورد غير معروف'),
                'supplier_id' => $invoice->supplier_id,
                'account_type' => 'supplier',
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // إنشاء القيد العكسي
        $reversalEntry = JournalEntry::create([
            'reference_number' => $invoice->code . '_إلغاء_دفعة_' . $payment->id . '_' . time(),
            'purchase_invoice_id' => $invoice->id,
            'date' => now(),
            'description' => 'قيد عكسي - إلغاء دفعة للمورد بمبلغ ' . number_format($payment->amount, 2) . ' ر.س - فاتورة # ' . $invoice->code,
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $invoice->supplier_id,
            'created_by_employee' => Auth::id(),
            'is_reversal' => true, // ✅ تعيين أن هذا قيد عكسي
            'original_payment_id' => $payment->id, // ✅ ربط بالدفعة الأصلية
        ]);

        // القيد العكسي: الخزينة (مدين) - استرداد النقد
        JournalEntryDetail::create([
            'journal_entry_id' => $reversalEntry->id,
            'account_id' => $mainTreasuryAccount->id,
            'description' => 'قيد عكسي - استرداد دفعة للمورد - فاتورة # ' . $invoice->code,
            'debit' => $payment->amount,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // القيد العكسي: حساب المورد (دائن) - زيادة الدين
        JournalEntryDetail::create([
            'journal_entry_id' => $reversalEntry->id,
            'account_id' => $supplierAccount->id,
            'description' => 'قيد عكسي - استرداد دفعة للمورد - فاتورة # ' . $invoice->code,
            'debit' => 0,
            'credit' => $payment->amount,
            'is_debit' => false,
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
