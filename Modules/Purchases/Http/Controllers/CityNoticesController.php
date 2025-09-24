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
use App\Models\PurchaseInvoiceSetting;
use App\Models\TreasuryEmployee;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CityNoticesController extends Controller
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
        $cityNotices = $this->getFilteredData($request, false);

        return view('purchases::purchases.city_notices.index', compact('cityNotices', 'taxes', 'suppliers', 'users', 'account_setting'));
    }

    private function getFilteredData(Request $request, $returnJson = true)
    {
        $query = PurchaseInvoice::query()
            ->with(['supplier', 'creator'])
            ->where('type', 'City Notice');

        // تطبيق الفلاتر
        $this->applyFilters($query, $request);

        // ترتيب النتائج
        $query->orderBy('created_at', 'desc');

        // الحصول على النتائج مع التقسيم إلى صفحات
        $cityNotices = $query->paginate(30);

        if ($returnJson) {
            $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

            return response()->json([
                'success' => true,
                'data' => view('purchases::purchases.city_notices.partials.table', compact('cityNotices', 'account_setting'))->render(),
                'total' => $cityNotices->total(),
                'current_page' => $cityNotices->currentPage(),
                'last_page' => $cityNotices->lastPage(),
                'from' => $cityNotices->firstItem(),
                'to' => $cityNotices->lastItem(),
            ]);
        }

        return $cityNotices;
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

        return redirect()->route('CityNotices.index');
    }
   public function create()
{

    if (!PurchaseInvoiceSetting::isSettingActive('enable_debit_notice')) {
        return redirect()->route('CityNotices.index')
            ->with('error', 'عذراً، ميزة الإشعار الدائن غير مفعلة حالياً.');
    }
    $suppliers = Supplier::all();
    $items = Product::all();
    $taxs = TaxSitting::all();
    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

    $availableInvoices = PurchaseInvoice::where('type', 'invoice')
        ->with(['supplier'])
        ->orderBy('created_at', 'desc')
        ->get();
    return view('purchases::purchases.city_notices.create', compact('suppliers', 'taxs', 'account_setting', 'items', 'availableInvoices'));
}

public function createFromInvoice($invoiceId)
{
    $originalInvoice = PurchaseInvoice::with(['supplier', 'items.product'])->findOrFail($invoiceId);

    if ($originalInvoice->type !== 'invoice') {
        return redirect()->back()->with('error', 'يمكن إنشاء اشعار مدين فقط من الفواتير العادية');
    }

    // فحص إذا كانت الفاتورة مستخدمة في مرتجع
    $hasReturnInvoice = PurchaseInvoice::where('reference_id', $invoiceId)
        ->where('type', 'Return')
        ->exists();

    if ($hasReturnInvoice) {
        return redirect()->back()->with('error', 'لا يمكن إنشاء إشعار دائن لهذه الفاتورة لأنه تم إنشاء فاتورة مرتجعة لها مسبقاً');
    }

    $suppliers = Supplier::all();
    $items = Product::all();
    $taxs = TaxSitting::all();
    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

    return view('purchases::purchases.city_notices.create', compact('suppliers', 'taxs', 'account_setting', 'items', 'originalInvoice'));
}

public function getInvoiceDetails($invoiceId)
{
    try {
        $invoice = PurchaseInvoice::with(['supplier', 'items.product'])->findOrFail($invoiceId);

        if ($invoice->type !== 'invoice') {
            return response()->json(['error' => 'يمكن إنشاء اشعار مدين فقط من الفواتير العادية'], 400);
        }

        // فحص إذا كانت الفاتورة مستخدمة في مرتجع
        $hasReturnInvoice = PurchaseInvoice::where('reference_id', $invoiceId)
            ->where('type', 'return')
            ->exists();

        if ($hasReturnInvoice) {
            return response()->json(['error' => 'لا يمكن إنشاء إشعار دائن لهذه الفاتورة لأنه تم إنشاء فاتورة مرتجعة لها مسبقاً'], 400);
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
        // التحقق من تفعيل إعداد الإشعار الدائن
        if (!PurchaseInvoiceSetting::isSettingActive('enable_debit_notice')) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'عذراً، ميزة الإشعار الدائن غير مفعلة حالياً. يرجى التواصل مع الإدارة لتفعيلها.');
        }

        $originalInvoice = null;
        if ($request->has('reference_id') && $request->reference_id) {
            $originalInvoice = PurchaseInvoice::find($request->reference_id);
            if (!$originalInvoice) {
                return redirect()->back()->withInput()->with('error', 'الفاتورة المرجعية غير موجودة');
            }
            if ($originalInvoice->type !== 'invoice') {
                return redirect()->back()->withInput()->with('error', 'يمكن إنشاء اشعار مدين فقط للفواتير العادية');
            }
        }

        if (!$request->supplier_id) {
            throw new \Exception('يجب تحديد المورد');
        }

        $supplier = Supplier::find($request->supplier_id);
        if (!$supplier) {
            throw new \Exception('المورد المحدد غير موجود');
        }

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

        DB::beginTransaction();

        $total_amount = 0;
        $total_discount = 0;
        $items_data = [];

        if ($request->has('items') && count($request->items)) {
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);

                $quantity = floatval($item['quantity']);
                $unit_price = floatval($item['unit_price']);
                $item_total = $quantity * $unit_price;

                $item_discount = 0;
                if (isset($item['discount']) && $item['discount'] > 0) {
                    if (isset($item['discount_type']) && $item['discount_type'] === 'percentage') {
                        $item_discount = ($item_total * floatval($item['discount'])) / 100;
                    } else {
                        $item_discount = floatval($item['discount']);
                    }
                }

                $total_amount += $item_total;
                $total_discount += $item_discount;

                $items_data[] = [
                    'purchase_invoice_id' => null,
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

        $invoice_discount = 0;
        if ($request->has('discount_amount') && $request->discount_amount > 0) {
            if ($request->has('discount_type') && $request->discount_type === 'percentage') {
                $invoice_discount = ($total_amount * floatval($request->discount_amount)) / 100;
            } else {
                $invoice_discount = floatval($request->discount_amount);
            }
        }

        $total_tax = 0;
        foreach ($request->items as $item) {
            $tax_1 = floatval($item['tax_1'] ?? 0);
            $tax_2 = floatval($item['tax_2'] ?? 0);

            $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
            $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

            $total_tax += $item_tax;
        }

        $final_total_discount = $total_discount + $invoice_discount;
        $amount_after_discount = $total_amount - $final_total_discount;

        $shipping_cost = floatval($request->shipping_cost ?? 0);
        $shipping_tax = 0;
        if ($request->tax_type == 1) {
            $shipping_tax = $shipping_cost * 0.15;
        }

        $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost + $shipping_tax;

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

        $reference_id = null;
        if ($originalInvoice) {
            $reference_id = $originalInvoice->id;
        } elseif ($request->filled('reference_id')) {
            $reference_id = $request->reference_id;
        }

        $cityNotices = PurchaseInvoice::create([
            'supplier_id' => $request->supplier_id,
            'code' => $code,
            'type' => 4,
            'reference_id' => $reference_id,
            'date' => $request->date,
            'terms' => $request->terms ?? 0,
            'notes' => ($request->notes ?? '') . ($originalInvoice ? "\nاشعار مدين للفاتورة رقم: " . $originalInvoice->code : '') . "\nالخزينة المستخدمة: " . $mainTreasuryAccount->name,
            'status' => $payment_status === 'paid' ? 1 : ($payment_status === 'partially_paid' ? 2 : 3),
            'created_by' => Auth::id(),
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
                            'invoice_id' => $cityNotices->id,
                            'type' => $tax->type,
                            'rate' => $tax->tax,
                            'value' => $tax_value,
                            'type_invoice' => 'cityNotices_purchase',
                        ]);
                    }
                }
            }
        }

        foreach ($items_data as $item) {
            $item['purchase_invoice_id'] = $cityNotices->id;
            $invoice_purhase = InvoiceItem::create($item);

            $invoice_purhase->load('product');

            ModelsLog::create([
                'type' => 'purchase_city_notices_log',
                'type_id' => $cityNotices->id,
                'type_log' => 'log',
                'icon'  => 'create',
                'description' => sprintf(
                    'تم انشاء اشعار مدين رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للمورد **%s** لفاتورة رقم **%s**',
                    $cityNotices->code ?? "",
                    $invoice_purhase->product->name ?? "",
                    $item['quantity'] ?? "",
                    $item['unit_price'] ?? "",
                    $supplier->trade_name ?? "",
                    $request->reference_number ?? ""
                ),
                'created_by' => auth()->id(),
            ]);
        }

        if ($paid_amount > 0) {
            $payment = PaymentsProcess::create([
                'purchases_id' => $cityNotices->id,
                'supplier_id' => $request->supplier_id,
                'amount' => $paid_amount,
                'payment_date' => $request->date ?? now(),
                'payment_method' => $request->payment_method ?? ($request->advance_payment_method ?? 1),
                'type' => 'supplier city notice payment',
                'payment_status' => 1,
                'employee_id' => Auth::id(),
                'treasury_id' => $treasury_id,
                'notes' => $payment_status === 'paid' ? 'دفع كامل لاشعار مدين رقم ' . $cityNotices->code . ' إلى خزينة ' . $mainTreasuryAccount->name : 'دفع جزئي بمبلغ ' . number_format($paid_amount, 2) . ' لاشعار مدين رقم ' . $cityNotices->code . ' إلى خزينة ' . $mainTreasuryAccount->name,
            ]);
        }

        $this->createCityNoticeAccountingEntries($cityNotices, $supplier, $paid_amount, $mainTreasuryAccount);

        if ($request->hasFile('attachments')) {
            $file = $request->file('attachments');
            if ($file->isValid()) {
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('assets/uploads/'), $filename);
                $cityNotices->attachments = $filename;
                $cityNotices->save();
            }
        }

        DB::commit();

        notifications::create([
            'type' => 'city_notice_creation',
            'title' => Auth::user()->name . ' أنشأ اشعار مدين جديد',
            'description' => 'تم إنشاء اشعار مدين رقم ' . $cityNotices->code . ' للمورد ' . ($supplier->trade_name ?? '') . ' بقيمة ' . number_format($cityNotices->grand_total, 2) . ' ر.س' . ($originalInvoice ? ' مرتبط بالفاتورة رقم ' . $originalInvoice->code : '') . ' من خزينة ' . $mainTreasuryAccount->name . ($paid_amount > 0 ? ' - تم إيداع ' . number_format($paid_amount, 2) . ' ر.س' : '') . ' - حالة الدفع: ' . $this->getPaymentStatusText($payment_status),
        ]);

        $message = $this->generateCityNoticeSuccessMessage($payment_status, $paid_amount, $due_value, $mainTreasuryAccount->name, $originalInvoice);

        return redirect()->route('CityNotices.show', $cityNotices->id)->with('success', $message);
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('خطأ في إنشاء اشعار مدين: ' . $e->getMessage());
        return redirect()
            ->back()
            ->withInput()
            ->with('error', 'عذراً، حدث خطأ أثناء حفظ اشعار مدين: ' . $e->getMessage());
    }
}
    private function createCityNoticeAccountingEntries($cityNotices, $supplier, $paid_amount, $mainTreasuryAccount)
{
    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

    // القيد الأول: تسجيل الإشعار المدين (بشكل مبسط مثل المرتجع)
    $journalEntry1 = JournalEntry::create([
        'code' => $cityNotices->code,
        'date' => now(),
        'description' => 'اشعار مدين رقم ' . $cityNotices->code,
        'status' => 1,
        'currency' => 'SAR',
        'supplier_id' => $cityNotices->supplier_id,
        'created_by_employee' => Auth::id(),
    ]);

    // جلب أو إنشاء حساب المورد
    $supplierAccount = Account::where('supplier_id', $cityNotices->supplier_id)->first();
    if (!$supplierAccount) {
        $supplierAccount = Account::create([
            'name' => 'حساب المورد - ' . ($supplier->trade_name ?? 'مورد غير معروف'),
            'supplier_id' => $cityNotices->supplier_id,
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
    $subtotal = $cityNotices->grand_total - $cityNotices->total_tax;

    // 1. حساب المورد (مدين) - بكامل مبلغ الإشعار المدين
    JournalEntryDetail::create([
        'journal_entry_id' => $journalEntry1->id,
        'account_id' => $supplierAccount->id,
        'description' => 'اشعار مدين رقم ' . $cityNotices->code,
        'debit' => $cityNotices->grand_total,
        'credit' => 0,
        'is_debit' => true,
    ]);

    // 2. حساب المشتريات (دائن) - بالمبلغ بدون ضريبة
    JournalEntryDetail::create([
        'journal_entry_id' => $journalEntry1->id,
        'account_id' => $purchasesAccount->id,
        'description' => 'اشعار مدين رقم ' . $cityNotices->code,
        'debit' => 0,
        'credit' => $subtotal,
        'is_debit' => false,
    ]);

    // 3. معالجة الضرائب إذا وجدت
    if ($cityNotices->total_tax > 0) {
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
            'description' => 'استرداد VAT اشعار مدين رقم ' . $cityNotices->code,
            'debit' => 0,
            'credit' => $cityNotices->total_tax,
            'is_debit' => false,
        ]);

        // تحديث رصيد ضريبة القيمة المضافة
        $taxAccount->balance -= $cityNotices->total_tax;
        $taxAccount->save();
    }

    // تحديث أرصدة الحسابات
    // تقليل رصيد المورد (تقليل الدين علينا)
    $supplierAccount->balance -= $cityNotices->grand_total;
    $supplierAccount->save();

    // تقليل رصيد المشتريات
    $purchasesAccount->balance -= $subtotal;
    $purchasesAccount->save();

    // القيد الثاني: تسجيل الدفع من المورد إلى الخزينة (إذا كان هناك دفع)
    if ($paid_amount > 0) {
        $journalEntry2 = JournalEntry::create([
            'code' => $cityNotices->code . '_دفع',
            'date' => now(),
            'description' => 'دفع من المورد - اشعار مدين ' . $cityNotices->code,
            'status' => 1,
            'currency' => 'SAR',
            'supplier_id' => $cityNotices->supplier_id,
            'created_by_employee' => Auth::id(),
        ]);

        // حساب الخزينة (مدين) - دخول نقد
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry2->id,
            'account_id' => $mainTreasuryAccount->id,
            'description' => 'دفع من المورد - اشعار مدين ' . $cityNotices->code,
            'debit' => $paid_amount,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // حساب المورد (دائن) - زيادة الدين عليه
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry2->id,
            'account_id' => $supplierAccount->id,
            'description' => 'دفع من المورد - اشعار مدين ' . $cityNotices->code,
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
        if (!PurchaseInvoiceSetting::isSettingActive('enable_debit_notice')) {
        return redirect()->route('CityNotices.index')
            ->with('error', 'عذراً، ميزة الإشعار الدائن غير مفعلة حالياً.');
    }
        $cityNotice = PurchaseInvoice::with(['items.product', 'supplier'])->findOrFail($id);
        $suppliers = Supplier::all();
        $items = Product::all();
        $taxs = TaxSitting::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        return view('purchases::purchases.city_notices.edit', compact('cityNotice', 'suppliers', 'items', 'taxs', 'account_setting'));
    }

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $cityNotices = PurchaseInvoice::findOrFail($id);

            if ($cityNotices->is_received) {
                throw new \Exception('لا يمكن تعديل اشعار مدين مستلم بالفعل');
            }

            $originalInvoice = null;
            if ($request->has('reference_id') && $request->reference_id) {
                $originalInvoice = PurchaseInvoice::find($request->reference_id);
                if (!$originalInvoice) {
                    return redirect()->back()->withInput()->with('error', 'الفاتورة المرجعية غير موجودة');
                }
                if ($originalInvoice->type !== 'invoice') {
                    return redirect()->back()->withInput()->with('error', 'يمكن إنشاء اشعار مدين فقط للفواتير العادية');
                }
            }

            if (!$request->supplier_id) {
                throw new \Exception('يجب تحديد المورد');
            }

            $supplier = Supplier::find($request->supplier_id);
            if (!$supplier) {
                throw new \Exception('المورد المحدد غير موجود');
            }

            $code = $request->code;
            if ($code && $code !== $cityNotices->code) {
                $existingCode = PurchaseInvoice::where('code', $code)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            } else {
                $code = $cityNotices->code;
            }

            $total_amount = 0;
            $total_discount = 0;
            $items_data = [];

            if ($request->has('items') && count($request->items)) {
                foreach ($request->items as $item) {
                    $product = Product::findOrFail($item['product_id']);

                    $quantity = floatval($item['quantity']);
                    $unit_price = floatval($item['unit_price']);
                    $item_total = $quantity * $unit_price;

                    $item_discount = 0;
                    if (isset($item['discount']) && $item['discount'] > 0) {
                        if (isset($item['discount_type']) && $item['discount_type'] === 'percentage') {
                            $item_discount = ($item_total * floatval($item['discount'])) / 100;
                        } else {
                            $item_discount = floatval($item['discount']);
                        }
                    }

                    $total_amount += $item_total;
                    $total_discount += $item_discount;

                    $items_data[] = [
                        'purchase_invoice_id' => $cityNotices->id,
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

            $invoice_discount = 0;
            if ($request->has('discount_amount') && $request->discount_amount > 0) {
                if ($request->has('discount_type') && $request->discount_type === 'percentage') {
                    $invoice_discount = ($total_amount * floatval($request->discount_amount)) / 100;
                } else {
                    $invoice_discount = floatval($request->discount_amount);
                }
            }

            $total_tax = 0;
            foreach ($request->items as $item) {
                $tax_1 = floatval($item['tax_1'] ?? 0);
                $tax_2 = floatval($item['tax_2'] ?? 0);

                $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
                $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

                $total_tax += $item_tax;
            }

            $final_total_discount = $total_discount + $invoice_discount;
            $amount_after_discount = $total_amount - $final_total_discount;

            $shipping_cost = floatval($request->shipping_cost ?? 0);
            $shipping_tax = 0;
            if ($request->tax_type == 1) {
                $shipping_tax = $shipping_cost * 0.15;
            }

            $total_with_tax = $amount_after_discount + $total_tax + $shipping_cost + $shipping_tax;

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

            $reference_id = null;
            if ($originalInvoice) {
                $reference_id = $originalInvoice->id;
            } elseif ($request->filled('reference_id')) {
                $reference_id = $request->reference_id;
            }

            $cityNotices->update([
                'supplier_id' => $request->supplier_id,
                'code' => $code,
                'reference_id' => $reference_id,
                'date' => $request->date,
                'terms' => $request->terms ?? 0,
                'notes' => ($request->notes ?? '') . ($originalInvoice ? "\nاشعار مدين للفاتورة رقم: " . $originalInvoice->code : '') . "\nالخزينة المستخدمة: " . $mainTreasuryAccount->name,
                'status' => $payment_status === 'paid' ? 1 : ($payment_status === 'partially_paid' ? 2 : 3),
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

            $cityNotices->invoiceItems()->delete();
            foreach ($items_data as $item) {
                InvoiceItem::create($item);
            }

            TaxInvoice::where('invoice_id', $cityNotices->id)
                     ->where('type_invoice', 'cityNotices_purchase')
                     ->delete();

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
                                'invoice_id' => $cityNotices->id,
                                'type' => $tax->type,
                                'rate' => $tax->tax,
                                'value' => $tax_value,
                                'type_invoice' => 'cityNotices_purchase',
                            ]);
                        }
                    }
                }
            }

            if ($request->hasFile('attachments')) {
                if ($cityNotices->attachments) {
                    $oldFile = public_path('assets/uploads/') . $cityNotices->attachments;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }

                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $cityNotices->attachments = $filename;
                    $cityNotices->save();
                }
            }

            DB::commit();

            return redirect()->route('CityNotices.show', $cityNotices->id)
                            ->with('success', 'تم تحديث اشعار مدين بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في تحديث اشعار مدين: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء تحديث اشعار مدين: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $cityNotice = PurchaseInvoice::findOrFail($id);
            ModelsLog::create([
                'type' => 'purchase_city_notices_log',
                'type_id' => $cityNotice->id,
                'type_log' => 'log',
                'icon'  => 'delete',
                'description' => sprintf(
                    'تم حذف اشعار مدين رقم **%s**',
                    $cityNotice->code ?? "",
                ),
                'created_by' => auth()->id(),
            ]);
            $cityNotice->delete();

            // إذا كان الطلب AJAX، نرجع استجابة JSON
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم حذف اشعار مدين بنجاح'
                ]);
            }

            return redirect()->route('CityNotices.index')->with('success', 'تم حذف اشعار مدين بنجاح');
        } catch (\Exception $e) {
            Log::error('خطأ في حذف اشعار مدين: ' . $e->getMessage());

            // إذا كان الطلب AJAX، نرجع استجابة JSON للخطأ
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف اشعار مدين: ' . $e->getMessage()
                ], 500);
            }

            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف اشعار مدين: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $cityNotice = PurchaseInvoice::with(['supplier', 'items.product', 'payments', 'user'])
            ->findOrFail($id);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'cityNotices_purchase')->get();

        $logs = ModelsLog::where('type', 'purchase_city_notices_log')
            ->where('type_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('purchases::purchases.city_notices.show', compact('cityNotice', 'logs', 'TaxsInvoice'));
    }

    public function pdf($id)
    {
        $cityNotice = PurchaseInvoice::with(['items.product'])->findOrFail($id);
        $pdf = Pdf::loadView('purchases::purchases.city_notices.pdf', compact('cityNotice'));
        return $pdf->download('اشعار_مدين.pdf');
    }

    private function getPaymentStatusText($status)
    {
        switch ($status) {
            case 'paid':
            case 1:
                return 'مدفوعة بالكامل';
            case 'partially_paid':
            case 2:
                return 'مدفوعة جزئياً';
            case 'unpaid':
            case 3:
                return 'غير مدفوعة';
            default:
                return 'غير محددة';
        }
    }

    private function generateCityNoticeSuccessMessage($payment_status, $paid_amount, $due_value, $treasury_name, $originalInvoice)
    {
        $payment_text = $this->getPaymentStatusText($payment_status);

        $message = 'تم إنشاء اشعار مدين بنجاح من خزينة ' . $treasury_name;

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
}
