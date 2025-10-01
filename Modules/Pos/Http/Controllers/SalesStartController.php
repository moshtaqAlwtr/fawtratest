<?php

namespace Modules\Pos\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\PosGeneralSetting;
use App\Models\InvoiceItem;
use App\Models\PaymentMethod;
use App\Models\PosSession;
use App\Models\PosSessionDetail;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\DefaultWarehouses;
use App\Models\PermissionSource;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use App\Models\CompiledProducts;
use App\Models\notifications;
use App\Models\User;
use App\Models\Employee;
use App\Models\GeneralSettings;
use App\Models\TaxInvoice;
use App\Models\AccountSetting;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Receipt;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Mpdf\Mpdf;
use App\Models\Log as ModelsLog;
use App\Models\TreasuryEmployee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SalesStartController extends Controller
{
    /**
     * عرض الصفحة الرئيسية لنقطة البيع
     */
    public function index()
    {
        // التحقق من وجود جلسة نشطة للموظف
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        
        if (!$activeSession) {
            return redirect()->route('pos::sessions.create')
                ->with('warning', 'يجب بدء جلسة عمل قبل استخدام نقطة البيع');
        }

        try {
            $categories = Category::orderBy('name')
                ->get(['id', 'name', 'attachments']);
            
            $products = Product::with(['category:id,name'])
                ->orderBy('name')
                ->get(['id', 'name', 'sale_price', 'images', 'category_id']);
            
            // معالجة مسارات الصور للمنتجات
            $products = $products->map(function ($product) {
                if ($product->images) {
                    if (!str_starts_with($product->images, 'http') && !str_starts_with($product->images, '/')) {
                        $product->images = '/assets/uploads/product/' . $product->images;
                    }
                } else {
                    $product->images = '/assets/uploads/no_image.jpg';
                }
                return $product;
            });
            
            $clients = Client::orderBy('trade_name')
                ->get(['id', 'trade_name', 'phone']);
            
            $paymentMethods = PaymentMethod::whereIn('id', [1, 2])
                ->orderBy('name')
                ->get(['id', 'name']);
                $defaultCustomerId = PosGeneralSetting::find(1)->default_customer_id ?? null;

            return view('pos::sales_start.index', compact(
                'products', 
                'clients', 
                'categories', 
                'paymentMethods',
                'activeSession',
                'defaultCustomerId'
            ));

        } catch (\Exception $e) {
            Log::error('خطأ في تحميل صفحة نقطة البيع: ' . $e->getMessage());
            
            return view('pos.sales_start.index', [
                'products' => collect([]),
                'clients' => collect([]),
                'categories' => collect([]),
                'paymentMethods' => collect([]),
                'activeSession' => $activeSession
            ])->with('error', 'حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.');
        }
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
        return view('pos.sales_start.print', compact('invoice_number', 'account_setting', 'nextCode', 'client', 'clients', 'employees', 'invoice', 'barcodeImage', 'TaxsInvoice', 'qrCodeSvg'));
    }
     private function generateInvoiceNumber()
    {
        $lastInvoice = Invoice::latest()->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        return str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
    /**
     * بحث متقدم للمنتجات والعملاء والفواتير
     */
    public function search(Request $request)
    {
        // التحقق من وجود جلسة نشطة
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة نشطة. يجب بدء جلسة عمل أولاً.',
                'redirect' => route('pos.sessions.create')
            ], 403);
        }

        try {
            $query = trim($request->input('query', ''));
            $type = $request->input('type', 'all'); // all, products, clients, invoices
            $category = $request->input('category');
            $limit = min((int) $request->input('limit', 20), 50);

            $results = [];

            if (empty($query) && $type !== 'invoices') {
                return response()->json([
                    'success' => true,
                    'products' => [],
                    'clients' => [],
                    'invoices' => [],
                    'message' => 'يرجى إدخال كلمة للبحث'
                ]);
            }

            // البحث في المنتجات
            if ($type === 'all' || $type === 'products') {
                $productsQuery = Product::with(['category:id,name'])
                    ->where('status', 0)
                    ->where(function ($q) use ($query) {
                        $q->where('name', 'LIKE', "%{$query}%")
                          ->orWhere('code', 'LIKE', "%{$query}%")
                          ->orWhere('description', 'LIKE', "%{$query}%");
                    });

                if ($category) {
                    $productsQuery->where('category_id', $category);
                }

                $products = $productsQuery->limit($limit)
                    ->get(['id', 'name', 'sale_price', 'images', 'category_id', 'code'])
                    ->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'sale_price' => (float) $product->sale_price,
                            'code' => $product->code,
                            'category_id' => $product->category_id,
                            'category_name' => optional($product->category)->name,
                            'images' => $product->images ? asset($product->images) : asset('assets/images/default.png'),
                            'type' => 'product',
                            'type' => $invoice->type, // إضافة النوع
                    'reference_number' => $invoice->reference_number, // إضافة المرجع
                        ];
                    });

                $results['products'] = $products;
            }

            // البحث في العملاء
            if ($type === 'all' || $type === 'clients') {
                $clients = Client::where('status', 0)
                    ->where(function ($q) use ($query) {
                        $q->where('trade_name', 'LIKE', "%{$query}%")
                          ->orWhere('phone', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->orWhere('code', 'LIKE', "%{$query}%");
                    })
                    ->limit($limit)
                    ->get(['id', 'trade_name', 'phone', 'email'])
                    ->map(function ($client) {
                        return [
                            'id' => $client->id,
                            'trade_name' => $client->trade_name,
                            'phone' => $client->phone,
                            'email' => $client->email,
                            'type' => 'client'
                        ];
                    });

                $results['clients'] = $clients;
            }

            // البحث في فواتير الجلسة الحالية
            if ($type === 'all' || $type === 'invoices') {
                $invoicesQuery = Invoice::with(['client:id,trade_name'])
                    ->where('type', 'pos')
                    ->where('session_id', $activeSession->id); // فقط فواتير الجلسة الحالية

                if (!empty($query)) {
                    $invoicesQuery->where(function ($q) use ($query) {
                        $q->where('code', 'LIKE', "%{$query}%")
                          ->orWhereHas('client', function ($clientQuery) use ($query) {
                              $clientQuery->where('trade_name', 'LIKE', "%{$query}%");
                          });
                    });
                }

                $invoices = $invoicesQuery->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get(['id', 'code', 'client_id', 'invoice_date', 'grand_total', 'payment_status', 'created_at'])
                    ->map(function ($invoice) {
                        return [
                            'id' => $invoice->id,
                            'code' => $invoice->code,
                            'client_id' => $invoice->client_id,
                            'client_name' => optional($invoice->client)->trade_name,
                            'invoice_date' => $invoice->invoice_date,
                            'grand_total' => (float) $invoice->grand_total,
                            'payment_status' => $invoice->payment_status,
                            'created_at' => $invoice->created_at->toDateTimeString(),
                            'type' => 'invoice'
                        ];
                    });

                $results['invoices'] = $invoices;
            }

            return response()->json([
                'success' => true,
                'query' => $query,
                'session_id' => $activeSession->id,
                'results_count' => [
                    'products' => isset($results['products']) ? $results['products']->count() : 0,
                    'clients' => isset($results['clients']) ? $results['clients']->count() : 0,
                    'invoices' => isset($results['invoices']) ? $results['invoices']->count() : 0,
                ],
                ...$results
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في البحث: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث. يرجى المحاولة مرة أخرى.'
            ], 500);
        }
    }

    /**
     * تخزين الفاتورة مع ربطها بالجلسة
     */
  public function store(Request $request)
{
    // التحقق من وجود جلسة نشطة
    $activeSession = PosSession::active()->forUser(auth()->id())->first();
    if (!$activeSession) {
        return response()->json([
            'success' => false,
            'message' => 'لا توجد جلسة نشطة. يجب بدء جلسة عمل أولاً.',
            'redirect' => route('pos.sessions.create')
        ], 403);
    }

    $validated = $request->validate([
        'client_id' => 'nullable|integer|exists:clients,id',
        'client_name' => 'nullable|string|max:255',
        'products' => 'required|array|min:1',
        'products.*.id' => 'required|integer|exists:products,id',
        'products.*.name' => 'required|string|max:255',
        'products.*.unit_price' => 'required|numeric|min:0',
        'products.*.quantity' => 'required|numeric|min:0.01',
        'products.*.total' => 'required|numeric|min:0',
        'discount_type' => 'nullable|string|in:amount,percentage',
        'discount_value' => 'nullable|numeric|min:0',
        'total' => 'required|numeric|min:0',
        'net_total' => 'required|numeric|min:0',
        'payments' => 'nullable|array',
        'payments.*.method_id' => 'required_with:payments|integer|exists:payment_methods,id',
        'payments.*.amount' => 'required_with:payments|numeric|min:0.01',
    ]);

    DB::beginTransaction();
    
    try {
        // التحقق من توفر المنتجات والمخزون
        $productIds = collect($validated['products'])->pluck('id');
        $availableProducts = Product::whereIn('id', $productIds)
            ->where('status', 0)
            ->get();

        if ($productIds->count() !== $availableProducts->count()) {
            throw new \Exception('بعض المنتجات غير متوفرة أو تم إلغاؤها.');
        }

        // الحصول على المستودع المناسب للموظف
        $storeHouse = $this->getEmployeeWarehouse();
        if (!$storeHouse) {
            throw new \Exception('لا يوجد أي مستودع في النظام. الرجاء إضافة مستودع واحد على الأقل.');
        }

        // التحقق من توفر الكميات المطلوبة في المخزون قبل البدء
        foreach ($validated['products'] as $productData) {
            $product = $availableProducts->where('id', $productData['id'])->first();
            if (!$product) {
                throw new \Exception("المنتج غير موجود: {$productData['id']}");
            }

            // التحقق من المخزون للمنتجات العادية والمنتجات المجمعة
            if ($product->type == 'products' || ($product->type == 'compiled' && $product->compile_type !== 'Instant')) {
                $productDetails = ProductDetails::where('store_house_id', $storeHouse->id)
                    ->where('product_id', $product->id)
                    ->first();

                $availableQuantity = $productDetails ? $productDetails->quantity : 0;
                
                
              $enable_negative_stock = GeneralSettings::first()->enable_negative_stock; // 0 = غير مفعل, 1 = مفعل

if ($productData['quantity'] > $availableQuantity && $enable_negative_stock == 0) {
    return response()->json([
        'success' => false,
        'message' => "الكمية المطلوبة ({$productData['quantity']}) غير متاحة في المخزون للمنتج '{$product->name}'. الكمية المتاحة: {$availableQuantity}"
    ], 200);
}


            }

            // التحقق من المنتجات المجمعة الفورية
            if ($product->type == 'compiled' && $product->compile_type == 'Instant') {
                $compiledProducts = CompiledProducts::where('compile_id', $product->id)->get();
                
                foreach ($compiledProducts as $compiledProduct) {
                    $requiredQuantity = $compiledProduct->qyt * $productData['quantity'];
                    $productDetails = ProductDetails::where('store_house_id', $storeHouse->id)
                        ->where('product_id', $compiledProduct->product_id)
                        ->first();
                    
                    $availableQuantity = $productDetails ? $productDetails->quantity : 0;
                    
                    if ($requiredQuantity > $availableQuantity) {
                        $subProduct = Product::find($compiledProduct->product_id);
                        throw new \Exception("الكمية المطلوبة ({$requiredQuantity}) غير متاحة في المخزون للمنتج الفرعي '{$subProduct->name}' ضمن المنتج المجمع '{$product->name}'. الكمية المتاحة: {$availableQuantity}");
                    }
                }
            }
        }

        // حساب المجاميع
        $subtotal = collect($validated['products'])->sum('total');
        $discountAmount = $this->calculateDiscount(
            $subtotal, 
            $validated['discount_type'] ?? 'amount',
            $validated['discount_value'] ?? 0
        );
        $grandTotal = max(0, $subtotal - $discountAmount);

        // التحقق من مطابقة المجاميع
        if (abs($grandTotal - $validated['net_total']) > 0.01) {
            throw new \Exception('خطأ في حساب المجموع النهائي.');
        }

        // معالجة المدفوعات
        $totalPaid = 0;
        $cashAmount = 0;
        $cardAmount = 0;
        
        if (!empty($validated['payments'])) {
            foreach ($validated['payments'] as $payment) {
                $totalPaid += $payment['amount'];
                if ($payment['method_id'] == 1) {
                    $cashAmount += $payment['amount'];
                } else {
                    $cardAmount += $payment['amount'];
                }
            }
        }

        // تحديد حالة الدفع
        $paymentStatus = 1; // مدفوع بالكامل (افتراضي لـ POS)
        $isPaid = true;
        $dueValue = 0;

        if ($totalPaid < $grandTotal - 0.01) {
            $paymentStatus = 2; // دفع جزئي
            $isPaid = false;
            $dueValue = $grandTotal - $totalPaid;
        }

        // إنشاء الفاتورة مع ربطها بالجلسة
        $invoice = $this->createInvoice($validated, $subtotal, $discountAmount, $grandTotal, $paymentStatus, $isPaid, $dueValue, $totalPaid, $activeSession->id);

        // إضافة عناصر الفاتورة مع تحديث المخزون
        $this->createInvoiceItemsWithInventoryManagement($invoice->id, $validated['products'], $storeHouse, $invoice);

        // معالجة المدفوعات وإضافتها لتفاصيل الجلسة
        if (!empty($validated['payments'])) {
            $this->processPayments($invoice->id, $validated['payments']);
            
            // إضافة المعاملة لتفاصيل الجلسة
            $this->addTransactionToSession($activeSession->id, [
                'type' => 'sale',
                'reference' => $invoice->code,
                'amount' => $grandTotal,
                'payment_method' => count($validated['payments']) > 1 ? 'mixed' : 
                    ($validated['payments'][0]['method_id'] == 1 ? 'cash' : 'card'),
                'cash_amount' => $cashAmount,
                'card_amount' => $cardAmount,
                'description' => "بيع - فاتورة رقم {$invoice->code}",
                'metadata' => json_encode([
                    'invoice_id' => $invoice->id,
                    'products_count' => count($validated['products']),
                    'client_id' => $validated['client_id'] ?? null
                ])
            ]);
        }

        // تحديث إحصائيات الجلسة
        $this->updateSessionStatistics($activeSession);

        // إرسال إشعارات
        $this->sendInvoiceNotifications($invoice, $validated['products']);

        DB::commit();

        return response()->json([
            'success' => true,
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->code,
            'session_id' => $activeSession->id,
            'payment_status' => $paymentStatus,
            'total_paid' => $totalPaid,
            'due_amount' => $dueValue,
            'change_amount' => max(0, $totalPaid - $grandTotal),
            'message' => 'تم إنشاء الفاتورة بنجاح وربطها بالجلسة'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('خطأ في حفظ الفاتورة: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'session_id' => $activeSession->id,
            'request_data' => $request->all()
        ]);

        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}
/**
 * الحصول على المستودع المناسب للموظف
 */
private function getEmployeeWarehouse()
{
    $user = Auth::user();

    // التحقق مما إذا كان للمستخدم employee_id والبحث عن المستودع الافتراضي
    if ($user && $user->employee_id) {
        $defaultWarehouse = DefaultWarehouses::where('employee_id', $user->employee_id)->first();

        if ($defaultWarehouse && $defaultWarehouse->storehouse_id) {
            $storeHouse = StoreHouse::find($defaultWarehouse->storehouse_id);
        } else {
            $storeHouse = StoreHouse::where('major', 1)->first();
        }
    } else {
        $storeHouse = StoreHouse::where('major', 1)->first();
    }

    // إذا لم يتم العثور على مستودع، ابحث عن أي مستودع متاح
    if (!$storeHouse) {
        $storeHouse = StoreHouse::first();
    }

    return $storeHouse;
}

/**
 * إنشاء عناصر الفاتورة مع إدارة المخزون
 */
private function createInvoiceItemsWithInventoryManagement($invoiceId, $products, $storeHouse, $invoice)
{
    $items = [];
    
    foreach ($products as $productData) {
        $product = Product::find($productData['id']);
        if (!$product) {
            throw new \Exception("المنتج غير موجود: {$productData['id']}");
        }

        // إنشاء عنصر الفاتورة
        // حساب الضريبة من السعر الشامل
$priceIncludingTax = $productData['unit_price'];
$priceExcludingTax = $priceIncludingTax / 1.15; // السعر قبل الضريبة
$taxAmount = $priceIncludingTax - $priceExcludingTax; // مقدار الضريبة
$totalExcludingTax = $priceExcludingTax * $productData['quantity'];
$totalTaxAmount = $taxAmount * $productData['quantity'];

$item = [
    'invoice_id' => $invoiceId,
    'product_id' => $product->id,
    'store_house_id' => $storeHouse->id,
    'item' => $product->name,
    'description' => $product->name,
    'unit_price' => $priceExcludingTax, // السعر قبل الضريبة
    'quantity' => $productData['quantity'],
    'discount' => 0,
    'tax_1' => $totalTaxAmount, // إجمالي الضريبة للعنصر
    'tax_2' => 0,
    'total' => $productData['total'], // الإجمالي شامل الضريبة
    'type' => 'product',
    'created_at' => now(),
    'updated_at' => now()
];

        $itemInvoice = InvoiceItem::create($item);

        // إدارة المخزون حسب نوع المنتج
        $this->manageProductInventory($product, $productData['quantity'], $storeHouse, $invoice, $itemInvoice);

        $items[] = $item;
    }

    return $items;
}
/**
 * إدارة مخزون المنتج
 */
private function manageProductInventory($product, $quantity, $storeHouse, $invoice, $itemInvoice)
{
    // الحصول على أو إنشاء سجل تفاصيل المنتج في المستودع
    $productDetails = ProductDetails::firstOrCreate(
        [
            'store_house_id' => $storeHouse->id,
            'product_id' => $product->id
        ],
        ['quantity' => 0]
    );

    if ($product->type == 'products') {
        $this->handleRegularProduct($product, $quantity, $productDetails, $storeHouse, $invoice, $itemInvoice);
    } elseif ($product->type == 'compiled' && $product->compile_type == 'Instant') {
        $this->handleInstantCompiledProduct($product, $quantity, $productDetails, $storeHouse, $invoice, $itemInvoice);
    } elseif ($product->type == 'compiled' && $product->compile_type !== 'Instant') {
        $this->handleCompiledProduct($product, $quantity, $productDetails, $storeHouse, $invoice, $itemInvoice);
    }
}
/**
* معالجة المنتج العادي
 */
private function handleRegularProduct($product, $quantity, $productDetails, $storeHouse, $invoice, $itemInvoice)
{
    // حساب المخزون قبل وبعد التعديل
    $totalQuantity = DB::table('product_details')->where('product_id', $product->id)->sum('quantity');
    $stockBefore = $totalQuantity;
    $stockAfter = $stockBefore - $quantity;

    // تحديث المخزون
    $productDetails->decrement('quantity', $quantity);

    // جلب مصدر إذن المخزون المناسب
    $permissionSource = PermissionSource::where('name', 'فاتورة مبيعات')->first();
    if (!$permissionSource) {
        $permissionSource = PermissionSource::firstOrCreate(['name' => 'فاتورة مبيعات']);
    }

    // تسجيل المبيعات في حركة المخزون
    $wareHousePermits = WarehousePermits::create([
        'permission_type' => $permissionSource->id,
        'permission_date' => $invoice->created_at,
        'number' => $invoice->id,
        'grand_total' => $invoice->grand_total,
        'store_houses_id' => $storeHouse->id,
        'created_by' => auth()->id(),
    ]);

    // تسجيل البيانات في WarehousePermitsProducts
    WarehousePermitsProducts::create([
        'quantity' => $quantity,
        'total' => $itemInvoice->total,
        'unit_price' => $itemInvoice->unit_price,
        'product_id' => $product->id,
        'stock_before' => $stockBefore,
        'stock_after' => $stockAfter,
        'warehouse_permits_id' => $wareHousePermits->id,
    ]);

    // تحقق من تنبيهات انخفاض الكمية
    $this->checkLowStockAlert($product, $productDetails);
    $this->checkExpiryAlert($product);
}

/**
 * معالجة المنتج المجمع الفوري
 */
private function handleInstantCompiledProduct($product, $quantity, $productDetails, $storeHouse, $invoice, $itemInvoice)
{
    $totalQuantity = DB::table('product_details')->where('product_id', $product->id)->sum('quantity');
    $stockBefore = $totalQuantity;

    // الحركة الأولى: إضافة الكمية إلى المخزن
    $addPermissionSource = PermissionSource::firstOrCreate(['name' => 'إضافة منتج مجمع فوري']);
    $wareHousePermitsAdd = WarehousePermits::create([
        'permission_type' => $addPermissionSource->id,
        'permission_date' => $invoice->created_at,
        'number' => $invoice->id,
        'grand_total' => $invoice->grand_total,
        'store_houses_id' => $storeHouse->id,
        'created_by' => auth()->id(),
    ]);

    // تحديث المخزون: إضافة الكمية
    $productDetails->increment('quantity', $quantity);

    WarehousePermitsProducts::create([
        'quantity' => $quantity,
        'total' => $itemInvoice->total,
        'unit_price' => $itemInvoice->unit_price,
        'product_id' => $product->id,
        'stock_before' => $stockBefore,
        'stock_after' => $stockBefore + $quantity,
        'warehouse_permits_id' => $wareHousePermitsAdd->id,
    ]);

    // الحركة الثانية: خصم الكمية من المخزن
    $salePermissionSource = PermissionSource::firstOrCreate(['name' => 'فاتورة مبيعات']);
    $wareHousePermitsSale = WarehousePermits::create([
        'permission_type' => $salePermissionSource->id,
        'permission_date' => $invoice->created_at,
        'number' => $invoice->id,
        'grand_total' => $invoice->grand_total,
        'store_houses_id' => $storeHouse->id,
        'created_by' => auth()->id(),
    ]);

    // تحديث المخزون: خصم الكمية
    $productDetails->decrement('quantity', $quantity);

    WarehousePermitsProducts::create([
        'quantity' => $quantity,
        'total' => $itemInvoice->total,
        'unit_price' => $itemInvoice->unit_price,
        'product_id' => $product->id,
        'stock_before' => $stockBefore + $quantity,
        'stock_after' => $stockBefore,
        'warehouse_permits_id' => $wareHousePermitsSale->id,
    ]);

    // معالجة المنتجات التابعة للمنتج المجمع
    $compiledProducts = CompiledProducts::where('compile_id', $product->id)->get();
    foreach ($compiledProducts as $compiledProduct) {
        $this->handleCompiledSubProduct($compiledProduct, $quantity, $storeHouse, $invoice, $itemInvoice);
    }
}

/**
 * معالجة المنتج المجمع العادي
 */
private function handleCompiledProduct($product, $quantity, $productDetails, $storeHouse, $invoice, $itemInvoice)
{
    // التحقق من توفر الكميات المطلوبة من المنتجات الفرعية
    $compiledProducts = CompiledProducts::where('compile_id', $product->id)->get();
    
    foreach ($compiledProducts as $compiledProduct) {
        $requiredQuantity = $compiledProduct->qyt * $quantity;
        $subProductDetails = ProductDetails::where('store_house_id', $storeHouse->id)
            ->where('product_id', $compiledProduct->product_id)
            ->first();
        
        if (!$subProductDetails || $subProductDetails->quantity < $requiredQuantity) {
            $subProduct = Product::find($compiledProduct->product_id);
            throw new \Exception("الكمية المطلوبة ({$requiredQuantity}) غير متاحة في المخزون للمنتج الفرعي '{$subProduct->name}'");
        }
    }

    // خصم المنتج المجمع من المخزون
    $this->handleRegularProduct($product, $quantity, $productDetails, $storeHouse, $invoice, $itemInvoice);
}

/**
 * معالجة المنتج الفرعي في المنتج المجمع
 */
private function handleCompiledSubProduct($compiledProduct, $parentQuantity, $storeHouse, $invoice, $itemInvoice)
{
    $requiredQuantity = $compiledProduct->qyt * $parentQuantity;
    $subProduct = Product::find($compiledProduct->product_id);
    
    if (!$subProduct) return;

    $subProductDetails = ProductDetails::firstOrCreate(
        [
            'store_house_id' => $storeHouse->id,
            'product_id' => $subProduct->id
        ],
        ['quantity' => 0]
    );

    // حساب المخزون قبل وبعد التعديل
    $totalQuantity = DB::table('product_details')->where('product_id', $subProduct->id)->sum('quantity');
    $stockBefore = $totalQuantity;
    $stockAfter = $stockBefore - $requiredQuantity;

    // تسجيل المبيعات في حركة المخزون
    $permissionSource = PermissionSource::firstOrCreate(['name' => 'فاتورة مبيعات']);
    $wareHousePermits = WarehousePermits::create([
        'permission_type' => $permissionSource->id,
        'permission_date' => $invoice->created_at,
        'number' => $invoice->id,
        'grand_total' => $invoice->grand_total,
        'store_houses_id' => $storeHouse->id,
        'created_by' => auth()->id(),
    ]);

    WarehousePermitsProducts::create([
        'quantity' => $requiredQuantity,
        'total' => $itemInvoice->total,
        'unit_price' => $itemInvoice->unit_price,
        'product_id' => $subProduct->id,
        'stock_before' => $stockBefore,
        'stock_after' => $stockAfter,
        'warehouse_permits_id' => $wareHousePermits->id,
    ]);

    // تحديث المخزون للمنتج الفرعي
    $subProductDetails->decrement('quantity', $requiredQuantity);

    // تحقق من التنبيهات
    $this->checkLowStockAlert($subProduct, $subProductDetails);
}

/**
 * تحقق من تنبيه انخفاض الكمية
 */
private function checkLowStockAlert($product, $productDetails)
{
    if (isset($product->low_stock_alert) && $productDetails->quantity < $product->low_stock_alert) {
        // إنشاء إشعار في النظام
        notifications::create([
            'type' => 'Products',
            'title' => 'تنبيه الكمية',
            'description' => "كمية المنتج '{$product->name}' قاربت على الانتهاء. الكمية المتبقية: {$productDetails->quantity}",
        ]);

        // إرسال إشعار تليجرام
        $this->sendTelegramAlert([
            'type' => 'low_stock',
            'product_name' => $product->name,
            'remaining_quantity' => $productDetails->quantity,
            'alert_level' => $product->low_stock_alert
        ]);
    }
}

/**
 * تحقق من تنبيه انتهاء الصلاحية
 */
private function checkExpiryAlert($product)
{
    if ($product->track_inventory == 2 && 
        !empty($product->expiry_date) && 
        !empty($product->notify_before_days)) {
        
        $expiryDate = Carbon::parse($product->expiry_date);
        $daysBeforeExpiry = (int) $product->notify_before_days;

        if ($expiryDate->greaterThan(now())) {
            $remainingDays = floor($expiryDate->diffInDays(now()));

            if ($remainingDays <= $daysBeforeExpiry) {
                notifications::create([
                    'type' => 'Products',
                    'title' => 'تاريخ الانتهاء',
                    'description' => "المنتج '{$product->name}' قارب على الانتهاء في خلال {$remainingDays} يوم.",
                ]);

                $this->sendTelegramAlert([
                    'type' => 'expiry',
                    'product_name' => $product->name,
                    'expiry_date' => $expiryDate->format('Y-m-d'),
                    'remaining_days' => $remainingDays
                ]);
            }
        }
    }
}

/**
 * إرسال تنبيه تليجرام
 */
private function sendTelegramAlert($data)
{
    try {
        $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';
        
        if ($data['type'] === 'low_stock') {
            $message = "🚨 *تنبيه جديد!* 🚨\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "📌 *العنوان:* 🔔 `تنبيه الكمية`\n";
            $message .= "📦 *المنتج:* `{$data['product_name']}`\n";
            $message .= "⚠️ *الكمية المتبقية:* `{$data['remaining_quantity']}`\n";
            $message .= "📅 *التاريخ:* `" . now()->format('Y-m-d H:i') . "`\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        } elseif ($data['type'] === 'expiry') {
            $message = "⚠️ *تنبيه انتهاء صلاحية المنتج* ⚠️\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "📌 *اسم المنتج:* `{$data['product_name']}`\n";
            $message .= "📅 *تاريخ الانتهاء:* `{$data['expiry_date']}`\n";
            $message .= "⏳ *المدة المتبقية:* `{$data['remaining_days']} يوم`\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        }

        Http::timeout(60)->post($telegramApiUrl, [
            'chat_id' => '@Salesfatrasmart',
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);
    } catch (\Exception $e) {
        Log::warning('فشل في إرسال تنبيه تليجرام: ' . $e->getMessage());
    }
}

/**
 * إرسال إشعارات الفاتورة
 */
private function sendInvoiceNotifications($invoice, $products)
{
    try {
        $user = User::find($invoice->created_by);
        $client = Client::find($invoice->client_id);
        
        // إنشاء إشعار في النظام
        notifications::create([
            'type' => 'invoice',
            'title' => ($user->name ?? 'مستخدم') . ' أضاف فاتورة POS',
            'description' => 'فاتورة POS للعميل ' . ($client->trade_name ?? 'عميل نقدي') . ' بقيمة ' . number_format($invoice->grand_total, 2) . ' ر.س',
        ]);

        // تجهيز قائمة المنتجات لإشعار تليجرام
        $productsList = '';
        foreach ($products as $productData) {
            $product = Product::find($productData['id']);
            $productName = $product ? $product->name : 'منتج غير معروف';
            $productsList .= "▫️ *{$productName}* - الكمية: {$productData['quantity']}, السعر: {$productData['unit_price']} \n";
        }

        // إرسال إشعار تليجرام
        $telegramApiUrl = 'https://api.telegram.org/bot7642508596:AAHQ8sST762ErqUpX3Ni0f1WTeGZxiQWyXU/sendMessage';
        
        $message = "📜 *فاتورة POS جديدة* 📜\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🆔 *رقم الفاتورة:* `{$invoice->code}`\n";
        $message .= "🏢 *العميل:* " . ($client->trade_name ?? 'عميل نقدي') . "\n";
        $message .= "✍🏻 *أنشئت بواسطة:* " . ($user->name ?? 'مستخدم') . "\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💰 *المجموع:* `" . number_format($invoice->grand_total, 2) . "` ريال\n";
        $message .= "📌 *نوع الفاتورة:* `نقطة بيع (POS)`\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📦 *المنتجات:* \n" . $productsList;
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "📅 *التاريخ:* `" . now()->format('Y-m-d H:i') . "`\n";

        Http::timeout(30)->post($telegramApiUrl, [
            'chat_id' => '@Salesfatrasmart',
            'text' => $message,
            'parse_mode' => 'Markdown',
        ]);

    } catch (\Exception $e) {
        Log::warning('فشل في إرسال إشعارات الفاتورة: ' . $e->getMessage());
    }
}

/**
 * إنشاء سجل في نظام اللوجز
 */
private function createInvoiceLog($invoice, $product, $quantity, $unitPrice, $clientName)
{
    try {
        ModelsLog::create([
            'type' => 'sales',
            'type_id' => $invoice->id,
            'type_log' => 'log',
            'icon' => 'create',
            'description' => sprintf(
                'تم إنشاء فاتورة POS رقم **%s** للمنتج **%s** كمية **%s** بسعر **%s** للعميل **%s**',
                $invoice->code ?? '',
                $product->name ?? '',
                $quantity ?? '',
                $unitPrice ?? '',
                $clientName ?? ''
            ),
            'created_by' => auth()->id(),
        ]);
    } catch (\Exception $e) {
        Log::warning('فشل في إنشاء سجل اللوج: ' . $e->getMessage());
    }
}
   /**
 * إنشاء الفاتورة مع ربطها بالجلسة - نسخة محدثة
 */
private function createInvoice($data, $subtotal, $discountAmount, $grandTotal, $paymentStatus, $isPaid, $dueValue, $totalPaid, $sessionId)
{
    // إنشاء كود للفاتورة
    $lastInvoice = Invoice::where('type', 'pos')->orderBy('id', 'desc')->first();
    $nextNumber = $lastInvoice ? intval(substr($lastInvoice->code, -5)) + 1 : 1;
    
    while (Invoice::where('code', 'POS' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT))->exists()) {
        $nextNumber++;
    }
    
    $code = 'POS' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    $defaultClient = PosGeneralSetting::find(1)->default_customer_id ?? null;
// حساب إجمالي الضريبة للفاتورة
$taxTotal = 0;
foreach ($data['products'] as $productData) {
    $priceIncludingTax = $productData['unit_price'];
    $priceExcludingTax = $priceIncludingTax / 1.15;
    $taxAmount = $priceIncludingTax - $priceExcludingTax;
    $taxTotal += $taxAmount * $productData['quantity'];
}
    $invoice = Invoice::create([
        'client_id' => $data['client_id'] ?? $defaultClient,
        'code' => $code,
        'invoice_date' => now(),
        'issue_date' => now(),
        'payment_status' => $paymentStatus,
        'is_paid' => $isPaid,
        'total' => $grandTotal,
        'grand_total' => $grandTotal,
        'subtotal' => $subtotal,
        'due_value' => $dueValue,
        'remaining_amount' => $dueValue,
        'discount_amount' => $discountAmount,
        'discount_type' => isset($data['discount_type']) && $data['discount_type'] === 'percentage' ? 2 : 1,
        'tax_total' => $taxTotal,
        'paid_amount' => $totalPaid,
        'payment_method' => !empty($data['payments']) ? json_encode($data['payments']) : null,
        'created_by' => auth()->id(),
        'updated_by' => auth()->id(),
        'currency' => 'SAR',
        'type' => 'pos',
        'session_id' => $sessionId,
        'notes' => 'فاتورة نقطة بيع - الجلسة رقم: ' . $sessionId,
        'status' => $isPaid ? 'completed' : 'pending'
    ]);

    // إنشاء QR Code للفاتورة
    $invoice->qrcode = $this->generateQRCode($invoice);
    $invoice->save();

    return $invoice;
}

    /**
     * إضافة معاملة لتفاصيل الجلسة
     */
    private function addTransactionToSession($sessionId, $data)
    {
        PosSessionDetail::create([
            'session_id' => $sessionId,
            'transaction_type' => $data['type'],
            'reference_number' => $data['reference'] ?? null,
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'cash_amount' => $data['cash_amount'] ?? 0,
            'card_amount' => $data['card_amount'] ?? 0,
            'description' => $data['description'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'transaction_time' => now()
        ]);
    }

    /**
     * تحديث إحصائيات الجلسة
     */
    private function updateSessionStatistics(PosSession $session)
    {
        $stats = $session->details()
            ->selectRaw('
                COUNT(*) as transaction_count,
                SUM(CASE WHEN transaction_type = "sale" THEN amount ELSE 0 END) as total_sales,
                SUM(CASE WHEN transaction_type = "return" THEN amount ELSE 0 END) as total_returns,
                SUM(cash_amount) as total_cash,
                SUM(card_amount) as total_card
            ')
            ->first();

        $session->update([
            'total_transactions' => $stats->transaction_count ?? 0,
            'total_sales' => $stats->total_sales ?? 0,
            'total_returns' => $stats->total_returns ?? 0,
            'total_cash' => $stats->total_cash ?? 0,
            'total_card' => $stats->total_card ?? 0
        ]);

        return $session->fresh();
    }

    /**
     * جلب إحصائيات الجلسة الحالية
     */
    public function getSessionStats()
    {
        try {
            $activeSession = PosSession::active()->forUser(auth()->id())->first();
            
            if (!$activeSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد جلسة نشطة'
                ], 404);
            }

            // تحديث الإحصائيات
            $this->updateSessionStatistics($activeSession);
            
            // جلب إحصائيات إضافية
            $invoicesCount = Invoice::where('session_id', $activeSession->id)->count();
            $lastInvoice = Invoice::where('session_id', $activeSession->id)
                ->orderBy('created_at', 'desc')
                ->first();

            $stats = [
                'session_id' => $activeSession->id,
                'session_number' => $activeSession->session_number,
                'started_at' => $activeSession->started_at->format('Y-m-d H:i'),
                'opening_balance' => (float) $activeSession->opening_balance,
                'total_sales' => (float) $activeSession->total_sales,
                'total_cash' => (float) $activeSession->total_cash,
                'total_card' => (float) $activeSession->total_card,
                'total_transactions' => $activeSession->total_transactions,
                'invoices_count' => $invoicesCount,
                'expected_balance' => (float) ($activeSession->opening_balance + $activeSession->total_cash),
                'last_invoice_time' => $lastInvoice ? $lastInvoice->created_at->diffForHumans() : null
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في جلب إحصائيات الجلسة: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات'
            ], 500);
        }
    }

    /**
     * التحقق من حالة الجلسة
     */
    public function checkSession()
    {
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        
        return response()->json([
            'has_active_session' => (bool) $activeSession,
            'session' => $activeSession ? [
                'id' => $activeSession->id,
                'session_number' => $activeSession->session_number,
                'started_at' => $activeSession->started_at->format('Y-m-d H:i'),
                'device_name' => $activeSession->device->name ?? 'غير محدد'
            ] : null
        ]);
    }

    // باقي الدوال من الكلاس الأصلي...
    private function calculateDiscount($subtotal, $discountType, $discountValue)
    {
        if ($discountValue <= 0) {
            return 0;
        }

        if ($discountType === 'percentage') {
            if ($discountValue > 100) {
                throw new \Exception('نسبة الخصم لا يمكن أن تتجاوز 100%');
            }
            return $subtotal * ($discountValue / 100);
        }

        if ($discountValue > $subtotal) {
            throw new \Exception('قيمة الخصم لا يمكن أن تتجاوز المجموع الفرعي');
        }

        return $discountValue;
    }

    private function createInvoiceItems($invoiceId, $products)
    {
        $items = [];
        
        foreach ($products as $product) {
            $items[] = [
                'invoice_id' => $invoiceId,
                'product_id' => $product['id'],
                'item' => $product['name'],
                'description' => $product['name'],
                'unit_price' => $product['unit_price'],
                'quantity' => $product['quantity'],
                'discount' => 0,
                'tax_1' => 0,
                'tax_2' => 0,
                'total' => $product['total'],
                'type' => 'product',
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        InvoiceItem::insert($items);
    }

    private function processPayments($invoiceId, $payments)
    {
        $totalPaid = collect($payments)->sum('amount');
        
        // التحقق من صحة المدفوعات
        $paymentMethodIds = collect($payments)->pluck('method_id');
        $validMethods = PaymentMethod::whereIn('id', $paymentMethodIds)
            ->where('status', 0)
            ->count();

        if ($validMethods !== $paymentMethodIds->count()) {
            throw new \Exception('إحدى طرق الدفع غير صحيحة أو غير متاحة');
        }

        Log::info('تم معالجة المدفوعات للفاتورة: ' . $invoiceId, [
            'total_paid' => $totalPaid,
            'payments' => $payments
        ]);
    }

    private function updateProductStock($products)
    {
        // إذا كان لديك نظام إدارة مخزون
        foreach ($products as $product) {
            // Product::where('id', $product['id'])
            //     ->decrement('stock_quantity', $product['quantity']);
        }
    }

    private function generateQRCode($invoice)
    {
        $companyName = config('app.company_name', 'مؤسسة اعمال خاصة للتجارة');
        $vatNumber = config('app.vat_number', '300000000000003');
        
        $tlvContent = $this->getTlv(1, $companyName) 
            . $this->getTlv(2, $vatNumber) 
            . $this->getTlv(3, $invoice->created_at->toISOString()) 
            . $this->getTlv(4, number_format($invoice->grand_total, 2, '.', '')) 
            . $this->getTlv(5, number_format($invoice->tax_total ?? 0, 2, '.', ''));

        return base64_encode($tlvContent);
    }

    private function getTlv($tag, $value)
    {
        $value = (string) $value;
        return pack('C', $tag) . pack('C', strlen($value)) . $value;
    }

    // باقي الدوال الموجودة في الكلاس الأصلي...
    public function getInvoiceDetails($id)
    {
        try {
            $invoice = Invoice::with(['client:id,trade_name', 'items.product:id,name'])
                ->where('id', $id)
                ->where('type', 'pos')
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة غير موجودة'
                ], 404);
            }

            $invoiceData = [
                'id' => $invoice->id,
                'code' => $invoice->code,
                'client_id' => $invoice->client_id,
                'client_name' => optional($invoice->client)->trade_name,
                'invoice_date' => $invoice->invoice_date,
                'grand_total' => (float) $invoice->grand_total,
                'subtotal' => (float) $invoice->subtotal,
                'tax_total' => (float) $invoice->tax_total,
                'discount_amount' => (float) $invoice->discount_amount,
                'payment_status' => $invoice->payment_status,
                'notes' => $invoice->notes,
                'session_id' => $invoice->session_id,
                'items' => $invoice->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => optional($item->product)->name ?: $item->item,
                        'item' => $item->item,
                        'quantity' => (float) $item->quantity,
                        'unit_price' => (float) $item->unit_price,
                        'total' => (float) $item->total,
                        'discount' => (float) $item->discount,
                    ];
                })
            ];

            return response()->json([
                'success' => true,
                'invoice' => $invoiceData
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في جلب تفاصيل الفاتورة: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الفاتورة.'
            ], 500);
        }
    }

    public function getProductsByCategory(Request $request)
    {
        // التحقق من الجلسة النشطة
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة نشطة',
                'redirect' => route('pos.sessions.create')
            ], 403);
        }

        try {
            $categoryId = $request->input('category_id');
            $search = $request->input('search');
            $limit = min((int) $request->input('limit', 20), 50);

            $query = Product::with(['category:id,name']);

            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            $products = $query->orderBy('name')
                ->limit($limit)
                ->get(['id', 'name', 'sale_price', 'images', 'category_id'])
                ->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'sale_price' => (float) $product->sale_price,
                        'category_id' => $product->category_id,
                        'images' => $product->images ? asset($product->images) : asset('assets/images/default.png'),
                    ];
                });

            return response()->json([
                'success' => true,
                'products' => $products
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في جلب منتجات التصنيف: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب المنتجات.'
            ], 500);
        }
    }

  

    public function getHeldInvoices(Request $request)
    {
        // التحقق من الجلسة النشطة
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة نشطة',
                'redirect' => route('pos.sessions.create')
            ], 403);
        }

        try {
            $heldInvoices = Invoice::where('type', 'pos')
                ->where('status', 'held')
                ->where('session_id', $activeSession->id) // فقط فواتير الجلسة الحالية
                ->where('created_by', auth()->id())
                ->with(['items', 'client'])
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'invoices' => $heldInvoices
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في جلب الفواتير المعلقة: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الفواتير المعلقة'
            ], 500);
        }
    }

    public function resumeHeldInvoice(Request $request)
    {
        // التحقق من الجلسة النشطة
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة نشطة',
                'redirect' => route('pos.sessions.create')
            ], 403);
        }

        try {
            $invoiceId = $request->input('invoice_id');
            
            $invoice = Invoice::with(['items', 'client'])
                ->where('id', $invoiceId)
                ->where('status', 'held')
                ->where('session_id', $activeSession->id) // فقط من الجلسة الحالية
                ->where('created_by', auth()->id())
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة غير موجودة أو لا يمكن الوصول إليها'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'invoice' => $invoice
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في استكمال الفاتورة المعلقة: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استكمال الفاتورة'
            ], 500);
        }
    }

    public function deleteHeldInvoice($id)
    {
        // التحقق من الجلسة النشطة
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة نشطة',
                'redirect' => route('pos.sessions.create')
            ], 403);
        }

        try {
            $invoice = Invoice::where('id', $id)
                ->where('status', 'held')
                ->where('session_id', $activeSession->id) // فقط من الجلسة الحالية
                ->where('created_by', auth()->id())
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة غير موجودة أو لا يمكن حذفها'
                ], 404);
            }

            DB::beginTransaction();

            InvoiceItem::where('invoice_id', $id)->delete();
            $invoice->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الفاتورة المعلقة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في حذف الفاتورة المعلقة: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الفاتورة'
            ], 500);
        }
    }

    public function getDailyStats()
    {
        // التحقق من الجلسة النشطة
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة نشطة',
                'redirect' => route('pos.sessions.create')
            ], 403);
        }

        try {
            $today = Carbon::today();
            
            $stats = [
                // إحصائيات اليوم عامة
                'daily_total_sales' => Invoice::where('type', 'pos')
                    ->whereDate('created_at', $today)
                    ->sum('grand_total'),
                
                'daily_total_invoices' => Invoice::where('type', 'pos')
                    ->whereDate('created_at', $today)
                    ->count(),
                
                // إحصائيات الجلسة الحالية
                'session_total_sales' => $activeSession->total_sales,
                'session_total_transactions' => $activeSession->total_transactions,
                'session_cash_amount' => $activeSession->total_cash,
                'session_card_amount' => $activeSession->total_card,
                'session_expected_balance' => $activeSession->opening_balance + $activeSession->total_cash,
                
                'held_invoices' => Invoice::where('type', 'pos')
                    ->where('status', 'held')
                    ->where('session_id', $activeSession->id)
                    ->count(),
                
                'top_products' => InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->where('invoices.type', 'pos')
                    ->where('invoices.session_id', $activeSession->id)
                    ->select('invoice_items.item', DB::raw('SUM(invoice_items.quantity) as total_quantity'))
                    ->groupBy('invoice_items.item')
                    ->orderBy('total_quantity', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'session_info' => [
                    'session_number' => $activeSession->session_number,
                    'started_at' => $activeSession->started_at->format('Y-m-d H:i'),
                    'duration' => $activeSession->started_at->diffForHumans()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في جلب إحصائيات اليوم: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات'
            ], 500);
        }
    }
    /**
 * جلب الفواتير المتاحة للاسترداد من الجلسة الحالية
 */
public function getAvailableInvoicesForReturn()
{
    try {
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            return response()->json([
                'success' => false,
                'message' => 'لا توجد جلسة نشطة'
            ], 403);
        }

        $invoices = Invoice::with(['client:id,trade_name'])
            ->where('type', 'pos')
            ->where('session_id', $activeSession->id)
            ->where('payment_status', 1) // فقط الفواتير المدفوعة
            ->orderBy('created_at', 'desc')
            ->get(['id', 'code', 'client_id', 'invoice_date', 'grand_total']);

        return response()->json([
            'success' => true,
            'invoices' => $invoices->map(function($invoice) {
                return [
                    'id' => $invoice->id,
                    'code' => $invoice->code,
                    'client_name' => optional($invoice->client)->trade_name,
                    'invoice_date' => $invoice->invoice_date,
                    'grand_total' => $invoice->grand_total
                ];
            })
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في جلب الفواتير للاسترداد: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب الفواتير'
        ], 500);
    }
}

/**
 * جلب تفاصيل الفاتورة للاسترداد
 */
public function getInvoiceDetailsForReturn($id)
{
    try {
        $invoice = Invoice::with(['items', 'client'])
            ->where('id', $id)
            ->where('type', 'pos')
            ->first();

        if (!$invoice) {
            return response()->json([
                'success' => false,
                'message' => 'الفاتورة غير موجودة'
            ], 404);
        }

        // حساب الكميات المرتجعة سابقاً
        $returnedQuantities = InvoiceItem::whereHas('invoice', function($query) use ($id) {
                $query->where('reference_number', $id)
                      ->where('type', 'returned');
            })
            ->selectRaw('product_id, SUM(quantity) as returned_quantity')
            ->groupBy('product_id')
            ->pluck('returned_quantity', 'product_id');

        $items = $invoice->items->map(function($item) use ($returnedQuantities) {
            return [
                'id' => $item->id,
                'invoice_id' => $item->invoice_id,
                'product_id' => $item->product_id,
                'item' => $item->item,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total' => $item->total,
                'returned_quantity' => $returnedQuantities[$item->product_id] ?? 0
            ];
        });

        return response()->json([
            'success' => true,
            'invoice' => [
                'id' => $invoice->id,
                'code' => $invoice->code,
                'items' => $items
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في جلب تفاصيل الفاتورة للاسترداد: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء جلب تفاصيل الفاتورة'
        ], 500);
    }
}

/**
 * معالجة الاسترداد
 */
public function processReturn(Request $request)
{
    $validated = $request->validate([
        'invoice_id' => 'required|integer|exists:invoices,id',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|integer|exists:products,id',
        'items.*.quantity' => 'required|numeric|min:0.01',
        'items.*.unit_price' => 'required|numeric|min:0',
        'items.*.total' => 'required|numeric|min:0'
    ]);

    DB::beginTransaction();
    
    try {
        $originalInvoice = Invoice::find($validated['invoice_id']);
        if (!$originalInvoice) {
            throw new \Exception('الفاتورة الأصلية غير موجودة');
        }

        // التحقق من وجود جلسة نشطة
        $activeSession = PosSession::active()->forUser(auth()->id())->first();
        if (!$activeSession) {
            throw new \Exception('لا توجد جلسة نشطة');
        }

        // إنشاء كود للفاتورة المرتجعة
        $lastOrder = Invoice::where('type', 'returned')->orderBy('id', 'desc')->first();
        $nextNumber = $lastOrder ? intval(substr($lastOrder->code, -5)) + 1 : 1;
        while (Invoice::where('code', 'RET' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT))->exists()) {
            $nextNumber++;
        }
        $code = 'RET' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

        // تجهيز المتغيرات الرئيسية لحساب الفاتورة
        $total_amount = 0;
        $total_discount = 0;
        $items_data = [];

        // الحصول على المستودع
        $user = Auth::user();
        if ($user && $user->employee_id) {
            $defaultWarehouse = DefaultWarehouses::where('employee_id', $user->employee_id)->first();
            if ($defaultWarehouse && $defaultWarehouse->storehouse_id) {
                $storeHouse = StoreHouse::find($defaultWarehouse->storehouse_id);
            } else {
                $storeHouse = StoreHouse::where('major', 1)->first();
            }
        } else {
            $storeHouse = StoreHouse::where('major', 1)->first();
        }

        if (!$storeHouse) {
            $storeHouse = StoreHouse::first();
            if (!$storeHouse) {
                throw new \Exception('لا يوجد أي مستودع في النظام');
            }
        }

        // الحصول على الخزينة
        $TreasuryEmployee = TreasuryEmployee::where('employee_id', $user->employee_id)->first();
        if ($user && $user->employee_id) {
            if ($TreasuryEmployee && $TreasuryEmployee->treasury_id) {
                $MainTreasury = Account::where('id', $TreasuryEmployee->treasury_id)->first();
            } else {
                $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
            }
        } else {
            $MainTreasury = Account::where('name', 'الخزينة الرئيسية')->first();
        }

        // معالجة البنود
        foreach ($validated['items'] as $item) {
            // جلب المنتج
            $product = Product::find($item['product_id']);
            if (!$product) {
                throw new \Exception('المنتج غير موجود: ' . $item['product_id']);
            }

            // التحقق من البند في الفاتورة الأصلية
            $original_item = InvoiceItem::where('invoice_id', $originalInvoice->id)
                ->where('product_id', $item['product_id'])->first();

            if (!$original_item) {
                throw new \Exception('المنتج غير موجود في الفاتورة الأصلية: ' . $product->name);
            }

            // حساب الكمية المرتجعة سابقاً
            $previous_return_qty = InvoiceItem::whereHas('invoice', function ($query) use ($originalInvoice) {
                $query->where('reference_number', $originalInvoice->id)
                      ->where('type', 'returned');
            })->where('product_id', $item['product_id'])->sum('quantity');

            // التحقق من عدم تجاوز الكمية الأصلية
            $total_return_qty = floatval($previous_return_qty) + floatval($item['quantity']);
            if ($total_return_qty > $original_item->quantity) {
                throw new \Exception('لا يمكن إرجاع كمية أكبر من الأصلية للمنتج: ' . $product->name);
            }

            // حساب تفاصيل الكمية والأسعار مع الضريبة
            $quantity = floatval($item['quantity']);
            $unit_price_including_tax = floatval($item['unit_price']);
            $unit_price_excluding_tax = $unit_price_including_tax / 1.15; // السعر بدون ضريبة
            $tax_per_unit = $unit_price_including_tax - $unit_price_excluding_tax; // مقدار الضريبة للوحدة
            $item_total = $quantity * $unit_price_including_tax;
            $item_tax_total = $quantity * $tax_per_unit;

            // تحديث الإجماليات
            $total_amount += $item_total;

            // تجهيز بيانات البند
            $items_data[] = [
                'invoice_id' => null, // سيتم تعيينه لاحقاً
                'product_id' => $item['product_id'],
                'store_house_id' => $storeHouse->id,
                'item' => $product->name,
                'description' => $product->name,
                'quantity' => $quantity,
                'unit_price' => $unit_price_excluding_tax, // السعر بدون ضريبة
                'discount' => 0,
                'discount_type' => 1,
                'tax_1' => $item_tax_total, // إجمالي الضريبة للعنصر
                'tax_2' => 0,
                'total' => $item_total, // الإجمالي شامل الضريبة
            ];
        }

        // حساب إجمالي الضريبة
        $tax_total = 0;
        foreach ($items_data as $item_data) {
            $tax_total += $item_data['tax_1'];
        }

        // حساب المبلغ بدون ضريبة
        $amount_excluding_tax = $total_amount - $tax_total;

        // إنشاء فاتورة الإرجاع
        $returnInvoice = Invoice::create([
            'client_id' => $originalInvoice->client_id,
            'employee_id' => $user->employee_id,
            'due_value' => $total_amount,
            'reference_number' => $originalInvoice->id,
            'code' => $code,
            'type' => 'returned',
            'invoice_date' => now(),
            'issue_date' => now(),
            'terms' => 0,
            'notes' => 'فاتورة إرجاع من نقطة البيع - الفاتورة الأصلية: ' . $originalInvoice->code,
            'payment_status' => 4,
            'is_paid' => false,
            'created_by' => Auth::id(),
            'account_id' => null,
            'discount_amount' => 0,
            'discount_type' => 1,
            'advance_payment' => 0,
            'payment_type' => 1,
            'shipping_cost' => 0,
            'shipping_tax' => 0,
            'tax_type' => 1,
            'payment_method' => null,
            'received_date' => now(),
            'subtotal' => $amount_excluding_tax,
            'total_discount' => 0,
            'tax_total' => $tax_total,
            'grand_total' => $total_amount,
            'paid_amount' => 0,
            'session_id' => $activeSession->id, // ربط بالجلسة
        ]);

        // تحديث قيمة المرتجع في الفاتورة الأصلية
        $originalInvoice->returned_payment += $returnInvoice->grand_total;
        $originalInvoice->save();

        // إنشاء عناصر الفاتورة وتحديث المخزون
        foreach ($items_data as $item) {
            $item['invoice_id'] = $returnInvoice->id;
            $item_invoice = InvoiceItem::create($item);

            // تحديث المخزون
            $productDetails = ProductDetails::where('store_house_id', $item['store_house_id'])
                ->where('product_id', $item['product_id'])->first();

            if (!$productDetails) {
                $productDetails = ProductDetails::create([
                    'store_house_id' => $item['store_house_id'],
                    'product_id' => $item['product_id'],
                    'quantity' => 0,
                ]);
            }

            $product = Product::find($item['product_id']);

            if ($product->type == 'products') {
                // حساب المخزون قبل وبعد التعديل (زيادة بسبب المرتجع)
                $total_quantity = DB::table('product_details')->where('product_id', $item['product_id'])->sum('quantity');
                $stock_before = $total_quantity;
                $stock_after = $stock_before + $item['quantity'];

                // تحديث المخزون بزيادة الكمية
                $productDetails->increment('quantity', $item['quantity']);

                // جلب مصدر إذن المخزون للإرجاع
                $permissionSource = PermissionSource::where('name', 'مرتجع مبيعات')->first();
                if (!$permissionSource) {
                    $permissionSource = PermissionSource::create(['name' => 'مرتجع مبيعات']);
                }

                // تسجيل حركة المخزون للإرجاع
                $wareHousePermits = WarehousePermits::create([
                    'permission_type' => $permissionSource->id,
                    'permission_date' => now(),
                    'number' => $returnInvoice->id,
                    'grand_total' => $returnInvoice->grand_total,
                    'store_houses_id' => $storeHouse->id,
                    'created_by' => auth()->user()->id,
                ]);

                // تسجيل تفاصيل حركة المخزون
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

        // القيود المحاسبية
        $this->createReturnAccountingEntries($returnInvoice, $originalInvoice, $MainTreasury);

        // إضافة معاملة لتفاصيل الجلسة
        $this->addReturnTransactionToSession($activeSession->id, $returnInvoice);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'تم إنشاء فاتورة الاسترداد بنجاح',
            'return_invoice_id' => $returnInvoice->id
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('خطأ في معالجة الاسترداد: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 422);
    }
}

/**
 * إنشاء القيود المحاسبية للمرتجع
 */
private function createReturnAccountingEntries($returnInvoice, $originalInvoice, $MainTreasury)
{
    $vatAccount = Account::where('name', 'القيمة المضافة المحصلة')->first();
    $storeAccount = Account::where('name', 'المخزون')->first();
    $costAccount = Account::where('id', 50)->first();
    $retursalesnAccount = Account::where('id', 45)->first();
    $clientaccounts = Account::where('client_id', $returnInvoice->client_id)->first();

    if ($originalInvoice->payment_status == 1) {
        // مرتجع مبيعات لفاتورة مدفوعة
        $journalEntry = JournalEntry::create([
            'reference_number' => $returnInvoice->code,
            'date' => now(),
            'description' => 'قيد محاسبي لمرتجع مبيعات POS مدفوعة للفاتورة رقم ' . $returnInvoice->code,
            'status' => 1,
            'currency' => 'SAR',
            'client_id' => $returnInvoice->client_id,
            'invoice_id' => $returnInvoice->id,
            'created_by_employee' => Auth::id(),
        ]);

        // مردود المبيعات (مدين)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $retursalesnAccount->id,
            'description' => 'قيد مردود المبيعات POS',
            'debit' => $returnInvoice->grand_total,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // العميل (دائن)
        if ($clientaccounts) {
            JournalEntryDetail::create([
                'journal_entry_id' => $journalEntry->id,
                'account_id' => $clientaccounts->id,
                'description' => 'فاتورة مرتجعه POS لفاتورة رقم ' . $returnInvoice->code,
                'debit' => 0,
                'credit' => $returnInvoice->grand_total,
                'is_debit' => false,
            ]);
        }

        // الخزينة (دائن)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $MainTreasury->id,
            'description' => 'صرف قيمة المرتجع POS من الخزينة للفاتورة رقم ' . $returnInvoice->code,
            'debit' => 0,
            'credit' => $returnInvoice->grand_total,
            'is_debit' => false,
        ]);

        // المخزون (مدين)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $storeAccount->id,
            'description' => 'إرجاع البضاعة إلى المخزون POS',
            'debit' => $returnInvoice->grand_total,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // تكلفة المبيعات (دائن)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $costAccount->id,
            'description' => 'إلغاء تكلفة المبيعات POS',
            'debit' => 0,
            'credit' => $returnInvoice->grand_total,
            'is_debit' => false,
        ]);

        // تحديث الأرصدة
        $retursalesnAccount->balance += $returnInvoice->grand_total;
        $retursalesnAccount->save();

        $MainTreasury->balance -= $returnInvoice->grand_total;
        $MainTreasury->save();

        $storeAccount->balance += $returnInvoice->grand_total;
        $storeAccount->save();

        $costAccount->balance -= $returnInvoice->grand_total;
        $costAccount->save();
    }
}

/**
 * إضافة معاملة الإرجاع لتفاصيل الجلسة
 */
private function addReturnTransactionToSession($sessionId, $returnInvoice)
{
    PosSessionDetail::create([
        'session_id' => $sessionId,
        'transaction_type' => 'return',
        'reference_number' => $returnInvoice->code,
        'amount' => $returnInvoice->grand_total,
        'payment_method' => 'cash', // افتراضي للمرتجعات
        'cash_amount' => $returnInvoice->grand_total,
        'card_amount' => 0,
        'description' => "استرداد - فاتورة رقم {$returnInvoice->code}",
        'metadata' => json_encode([
            'return_invoice_id' => $returnInvoice->id,
            'original_invoice_id' => $returnInvoice->reference_number,
            'items_count' => $returnInvoice->items->count()
        ]),
        'transaction_time' => now()
    ]);
}
public function printReturnInvoice($id)
{
    try {
        $invoice = Invoice::with(['client', 'items.product'])
            ->where('id', $id)
            ->where('type', 'returned')
            ->first();

        if (!$invoice) {
            return redirect()->back()->with('error', 'فاتورة الإرجاع غير موجودة');
        }

        // إنشاء QR Code للفاتورة
        $renderer = new ImageRenderer(
            new RendererStyle(150),
            new SvgImageBackEnd(),
        );

        $writer = new Writer($renderer);
        $qrCodeSvg = $writer->writeString($this->generateReturnQRCode($invoice));

        // جلب الفاتورة الأصلية
        $originalInvoice = Invoice::find($invoice->reference_number);

        return view('pos.sales_start.return_print', compact('invoice', 'qrCodeSvg', 'originalInvoice'));
        
    } catch (\Exception $e) {
        Log::error('خطأ في طباعة فاتورة الإرجاع: ' . $e->getMessage());
        return redirect()->back()->with('error', 'حدث خطأ أثناء طباعة فاتورة الإرجاع');
    }
}

/**
 * إنشاء QR Code لفاتورة الإرجاع
 */
private function generateReturnQRCode($invoice)
{
    $companyName = 'مؤسسة الطيب الافضل للتجارة';
    $vatNumber = '310213567700003';
    
    $tlvContent = $this->getTlv(1, $companyName) 
        . $this->getTlv(2, $vatNumber) 
        . $this->getTlv(3, $invoice->created_at->toISOString()) 
        . $this->getTlv(4, '-' . number_format($invoice->grand_total, 2, '.', '')) 
        . $this->getTlv(5, '-' . number_format($invoice->tax_total ?? 0, 2, '.', ''));

    return base64_encode($tlvContent);
}
}