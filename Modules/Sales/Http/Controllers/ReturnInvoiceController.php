<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\PaymentsProcess;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\Treasury;
use App\Models\User;
use App\Models\TaxSitting;
use App\Models\TaxInvoice;
use App\Models\AccountSetting;
use App\Models\DefaultWarehouses;
use App\Models\PermissionSource;
use App\Models\TreasuryEmployee;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use Illuminate\Http\Request;
use App\Mail\SimpleLinkMail;
use App\Models\CreditNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnInvoiceController extends Controller
{
    public function index(Request $request)
    {
        // بدء بناء الاستعلام
        $query = Invoice::with(['client', 'createdByUser', 'updatedByUser'])->where('type', 'returned')->orderBy('created_at', 'desc');

        // 1. البحث حسب العميل
        if ($request->has('client_id') && $request->client_id) {
            $query->where('client_id', $request->client_id);
        }

        // 2. البحث حسب رقم الفاتورة
        if ($request->has('invoice_number') && $request->invoice_number) {
            $query->where('id', 'like', '%' . $request->invoice_number . '%');
        }

        // 3. البحث حسب حالة الفاتورة
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // 4. البحث حسب البند
        if ($request->has('item') && $request->item) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('item', 'like', '%' . $request->item . '%');
            });
        }

        // 5. البحث حسب العملة
        if ($request->has('currency') && $request->currency) {
            $query->where('currency', $request->currency);
        }

        // 6. البحث حسب الإجمالي (من)
        if ($request->has('total_from') && $request->total_from) {
            $query->where('grand_total', '>', $request->total_from);
        }

        // 7. البحث حسب الإجمالي (إلى)
        if ($request->has('total_to') && $request->total_to) {
            $query->where('grand_total', '<', $request->total_to);
        }

        // 8. البحث حسب حالة الدفع
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // 9. البحث حسب التاريخ (من)
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // 10. البحث حسب التاريخ (إلى)
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // 11. البحث حسب تاريخ الاستحقاق (من)
        if ($request->has('due_date_from') && $request->due_date_from) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        // 12. البحث حسب تاريخ الاستحقاق (إلى)
        if ($request->has('due_date_to') && $request->due_date_to) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        // 13. البحث حسب المصدر
        if ($request->has('source') && $request->source) {
            $query->where('source', $request->source);
        }

        // 14. البحث حسب الحقل المخصص
        if ($request->has('custom_field') && $request->custom_field) {
            $query->where('custom_field', 'like', '%' . $request->custom_field . '%');
        }

        // 15. البحث حسب تاريخ الإنشاء (من)
        if ($request->has('created_at_from') && $request->created_at_from) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }

        // 16. البحث حسب تاريخ الإنشاء (إلى)
        if ($request->has('created_at_to') && $request->created_at_to) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // 17. البحث حسب حالة التسليم
        if ($request->has('delivery_status') && $request->delivery_status) {
            $query->where('delivery_status', $request->delivery_status);
        }

        // 18. البحث حسب "أضيفت بواسطة"
        if ($request->has('added_by') && $request->added_by) {
            $query->where('created_by', $request->added_by);
        }

        // 19. البحث حسب مسؤول المبيعات
        if ($request->has('sales_person') && $request->sales_person) {
            $query->where('sales_person_id', $request->sales_person);
        }

        // 20. البحث حسب خيارات الشحن
        if ($request->has('shipping_option') && $request->shipping_option) {
            $query->where('shipping_option', $request->shipping_option);
        }

        // 21. البحث حسب مصدر الطلب
        if ($request->has('order_source') && $request->order_source) {
            $query->where('order_source', $request->order_source);
        }

        // 22. البحث حسب التخصيص (شهريًا، أسبوعيًا، يوميًا)
        if ($request->has('custom_period') && $request->custom_period) {
            if ($request->custom_period == 'monthly') {
                $query->whereMonth('created_at', now()->month);
            } elseif ($request->custom_period == 'weekly') {
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
            } elseif ($request->custom_period == 'daily') {
                $query->whereDate('created_at', now()->toDateString());
            }
        }

        // 23. البحث حسب حالة التسليم
        if ($request->has('delivery_status') && $request->delivery_status) {
            $query->where('delivery_status', $request->delivery_status);
        }

        // 24. البحث حسب "أضيفت بواسطة" (الموظفين)
        if ($request->has('added_by_employee') && $request->added_by_employee) {
            $query->where('created_by', $request->added_by_employee);
        }

        // 25. البحث حسب مسؤول المبيعات (المستخدمين)
        if ($request->has('sales_person_user') && $request->sales_person_user) {
            $query->where('sales_person_id', $request->sales_person_user);
        }

        // جلب النتائج مع التقسيم
        $return = $query->get(); // استبدل get() بـ paginate()

        // البيانات الأخرى المطلوبة للواجهة
        $clients = Client::all();
        $users = User::all();
        $employees = Employee::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();

        return view('sales::retend_invoice.index', compact('return', 'account_setting', 'clients', 'users', 'employees'));
    }


    public function showPrintable($id)
  {
    $credit = CreditNotification::with(['client', 'createdBy'])->findOrFail($id);
    $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice','credit')->get();

    return view('sales::creted_note.pdf', compact('credit', 'TaxsInvoice'));
  }
    public function create($id)
    {
        // العثور على الفاتورة
        $invoice = Invoice::findOrFail($id);

        // تحديث نوع الفاتورة إلى مرتجع

        // توليد رقم الفاتورة
        // $invoice_number = $this->generateInvoiceNumber();
        $items = Product::all();
        $clients = Client::all();
        $treasury = Treasury::all();
        $users = User::all();
        $taxs = TaxSitting::all();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        // تمرير البيانات إلى العرض
        return view('sales::retend_invoice.create', compact('clients', 'account_setting', 'taxs', 'items', 'treasury', 'users', 'invoice'));
    }
    public function store(Request $request)
    {
        // dd($request->all());
        try {
            $invoice_orginal = Invoice::find($request->invoice_id);
            $invoice_code = $invoice_orginal->id;

            // ** الخطوة الأولى: إنشاء كود للفاتورة **
            $code = $request->code;
            if (!$code) {
                $lastOrder = Invoice::orderBy('id', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->code) + 1 : 1;
                // التحقق من أن الرقم فريد
                while (Invoice::where('code', str_pad($nextNumber, 5, '0', STR_PAD_LEFT))->exists()) {
                    $nextNumber++;
                }
                $code = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $existingCode = Invoice::where('code', $request->code)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم الفاتورة موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            }
            DB::beginTransaction(); // بدء المعاملة

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود
            // $invoiceItems = $invoice->items;
            // ** الخطوة الثانية: معالجة البنود (items) **
            if ($request->has('items') && count($request->items)) {
                foreach ($request->items as $item) {
                    // التحقق من وجود product_id في البند

                    if (!isset($item['product_id'])) {
                        throw new \Exception('معرف المنتج (product_id) مطلوب لكل بند.');
                    }

                    // جلب المنتج
                    $product = Product::find($item['product_id']);
                    if (!$product) {
                        throw new \Exception('المنتج غير موجود: ' . $item['product_id']);
                    }
                    // التحقق من وجود store_house_id في جدول store_houses
                    // التحقق من وجود store_house_id في جدول store_houses
                    $store_house_id = $item['store_house_id'] ?? null;

                    // البحث عن المستودع
                    $storeHouse = null;
                    if ($store_house_id) {
                        // البحث عن المستودع المحدد
                        $storeHouse = StoreHouse::find($store_house_id);
                    }

                    if (!$storeHouse) {
                        // إذا لم يتم العثور على المستودع المحدد، ابحث عن أول مستودع متاح
                        $storeHouse = StoreHouse::first();
                        if (!$storeHouse) {
                            throw new \Exception('لا يوجد أي مستودع في النظام. الرجاء إضافة مستودع واحد على الأقل.');
                        }
                        $store_house_id = $storeHouse->id;
                    }
                    // الحصول على المستخدم الحالي
                    $user = Auth::user();

                    // التحقق مما إذا كان للمستخدم employee_id
                    // الحصول على المستخدم الحالي
                    $user = Auth::user();

                    // التحقق مما إذا كان للمستخدم employee_id والبحث عن المستودع الافتراضي
                    if ($user && $user->employee_id) {
                        $defaultWarehouse = DefaultWarehouses::where('employee_id', $user->employee_id)->first();

                        // التحقق مما إذا كان هناك مستودع افتراضي واستخدام storehouse_id إذا وجد
                        if ($defaultWarehouse && $defaultWarehouse->storehouse_id) {
                            $storeHouse = StoreHouse::find($defaultWarehouse->storehouse_id);
                        } else {
                            $storeHouse = StoreHouse::where('major', 1)->first();
                        }
                    } else {
                        // إذا لم يكن لديه employee_id، يتم تعيين storehouse الافتراضي
                        $storeHouse = StoreHouse::where('major', 1)->first();
                    }
                    $store_house_id = $storeHouse ? $storeHouse->id : null;
                    $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                    if ($user && $user->employee_id) {
                        // تحقق مما إذا كان treasury_id فارغًا أو null
                        if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                            $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
                        } else {
                            // إذا كان treasury_id null أو غير موجود، اختر الخزينة الرئيسية
                            $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                        }
                    } else {
                        // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، اختر الخزينة الرئيسية
                        $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                    }
                    // if (!$storeHouse) {
                    //     throw new \Exception('المستودع غير موجود: ' . $item->store_house_id);
                    // }

                    // التحقق مما إذا كان للمستخدم employee_id
                    // الحصول على المستخدم الحالي

                    // احصل على بند المنتج في الفاتورة الأصلية
                    $original_item = InvoiceItem::where('invoice_id', $invoice_orginal->id)->where('product_id', $item['product_id'])->first();

                    if (!$original_item) {
                        return back()->with('error', 'المنتج غير موجود في الفاتورة الأصلية');
                    }

                    // اجمع الكمية المرتجعة سابقًا لهذا المنتج من فواتير الإرجاع التي تشير لنفس الفاتورة الأصلية
                    $previous_return_qty = InvoiceItem::whereHas('invoice', function ($query) use ($invoice_orginal) {
                        $query->where('reference_number', $invoice_orginal->id); // أو رقم الفاتورة الأصلية
                    })
                        ->where('product_id', $item['product_id'])
                        ->sum('quantity');

                    // اجمع الكمية المرتجعة سابقًا + الحالية
                    $total_return_qty = floatval($previous_return_qty) + floatval($item['quantity']);

                    if ($total_return_qty > $original_item->quantity) {
                        return back()->with('error', 'لا يمكن إرجاع كمية أكبر من الأصلية للمنتج: ' . ($original_item->product->name ?? 'غير معروف'));
                    }
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
                        'invoice_id' => null, // سيتم تعيينه لاحقًا بعد إنشاء الفاتورة
                        'product_id' => $item['product_id'],
                        'store_house_id' => $store_house_id,
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

            // ** الخطوة الثالثة: حساب الخصم الإضافي للفاتورة ككل **
            $invoice_discount = 0;
            if ($request->has('discount_amount') && $request->discount_amount > 0) {
                if ($request->has('discount_type') && $request->discount_type === 'percentage') {
                    $invoice_discount = ($total_amount * floatval($request->discount_amount)) / 100;
                } else {
                    $invoice_discount = floatval($request->discount_amount);
                }
            }

            // الخصومات الإجمالية
            $final_total_discount = $total_discount + $invoice_discount;

            // حساب المبلغ بعد الخصم
            $amount_after_discount = $total_amount - $final_total_discount;

            // ** حساب الضرائب **
            $tax_total = 0;
            if ($request->tax_type == 1) {
                // حساب الضريبة بناءً على القيمة التي يدخلها المستخدم في tax_1 أو tax_2
                foreach ($request->items as $item) {
                    $tax_1 = floatval($item['tax_1'] ?? 0); // الضريبة الأولى
                    $tax_2 = floatval($item['tax_2'] ?? 0); // الضريبة الثانية

                    // حساب الضريبة لكل بند
                    $item_total = floatval($item['quantity']) * floatval($item['unit_price']);
                    $item_tax = ($item_total * $tax_1) / 100 + ($item_total * $tax_2) / 100;

                    // إضافة الضريبة إلى الإجمالي
                    $tax_total += $item_tax;
                }
            }

            // ** إضافة تكلفة الشحن (إذا وجدت) **
            $shipping_cost = floatval($request->shipping_cost ?? 0);

            // ** حساب ضريبة الشحن (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($request->tax_type == 1) {
                $shipping_tax = $shipping_cost * 0.15; // ضريبة الشحن 15%
            }

            // ** إضافة ضريبة الشحن إلى tax_total **
            $tax_total += $shipping_tax;

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $tax_total + $shipping_cost;

            // ** حساب المبلغ المستحق (due_value) بعد خصم الدفعة المقدمة **
            $advance_payment = floatval($request->advance_payment ?? 0);
            $due_value = $total_with_tax - $advance_payment;

            // ** تحديد حالة الفاتورة بناءً على المدفوعات **
            $payment_status = 3; // الحالة الافتراضية (مسودة)
            $is_paid = false;

            if ($advance_payment > 0 || $request->has('is_paid')) {
                // حساب إجمالي المدفوعات
                $total_payments = $advance_payment;

                if ($request->has('is_paid') && $request->is_paid) {
                    $total_payments = $total_with_tax;
                    $advance_payment = $total_with_tax;
                    $due_value = 0;
                    $payment_status = 1; // مكتمل
                    $is_paid = true;
                } else {
                    // إذا كان هناك دفعة مقدمة لكن لم يتم اكتمال المبلغ
                    $payment_status = 2; // غير مكتمل
                    $is_paid = false;
                }
            }

            // إذا تم تحديد حالة دفع معينة في الطلب
            if ($request->has('payment_status')) {
                switch ($request->payment_status) {
                    case 4: // تحت المراجعة
                        $payment_status = 4;
                        $is_paid = false;
                        break;
                    case 5: // فاشلة
                        $payment_status = 5;
                        $is_paid = false;
                        break;
                }
            }

            $invoice = Invoice::create([
                'client_id' => $request->client_id,
                'employee_id' => $request->employee_id,
                'due_value' => $due_value,
                'reference_number' => $invoice_code,
                'code' => $code,
                'type' => 'returned',
                'invoice_date' => $request->invoice_date,
                'issue_date' => $request->issue_date,
                'terms' => $request->terms ?? 0,
                'notes' => $request->notes,
                'payment_status' => 4,
                'is_paid' => $is_paid,
                'created_by' => Auth::id(),
                'account_id' => $request->account_id,
                'discount_amount' => $invoice_discount,
                'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
                'advance_payment' => $advance_payment,
                'payment_type' => $request->payment_type ?? 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $request->tax_type ?? 1,
                'payment_method' => $request->payment_method,

                'received_date' => $request->received_date,
                'subtotal' => $total_amount,
                'total_discount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'paid_amount' => $advance_payment,
            ]);

            $invoice->save();
            $invoice_orginal->returned_payment += $invoice->grand_total;

            $invoice_orginal->save();
            // حساب الضريبة
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
                                'invoice_id' => $invoice->id,
                                'type' => $tax->type,
                                'rate' => $tax->tax,
                                'value' => $tax_value,
                                'type_invoice' => 'invoice',
                            ]);
                        }
                    }
                }
            }

            // ** تحديث رصيد حساب أبناء العميل **

            // إضافة المبلغ الإجمالي للفاتورة إلى رصيد أبناء العميل

            // ** الخطوة الخامسة: إنشاء سجلات البنود (items) للفاتورة **
            foreach ($items_data as $item) {
                $item['invoice_id'] = $invoice->id;
                $item_invoice = InvoiceItem::create($item);
                $client_name = Client::find($invoice->client_id);

                // ** تحديث المخزون بناءً على store_house_id المحدد في البند **
                $productDetails = ProductDetails::where('store_house_id', $item['store_house_id'])->where('product_id', $item['product_id'])->first();

                if (!$productDetails) {
                    $productDetails = ProductDetails::create([
                        'store_house_id' => $item['store_house_id'],
                        'product_id' => $item['product_id'],
                        'quantity' => 0,
                    ]);
                }

                $proudect = Product::where('id', $item['product_id'])->first();

                if ($proudect->type == 'products') {
                    // ** حساب المخزون قبل وبعد التعديل (زيادة بسبب المرتجع) **
                    $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;
                    $stock_after = $stock_before + $item['quantity'];

                    // ** تحديث المخزون بزيادة الكمية **
                    $productDetails->increment('quantity', $item['quantity']);

                    // ** جلب مصدر إذن المخزون للإرجاع ** (مثلاً اسمه "مرتجع مبيعات")
                    $permissionSource = PermissionSource::where('name', 'مرتجع مبيعات')->first();

                    if (!$permissionSource) {
                        throw new \Exception("مصدر إذن 'مرتجع مبيعات' غير موجود في قاعدة البيانات.");
                    }

                    // ** تسجيل حركة المخزون للإرجاع **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = $permissionSource->id; // جلب id المصدر ديناميكياً
                    $wareHousePermits->permission_date = now();
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تسجيل تفاصيل حركة المخزون **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before,
                        'stock_after' => $stock_after,
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);
                }
            }

            // جلب بيانات الموظف والمستخدم
            $employee_name = Employee::where('id', $invoice->employee_id)->first();
            $user_name = User::where('id', $invoice->created_by)->first();
            $client_name = Client::find($invoice->client_id);
            // جلب جميع المنتجات المرتبطة بالفاتورة
            $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();

            // تجهيز قائمة المنتجات
            $productsList = '';
            foreach ($invoiceItems as $item) {
                $product = Product::find($item['product_id']);
                $productName = $product ? $product->name : 'منتج غير معروف';
                $productsList .= "▫️ *{$productName}* - الكمية: {$item->quantity}, السعر: {$item->unit_price} \n";
            }

            // // // رابط API التلقرام
            // $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

            // // تجهيز الرسالة
            // $message = "📜 *فاتورة جديدة* 📜\n";
            // $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            // $message .= "🆔 *رقم الفاتورة:* `$code`\n";
            // $message .= '👤 *مسؤول البيع:* ' . ($employee_name->first_name ?? 'لا يوجد') . "\n";
            // $message .= '🏢 *العميل:* ' . ($client_name->trade_name ?? 'لا يوجد') . "\n";
            // $message .= '✍🏻 *أنشئت بواسطة:* ' . ($user_name->name ?? 'لا يوجد') . "\n";
            // $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            // $message .= '💰 *المجموع:* `' . number_format($invoice->grand_total, 2) . "` ريال\n";
            // $message .= '🧾 *الضريبة:* `' . number_format($invoice->tax_total, 2) . "` ريال\n";
            // $message .= '📌 *الإجمالي:* `' . number_format($invoice->tax_total + $invoice->grand_total, 2) . "` ريال\n";
            // $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            // $message .= "📦 *المنتجات:* \n" . $productsList;
            // $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            // $message .= '📅 *التاريخ:* `' . date('Y-m-d H:i') . "`\n";

            // // إرسال الرسالة إلى التلقرام
            // $response = Http::post($telegramApiUrl, [
            //     'chat_id' => '@Salesfatrasmart', // تأكد من أن لديك صلاحية الإرسال للقناة
            //     'text' => $message,
            //     'parse_mode' => 'Markdown',
            //     'timeout' => 30,
            // ]);
            // notifications::create([
            //     'type' => 'invoice',
            //     'title' => $user_name->name . ' أضاف فاتورة لعميل',
            //     'description' => 'فاتورة للعميل ' . $client_name->trade_name . ' بقيمة ' . number_format($invoice->grand_total, 2) . ' ر.س',
            // ]);

            // ** معالجة المرفقات (attachments) إذا وجدت **
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/'), $filename);
                    $invoice->attachments = $filename;
                    $invoice->save();
                }
            }

            $vatAccount = Account::where('name', 'القيمة المضافة المحصلة')->first();
            if (!$vatAccount) {
                throw new \Exception('حساب القيمة المضافة المحصلة غير موجود');
            }
            $storeAccount = Account::where('name', 'المخزون')->first();
            if (!$storeAccount) {
                throw new \Exception('حساب المخزون غير موجود');
            }
            $costAccount = Account::where('id', 50)->first();
            if (!$costAccount) {
                throw new \Exception('حساب تكلفة المبيعات غير موجود');
            }
            $retursalesnAccount = Account::where('id', 45)->first();
            if (!$retursalesnAccount) {
                throw new \Exception('حساب  مردودات المبيعات غير موجود');
            }
            // $mainAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            // if (!$mainAccount) {
            //     throw new \Exception('حساب  الخزينة الرئيسية غير موجود');
            // }

            $clientaccounts = Account::where('client_id', $invoice->client_id)->first();

            $invoice_refrence = Invoice::find($request->invoice_id);
            if ($invoice_refrence->payment_status == 1) {
                // مرتجع مبيعات لفاتورة مدفوعة
                $journalEntry = JournalEntry::create([
                    'reference_number' => $invoice->code,
                    'date' => now(),
                    'description' => 'قيد محاسبي لمرتجع مبيعات مدفوعة للفاتورة رقم ' . $invoice->code,
                    'status' => 1,
                    'currency' => 'SAR',
                    'client_id' => $invoice->client_id,
                    'invoice_id' => $invoice->id,
                    'created_by_employee' => Auth::id(),
                ]);

                // 1. مردود المبيعات (مدين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $retursalesnAccount->id,
                    'description' => 'قيد مردود المبيعات',
                    'debit' => $invoice->grand_total,
                    'credit' => 0,
                    'is_debit' => true,
                ]);

                // 2. العميل (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $clientaccounts->id,
                    'description' => 'فاتورة مرتجعه لفاتورة  رقم ' . $invoice->code,

                    'debit' => 0,
                    'credit' => $invoice->grand_total,
                    'is_debit' => false,
                ]);
                // 2. الخزينة (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $MainTreasury->id,
                    'description' => 'صرف قيمة المرتجع من الخزينة للفاتورة رقم ' . $invoice->code,
                    'debit' => 0,
                    'credit' => $invoice->grand_total,
                    'is_debit' => false,
                ]);

                // 3. المخزون (مدين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $storeAccount->id,
                    'description' => 'إرجاع البضاعة إلى المخزون',
                    'debit' => $invoice->grand_total,
                    'credit' => 0,
                    'is_debit' => true,
                ]);

                // 4. تكلفة المبيعات (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $costAccount->id,
                    'description' => 'إلغاء تكلفة المبيعات',
                    'debit' => 0,
                    'credit' => $invoice->grand_total,
                    'is_debit' => false,
                ]);

                // تحديث الأرصدة
                $retursalesnAccount->balance += $invoice->grand_total;
                $retursalesnAccount->save();

                $MainTreasury->balance -= $invoice->grand_total;
                $MainTreasury->save();

                $storeAccount->balance += $invoice->grand_total;
                $storeAccount->save();

                $costAccount->balance -= $invoice->grand_total;
                $costAccount->save();
            } else {
                // مرتجع لفاتورة آجلة (لم تُدفع)

                $journalEntry = JournalEntry::create([
                    'reference_number' => $invoice->code,
                    'date' => now(),
                    'description' => 'قيد محاسبي لمرتجع مبيعات آجلة للفاتورة رقم ' . $invoice->code,
                    'status' => 1,
                    'currency' => 'SAR',
                    'client_id' => $invoice->client_id,
                    'invoice_id' => $invoice->id,
                    'created_by_employee' => Auth::id(),
                ]);

                // 1. مردود المبيعات (مدين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $retursalesnAccount->id,
                    'description' => 'قيد مردود المبيعات',
                    'debit' => $invoice->grand_total,
                    'credit' => 0,
                    'is_debit' => true,
                ]);

                // 2. العميل (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $clientaccounts->id,
                    'description' => 'فاتورة مرتجعه لفاتورة  رقم ' . $invoice->code,

                    'debit' => 0,
                    'credit' => $invoice->grand_total,
                    'is_debit' => false,
                ]);

                // 3. المخزون (مدين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $storeAccount->id,
                    'description' => 'إرجاع البضاعة إلى المخزون',
                    'debit' => $invoice->grand_total,
                    'credit' => 0,
                    'is_debit' => true,
                ]);

                // 4. تكلفة المبيعات (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $costAccount->id,
                    'description' => 'إلغاء تكلفة المبيعات',
                    'debit' => 0,
                    'credit' => $invoice->grand_total,
                    'is_debit' => false,
                ]);

                // تحديث الأرصدة
                $retursalesnAccount->balance += $invoice->grand_total;
                $retursalesnAccount->save();

                $clientaccounts->balance -= $invoice->grand_total;
                $clientaccounts->save();

                $storeAccount->balance += $invoice->grand_total;
                $storeAccount->save();

                $costAccount->balance -= $invoice->grand_total;
                $costAccount->save();
            }

            DB::commit();

            return redirect()->route('ReturnIInvoices.show', $invoice->id)->with('success', 'تم إرجاع الفاتورة بنجاح.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('خطأ في إرجاع الفاتورة: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء إرجاع الفاتورة: ' . $e->getMessage());
        }
        //edit
    }
    public function edit($id)
    {
        return redirect()
            ->back()
            ->with('error', 'لا يمكنك تعديل الفاتورة رقم ' . $id . '. طبقا لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقا لمتطلبات الفاتورة الإلكترونية، ولكن يمكن إصدار فاتورة مرتجعة أو إشعار دائن لإلغائها أو تعديلها.');
    }

    /**
     * Remove the specified invoice from storage.
     */
    public function destroy($id)
    {
        return redirect()->route('ReturnIInvoices.index')->with('error', 'لا يمكنك حذف الفاتورة. طبقا لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقا لمتطلبات الفاتورة الإلكترونية، ولكن يمكن إصدار فاتورة مرتجعة أو إشعار دائن لإلغائها أو تعديلها.');
    }



    /**
     * Update the specified invoice in storage.
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('ReturnIInvoices.index')->with('error', 'لا يمكنك تعديل الفاتورة. طبقا لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقا لمتطلبات الفاتورة الإلكترونية، ولكن يمكن إصدار فاتورة مرتجعة أو إشعار دائن لإلغائها أو تعديلها.');
    }



    public function show($id)
    {
        $clients = Client::all();
        $employees = Employee::all();
        $return_invoice = Invoice::find($id);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->get();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        // $invoice_number = $this->generateInvoiceNumber();
        return view('sales::retend_invoice.show', compact('clients', 'TaxsInvoice', 'id', 'employees', 'account_setting', 'return_invoice'));
    }
    public function print($id)
    {
        $clients = Client::all();
        $employees = Employee::all();
        $return_invoice = Invoice::find($id);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->get();
        $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        // $invoice_number = $this->generateInvoiceNumber();
        return view('sales::retend_invoice.pdf', compact('clients', 'id', 'TaxsInvoice', 'employees', 'account_setting', 'return_invoice'));
    }
    public function sendReturnInvoiceEmail($id)
{
    $invoice = Invoice::with('client')->findOrFail($id);

    if (!$invoice->client || !filter_var($invoice->client->email, FILTER_VALIDATE_EMAIL)) {
        return back()->with('error', 'لا يوجد بريد إلكتروني صالح لهذا العميل.');
    }

    $link = route('return.print', $invoice->id);
    $subject = 'عرض الفاتورة المرتجعة #' . $invoice->id;
    $message = "مرحبًا،<br><br>يمكنك عرض الفاتورة المرتجعة عبر الرابط التالي:<br><a href=\"$link\">$link</a>";

    // إرسال الإيميل
    Mail::to($invoice->client->email)->send(new SimpleLinkMail($subject, $message));

    return back()->with('success', 'تم إرسال رابط الفاتورة المرتجعة إلى العميل.');
}
}
