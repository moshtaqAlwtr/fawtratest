<?php

namespace Modules\Sales\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\Client;
use App\Models\InvoiceItem;
use App\Models\PeriodicInvoice;
use App\Models\Product;
use App\Models\AccountSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PeriodicInvoicesController extends Controller
{
    public function index(Request $request)
    {
        $query = PeriodicInvoice::query();

        // البحث حسب اسم الاشتراك
        if ($request->filled('name_subscription')) {
            $query->where('details_subscription', 'like', '%' . $request->name_subscription . '%');
        }

        // البحث حسب العميل
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        // البحث حسب نوع التكرار (تخصيص)
        if ($request->filled('repeat_type')) {
            $query->where('repeat_type', $request->repeat_type);
        }

        // البحث حسب التاريخ (من)
        if ($request->filled('from_date')) {
            $query->where('first_invoice_date', '>=', $request->from_date);
        }

        // البحث حسب التاريخ (إلى)
        if ($request->filled('to_date')) {
            $query->where('first_invoice_date', '<=', $request->to_date);
        }

        // البحث حسب الإجمالي (أكبر من)
        if ($request->filled('min_total')) {
            $query->where('grand_total', '>=', $request->min_total);
        }

        // البحث حسب الإجمالي (أصغر من)
        if ($request->filled('max_total')) {
            $query->where('grand_total', '<=', $request->max_total);
        }

        // تنفيذ الاستعلام مع علاقة العميل
        $periodicInvoices = $query->with('client')->latest()->paginate(10);

        // جلب قوائم العملاء والمنتجات
        $clients = Client::all();
        $items = Product::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        return view('sales::periodic_invoices.index', compact('periodicInvoices', 'clients','account_setting', 'items'));
    }
    public function create()
    {
        $items = Product::all();
        $periodicInvoices = PeriodicInvoice::all();
        $clients = Client::all();
          $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::periodic_invoices.create', compact('periodicInvoices', 'clients','account_setting', 'items'));
    }

    public function show($id)
    {
        $periodicInvoice = PeriodicInvoice::with(['instances', 'instances.invoice'])->findOrFail($id);

        // حساب إجمالي المبالغ المدفوعة وغير المدفوعة
        $periodicInvoice->paid_amount = $periodicInvoice->instances
            ->sum(function($instance) {
                return $instance->invoice->payment_status === 'paid' ? $instance->invoice->grand_total : 0;
            });

        $periodicInvoice->unpaid_amount = $periodicInvoice->instances
            ->sum(function($instance) {
                return $instance->invoice->payment_status !== 'paid' ? $instance->invoice->grand_total : 0;
            });

  $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::periodic_invoices.show', compact('periodicInvoice','account_setting'));
    }

    /**
     * Store a newly created periodic invoice in storage.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات باستخدام helper function
        $validated = validator($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'first_invoice_date' => 'required|date',
            'repeat_type' => 'required|integer|in:1,2,3,4,5',
            'repeat_interval' => 'nullable|integer|min:1',
            'repeat_count' => 'required|integer|min:1',
            'details_subscription' => 'nullable|string',
            'before_days' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string',
            'send_copy' => 'boolean',
            'show_dates' => 'boolean',
            'disable_partial' => 'boolean',
            'is_active' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:amount,percentage',
            'items.*.tax_1' => 'nullable|numeric|min:0',
            'items.*.tax_2' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_type' => 'required|in:1,2,3', // 1=vat, 2=zero, 3=exempt
            'tax_rate' => 'nullable|numeric|min:0', // إضافة حقل tax_rate
            'notes' => 'nullable|string',
        ])->validate();

        // تحويل tax_type إلى النص المناسب
        $tax_type_map = [
            '1' => 'vat',
            '2' => 'zero',
            '3' => 'exempt',
        ];

        // بدء العملية داخل ترانزاكشن
        DB::beginTransaction();

        try {
            // ** الخطوة الأولى: إنشاء كود للفاتورة **
            $invoice_number = $request->input('invoice_number');
            if (!$invoice_number) {
                $lastOrder = PeriodicInvoice::orderBy('id', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->invoice_number) + 1 : 1;
                $invoice_number = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $existingCode = PeriodicInvoice::where('invoice_number', $invoice_number)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة الدورية موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            }

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **
            foreach ($validated['items'] as $item) {
                // جلب المنتج
                $product = Product::findOrFail($item['product_id']);

                // حساب تفاصيل الكمية والأسعار
                $quantity = floatval($item['quantity']);
                $unit_price = floatval($item['unit_price']);
                $item_total = $quantity * $unit_price;

                // حساب الخصم للبند
                $item_discount = 0; // قيمة الخصم المبدئية
                if (isset($item['discount']) && $item['discount'] > 0) {
                    $discountType = $item['discount_type'] ?? 'amount';
                    if ($discountType === 'percentage') {
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
                    'periodic_invoice_id' => null, // سيتم تعيينه لاحقاً بعد إنشاء الفاتورة
                    'product_id' => $item['product_id'],
                    'item' => $product->name,
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

            // ** الخطوة الثالثة: حساب الخصم الإضافي للفاتورة ككل **
            $quote_discount = floatval($validated['discount_amount'] ?? 0);
            $discountType = $validated['discount_type'] ?? 'amount';
            if ($discountType === 'percentage') {
                $quote_discount = ($total_amount * $quote_discount) / 100;
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $quote_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            // ** حساب الضرائب **
            $total_tax = 0;
            $tax_type = $tax_type_map[$validated['tax_type']];
            $tax_rate = floatval($validated['tax_rate'] ?? 0); // الحصول على نسبة الضريبة من المستخدم

            if ($tax_type === 'vat' && $tax_rate > 0) {
                // حساب الضريبة على المبلغ بعد الخصم باستخدام النسبة التي أدخلها المستخدم
                $total_tax = ($amount_after_discount * $tax_rate) / 100;
            }

            // ** إضافة تكلفة الشحن (إذا وجدت) **
            $shipping_cost = floatval($validated['shipping_cost'] ?? 0);

            // ** حساب ضريبة الشحن (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($tax_type === 'vat' && $tax_rate > 0) {
                $shipping_tax = ($shipping_cost * $tax_rate) / 100; // ضريبة الشحن باستخدام النسبة التي أدخلها المستخدم
            }

            // ** إضافة ضريبة الشحن إلى total_tax **
            $total_tax += $shipping_tax;

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost;

            // ** الخطوة الرابعة: إنشاء الفاتورة في قاعدة البيانات **
            $periodicInvoice = PeriodicInvoice::create([
                'client_id' => $validated['client_id'],
                'invoice_number' => $invoice_number,
                'first_invoice_date' => $validated['first_invoice_date'],
                'repeat_type' => $validated['repeat_type'],
                'repeat_interval' => $validated['repeat_interval'],
                'repeat_count' => $validated['repeat_count'],
                'details_subscription' => $validated['details_subscription'] ?? null,
                'before_days' => $validated['before_days'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'send_copy' => $validated['send_copy'] ?? false,
                'show_dates' => $validated['show_dates'] ?? false,
                'disable_partial' => $validated['disable_partial'] ?? false,
                'is_active' => $validated['is_active'] ?? true,
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
                'discount_amount' => $quote_discount,
                'discount_type' => $discountType === 'percentage' ? 2 : 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $validated['tax_type'],
                'tax_rate' => $tax_rate, // حفظ نسبة الضريبة
                'subtotal' => $total_amount,
                'total' => $amount_after_discount,
                'total_discount' => $final_total_discount,
                'total_tax' => $total_tax,
                'grand_total' => $total_with_tax,
                'status' => 1, // حالة الفاتورة (1: Draft)
            ]);

            // ** الخطوة الخامسة: إنشاء سجلات البنود (items) للفاتورة **
            foreach ($items_data as $item) {
                $item['periodic_invoice_id'] = $periodicInvoice->id;
                InvoiceItem::create($item);
            }

            DB::commit();
            return redirect()->route('periodic_invoices.index')->with('success', 'تم إنشاء فاتورة دورية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('حدث خطأ في دالة store: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء حفظ فاتورة دورية: ' . $e->getMessage());
        }
    }
    /**
     * عرض نموذج تعديل الفاتورة الدورية
     */
    public function edit($id)
    {
        // جلب الفاتورة المحددة مع علاقاتها
        $periodicInvoice = PeriodicInvoice::with(['items.product', 'client'])->findOrFail($id);

        // جلب قوائم العملاء والمنتجات
        $clients = Client::all();
        $items = Product::all();

        return view('sales::periodic_invoices.edit', compact('periodicInvoice', 'clients', 'items'));
    }

    /**
     * تحديث الفاتورة الدورية في قاعدة البيانات
     */
    public function update(Request $request, $id)
    {
        // التحقق من صحة البيانات
        $validated = validator($request->all(), [
            'client_id' => 'nullable|exists:clients,id',
            'first_invoice_date' => 'nullable|date',
            'repeat_type' => 'nullable|integer|in:1,2,3,4,5',
            'repeat_interval' => 'nullable|integer|min:1',
            'repeat_count' => 'nullable|integer|min:1',
            'details_subscription' => 'nullable|string',
            'before_days' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string',
            'send_copy' => 'nullable|boolean',
            'show_dates' => 'nullable|boolean',
            'disable_partial' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'items' => 'nullable|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.quantity' => 'nullable|numeric|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.discount_type' => 'nullable|in:amount,percentage',
            'items.*.tax_1' => 'nullable|numeric|min:0',
            'items.*.tax_2' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:amount,percentage',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_type' => 'nullable|in:1,2,3',
            'notes' => 'nullable|string',
        ])->validate();

        // بدء العملية داخل ترانزاكشن
        DB::beginTransaction();

        try {
            // البحث عن الفاتورة الدورية
            $periodicInvoice = PeriodicInvoice::findOrFail($id);

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = $periodicInvoice->subtotal; // استخدام القيم القديمة
            $total_discount = $periodicInvoice->total_discount; // استخدام القيم القديمة
            $items_data = []; // تجميع بيانات البنود

            // ** معالجة البنود (items) **
            if (isset($validated['items'])) {
                foreach ($validated['items'] as $item) {
                    // جلب المنتج مع التحقق من وجوده
                    $product = Product::find($item['product_id']);

                    // إذا لم يتم العثور على المنتج، نستمر إلى العنصر التالي
                    if (!$product) {
                        continue;
                    }

                    // حساب تفاصيل الكمية والأسعار
                    $quantity = floatval($item['quantity']);
                    $unit_price = floatval($item['unit_price']);
                    $item_total = $quantity * $unit_price;

                    // حساب الخصم للبند
                    $item_discount = 0;
                    if (isset($item['discount']) && $item['discount'] > 0) {
                        $discountType = $item['discount_type'] ?? 'amount';
                        if ($discountType === 'percentage') {
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
                        'periodic_invoice_id' => $periodicInvoice->id,
                        'product_id' => $product->id,
                        'item' => $product->name,
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
            $quote_discount = floatval($validated['discount_amount'] ?? $periodicInvoice->discount_amount);
            $discountType = $validated['discount_type'] ?? ($periodicInvoice->discount_type == 2 ? 'percentage' : 'amount');
            if ($discountType === 'percentage') {
                $quote_discount = ($total_amount * $quote_discount) / 100;
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $quote_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            // ** حساب الضرائب **
            $tax_type = $validated['tax_type'] ?? $periodicInvoice->tax_type;
            $total_tax = 0;
            if ($tax_type == 1) { // VAT
                $total_tax = $amount_after_discount * 0.15;
            }

            // ** إضافة تكلفة الشحن **
            $shipping_cost = floatval($validated['shipping_cost'] ?? $periodicInvoice->shipping_cost);

            // ** حساب ضريبة الشحن **
            $shipping_tax = 0;
            if ($tax_type == 1) { // VAT
                $shipping_tax = $shipping_cost * 0.15;
            }

            // ** إضافة ضريبة الشحن إلى total_tax **
            $total_tax += $shipping_tax;

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost;

            // ** تحديث الفاتورة في قاعدة البيانات **
            $periodicInvoice->update([
                'client_id' => $validated['client_id'] ?? $periodicInvoice->client_id,
                'first_invoice_date' => $validated['first_invoice_date'] ?? $periodicInvoice->first_invoice_date,
                'repeat_type' => $validated['repeat_type'] ?? $periodicInvoice->repeat_type,
                'repeat_interval' => $validated['repeat_interval'] ?? $periodicInvoice->repeat_interval,
                'repeat_count' => $validated['repeat_count'] ?? $periodicInvoice->repeat_count,
                'details_subscription' => $validated['details_subscription'] ?? $periodicInvoice->details_subscription,
                'before_days' => $validated['before_days'] ?? $periodicInvoice->before_days,
                'payment_terms' => $validated['payment_terms'] ?? $periodicInvoice->payment_terms,
                'send_copy' => $validated['send_copy'] ?? $periodicInvoice->send_copy,
                'show_dates' => $validated['show_dates'] ?? $periodicInvoice->show_dates,
                'disable_partial' => $validated['disable_partial'] ?? $periodicInvoice->disable_partial,
                'is_active' => $validated['is_active'] ?? $periodicInvoice->is_active,
                'notes' => $validated['notes'] ?? $periodicInvoice->notes,
                'discount_amount' => $quote_discount,
                'discount_type' => $discountType === 'percentage' ? 2 : 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $tax_type,
                'subtotal' => $total_amount,
                'total' => $amount_after_discount,
                'total_discount' => $final_total_discount,
                'total_tax' => $total_tax,
                'grand_total' => $total_with_tax,
            ]);

            // ** تحديث البنود **
            if (isset($validated['items'])) {
                $periodicInvoice->items()->delete(); // حذف البنود القديمة
                $periodicInvoice->items()->createMany($items_data); // إضافة البنود الجديدة
            }

            DB::commit();
            return redirect()->route('periodic_invoices.index')->with('success', 'تم تحديث الفاتورة الدورية بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('حدث خطأ في دالة update: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء تحديث الفاتورة الدورية: ' . $e->getMessage());
        }
    }
    /**
     * Generate a unique invoice number
     */
public function destroy ($id)

{
    PeriodicInvoice::destroy($id);
    return redirect()->route('periodic_invoices.index')
        ->with('success', 'تم حذف الفاتورة الدورية بنجاح');

}

}
