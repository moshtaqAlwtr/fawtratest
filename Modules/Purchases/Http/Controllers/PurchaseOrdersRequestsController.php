<?php

namespace Modules\Purchases\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Log as ModelsLog;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Models\AccountSetting;
use App\Models\ClientRelation;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\TaxSitting;
use App\Models\TaxInvoice;
use App\Models\TreasuryEmployee;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PurchaseOrdersRequestsController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::all();
        $users = User::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        // إذا كان الطلب Ajax، نعيد البيانات فقط
        if ($request->ajax()) {
            return $this->getFilteredPurchaseData($request);
        }

        // في البداية نعيد الصفحة مع البيانات الأولية
        $purchaseOrdersRequests = $this->getFilteredPurchaseData($request, false);

        return view('purchases::purchases.purchasing_order_requests.index', compact('purchaseOrdersRequests', 'suppliers', 'users', 'account_setting'));
    }

    private function getFilteredPurchaseData(Request $request, $returnJson = true)
    {
        $query = PurchaseInvoice::query()
            ->with(['supplier', 'creator', 'items'])
            ->where('type', 'Requested'); // مرتجع المشتريات

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
            switch ($request->payment_status) {
                case 'paid':
                    $query->where('is_paid', 1);
                    break;
                case 'partial':
                    $query->where('is_paid', 0)->whereColumn('advance_payment', '<', 'grand_total')->where('advance_payment', '>', 0);
                    break;
                case 'unpaid':
                    $query->where('is_paid', 0)->where('advance_payment', 0);
                    break;
                case 'returned':
                    $query->where('type', 3);
                    break;
                case 'overpaid':
                    $query->whereColumn('advance_payment', '>', 'grand_total');
                    break;
                case 'draft':
                    $query->where('status', 0);
                    break;
            }
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
            if ($request->source === 'return') {
                $query->where('type', 1);
            } else {
                $query->where('type', 1);
            }
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

        // ترتيب النتائج
        $query->orderBy('created_at', 'desc');

        // الحصول على النتائج مع التقسيم إلى صفحات
        $purchaseOrdersRequests = $query->paginate(30);

        if ($returnJson) {
            return response()->json([
                'success' => true,
                'data' => view('purchases::purchases.purchasing_order_requests.partials.table', compact('purchaseOrdersRequests'))->render(),
                'pagination' => view('purchases::purchases.purchasing_order_requests.partials.pagination', compact('purchaseOrdersRequests'))->render(),
                'total' => $purchaseOrdersRequests->total(),
                'current_page' => $purchaseOrdersRequests->currentPage(),
                'last_page' => $purchaseOrdersRequests->lastPage(),
            ]);
        }

        return $purchaseOrdersRequests;
    }

    // دالة للتعامل مع طلبات الـ pagination عبر Ajax
    public function paginatePurchase(Request $request)
    {
        if ($request->ajax()) {
            return $this->getFilteredPurchaseData($request);
        }

        return redirect()->route('OrdersRequests.index'); // تغيير هذا إلى اسم المسار الصحيح
    }

public function create()
{
    $suppliers = Supplier::all();
    $items = Product::all();
    $accounts = Account::all();
    $users = User::all();
    $taxs = TaxSitting::all();

    // الحصول على آخر كود مستخدم مع تأمين الجدول
    $code = DB::transaction(function () {
        $lastInvoice = PurchaseInvoice::lockForUpdate()
                      ->orderByRaw('CAST(code AS UNSIGNED) DESC')
                      ->first();

        $nextCode = $lastInvoice ? ((int)$lastInvoice->code + 1) : 1;
        return str_pad($nextCode, 5, '0', STR_PAD_LEFT);
    });

    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

    return view('purchases::purchases.purchasing_order_requests.create',
           compact('suppliers', 'code', 'accounts', 'taxs', 'users', 'items', 'account_setting'));
}
    public function store(Request $request)
{
    try {
        // ** الخطوة الأولى: إنشاء كود للفاتورة **
        $existingInvoice = PurchaseInvoice::where('code', $request->code)->first();
        if ($existingInvoice) {
            throw new \Exception('كود الفاتورة مسجل مسبقاً، يرجى تحديث الصفحة والحصول على كود جديد');
        }

        // التحقق من صحة تنسيق الكود
        if (!preg_match('/^\d{5}$/', $request->code)) {
            throw new \Exception('تنسيق كود الفاتورة غير صالح');
        }

        DB::beginTransaction();
        $code = $request->code;

        // ** إنشاء الفاتورة أولاً (بدون المبالغ المحسوبة) **
        $purchaseOrder = PurchaseInvoice::create([
            'supplier_id' => $request->supplier_id,
            'code' => $code,
            'type' => 'Requested',
            'date' => $request->date,
            'terms' => $request->terms ?? 0,
            'notes' => $request->notes,
            'created_by' => Auth::id(),
            'account_id' => $request->account_id,
            'advance_payment' => floatval($request->advance_payment ?? 0),
            'payment_type' => $request->payment_type ?? 1,
            'payment_method' => $request->payment_method,
            'reference_number' => $request->reference_number,
            'received_date' => $request->received_date,
        ]);

        // ** متغيرات لحساب المبالغ **
        $subtotal = 0; // المجموع الفرعي قبل الضريبة والخصم
        $total_item_tax = 0; // إجمالي ضرائب العناصر
        $total_item_discount = 0; // إجمالي خصومات العناصر
        $taxDetails = []; // تفاصيل الضرائب المختارة

        // ** معالجة عناصر الفاتورة **
        if ($request->has('items')) {
            $invoiceItems = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                // حساب المجموع الفرعي لكل منتج (الكمية × السعر)
                $item_subtotal = floatval($item['quantity']) * floatval($item['unit_price']);
                $subtotal += $item_subtotal;

                // حساب خصم العنصر
                $item_discount = 0;
                if (isset($item['discount']) && $item['discount'] > 0) {
                    if (isset($item['discount_type']) && $item['discount_type'] === 'percentage') {
                        $item_discount = ($item_subtotal * floatval($item['discount'])) / 100;
                    } else {
                        $item_discount = floatval($item['discount']);
                    }
                }
                $total_item_discount += $item_discount;

                // حساب ضرائب العنصر
                $item_tax_total = 0;
                $tax_1_rate = floatval($item['tax_1'] ?? 0);
                $tax_2_rate = floatval($item['tax_2'] ?? 0);

                // معالجة الضريبة الأولى
                if ($tax_1_rate > 0 && !empty($item['tax_1_id'])) {
                    $tax_1 = TaxSitting::find($item['tax_1_id']);
                    if ($tax_1) {
                        $tax_1_value = 0;
                        if ($tax_1->type === 'included') {
                            // الضريبة متضمنة: نستخرجها من المجموع الكلي
                            $tax_1_value = $item_subtotal - $item_subtotal / (1 + $tax_1_rate / 100);
                        } else {
                            // الضريبة غير متضمنة: نضيفها إلى المجموع الفرعي
                            $tax_1_value = ($item_subtotal * $tax_1_rate) / 100;
                        }
                        $item_tax_total += $tax_1_value;

                        // تجميع الضرائب حسب النوع
                        if (!isset($taxDetails[$tax_1->name])) {
                            $taxDetails[$tax_1->name] = 0;
                        }
                        $taxDetails[$tax_1->name] += $tax_1_value;

                        // حفظ الضريبة في جدول TaxInvoice
                        TaxInvoice::create([
                            'name' => $tax_1->name,
                            'invoice_id' => $purchaseOrder->id,
                            'type' => $tax_1->type,
                            'rate' => $tax_1->tax,
                            'value' => $tax_1_value,
                            'type_invoice' => 'purchase',
                        ]);
                    }
                }

                // معالجة الضريبة الثانية
                if ($tax_2_rate > 0 && !empty($item['tax_2_id'])) {
                    $tax_2 = TaxSitting::find($item['tax_2_id']);
                    if ($tax_2) {
                        $tax_2_value = 0;
                        if ($tax_2->type === 'included') {
                            // الضريبة متضمنة: نستخرجها من المجموع الكلي
                            $tax_2_value = $item_subtotal - $item_subtotal / (1 + $tax_2_rate / 100);
                        } else {
                            // الضريبة غير متضمنة: نضيفها إلى المجموع الفرعي
                            $tax_2_value = ($item_subtotal * $tax_2_rate) / 100;
                        }
                        $item_tax_total += $tax_2_value;

                        // تجميع الضرائب حسب النوع
                        if (!isset($taxDetails[$tax_2->name])) {
                            $taxDetails[$tax_2->name] = 0;
                        }
                        $taxDetails[$tax_2->name] += $tax_2_value;

                        // حفظ الضريبة في جدول TaxInvoice
                        TaxInvoice::create([
                            'name' => $tax_2->name,
                            'invoice_id' => $purchaseOrder->id,
                            'type' => $tax_2->type,
                            'rate' => $tax_2->tax,
                            'value' => $tax_2_value,
                            'type_invoice' => 'purchase',
                        ]);
                    }
                }

                $total_item_tax += $item_tax_total;

                // حساب إجمالي العنصر (بعد الخصم)
                $item_total = $item_subtotal - $item_discount;

                // إضافة المنتج للمصفوفة
                $invoiceItems[] = [
                    'purchase_invoice_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'item' => $product->name ?? 'المنتج ' . $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item_discount,
                    'discount_type' => isset($item['discount_type']) && $item['discount_type'] === 'percentage' ? 2 : 1,
                    'tax_1' => $tax_1_rate,
                    'tax_2' => $tax_2_rate,
                    'total' => $item_total,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // إضافة البنود باستخدام insert لتحسين الأداء
            InvoiceItem::insert($invoiceItems);
        }

        // ** حساب الخصم الإضافي **
        $additional_discount = 0;
        if ($request->discount_amount && $request->discount_type) {
            if ($request->discount_type == 'percentage') {
                $additional_discount = ($subtotal * floatval($request->discount_amount)) / 100;
            } else {
                $additional_discount = floatval($request->discount_amount);
            }
        }

        // ** حساب تكلفة الشحن وضريبتها **
        $shipping_cost = floatval($request->shipping_cost ?? 0);
        $shipping_tax = 0;
        if ($shipping_cost > 0 && $request->tax_type == 1) {
            // استخدام ضريبة ثابتة 15% أو يمكن جعلها ديناميكية
            $shipping_tax_rate = 15; // أو جلب من إعدادات النظام
            $shipping_tax = ($shipping_cost * $shipping_tax_rate) / 100;

            // حفظ ضريبة الشحن
            TaxInvoice::create([
                'name' => 'ضريبة الشحن (' . $shipping_tax_rate . '%)',
                'invoice_id' => $purchaseOrder->id,
                'type' => 'excluded',
                'rate' => $shipping_tax_rate,
                'value' => $shipping_tax,
                'type_invoice' => 'purchase',
            ]);
        }

        // ** حساب إجمالي الخصومات **
        $total_discount = $total_item_discount + $additional_discount;

        // ** حساب إجمالي الضرائب **
        $total_tax = $total_item_tax + $shipping_tax;

        // ** حساب المجموع قبل خصم الدفعة المقدمة **
        $total_with_tax = $subtotal - $total_discount + $shipping_cost + $total_tax;

        // ** حساب المبلغ المتبقي بعد الدفعة المقدمة **
        $advance_payment = floatval($request->advance_payment ?? 0);
        $grand_total = $total_with_tax - $advance_payment;

        // ** حالة الفاتورة بناءً على المدفوعات والاستلام **
        $status = 'Under Review'; // الحالة الافتراضية (تحت المراجعة)
        $is_paid = false;
        $is_received = $request->has('is_received');

        if ($advance_payment > 0) {
            if ($advance_payment >= $total_with_tax) {
                $status = 4; // مدفوع بالكامل
                $is_paid = true;
                $grand_total = 0;
            } else {
                $status = 2; // مدفوع جزئيًا
                $is_paid = true;
            }
        } elseif ($request->has('is_paid')) {
            $status = $is_received ? 4 : 5; // مدفوع ومستلم أو مدفوع فقط
            $is_paid = true;
            $grand_total = 0;
        } elseif ($is_received) {
            $status = 3; // مستلم (غير مدفوع)
        }
        // إذا لم تتحقق أي من الشروط أعلاه، ستبقى الحالة "Under Review"

        // ** تحديث الفاتورة بالمبالغ المحسوبة **
        $purchaseOrder->update([
            'status' => $status,
            'discount_amount' => $additional_discount,
            'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
            'shipping_cost' => $shipping_cost,
            'tax_type' => $request->tax_type ?? 1,
            'is_paid' => $is_paid,
            'is_received' => $is_received,
            'subtotal' => $subtotal,
            'total_discount' => $total_discount,
            'total_tax' => $total_tax,
            'grand_total' => $grand_total,
        ]);

        // ** حفظ تفاصيل الخصم الإضافي إذا وجد **
        if ($additional_discount > 0) {
            TaxInvoice::create([
                'name' => 'خصم إضافي',
                'invoice_id' => $purchaseOrder->id,
                'type' => 'discount',
                'rate' => $request->discount_type == 'percentage' ? floatval($request->discount_amount) : 0,
                'value' => $additional_discount,
                'type_invoice' => 'purchase',
            ]);
        }

        // ** معالجة المرفقات (attachments) إذا وجدت **
        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/'), $filename);
                $purchaseOrder->attachments = $filename;
                $purchaseOrder->save();
            }
        }

        // ** الحصول على بيانات المورد للسجل **
        $supplier = Supplier::find($request->supplier_id);
        notifications::create([
            'user_id' => $supplier->user_id,
            'receiver_id' => auth()->id(),
            'title' => 'تم انشاء امر شراء',
            'description' => 'تم انشاء امر شراء رقم ' . $purchaseOrder->code . ' للمورد ' . $supplier->trade_name . ', بمبلغ ' . $grand_total . ', يرجى التحقق منها',
        ]);

        // ** إنشاء سجل النشاط **
        ModelsLog::create([
            'type' => 'purchase_request',
            'type_id' => $purchaseOrder->id,
            'type_log' => 'log',
            'icon' => 'create',
            'description' => sprintf('تم انشاء امر شراء رقم **%s** للمورد **%s** بمبلغ **%s**', $purchaseOrder->code ?? '', $supplier->trade_name ?? '', number_format($grand_total, 2)),
            'created_by' => auth()->id(),
        ]);

        DB::commit();
        return redirect()->route('OrdersRequests.show', $purchaseOrder->id)->with('success', 'تم انشاء امر الشراء بنجاح');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('خطأ في إنشاء امر الشراء: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'عذراً، حدث خطأ أثناء حفظ امر الشراء: ' . $e->getMessage());
    }
}
    public function edit($id)
    {
        $purchaseOrdersRequests = PurchaseInvoice::findOrFail($id);
        $suppliers = Supplier::all();
        $items = Product::all();
        $accounts = Account::all();
        $users = User::all();
$taxs=TaxSitting::all();
        return view('purchases::purchases.purchasing_order_requests.edit', compact('purchaseOrdersRequests','taxs', 'suppliers', 'accounts', 'users', 'items'));
    }
    public function show($id)
    {
        // جلب عرض السعر مع العلاقات المرتبطة
        $purchaseOrdersRequests = PurchaseInvoice::with(['supplier', 'account', 'items.product', 'creator'])->findOrFail($id);

        // جلب جميع الموردين
        $suppliers = Supplier::select('id', 'trade_name')->get();

        $logs = ModelsLog::where('type', 'purchase_request')
            ->where('type_id', $id)
            ->whereHas('purchase_request') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('purchases::purchases.purchasing_order_requests.show', compact('purchaseOrdersRequests', 'logs', 'suppliers'));
    }
   public function update(Request $request, $id)
{
    try {
        DB::beginTransaction();

        $purchaseInvoice = PurchaseInvoice::findOrFail($id);

        // تحديد حالة الفاتورة بناءً على الدفع والاستلام
        $status = 1; // الحالة الافتراضية: قيد المراجعة
        $is_paid = $request->has('is_paid') ? true : $purchaseInvoice->is_paid;
        $is_received = $request->has('is_received') ? true : $purchaseInvoice->is_received;

        if ($is_paid && $is_received) {
            $status = 4; // مكتملة
        } elseif ($is_paid) {
            $status = 2; // مدفوعة
        } elseif ($is_received) {
            $status = 3; // مستلمة
        }

        // تحديث بيانات الفاتورة الأساسية
        $updateData = [
            'supplier_id' => $request->supplier_id ?? $purchaseInvoice->supplier_id,
            'date' => $request->date ?? $purchaseInvoice->date,
            'terms' => $request->terms ?? $purchaseInvoice->terms ?? 0,
            'notes' => $request->notes ?? $purchaseInvoice->notes,
            'status' => $status,
            'account_id' => $request->account_id ?? $purchaseInvoice->account_id,
            'discount_amount' => $request->discount_value ?? $purchaseInvoice->discount_amount ?? 0,
            'discount_type' => $request->discount_type === 'percentage' ? 2 : 1,
            'advance_payment' => $request->advance_payment ?? $purchaseInvoice->advance_payment ?? 0,
            'payment_type' => $request->payment_type ?? $purchaseInvoice->payment_type ?? 1,
            'shipping_cost' => $request->shipping_cost ?? $purchaseInvoice->shipping_cost ?? 0,
            'tax_type' => $request->tax_type ?? $purchaseInvoice->tax_type ?? 1,
            'payment_method' => $request->payment_method ?? $purchaseInvoice->payment_method,
            'reference_number' => $request->reference_number ?? $purchaseInvoice->reference_number,
            'received_date' => $request->received_date ?? $purchaseInvoice->received_date,
            'is_paid' => $is_paid,
            'is_received' => $is_received,
            'updated_at' => now(),
        ];

        $purchaseInvoice->update($updateData);

        $subtotal = 0;
        $total_item_discount = 0;
        $total_item_tax = 0;
        $taxDetails = [];

        // تحديث العناصر فقط إذا تم إرسالها
        if ($request->has('items')) {
            // حذف العناصر القديمة والضرائب المرتبطة بها
            $purchaseInvoice->invoiceItems()->delete();
            TaxInvoice::where('invoice_id', $purchaseInvoice->id)
                     ->where('type_invoice', 'purchase')
                     ->delete();

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id'] ?? null);

                // حساب المجموع الفرعي لكل منتج
                $quantity = floatval($item['quantity'] ?? 0);
                $unit_price = floatval($item['unit_price'] ?? 0);
                $item_subtotal = $quantity * $unit_price;
                $subtotal += $item_subtotal;

                // حساب خصم العنصر
                $item_discount = 0;
                $item_discount_type = isset($item['discount_type']) ? ($item['discount_type'] === 'percentage' ? 2 : 1) : 1;
                $discount_amount = floatval($item['discount_amount'] ?? 0);

                if ($item_discount_type == 2) {
                    $item_discount = ($item_subtotal * $discount_amount) / 100;
                } else {
                    $item_discount = $discount_amount;
                }
                $total_item_discount += $item_discount;

                // حساب ضرائب العنصر
                $item_tax_total = 0;
                $tax_1_rate = floatval($item['tax_1'] ?? 0);
                $tax_2_rate = floatval($item['tax_2'] ?? 0);

                // معالجة الضريبة الأولى
                if ($tax_1_rate > 0 && !empty($item['tax_1_id'])) {
                    $tax_1 = TaxSitting::find($item['tax_1_id']);
                    if ($tax_1) {
                        $tax_1_value = ($item_subtotal - $item_discount) * ($tax_1_rate / 100);
                        $item_tax_total += $tax_1_value;

                        if (!isset($taxDetails[$tax_1->name])) {
                            $taxDetails[$tax_1->name] = 0;
                        }
                        $taxDetails[$tax_1->name] += $tax_1_value;

                        TaxInvoice::create([
                            'name' => $tax_1->name,
                            'invoice_id' => $purchaseInvoice->id,
                            'type' => $tax_1->type,
                            'rate' => $tax_1->tax,
                            'value' => $tax_1_value,
                            'type_invoice' => 'purchase',
                        ]);
                    }
                }

                // معالجة الضريبة الثانية
                if ($tax_2_rate > 0 && !empty($item['tax_2_id'])) {
                    $tax_2 = TaxSitting::find($item['tax_2_id']);
                    if ($tax_2) {
                        $tax_2_value = ($item_subtotal - $item_discount) * ($tax_2_rate / 100);
                        $item_tax_total += $tax_2_value;

                        if (!isset($taxDetails[$tax_2->name])) {
                            $taxDetails[$tax_2->name] = 0;
                        }
                        $taxDetails[$tax_2->name] += $tax_2_value;

                        TaxInvoice::create([
                            'name' => $tax_2->name,
                            'invoice_id' => $purchaseInvoice->id,
                            'type' => $tax_2->type,
                            'rate' => $tax_2->tax,
                            'value' => $tax_2_value,
                            'type_invoice' => 'purchase',
                        ]);
                    }
                }

                $total_item_tax += $item_tax_total;

                // إنشاء عنصر الفاتورة
                $invoiceItem = $purchaseInvoice->invoiceItems()->create([
                    'purchase_invoice_id' => $purchaseInvoice->id,
                    'product_id' => $item['product_id'],
                    'item' => $product->name ?? 'المنتج ' . $item['product_id'],
                    'description' => $item['description'] ?? null,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                    'discount' => $item_discount,
                    'discount_type' => $item_discount_type,
                    'tax_1' => $tax_1_rate,
                    'tax_2' => $tax_2_rate,
                    'total' => ($item_subtotal - $item_discount) + $item_tax_total,
                ]);

                // تسجيل في السجل
                $supplier = Supplier::find($updateData['supplier_id']);
                ModelsLog::create([
                    'type' => 'purchase_log',
                    'type_id' => $purchaseInvoice->id,
                    'type_log' => 'log',
                    'icon' => 'edit',
                    'description' => sprintf(
                        'تم تعديل امر شراء رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للمورد **%s**',
                        $purchaseInvoice->code ?? '',
                        $invoiceItem->item ?? '',
                        $quantity,
                        $unit_price,
                        $supplier->trade_name ?? ''
                    ),
                    'created_by' => auth()->id(),
                ]);
            }
        }

        // حساب الخصم الإضافي
        $additional_discount = 0;
        if ($request->discount_amount && $request->discount_type) {
            if ($request->discount_type == 'percentage') {
                $additional_discount = ($subtotal * floatval($request->discount_amount)) / 100;
            } else {
                $additional_discount = floatval($request->discount_amount);
            }
        }

        // حساب تكلفة الشحن وضريبتها
        $shipping_cost = floatval($request->shipping_cost ?? $purchaseInvoice->shipping_cost ?? 0);
        $shipping_tax = 0;
        if ($shipping_cost > 0 && ($request->tax_type ?? $purchaseInvoice->tax_type) == 1) {
            $shipping_tax_rate = 15;
            $shipping_tax = ($shipping_cost * $shipping_tax_rate) / 100;

            TaxInvoice::create([
                'name' => 'ضريبة الشحن (' . $shipping_tax_rate . '%)',
                'invoice_id' => $purchaseInvoice->id,
                'type' => 'excluded',
                'rate' => $shipping_tax_rate,
                'value' => $shipping_tax,
                'type_invoice' => 'purchase',
            ]);
        }

        // حساب إجمالي الخصومات والضرائب
        $total_discount = $total_item_discount + $additional_discount;
        $total_tax = $total_item_tax + $shipping_tax;

        // حساب المجموع النهائي
        $total_with_tax = $subtotal - $total_discount + $shipping_cost + $total_tax;
        $advance_payment = floatval($request->advance_payment ?? $purchaseInvoice->advance_payment ?? 0);
        $grand_total = $total_with_tax - $advance_payment;

        // تحديث الفاتورة بالمبالغ المحسوبة
        $purchaseInvoice->update([
            'subtotal' => $subtotal,
            'total_discount' => $total_discount,
            'total_tax' => $total_tax,
            'grand_total' => $grand_total,
        ]);

        // حفظ تفاصيل الخصم الإضافي إذا وجد
        if ($additional_discount > 0) {
            TaxInvoice::create([
                'name' => 'خصم إضافي',
                'invoice_id' => $purchaseInvoice->id,
                'type' => 'discount',
                'rate' => $request->discount_type == 'percentage' ? floatval($request->discount_amount) : 0,
                'value' => $additional_discount,
                'type_invoice' => 'purchase',
            ]);
        }

        // معالجة المرفقات
        if ($request->hasFile('attachments')) {
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

        DB::commit();

        return redirect()->route('OrdersRequests.index')->with('success', 'تم تحديث امر شراء بنجاح');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('خطأ في تحديث امر شراء: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'عذراً، حدث خطأ أثناء تحديث امر شراء: ' . $e->getMessage());
    }
}
    public function destroy($id)
    {
        try {
            $purchaseOrdersRequests = PurchaseInvoice::findOrFail($id);
            ModelsLog::create([
                'type' => 'purchase_request',
                'type_id' => $purchaseOrdersRequests->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'icon' => 'delete',
                'description' => sprintf(
                    'تم حذف امر شراء رقم **%s**',
                    $purchaseOrdersRequests->code ?? '', // رقم طلب الشراء
                ),
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
            $purchaseOrdersRequests->delete();
            return redirect()->route('OrdersRequests.index')->with('success', 'تم حذف أمر الشراء بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطاء في حذف أمر الشراء: ' . $e->getMessage());
        }
    }
public function convertToInvoice($id)
{
    try {
        DB::beginTransaction(); // بدء المعاملة

        // جلب أمر الشراء الأصلي
        $purchaseOrder = PurchaseInvoice::with('items')->findOrFail($id);

        // التحقق من أن النوع أمر شراء وليس فاتورة
        if ($purchaseOrder->type === 'invoice') {
            return redirect()->back()->with('error', 'هذا العنصر فاتورة بالفعل وليس أمر شراء');
        }

        // التحقق من أن الأمر لم يتم تحويله مسبقاً
        if ($purchaseOrder->status === 'convert invoice') {
            return redirect()->back()->with('error', 'تم تحويل هذا الأمر إلى فاتورة مسبقاً');
        }

        $lastInvoice = PurchaseInvoice::orderBy('id', 'desc')->first();
        $code = $lastInvoice ? (int) $lastInvoice->code + 1 : 1;
        $code = str_pad($code, 5, '0', STR_PAD_LEFT);

        // الحصول على بيانات التحويل من الطلب
        $paymentStatus = request('payment_status', 'deferred'); // آجلة افتراضياً
        $advanceAmount = request('advance_amount', 0); // المبلغ المقدم

        // *** تحديد حالات الدفع والاستلام الجديدة ***
        $payment_status = 'unpaid'; // الحالة الافتراضية
        // الحالة الافتراضية للاستلام
        $isPaid = false;
        $statusPayment = 0;
        $finalAdvanceAmount = 0;

        // تحديد حالة الدفع بناءً على البيانات المدخلة
        if ($paymentStatus === 'paid') {
            // دفع كامل
            $payment_status = 'paid';
            $isPaid = true;
            $statusPayment = 1;
            $finalAdvanceAmount = $purchaseOrder->grand_total;
        } elseif ($advanceAmount > 0) {
            // دفعة مقدمة
            $finalAdvanceAmount = floatval($advanceAmount);

            if ($finalAdvanceAmount >= $purchaseOrder->grand_total) {
                // إذا كانت الدفعة المقدمة تساوي أو تتجاوز المجموع الكلي
                $payment_status = 'paid';
                $finalAdvanceAmount = $purchaseOrder->grand_total;
                $isPaid = true;
                $statusPayment = 1;
            } else {
                // دفعة مقدمة جزئية
                $payment_status = 'partially_paid';
                $isPaid = false;
                $statusPayment = 0;
            }
        } else {
            // بدون دفع
            $payment_status = 'unpaid';
            $isPaid = false;
            $statusPayment = 0;
            $finalAdvanceAmount = 0;
        }


        $dueValue = $purchaseOrder->grand_total - $finalAdvanceAmount;

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
        if ($finalAdvanceAmount > 0 && $mainTreasuryAccount->balance < $finalAdvanceAmount) {
            throw new \Exception('رصيد الخزينة غير كافي. الرصيد الحالي: ' . number_format($mainTreasuryAccount->balance, 2) . ' والمطلوب: ' . number_format($finalAdvanceAmount, 2));
        }

        // إنشاء الفاتورة الجديدة بناءً على بيانات الأمر
        $purchaseInvoice = PurchaseInvoice::create([
            'supplier_id' => $purchaseOrder->supplier_id,
            'code' => $code,
            'type' => "invoice", // نوع الفاتورة
            'date' => now()->format('Y-m-d'), // تاريخ اليوم
            'terms' => $purchaseOrder->terms ?? 0,
            'notes' => $purchaseOrder->notes . "\n" . "محولة من أمر الشراء رقم: " . $purchaseOrder->code . "\nحالة الدفع: " . $this->getPaymentStatusText($payment_status) . "\nحالة الاستلام: "  . "\nالخزينة المستخدمة: " . $mainTreasuryAccount->name,
            'payment_status' => $payment_status, // ✅ الحقل الجديد
 // ✅ الحقل الجديد
            'status_payment' => $statusPayment, // للتوافق مع النظام القديم
            'created_by' => auth()->id(),
            'account_id' => $purchaseOrder->account_id,
            'discount_amount' => $purchaseOrder->discount_amount,
            'discount_type' => $purchaseOrder->discount_type,
            'payment_type' => $purchaseOrder->payment_type ?? 1,
            'shipping_cost' => $purchaseOrder->shipping_cost,
            'tax_type' => $purchaseOrder->tax_type ?? 1,
            'payment_method' => $purchaseOrder->payment_method,
            'reference_number' => $purchaseOrder->code, // رقم المرجع هو رقم الأمر الأصلي
            'reference_id' => $purchaseOrder->id, // ✅ إضافة ID أمر الشراء الأصلي
            'received_date' => now()->format('Y-m-d'),
            'is_paid' => $isPaid, // للتوافق مع النظام القديم
 // للتوافق مع النظام القديم
            'subtotal' => $purchaseOrder->subtotal,
            'total_discount' => $purchaseOrder->total_discount,
            'total_tax' => $purchaseOrder->total_tax,
            'grand_total' => $purchaseOrder->grand_total,
            'due_value' => $dueValue, // القيمة المستحقة
            'advance_payment' => $finalAdvanceAmount, // ✅ المبلغ المقدم أو كامل المبلغ
        ]);

        // ** إنشاء الإذن المخزني للفاتورة الجديدة **
        $warehousePermit = $this->createWarehousePermitFromOrder($purchaseInvoice, $purchaseOrder);

        // نسخ بنود الأمر إلى الفاتورة الجديدة والإذن المخزني
        foreach ($purchaseOrder->items as $originalItem) {
            $invoiceItem = InvoiceItem::create([
                'purchase_invoice_id' => $purchaseInvoice->id,
                'product_id' => $originalItem->product_id,
                'item' => $originalItem->item,
                'description' => $originalItem->description,
                'quantity' => $originalItem->quantity,
                'unit_price' => $originalItem->unit_price,
                'discount' => $originalItem->discount,
                'discount_type' => $originalItem->discount_type,
                'tax_1' => $originalItem->tax_1,
                'tax_2' => $originalItem->tax_2,
                'total' => $originalItem->total,
                'store_house_id' => $originalItem->store_house_id,
            ]);

            // إنشاء بند في الإذن المخزني
            WarehousePermitsProducts::create([
                'warehouse_permits_id' => $warehousePermit->id,
                'product_id' => $originalItem->product_id,
                'quantity' => $originalItem->quantity,
                'unit_price' => $originalItem->unit_price,
                'total' => $originalItem->total,
                'notes' => $originalItem->description,
                'store_house_id' => $originalItem->store_house_id,
            ]);

            $product = Product::find($originalItem->product_id);
            $supplier = Supplier::find($purchaseOrder->supplier_id);

            // تسجيل لوج النظام
            ModelsLog::create([
                'type' => 'purchase_log',
                'type_id' => $purchaseInvoice->id,
                'type_log' => 'log',
                'icon' => 'convert',
                'description' => sprintf(
                    'تم تحويل أمر شراء رقم **%s** إلى فاتورة شراء رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للمورد **%s** وإذن مخزني رقم **%s** من خزينة **%s** - حالة الدفع: **%s** - حالة الاستلام: **%s**',
                    $purchaseOrder->code,
                    $purchaseInvoice->code,
                    $product->name ?? '',
                    $originalItem->quantity,
                    $originalItem->unit_price,
                    $supplier->trade_name ?? '',
                    $warehousePermit->number ?? '',
                    $mainTreasuryAccount->name,
                    $this->getPaymentStatusText($payment_status),

                ),
                'created_by' => auth()->id(),
            ]);
        }

        // نسخ الضرائب من الأمر الأصلي
        $originalTaxes = TaxInvoice::where('invoice_id', $purchaseOrder->id)
                                  ->where('type_invoice', 'purchase')
                                  ->get();

        foreach ($originalTaxes as $originalTax) {
            TaxInvoice::create([
                'name' => $originalTax->name,
                'invoice_id' => $purchaseInvoice->id,
                'type' => $originalTax->type,
                'rate' => $originalTax->rate,
                'value' => $originalTax->value,
                'type_invoice' => 'purchase',
            ]);
        }

        // إنشاء عملية دفع (إذا كان هناك دفع مقدم أو الفاتورة مدفوعة)
        if ($finalAdvanceAmount > 0) {
            $payment = PaymentsProcess::create([
                'purchases_id' => $purchaseInvoice->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'amount' => $finalAdvanceAmount,
                'payment_date' => now(),
                'payment_method' => $purchaseOrder->payment_method ?? 1,
                'type' => 'supplier payments',
                'payment_status' => 1, // مكتمل
                'employee_id' => Auth::id(),
                'treasury_id' => $treasury_id, // ✅ إضافة ID الخزينة المستخدمة
                'notes' => $payment_status === 'paid' ?
                    'دفعة كاملة لفاتورة المشتريات رقم ' . $purchaseInvoice->code . ' (محولة من أمر ' . $purchaseOrder->code . ') من خزينة ' . $mainTreasuryAccount->name :
                    'دفعة مقدمة بمبلغ ' . number_format($finalAdvanceAmount, 2) . ' لفاتورة المشتريات رقم ' . $purchaseInvoice->code . ' (محولة من أمر ' . $purchaseOrder->code . ') من خزينة ' . $mainTreasuryAccount->name,
            ]);
        }

        // إنشاء القيود المحاسبية
        $this->createAccountingEntriesForConversion($purchaseInvoice, $purchaseOrder, $finalAdvanceAmount, $mainTreasuryAccount, $payment_status);

        // ✅ تحديث حالة الأمر الأصلي وإضافة الملاحظات
        $purchaseOrder->status = 'convert invoice'; // تغيير الحالة
        $purchaseOrder->notes = ($purchaseOrder->notes ?? '') . "\n" . "تم تحويله إلى فاتورة رقم: " . $purchaseInvoice->code . " بتاريخ: " . now()->format('Y-m-d H:i:s') . " من خزينة: " . $mainTreasuryAccount->name . " - حالة الدفع: " . $this->getPaymentStatusText($payment_status) . " - حالة الاستلام: " ;
        $purchaseOrder->save();

        // ✅ إنشاء إشعار للنظام
        $supplier = Supplier::find($purchaseOrder->supplier_id);
        notifications::create([
            'type' => 'purchase_conversion',
            'title' => $user->name . ' حول أمر شراء إلى فاتورة',
            'description' => 'تم تحويل أمر الشراء رقم ' . $purchaseOrder->code . ' إلى فاتورة رقم ' . $purchaseInvoice->code . ' للمورد ' . ($supplier->trade_name ?? '') . ' بقيمة ' . number_format($purchaseInvoice->grand_total, 2) . ' ر.س من خزينة ' . $mainTreasuryAccount->name . ($finalAdvanceAmount > 0 ? ' - تم سحب ' . number_format($finalAdvanceAmount, 2) . ' ر.س' : '') . ' - حالة الدفع: ' . $this->getPaymentStatusText($payment_status) . ' - حالة الاستلام: ' ,
        ]);

        DB::commit(); // تأكيد التغييرات

        // *** رسالة النجاح المحسنة ***
        $successMessage = $this->generateConversionSuccessMessage($purchaseOrder->code, $purchaseInvoice->code, $warehousePermit->number, $payment_status, $finalAdvanceAmount, $dueValue, $mainTreasuryAccount->name);

        return redirect()->route('invoicePurchases.show', $purchaseInvoice->id)
                        ->with('success', $successMessage);

    } catch (\Exception $e) {
        DB::rollback(); // تراجع عن التغييرات في حالة حدوث خطأ
        Log::error('خطأ في تحويل أمر الشراء إلى فاتورة وإذن مخزني: ' . $e->getMessage());

        return redirect()->back()->with('error', 'عذراً، حدث خطأ أثناء تحويل أمر الشراء إلى فاتورة وإذن مخزني: ' . $e->getMessage());
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
 * دالة مساعدة لتحويل حالة الاستلام إلى نص عربي
 */

/**
 * دالة مساعدة لتوليد رسالة نجاح التحويل
 */
private function generateConversionSuccessMessage($orderCode, $invoiceCode, $warehouseNumber, $payment_status,  $finalAdvanceAmount, $dueValue, $treasuryName)
{
    $payment_text = $this->getPaymentStatusText($payment_status);

    $message = 'تم تحويل أمر الشراء رقم ' . $orderCode . ' إلى فاتورة رقم ' . $invoiceCode . ' وإذن مخزني رقم ' . $warehouseNumber . ' بنجاح من خزينة ' . $treasuryName;

    switch ($payment_status) {
        case 'paid':
            $message .= '. تم سحب كامل المبلغ: ' . number_format($finalAdvanceAmount, 2) . ' ر.س';
            break;
        case 'partially_paid':
            $message .= '. المبلغ المدفوع مقدماً: ' . number_format($finalAdvanceAmount, 2) . ' ر.س والمبلغ المستحق: ' . number_format($dueValue, 2) . ' ر.س';
            break;
        case 'unpaid':
            $message .= '. بدون دفعة مقدمة';
            break;
    }

    $message .= ' | حالة الدفع: ' . $payment_text . ' | حالة الاستلام: ' ;
    $message .= '. يرجى الموافقة على الإذن لدخول البضاعة للمخزون';

    return $message;
}

/**
 * إنشاء القيود المحاسبية للتحويل
 */
private function createAccountingEntriesForConversion($purchaseInvoice, $total_with_tax, $tax_total, $paid_amount, $mainTreasuryAccount)
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
/**
 * إنشاء إذن مخزني من أمر الشراء المحول
 */
private function createWarehousePermitFromOrder($purchaseInvoice, $purchaseOrder)
{
    // إنشاء رقم الإذن المخزني
    $lastPermit = WarehousePermits::orderBy('id', 'desc')->first();
    $nextPermitNumber = $lastPermit ? intval($lastPermit->number) + 1 : 1;
    $permitNumber = str_pad($nextPermitNumber, 6, '0', STR_PAD_LEFT);

    // تحديد المستودع الرئيسي
    $storeHouseId = null;
    // البحث عن مستودع في بنود الأمر الأصلي
    $firstItem = $purchaseOrder->items->first();
    if ($firstItem && $firstItem->store_house_id) {
        $storeHouseId = $firstItem->store_house_id;
    } else {
        // استخدام المستودع الرئيسي
        $mainStoreHouse = StoreHouse::where('major', true)->first();
        $storeHouseId = $mainStoreHouse ? $mainStoreHouse->id : null;
    }

    // إنشاء الإذن المخزني
    $warehousePermit = WarehousePermits::create([
        'permission_source_id' => 1, // نوع الإذن: إدخال
        'permission_date' => now(),
        'sub_account' => $purchaseInvoice->supplier_id, // المورد
        'number' => $permitNumber,
        'store_houses_id' => $storeHouseId,
        'from_store_houses_id' => null, // لا يوجد مستودع مصدر للمشتريات
        'to_store_houses_id' => $storeHouseId, // المستودع المستهدف
        'grand_total' => $purchaseInvoice->grand_total,
        'details' => 'إذن إدخال بضاعة لفاتورة شراء رقم: ' . $purchaseInvoice->code . ' (محولة من أمر ' . $purchaseOrder->code . ')',
        'attachments' => null,
        'created_by' => Auth::id(),
        'status' => 'pending', // بانتظار الموافقة
        'reference_type' => 'purchase_invoice', // نوع المرجع
        'reference_id' => $purchaseInvoice->id, // معرف الفاتورة
    ]);

    return $warehousePermit;
}

    public function cancel($id)
    {
        try {
            $purchaseOrder = PurchaseInvoice::findOrFail($id);

            // إلغاء أمر الشراء
            $purchaseOrder->type = 3; // يمكنك تغيير هذا الرقم حسب حاجتك
            $purchaseOrder->save();

            return redirect()->back()->with('success', 'تم إلغاء أمر الشراء بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء أمر الشراء');
        }
    }
    public function updateStatus(Request $request, $id)
    {
        $purchaseOrder = PurchaseInvoice::findOrFail($id);
        $newType = $request->input('type');

        DB::beginTransaction();
        try {
            $oldStatus = $purchaseOrder->type;

            $purchaseOrder->update([
                'type' => $newType,
                'updated_by' => auth()->id(),
                'updated_at' => now(),
            ]);

            // تحديد نص الرسالة حسب نوع العملية
            $statusMessages = [
                "Under Review" => 'تحت المراجعة',
                "approval" => 'محول إلى فاتورة',
                "disagree" => 'ملغي',
            ];

            $description = sprintf('تم تغيير حالة أمر الشراء رقم **%s** من "%s" إلى "%s"', $purchaseOrder->code, $statusMessages[$oldStatus] ?? 'غير محدد', $statusMessages[$newType] ?? 'غير محدد');

            // إضافة ملاحظة إضافية إذا تم إدخالها
            if ($request->filled('note')) {
                $description .= "\n\n**ملاحظة:** " . $request->note;
            }

            // تسجيل النشاط
            ModelsLog::create([
                'type' => 'purchase_request',
                'type_id' => $purchaseOrder->id,
                'type_log' => 'log',
                'icon' => 'update',
                'description' => $description,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            $successMessage = match ($newType) {
                'approval' => 'تم تحويل أمر الشراء إلى فاتورة بنجاح',
                'disagree' => 'تم إلغاء أمر الشراء بنجاح',
                'Under Review' => 'تم إعادة تفعيل أمر الشراء بنجاح',
                default => 'تم تحديث حالة أمر الشراء بنجاح',
            };

            return redirect()->back()->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث حالة أمر الشراء');
        }
    }

    // دالة إضافة ملاحظة
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

            $purchaseOrder = PurchaseInvoice::findOrFail($id);

            // التعامل مع المرفق إذا تم رفعه
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('purchase_requests/notes', $fileName, 'public');
            }

            // إنشاء الملاحظة باستخدام ClientRelation
            $clientRelation = ClientRelation::create([
                'process' => $request->process,
                'time' => $request->time,
                'date' => $request->date,
                'quotation_id' => $id, // نستخدم نفس الحقل
                'employee_id' => auth()->user()->id,
                'description' => $request->description,
                'attachment' => $attachmentPath,
                'type' => 'purchase_Request', // نوع مختلف لتمييز أوامر الشراء
            ]);
            notifications::create([
                'user_id' => $purchaseOrder->user_id,
                'receiver_id' => $purchaseOrder->user_id,
                'title' => 'ملاحظة جديدة',
                'message' => 'تم اضافة ملاحظة جديدة لأمر الشراء رقم ' . $purchaseOrder->code,
            ]);

            // تسجيل النشاط في سجل الأنشطة
            ModelsLog::create([
                'type' => 'purchase_request',
                'type_id' => $purchaseOrder->id,
                'type_log' => 'log',
                'icon' => 'create',
                'description' => sprintf('تم إضافة ملاحظة جديدة لأمر الشراء رقم **%s** بعنوان: %s', $purchaseOrder->code ?? '', $request->process),
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
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حفظ الملاحظة: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // دالة جلب الملاحظات
    public function getNotes($id)
    {
        try {
            $notes = ClientRelation::where('quotation_id', $id)->where('type', 'purchase_Request')->with('employee')->orderBy('created_at', 'desc')->get();

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
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء جلب الملاحظات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    // دالة حذف الملاحظة
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
                'type' => 'purchase_Request',
                'type_id' => $quotationId,
                'type_log' => 'log',
                'icon' => 'delete',
                'description' => sprintf('تم حذف ملاحظة "%s" من أمر الشراء رقم %s', $process, $quotationId),
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الملاحظة بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف الملاحظة: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }


    public function approve(Request $request, $id)
    {
        $purchaseInvoice = PurchaseInvoice::findOrFail($id);

        DB::beginTransaction();
        try {
            $purchaseInvoice->update([
                'status' => 'approval',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
                'approval_note' => $request->note,
            ]);

            // إضافة سجل في النشاطات

            DB::commit();
            return redirect()->back()->with('success', 'تمت الموافقة على الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء الموافقة على الطلب');
        }
    }

    // دالة رفض الطلب
    public function reject(Request $request, $id)
    {
        $purchaseInvoice = PurchaseInvoice::findOrFail($id);

        DB::beginTransaction();
        try {
            $purchaseInvoice->update([
                'status' => 'disagree',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
            ]);
ModelsLog::create([
    'user_id' => auth()->id(),
    'type' => 'purchase_request',
    'type_id' => $purchaseInvoice->id,
    'description' => 'رفض الطلب',
]);
            // إضافة سجل في النشاطات

            DB::commit();
            return redirect()->back()->with('success', 'تم رفض الطلب بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء رفض الطلب');
        }
    }

    // دالة إلغاء الموافقة
    public function cancelApproval(Request $request, $id)
    {
        $purchaseInvoice = PurchaseInvoice::findOrFail($id);

        // التحقق من أن الطلب معتمد أصلاً
        if ($purchaseInvoice->status !== 'approval') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء الموافقة، الطلب غير معتمد');
        }

        DB::beginTransaction();
        try {
            $purchaseInvoice->update([
                'status' => 'Under Review',
                'approved_by' => null,
                'approved_at' => null,
                'approval_note' => null,
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
            ]);

            // إضافة سجل في النشاطات


ModelsLog::create([
    'type' => 'purchase_request',
    'type_id' => $purchaseInvoice->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => ' تم إلغاء الموافقة على الطلب',
]);
            DB::commit();
            return redirect()->back()->with('success', 'تم إلغاء الموافقة بنجاح وإعادة الطلب للمراجعة');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء إلغاء الموافقة');
        }
    }

    // دالة التراجع عن الرفض
    public function undoRejection(Request $request, $id)
    {
        $purchaseInvoice = PurchaseInvoice::findOrFail($id);

        // التحقق من أن الطلب مرفوض أصلاً
        if ($purchaseInvoice->status !== 'disagree') {
            return redirect()->back()->with('error', 'لا يمكن التراجع عن الرفض، الطلب غير مرفوض');
        }

        DB::beginTransaction();
        try {
            $purchaseInvoice->update([
                'status' => 'Under Review',
                'rejected_by' => null,
                'rejected_at' => null,
                'restored_by' => auth()->id(),
                'restored_at' => now(),
            ]);
ModelsLog::create([
    'user_id' => auth()->id(),
    'description' => 'تم التراجع عن الرفض',
    'type' => 'purchase_request',
    'type_id' => $purchaseInvoice->id

]);

            DB::commit();
            return redirect()->back()->with('success', 'تم التراجع عن الرفض بنجاح وإعادة الطلب للمراجعة');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التراجع عن الرفض');
        }
    }

    // دالة حذف أمر الشراء
}
