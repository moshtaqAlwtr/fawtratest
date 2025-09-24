<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Log as ModelsLog;
use App\Models\PaymentsProcess;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\PurchaseInvoice;
use App\Models\StoreHouse;
use App\Models\Supplier;
use App\Models\TaxSitting;
use App\Models\TaxInvoice;
use App\Models\AccountSetting;
use App\Models\ClientRelation;
use App\Models\notifications;
use App\Models\TreasuryEmployee;
use App\Models\User;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoicesPurchaseController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::all();
        $users = User::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        // إذا كان الطلب Ajax، نعيد البيانات فقط
        if ($request->ajax()) {
            return $this->getFilteredData($request);
        }

        // في البداية نعيد الصفحة مع البيانات الأولية
        $purchaseData = $this->getFilteredData($request, false);

        return view('purchases::purchases.invoices_purchase.index', compact('purchaseData', 'suppliers', 'users', 'account_setting'));
    }

    private function getFilteredData(Request $request, $returnJson = true)
{
    $query = PurchaseInvoice::query()
    ->with(['supplier', 'creator'])
    ->whereIn('type', ['invoice', 'Return']);


    // تطبيق الفلاتر
    $this->applyFilters($query, $request);

    // استبعاد الفواتير التي لها فواتير مرتجع
    $query->whereDoesntHave('returns', function ($subQuery) {
        $subQuery->where('type', 'Return');
    });

    // ترتيب النتائج
    $query->orderBy('created_at', 'desc');

    // الحصول على النتائج مع التقسيم إلى صفحات
    $purchaseData = $query->paginate(30);

    if ($returnJson) {
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        return response()->json([
            'success' => true,
            'data' => view('purchases::purchases.invoices_purchase.partials.table', compact('purchaseData', 'account_setting'))->render(),
            'total' => $purchaseData->total(),
            'current_page' => $purchaseData->currentPage(),
            'last_page' => $purchaseData->lastPage(),
            'from' => $purchaseData->firstItem(),
            'to' => $purchaseData->lastItem(),
        ]);
    }

    return $purchaseData;
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

        return redirect()->route('invoicePurchases.index');
    }

    // دالة حذف الفاتورة مع دعم Ajax


    public function create(Request $request)
    {
        $suppliers = Supplier::all();
        $selectedSupplierId = $request->supplier_id;
        $items = Product::all();
        $accounts = Account::all();
        $storeHouses = StoreHouse::all(); // إضافة المستودعات
        $taxs = TaxSitting::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

            $code = DB::transaction(function () {
        $lastInvoice = PurchaseInvoice::lockForUpdate()
                      ->orderByRaw('CAST(code AS UNSIGNED) DESC')
                      ->first();

        $nextCode = $lastInvoice ? ((int)$lastInvoice->code + 1) : 1;
        return str_pad($nextCode, 5, '0', STR_PAD_LEFT);
    });

        return view('purchases::purchases.invoices_purchase.create', compact('suppliers','code', 'items', 'taxs', 'accounts', 'storeHouses', 'account_setting', 'selectedSupplierId'));
    }

public function store(Request $request)
{
    try {
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

        // ** التحقق من وجود المورد **
        if (!$request->supplier_id) {
            throw new \Exception('يجب تحديد المورد');
        }

        $supplier = Supplier::find($request->supplier_id);
        if (!$supplier) {
            throw new \Exception('المورد المحدد غير موجود');
        }

        // ✅ جلب جميع إعدادات فواتير الشراء
        $autoPaidSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('default_paid_invoices');
        $autoReceivedSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('default_received_invoices');
        $autoPaymentSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('auto_payment');
        $updatePricesSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('update_product_prices');
        $manualInvoiceStatusSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('manual_invoice_status');
        $enableSettlementSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('enable_settlement');
        $totalDiscountsSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('total_discounts');

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
                    'store_house_id' => $item['store_house_id'] ?? null, // إضافة معرف المستودع
                ];

                // ✅ تحديث أسعار المنتجات إذا كان الإعداد مفعل
                if ($updatePricesSetting) {
                    $product->update([
                        'purchase_price' => $unit_price,
                        'price' => $unit_price, // أو يمكن حساب سعر البيع بناءً على هامش ربح
                        'updated_at' => now()
                    ]);
                }
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

        // ✅ إجمالي الخصومات مع مراعاة الإعداد
        $final_total_discount = $totalDiscountsSetting ? $total_discount + $invoice_discount : $invoice_discount;

        // حساب المبلغ بعد الخصم
        $amount_after_discount = $total_amount - $final_total_discount;

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

        // ** إضافة تكلفة الشحن (إذا وجدت) **
        $shipping_cost = floatval($request->shipping_cost ?? 0);

        // ** حساب ضريبة التوصيل (إذا كانت الضريبة مفعلة) **
        $shipping_tax = 0;
        if ($request->tax_type == 1) {
            $shipping_tax = $shipping_cost * 0.15; // ضريبة التوصيل 15%
        }

        // ** الحساب النهائي للمجموع الكلي **
        $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost + $shipping_tax;

        // *** تحديد حالات الدفع والاستلام مع جميع الإعدادات ***
        $paid_amount = 0;
        $advance_payment = 0; // الدفعة المقدمة المنفصلة
        $payment_status = 'unpaid'; // الحالة الافتراضية للدفع
        $receiving_status = 'not_received'; // الحالة الافتراضية للاستلام

        // ✅ حالة الاستلام بناءً على الإعداد
        if ($autoReceivedSetting || ($request->has('is_received') && $request->is_received == '1')) {
            $receiving_status = 'received'; // مستلمة
        }

        // ✅ حالة الدفع بناءً على الإعدادات المختلفة
        $shouldAutoPay = false;

        // 1. إعداد الدفع الكامل التلقائي
        if ($autoPaidSetting) {
            $shouldAutoPay = true;
        }
        // 2. الدفع التلقائي إذا كان لدى المورد رصيد صالح
        elseif ($autoPaymentSetting && $supplier && $supplier->account && $supplier->account->balance > 0) {
            $shouldAutoPay = true;
        }
        // 3. دفع يدوي من المستخدم
        elseif ($request->has('is_paid') && $request->is_paid == '1') {
            $shouldAutoPay = true;
        }

        if ($shouldAutoPay) {
            $paid_amount = $total_with_tax; // دفع كامل
            $advance_payment = $total_with_tax; // نفس المبلغ للدفعة المقدمة
            $payment_status = 'paid'; // مدفوعة بالكامل
        }
        // أو إذا كان هناك دفعة مقدمة من حقل advance_payment (فقط إذا لم يكن الدفع التلقائي مفعل)
        elseif (!$shouldAutoPay && $request->has('advance_payment') && floatval($request->advance_payment) > 0) {
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
        // أو إذا كان هناك مبلغ مدفوع من الحقل العام paid_amount (فقط إذا لم يكن الدفع التلقائي مفعل)
        elseif (!$shouldAutoPay && $request->has('paid_amount') && floatval($request->paid_amount) > 0) {
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

        // ✅ تحديد الخزينة المستهدفة بناءً على الموظف
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

        // ✅ التحقق من وجود رصيد كافي في الخزينة قبل السحب
        if ($paid_amount > 0 && $mainTreasuryAccount->balance < $paid_amount) {
            throw new \Exception('رصيد الخزينة غير كافي. الرصيد الحالي: ' . number_format($mainTreasuryAccount->balance, 2) . ' والمطلوب: ' . number_format($paid_amount, 2));
        }

        // ✅ تحديد حالة الفاتورة بناءً على الإعدادات
        $invoice_status = 'pending'; // الحالة الافتراضية
        if (!$manualInvoiceStatusSetting) {
            // إذا لم يكن الإعداد اليدوي مفعل، تحديد الحالة تلقائياً
            if ($payment_status === 'paid' && $receiving_status === 'received') {
                $invoice_status = 'completed';
            } elseif ($payment_status === 'paid') {
                $invoice_status = 'paid_pending_delivery';
            } elseif ($receiving_status === 'received') {
                $invoice_status = 'delivered_pending_payment';
            } else {
                $invoice_status = 'pending';
            }
        }

        // ** الخطوة الرابعة: إنشاء الفاتورة في قاعدة البيانات **
        $purchaseInvoice = PurchaseInvoice::create([
            'supplier_id' => $request->supplier_id,
            'code' => $code,
            'type' => 'invoice', // نوع الفاتورة: مشتريات
            'date' => $request->date,
            'terms' => $request->terms ?? 0,
            'notes' => ($request->notes ?? '') .
                      "\nالخزينة المستخدمة: " . $mainTreasuryAccount->name .
                      ($autoPaidSetting ? "\n(دفع تلقائي بالكامل مفعل)" : "") .
                      ($autoReceivedSetting ? "\n(استلام تلقائي مفعل)" : "") .
                      ($autoPaymentSetting ? "\n(دفع تلقائي حسب رصيد المورد مفعل)" : "") .
                      ($updatePricesSetting ? "\n(تحديث أسعار المنتجات مفعل)" : ""),
            'payment_status' => $payment_status, // ✅ حالة الدفع الجديدة (enum)
            'receiving_status' => $receiving_status, // ✅ حالة الاستلام

            'created_by' => Auth::id(),
            'account_id' => $request->account_id,
            'discount_amount' => $invoice_discount, // تخزين الخصم الإضافي للفاتورة
            'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
            'payment_type' => $request->payment_type ?? 1,
            'shipping_cost' => $shipping_cost,
            'tax_type' => $request->tax_type ?? 1,
            'payment_method' => $request->payment_method ?? $request->advance_payment_method,
            'reference_number' => $request->reference_number ?? $request->advance_reference_number,
            'received_date' => $autoReceivedSetting ? now() : $request->received_date,
            'is_paid' => $payment_status === 'paid', // للتوافق مع النظام القديم
            'is_received' => $receiving_status === 'received', // للتوافق مع النظام القديم
            'subtotal' => $total_amount,
            'total_discount' => $final_total_discount, // تخزين الخصم الإجمالي
            'total_tax' => $total_tax + $shipping_tax, // إضافة ضريبة التوصيل إلى مجموع الضرائب
            'grand_total' => $total_with_tax, // تخزين المبلغ الإجمالي الكامل
            'due_value' => $due_value, // القيمة المستحقة (بعد خصم الدفعة المقدمة)
            'advance_payment' => $advance_payment, // ✅ استخدام المتغير المنفصل للدفعة المقدمة
        ]);

        // ** إنشاء الإذن المخزني **
        $warehousePermit = $this->createWarehousePermit($purchaseInvoice, $request);

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
                            'invoice_id' => $purchaseInvoice->id,
                            'type' => $tax->type,
                            'rate' => $tax->tax,
                            'value' => $tax_value,
                            'type_invoice' => 'purchase',
                        ]);
                    }
                }
            }
        }

        // ** الخطوة الخامسة: إنشاء سجلات البنود (items) للفاتورة والإذن المخزني **
        foreach ($items_data as $item) {
            $item['purchase_invoice_id'] = $purchaseInvoice->id; // تعيين purchase_invoice_id
            $invoiceItem = InvoiceItem::create($item); // تخزين البند مع purchase_invoice_id

            // إنشاء بند في الإذن المخزني
            WarehousePermitsProducts::create([
                'warehouse_permits_id' => $warehousePermit->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total'],
                'notes' => $item['description'],
                'store_house_id' => $item['store_house_id'],
            ]);

            $product = Product::find($item['product_id']);

            // تسجيل اشعار نظام جديد لكل منتج
            ModelsLog::create([
                'type' => 'purchase_invoice',
                'type_id' => $purchaseInvoice->id,
                'type_log' => 'log',
                'icon' => 'create',
                'description' => sprintf(
                    'تم انشاء فاتورة شراء رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للمورد **%s** وإذن مخزني رقم **%s** من خزينة **%s** - حالة الدفع: **%s** - حالة الاستلام: **%s**%s%s%s',
                    $purchaseInvoice->code ?? '',
                    $product->name ?? '',
                    $item['quantity'] ?? '',
                    $item['unit_price'] ?? '',
                    $supplier->trade_name ?? '',
                    $warehousePermit->number ?? '',
                    $mainTreasuryAccount->name ?? '',
                    $this->getPaymentStatusText($payment_status),
                    $this->getReceivingStatusText($receiving_status),
                    $autoPaidSetting ? ' (دفع تلقائي)' : '',
                    $autoReceivedSetting ? ' (استلام تلقائي)' : '',
                    $updatePricesSetting ? ' (تحديث أسعار)' : ''
                ),
                'created_by' => auth()->id(),
            ]);
        }

        // ** معالجة المرفقات (attachments) إذا وجدت **
        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/'), $filename);
                $purchaseInvoice->attachments = $filename;
                $purchaseInvoice->save();
            }
        }

        // *** إنشاء عملية الدفع إذا تم دفع مبلغ ***
        if ($paid_amount > 0) {
            $paymentNotes = '';
            if ($autoPaidSetting) {
                $paymentNotes = 'دفعة كاملة تلقائية (إعداد الدفع التلقائي مفعل)';
            } elseif ($autoPaymentSetting) {
                $paymentNotes = 'دفعة كاملة تلقائية (رصيد المورد صالح)';
            } else {
                $paymentNotes = $payment_status === 'paid' ? 'دفعة كاملة' : 'دفعة مقدمة بمبلغ ' . number_format($paid_amount, 2);
            }

            $payment = PaymentsProcess::create([
                'purchases_id' => $purchaseInvoice->id,
                'supplier_id' => $request->supplier_id,
                'amount' => $paid_amount,
                'payment_date' => $request->date ?? now(),
                'payment_method' => $request->payment_method ?? $request->advance_payment_method ?? 1,
                'type' => 'supplier payments',
                'payment_status' => 1, // مكتمل
                'employee_id' => Auth::id(),
                'treasury_id' => $treasury_id, // ✅ إضافة ID الخزينة المستخدمة
                'notes' => $paymentNotes . ' لفاتورة المشتريات رقم ' . $purchaseInvoice->code . ' من خزينة ' . $mainTreasuryAccount->name,
            ]);
        }

        // ** القيود المحاسبية المُصححة **
        $this->createAccountingEntriesFixed($purchaseInvoice, $total_with_tax, $total_tax + $shipping_tax, $paid_amount, $mainTreasuryAccount);

        DB::commit(); // تأكيد التغييرات

        // ✅ إنشاء إشعار للنظام
        $activeSettings = [];
        if ($autoPaidSetting) $activeSettings[] = 'دفع تلقائي';
        if ($autoReceivedSetting) $activeSettings[] = 'استلام تلقائي';
        if ($autoPaymentSetting) $activeSettings[] = 'دفع حسب رصيد المورد';
        if ($updatePricesSetting) $activeSettings[] = 'تحديث أسعار';

        notifications::create([
            'type' => 'purchase_creation',
            'title' => $user->name . ' أنشأ فاتورة شراء جديدة',
            'description' => 'تم إنشاء فاتورة الشراء رقم ' . $purchaseInvoice->code . ' للمورد ' . ($supplier->trade_name ?? '') . ' بقيمة ' . number_format($purchaseInvoice->grand_total, 2) . ' ر.س من خزينة ' . $mainTreasuryAccount->name . ($paid_amount > 0 ? ' - تم سحب ' . number_format($paid_amount, 2) . ' ر.س' : '') . ' - حالة الدفع: ' . $this->getPaymentStatusText($payment_status) . ' - حالة الاستلام: ' . $this->getReceivingStatusText($receiving_status) . (!empty($activeSettings) ? ' (الإعدادات المفعلة: ' . implode(', ', $activeSettings) . ')' : ''),
        ]);

        // *** تحسين رسائل النجاح ***
        $message = $this->generateEnhancedSuccessMessage($payment_status, $receiving_status, $warehousePermit->number, $paid_amount, $due_value, $mainTreasuryAccount->name, [
            'auto_paid' => $autoPaidSetting,
            'auto_received' => $autoReceivedSetting,
            'auto_payment' => $autoPaymentSetting,
            'update_prices' => $updatePricesSetting
        ]);

        return redirect()
            ->route('invoicePurchases.show', $purchaseInvoice->id)
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollback(); // تراجع عن التغييرات في حالة حدوث خطأ
        Log::error('خطأ في إنشاء فاتورة المشتريات والإذن المخزني: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'عذراً، حدث خطأ أثناء حفظ فاتورة المشتريات والإذن المخزني: ' . $e->getMessage());
    }
}

/**
 * دالة مساعدة لتحويل حالة الاستلام إلى نص عربي
 */
private function getReceivingStatusText($status)
{
    switch ($status) {
        case 'received':
            return 'مستلمة';
        case 'pending':
            return 'في الانتظار';
        case 'partial':
            return 'مستلمة جزئياً';
        default:
            return 'غير محددة';
    }
}

/**
 * دالة مساعدة محدثة لتوليد رسالة النجاح بناءً على جميع الإعدادات
 */
private function generateEnhancedSuccessMessage($payment_status, $receiving_status, $warehouse_number, $paid_amount, $due_value, $treasury_name, $settings = [])
{
    $payment_text = $this->getPaymentStatusText($payment_status);
    $receiving_text = $this->getReceivingStatusText($receiving_status);

    $message = 'تم إنشاء فاتورة المشتريات والإذن المخزني رقم ' . $warehouse_number . ' بنجاح من خزينة ' . $treasury_name;

    // إضافة معلومات الإعدادات المفعلة
    $activeSettings = [];
    if ($settings['auto_paid']) $activeSettings[] = 'دفع تلقائي';
    if ($settings['auto_received']) $activeSettings[] = 'استلام تلقائي';
    if ($settings['auto_payment']) $activeSettings[] = 'دفع حسب رصيد المورد';
    if ($settings['update_prices']) $activeSettings[] = 'تحديث أسعار';

    if (!empty($activeSettings)) {
        $message .= ' (' . implode(', ', $activeSettings) . ')';
    }

    switch ($payment_status) {
        case 'paid':
            $message .= ' - تم تسجيل الدفع الكامل';
            if ($settings['auto_paid'] || $settings['auto_payment']) {
                $message .= ' تلقائياً';
            }
            break;
        case 'partially_paid':
            $message .= ' - تم تسجيل دفعة مقدمة بمبلغ ' . number_format($paid_amount) . ' ريال. المتبقي: ' . number_format($due_value) . ' ريال';
            break;
        case 'unpaid':
            $message .= ' - بدون دفعة مقدمة';
            break;
    }

    // إضافة معلومات الاستلام
    switch ($receiving_status) {
        case 'received':
            $message .= ' - تم تسجيل الاستلام';
            if ($settings['auto_received']) {
                $message .= ' تلقائياً';
            }
            break;
        case 'pending':
            $message .= ' - في انتظار الاستلام';
            break;
        case 'partial':
            $message .= ' - استلام جزئي';
            break;
    }

    $message .= ' | حالة الدفع: ' . $payment_text . ' | حالة الاستلام: ' . $receiving_text;

    return $message;
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




// private function generateSuccessMessage($payment_status,  $warehouse_number, $paid_amount, $due_value, $treasury_name)
// {
//     $payment_text = $this->getPaymentStatusText($payment_status);


//     $message = 'تم إنشاء فاتورة المشتريات والإذن المخزني رقم ' . $warehouse_number . ' بنجاح من خزينة ' . $treasury_name;

//     switch ($payment_status) {
//         case 'paid':
//             $message .= ' - تم تسجيل الدفع الكامل';
//             break;
//         case 'partially_paid':
//             $message .= ' - تم تسجيل دفعة مقدمة بمبلغ ' . number_format($paid_amount) . ' ريال. المتبقي: ' . number_format($due_value) . ' ريال';
//             break;
//         case 'unpaid':
//             $message .= ' - بدون دفعة مقدمة';
//             break;
//     }

//     $message .= ' | حالة الدفع: ' . $payment_text  ;

//     return $message;
// }

private function createWarehousePermit($purchaseInvoice, $request)
{
    // إنشاء رقم الإذن المخزني
    $lastPermit = WarehousePermits::orderBy('id', 'desc')->first();
    $nextPermitNumber = $lastPermit ? intval($lastPermit->number) + 1 : 1;
    $permitNumber = str_pad($nextPermitNumber, 6, '0', STR_PAD_LEFT);

    // تحديد المستودع الرئيسي إذا لم يتم تحديده
    $storeHouseId = $request->store_house_id ?? null;
    if (!$storeHouseId) {
        $mainStoreHouse = StoreHouse::where('major', true)->first();
        $storeHouseId = $mainStoreHouse ? $mainStoreHouse->id : null;
    }

    // إنشاء الإذن المخزني
    $warehousePermit = WarehousePermits::create([
        'permission_source_id' => 1, // نوع الإذن: إدخال
        'permission_date' => $request->date ?? now(),
        'sub_account' => $purchaseInvoice->supplier_id, // المورد
        'number' => $permitNumber,
        'store_houses_id' => $storeHouseId,
        'from_store_houses_id' => null, // لا يوجد مستودع مصدر للمشتريات
        'to_store_houses_id' => $storeHouseId, // المستودع المستهدف
        'grand_total' => $purchaseInvoice->grand_total,
        'details' => 'إذن إدخال بضاعة لفاتورة شراء رقم: ' . $purchaseInvoice->code,
        'attachments' => null,
        'created_by' => Auth::id(),
        'status' => 'pending', // بانتظار الموافقة
        'reference_type' => 'purchase_invoice', // نوع المرجع
        'reference_id' => $purchaseInvoice->id, // معرف الفاتورة
    ]);

    return $warehousePermit;
}

// ✅ دالة مُصححة للقيود المحاسبية (نفس منطق التحويل)
// ✅ دالة مُصححة للقيود المحاسبية - قيد للفاتورة + قيد للدفع (عند الدفع)
private function createAccountingEntriesFixed($purchaseInvoice, $total_with_tax, $tax_total, $paid_amount, $mainTreasuryAccount)
{
    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

    // حساب المورد
    $supplierAccount = Account::where('supplier_id', $purchaseInvoice->supplier_id)->first();
    if (!$supplierAccount) {
        $supplier = Supplier::find($purchaseInvoice->supplier_id);
        $supplierAccount = Account::create([
            'name' => 'حساب المورد - ' . ($supplier->trade_name ?? 'مورد غير معروف'),
            'supplier_id' => $purchaseInvoice->supplier_id,
            'account_type' => 'supplier',
            'balance' => 0,
            'status' => 1,
        ]);
    }

    // إنشاء الحسابات المطلوبة إذا لم تكن موجودة
    $mainStore = Account::where('name', 'المشتريات')->first();
    if (!$mainStore) {
        $mainStore = Account::create([
            'name' => 'المشتريات',
            'account_type' => 'expense',
            'balance' => 0,
            'status' => 1,
        ]);
    }

    $taxAccount = null;
    if ($purchaseInvoice->total_tax > 0) {
        $taxAccount = Account::where('name', 'القيمة المضافة')->first();
        if (!$taxAccount) {
            $taxAccount = Account::create([
                'name' => 'القيمة المضافة',
                'account_type' => 'asset',
                'balance' => 0,
                'status' => 1,
            ]);
        }
    }

    // ✅ القيد الأول: قيد الفاتورة (يتم دائماً سواء كان هناك دفع أم لا)
    $journalEntry1 = JournalEntry::create([
        'reference_number' => $purchaseInvoice->code,
        'purchase_invoice_id' => $purchaseInvoice->id,
        'date' => now(),
        'description' => 'فاتورة شراء # ' . $purchaseInvoice->code,
        'status' => 1,
        'currency' => 'SAR',
        'client_id' => $purchaseInvoice->supplier_id,
        'created_by_employee' => Auth::id(),
    ]);

    // 1. حساب المشتريات (مدين) - بـ subtotal (المبلغ بدون ضريبة)
    $subtotal = $purchaseInvoice->grand_total - $purchaseInvoice->total_tax;
    JournalEntryDetail::create([
        'journal_entry_id' => $journalEntry1->id,
        'account_id' => $mainStore->id,
        'description' => 'فاتورة شراء # ' . $purchaseInvoice->code,
        'debit' => $subtotal,
        'credit' => 0,
        'is_debit' => true,
    ]);

    // 2. حساب VAT المدفوعة (مدين) - إذا كان هناك ضريبة
    if ($purchaseInvoice->total_tax > 0 && $taxAccount) {
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry1->id,
            'account_id' => $taxAccount->id,
            'description' => 'VAT المدفوعة فاتورة شراء # ' . $purchaseInvoice->code,
            'debit' => $purchaseInvoice->total_tax,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // تحديث رصيد ضريبة القيمة المضافة
        $taxAccount->balance += $purchaseInvoice->total_tax;
        $taxAccount->save();
    }

    // 3. حساب المورد/المؤسسة (دائن) - بالمبلغ الإجمالي
    JournalEntryDetail::create([
        'journal_entry_id' => $journalEntry1->id,
        'account_id' => $supplierAccount->id,
        'description' => 'فاتورة شراء # ' . $purchaseInvoice->code,
        'debit' => 0,
        'credit' => $purchaseInvoice->grand_total,
        'is_debit' => false,
    ]);

    // تحديث رصيد المشتريات
    $mainStore->balance += $subtotal;
    $mainStore->save();

    // تحديث رصيد المورد (زيادة الدين)
    $supplierAccount->balance += $purchaseInvoice->grand_total;
    $supplierAccount->save();

    // ✅ القيد الثاني: قيد الدفع (فقط إذا كان هناك دفع)
    if ($paid_amount > 0) {
        $journalEntry2 = JournalEntry::create([
            'reference_number' => $purchaseInvoice->code . '_دفع',
            'date' => now(),
            'description' => 'دفع للمورد # ' . $purchaseInvoice->code . ' - ' . $this->getPaymentStatusText($purchaseInvoice->payment_status),
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $purchaseInvoice->supplier_id,
            'created_by_employee' => Auth::id(),
        ]);

        // حساب المورد/المؤسسة (مدين) - تسديد الدين
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry2->id,
            'account_id' => $supplierAccount->id,
            'description' => 'دفع للمورد # ' . $purchaseInvoice->code,
            'debit' => $paid_amount,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // حساب الخزينة الأساسية (دائن) - خروج نقد
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry2->id,
            'account_id' => $mainTreasuryAccount->id,
            'description' => 'دفع للمورد # ' . $purchaseInvoice->code,
            'debit' => 0,
            'credit' => $paid_amount,
            'is_debit' => false,
        ]);

        // تحديث رصيد الخزينة (سحب المبلغ)
        $mainTreasuryAccount->balance -= $paid_amount;
        $mainTreasuryAccount->save();

        // تحديث رصيد المورد (تقليل الدين)
        $supplierAccount->balance -= $paid_amount;
        $supplierAccount->save();
    }

    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
}


public function edit($id)
    {
        $invoice = PurchaseInvoice::findOrFail($id);
        $suppliers = Supplier::all();
        $items = Product::all();
        $accounts = Account::all();
        $taxs = TaxSitting::all();

        return view('purchases::purchases.invoices_purchase.edit', compact('invoice', 'taxs', 'suppliers', 'items', 'accounts'));
    }

public function update(Request $request, $id)
{
    try {
        DB::beginTransaction(); // بدء المعاملة

        $purchaseInvoice = PurchaseInvoice::findOrFail($id);

        // ** حفظ القيم القديمة للمقارنة **
        $old_grand_total = $purchaseInvoice->grand_total;
        $old_paid_amount = $purchaseInvoice->advance_payment;
        $old_total_tax = $purchaseInvoice->total_tax;

        // ** التحقق من وجود المورد **
        if (!$request->supplier_id) {
            throw new \Exception('يجب تحديد المورد');
        }

        $supplier = Supplier::find($request->supplier_id);
        if (!$supplier) {
            throw new \Exception('المورد المحدد غير موجود');
        }

        // ✅ جلب جميع إعدادات فواتير الشراء (تصحيح اسم المفتاح)
        $autoPaidSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('default_paid_invoices');
        $autoReceivedSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('default_received_invoices');
        $autoPaymentSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('auto_payment');
        $updatePricesSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('update_product_prices');
        $manualInvoiceStatusSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('manual_invoice_status');
        $enableSettlementSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('enable_settlement');
        $totalDiscountsSetting = \App\Models\PurchaseInvoiceSetting::isSettingActive('total_discounts');

        // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
        $total_amount = 0; // إجمالي المبلغ قبل الخصومات
        $total_discount = 0; // إجمالي الخصومات على البنود
        $items_data = []; // تجميع بيانات البنود

        // ** معالجة البنود (items) **
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
                    'purchase_invoice_id' => $purchaseInvoice->id,
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
                    'store_house_id' => $item['store_house_id'] ?? null,
                ];

                // ✅ تحديث أسعار المنتجات إذا كان الإعداد مفعل
                if ($updatePricesSetting) {
                    $product->update([
                        'purchase_price' => $unit_price,
                        'price' => $unit_price, // أو يمكن حساب سعر البيع بناءً على هامش ربح
                        'updated_at' => now()
                    ]);
                }
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

        // ✅ إجمالي الخصومات مع مراعاة الإعداد
        $final_total_discount = $totalDiscountsSetting ? $total_discount + $invoice_discount : $invoice_discount;

        // حساب المبلغ بعد الخصم
        $amount_after_discount = $total_amount - $final_total_discount;

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

        // ** إضافة تكلفة الشحن **
        $shipping_cost = floatval($request->shipping_cost ?? 0);

        // ** حساب ضريبة التوصيل **
        $shipping_tax = 0;
        if ($request->tax_type == 1) {
            $shipping_tax = $shipping_cost * 0.15; // ضريبة التوصيل 15%
        }

        // ** الحساب النهائي للمجموع الكلي **
        $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost + $shipping_tax;

        // *** تحديد حالات الدفع والاستلام مع جميع الإعدادات ***
        $paid_amount = 0;
        $advance_payment = 0; // الدفعة المقدمة المنفصلة
        $payment_status = 'unpaid'; // الحالة الافتراضية للدفع
        $receiving_status = 'not_received'; // الحالة الافتراضية للاستلام

        // ✅ حالة الاستلام بناءً على الإعداد
        if ($autoReceivedSetting || ($request->has('is_received') && $request->is_received == '1')) {
            $receiving_status = 'received'; // مستلمة
        } else {
            // الاحتفاظ بالحالة السابقة
            $receiving_status = $purchaseInvoice->receiving_status ?? 'pending';
        }

        // ✅ حالة الدفع بناءً على الإعدادات المختلفة
        $shouldAutoPay = false;

        // 1. إعداد الدفع الكامل التلقائي
        if ($autoPaidSetting) {
            $shouldAutoPay = true;
        }
        // 2. الدفع التلقائي إذا كان لدى المورد رصيد صالح
        elseif ($autoPaymentSetting && $supplier && $supplier->account && $supplier->account->balance > 0) {
            $shouldAutoPay = true;
        }
        // 3. دفع يدوي من المستخدم
        elseif ($request->has('is_paid') && $request->is_paid == '1') {
            $shouldAutoPay = true;
        }

        if ($shouldAutoPay) {
            $paid_amount = $total_with_tax; // دفع كامل
            $advance_payment = $total_with_tax; // نفس المبلغ للدفعة المقدمة
            $payment_status = 'paid'; // مدفوعة بالكامل
        }
        // أو إذا كان هناك دفعة مقدمة من حقل advance_payment (فقط إذا لم يكن الدفع التلقائي مفعل)
        elseif (!$shouldAutoPay && $request->has('advance_payment') && floatval($request->advance_payment) > 0) {
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
        // أو إذا كان هناك مبلغ مدفوع من الحقل العام paid_amount (فقط إذا لم يكن الدفع التلقائي مفعل)
        elseif (!$shouldAutoPay && $request->has('paid_amount') && floatval($request->paid_amount) > 0) {
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
        } else {
            // الاحتفاظ بالدفعة المقدمة القديمة إذا لم يتم تغييرها ولم يكن الدفع التلقائي مفعل
            if (!$shouldAutoPay) {
                $advance_payment = $old_paid_amount;
                $paid_amount = $old_paid_amount;

                if ($paid_amount >= $total_with_tax) {
                    $payment_status = 'paid';
                } elseif ($paid_amount > 0) {
                    $payment_status = 'partially_paid';
                } else {
                    $payment_status = 'unpaid';
                }
            } else {
                // إذا كان الدفع التلقائي مفعل
                $paid_amount = $total_with_tax;
                $advance_payment = $total_with_tax;
                $payment_status = 'paid';
            }
        }

        $due_value = $total_with_tax - $paid_amount;
        $payment_difference = $paid_amount - $old_paid_amount; // الفرق في الدفع

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

        // ✅ التحقق من وجود رصيد كافي للفرق الإضافي
        if ($payment_difference > 0 && $mainTreasuryAccount->balance < $payment_difference) {
            throw new \Exception('رصيد الخزينة غير كافي للفرق الإضافي. الرصيد الحالي: ' . number_format($mainTreasuryAccount->balance, 2) . ' والمطلوب: ' . number_format($payment_difference, 2));
        }

        // ✅ تحديد حالة الفاتورة بناءً على الإعدادات
        $invoice_status = $purchaseInvoice->status ?? 'pending'; // الاحتفاظ بالحالة السابقة
        if (!$manualInvoiceStatusSetting) {
            // إذا لم يكن الإعداد اليدوي مفعل، تحديد الحالة تلقائياً
            if ($payment_status === 'paid' && $receiving_status === 'received') {
                $invoice_status = 'completed';
            } elseif ($payment_status === 'paid') {
                $invoice_status = 'paid_pending_delivery';
            } elseif ($receiving_status === 'received') {
                $invoice_status = 'delivered_pending_payment';
            } else {
                $invoice_status = 'pending';
            }
        }

        // ** تحديث الفاتورة في قاعدة البيانات **
        $purchaseInvoice->update([
            'supplier_id' => $request->supplier_id,
            'date' => $request->date,
            'terms' => $request->terms ?? 0,
            'notes' => ($request->notes ?? '') .
                      "\nتم التحديث بتاريخ: " . now()->format('Y-m-d H:i:s') .
                      " - الخزينة المستخدمة: " . $mainTreasuryAccount->name .
                      ($autoPaidSetting ? "\n(دفع تلقائي بالكامل مفعل)" : "") .
                      ($autoReceivedSetting ? "\n(استلام تلقائي مفعل)" : "") .
                      ($autoPaymentSetting ? "\n(دفع تلقائي حسب رصيد المورد مفعل)" : "") .
                      ($updatePricesSetting ? "\n(تحديث أسعار المنتجات مفعل)" : ""),
            'payment_status' => $payment_status, // ✅ حالة الدفع الجديدة (enum)
            'receiving_status' => $receiving_status, // ✅ حالة الاستلام
            'status' => $invoice_status, // ✅ حالة الفاتورة العامة
            'account_id' => $request->account_id,
            'discount_amount' => $invoice_discount,
            'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
            'payment_type' => $request->payment_type ?? 1,
            'shipping_cost' => $shipping_cost,
            'tax_type' => $request->tax_type ?? 1,
            'payment_method' => $request->payment_method ?? $request->advance_payment_method,
            'reference_number' => $request->reference_number ?? $request->advance_reference_number,
            'received_date' => $autoReceivedSetting ? now() : $request->received_date,
            'is_paid' => $payment_status === 'paid', // للتوافق مع النظام القديم
            'is_received' => $receiving_status === 'received', // للتوافق مع النظام القديم
            'subtotal' => $total_amount,
            'total_discount' => $final_total_discount,
            'total_tax' => $total_tax + $shipping_tax,
            'grand_total' => $total_with_tax,
            'due_value' => $due_value,
            'advance_payment' => $advance_payment, // ✅ استخدام المتغير المنفصل للدفعة المقدمة
        ]);

        // ** تحديث الإذن المخزني إذا وجد **
        $warehousePermit = WarehousePermits::where('reference_type', 'purchase_invoice')
                                          ->where('reference_id', $purchaseInvoice->id)
                                          ->first();

        if ($warehousePermit) {
            $warehousePermit->update([
                'grand_total' => $purchaseInvoice->grand_total,
                'details' => 'إذن إدخال بضاعة محدَّث لفاتورة شراء رقم: ' . $purchaseInvoice->code,
            ]);

            // حذف منتجات الإذن المخزني القديمة
            WarehousePermitsProducts::where('warehouse_permits_id', $warehousePermit->id)->delete();
        }

        // ** حذف البنود والضرائب القديمة **
        $purchaseInvoice->invoiceItems()->delete();
        TaxInvoice::where('invoice_id', $purchaseInvoice->id)
                  ->where('type_invoice', 'purchase')
                  ->delete();

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
                            'invoice_id' => $purchaseInvoice->id,
                            'type' => $tax->type,
                            'rate' => $tax->tax,
                            'value' => $tax_value,
                            'type_invoice' => 'purchase',
                        ]);
                    }
                }
            }
        }

        // ** إنشاء سجلات البنود المُحدَّثة **
        foreach ($items_data as $item) {
            $invoiceItem = InvoiceItem::create($item);

            // إضافة منتج جديد للإذن المخزني إذا وجد
            if ($warehousePermit) {
                WarehousePermitsProducts::create([
                    'warehouse_permits_id' => $warehousePermit->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                    'notes' => $item['description'],
                    'store_house_id' => $item['store_house_id'],
                ]);
            }

            $product = Product::find($item['product_id']);

            // تسجيل اشعار نظام جديد لكل منتج
            ModelsLog::create([
                'type' => 'purchase_invoice',
                'type_id' => $purchaseInvoice->id,
                'type_log' => 'log',
                'icon' => 'update',
                'description' => sprintf(
                    'تم تحديث فاتورة شراء رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للمورد **%s** وإذن مخزني رقم **%s** من خزينة **%s** - حالة الدفع: **%s** - حالة الاستلام: **%s**%s%s%s',
                    $purchaseInvoice->code ?? '',
                    $product->name ?? '',
                    $item['quantity'] ?? '',
                    $item['unit_price'] ?? '',
                    $supplier->trade_name ?? '',
                    $warehousePermit->number ?? '',
                    $mainTreasuryAccount->name ?? '',
                    $this->getPaymentStatusText($payment_status),
                    $this->getReceivingStatusText($receiving_status),
                    $autoPaidSetting ? ' (دفع تلقائي)' : '',
                    $autoReceivedSetting ? ' (استلام تلقائي)' : '',
                    $updatePricesSetting ? ' (تحديث أسعار)' : ''
                ),
                'created_by' => auth()->id(),
            ]);
        }

        // ** معالجة المرفقات **
        if ($request->hasFile('attachments')) {
            // حذف الملف القديم إذا وجد
            if ($purchaseInvoice->attachments) {
                $oldFile = public_path('assets/uploads/') . $purchaseInvoice->attachments;
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
            }

            $file = $request->file('attachments');
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/'), $filename);
                $purchaseInvoice->attachments = $filename;
                $purchaseInvoice->save();
            }
        }

        // *** تحديث أو إنشاء عملية الدفع ***
        if ($payment_difference != 0) {
            // البحث عن عملية دفع موجودة
            $existingPayment = PaymentsProcess::where('purchases_id', $purchaseInvoice->id)->first();

            if ($payment_difference > 0) {
                // زيادة في الدفع - إنشاء دفعة إضافية أو تحديث الموجودة
                $paymentNotes = '';
                if ($autoPaidSetting) {
                    $paymentNotes = 'دفعة محدَّثة تلقائية (إعداد الدفع التلقائي مفعل)';
                } elseif ($autoPaymentSetting) {
                    $paymentNotes = 'دفعة محدَّثة تلقائية (رصيد المورد صالح)';
                } else {
                    $paymentNotes = $payment_status === 'paid' ? 'دفعة كاملة محدَّثة' : 'دفعة مقدمة محدَّثة بمبلغ ' . number_format($paid_amount, 2);
                }

                if ($existingPayment) {
                    $existingPayment->update([
                        'amount' => $paid_amount,
                        'payment_date' => $request->date ?? now(),
                        'payment_method' => $request->payment_method ?? $request->advance_payment_method ?? $existingPayment->payment_method,
                        'treasury_id' => $treasury_id,
                        'notes' => $paymentNotes . ' لفاتورة المشتريات رقم ' . $purchaseInvoice->code . ' من خزينة ' . $mainTreasuryAccount->name,
                    ]);
                } else if ($paid_amount > 0) {
                    PaymentsProcess::create([
                        'purchases_id' => $purchaseInvoice->id,
                        'supplier_id' => $request->supplier_id,
                        'amount' => $paid_amount,
                        'payment_date' => $request->date ?? now(),
                        'payment_method' => $request->payment_method ?? $request->advance_payment_method ?? 1,
                        'type' => 'supplier payments',
                        'payment_status' => 1,
                        'employee_id' => Auth::id(),
                        'treasury_id' => $treasury_id,
                        'notes' => $paymentNotes . ' لفاتورة المشتريات رقم ' . $purchaseInvoice->code . ' من خزينة ' . $mainTreasuryAccount->name,
                    ]);
                }

                // تحديث رصيد الخزينة (سحب الفرق الإضافي فقط)
                $mainTreasuryAccount->balance -= $payment_difference;
                $mainTreasuryAccount->save();

            } elseif ($payment_difference < 0) {
                // تقليل في الدفع - إرجاع الفرق للخزينة
                if ($existingPayment) {
                    $existingPayment->update([
                        'amount' => $paid_amount,
                        'notes' => $existingPayment->notes . " - تم تعديل المبلغ وإرجاع " . number_format(abs($payment_difference), 2) . " للخزينة",
                    ]);
                }

                // إرجاع الفرق للخزينة
                $mainTreasuryAccount->balance += abs($payment_difference);
                $mainTreasuryAccount->save();
            }
        }

        // ** القيود المحاسبية المُصححة - تعديل القيود الموجودة بدلاً من حذفها **
        $this->updateAccountingEntriesFixed($purchaseInvoice, $old_grand_total, $old_paid_amount, $old_total_tax,
                                           $total_with_tax, $paid_amount, $total_tax + $shipping_tax, $mainTreasuryAccount);

        DB::commit(); // تأكيد التغييرات

        // ✅ إنشاء إشعار للنظام
        $activeSettings = [];
        if ($autoPaidSetting) $activeSettings[] = 'دفع تلقائي';
        if ($autoReceivedSetting) $activeSettings[] = 'استلام تلقائي';
        if ($autoPaymentSetting) $activeSettings[] = 'دفع حسب رصيد المورد';
        if ($updatePricesSetting) $activeSettings[] = 'تحديث أسعار';

        notifications::create([
            'type' => 'purchase_update',
            'title' => $user->name . ' حدَّث فاتورة شراء',
            'description' => 'تم تحديث فاتورة الشراء رقم ' . $purchaseInvoice->code . ' للمورد ' . ($supplier->trade_name ?? '') . ' بقيمة ' . number_format($purchaseInvoice->grand_total, 2) . ' ر.س من خزينة ' . $mainTreasuryAccount->name . ($payment_difference > 0 ? ' - تم سحب إضافي ' . number_format($payment_difference, 2) . ' ر.س' : ($payment_difference < 0 ? ' - تم إرجاع ' . number_format(abs($payment_difference), 2) . ' ر.س' : '')) . ' - حالة الدفع: ' . $this->getPaymentStatusText($payment_status) . ' - حالة الاستلام: ' . $this->getReceivingStatusText($receiving_status) . (!empty($activeSettings) ? ' (الإعدادات المفعلة: ' . implode(', ', $activeSettings) . ')' : ''),
        ]);

        // *** تحسين رسائل النجاح ***
        $message = $this->generateEnhancedUpdateSuccessMessage($payment_status, $receiving_status, $warehousePermit->number ?? '',
                                                             $payment_difference, $due_value, $mainTreasuryAccount->name, [
            'auto_paid' => $autoPaidSetting,
            'auto_received' => $autoReceivedSetting,
            'auto_payment' => $autoPaymentSetting,
            'update_prices' => $updatePricesSetting
        ]);

        return redirect()
            ->route('invoicePurchases.show', $purchaseInvoice->id)
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollback(); // تراجع عن التغييرات في حالة حدوث خطأ
        Log::error('خطأ في تحديث فاتورة المشتريات: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'عذراً، حدث خطأ أثناء تحديث فاتورة المشتريات: ' . $e->getMessage());
    }
}

/**
 * دالة مساعدة محدثة لتوليد رسالة النجاح للتحديث بناءً على جميع الإعدادات
 */
private function generateEnhancedUpdateSuccessMessage($payment_status, $receiving_status, $warehouse_number, $payment_difference, $due_value, $treasury_name, $settings = [])
{
    $payment_text = $this->getPaymentStatusText($payment_status);
    $receiving_text = $this->getReceivingStatusText($receiving_status);

    $message = 'تم تحديث فاتورة المشتريات والإذن المخزني';
    if ($warehouse_number) {
        $message .= ' رقم ' . $warehouse_number;
    }
    $message .= ' بنجاح من خزينة ' . $treasury_name;

    // إضافة معلومات الإعدادات المفعلة
    $activeSettings = [];
    if ($settings['auto_paid']) $activeSettings[] = 'دفع تلقائي';
    if ($settings['auto_received']) $activeSettings[] = 'استلام تلقائي';
    if ($settings['auto_payment']) $activeSettings[] = 'دفع حسب رصيد المورد';
    if ($settings['update_prices']) $activeSettings[] = 'تحديث أسعار';

    if (!empty($activeSettings)) {
        $message .= ' (' . implode(', ', $activeSettings) . ')';
    }

    if ($payment_difference > 0) {
        $message .= ' - تم سحب مبلغ إضافي ' . number_format($payment_difference, 2) . ' ريال';
        if ($settings['auto_paid'] || $settings['auto_payment']) {
            $message .= ' (تلقائياً)';
        }
    } elseif ($payment_difference < 0) {
        $message .= ' - تم إرجاع مبلغ ' . number_format(abs($payment_difference), 2) . ' ريال للخزينة';
    }

    switch ($payment_status) {
        case 'paid':
            $message .= ' - الفاتورة مدفوعة بالكامل';
            if ($settings['auto_paid'] || $settings['auto_payment']) {
                $message .= ' (تلقائياً)';
            }
            break;
        case 'partially_paid':
            $message .= ' - المتبقي: ' . number_format($due_value) . ' ريال';
            break;
        case 'unpaid':
            $message .= ' - بدون دفعة';
            break;
    }

    // إضافة معلومات الاستلام
    switch ($receiving_status) {
        case 'received':
            $message .= ' - مستلمة';
            if ($settings['auto_received']) {
                $message .= ' (تلقائياً)';
            }
            break;
        case 'pending':
            $message .= ' - في انتظار الاستلام';
            break;
        case 'partial':
            $message .= ' - استلام جزئي';
            break;
    }

    $message .= ' | حالة الدفع: ' . $payment_text . ' | حالة الاستلام: ' . $receiving_text;

    return $message;
}

// ✅ دالة محدثة للقيود المحاسبية - تحديث القيود الموجودة بدلاً من حذفها
private function updateAccountingEntriesFixed($purchaseInvoice, $old_grand_total, $old_paid_amount, $old_total_tax,
                                             $new_grand_total, $new_paid_amount, $new_total_tax, $mainTreasuryAccount)
{
    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

    // حساب المورد
    $supplierAccount = Account::where('supplier_id', $purchaseInvoice->supplier_id)->first();
    if (!$supplierAccount) {
        $supplier = Supplier::find($purchaseInvoice->supplier_id);
        $supplierAccount = Account::create([
            'name' => 'حساب المورد - ' . ($supplier->trade_name ?? 'مورد غير معروف'),
            'supplier_id' => $purchaseInvoice->supplier_id,
            'account_type' => 'supplier',
            'balance' => 0,
            'status' => 1,
        ]);
    }

    // إنشاء الحسابات المطلوبة إذا لم تكن موجودة
    $mainStore = Account::where('name', 'المشتريات')->first();
    if (!$mainStore) {
        $mainStore = Account::create([
            'name' => 'المشتريات',
            'account_type' => 'expense',
            'balance' => 0,
            'status' => 1,
        ]);
    }

    $taxAccount = null;
    if ($new_total_tax > 0) {
        $taxAccount = Account::where('name', 'القيمة المضافة')->first();
        if (!$taxAccount) {
            $taxAccount = Account::create([
                'name' => 'القيمة المضافة',
                'account_type' => 'asset',
                'balance' => 0,
                'status' => 1,
            ]);
        }
    }

    // ** تحديث الأرصدة بناءً على الفروق **

    // 1. تحديث حساب المشتريات
    $old_subtotal = $old_grand_total - $old_total_tax;
    $new_subtotal = $new_grand_total - $new_total_tax;
    $subtotal_difference = $new_subtotal - $old_subtotal;

    if ($subtotal_difference != 0) {
        $mainStore->balance += $subtotal_difference;
        $mainStore->save();
    }

    // 2. تحديث حساب ضريبة القيمة المضافة
    if ($taxAccount && ($new_total_tax - $old_total_tax) != 0) {
        $tax_difference = $new_total_tax - $old_total_tax;
        $taxAccount->balance += $tax_difference;
        $taxAccount->save();
    }

    // 3. تحديث حساب المورد
    $total_difference = $new_grand_total - $old_grand_total;
    if ($total_difference != 0) {
        $supplierAccount->balance += $total_difference;
        $supplierAccount->save();
    }

    // 4. تحديث حساب الخزينة بناءً على فرق الدفع
    $payment_difference = $new_paid_amount - $old_paid_amount;
    if ($payment_difference != 0) {
        // تحديث رصيد المورد (عكس التأثير على الدين)
        $supplierAccount->balance -= $payment_difference;
        $supplierAccount->save();
    }

    // ** تحديث القيود المحاسبية الموجودة بدلاً من حذفها **

    // البحث عن قيد الفاتورة الرئيسي
    $invoiceJournalEntry = JournalEntry::where('reference_number', $purchaseInvoice->code)
                                      ->where('purchase_invoice_id', $purchaseInvoice->id)
                                      ->first();

    if ($invoiceJournalEntry) {
        // تحديث وصف القيد
        $invoiceJournalEntry->update([
            'description' => 'فاتورة شراء محدَّثة # ' . $purchaseInvoice->code,
            'client_id' => $purchaseInvoice->supplier_id,
        ]);

        // تحديث تفاصيل القيد
        $journalDetails = JournalEntryDetail::where('journal_entry_id', $invoiceJournalEntry->id)->get();

        foreach ($journalDetails as $detail) {
            if ($detail->account_id == $mainStore->id) {
                // تحديث قيمة حساب المشتريات
                $detail->update([
                    'debit' => $new_subtotal,
                    'description' => 'فاتورة شراء محدَّثة # ' . $purchaseInvoice->code,
                ]);
            } elseif ($taxAccount && $detail->account_id == $taxAccount->id) {
                // تحديث قيمة ضريبة القيمة المضافة
                if ($new_total_tax > 0) {
                    $detail->update([
                        'debit' => $new_total_tax,
                        'description' => 'VAT المدفوعة فاتورة شراء محدَّثة # ' . $purchaseInvoice->code,
                    ]);
                } else {
                    // حذف قيد الضريبة إذا أصبحت صفر
                    $detail->delete();
                }
            } elseif ($detail->account_id == $supplierAccount->id) {
                // تحديث قيمة حساب المورد
                $detail->update([
                    'credit' => $new_grand_total,
                    'description' => 'فاتورة شراء محدَّثة # ' . $purchaseInvoice->code,
                ]);
            }
        }

        // إضافة قيد ضريبة جديد إذا لم يكن موجوداً ولكن الضريبة الجديدة > 0
        if ($taxAccount && $new_total_tax > 0 && $old_total_tax == 0) {
            JournalEntryDetail::create([
                'journal_entry_id' => $invoiceJournalEntry->id,
                'account_id' => $taxAccount->id,
                'description' => 'VAT المدفوعة فاتورة شراء محدَّثة # ' . $purchaseInvoice->code,
                'debit' => $new_total_tax,
                'credit' => 0,
                'is_debit' => true,
            ]);
        }
    } else {
        // إنشاء قيد جديد إذا لم يكن موجوداً (حالة استثنائية)
        $this->createAccountingEntriesFixed($purchaseInvoice, $new_grand_total, $new_total_tax, $new_paid_amount, $mainTreasuryAccount);
    }

    // ** تحديث قيد الدفع إذا وجد **
    if ($payment_difference != 0) {
        $paymentJournalEntry = JournalEntry::where('reference_number', $purchaseInvoice->code . '_دفع')
                                          ->first();

        if ($paymentJournalEntry && $new_paid_amount > 0) {
            // تحديث قيد الدفع الموجود
            $paymentJournalEntry->update([
                'description' => 'دفع محدَّث للمورد # ' . $purchaseInvoice->code . ' - ' . $this->getPaymentStatusText($purchaseInvoice->payment_status),
                'client_id' => $purchaseInvoice->supplier_id,
            ]);

            $paymentDetails = JournalEntryDetail::where('journal_entry_id', $paymentJournalEntry->id)->get();

            foreach ($paymentDetails as $detail) {
                if ($detail->account_id == $supplierAccount->id) {
                    $detail->update([
                        'debit' => $new_paid_amount,
                        'description' => 'دفع محدَّث للمورد # ' . $purchaseInvoice->code,
                    ]);
                } elseif ($detail->account_id == $mainTreasuryAccount->id) {
                    $detail->update([
                        'credit' => $new_paid_amount,
                        'description' => 'دفع محدَّث للمورد # ' . $purchaseInvoice->code,
                    ]);
                }
            }
        } elseif ($new_paid_amount > 0 && !$paymentJournalEntry) {
            // إنشاء قيد دفع جديد
            $newPaymentEntry = JournalEntry::create([
                'reference_number' => $purchaseInvoice->code . '_دفع',
                'date' => now(),
                'description' => 'دفع للمورد # ' . $purchaseInvoice->code . ' - ' . $this->getPaymentStatusText($purchaseInvoice->payment_status),
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $purchaseInvoice->supplier_id,
                'created_by_employee' => Auth::id(),
            ]);

            // حساب المورد (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $newPaymentEntry->id,
                'account_id' => $supplierAccount->id,
                'description' => 'دفع للمورد # ' . $purchaseInvoice->code,
                'debit' => $new_paid_amount,
                'credit' => 0,
                'is_debit' => true,
            ]);

            // حساب الخزينة (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $newPaymentEntry->id,
                'account_id' => $mainTreasuryAccount->id,
                'description' => 'دفع للمورد # ' . $purchaseInvoice->code,
                'debit' => 0,
                'credit' => $new_paid_amount,
                'is_debit' => false,
            ]);
        } elseif ($new_paid_amount == 0 && $paymentJournalEntry) {
            // حذف قيد الدفع إذا أصبح المبلغ المدفوع صفر
            JournalEntryDetail::where('journal_entry_id', $paymentJournalEntry->id)->delete();
            $paymentJournalEntry->delete();
        }
    }

    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
}

// ✅ دالة مُصححة للقيود المحاسبية (نفس منطق دالة الـ store)

    public function exportPDF($id)
    {
        try {
            $purchaseOrder = PurchaseInvoice::with(['supplier', 'account', 'items.product', 'creator'])->findOrFail($id);

            $pdf = Pdf::loadView('purchases.view_purchase_price.pdf', compact('purchaseOrder'));

            return $pdf->download('عرض- فاتورة شراء -' . $purchaseOrder->code . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تصدير PDF: ' . $e->getMessage());
        }
    }

    public function convertToCreditMemo($id)
    {
        $purchaseOrder = PurchaseInvoice::findOrFail($id);
        $purchaseOrder->update([
            'type' => 3,
        ]);
        return redirect()->route('invoicePurchases.index')->with('success', 'تم تحويل فاتورة المشتريات الى مرتجع مدفوعة بنجاح');
    }
    // public function Show($id)
    // {
    //     $purchaseInvoice = PurchaseInvoice::with([
    //         'invoiceItems' => function ($query) {
    //             $query->where('purchase_invoice_id');
    //         },
    //         'invoiceItems.product',
    //         'supplier',
    //     ])->findOrFail($id);
    //     $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'purchase')->get();
    //     return view('purchases::purchases.invoices_purchase.show', compact('purchaseInvoice', 'TaxsInvoice'));
    // }

       public function show($id)
    {
        $purchaseInvoice = PurchaseInvoice::with(['supplier', 'items.product', 'payments', 'user'])
            ->findOrFail($id);
            $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'purchase')->get();

        // جلب سجل الأنشطة
                $logs = ModelsLog::where('type', 'purchase_invoice')
            ->where('type_id', $id)
            ->whereHas('purchase_invoice') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('purchases::purchases.invoices_purchase.show', compact('purchaseInvoice', 'logs','TaxsInvoice'));
    }

    // تعليم الفاتورة كمدفوعة


    // إلغاء الدفع (العودة لغير مدفوعة)
    public function markAsUnpaid($id)
    {
        try {
            $purchaseInvoice = PurchaseInvoice::findOrFail($id);

            $purchaseInvoice->update(['status' => 0]); // 0 = غير مدفوعة

            // تسجيل النشاط
            ModelsLog::create([
                'type' => 'purchase_invoice',
                'type_id' => $purchaseInvoice->id,
                'type_log' => 'log',
                'icon' => 'update',
                'description' => sprintf('تم إلغاء دفع فاتورة الشراء رقم **%s**', $purchaseInvoice->code),
                'created_by' => auth()->id(),
            ]);

            return redirect()->back()->with('success', 'تم إلغاء دفع الفاتورة بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث حالة الفاتورة: ' . $e->getMessage());
        }
    }

    // حذف الفاتورة
    public function destroy($id)
    {
        try {
            $purchaseInvoice = PurchaseInvoice::findOrFail($id);
            $invoiceCode = $purchaseInvoice->code;

            // حذف المدفوعات المرتبطة
            $purchaseInvoice->payments()->delete();

            // حذف العناصر المرتبطة
            $purchaseInvoice->items()->delete();

            // حذف الملاحظات المرتبطة
            $notes = ClientRelation::where('quotation_id', $id)
                ->where('type', 'purchase_invoice')
                ->get();

            foreach ($notes as $note) {
                // حذف المرفقات
                if ($note->attachment && Storage::disk('public')->exists($note->attachment)) {
                    Storage::disk('public')->delete($note->attachment);
                }
                $note->delete();
            }

            // حذف الفاتورة
            $purchaseInvoice->delete();

            // تسجيل النشاط
            ModelsLog::create([
                'type' => 'purchase_invoice',
                'type_id' => $id,
                'type_log' => 'log',
                'icon' => 'delete',
                'description' => sprintf('تم حذف فاتورة الشراء رقم **%s**', $invoiceCode),
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('invoicePurchases.index')->with('success', 'تم حذف الفاتورة بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء حذف الفاتورة: ' . $e->getMessage());
        }
    }

    // إضافة ملاحظة للفاتورة
    public function addNote(Request $request, $id)
    {
        try {
            $request->validate([
                'description' => 'required|string|max:1000',
                'process' => 'required|string|max:255',
                'date' => 'required|date',
                'time' => 'required|string',
                'attachment' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
            ]);

            $purchaseInvoice = PurchaseInvoice::findOrFail($id);

            // التعامل مع المرفق إذا تم رفعه
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('purchase_invoices/notes', $fileName, 'public');
            }

            // إنشاء الملاحظة باستخدام ClientRelation
            $clientRelation = ClientRelation::create([
                'process' => $request->process,
                'time' => $request->time,
                'date' => $request->date,
                'quotation_id' => $id,
                'employee_id' => auth()->user()->id,
                'description' => $request->description,
                'attachment' => $attachmentPath,
                'type' => 'purchase_invoice', // نوع مختلف للفواتير
            ]);

            // إرسال إشعار
            notifications::create([
                'user_id' => $purchaseInvoice->user_id,
                'receiver_id' => $purchaseInvoice->user_id,
                'title' => 'ملاحظة جديدة',
                'description' => 'تم إضافة ملاحظة جديدة لفاتورة الشراء رقم ' . $purchaseInvoice->code,
            ]);

            // تسجيل النشاط في سجل الأنشطة
            ModelsLog::create([
                'type' => 'purchase_invoice',
                'type_id' => $purchaseInvoice->id,
                'type_log' => 'log',
                'icon' => 'create',
                'description' => sprintf(
                    'تم إضافة ملاحظة جديدة لفاتورة الشراء رقم **%s** بعنوان: %s',
                    $purchaseInvoice->code ?? '',
                    $request->process
                ),
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حفظ الملاحظة بنجاح',
                'note' => [
                    'id' => $clientRelation->id,
                    'description' => $clientRelation->description,
                    'process' => $clientRelation->process,
                    'date' => $clientRelation->date,
                    'time' => $clientRelation->time,
                    'employee_name' => auth()->user()->name,
                    'has_attachment' => $attachmentPath ? true : false,
                    'attachment_url' => $attachmentPath ? asset('storage/' . $attachmentPath) : null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الملاحظة: ' . $e->getMessage(),
            ], 500);
        }
    }

    // جلب الملاحظات
    public function getNotes($id)
    {
        try {
            $notes = ClientRelation::where('quotation_id', $id)
                ->where('type', 'purchase_invoice')
                ->with('employee')
                ->orderBy('created_at', 'desc')
                ->get();

            // تنسيق البيانات لتتطابق مع JavaScript
            $formattedNotes = $notes->map(function ($note) {
                return [
                    'id' => $note->id,
                    'description' => $note->description,
                    'process' => $note->process,
                    'date' => $note->date,
                    'time' => $note->time,
                    'employee_name' => $note->employee->name ?? 'غير محدد',
                    'has_attachment' => !empty($note->attachment),
                    'attachment_url' => $note->attachment ? asset('storage/' . $note->attachment) : null,
                    'created_at' => $note->created_at->format('Y-m-d H:i:s'),
                ];
            });

            return response()->json([
                'success' => true,
                'notes' => $formattedNotes->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الملاحظات: ' . $e->getMessage(),
            ], 500);
        }
    }

    // حذف الملاحظة
    public function deleteNote($noteId)
    {
        try {
            $note = ClientRelation::findOrFail($noteId);

            // حذف المرفق إذا كان موجود
            if ($note->attachment && Storage::disk('public')->exists($note->attachment)) {
                Storage::disk('public')->delete($note->attachment);
            }

            $quotationId = $note->quotation_id;
            $process = $note->process;

            $note->delete();

            // تسجيل النشاط
            ModelsLog::create([
                'type' => 'purchase_invoice',
                'type_id' => $quotationId,
                'type_log' => 'log',
                'icon' => 'delete',
                'description' => sprintf('تم حذف ملاحظة "%s" من فاتورة الشراء رقم %s', $process, $quotationId),
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الملاحظة بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الملاحظة: ' . $e->getMessage(),
            ], 500);
        }
    }

    // تعيين مراكز التكلفة
    public function assignCostCenter($id)
    {
        $purchaseInvoice = PurchaseInvoice::with(['items.product'])->findOrFail($id);

        // هنا يمكن إضافة منطق تعيين مراكز التكلفة
        // return view('purchases.invoices.assign_cost_center', compact('purchaseInvoice'));

        return redirect()->back()->with('info', 'صفحة تعيين مراكز التكلفة قيد التطوير');
    }

    // نسخ الفاتورة
    public function copy($id)
    {
        try {
            $originalInvoice = PurchaseInvoice::with(['items'])->findOrFail($id);

            // إنشاء فاتورة جديدة بنفس البيانات
            $newInvoice = $originalInvoice->replicate();
            $newInvoice->code = $this->generateInvoiceCode();
            $newInvoice->status = 0; // غير مدفوعة
            $newInvoice->created_at = now();
            $newInvoice->updated_at = now();
            $newInvoice->save();

            // نسخ العناصر
            foreach ($originalInvoice->items as $item) {
                $newItem = $item->replicate();
                $newItem->purchase_invoice_id = $newInvoice->id;
                $newItem->save();
            }

            // تسجيل النشاط
            ModelsLog::create([
                'type' => 'purchase_invoice',
                'type_id' => $newInvoice->id,
                'type_log' => 'log',
                'icon' => 'create',
                'description' => sprintf(
                    'تم إنشاء فاتورة شراء جديدة رقم **%s** بنسخ البيانات من الفاتورة رقم **%s**',
                    $newInvoice->code,
                    $originalInvoice->code
                ),
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('invoicePurchases.show', $newInvoice->id)
                ->with('success', 'تم نسخ الفاتورة بنجاح');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء نسخ الفاتورة: ' . $e->getMessage());
        }
    }

    // دالة مساعدة لتوليد رقم الفاتورة
    private function generateInvoiceCode()
    {
        $prefix = 'INV-';
        $year = date('Y');
        $month = date('m');

        $lastInvoice = PurchaseInvoice::where('code', 'like', $prefix . $year . $month . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $year . $month . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // تصدير PDF

    // تصدير Excel
    public function exportExcel($id)
    {
        $purchaseInvoice = PurchaseInvoice::with(['supplier', 'items.product'])->findOrFail($id);

        // هنا يمكن إضافة منطق تصدير Excel
        return redirect()->back()->with('info', 'تصدير Excel قيد التطوير');
    }

    // إرسال للطباعة
    public function print($id)
    {
        $purchaseInvoice = PurchaseInvoice::with(['supplier', 'items.product'])->findOrFail($id);

        // تسجيل النشاط
        ModelsLog::create([
            'type' => 'purchase_invoice',
            'type_id' => $purchaseInvoice->id,
            'type_log' => 'log',
            'icon' => 'print',
            'description' => sprintf('تم طباعة فاتورة الشراء رقم **%s**', $purchaseInvoice->code),
            'created_by' => auth()->id(),
        ]);

        return view('purchases::purchases.Invoices_purchase.print', compact('purchaseInvoice'));
    }

}
