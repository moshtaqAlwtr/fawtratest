<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Modules\Api\Services\InvoiceService;
use Modules\Api\Http\Resources\ClientResource;
use Modules\Api\Http\Resources\InvoiceResource;
use Modules\Api\Http\Resources\InvoiceFullResource;
use App\Http\Requests\StoreInvoiceRequest;
use App\Models\AccountSetting;
use App\Models\Client;
use App\Models\Log as ModelsLog;
use App\Models\ClientRelation;
use App\Models\Employee;
use App\Models\Offer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Writer;
use App\Models\Receipt;

use App\Models\PriceList;
use App\Models\PriceListItems;
use App\Models\Product;
use App\Models\TaxInvoice;
use App\Models\TaxSitting;
use App\Models\Treasury;
use App\Models\User;
use App\Mail\InvoicePdfMail;
use App\Models\Account;
use App\Models\CompiledProducts;
use App\Models\CreditLimit;
use App\Models\DefaultWarehouses;
use App\Models\EmployeeClientVisit;
use App\Models\GiftOffer;
use App\Models\InvoiceItem;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\notifications;
use App\Models\PaymentsProcess;
use App\Models\PermissionSource;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\TreasuryEmployee;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use Illuminate\Support\Facades\Mail;
use TCPDF;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //   protected $invoiceService;

    // public function __construct(InvoiceService $invoiceService)
    // {
    //     $this->invoiceService = $invoiceService;
    // }

    //  public function store(Request $request): JsonResponse
    // {
    //     try {
    //         $invoice = $this->invoiceService->createInvoice($request);

    //         return response()->json([
    //             'success' => true,
    //             'message' => sprintf('تم إنشاء فاتورة المبيعات بنجاح. رقم الفاتورة: %s', $invoice->code),
    //             'data' => [
    //                 'invoice_id' => $invoice->id,
    //                 'invoice_code' => $invoice->code,
    //                 'grand_total' => $invoice->grand_total,
    //             ]
    //         ], 201);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'عذراً، حدث خطأ أثناء حفظ فاتورة المبيعات: ' . $e->getMessage(),
    //         ], 400);
    //     }
    // }

    public function storeee(Request $request)
    {

        try {
            // ** الخطوة الأولى: إنشاء كود للفاتورة **



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
            // بدء المعاملة

            // ** تجهيز المتغيرات الرئيسية لحساب الفاتورة **
            $total_amount = 0; // إجمالي المبلغ قبل الخصومات
            $total_discount = 0; // إجمالي الخصومات على البنود
            $items_data = []; // تجميع بيانات البنود

            // ** الخطوة الثانية: معالجة البنود (items) **
            if ($request->has('items') && count($request->items)) {
                foreach ($request->items as $item) {
                    // التحقق من وجود product_id في البند
                    if (!isset($item['product_id'])) {
                        throw new \Exception('الرجاء اختيار المنتج');
                    }

                    // جلب المنتج
                    $product = Product::find($item['product_id']);
                    if (!$product) {
                        throw new \Exception('المنتج غير موجود: ' . $item['product_id']);
                    }

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

                    // الخزينة الاقتراضيه للموظف
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
            // ✅ استخراج عروض الهدايا
            // ✅ استخراج عروض الهدايا
              $giftOffers = GiftOffer::where('is_active', true) // ✅ شرط التفعيل
                ->where(function ($q) use ($request) {
                    $q->where('is_for_all_clients', true)
                        ->orWhereHas('clients', function ($q2) use ($request) {
                            $q2->where('client_id', $request->client_id);
                        });
                })
                ->where(function ($q) {
                    $q->where('is_for_all_employees', true)
                        ->orWhereHas('users', function ($q2) {
                            $q2->where('user_id', auth()->id());
                        });
                })


                ->whereDate('start_date', '<=', now())
                ->whereDate('end_date', '>=', now())
                ->get();

            // ✅ فحص كل بند مقابل العروض
            foreach ($request->items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];
                $unit_price = floatval($item['unit_price']);

                // 🔍 الحصول على العروض المطابقة لهذا المنتج والكمية
                $validOffers = $giftOffers->filter(function ($offer) use ($productId, $quantity) {
                    $matchesTarget = !$offer->target_product_id || $offer->target_product_id == $productId;
                    return $matchesTarget && $quantity >= $offer->min_quantity;
                });

                // ✅ اختيار أفضل عرض (أعلى عدد هدايا)
                $bestOffer = $validOffers->sortByDesc('gift_quantity')->first();

                if ($bestOffer) {
                    $giftProduct = Product::find($bestOffer->gift_product_id);
                    if (!$giftProduct) continue;

                    $items_data[] = [
                        'invoice_id' => null,
                        'product_id' => $giftProduct->id,
                        'store_house_id' => $store_house_id,
                        'item' => $giftProduct->name . ' (هدية)',
                        'description' => 'هدية عرض عند شراء ' . $quantity . ' من المنتج',
                        'quantity' => $bestOffer->gift_quantity,
                        'unit_price' => 0,
                        'discount' => 0,
                        'discount_type' => 1,
                        'tax_1' => 0,
                        'tax_2' => 0,
                        'total' => 0,
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

            $adjustmentLabel = $request->input('adjustment_label');
            $adjustmentValue = floatval($request->input('adjustment_value', 0));
            $adjustmentType = $request->input('adjustment_type');

            // حساب قيمة التسوية الفعلية
            if ($adjustmentType === 'discount') {
                $adjustmentEffect = -$adjustmentValue;
            } elseif ($adjustmentType === 'addition') {
                $adjustmentEffect = $adjustmentValue;
            } else {
                $adjustmentEffect = 0; // احتياطًا لأي قيمة غير متوقعة
            }

            // ** الحساب النهائي للمجموع الكلي **
            $total_with_tax = $amount_after_discount + $tax_total + $shipping_cost + $adjustmentEffect;




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

            $clientAccount = Account::where('client_id', $request->client_id)->first();

            if ($payment_status == 3) {
                if (
                    !auth()
                        ->user()
                        ->hasAnyPermission(['Issue_an_invoice_to_a_customer_who_has_a_debt'])
                ) {
                    if ($clientAccount && $clientAccount->balance != 0) {
                        return redirect()->back()->with('error', 'عفوا، لا يمكن إصدار فاتورة للعميل. الرجاء سداد المديونية أولًا.');
                    }
                }
            }

            $creditLimit = CreditLimit::first(); // جلب أول حد ائتماني
            if ($payment_status == 3) {
                if ($creditLimit && $total_with_tax + $clientAccount->balance > $creditLimit->value) {
                    return redirect()->back()->with('error', 'عفوا، لقد تجاوز العميل الحد الائتماني. الرجاء سداد المديونية أولًا.');
                }
            }
            // // التحقق من الرمز قبل إنشاء الفاتورة
            // if ($request->verification_code !== '123') {
            //     return response()->json(['error' => 'رمز التحقق غير صحيح.'], 400);
            // }
            // ** الخطوة الرابعة: إنشاء الفاتورة في قاعدة البيانات **



            $invoice = Invoice::create([
                'client_id' => $request->client_id,
                'employee_id' => $request->employee_id,
                'due_value' => $due_value,
                'code' => $code,
                'type' => 'normal',
                'invoice_date' => $request->invoice_date,
                'adjustment_label' => $request->adjustment_label,
                'adjustment_value' => $request->adjustment_value,
                'issue_date' => $request->issue_date,
                'terms' => $request->terms ?? 0,
                'notes' => $request->notes,
                'payment_status' => $payment_status,
                'is_paid' => $is_paid,
                'created_by' => Auth::id(),
                'account_id' => $request->account_id,
                'discount_amount' => $final_total_discount,
                'discount_type' => $request->has('discount_type') ? ($request->discount_type === 'percentage' ? 2 : 1) : 1,
                'advance_payment' => $advance_payment,
                'payment_type' => $request->payment_type ?? 1,
                'shipping_cost' => $shipping_cost,
                'shipping_tax' => $shipping_tax,
                'tax_type' => $request->tax_type ?? 1,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'received_date' => $request->received_date,
                'subtotal' => $total_amount,
                // 'discount_amount' => $final_total_discount,
                'tax_total' => $tax_total,
                'grand_total' => $total_with_tax,
                'paid_amount' => $advance_payment,
                'subscription_id' => $request->subscription_id,
            ]);

            $invoice->qrcode = $this->generateTlvContent($invoice->created_at, $invoice->grand_total, $invoice->tax_total);
            $invoice->save();
                  $client = Client::find($invoice->client_id);

if ($client) {
    // البحث عن آخر زيارة لهذا العميل وتحديث حالتها
    $visit = EmployeeClientVisit::where('employee_id', auth()->id())
        ->where('client_id', $client->id)
        ->latest() // أخذ آخر زيارة
        ->first();

    if ($visit) {
        $visit->update([
            'status' => 'active',
            'updated_at' => now()
        ]);

        Log::info('تم تحديث حالة الزيارة الحالية للفاتورة', [
            'visit_id' => $visit->id,
            'client_id' => $client->id,
            'invoice_id' => $invoice->id
        ]);
    } else {
        Log::warning('لا توجد زيارات مسجلة لهذا العميل للفاتورة', [
            'client_id' => $client->id,
            'employee_id' => auth()->id()
        ]);
    }
}


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
                ModelsLog::create([
                    'type' => 'sales',
                    'type_id' => $invoice->id, // ID النشاط المرتبط
                    'type_log' => 'log', // نوع النشاط
                    'icon' => 'create',
                    'description' => sprintf(
                        'تم انشاء فاتورة مبيعات رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للعميل **%s**',
                        $invoice->code ?? '', // رقم طلب الشراء
                        $item_invoice->product->name ?? '', // اسم المنتج
                        $item['quantity'] ?? '', // الكمية
                        $item['unit_price'] ?? '', // السعر
                        $client_name->trade_name ?? '', // المورد (يتم استخدام %s للنصوص)
                    ),
                    'created_by' => auth()->id(), // ID المستخدم الحالي
                ]);

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

                if ($proudect->type == 'products' || ($proudect->type == 'compiled' && $proudect->compile_type !== 'Instant')) {
                    if ((int) $item['quantity'] > (int) $productDetails->quantity) {
                        throw new \Exception('الكمية المطلوبة (' . $item['quantity'] . ') غير متاحة في المخزون. الكمية المتاحة: ' . $productDetails->quantity);
                    }
                }

                if ($proudect->type == 'products') {
                    // ** حساب المخزون قبل وبعد التعديل **
                    $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;
                    $stock_after = $stock_before - $item['quantity'];

                    // ** تحديث المخزون **
                    $productDetails->decrement('quantity', $item['quantity']);

                    // ** جلب مصدر إذن المخزون المناسب (فاتورة مبيعات) **
                    $permissionSource = PermissionSource::where('name', 'فاتورة مبيعات')->first();

                    if (!$permissionSource) {
                        // لو ما وجدنا مصدر إذن، ممكن ترمي استثناء أو ترجع خطأ
                        throw new \Exception("مصدر إذن 'فاتورة مبيعات' غير موجود في قاعدة البيانات.");
                    }

                    // ** تسجيل المبيعات في حركة المخزون **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = $permissionSource->id; // جلب id المصدر الديناميكي
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تسجيل البيانات في WarehousePermitsProducts **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before, // المخزون قبل التحديث
                        'stock_after' => $stock_after,   // المخزون بعد التحديث
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** تنبيه انخفاض الكمية **
                    if ($productDetails->quantity < $product['low_stock_alert']) {
                        notifications::create([
                            'type' => 'Products',
                            'title' => 'تنبيه الكمية',
                            'description' => 'كمية المنتج ' . $product['name'] . ' قاربت على الانتهاء.',
                        ]);

                        $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

                        $message = "🚨 *تنبيه جديد!* 🚨\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
                        $message .= "📌 *العنوان:* 🔔 `تنبيه الكمية`\n";
                        $message .= '📦 *المنتج:* `' . $product['name'] . "`\n";
                        $message .= "⚠️ *الوصف:* _كمية المنتج قاربت على الانتهاء._\n";
                        $message .= '📅 *التاريخ:* `' . now()->format('Y-m-d H:i') . "`\n";
                        $message .= "━━━━━━━━━━━━━━━━━━━━\n";

                        $response = Http::post($telegramApiUrl, [
                            'chat_id' => '@Salesfatrasmart',
                            'text' => $message,
                            'parse_mode' => 'Markdown',
                            'timeout' => 60,
                        ]);
                    }

                    // ** تنبيه تاريخ انتهاء الصلاحية **
                    if ($product['track_inventory'] == 2 && !empty($product['expiry_date']) && !empty($product['notify_before_days'])) {
                        $expiryDate = Carbon::parse($product['expiry_date']);
                        $daysBeforeExpiry = (int) $product['notify_before_days'];

                        if ($expiryDate->greaterThan(now())) {
                            $remainingDays = floor($expiryDate->diffInDays(now()));

                            if ($remainingDays <= $daysBeforeExpiry) {
                                notifications::create([
                                    'type' => 'Products',
                                    'title' => 'تاريخ الانتهاء',
                                    'description' => 'المنتج ' . $product['name'] . ' قارب على الانتهاء في خلال ' . $remainingDays . ' يوم.',
                                ]);

                                $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

                                $message = "⚠️ *تنبيه انتهاء صلاحية المنتج* ⚠️\n";
                                $message .= "━━━━━━━━━━━━━━━━━━━━\n";
                                $message .= '📌 *اسم المنتج:* ' . $product['name'] . "\n";
                                $message .= '📅 *تاريخ الانتهاء:* ' . $expiryDate->format('Y-m-d') . "\n";
                                $message .= '⏳ *المدة المتبقية:* ' . $remainingDays . " يوم\n";
                                $message .= "━━━━━━━━━━━━━━━━━━━━\n";

                                $response = Http::post($telegramApiUrl, [
                                    'chat_id' => '@Salesfatrasmart',
                                    'text' => $message,
                                    'parse_mode' => 'Markdown',
                                    'timeout' => 60,
                                ]);
                            }
                        }
                    }
                }


                if ($proudect->type == 'compiled' && $proudect->compile_type == 'Instant') {
                    // ** حساب المخزون قبل وبعد التعديل للمنتج التجميعي **
                    $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                    $stock_before = $total_quantity;

                    // ** الحركة الأولى: إضافة الكمية إلى المخزن **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = 1; // إضافة للمخزون منتج مجمع خارجي
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تحديث المخزون: إضافة الكمية **
                    $productDetails->increment('quantity', $item['quantity']); // إضافة الكمية بدلاً من خصمها

                    // ** تسجيل البيانات في WarehousePermitsProducts للإضافة **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before, // المخزون قبل التحديث
                        'stock_after' => $stock_before + $item['quantity'], // المخزون بعد الإضافة
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** الحركة الثانية: خصم الكمية من المخزن **
                    $wareHousePermits = new WarehousePermits();
                    $wareHousePermits->permission_source_id = 10; // خصم من الفاتورة
                    $wareHousePermits->permission_date = $invoice->created_at;
                    $wareHousePermits->number = $invoice->id;
                    $wareHousePermits->grand_total = $invoice->grand_total;
                    $wareHousePermits->store_houses_id = $storeHouse->id;
                    $wareHousePermits->created_by = auth()->user()->id;
                    $wareHousePermits->save();

                    // ** تحديث المخزون: خصم الكمية **
                    $productDetails->decrement('quantity', $item['quantity']); // خصم الكمية

                    // ** تسجيل البيانات في WarehousePermitsProducts للخصم **
                    WarehousePermitsProducts::create([
                        'quantity' => $item['quantity'],
                        'total' => $item['total'],
                        'unit_price' => $item['unit_price'],
                        'product_id' => $item['product_id'],
                        'stock_before' => $stock_before + $item['quantity'], // المخزون قبل الخصم (بعد الإضافة)
                        'stock_after' => $stock_before, // المخزون بعد الخصم (يعود إلى القيمة الأصلية)
                        'warehouse_permits_id' => $wareHousePermits->id,
                    ]);

                    // ** الحصول على المنتجات التابعة للمنتج التجميعي **
                    $CompiledProducts = CompiledProducts::where('compile_id', $item['product_id'])->get();

                    foreach ($CompiledProducts as $compiledProduct) {
                        // ** حساب المخزون قبل وبعد التعديل للمنتج التابع **
                        $total_quantity = DB::table('product_details')->where('product_id', $compiledProduct->product_id)->sum('quantity');
                        $stock_before = $total_quantity;
                        $stock_after = $stock_before - $compiledProduct->qyt * $item['quantity']; // خصم الكمية المطلوبة

                        // ** تسجيل المبيعات في حركة المخزون للمنتج التابع **
                        $wareHousePermits = new WarehousePermits();
                        $wareHousePermits->permission_source_id = 10; // خصم من الفاتورة
                        $wareHousePermits->permission_date = $invoice->created_at;
                        $wareHousePermits->number = $invoice->id;
                        $wareHousePermits->grand_total = $invoice->grand_total;
                        $wareHousePermits->store_houses_id = $storeHouse->id;
                        $wareHousePermits->created_by = auth()->user()->id;
                        $wareHousePermits->save();

                        // ** تسجيل البيانات في WarehousePermitsProducts للمنتج التابع **
                        WarehousePermitsProducts::create([
                            'quantity' => $compiledProduct->qyt * $item['quantity'],
                            'total' => $item['total'],
                            'unit_price' => $item['unit_price'],
                            'product_id' => $compiledProduct->product_id,
                            'stock_before' => $stock_before, // المخزون قبل التحديث
                            'stock_after' => $stock_after, // المخزون بعد التحديث
                            'warehouse_permits_id' => $wareHousePermits->id,
                        ]);

                        // ** تحديث المخزون للمنتج التابع **
                        $compiledProductDetails = ProductDetails::where('store_house_id', $item['store_house_id'])->where('product_id', $compiledProduct->product_id)->first();

                        if (!$compiledProductDetails) {
                            $compiledProductDetails = ProductDetails::create([
                                'store_house_id' => $item['store_house_id'],
                                'product_id' => $compiledProduct->product_id,
                                'quantity' => 0,
                            ]);
                        }

                        $compiledProductDetails->decrement('quantity', $compiledProduct->qyt * $item['quantity']);
                    }
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
                $product = Product::find($item->product_id);
                $productName = $product ? $product->name : 'منتج غير معروف';
                $productsList .= "▫️ *{$productName}* - الكمية: {$item->quantity}, السعر: {$item->unit_price} \n";
            }

            // // رابط API التلقرام
            $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';

            // تجهيز الرسالة
            $message = "📜 *فاتورة جديدة* 📜\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🆔 *رقم الفاتورة:* `$code`\n";
            $message .= '👤 *مسؤول البيع:* ' . ($employee_name->first_name ?? 'لا يوجد') . "\n";
            $message .= '🏢 *العميل:* ' . ($client_name->trade_name ?? 'لا يوجد') . "\n";
            $message .= '✍🏻 *أنشئت بواسطة:* ' . ($user_name->name ?? 'لا يوجد') . "\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= '💰 *المجموع:* `' . number_format($invoice->grand_total, 2) . "` ريال\n";
            $message .= '🧾 *الضريبة:* `' . number_format($invoice->tax_total, 2) . "` ريال\n";
            $message .= '📌 *الإجمالي:* `' . number_format($invoice->tax_total + $invoice->grand_total, 2) . "` ريال\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "📦 *المنتجات:* \n" . $productsList;
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= '📅 *التاريخ:* `' . date('Y-m-d H:i') . "`\n";

            // إرسال الرسالة إلى التلقرام
            $response = Http::post($telegramApiUrl, [
                'chat_id' => '@Salesfatrasmart', // تأكد من أن لديك صلاحية الإرسال للقناة
                'text' => $message,
                'parse_mode' => 'Markdown',
                'timeout' => 30,
            ]);
            notifications::create([
                'type' => 'invoice',
                'title' => $user_name->name . ' أضاف فاتورة لعميل',
                'description' => 'فاتورة للعميل ' . $client_name->trade_name . ' بقيمة ' . number_format($invoice->grand_total, 2) . ' ر.س',
            ]);




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
            $clientaccounts = Account::where('client_id', $invoice->client_id)->first();
            if (!$clientaccounts) {
                throw new \Exception('حساب العميل غير موجود');
            }
            // استرجاع حساب القيمة المضافة المحصلة
            $vatAccount = Account::where('name', 'القيمة المضافة المحصلة')->first();
            if (!$vatAccount) {
                throw new \Exception('حساب القيمة المضافة المحصلة غير موجود');
            }
            $salesAccount = Account::where('name', 'المبيعات')->first();
            if (!$salesAccount) {
                throw new \Exception('حساب المبيعات غير موجود');
            }

            //     // إنشاء القيد المحاسبي للفاتورة
            $journalEntry = JournalEntry::create([
                'reference_number' => $invoice->code,
                'date' => now(),
                'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
                'status' => 1,
                'currency' => 'SAR',
                'client_id' => $invoice->client_id,
                'invoice_id' => $invoice->id,
                // 'created_by_employee' => Auth::id(),
            ]);

            // // إضافة تفاصيل القيد المحاسبي
            // // 1. حساب العميل (مدين)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $clientaccounts->id, // حساب العميل
                'description' => 'فاتورة مبيعات رقم ' . $invoice->code,
                'debit' => $total_with_tax, // المبلغ الكلي للفاتورة (مدين)
                'credit' => 0,
                'is_debit' => true,
            ]);

            // // 2. حساب المبيعات (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $salesAccount->id, // حساب المبيعات
                'description' => 'إيرادات مبيعات',
                'debit' => 0,
                'credit' => $amount_after_discount, // المبلغ بعد الخصم (دائن)
                'is_debit' => false,
            ]);

            // // 3. حساب القيمة المضافة المحصلة (دائن)
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $vatAccount->id, // حساب القيمة المضافة المحصلة
                'description' => 'ضريبة القيمة المضافة',
                'debit' => 0,
                'credit' => $tax_total, // قيمة الضريبة (دائن)
                'is_debit' => false,
            ]);

            // ** تحديث رصيد حساب المبيعات (إيرادات) **
            //  if ($salesAccount) {
            //     $salesAccount->balance += $amount_after_discount; // إضافة المبلغ بعد الخصم
            //     $salesAccount->save();
            // }

            // ** تحديث رصيد حساب المبيعات والحسابات المرتبطة به (إيرادات) **
            if ($salesAccount) {
                $amount = $amount_after_discount;
                $salesAccount->balance += $amount;
                $salesAccount->save();

                // تحديث جميع الحسابات الرئيسية المتصلة به
                // $this->updateParentBalanceSalesAccount($salesAccount->parent_id, $amount);
            }

            // تحديث رصيد حساب الإيرادات (المبيعات + الضريبة)
            $revenueAccount = Account::where('name', 'الإيرادات')->first();
            if ($revenueAccount) {
                $revenueAccount->balance += $amount_after_discount; // المبلغ بعد الخصم (بدون الضريبة)
                $revenueAccount->save();
            }

            // $vatAccount->balance += $tax_total; // قيمة الضريبة
            // $vatAccount->save();

            //تحديث رصيد حساب القيمة المضافة (الخصوم)
            if ($vatAccount) {
                $amount = $tax_total;
                $vatAccount->balance += $amount;
                $vatAccount->save();

                // تحديث جميع الحسابات الرئيسية المتصلة به
                $this->updateParentBalance($vatAccount->parent_id, $amount);
            }

            // تحديث رصيد حساب الأصول (المبيعات + الضريبة)
            $assetsAccount = Account::where('name', 'الأصول')->first();
            if ($assetsAccount) {
                $assetsAccount->balance += $total_with_tax; // المبلغ الكلي (المبيعات + الضريبة)
                $assetsAccount->save();
            }
            // تحديث رصيد حساب الخزينة الرئيسية

            // if ($MainTreasury) {
            //     $MainTreasury->balance += $total_with_tax; // المبلغ الكلي (المبيعات + الضريبة)
            //     $MainTreasury->save();
            // }

            if ($clientaccounts) {
                $clientaccounts->balance += $invoice->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                $clientaccounts->save();
            }


            // تحديث رصيد حساب الخزينة الرئيسية

            // ** الخطوة السابعة: إنشاء سجل الدفع إذا كان هناك دفعة مقدمة أو دفع كامل **
            if ($advance_payment > 0 || $is_paid) {
                $payment_amount = $is_paid ? $total_with_tax : $advance_payment;

                // تحديد الخزينة المستهدفة بناءً على الموظف
                $MainTreasury = null;

                if ($user && $user->employee_id) {
                    // البحث عن الخزينة المرتبطة بالموظف
                    $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();

                    if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                        // إذا كان الموظف لديه خزينة مرتبطة
                        $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
                    } else {
                        // إذا لم يكن لدى الموظف خزينة مرتبطة، استخدم الخزينة الرئيسية
                        $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                    }
                } else {
                    // إذا لم يكن المستخدم موجودًا أو لم يكن لديه employee_id، استخدم الخزينة الرئيسية
                    $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
                }

                // إذا لم يتم العثور على خزينة، توقف العملية وأظهر خطأ
                if (!$MainTreasury) {
                    throw new \Exception('لا توجد خزينة متاحة. يرجى التحقق من إعدادات الخزينة.');
                }

                // إنشاء سجل الدفع
                $payment = PaymentsProcess::create([
                    'invoice_id' => $invoice->id,
                    'amount' => $payment_amount,
                    'payment_date' => now(),
                    'payment_method' => $request->payment_method,
                    'reference_number' => $request->reference_number,
                    'notes' => 'تم إنشاء الدفعة تلقائياً عند إنشاء الفاتورة',
                    'type' => 'client payments',
                    'payment_status' => $payment_status,
                    'created_by' => Auth::id(),
                ]);

                // تحديث رصيد الخزينة
                if ($MainTreasury) {
                    $MainTreasury->balance += $payment_amount;
                    $MainTreasury->save();
                }

                if ($advance_payment > 0) {

                    if ($clientaccounts) {
                        $clientaccounts->balance -= $payment_amount; // المبلغ الكلي (المبيعات + الضريبة)
                        $clientaccounts->save();
                    }
                } else {
                    if ($clientaccounts) {
                        $clientaccounts->balance -= $invoice->grand_total; // المبلغ الكلي (المبيعات + الضريبة)
                        $clientaccounts->save();
                    }
                }

                // إنشاء قيد محاسبي للدفعة
                $paymentJournalEntry = JournalEntry::create([
                    'reference_number' => $payment->reference_number ?? $invoice->code,
                    'date' => now(),
                    'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
                    'status' => 1,
                    'currency' => 'SAR',
                    'client_id' => $invoice->client_id,
                    'invoice_id' => $invoice->id,
                    // 'created_by_employee' => Auth::id(),
                ]);

                // 1. حساب الخزينة المستهدفة (مدين)
                JournalEntryDetail::create([
                    'journal_entry_id' => $paymentJournalEntry->id,
                    'account_id' => $MainTreasury->id,
                    'description' => 'دفعة للفاتورة رقم ' . $invoice->code,
                    'debit' => $payment_amount,
                    'credit' => 0,
                    'is_debit' => true,
                    'client_account_id' => $clientaccounts->id,
                ]);

                // 2. حساب العميل (دائن)
                JournalEntryDetail::create([
                    'journal_entry_id' => $paymentJournalEntry->id,
                    'account_id' => $clientaccounts->id,
                    'description' => 'دفعة عميل  للفاتورة رقم ' . $invoice->code,
                    'debit' => 0,
                    'credit' => $payment_amount,
                    'is_debit' => false,
                    'client_account_id' => $clientaccounts->id,
                ]);
            }
            DB::commit();



            return response()->json([
                'success' => true,
                'message' => sprintf('تم إنشاء فاتورة المبيعات بنجاح. رقم الفاتورة: %s', $invoice->code),
                'data' => [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->code,
                    'grand_total' => $invoice->grand_total,
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، حدث خطأ أثناء حفظ فاتورة المبيعات: ' . $e->getMessage(),
            ], 400);
        }
        //edit
    }


public function generatePdf($id)
{
    $invoice = Invoice::with(['client', 'items', 'createdByUser'])->findOrFail($id);

    // تنظيف بيانات QR من الرموز غير المدعومة
    $clientName = $invoice->client->trade_name
        ?? ($invoice->client->first_name . ' ' . $invoice->client->last_name);

    // إزالة أي رموز غير قابلة للعرض (مثل الإيموجي)
    $cleanClientName = preg_replace('/[^\p{Arabic}\p{Latin}\p{N}\s\p{P}]/u', '', $clientName);

    $qrData = 'رقم الفاتورة: ' . $invoice->id . "\n";
    $qrData .= 'التاريخ: ' . $invoice->created_at->format('Y/m/d') . "\n";
    $qrData .= 'العميل: ' . $cleanClientName . "\n";
    $qrData .= 'الإجمالي: ' . number_format($invoice->grand_total, 2) . ' ر.س';

    // توليد QR باستخدام chillerlan
    $qrOptions = new QROptions([
        'outputType' => QRCode::OUTPUT_IMAGE_PNG,
        'eccLevel' => QRCode::ECC_L,
        'scale' => 5,
        'imageBase64' => true,
    ]);

    $qrCode = new QRCode($qrOptions);
    $barcodeImage = $qrCode->render($qrData);

    $TaxsInvoice = TaxInvoice::where('invoice_id', $id)
        ->where('type_invoice', 'invoice')
        ->get();

    $account_setting = AccountSetting::where('user_id', auth()->id())->first();

    $pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('Fawtra');
    $pdf->SetAuthor('Fawtra System');
    $pdf->SetTitle('فاتورة رقم ' . $invoice->code);
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(0);
    $pdf->SetFooterMargin(0);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->setRTL(true);
    $pdf->SetFont('dejavusans', '', 12); // ✅ يدعم UTF-8 ويريحك من مشاكل الخطوط

    $html = view('sales::invoices.pdf', compact('invoice', 'barcodeImage', 'TaxsInvoice', 'account_setting'))->render();
    $pdf->writeHTML($html, true, false, true, false, '');

    return $pdf->Output('invoice-' . $invoice->code . '.pdf', 'I');
}

 public function print($id)
    {
        $clients = Client::all();
        $employees = Employee::all();
        $invoice = Invoice::find($id);
        // $qrCodeSvg = QrCode::encoding('UTF-8')->size(150)->generate($invoice->qrcode);
        $renderer = new ImageRenderer(
            new RendererStyle(150), // تحديد الحجم
            new SvgImageBackEnd(), // تحديد نوع الصورة (SVG)
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($invoice->qrcode);
        $TaxsInvoice = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'invoice')->get();
        $account_setting = null;

        if (auth()->check()) {
            $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
        }
        $client =  null;
        if (auth()->check()) {
            $client = Client::where('user_id', auth()->user()->id)->first();
        }
        $invoice_number = $this->generateInvoiceNumber();

        // إنشاء رقم الباركود من رقم الفاتورة
        $barcodeNumber = str_pad($invoice->id, 13, '0', STR_PAD_LEFT); // تنسيق الرقم إلى 13 خانة

        // إنشاء رابط الباركود باستخدام خدمة Barcode Generator
        $barcodeImage = 'https://barcodeapi.org/api/128/' . $barcodeNumber;
        $nextCode = Receipt::max('code') ?? 0;

        // نحاول تكرار البحث حتى نحصل على كود غير مكرر
        while (Receipt::where('code', $nextCode)->exists()) {
            $nextCode++;
        }
        // تغيير اسم المتغير من qrCodeImage إلى barcodeImage
        return view('sales::invoices.print', compact('invoice_number', 'account_setting', 'nextCode', 'client', 'clients', 'employees', 'invoice', 'barcodeImage', 'TaxsInvoice', 'qrCodeSvg'));
    }
 public function index(Request $request)
{
    $query = auth()->user()->hasAnyPermission(['sales_view_all_invoices'])
        ? Invoice::with(['client', 'createdByUser', 'updatedByUser'])->where('type', 'normal')
        : Invoice::with(['client', 'createdByUser', 'updatedByUser'])
            ->where(function ($q) {
                $q->where('created_by', auth()->id())
                  ->orWhere('employee_id', auth()->user()->employee_id);
            })->where('type', 'normal');

    $this->applySearchFilters($query, $request);

    $perPage = (int) $request->input('per_page', 30);
    $perPage = $perPage > 0 && $perPage <= 100 ? $perPage : 30;

    $invoices = $query->orderBy('created_at', 'desc')->paginate($perPage);

    return response()->json([
        'success' => true,
        'message' => 'تم جلب الفواتير بنجاح',
        'data' => InvoiceResource::collection($invoices)->resolve(),
        'pagination' => [
            'total' => $invoices->total(),
            'count' => $invoices->count(),
            'per_page' => $invoices->perPage(),
            'current_page' => $invoices->currentPage(),
            'total_pages' => $invoices->lastPage(),
            'next_page_url' => $invoices->nextPageUrl(),
            'prev_page_url' => $invoices->previousPageUrl(),
            'from' => $invoices->firstItem(),
            'to' => $invoices->lastItem(),
            'path' => $invoices->path(),
        ]
    ]);
}


    protected function applySearchFilters($query, $request)
    {
        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('invoice_number')) {
            $query->where('id', $request->invoice_number);
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('item')) {
            $query->whereHas('items', function ($q) use ($request) {
                $q->where('item', 'like', '%' . $request->item . '%');
            });
        }


    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('api::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // Modules/Api/Http/Controllers/InvoiceApiController.php
//  public function store(StoreInvoiceRequest $request): JsonResponse
//     {
//         try {
//             $invoice = app(InvoiceService::class)->createInvoice($request->validated());

//             return response()->json([
//                 'message' => 'تم إنشاء الفاتورة بنجاح',
//                 'data' => new InvoiceResource($invoice),
//             ], 201);
//         } catch (\Throwable $e) {
//             report($e);

//             return response()->json([
//                 'message' => 'فشل إنشاء الفاتورة',
//                 'error' => $e->getMessage(),
//             ], 500);
//         }
//      }
public function createFormData(Request $request): JsonResponse
{
    $user = auth()->user();

    // تحديد العملاء حسب الصلاحيات والوظيفة
    if ($user->role === 'employee' && optional($user->employee)->Job_role_id == 1) {
        $clients = Client::where('branch_id', $user->branch_id)
        ->select('id', 'trade_name as name', 'code')
        ->get();
    } else {
        $clients = Client::select('id', 'trade_name as name', 'code')->get();
    }

    $items = Product::select('id', 'name')->get();
    $users = User::all();
    $treasury = Treasury::all();
    $taxs = TaxSitting::select('id', 'name','tax')->get();
    $price_lists = PriceList::orderBy('id', 'DESC')->get();
    $price_sales = PriceListItems::all();
    $account_setting = AccountSetting::where('user_id', $user->id)->first();
    $offers = Offer::all();

    // الموظفين حسب الصلاحيات
    if ($user->employee_id !== null) {
        if ($user->hasAnyPermission(['sales_view_all_invoices'])) {
            $employees = Employee::all()->sortBy(function ($employee) use ($user) {
                return $employee->id === $user->employee_id ? 0 : 1;
            })->values();
        } else {
            $employees = Employee::where('id', $user->employee_id)->get();
        }
    } else {
        $employees = Employee::all();
    }

    return response()->json([
        // 'invoice_number' => $this->generateInvoiceNumber(),
        'clients' => $clients,
        'items' => $items,
        // 'users' => $users,
        // 'treasury' => $treasury,
        'taxs' => $taxs,
        // 'price_lists' => $price_lists,
        // 'price_sales' => $price_sales,
        // 'account_setting' => $account_setting,
        // 'offers' => $offers,
        // 'employees' => $employees,
        // 'invoiceType' => 'normal',
    ]);
}
 private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::latest()->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Show the specified resource.
     */
   public function show($id): JsonResponse
{
    $invoice = Invoice::with(['client', 'employee', 'items.product'])->findOrFail($id);

    $invoice->setRelation('returns', Invoice::where('reference_number', $id)->get());
    $invoice->setRelation('notes', ClientRelation::where('invoice_id', $id)->get());
    $invoice->setRelation('taxes', TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'invoice')->get());

    $logs = ModelsLog::where('type_log', 'log')
        ->where('type', 'sales')
        ->where('type_id', $id)
        ->orderBy('created_at', 'desc')
        ->get()
        ->groupBy(function ($log) {
            return optional($log->created_at)->format('Y-m-d');
        });

    $invoice->setRelation('logs', $logs);

    // QR code SVG (لو تحتاج توليدها من جديد)


    return response()->json(new InvoiceFullResource($invoice));
}
 private function generateTlvContent($timestamp, $totalAmount, $vatAmount)
    {
        $tlvContent = $this->getTlv(1, 'مؤسسة اعمال خاصة للتجارة') . $this->getTlv(2, '000000000000000') . $this->getTlv(3, $timestamp) . $this->getTlv(4, number_format($totalAmount, 2, '.', '')) . $this->getTlv(5, number_format($vatAmount, 2, '.', ''));

        return base64_encode($tlvContent);
    }
    private function getTlv($tag, $value)
    {
        $value = (string) $value;
        return pack('C', $tag) . pack('C', strlen($value)) . $value;
    }
    private function updateParentBalance($parentId, $amount)
    {
        //تحديث الحسابات المرتبطة بالقيمة المضافة
        if ($parentId) {
            $vatAccount = Account::find($parentId);
            if ($vatAccount) {
                $vatAccount->balance += $amount;
                $vatAccount->save();

                // استدعاء الوظيفة نفسها لتحديث الحساب الأعلى منه
                $this->updateParentBalance($vatAccount->parent_id, $amount);
            }
        }
    }

    private function updateParentBalanceMainTreasury($parentId, $amount)
    {
        // تحديث رصيد الحسابات المرتبطة الخزينة الرئيسية
        if ($parentId) {
            $MainTreasury = Account::find($parentId);
            if ($MainTreasury) {
                $MainTreasury->balance += $amount;
                $MainTreasury->save();

                // استدعاء الوظيفة نفسها لتحديث الحساب الأعلى منه
                $this->updateParentBalance($MainTreasury->parent_id, $amount);
            }
        }
    }
    private function calculateTaxValue($rate, $total)
    {
        return ($rate / 100) * $total;
    }

    private function updateParentBalanceSalesAccount($parentId, $amount)
    {
        // تحديث رصيد الحسابات المرتبطة  المبيعات
        if ($parentId) {
            $MainTreasury = Account::find($parentId);
            if ($MainTreasury) {
                $MainTreasury->balance += $amount;
                $MainTreasury->save();

                // استدعاء الوظيفة نفسها لتحديث الحساب الأعلى منه
                $this->updateParentBalanceSalesAccount($MainTreasury->parent_id, $amount);
            }
        }
    }
public function sendInvoice($id)
{
    $invoice = Invoice::with(['client', 'items', 'createdByUser'])->findOrFail($id);
    $client = $invoice->client;

    if (!$client || !$client->email || !filter_var($client->email, FILTER_VALIDATE_EMAIL)) {
        return response()->json(['message' => 'هذا العميل لا يملك بريد إلكتروني صالح.'], 422);
    }

    // إعداد بيانات QR
    $qrData = 'رقم الفاتورة: ' . $invoice->id . "\n";
    $qrData .= 'التاريخ: ' . $invoice->created_at->format('Y/m/d') . "\n";
    $qrData .= 'العميل: ' . ($client->trade_name ?? $client->first_name . ' ' . $client->last_name) . "\n";
    $qrData .= 'الإجمالي: ' . number_format($invoice->grand_total, 2) . ' ر.س';

    // توليد QR كصورة base64
    $qrCode = new \chillerlan\QRCode\QRCode(
        new \chillerlan\QRCode\QROptions([
            'outputType' => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel' => \chillerlan\QRCode\QRCode::ECC_L,
            'scale' => 5,
            'imageBase64' => true,
        ])
    );
    $barcodeImage = $qrCode->render($qrData);

    // معلومات الفاتورة
    $taxes = TaxInvoice::where('invoice_id', $id)->where('type_invoice', 'invoice')->get();
    $account_setting = AccountSetting::where('user_id', $invoice->created_by)->first();

    // SVG QR Code



    // HTML
    $html = view('print', [
        'invoice' => $invoice,
        'barcodeImage' => $barcodeImage,
        'TaxsInvoice' => $taxes,
        'account_setting' => $account_setting,
        'qrCodeSvg' => 1111,
    ])->render();

    // PDF
    $pdf = new TCPDF();
    $pdf->SetMargins(15, 15, 15);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();
    $pdf->setRTL(true);
    $pdf->SetFont('dejavusans', '', 12);

    $pdf->writeHTML($html, true, false, true, false, '');
    $fileName = 'invoice-' . $invoice->code . '.pdf';
    $filePath = storage_path('app/public/' . $fileName);
    $pdf->Output($filePath, 'F');

    // Send
    Mail::to($client->email)->send(new InvoicePdfMail($invoice, $filePath));

    // Remove file
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    return response()->json(['message' => 'تم إرسال الفاتورة إلى بريد العميل بنجاح.'], 200);
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('api::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}






