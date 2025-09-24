<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\Supplier;
use App\Models\TaxSitting;
use App\Models\TaxInvoice;
use App\Models\AccountSetting;

use App\Models\Log as ModelsLog;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\TreasuryEmployee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Commands\Show;

class ReturnsInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::all();
        $users = User::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        $taxes = TaxInvoice::all();

        // إذا كان الطلب Ajax، نعيد البيانات فقط
        if ($request->ajax()) {
            return $this->getFilteredData($request);
        }

        // في البداية نعيد الصفحة مع البيانات الأولية
        $returnData = $this->getFilteredData($request, false);

        return view('purchases::purchases.returns.index', compact('returnData', 'taxes', 'suppliers', 'users', 'account_setting'));
    }

    private function getFilteredData(Request $request, $returnJson = true)
    {
        $query = PurchaseInvoice::query()
            ->with(['supplier', 'creator'])
            ->where('type', 'Return');

        // تطبيق الفلاتر
        $this->applyFilters($query, $request);

        // ترتيب النتائج
        $query->orderBy('created_at', 'desc');

        // الحصول على النتائج مع التقسيم إلى صفحات
        $returnData = $query->paginate(30);

        if ($returnJson) {
            $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

            return response()->json([
                'success' => true,
                'data' => view('purchases::purchases.returns.partials.table', compact('returnData', 'account_setting'))->render(),
                'total' => $returnData->total(),
                'current_page' => $returnData->currentPage(),
                'last_page' => $returnData->lastPage(),
                'from' => $returnData->firstItem(),
                'to' => $returnData->lastItem(),
            ]);
        }

        return $returnData;
    }

    private function applyFilters($query, $request)
    {
        // البحث بواسطة المورد
        if ($request->filled('employee_search')) {
            $query->where('supplier_id', $request->employee_search);
        }

        // البحث برقم الفاتورة
        if ($request->filled('number_invoice')) {
            $query->where('code', 'LIKE', '%' . $request->number_invoice . '%');
        }

        // البحث بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // البحث بحالة الدفع
        if ($request->filled('payment_status')) {
            $this->applyPaymentStatusFilter($query, $request->payment_status);
        }

        // البحث بواسطة المنشئ
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // البحث بواسطة الوسم
        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('tags.id', $request->tag);
            });
        }

        // البحث في الحقل المخصص
        if ($request->filled('contract')) {
            $query->where('reference_number', 'LIKE', '%' . $request->contract . '%');
        }

        // البحث في وصف البنود
        if ($request->filled('description')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('description', 'LIKE', '%' . $request->description . '%');
            });
        }

        // البحث بنوع المصدر
        if ($request->filled('source')) {
            $query->where('type', $request->source);
        }

        // البحث بحالة التسليم
        if ($request->filled('delivery_status')) {
            $query->where('delivery_status', $request->delivery_status);
        }

        // البحث بالتاريخ
        if ($request->filled('start_date_from')) {
            $query->whereDate('date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->whereDate('date', '<=', $request->start_date_to);
        }

        // البحث بتاريخ الإنشاء
        if ($request->filled('created_at_from')) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->filled('created_at_to')) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // الفلاتر المضافة حديثاً بناءً على قاعدة البيانات:

        // البحث بحالة الاستلام (receiving_status)
        if ($request->filled('receiving_status')) {
            $query->where('receiving_status', $request->receiving_status);
        }

        // البحث برقم الحساب (account_id)
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        // البحث بعدد البنود (terms)
        if ($request->filled('terms_min')) {
            $query->where('terms', '>=', $request->terms_min);
        }
        if ($request->filled('terms_max')) {
            $query->where('terms', '<=', $request->terms_max);
        }

        // البحث بمبلغ الخصم (discount_amount)
        if ($request->filled('discount_amount_min')) {
            $query->where('discount_amount', '>=', $request->discount_amount_min);
        }
        if ($request->filled('discount_amount_max')) {
            $query->where('discount_amount', '<=', $request->discount_amount_max);
        }

        // البحث بنسبة الخصم (discount_percentage)
        if ($request->filled('discount_percentage_min')) {
            $query->where('discount_percentage', '>=', $request->discount_percentage_min);
        }
        if ($request->filled('discount_percentage_max')) {
            $query->where('discount_percentage', '<=', $request->discount_percentage_max);
        }

        // البحث بنوع الخصم (discount_type)
        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        // البحث بالدفعة المقدمة (advance_payment)
        if ($request->filled('advance_payment_min')) {
            $query->where('advance_payment', '>=', $request->advance_payment_min);
        }
        if ($request->filled('advance_payment_max')) {
            $query->where('advance_payment', '<=', $request->advance_payment_max);
        }

        // البحث بنوع الدفعة المقدمة (advance_payment_type)
        if ($request->filled('advance_payment_type')) {
            $query->where('advance_payment_type', $request->advance_payment_type);
        }

        // البحث بحالة الدفع المنطقية (is_paid)
        if ($request->filled('is_paid')) {
            $query->where('is_paid', $request->is_paid);
        }

        // البحث بطريقة الدفع (payment_method)
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // البحث بنوع الضريبة (tax_type)
        if ($request->filled('tax_type')) {
            $query->where('tax_type', $request->tax_type);
        }

        // البحث بتكلفة الشحن (shipping_cost)
        if ($request->filled('shipping_cost_min')) {
            $query->where('shipping_cost', '>=', $request->shipping_cost_min);
        }
        if ($request->filled('shipping_cost_max')) {
            $query->where('shipping_cost', '<=', $request->shipping_cost_max);
        }

        // البحث بالمجموع الفرعي (subtotal)
        if ($request->filled('subtotal_min')) {
            $query->where('subtotal', '>=', $request->subtotal_min);
        }
        if ($request->filled('subtotal_max')) {
            $query->where('subtotal', '<=', $request->subtotal_max);
        }

        // البحث بالقيمة المستحقة (due_value)
        if ($request->filled('due_value_min')) {
            $query->where('due_value', '>=', $request->due_value_min);
        }
        if ($request->filled('due_value_max')) {
            $query->where('due_value', '<=', $request->due_value_max);
        }

        // البحث بإجمالي الخصم (total_discount)
        if ($request->filled('total_discount_min')) {
            $query->where('total_discount', '>=', $request->total_discount_min);
        }
        if ($request->filled('total_discount_max')) {
            $query->where('total_discount', '<=', $request->total_discount_max);
        }

        // البحث بإجمالي الضريبة (total_tax)
        if ($request->filled('total_tax_min')) {
            $query->where('total_tax', '>=', $request->total_tax_min);
        }
        if ($request->filled('total_tax_max')) {
            $query->where('total_tax', '<=', $request->total_tax_max);
        }

        // البحث بالإجمالي الكلي (grand_total)
        if ($request->filled('grand_total_min')) {
            $query->where('grand_total', '>=', $request->grand_total_min);
        }
        if ($request->filled('grand_total_max')) {
            $query->where('grand_total', '<=', $request->grand_total_max);
        }

        // البحث في الملاحظات (notes)
        if ($request->filled('notes')) {
            $query->where('notes', 'LIKE', '%' . $request->notes . '%');
        }
    }

    // دالة مساعدة لفلترة حالة الدفع
    private function applyPaymentStatusFilter($query, $paymentStatus)
    {
        switch ($paymentStatus) {
            case 'paid':
                $query->where('payment_status', 'paid');
                break;
            case 'partial':
                $query->where('payment_status', 'partially_paid');
                break;
            case 'unpaid':
                $query->where('payment_status', 'unpaid');
                break;
            case 'returned':
                $query->where('payment_status', 'returned');
                break;
            case 'overpaid':
                $query->where('payment_status', 'overpaid');
                break;
            case 'draft':
                $query->where('payment_status', 'draft');
                break;
        }
    }

    // دالة للتعامل مع طلبات الـ pagination عبر Ajax
    public function paginatePurchase(Request $request)
    {
        if ($request->ajax()) {
            return $this->getFilteredPurchaseData($request);
        }

        return redirect()->route('ReturnsInvoice.index');
    }
    public function create()
    {
        $suppliers = Supplier::all();
        $items = Product::all();
        $taxs = TaxSitting::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        // جلب الفواتير المتاحة للمرتجع (فواتير عادية فقط)
        $availableInvoices = PurchaseInvoice::where('type', 'invoice')
            ->with(['supplier'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchases::purchases.returns.create', compact('suppliers', 'taxs', 'account_setting', 'items', 'availableInvoices'));
    }

    /**
     * إنشاء مرتجع من فاتورة موجودة
     */
    public function createFromInvoice($invoiceId)
    {
        $originalInvoice = PurchaseInvoice::with(['supplier', 'items.product'])->findOrFail($invoiceId);

        if ($originalInvoice->type !== 'invoice') {
            return redirect()->back()->with('error', 'يمكن إنشاء مرتجع فقط من الفواتير العادية');
        }

        $suppliers = Supplier::all();
        $items = Product::all();
        $taxs = TaxSitting::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        return view('purchases::purchases.returns.create', compact('suppliers', 'taxs', 'account_setting', 'items', 'originalInvoice'));
    }

    /**
     * جلب تفاصيل فاتورة عبر Ajax
     */
    public function getInvoiceDetails($invoiceId)
    {
        try {
            $invoice = PurchaseInvoice::with(['supplier', 'items.product'])->findOrFail($invoiceId);

            if ($invoice->type !== 'invoice') {
                return response()->json(['error' => 'يمكن إنشاء مرتجع فقط من الفواتير العادية'], 400);
            }

            return response()->json([
                'success' => true,
                'invoice' => $invoice,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'الفاتورة غير موجودة'], 404);
        }
    }

    public function store(Request $request)
    {
        try {
            // ** التحقق من وجود الفاتورة الأصلية إذا تم تمرير reference_id **
            $originalInvoice = null;
            if ($request->has('reference_id') && $request->reference_id) {
                $originalInvoice = PurchaseInvoice::find($request->reference_id);
                if (!$originalInvoice) {
                    return redirect()->back()->withInput()->with('error', 'الفاتورة المرجعية غير موجودة');
                }
                if ($originalInvoice->type !== 'invoice') {
                    return redirect()->back()->withInput()->with('error', 'يمكن إنشاء مرتجع فقط للفواتير العادية');
                }
            }

            // ** التحقق من وجود المورد **
            if (!$request->supplier_id) {
                throw new \Exception('يجب تحديد المورد');
            }

            $supplier = Supplier::find($request->supplier_id);
            if (!$supplier) {
                throw new \Exception('المورد المحدد غير موجود');
            }

            // ** الخطوة الأولى: إنشاء كود للفاتورة **
            $code = $request->code;
            if (!$code) {
                $lastOrder = PurchaseInvoice::orderBy('id', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->code) + 1 : 1;
                $code = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $existingCode = PurchaseInvoice::where('code', $request->code)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            }

            DB::beginTransaction(); // بدء المعاملة

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **
            if ($request->has('items') && count($request->items)) {
                foreach ($request->items as $item) {
                    // جلب المنتج
                    $product = Product::findOrFail($item['product_id']);

                    // حساب تفاصيل الكمية والأسعار
                    $quantity = floatval($item['quantity']);
                    $unit_price = floatval($item['unit_price']);
                    $item_total = $quantity * $unit_price;

                    // حساب الخصم للبند
                    $item_discount = 0; // قيمة الخصم المبدئية
                    if (isset($item['discount']) && $item['discount'] > 0) {
                        if (isset($item['discount_type']) && $item['discount_type'] === 'percentage') {
                            $item_discount = ($item_total * floatval($item['discount'])) / 100;
                        } else {
                            $item_discount = floatval($item['discount']);
                        }
                    }

                    // تحديث الإجماليات
                    $total_amount += $item_total;
                    $total_discount += $item_discount;

                    // تجهيز بيانات البند
                    $items_data[] = [
                        'purchase_invoice_id' => null, // سيتم تعيينه لاحقًا بعد إنشاء الفاتورة
                        'product_id' => $item['product_id'],
                        'item' => $product->name ?? 'المنتج ' . $item['product_id'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'discount' => $item_discount, // تخزين الخصم للبند
                        'discount_type' => isset($item['discount_type']) && $item['discount_type'] === 'percentage' ? 2 : 1,
                        'tax_1' => floatval($item['tax_1'] ?? 0),
                        'tax_2' => floatval($item['tax_2'] ?? 0),
                        'total' => $item_total - $item_discount,
                    ];
                }
            }

            // ** الخطوة الثالثة: حساب الخصم الإضافي للفاتورة ككل **
            $invoice_discount = 0;
            if ($request->has('discount_amount') && $request->discount_amount > 0) {
                if ($request->has('discount_type') && $request->discount_type === 'percentage') {
                    $invoice_discount = ($total_amount * floatval($request->discount_amount)) / 100;
                } else {
                    $invoice_discount = floatval($request->discount_amount);
                }
            }

            // ** حساب الضرائب **
            $total_tax = 0;
            foreach ($request->items as $item) {
                $tax_1 = floatval($item['tax_1'] ?? 0); // الضريبة الأولى
                $tax_2 = floatval($item['tax_2'] ?? 0); // الضريبة الثانية

                // حساب الضريبة لكل بند
                $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
                $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

                // إضافة الضريبة إلى الإجمالي
                $total_tax += $item_tax;
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $invoice_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            $shipping_cost = floatval($request->shipping_cost ?? 0);

            // ** حساب ضريبة التوصيل (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($request->tax_type == 1) {
                $shipping_tax = $shipping_cost * 0.15; // ضريبة التوصيل 15%
            }

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost + $shipping_tax;

            // *** تحديد حالات الدفع والاستلام (نفس منطق الفاتورة العادية) ***
            $paid_amount = 0;
            $advance_payment = 0; // الدفعة المقدمة المنفصلة
            $payment_status = 'unpaid'; // الحالة الافتراضية للدفع

            // فحص إذا كان هناك دفع كامل
            if ($request->has('is_paid') && $request->is_paid == '1') {
                $paid_amount = $total_with_tax; // دفع كامل
                $advance_payment = $total_with_tax; // نفس المبلغ للدفعة المقدمة
                $payment_status = 'paid'; // مدفوعة بالكامل
            }
            // أو إذا كان هناك دفعة مقدمة من حقل advance_payment
            elseif ($request->has('advance_payment') && floatval($request->advance_payment) > 0) {
                $advance_payment = floatval($request->advance_payment);
                $paid_amount = $advance_payment;

                // التحقق من نوع الدفعة المقدمة (مبلغ أو نسبة مئوية)
                if ($request->has('advance_payment_type') && $request->advance_payment_type === 'percentage') {
                    $advance_payment = ($total_with_tax * $advance_payment) / 100;
                    $paid_amount = $advance_payment;
                }

                // التأكد من أن المبلغ المدفوع لا يتجاوز المبلغ الإجمالي
                if ($paid_amount >= $total_with_tax) {
                    $paid_amount = $total_with_tax;
                    $advance_payment = $total_with_tax;
                    $payment_status = 'paid'; // مدفوعة بالكامل
                } else {
                    $payment_status = 'partially_paid'; // مدفوعة جزئياً
                }
            }
            // أو إذا كان هناك مبلغ مدفوع من الحقل العام paid_amount
            elseif ($request->has('paid_amount') && floatval($request->paid_amount) > 0) {
                $paid_amount = floatval($request->paid_amount);
                $advance_payment = $paid_amount;

                // التأكد من أن المبلغ المدفوع لا يتجاوز المبلغ الإجمالي
                if ($paid_amount >= $total_with_tax) {
                    $paid_amount = $total_with_tax;
                    $advance_payment = $total_with_tax;
                    $payment_status = 'paid'; // مدفوعة بالكامل
                } else {
                    $payment_status = 'partially_paid'; // مدفوعة جزئياً
                }
            }

            $due_value = $total_with_tax - $paid_amount;

            // ✅ تحديد الخزينة المستهدفة بناءً على الموظف (نفس منطق الفاتورة العادية)
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

            // ✅ تحديد reference_id بشكل صحيح
            $reference_id = null;
            if ($originalInvoice) {
                // إذا كانت الفاتورة الأصلية موجودة، استخدم ID الخاص بها
                $reference_id = $originalInvoice->id;
            } elseif ($request->filled('reference_id')) {
                // إذا كان reference_id موجود في الطلب وليس فارغ
                $reference_id = $request->reference_id;
            }

            // ** الخطوة الرابعة: إنشاء مرتجع المشتريات في قاعدة البيانات **
            $purchaseInvoiceReturn = PurchaseInvoice::create([
                'supplier_id' => $request->supplier_id,
                'code' => $code,
                'type' => 'Return', // نوع المرتجع
                'reference_id' => $reference_id, // ✅ سيتم حفظه بشكل صحيح
                'date' => $request->date,
                'terms' => $request->terms ?? 0,
                'notes' => ($request->notes ?? '') . ($originalInvoice ? "\nمرتجع للفاتورة رقم: " . $originalInvoice->code : '') . "\nالخزينة المستخدمة: " . $mainTreasuryAccount->name,
                'payment_status' => $payment_status, // ✅ حالة الدفع الجديدة (enum)
                'created_by' => Auth::id(),
                'account_id' => $request->account_id,
                'discount_amount' => $invoice_discount, // تخزين الخصم الإضافي للفاتورة
                'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
                'advance_payment' => $advance_payment,
                'payment_type' => $request->payment_type ?? 1,
                'shipping_cost' => $shipping_cost,
                'tax_type' => $request->tax_type ?? 1,
                'payment_method' => $request->payment_method ?? $request->advance_payment_method,
                'reference_number' => $request->reference_number ?? $request->advance_reference_number,
                'received_date' => $request->received_date,
                'is_paid' => $payment_status === 'paid', // للتوافق مع النظام القديم
                'is_received' => $request->has('is_received'),
                'subtotal' => $total_amount,
                'total_discount' => $final_total_discount, // تخزين الخصم الإجمالي
                'total_tax' => $total_tax + $shipping_tax, // إضافة ضريبة التوصيل إلى مجموع الضرائب
                'grand_total' => $total_with_tax, // المبلغ الإجمالي الكامل
                'due_value' => $due_value, // المبلغ المستحق
            ]);

            // ✅ إضافة تأكيد إضافي للتأكد من الحفظ
            if ($reference_id) {
                Log::info('Reference ID Set Successfully', [
                    'return_id' => $purchaseInvoiceReturn->id,
                    'reference_id' => $reference_id,
                    'original_invoice_code' => $originalInvoice ? $originalInvoice->code : 'N/A',
                ]);
            }

            // ** تحديث نوع الفاتورة الأصلية إذا كانت موجودة **
            if ($originalInvoice) {
                $originalInvoice->update([
                    'type' => 'invoice', // تغيير نوع الفاتورة الأصلية
                ]);
            }

            // ** معالجة الضرائب **
            foreach ($request->items as $item) {
                // حساب الإجمالي لكل منتج (السعر × الكمية)
                $item_subtotal = $item['unit_price'] * $item['quantity'];

                // حساب الضرائب بناءً على البيانات القادمة من `request`
                $tax_ids = ['tax_1_id', 'tax_2_id'];
                foreach ($tax_ids as $tax_id) {
                    if (!empty($item[$tax_id])) {
                        // التحقق مما إذا كان هناك ضريبة
                        $tax = TaxSitting::find($item[$tax_id]);

                        if ($tax) {
                            $tax_value = ($tax->tax / 100) * $item_subtotal; // حساب قيمة الضريبة

                            // حفظ الضريبة في جدول TaxInvoice
                            TaxInvoice::create([
                                'name' => $tax->name,
                                'invoice_id' => $purchaseInvoiceReturn->id,
                                'type' => $tax->type,
                                'rate' => $tax->tax,
                                'value' => $tax_value,
                                'type_invoice' => 'InvoiceReturn_purchase',
                            ]);
                        }
                    }
                }
            }

            // ** الخطوة الخامسة: إنشاء سجلات البنود (items) للفاتورة **
            foreach ($items_data as $item) {
                $item['purchase_invoice_id'] = $purchaseInvoiceReturn->id; // تعيين purchase_invoice_id
                $invoiceItem = InvoiceItem::create($item); // تخزين البند مع purchase_invoice_id

                $invoiceItem->load('product');

                // تسجيل اشعار نظام جديد لكل منتج
                ModelsLog::create([
                    'type' => 'purchase_return_log',
                    'type_id' => $purchaseInvoiceReturn->id, // ID النشاط المرتبط
                    'type_log' => 'log', // نوع النشاط
                    'icon' => 'create',
                    'description' => sprintf(
                        'تم انشاء مرتجع شراء رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للمورد **%s**%s من خزينة **%s** - حالة الدفع: **%s**',
                        $purchaseInvoiceReturn->code ?? '', // رقم مرتجع الشراء
                        $invoiceItem->product->name ?? '', // اسم المنتج
                        $item['quantity'] ?? '', // الكمية
                        $item['unit_price'] ?? '', // السعر
                        $supplier->trade_name ?? '', // المورد
                        $originalInvoice ? ' مرتبط بالفاتورة رقم **' . $originalInvoice->code . '**' : '', // رقم الفاتورة المرجعية
                        $mainTreasuryAccount->name ?? '', // اسم الخزينة
                        $this->getPaymentStatusText($payment_status), // حالة الدفع
                    ),
                    'created_by' => auth()->id(), // ID المستخدم الحالي
                ]);
            }

            // *** إنشاء عملية الدفع إذا تم دفع مبلغ (في المرتجع هذا يعني أن المورد دفع للشركة) ***
            if ($paid_amount > 0) {
                $payment = PaymentsProcess::create([
                    'purchases_id' => $purchaseInvoiceReturn->id,
                    'supplier_id' => $request->supplier_id,
                    'amount' => $paid_amount,
                    'payment_date' => $request->date ?? now(),
                    'payment_method' => $request->payment_method ?? ($request->advance_payment_method ?? 1),
                    'type' => 'supplier return payment', // نوع دفع المرتجع
                    'payment_status' => 1, // مكتمل
                    'employee_id' => Auth::id(),
                    'treasury_id' => $treasury_id, // ✅ إضافة ID الخزينة المستخدمة
                    'notes' => $payment_status === 'paid' ? 'دفع كامل لمرتجع المشتريات رقم ' . $purchaseInvoiceReturn->code . ' إلى خزينة ' . $mainTreasuryAccount->name : 'دفع جزئي بمبلغ ' . number_format($paid_amount, 2) . ' لمرتجع المشتريات رقم ' . $purchaseInvoiceReturn->code . ' إلى خزينة ' . $mainTreasuryAccount->name,
                ]);
            }

            // ** إنشاء القيود المحاسبية المُصححة للمرتجع **
            $this->createReturnAccountingEntriesFixed($purchaseInvoiceReturn, $supplier, $paid_amount, $mainTreasuryAccount);

            // ** معالجة المرفقات (attachments) إذا وجدت **
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $purchaseInvoiceReturn->attachments = $filename;
                    $purchaseInvoiceReturn->save();
                }
            }

            // ✅ Debug: تحقق من حفظ reference_id
            Log::info('Purchase Return Created', [
                'return_id' => $purchaseInvoiceReturn->id,
                'return_code' => $purchaseInvoiceReturn->code,
                'reference_id' => $purchaseInvoiceReturn->reference_id,
                'original_invoice' => $originalInvoice ? $originalInvoice->code : 'none',
            ]);

            DB::commit(); // تأكيد التغييرات

            // إنشاء إشعار للنظام
            $user = Auth::user();
            notifications::create([
                'type' => 'purchase_return_creation',
                'title' => $user->name . ' أنشأ مرتجع شراء جديد',
                'description' => 'تم إنشاء مرتجع الشراء رقم ' . $purchaseInvoiceReturn->code . ' للمورد ' . ($supplier->trade_name ?? '') . ' بقيمة ' . number_format($purchaseInvoiceReturn->grand_total, 2) . ' ر.س' . ($originalInvoice ? ' مرتبط بالفاتورة رقم ' . $originalInvoice->code : '') . ' من خزينة ' . $mainTreasuryAccount->name . ($paid_amount > 0 ? ' - تم إيداع ' . number_format($paid_amount, 2) . ' ر.س' : '') . ' - حالة الدفع: ' . $this->getPaymentStatusText($payment_status),
            ]);

            // *** تحسين رسائل النجاح ***
            $message = $this->generateReturnSuccessMessage($payment_status, $paid_amount, $due_value, $mainTreasuryAccount->name, $originalInvoice);

            return redirect()->route('ReturnsInvoice.show', $purchaseInvoiceReturn->id)->with('success', $message);
        } catch (\Exception $e) {
            DB::rollback(); // تراجع عن التغييرات في حالة حدوث خطأ
            Log::error('خطأ في إنشاء مرتجع المشتريات: ' . $e->getMessage()); // تسجيل الخطأ
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء حفظ مرتجع المشتريات: ' . $e->getMessage());
        }
    }

    /**
     * دالة مساعدة لتحويل حالة الدفع إلى نص عربي
     */
    private function getPaymentStatusText($status)
    {
        switch ($status) {
            case 'paid':
                return 'مدفوعة بالكامل';
            case 'partially_paid':
                return 'مدفوعة جزئياً';
            case 'unpaid':
                return 'غير مدفوعة';
            default:
                return 'غير محددة';
        }
    }

    /**
     * دالة مساعدة لتوليد رسالة النجاح للمرتجع بناءً على الحالات
     */
    private function generateReturnSuccessMessage($payment_status, $paid_amount, $due_value, $treasury_name, $originalInvoice)
    {
        $payment_text = $this->getPaymentStatusText($payment_status);

        $message = 'تم إنشاء مرتجع المشتريات بنجاح من خزينة ' . $treasury_name;

        if ($originalInvoice) {
            $message .= ' وتم تحديث نوع الفاتورة الأصلية رقم ' . $originalInvoice->code;
        }

        switch ($payment_status) {
            case 'paid':
                $message .= ' - تم تسجيل الإيداع الكامل في الخزينة';
                break;
            case 'partially_paid':
                $message .= ' - تم تسجيل إيداع جزئي بمبلغ ' . number_format($paid_amount) . ' ريال في الخزينة. المتبقي: ' . number_format($due_value) . ' ريال';
                break;
            case 'unpaid':
                $message .= ' - بدون إيداع في الخزينة';
                break;
        }

        $message .= ' | حالة الدفع: ' . $payment_text;

        return $message;
    }

    private function createReturnAccountingEntriesFixed($purchaseInvoiceReturn, $supplier, $paid_amount, $mainTreasuryAccount)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

        // القيد الأول: تسجيل المرتجع (بشكل مبسط)
        $journalEntry1 = JournalEntry::create([
            'reference_number' => $purchaseInvoiceReturn->code,
'purchase_invoice_id' => $purchaseInvoiceReturn->id,
            'date' => now(),
            'description' => 'مرتجع مشتريات رقم ' . $purchaseInvoiceReturn->code,
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $purchaseInvoiceReturn->supplier_id,
            'created_by_employee' => Auth::id(),
        ]);

        // جلب أو إنشاء حساب المورد
        $supplierAccount = Account::where('supplier_id', $purchaseInvoiceReturn->supplier_id)->first();
        if (!$supplierAccount) {
            $supplierAccount = Account::create([
                'name' => 'حساب المورد - ' . ($supplier->trade_name ?? 'مورد غير معروف'),
                'supplier_id' => $purchaseInvoiceReturn->supplier_id,
                'account_type' => 'supplier',
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // جلب أو إنشاء حساب المشتريات (نفس الحساب المستخدم في الشراء)
        $purchasesAccount = Account::where('name', 'المشتريات')->first();
        if (!$purchasesAccount) {
            $purchasesAccount = Account::create([
                'name' => 'المشتريات',
                'account_type' => 'expense',
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // حساب المبلغ بدون ضريبة
        $subtotal = $purchaseInvoiceReturn->grand_total - $purchaseInvoiceReturn->total_tax;

        // 1. حساب المورد (مدين) - بكامل مبلغ المرتجع
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry1->id,
            'account_id' => $supplierAccount->id,
            'description' => 'مرتجع مشتريات رقم ' . $purchaseInvoiceReturn->code,
            'debit' => $purchaseInvoiceReturn->grand_total,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // 2. حساب المشتريات (دائن) - بالمبلغ بدون ضريبة
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry1->id,
            'account_id' => $purchasesAccount->id,
            'description' => 'مرتجع مشتريات رقم ' . $purchaseInvoiceReturn->code,
            'debit' => 0,
            'credit' => $subtotal,
            'is_debit' => false,
        ]);

        // 3. معالجة الضرائب إذا وجدت
        if ($purchaseInvoiceReturn->total_tax > 0) {
            $taxAccount = Account::where('name', 'القيمة المضافة')->first();
            if (!$taxAccount) {
                $taxAccount = Account::create([
                    'name' => 'القيمة المضافة',
                    'account_type' => 'asset',
                    'balance' => 0,
                    'status' => 1,
                ]);
            }

            // حساب VAT (دائن) - تقليل VAT المدفوعة
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry1->id,
                'account_id' => $taxAccount->id,
                'description' => 'استرداد VAT مرتجع رقم ' . $purchaseInvoiceReturn->code,
                'debit' => 0,
                'credit' => $purchaseInvoiceReturn->total_tax,
                'is_debit' => false,
            ]);

            // تحديث رصيد ضريبة القيمة المضافة
            $taxAccount->balance -= $purchaseInvoiceReturn->total_tax;
            $taxAccount->save();
        }

        // تحديث أرصدة الحسابات
        // تقليل رصيد المورد (تقليل الدين علينا)
        $supplierAccount->balance -= $purchaseInvoiceReturn->grand_total;
        $supplierAccount->save();

        // تقليل رصيد المشتريات
        $purchasesAccount->balance -= $subtotal;
        $purchasesAccount->save();

        // القيد الثاني: تسجيل الدفع من المورد إلى الخزينة (إذا كان هناك دفع)
        if ($paid_amount > 0) {
            $journalEntry2 = JournalEntry::create([
                'reference_number' => $purchaseInvoiceReturn->code . '_دفع',
                'date' => now(),
                'description' => 'دفع من المورد - مرتجع ' . $purchaseInvoiceReturn->code,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $purchaseInvoiceReturn->supplier_id,
                'created_by_employee' => Auth::id(),
            ]);

            // حساب الخزينة (مدين) - دخول نقد
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry2->id,
                'account_id' => $mainTreasuryAccount->id,
                'description' => 'دفع من المورد - مرتجع ' . $purchaseInvoiceReturn->code,
                'debit' => $paid_amount,
                'credit' => 0,
                'is_debit' => true,
            ]);

            // حساب المورد (دائن) - زيادة الدين عليه
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry2->id,
                'account_id' => $supplierAccount->id,
                'description' => 'دفع من المورد - مرتجع ' . $purchaseInvoiceReturn->code,
                'debit' => 0,
                'credit' => $paid_amount,
                'is_debit' => false,
            ]);

            // تحديث أرصدة الحسابات
            $mainTreasuryAccount->balance += $paid_amount;
            $mainTreasuryAccount->save();

            $supplierAccount->balance += $paid_amount;
            $supplierAccount->save();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public function edit($id)
    {
        $returnInvoice = purchaseInvoice::findOrFail($id);
        $suppliers = Supplier::all();
        $items = Product::all();
        $taxs = TaxInvoice::all();

        return view('purchases::purchases.returns.edit', compact('returnInvoice', 'suppliers', 'items', 'taxs'));
    }
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $purchaseInvoiceReturn = PurchaseInvoice::findOrFail($id);

            // التحقق من حالة التسليم - لا تسمح بالتعديل إذا كانت مستلمة
            if ($purchaseInvoiceReturn->receiving_status === 'received') {
                throw new \Exception('لا يمكن تعديل مرتجع مشتريات مستلم بالفعل');
            }

            // ** التحقق من وجود الفاتورة الأصلية إذا تم تمرير reference_id **
            $originalInvoice = null;
            if ($request->has('reference_id') && $request->reference_id) {
                $originalInvoice = PurchaseInvoice::find($request->reference_id);
                if (!$originalInvoice) {
                    return redirect()->back()->withInput()->with('error', 'الفاتورة المرجعية غير موجودة');
                }
                if ($originalInvoice->type !== 'invoice') {
                    return redirect()->back()->withInput()->with('error', 'يمكن إنشاء مرتجع فقط للفواتير العادية');
                }
            }

            // ** التحقق من وجود المورد **
            if (!$request->supplier_id) {
                throw new \Exception('يجب تحديد المورد');
            }

            $supplier = Supplier::find($request->supplier_id);
            if (!$supplier) {
                throw new \Exception('المورد المحدد غير موجود');
            }

            // ** التحقق من عدم تكرار الكود إذا تم تغييره **
            $code = $request->code;
            if ($code && $code !== $purchaseInvoiceReturn->code) {
                $existingCode = PurchaseInvoice::where('code', $code)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            } else {
                $code = $purchaseInvoiceReturn->code;
            }

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** معالجة البنود (items) **
            if ($request->has('items') && count($request->items)) {
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    // حساب تفاصيل الكمية والأسعار
                    $quantity = floatval($item['quantity']);
                    $unit_price = floatval($item['unit_price']);
                    $item_total = $quantity * $unit_price;

                    // حساب الخصم للبند
                    $item_discount = 0;
                    if (isset($item['discount']) && $item['discount'] > 0) {
                        if (isset($item['discount_type']) && $item['discount_type'] === 'percentage') {
                            $item_discount = ($item_total * floatval($item['discount'])) / 100;
                        } else {
                            $item_discount = floatval($item['discount']);
                        }
                    }

                    // تحديث الإجماليات
                    $total_amount += $item_total;
                    $total_discount += $item_discount;

                    // تجهيز بيانات البند
                    $items_data[] = [
                        'purchase_invoice_id' => $purchaseInvoiceReturn->id,
                        'product_id' => $item['product_id'],
                        'item' => $product->name ?? 'المنتج ' . $item['product_id'],
                        'description' => $item['description'] ?? null,
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'discount' => $item_discount,
                        'discount_type' => isset($item['discount_type']) && $item['discount_type'] === 'percentage' ? 2 : 1,
                        'tax_1' => floatval($item['tax_1'] ?? 0),
                        'tax_2' => floatval($item['tax_2'] ?? 0),
                        'total' => $item_total - $item_discount,
                    ];
                }
            }

            // ** حساب الخصم الإضافي للفاتورة ككل **
            $invoice_discount = 0;
            if ($request->has('discount_amount') && $request->discount_amount > 0) {
                if ($request->has('discount_type') && $request->discount_type === 'percentage') {
                    $invoice_discount = ($total_amount * floatval($request->discount_amount)) / 100;
                } else {
                    $invoice_discount = floatval($request->discount_amount);
                }
            }

            // ** حساب الضرائب **
            $total_tax = 0;
            foreach ($request->items as $item) {
                $tax_1 = floatval($item['tax_1'] ?? 0);
                $tax_2 = floatval($item['tax_2'] ?? 0);

                $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
                $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

                $total_tax += $item_tax;
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $invoice_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            $shipping_cost = floatval($request->shipping_cost ?? 0);

            // ** حساب ضريبة التوصيل (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($request->tax_type == 1) {
                $shipping_tax = $shipping_cost * 0.15;
            }

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost + $shipping_tax;

            // *** تحديد حالات الدفع والاستلام ***
            $paid_amount = 0;
            $advance_payment = 0;
            $payment_status = 'unpaid';

            if ($request->has('is_paid') && $request->is_paid == '1') {
                $paid_amount = $total_with_tax;
                $advance_payment = $total_with_tax;
                $payment_status = 'paid';
            } elseif ($request->has('advance_payment') && floatval($request->advance_payment) > 0) {
                $advance_payment = floatval($request->advance_payment);
                $paid_amount = $advance_payment;

                if ($request->has('advance_payment_type') && $request->advance_payment_type === 'percentage') {
                    $advance_payment = ($total_with_tax * $advance_payment) / 100;
                    $paid_amount = $advance_payment;
                }

                if ($paid_amount >= $total_with_tax) {
                    $paid_amount = $total_with_tax;
                    $advance_payment = $total_with_tax;
                    $payment_status = 'paid';
                } else {
                    $payment_status = 'partially_paid';
                }
            } elseif ($request->has('paid_amount') && floatval($request->paid_amount) > 0) {
                $paid_amount = floatval($request->paid_amount);
                $advance_payment = $paid_amount;

                if ($paid_amount >= $total_with_tax) {
                    $paid_amount = $total_with_tax;
                    $advance_payment = $total_with_tax;
                    $payment_status = 'paid';
                } else {
                    $payment_status = 'partially_paid';
                }
            }

            $due_value = $total_with_tax - $paid_amount;

            // ✅ تحديد الخزينة المستهدفة بناءً على الموظف
            $mainTreasuryAccount = null;
            $treasury_id = null;
            $user = Auth::user();

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

            // ✅ تحديد reference_id بشكل صحيح
            $reference_id = null;
            if ($originalInvoice) {
                $reference_id = $originalInvoice->id;
            } elseif ($request->filled('reference_id')) {
                $reference_id = $request->reference_id;
            }

            // ** تحديث بيانات المرتجع **
            $purchaseInvoiceReturn->update([
                'supplier_id' => $request->supplier_id,
                'code' => $code,
                'reference_id' => $reference_id,
                'date' => $request->date,
                'terms' => $request->terms ?? 0,
                'notes' => ($request->notes ?? '') . ($originalInvoice ? "\nمرتجع للفاتورة رقم: " . $originalInvoice->code : '') . "\nالخزينة المستخدمة: " . $mainTreasuryAccount->name,
                'payment_status' => $payment_status,
                'account_id' => $request->account_id,
                'discount_amount' => $invoice_discount,
                'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
                'advance_payment' => $advance_payment,
                'payment_type' => $request->payment_type ?? 1,
                'shipping_cost' => $shipping_cost,
                'tax_type' => $request->tax_type ?? 1,
                'payment_method' => $request->payment_method ?? $request->advance_payment_method,
                'reference_number' => $request->reference_number ?? $request->advance_reference_number,
                'received_date' => $request->received_date,
                'is_paid' => $payment_status === 'paid',
                'is_received' => $request->has('is_received'),
                'subtotal' => $total_amount,
                'total_discount' => $final_total_discount,
                'total_tax' => $total_tax + $shipping_tax,
                'grand_total' => $total_with_tax,
                'due_value' => $due_value,
            ]);

            // ** حذف البنود القديمة وإنشاء البنود الجديدة **
            $purchaseInvoiceReturn->invoiceItems()->delete();
            foreach ($items_data as $item) {
                InvoiceItem::create($item);
            }

            // ** معالجة الضرائب **
            TaxInvoice::where('invoice_id', $purchaseInvoiceReturn->id)->where('type_invoice', 'InvoiceReturn_purchase')->delete();

            foreach ($request->items as $item) {
                $item_subtotal = $item['unit_price'] * $item['quantity'];

                $tax_ids = ['tax_1_id', 'tax_2_id'];
                foreach ($tax_ids as $tax_id) {
                    if (!empty($item[$tax_id])) {
                        $tax = TaxSitting::find($item[$tax_id]);

                        if ($tax) {
                            $tax_value = ($tax->tax / 100) * $item_subtotal;

                            TaxInvoice::create([
                                'name' => $tax->name,
                                'invoice_id' => $purchaseInvoiceReturn->id,
                                'type' => $tax->type,
                                'rate' => $tax->tax,
                                'value' => $tax_value,
                                'type_invoice' => 'InvoiceReturn_purchase',
                            ]);
                        }
                    }
                }
            }

            // ** معالجة المرفقات **
            if ($request->hasFile('attachments')) {
                if ($purchaseInvoiceReturn->attachments) {
                    $oldFile = public_path('assets/uploads/') . $purchaseInvoiceReturn->attachments;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $purchaseInvoiceReturn->attachments = $filename;
                    $purchaseInvoiceReturn->save();
                }
            }

            DB::commit();

            return redirect()->route('ReturnsInvoice.show', $purchaseInvoiceReturn->id)->with('success', 'تم تحديث مرتجع المشتريات بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث مرتجع المشتريات: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء تحديث مرتجع المشتريات: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $purchaseOrder = PurchaseInvoice::findOrFail($id);
            ModelsLog::create([
                'type' => 'purchase_return_log',
                'type_id' => $purchaseOrder->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'icon' => 'delete',
                'description' => sprintf(
                    'تم حذف مرتجع شراء رقم **%s**',
                    $purchaseOrder->code ?? '', // رقم طلب الشراء
                ),
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
            $purchaseOrder->delete();
            return redirect()->route('ReturnsInvoice.index')->with('success', 'تم حذف مرتجع المشتريات بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حذف مرتجع المشتريات: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء حذف مرتجع المشتريات: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $purchaseInvoiceReturn = PurchaseInvoice::with(['supplier', 'items.product', 'payments', 'user'])->findOrFail($id);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'purchase')->get();

        // جلب سجل الأنشطة
        $logs = ModelsLog::where('type', 'purchase_return_log')
            ->where('type_id', $id)
            ->whereHas('purchase_return_log') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('purchases::purchases.returns.show', compact('purchaseInvoiceReturn', 'logs', 'TaxsInvoice'));
    }
    public function pdf($id)
    {
        $purchaseRentalInvoice = PurchaseInvoice::with(['items.product'])->findOrFail($id);
        $pdf = Pdf::loadView('Purchases.Returns.pdf', compact('purchaseRentalInvoice'));
        return $pdf->download('مرتجع_مشتريات.pdf');
    }
}
