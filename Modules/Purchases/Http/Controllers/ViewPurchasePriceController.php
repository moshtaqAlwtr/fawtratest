<?php

namespace Modules\Purchases\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ClientRelation;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\Log as ModelsLog;
use App\Models\notifications;
use App\Models\PurchaseQuotationView;
use App\Models\Supplier;
use App\Models\TaxSitting;
use App\Models\TaxInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ViewPurchasePriceController extends Controller
{
    public function index(Request $request)
    {
        $suppliers = Supplier::all();

        // إذا كان الطلب Ajax، نعيد البيانات فقط
        if ($request->ajax()) {
            return $this->getFilteredQuotationData($request);
        }

        // في البداية نعيد الصفحة مع البيانات الأولية
        $purchaseQuotation = $this->getFilteredQuotationData($request, false);

        return view('purchases::purchases.view_purchase_price.index', compact('suppliers', 'purchaseQuotation'));
    }

    private function getFilteredQuotationData(Request $request, $returnJson = true)
    {
        // بناء الاستعلام
        $query = PurchaseQuotationView::query();

        // البحث بالكود
        if ($request->filled('code')) {
            $query->where('code', 'like', '%' . $request->code . '%');
        }

        // البحث بالتاريخ (من)
        if ($request->filled('order_date_from')) {
            $query->whereDate('date', '>=', $request->order_date_from);
        }

        // البحث بالتاريخ (إلى)
        if ($request->filled('order_date_to')) {
            $query->whereDate('date', '<=', $request->order_date_to);
        }

        // البحث بالعملة
        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        // البحث بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // البحث بالوسم
        if ($request->filled('tag')) {
            $query->where('tag', $request->tag);
        }

        // البحث بالنوع
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // البحث بالمورد
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // ترتيب النتائج حسب تاريخ الإنشاء (الأحدث أولًا)
        $query->latest('created_at');

        // تحميل العلاقات المطلوبة
        $query->with(['supplier', 'account', 'items.product']);

        // الحصول على النتائج مع التقسيم إلى صفحات
        $purchaseQuotation = $query->paginate(10);

        if ($returnJson) {
            return response()->json([
                'success' => true,
                'data' => view('purchases::purchases.view_purchase_price.partials.table', compact('purchaseQuotation'))->render(),
                'pagination' => view('purchases::purchases.view_purchase_price.partials.pagination', compact('purchaseQuotation'))->render(),
                'total' => $purchaseQuotation->total(),
                'current_page' => $purchaseQuotation->currentPage(),
                'last_page' => $purchaseQuotation->lastPage(),
            ]);
        }

        return $purchaseQuotation;
    }

    // دالة للتعامل مع طلبات الـ pagination عبر Ajax
    public function paginateQuotation(Request $request)
    {
        if ($request->ajax()) {
            return $this->getFilteredQuotationData($request);
        }

        return redirect()->route('PurchaseQuotation.index');
    }

    public function show($id)
    {
        // جلب عرض السعر مع العلاقات المرتبطة
        $purchaseQuotation = PurchaseQuotationView::with(['supplier', 'account', 'items.product', 'creator'])->findOrFail($id);

        // جلب جميع الموردين
        $suppliers = Supplier::select('id', 'trade_name')->get();

        $logs = ModelsLog::where('type', 'quotation_view')
            ->where('type_id', $id)
            ->whereHas('quotation_view') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('purchases::purchases.view_purchase_price.show', compact('purchaseQuotation', 'logs', 'suppliers'));
    }
    public function create()
    {
        // جلب البيانات الأساسية
        $accounts = Account::all();
        $items = Product::all();
        $suppliers = Supplier::all();
        $taxs = TaxSitting::all();

        // جلب المورد المحدد إذا كان موجودًا في الرابط
        $selectedSupplier = null;
        if (request()->has('supplier_id')) {
            $selectedSupplier = Supplier::find(request('supplier_id'));
        }

        // جلب بيانات عرض السعر الأصلي إذا كان موجودًا
        $quotation = null;
        if (request()->has('quotation_id')) {
            $quotation = \App\Models\PurchaseQuotation::with(['items.product'])->find(request('quotation_id'));

            // إذا لم يتم العثور على عرض السعر، إرجاع رسالة خطأ
            if (!$quotation) {
                return redirect()->back()->with('error', 'عرض السعر المحدد غير موجود');
            }

            // تعيين المورد المحدد من عرض السعر الأصلي
            if (!$selectedSupplier) {
                $selectedSupplier = $quotation->supplier;
            }
        }

        // توليد رقم عرض الشراء
        $lastPurchasePrice = PurchaseQuotationView::orderBy('id', 'desc')->first();
        $nextId = $lastPurchasePrice ? $lastPurchasePrice->id + 1 : 1;

        // تنسيق رقم العرض (5 خانات مع أصفار على اليسار)
        $purchasePriceNumber = str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return view('purchases::purchases.view_purchase_price.create', [
            'suppliers' => $suppliers,
            'items' => $items,
            'taxs' => $taxs,
            'accounts' => $accounts,
            'selectedSupplier' => $selectedSupplier,
            'purchasePriceNumber' => $purchasePriceNumber,
            'nextId' => $nextId,
            'quotation' => $quotation,
        ]);
    }
    public function store(Request $request)
{
    try {
        // توليد الكود تلقائياً إذا لم يتم إرساله
        if (!$request->code) {
            $lastQuotation = PurchaseQuotationView::latest()->first();
            $nextNumber = $lastQuotation ? intval($lastQuotation->code) + 1 : 1;
            $request->merge(['code' => str_pad($nextNumber, 5, '0', STR_PAD_LEFT)]);
        }

        // التحقق مما إذا كان هناك عرض سعر أصلي لتحديث حالته
        $originalQuotation = null;
        if ($request->has('quotation_id')) {
            $originalQuotation = \App\Models\PurchaseQuotation::find($request->quotation_id);
        }

        // التحقق من البيانات
        $rules = [
            'quotation_id' => 'nullable|integer',
            'supplier_id' => 'required|exists:suppliers,id',
            'code' => 'required|string|unique:purchase_quotations_view,code',
            'date' => 'required|date',
            'valid_days' => 'nullable|integer|min:0',

            // قواعد الخصم والتسوية
            'discount_amount' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percentage',
            'adjustment_label' => 'nullable|string|max:255',
            'adjustment_type' => 'nullable|in:discount,addition',
            'adjustment_value' => 'nullable|numeric',
            // قواعد الشحن
            'shipping_cost' => 'nullable|numeric|min:0',
            'tax_id' => 'nullable|exists:tax_sittings,id',
        ];

        $validator = Validator::make($request->all(), $rules);
        $validatedData = $validator->validated();

        DB::beginTransaction();

        // إنشاء عرض السعر أولاً (بدون المبالغ المحسوبة)
        $quotation = PurchaseQuotationView::create([
            'id' => $request->id,
            'quotation_id' => $request->quotation_id ?? $request->id, // ✅ إضافة quotation_id
            'purchase_price_number' => $request->purchase_price_number,
            'supplier_id' => $validatedData['supplier_id'],
            'code' => $validatedData['code'],
            'date' => $validatedData['date'],
            'valid_days' => $validatedData['valid_days'] ?? 0,
            'created_by' => Auth::id(),
        ]);

        // متغيرات لحساب المبالغ
        $subtotal = 0; // المجموع الفرعي قبل الضريبة والخصم
        $total_item_tax = 0; // إجمالي ضرائب العناصر
        $total_item_discount = 0; // إجمالي خصومات العناصر
        $taxDetails = []; // تفاصيل الضرائب المختارة

        // معالجة عناصر الفاتورة
        if ($request->has('items')) {
            $invoiceItems = [];

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                // حساب المجموع الفرعي لكل منتج (الكمية × السعر)
                $item_subtotal = floatval($item['quantity']) * floatval($item['unit_price']);
                $subtotal += $item_subtotal;

                // حساب خصم العنصر
                $item_discount = 0;
                if (isset($item['discount_type']) && isset($item['discount_amount'])) {
                    if ($item['discount_type'] == 'percentage') {
                        $item_discount = ($item_subtotal * floatval($item['discount_percentage'] ?? 0)) / 100;
                    } else {
                        $item_discount = floatval($item['discount'] ?? 0);
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
                            'invoice_id' => $quotation->id,
                            'type' => $tax_1->type,
                            'rate' => $tax_1->tax,
                            'value' => $tax_1_value,
                            'type_invoice' => 'quotation_purchase',
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
                            'invoice_id' => $quotation->id,
                            'type' => $tax_2->type,
                            'rate' => $tax_2->tax,
                            'value' => $tax_2_value,
                            'type_invoice' => 'quotation_purchase',
                        ]);
                    }
                }

                $total_item_tax += $item_tax_total;

                // حساب إجمالي العنصر (بعد الخصم)
                $item_total = $item_subtotal - $item_discount;

                // إضافة المنتج للمصفوفة
                $invoiceItems[] = [
                    'quotes_purchase_order_id' => $quotation->id,
                    'product_id' => $item['product_id'],
                    'item' => $product->name ?? 'المنتج ' . $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'discount' => $item_discount,
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

        // حساب الخصم الإضافي
        $additional_discount = 0;
        if ($request->discount_amount && $request->discount_type) {
            if ($request->discount_type == 'percentage') {
                $additional_discount = ($subtotal * floatval($request->discount_amount)) / 100;
            } else {
                $additional_discount = floatval($request->discount_amount);
            }
        }

        // حساب التسوية
        $adjustment_amount = 0;
        if ($request->adjustment_value && $request->adjustment_type) {
            $adjustment_value = floatval($request->adjustment_value);
            if ($request->adjustment_type == 'discount') {
                $adjustment_amount = -$adjustment_value; // خصم
            } else {
                $adjustment_amount = $adjustment_value; // إضافة
            }
        }

        // حساب تكلفة الشحن وضريبتها
        $shipping_cost = floatval($request->shipping_cost ?? 0);
        $shipping_tax = 0;
        if ($shipping_cost > 0 && $request->tax_id) {
            $shipping_tax_rate = TaxSitting::find($request->tax_id);
            if ($shipping_tax_rate) {
                $shipping_tax = ($shipping_cost * $shipping_tax_rate->tax) / 100;

                // حفظ ضريبة الشحن
                TaxInvoice::create([
                    'name' => $shipping_tax_rate->name . ' (شحن)',
                    'invoice_id' => $quotation->id,
                    'type' => $shipping_tax_rate->type,
                    'rate' => $shipping_tax_rate->tax,
                    'value' => $shipping_tax,
                    'type_invoice' => 'quotation_purchase',
                ]);
            }
        }

        // حساب إجمالي الخصومات
        $total_discount = $total_item_discount + $additional_discount;

        // حساب إجمالي الضرائب
        $total_tax = $total_item_tax + $shipping_tax;

        // حساب المجموع النهائي
        // المجموع النهائي = المجموع الفرعي - إجمالي الخصم + التسوية + تكلفة الشحن + إجمالي الضريبة
        $grand_total = $subtotal - $total_discount + $adjustment_amount + $shipping_cost + $total_tax;

        // تحديث عرض السعر بالمبالغ المحسوبة
        $quotation->update([
            'subtotal' => $subtotal,
            'total_discount' => $total_discount,
            'total_tax' => $total_tax,
            'grand_total' => $grand_total,
            'shipping_cost' => $shipping_cost,
            'adjustment_amount' => $adjustment_amount,
            'adjustment_label' => $request->adjustment_label,
            'notes' => $request->notes,
        ]);

        // حفظ تفاصيل الخصم الإضافي إذا وجد
        if ($additional_discount > 0) {
            TaxInvoice::create([
                'name' => 'خصم إضافي',
                'invoice_id' => $quotation->id,
                'type' => 'discount',
                'rate' => $request->discount_type == 'percentage' ? floatval($request->discount_amount) : 0,
                'value' => $additional_discount,
                'type_invoice' => 'quotation_purchase',
            ]);
        }

        // حفظ تفاصيل التسوية إذا وجدت
        if ($adjustment_amount != 0) {
            TaxInvoice::create([
                'name' => $request->adjustment_label ?? 'تسوية',
                'invoice_id' => $quotation->id,
                'type' => $request->adjustment_type,
                'rate' => 0,
                'value' => abs($adjustment_amount),
                'type_invoice' => 'quotation_purchase',
            ]);
        }

        // تحديث حالة عرض السعر الأصلي إلى مسعر (2) إذا كان موجودًا
        if ($originalQuotation) {
            $originalQuotation->update([
                'status' => 'approval', // مسعر
                'updated_by' => Auth::id(),
            ]);
        }

        // الحصول على بيانات المورد للسجل
        $supplier = \App\Models\Supplier::find($validatedData['supplier_id']);

        // تصحيح إنشاء السجل
        ModelsLog::create([
            'type' => 'quotation_view',
            'type_id' => $quotation->id,
            'type_log' => 'log',
            'icon' => 'create',
            'description' => sprintf(
                'تم انشاء عرض سعر شراء رقم **%s** للمورد **%s**',
                $quotation->code ?? '',
                $supplier->trade_name ?? ''
            ),
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()
            ->route('pricesPurchase.index')
            ->with('success', 'تم إنشاء عرض السعر بنجاح' . ($originalQuotation ? ' وتحديث حالة عرض السعر الأصلي' : ''));

    } catch (\Exception $e) {
        DB::rollback();
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'حدث خطأ أثناء إنشاء عرض السعر: ' . $e->getMessage());
    }
}
    public function update(Request $request, $id)
    {
        try {
            $quotation = PurchaseQuotationView::findOrFail($id);

            // التحقق من البيانات
            $rules = [
                'supplier_id' => 'required|exists:suppliers,id',
                'code' => 'required|string|unique:purchase_quotations_view,code,' . $id,
                'date' => 'required|date',
                'valid_days' => 'nullable|integer|min:0',
                'status' => 'nullable|string|in:Under Review,approval,disagree',

                // قواعد الخصم والتسوية
                'discount_amount' => 'nullable|numeric|min:0',
                'discount_type' => 'nullable|in:amount,percentage',
                'adjustment_label' => 'nullable|string|max:255',
                'adjustment_type' => 'nullable|in:discount,addition',
                'adjustment_value' => 'nullable|numeric',
                // قواعد الشحن
                'shipping_cost' => 'nullable|numeric|min:0',
                'tax_id' => 'nullable|exists:tax_sittings,id',
            ];

            $validator = Validator::make($request->all(), $rules);
            $validatedData = $validator->validated();

            DB::beginTransaction();

            // تحديث بيانات عرض السعر الأساسية
            $quotation->update([
                'supplier_id' => $validatedData['supplier_id'],
                'code' => $validatedData['code'],
                'date' => $validatedData['date'],
                'valid_days' => $validatedData['valid_days'] ?? 0,
                'status' => $validatedData['status'],
                'created_by' => Auth::id(),
            ]);

            // متغيرات لحساب المبالغ
            $subtotal = 0;
            $total_item_tax = 0;
            $total_item_discount = 0;
            $taxDetails = [];

            // حذف العناصر القديمة والضرائب المرتبطة بها
            InvoiceItem::where('quotes_purchase_order_id', $quotation->id)->delete();
            TaxInvoice::where('invoice_id', $quotation->id)->where('type_invoice', 'quotation_purchase')->delete();

            // معالجة عناصر الفاتورة الجديدة
            if ($request->has('items')) {
                $invoiceItems = [];

                foreach ($request->items as $item) {
                    $product = Product::find($item['product_id']);

                    // حساب المجموع الفرعي لكل منتج
                    $item_subtotal = floatval($item['quantity']) * floatval($item['unit_price']);
                    $subtotal += $item_subtotal;

                    // حساب خصم العنصر
                    $item_discount = 0;
                    if (isset($item['discount_type']) && isset($item['discount_amount'])) {
                        if ($item['discount_type'] == 'percentage') {
                            $item_discount = ($item_subtotal * floatval($item['discount_percentage'] ?? 0)) / 100;
                        } else {
                            $item_discount = floatval($item['discount'] ?? 0);
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
                                $tax_1_value = $item_subtotal - $item_subtotal / (1 + $tax_1_rate / 100);
                            } else {
                                $tax_1_value = ($item_subtotal * $tax_1_rate) / 100;
                            }
                            $item_tax_total += $tax_1_value;

                            if (!isset($taxDetails[$tax_1->name])) {
                                $taxDetails[$tax_1->name] = 0;
                            }
                            $taxDetails[$tax_1->name] += $tax_1_value;

                            TaxInvoice::create([
                                'name' => $tax_1->name,
                                'invoice_id' => $quotation->id,
                                'type' => $tax_1->type,
                                'rate' => $tax_1->tax,
                                'value' => $tax_1_value,
                                'type_invoice' => 'quotation_purchase',
                            ]);
                        }
                    }

                    // معالجة الضريبة الثانية
                    if ($tax_2_rate > 0 && !empty($item['tax_2_id'])) {
                        $tax_2 = TaxSitting::find($item['tax_2_id']);
                        if ($tax_2) {
                            $tax_2_value = 0;
                            if ($tax_2->type === 'included') {
                                $tax_2_value = $item_subtotal - $item_subtotal / (1 + $tax_2_rate / 100);
                            } else {
                                $tax_2_value = ($item_subtotal * $tax_2_rate) / 100;
                            }
                            $item_tax_total += $tax_2_value;

                            if (!isset($taxDetails[$tax_2->name])) {
                                $taxDetails[$tax_2->name] = 0;
                            }
                            $taxDetails[$tax_2->name] += $tax_2_value;

                            TaxInvoice::create([
                                'name' => $tax_2->name,
                                'invoice_id' => $quotation->id,
                                'type' => $tax_2->type,
                                'rate' => $tax_2->tax,
                                'value' => $tax_2_value,
                                'type_invoice' => 'quotation_purchase',
                            ]);
                        }
                    }

                    $total_item_tax += $item_tax_total;

                    // حساب إجمالي العنصر (بعد الخصم)
                    $item_total = $item_subtotal - $item_discount;

                    // إضافة المنتج للمصفوفة
                    $invoiceItems[] = [
                        'quotes_purchase_order_id' => $quotation->id,
                        'product_id' => $item['product_id'],
                        'item' => $product->name ?? 'المنتج ' . $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'discount' => $item_discount,
                        'tax_1' => $tax_1_rate,
                        'tax_2' => $tax_2_rate,
                        'total' => $item_total,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // إضافة البنود الجديدة
                InvoiceItem::insert($invoiceItems);
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

            // حساب التسوية
            $adjustment_amount = 0;
            if ($request->adjustment_value && $request->adjustment_type) {
                $adjustment_value = floatval($request->adjustment_value);
                if ($request->adjustment_type == 'discount') {
                    $adjustment_amount = -$adjustment_value;
                } else {
                    $adjustment_amount = $adjustment_value;
                }
            }

            // حساب تكلفة الشحن وضريبتها
            $shipping_cost = floatval($request->shipping_cost ?? 0);
            $shipping_tax = 0;
            if ($shipping_cost > 0 && $request->tax_id) {
                $shipping_tax_rate = TaxSitting::find($request->tax_id);
                if ($shipping_tax_rate) {
                    $shipping_tax = ($shipping_cost * $shipping_tax_rate->tax) / 100;

                    TaxInvoice::create([
                        'name' => $shipping_tax_rate->name . ' (شحن)',
                        'invoice_id' => $quotation->id,
                        'type' => $shipping_tax_rate->type,
                        'rate' => $shipping_tax_rate->tax,
                        'value' => $shipping_tax,
                        'type_invoice' => 'quotation_purchase',
                    ]);
                }
            }

            // حساب إجمالي الخصومات
            $total_discount = $total_item_discount + $additional_discount;

            // حساب إجمالي الضرائب
            $total_tax = $total_item_tax + $shipping_tax;

            // حساب المجموع النهائي
            $grand_total = $subtotal - $total_discount + $adjustment_amount + $shipping_cost + $total_tax;

            // تحديث عرض السعر بالمبالغ المحسوبة
            $quotation->update([
                'subtotal' => $subtotal,
                'total_discount' => $total_discount,
                'total_tax' => $total_tax,
                'grand_total' => $grand_total,
                'shipping_cost' => $shipping_cost,
                'adjustment_amount' => $adjustment_amount,
                'adjustment_label' => $request->adjustment_label,
                'notes' => $request->notes,
            ]);

            // حفظ تفاصيل الخصم الإضافي إذا وجد
            if ($additional_discount > 0) {
                TaxInvoice::create([
                    'name' => 'خصم إضافي',
                    'invoice_id' => $quotation->id,
                    'type' => 'discount',
                    'rate' => $request->discount_type == 'percentage' ? floatval($request->discount_amount) : 0,
                    'value' => $additional_discount,
                    'type_invoice' => 'quotation_purchase',
                ]);
            }

            // حفظ تفاصيل التسوية إذا وجدت
            if ($adjustment_amount != 0) {
                TaxInvoice::create([
                    'name' => $request->adjustment_label ?? 'تسوية',
                    'invoice_id' => $quotation->id,
                    'type' => $request->adjustment_type,
                    'rate' => 0,
                    'value' => abs($adjustment_amount),
                    'type_invoice' => 'quotation_purchase',
                ]);
            }
            ModelsLog::create([
                'type' => 'quotation_view',
                'type_id' => $quotation->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط

                'icon' => 'create',
                'description' => sprintf(
                    'تم تحديث امر شراء رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للمورد **%s**',
                    $purchaseOrderRequest->code ?? '', // رقم طلب الشراء
                    $quotation->product->name ?? '', // اسم المنتج
                    $item['quantity'] ?? '', // الكمية
                    $item['unit_price'] ?? '', // السعر
                    $Supplier->trade_name ?? '', // المورد (يتم استخدام %s للنصوص)
                ),
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
            DB::commit();

            return redirect()->route('pricesPurchase.show', $quotation->id)->with('success', 'تم تحديث عرض السعر بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث عرض السعر: ' . $e->getMessage());
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء تحديث عرض السعر: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $purchaseQuotation = PurchaseQuotationView::with(['supplier', 'account', 'items.product'])->findOrFail($id);
        $suppliers = Supplier::all();
        $taxs = TaxSitting::all();
        $accounts = Account::all();
        $items = Product::all();
        return view('purchases::purchases.view_purchase_price.edit', compact('purchaseQuotation', 'taxs', 'suppliers', 'accounts', 'items'));
    }
    public function destroy($id)
    {
        $quotation = PurchaseQuotationView::findOrFail($id);
        $quotation->delete();
        return redirect()->route('pricesPurchase.index')->with('success', 'تم حذف عرض السعر بنجاح');
    }
    public function updateStatus($id, Request $request)
    {
        try {
            $quotation = PurchaseQuotationView::findOrFail($id);

            // التحقق من الحالة المرسلة
            if (!in_array($request->status, [2, 3])) {
                return back()->with('error', 'حالة غير صالحة');
            }

            // تحديث الحالة
            $quotation->status = $request->status;
            $quotation->save();

            // رسالة نجاح مخصصة حسب الحالة
            $message = $request->status == 2 ? 'تمت الموافقة على عرض السعر بنجاح' : 'تم رفض عرض السعر';

            return redirect()->route('pricesPurchase.show', $id)->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تحديث الحالة: ' . $e->getMessage());
        }
    }
    public function exportPDF($id)
    {
        try {
            $purchaseQuotation = PurchaseQuotationView::with(['supplier', 'account', 'items.product', 'creator'])->findOrFail($id);

            $pdf = Pdf::loadView('purchases::purc hases.view_purchase_price.pdf', compact('purchaseQuotation'));

            return $pdf->download('عرض-سعر-' . $purchaseQuotation->code . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء تصدير PDF: ' . $e->getMessage());
        }
    }

    public function convertToPurchaseOrder($id, Request $request)
{
    try {
        // جلب عرض السعر مع جميع البيانات المطلوبة
        $quotation = PurchaseQuotationView::with(['items', 'taxes'])->findOrFail($id);

        if ($quotation->status != "approval") {
            return back()->with('error', 'لا يمكن تحويل عرض السعر غير المعتمد إلى أمر شراء');
        }

        if (!$request->supplier_id) {
            return back()->with('error', 'يرجى اختيار المورد');
        }

        DB::beginTransaction();

        // إنشاء أمر شراء جديد مع نسخ جميع البيانات من عرض السعر
        $purchaseOrder = PurchaseInvoice::create([
            'supplier_id' => $request->supplier_id,
            'account_id' => $quotation->account_id,
            'quotation_id' => $quotation->id,
            'code' => 'PO-' . date('Ym') . '-' . str_pad(PurchaseInvoice::where('type', 1)->count() + 1, 4, '0', STR_PAD_LEFT),
            'date' => now(),
            'delivery_date' => now()->addDays(7),
            'type' => "Requested", // نوع أمر شراء
            'status' => 1, // 1 = تحت المراجعة

            // نسخ جميع المبالغ المحسوبة من عرض السعر
            'subtotal' => $quotation->subtotal ?? 0,
            'total_discount' => $quotation->total_discount ?? 0,
            'total_tax' => $quotation->total_tax ?? 0,
            'grand_total' => $quotation->grand_total ?? 0,
            'shipping_cost' => $quotation->shipping_cost ?? 0,
            'adjustment_amount' => $quotation->adjustment_amount ?? 0,
            'adjustment_label' => $quotation->adjustment_label,

            // بيانات إضافية
            'notes' => $quotation->notes,
            'payment_terms' => $quotation->payment_terms ?? '',
            'currency' => $quotation->currency ?? 'SAR',
            'created_by' => Auth::id(),
        ]);

        // نسخ عناصر عرض السعر إلى بنود أمر الشراء
        $items = $quotation->items()->get();
        $invoiceItems = [];

        foreach ($items as $item) {
            $invoiceItems[] = [
                'purchase_invoice_id_type' => $purchaseOrder->id,
                'quotes_purchase_order_id' => $quotation->id,
                'product_id' => $item->product_id,
                'item' => $item->item,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'discount' => $item->discount ?? 0,
                'tax_1' => $item->tax_1 ?? 0,
                'tax_2' => $item->tax_2 ?? 0,
                'total' => $item->total,
                'description' => $item->description ?? '',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // إدراج جميع العناصر مرة واحدة لتحسين الأداء
        if (!empty($invoiceItems)) {
            DB::table('invoice_items')->insert($invoiceItems);
        }

        // نسخ جميع الضرائب والخصومات من جدول TaxInvoice
        $taxInvoices = TaxInvoice::where('invoice_id', $quotation->id)
            ->where('type_invoice', 'quotation_purchase')
            ->get();

        foreach ($taxInvoices as $taxInvoice) {
            TaxInvoice::create([
                'name' => $taxInvoice->name,
                'invoice_id' => $purchaseOrder->id,
                'type' => $taxInvoice->type,
                'rate' => $taxInvoice->rate,
                'value' => $taxInvoice->value,
                'type_invoice' => 'purchase_order', // تغيير النوع إلى أمر شراء
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // تحديث حالة عرض السعر
        $quotation->update([
            'converted_to_po' => true,
            'status' => "approval", // حالة محول إلى أمر شراء
            // 'updated_by' => Auth::id(),
        ]);

        // الحصول على بيانات المورد للسجل
        $supplier = \App\Models\Supplier::find($request->supplier_id);

        // إنشاء سجل للعملية
        ModelsLog::create([
            'type' => 'purchase_order',
            'type_id' => $purchaseOrder->id,
            'type_log' => 'log',
            'icon' => 'convert',
            'description' => sprintf(
                'تم تحويل عرض السعر رقم **%s** إلى أمر شراء رقم **%s** للمورد **%s**',
                $quotation->code ?? '',
                $purchaseOrder->code ?? '',
                $supplier->trade_name ?? ''
            ),
            'created_by' => Auth::id(),
        ]);

        DB::commit();

        return redirect()
            ->route('purchase-orders.show', $purchaseOrder->id)
            ->with('success', 'تم تحويل عرض السعر إلى أمر شراء بنجاح');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('خطأ في تحويل عرض السعر: ' . $e->getMessage());
        return back()->with('error', 'حدث خطأ أثناء تحويل عرض السعر: ' . $e->getMessage());
    }
}

    public function approve(Request $request, $id)
    {
        $PurchaseQuotation = PurchaseQuotationView::findOrFail($id);

        DB::beginTransaction();
        try {
            $PurchaseQuotation->update([
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
        $PurchaseQuotation = PurchaseQuotationView::findOrFail($id);

        DB::beginTransaction();
        try {
            $PurchaseQuotation->update([
                'status' => 'disagree',
                'rejected_by' => auth()->id(),
                'rejected_at' => now(),
            ]);
ModelsLog::create([
    'user_id' => auth()->id(),
    'type' => 'PurchaseQuotation',
    'type_id' => $PurchaseQuotation->id,
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
        $PurchaseQuotation = PurchaseQuotationView::findOrFail($id);

        // التحقق من أن الطلب معتمد أصلاً
        if ($PurchaseQuotation->status !== 'approval') {
            return redirect()->back()->with('error', 'لا يمكن إلغاء الموافقة، الطلب غير معتمد');
        }

        DB::beginTransaction();
        try {
            $PurchaseQuotation->update([
                'status' => 'Under Review',
                'approved_by' => null,
                'approved_at' => null,
                'approval_note' => null,
                'cancelled_by' => auth()->id(),
                'cancelled_at' => now(),
            ]);

            // إضافة سجل في النشاطات


ModelsLog::create([
    'type' => 'purchase_quotation',
    'type_id' => $PurchaseQuotation->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم إلغاء الموافقة على طلب شراء رقم **' . $PurchaseQuotation->code . '**', // النص المنسق
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
        $PurchaseQuotation = PurchaseQuotationView::findOrFail($id);

        // التحقق من أن الطلب مرفوض أصلاً
        if ($PurchaseQuotation->status !== 'disagree') {
            return redirect()->back()->with('error', 'لا يمكن التراجع عن الرفض، الطلب غير مرفوض');
        }

        DB::beginTransaction();
        try {
            $PurchaseQuotation->update([
                'status' => 'Under Review',
                'rejected_by' => null,
                'rejected_at' => null,
                'restored_by' => auth()->id(),
                'restored_at' => now(),
            ]);
ModelsLog::create([
    'user_id' => auth()->id(),
    'description' => 'تم التراجع عن الرفض',
    'type' => 'PurchaseQuotation',
    'type_id' => $PurchaseQuotation->id

]);

            DB::commit();
            return redirect()->back()->with('success', 'تم التراجع عن الرفض بنجاح وإعادة الطلب للمراجعة');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'حدث خطأ أثناء التراجع عن الرفض');
        }
    }
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

        $purchaseQuotation = PurchaseQuotationView::findOrFail($id);

        // التعامل مع المرفق إذا تم رفعه
        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $attachmentPath = $file->storeAs('purchase_quotations/notes', $fileName, 'public');
        }

        // إنشاء الملاحظة مع تحديد النوع مباشرة
$clientRelation = ClientRelation::create([
    'process' => $request->process,
    'time' => $request->time,
    'date' => $request->date,
    'quotation_id' => $id,
    'employee_id' => auth()->user()->id,
    'description' => $request->description,
    'attachment' => $attachmentPath,
    'type' => $request->input('type', 'quotation'), // قيمة افتراضية
]);
        // تسجيل النشاط في سجل الأنشاطات
        ModelsLog::create([
            'type' => 'quotation_view',
            'type_id' => $purchaseQuotation->id,
            'type_log' => 'log',
            'icon' => 'create',
            'description' => sprintf(
                'تم اضافة ملاحظة جديدة لعرض السعر رقم %s',
                $purchaseQuotation->code ?? ''
            ),
            'created_by' => auth()->id(),
        ]);
notifications::create([
    'user_id' => auth()->user()->id,
    'title' => 'ملاحظة جديدة',
    'message' => 'تم اضافة ملاحظة جديدة لعرض السعر رقم ' . $purchaseQuotation->code,
'description' => $request->description
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
                'attachment' => $attachmentPath ? asset('storage/' . $attachmentPath) : null,
            ],
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء حفظ الملاحظة: ' . $e->getMessage(),
        ], 500);
    }
}
public function getNotes($id)
{
    try {
        $notes = ClientRelation::where('quotation_id', $id)
            ->where('type', 'quotation')
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
            'notes' => $formattedNotes->toArray(), // تحويل إلى array
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب الملاحظات: ' . $e->getMessage(),
        ], 500);
    }



}
public function destroyNote($id)
{
    try {
        $note = ClientRelation::findOrFail($id);
        $note->delete();
        return response()->json([
            'success' => true,
            'message' => 'تم حذف الملاحظة بنجاح',
        ]);

ModelsLog::create([
    'type' => 'quotation_view',
    'type_id' => $note->quotation_id,
    'type_log' => 'log',
    'icon' => 'delete',
    'description' => sprintf(
        'تم حذف ملاحظة عرض السعر رقم %s',
        $note->quotation_id ?? ''
    ),
    'created_by' => auth()->id(),
]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطاء اثناء حذف الملاحظة: ' . $e->getMessage(),
        ], 500);
    }
}
}
