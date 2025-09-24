<?php

namespace Modules\Sales\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sales\StoreCreditNotificationRequest;
use App\Models\Account;
use App\Models\Client;
use App\Models\CreditNotification;
use App\Models\Employee;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Product;
use App\Models\User;
use App\Models\TaxSitting;
use App\Models\AccountSetting;
use App\Models\TaxInvoice;
use App\Models\StoreHouse;
use App\Models\ProductDetails;
use Carbon\Carbon;
use App\Models\PermissionSource;
use App\Models\WarehousePermitsProducts;
use App\Models\WarehousePermits;
use App\Models\DefaultWarehouses;
use Illuminate\Http\Request;
use App\Mail\CreditNotificationLinkMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Dompdf\Dompdf;
use Dompdf\Options;

use function Ramsey\Uuid\v1;

class CreditNotificationController extends Controller
{
    public function index(Request $request)
    {
        $query = CreditNotification::with(['client', 'createdBy']);

        // Apply filters based on request parameters
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('invoice_number')) {
            $query->where('credit_number', 'LIKE', '%' . $request->invoice_number . '%');
        }

        if ($request->filled('item_search')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('item', 'LIKE', '%' . $request->item_search . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->item_search . '%');
            });
        }

        if ($request->filled('currency')) {
            $query->where('currency', $request->currency);
        }

        if ($request->filled('total_from')) {
            $query->where('grand_total', '>', $request->total_from);
        }
        if ($request->filled('total_to')) {
            $query->where('grand_total', '<', $request->total_to);
        }

        if ($request->filled('from_date_1') && $request->filled('to_date_1')) {
    $from = Carbon::parse($request->from_date_1)->startOfDay();
    $to = Carbon::parse($request->to_date_1)->endOfDay();

    $query->whereBetween('created_at', [$from, $to]);
}

        if ($request->filled('date_type_2')) {
            switch ($request->date_type_2) {
                case 'monthly':
                    $query->whereMonth('due_date', now()->month);
                    break;
                case 'weekly':
                    $query->whereBetween('due_date', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'daily':
                    $query->whereDate('due_date', now());
                    break;
                default:
                    if ($request->filled('from_date_2') && $request->filled('to_date_2')) {
                        $query->whereBetween('due_date', [$request->from_date_2, $request->to_date_2]);
                    }
            }
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('custom_field')) {
            $query->where('custom_field', 'LIKE', '%' . $request->custom_field . '%');
        }

        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        if ($request->filled('shipping_option')) {
            $query->where('shipping_option', $request->shipping_option);
        }

        if ($request->filled('post_shift')) {
            $query->where('post_shift', 'LIKE', '%' . $request->post_shift . '%');
        }

        if ($request->filled('order_source')) {
            $query->where('order_source', $request->order_source);
        }

        // Paginate the results instead of using get()
        $credits = $query->orderBy('created_at', 'desc')->get(); // Adjust the number of items per page as needed

        // Fetch other required data for the page
        $Credits_number = $this->generateInvoiceNumber();
        $clients = Client::all();
        $users = User::whereIn('role', ['employee', 'manager'])->get();
 $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::creted_note.index', compact(
            'credits',
            'account_setting',
            'users',
            'Credits_number',
            'clients'
        ))->with('search_params', $request->all()); // Return search parameters to maintain form state
    }

// public function sendCreditNotification($id)
// {
//     $credit = CreditNotification::with(['client', 'createdBy'])->findOrFail($id);

//     // تحقق من وجود بريد إلكتروني صالح
//     if (!$credit->client || !$credit->client->email || !filter_var($credit->client->email, FILTER_VALIDATE_EMAIL)) {
//         return redirect()->back()->with('error', 'هذا العميل لا يحتوي على بريد إلكتروني صالح.');
//     }

//     // توليد رابط العرض
//     $url = route('credits.print', $credit->id);

//     // إرسال البريد
//     Mail::to($credit->client->email)->send(new CreditNotificationLinkMail($credit, $url));

//     return redirect()->back()->with('success', 'تم إرسال رابط إشعار الدائن إلى بريد العميل.');
// }
    private function generateInvoiceNumber()
    {
        $lastCredit = CreditNotification::latest()->first();
        return $lastCredit ? $lastCredit->id + 1 : 1;
    }

    public function create()
    {
        $items = Product::all();
        $Credits_number = $this->generateInvoiceNumber();
        $clients = Client::all();
        $users = User::all();
        $taxs = TaxSitting::all();
            $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::creted_note.create', compact('clients','taxs', 'users', 'Credits_number', 'items','account_setting'));
    }
    public function show($id)
    {
        $credit = CreditNotification::with(['client', 'createdBy', 'items'])->findOrFail($id);
 $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice','credit')->get();
   $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        return view('sales::creted_note.show', compact('credit','TaxsInvoice','account_setting'));
    }
    public function edit($id)
    {
        return redirect()
            ->back()
            ->with('error', 'لا يمكنك تعديل الفاتورة رقم ' . $id . '. طبقاً لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقاً لمتطلبات الفاتورة الإلكترونية.');
    }


    public function destroy($id)
    {
        return redirect()->route('CreditNotes.index')->with('error', 'لا يمكنك حذف الفاتورة. طبقاً لتعليمات هيئة الزكاة والدخل يمنع حذف أو تعديل الفاتورة بعد إصدارها وفقاً لمتطلبات الفاتورة الإلكترونية.');
    }

    public function store(Request $request)
    {

        // التحقق من صحة البيانات باستخدام helper function
        $validated = validator($request->all(), [
            'client_id' => 'required|exists:clients,id',
            'credit_date' => 'required|date_format:Y-m-d',
            'release_date' => 'required|date_format:Y-m-d',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id', // إضافة التحقق من المستودع
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

        // try {
            // ** الخطوة الأولى: إنشاء كود للعرض **
            $credit_number = $request->input('credit_number');
            if (!$credit_number) {
                $lastOrder = CreditNotification::orderBy('id', 'desc')->first();
                $nextNumber = $lastOrder ? intval($lastOrder->credit_number) + 1 : 1;
                $credit_number = str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            } else {
                $existingCode = CreditNotification::where('credit_number', $credit_number)->exists();
                if ($existingCode) {
                    return redirect()->back()->withInput()->with('error', 'رقم العرض موجود مسبقاً، الرجاء استخدام رقم آخر');
                }
            }

            // ** تجهيز المتغيرات الرئيسية لحساب العرض **
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
                    'credit_note_id' => null, // سيتم تعيينه لاحقًا بعد إنشاء العرض
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
            $user = Auth::user();
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

        $productDetails = ProductDetails::where('store_house_id', $storeHouse->id)
            ->where('product_id', $item['product_id'])
            ->first();

        if (!$productDetails) {
            $productDetails = ProductDetails::create([
                'store_house_id' => $storeHouse->id,
                'product_id' => $item['product_id'],
                'quantity' => 0,
            ]);
        }

        $proudect = Product::where('id', $item['product_id'])->first();

        if ($proudect->type == 'products') {
            // حساب المخزون قبل وبعد التعديل
            $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
            $stock_before = $total_quantity;
            $stock_after = $stock_before + $quantity;

            // زيادة كمية المنتج في المخزون
            $productDetails->increment('quantity', $quantity);



            $wareHousePermits = new WarehousePermits();
            $wareHousePermits->permission_source_id = 11;
            $wareHousePermits->permission_date = now();
            $wareHousePermits->number = $credit_number; // استخدام رقم إشعار الدائن
            $wareHousePermits->grand_total = $item_total;
            $wareHousePermits->store_houses_id = $storeHouse->id;
            $wareHousePermits->created_by = auth()->user()->id;
            $wareHousePermits->save();

            WarehousePermitsProducts::create([
                'quantity' => $quantity,
                'total' => $item_total,
                'unit_price' => $unit_price,
                'product_id' => $item['product_id'],
                'stock_before' => $stock_before,
                'stock_after' => $stock_after,
                'warehouse_permits_id' => $wareHousePermits->id,
            ]);
        }

            // ** الخطوة الثالثة: حساب الخصم الإضافي للعرض ككل **
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
            $tax_total = 0;
            $tax_type = $tax_type_map[$validated['tax_type']];
            $tax_rate = floatval($validated['tax_rate'] ?? 0); // الحصول على نسبة الضريبة من المستخدم

            $tax_total = 0;

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


            // ** إضافة تكلفة الشحن (إذا وجدت) **
            $shipping_cost = floatval($validated['shipping_cost'] ?? 0);

            // ** حساب ضريبة الشحن (إذا كانت الضريبة مفعلة) **
            $shipping_tax = 0;
            if ($tax_type === 'vat' && $tax_rate > 0) {
                $shipping_tax = ($shipping_cost * $tax_rate) / 100; // ضريبة الشحن باستخدام النسبة التي أدخلها المستخدم
            }

            // ** إضافة ضريبة الشحن إلى tax_total **
            $tax_total += $shipping_tax;

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $tax_total + $shipping_cost;

            // ** الخطوة الرابعة: إنشاء العرض في قاعدة البيانات **
            $creditNot = CreditNotification::create([
                'client_id' => $validated['client_id'],
                'credit_number' => $credit_number,
                'release_date' => $validated['release_date'],
                'credit_date' => $validated['credit_date'],
                'notes' => $validated['notes'] ?? null,
                'created_by' => Auth::id(),
                'discount_amount' => $quote_discount,
                'discount_type' => $discountType === 'percentage' ? 2 : 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $validated['tax_type'], // نحفظ الرقم في قاعدة البيانات
                'tax_rate' => $tax_rate, // حفظ نسبة الضريبة
                'subtotal' => $total_amount,
                'total_discount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'status' => 1, // حالة العرض (1: Draft)
            ]);

             foreach ($request->items as $item) {
    // حساب الإجمالي لكل منتج (السعر × الكمية)
    $item_subtotal = $item['unit_price'] * $item['quantity'];

    // حساب قيمة الضريبة 1 إن وجدت
    if (!empty($item['tax_1_id'])) {
        $tax1 = TaxSitting::find($item['tax_1_id']);
        if ($tax1) {
            $tax_value1 = ($tax1->tax / 100) * $item_subtotal; // حساب قيمة الضريبة كنسبة مئوية من المجموع الجزئي للمنتج
            TaxInvoice::create([
                'name' => $tax1->name,
                'invoice_id' => $creditNot->id,
                'type' => $tax1->type,
                'rate' => $tax1->tax,
                'value' => $tax_value1,
                 'type_invoice' =>     'credit',
            ]);
        }
    }

    // حساب قيمة الضريبة 2 إن وجدت
    if (!empty($item['tax_2_id'])) {
        $tax2 = TaxSitting::find($item['tax_2_id']);
        if ($tax2) {
            $tax_value2 = ($tax2->tax / 100) * $item_subtotal; // حساب قيمة الضريبة كنسبة مئوية من المجموع الجزئي للمنتج
            TaxInvoice::create([
                'name' => $tax2->name,
                'invoice_id' => $creditNot->id,
                'type' => $tax2->type,
                'rate' => $tax2->tax,
                'value' => $tax_value2,
                 'type_invoice' =>     'credit',
            ]);
        }
    }
}

            // ** الخطوة الخامسة: إنشاء سجلات البنود (items) للعرض **
            foreach ($items_data as $item) {
                $item['credit_note_id'] = $creditNot->id;
                InvoiceItem::create($item);
            }

            // تسجيل القيود
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
            $mainAccount = Account::where('name', 'الخزينة الرئيسية')->first();
            if (!$mainAccount) {
                throw new \Exception('حساب  الخزينة الرئيسية غير موجود');
            }


            $clientaccounts = Account::where('client_id', $creditNot->client_id)->first();
            // القيد الاول
            $journalEntry = JournalEntry::create([
                'reference_number' => $creditNot->id,
                'date' => now(),
                'description' => 'اشعار  دائن  رقم ' . $creditNot->id,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $creditNot->client_id,

                // 'created_by_employee' => Auth::id(),
            ]);


            // // 2. حساب مردود المبيعات (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $retursalesnAccount->id, // حساب المبيعات
                'description' => 'اشعار  دائن  رقم ' . $creditNot->id,
                'debit' => $creditNot->grand_total, // المبلغ بعد الخصم (مدين)
                'credit' => 0,
                'is_debit' => false,
            ]);

            // // 2. حساب العميل (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $clientaccounts->id, // حساب المبيعات
                'description' => 'اشعار  دائن  رقم ' . $creditNot->id,
                'debit' => 0,
                'credit' => $creditNot->grand_total, // المبلغ بعد الخصم (دائن)
                'is_debit' => false,
            ]);

            // القيد الثاني
            $journalEntry = JournalEntry::create([
                'reference_number' => $creditNot->id,
                'date' => now(),
                'description' => 'اشعار  دائن  رقم ' . $creditNot->id,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $creditNot->client_id,

                // 'created_by_employee' => Auth::id(),
            ]);

            // // 2. حساب  المخزون (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $storeAccount->id, // حساب المبيعات
                'description' => 'اشعار  دائن  رقم ' . $creditNot->id,
                'debit' => $creditNot->grand_total, // المبلغ بعد الخصم (مدين)
                'credit' => 0,
                'is_debit' => false,
            ]);

            // // 2. حساب العميل (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $costAccount->id, // حساب المبيعات
                'description' => 'اشعار  دائن  رقم ' . $creditNot->id,
                'debit' => 0,
                'credit' => $creditNot->grand_total, // المبلغ بعد الخصم (دائن)
                'is_debit' => false,
            ]);


            if ($clientaccounts) {
                $clientaccounts->balance -= $creditNot->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                $clientaccounts->save();
            }
            if ($storeAccount) {
                $storeAccount->balance += $creditNot->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                $storeAccount->save();
            }
            if ($retursalesnAccount) {
                $retursalesnAccount->balance += $creditNot->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                $retursalesnAccount->save();
            }

            if ($costAccount) {
                $costAccount->balance -= $creditNot->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                $costAccount->save();
            }
            DB::commit();
            return redirect()->route('CreditNotes.index')->with('success', 'تم إنشاء اشعار دائن  بنجاح');
        // } catch (\Exception $e) {
            DB::rollback();
            Log::error('حدث خطأ في دالة store: ' . $e->getMessage());
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'عذراً، حدث خطأ أثناء حفظ  اشعار دائن: ' . $e->getMessage());
        // }
    }

    public function print($id)
    {
        // try {
            $credit = CreditNotification::with(['client', 'createdBy'])->findOrFail($id);
            $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice','credit')->get();

            // تكوين خيارات PDF
            $config = new Options();
            $config->set('defaultFont', 'DejaVu Sans');
            $config->set('isRemoteEnabled', true);
            $config->set('isHtml5ParserEnabled', true);
            $config->set('isFontSubsettingEnabled', true);
            $config->set('isPhpEnabled', true);
            $config->set('chroot', [
                public_path(),
                base_path('vendor/dompdf/dompdf/lib/fonts/'),
                storage_path('fonts/')
            ]);

            // إنشاء كائن Dompdf
            $dompdf = new Dompdf($config);

            // تحميل محتوى HTML مع ترميز UTF-8
            $data = [
                'credit' => $credit,
                'TaxsInvoice' => $TaxsInvoice,
                'company_data' => [
                    'company_name' => 'اسم شركتك',
                    'company_address' => 'عنوان الشركة',
                    'company_phone' => 'رقم الهاتف',
                    'company_email' => 'email@company.com',
                    'company_logo' => asset('path/to/your/logo.png'),
                    'tax_number' => 'الرقم الضريبي للشركة'
                ]
            ];
            $html = view('sales::creted_note.pdf', $data)->render();
            $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

            // تحميل HTML
            $dompdf->loadHtml($html);

            // تعيين حجم الصفحة
            $dompdf->setPaper('A4', 'portrait');

            // تنفيذ PDF
            $dompdf->render();

            // إرجاع الملف للتحميل
            return $dompdf->stream('credit_note_' . $credit->credit_number . '.pdf', [
                'Attachment' => false
            ]);
        // } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إنشاء PDF: ' . $e->getMessage());
        // }
    }




public function sendCreditNotification($id)
{
    $credit = CreditNotification::with(['client', 'createdBy'])->findOrFail($id);

    // تحقق من وجود بريد إلكتروني صالح
    if (!$credit->client || !$credit->client->email || !filter_var($credit->client->email, FILTER_VALIDATE_EMAIL)) {
        return redirect()->back()->with('error', 'هذا العميل لا يحتوي على بريد إلكتروني صالح.');
    }

    // توليد رابط العرض
    $url = route('credits.print', $credit->id);

    // إرسال البريد
    Mail::to($credit->client->email)->send(new CreditNotificationLinkMail($credit, $url));

    return redirect()->back()->with('success', 'تم إرسال رابط إشعار الدائن إلى بريد العميل.');
}

  public function showPrintable($id)
  {
    $credit = CreditNotification::with(['client', 'createdBy'])->findOrFail($id);
    $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice','credit')->get();

    return view('sales::creted_note.pdf', compact('credit', 'TaxsInvoice'));
  }
    private function convertNumberToArabicWords($number)
    {
        $digit1 = ['', 'واحد', 'اثنان', 'ثلاثة', 'أربعة', 'خمسة', 'ستة', 'سبعة', 'ثمانية', 'تسعة'];
        $digit2 = ['', 'عشر', 'عشرون', 'ثلاثون', 'أربعون', 'خمسون', 'ستون', 'سبعون', 'ثمانون', 'تسعون'];
        $digit3 = ['', 'مائة', 'مئتان', 'ثلاثمائة', 'أربعمائة', 'خمسمائة', 'ستمائة', 'سبعمائة', 'ثمانمائة', 'تسعمائة'];

        // تنفيذ المنطق الخاص بتحويل الرقم إلى كلمات
        // يمكنك استخدام مكتبة جاهزة مثل khaled.alshamaa/ar-php

        return ""; // قم بتنفيذ المنطق المناسب هنا
    }
}
