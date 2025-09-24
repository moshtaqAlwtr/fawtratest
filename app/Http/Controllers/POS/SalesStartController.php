<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Client;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
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
        try {
            // تحسين الاستعلامات باستخدام Eager Loading
            $categories = Category::
                orderBy('name')
                ->get(['id', 'name', 'attachments']);
            
            $products = Product::with(['category:id,name'])
                
                ->orderBy('name')
                ->get(['id', 'name', 'sale_price', 'images', 'category_id']);
            
            // معالجة مسارات الصور للمنتجات
            $products = $products->map(function ($product) {
                if ($product->images) {
                    // التأكد من أن المسار صحيح
                    if (!str_starts_with($product->images, 'http') && !str_starts_with($product->images, '/')) {
                        $product->images = '/assets/uploads/product/' . $product->images;
                    }
                } else {
                    $product->images = '/assets/uploads/no_image.jpg';
                }
                return $product;
            });
            
            $clients = Client::
                orderBy('trade_name')
                ->get(['id', 'trade_name', 'phone']);
            
            $paymentMethods = PaymentMethod::
                orderBy('name')
                ->get(['id', 'name']);

            return view('pos.sales_start.index', compact(
                'products', 
                'clients', 
                'categories', 
                'paymentMethods'
            ));

        } catch (\Exception $e) {
            Log::error('خطأ في تحميل صفحة نقطة البيع: ' . $e->getMessage());
            
            // في حالة فشل تحميل البيانات، إرسال بيانات فارغة لتجنب الأخطاء
            return view('pos.sales_start.index', [
                'products' => collect([]),
                'clients' => collect([]),
                'categories' => collect([]),
                'paymentMethods' => collect([])
            ])->with('error', 'حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * بحث متقدم للمنتجات والعملاء
     */
    public function search(Request $request)
    {
        try {
            $query = trim($request->input('query', ''));
            $type = $request->input('type', 'all'); // all, products, clients
            $category = $request->input('category');
            $limit = min((int) $request->input('limit', 20), 50); // حد أقصى 50 نتيجة

            $results = [];

            if (empty($query)) {
                return response()->json([
                    'success' => true,
                    'products' => [],
                    'clients' => [],
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
                    ->get(['id', 'name', 'sale_price', 'images', 'category_id'])
                    ->map(function ($product) {
                        return [
                            'id' => $product->id,
                            'name' => $product->name,
                            'sale_price' => (float) $product->sale_price,
                            'code' => $product->code,
                            'category_id' => $product->category_id,
                            'category_name' => optional($product->category)->name,
                            'images' => $product->images ? asset($product->images) : asset('assets/images/default.png'),
                            'type' => 'product'
                        ];
                    });

                $results['products'] = $products;
            }

            // البحث في العملاء
            if ($type === 'all' || $type === 'clients') {
                $clients = Client::
                    where(function ($q) use ($query) {
                        $q->where('trade_name', 'LIKE', "%{$query}%")
                          ->orWhere('phone', 'LIKE', "%{$query}%")
                          ->orWhere('email', 'LIKE', "%{$query}%")
                          ->orWhere('address', 'LIKE', "%{$query}%");
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

            return response()->json([
                'success' => true,
                'query' => $query,
                'results_count' => [
                    'products' => isset($results['products']) ? $results['products']->count() : 0,
                    'clients' => isset($results['clients']) ? $results['clients']->count() : 0,
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
     * جلب المنتجات بناءً على التصنيف
     */
    public function getProductsByCategory(Request $request)
    {
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
                    $q->where('name', 'LIKE', "%{$search}%");
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
                        'code' => $product->code ?? 0,
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

    /**
     * تخزين الفاتورة مع تحسينات الأمان والتحقق
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المرسلة
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
            // التحقق من توفر المنتجات
            $productIds = collect($validated['products'])->pluck('id');
            $availableProducts = Product::whereIn('id', $productIds)
                ->where('status', 0)
                ->pluck('id');

            if ($productIds->count() !== $availableProducts->count()) {
                throw new \Exception('بعض المنتجات غير متوفرة أو تم إلغاؤها.');
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

            // إنشاء الفاتورة
            $invoice = $this->createInvoice($validated, $subtotal, $discountAmount, $grandTotal);

            // إضافة عناصر الفاتورة
            $this->createInvoiceItems($invoice->id, $validated['products']);

            // معالجة المدفوعات
            if (!empty($validated['payments'])) {
                $this->processPayments($invoice->id, $validated['payments']);
            }

            // تحديث مخزون المنتجات (إذا كان مطلوباً)
            $this->updateProductStock($validated['products']);

            DB::commit();

            return response()->json([
                'success' => true,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->id,
                'message' => 'تم إنشاء الفاتورة بنجاح'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في حفظ الفاتورة: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * حساب قيمة الخصم
     */
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

    /**
     * إنشاء الفاتورة
     */
    private function createInvoice($data, $subtotal, $discountAmount, $grandTotal)
    {
        $invoice = Invoice::create([
            'client_id' => $data['client_id'] ?? 7129,
            'invoice_date' => now(),
            'issue_date' => now(),
            'payment_status' => 1, // مدفوع بالكامل
            'is_paid' => true,
            'total' => $grandTotal,
            'grand_total' => $grandTotal,
            'subtotal' => $subtotal,
            'due_value' => 0,
            'remaining_amount' => 0,
            'discount_amount' => $discountAmount,
            'discount_type' => $data['discount_type'] ?? null,
            'tax_total' => 0, // يمكن حسابها لاحقاً
            'payment_method' => !empty($data['payments']) ? json_encode($data['payments']) : null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
            'currency' => 'SAR',
            'type' => 'pos',
            'notes' => 'فاتورة نقطة بيع',
           
        ]);

        // إنشاء QR Code للفاتورة
        $invoice->qrcode = $this->generateQRCode($invoice);
        $invoice->save();

        return $invoice;
    }

    /**
     * إنشاء عناصر الفاتورة
     */
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

    /**
     * معالجة المدفوعات
     */
    private function processPayments($invoiceId, $payments)
    {
        // يمكن إضافة جدول للمدفوعات إذا لم يكن موجوداً
        // أو حفظها في حقل JSON في جدول الفواتير
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

    /**
     * تحديث مخزون المنتجات
     */
    private function updateProductStock($products)
    {
        // إذا كان لديك نظام إدارة مخزون
        foreach ($products as $product) {
            // Product::where('id', $product['id'])
            //     ->decrement('stock_quantity', $product['quantity']);
        }
    }

    /**
     * إنشاء QR Code للفاتورة
     */
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

    /**
     * إنشاء TLV للـ QR Code
     */
    private function getTlv($tag, $value)
    {
        $value = (string) $value;
        return pack('C', $tag) . pack('C', strlen($value)) . $value;
    }

    /**
     * جلب الفواتير المعلقة
     */
    public function getHeldInvoices(Request $request)
    {
        try {
            $heldInvoices = Invoice::where('type', 'pos')
                ->where('status', 'held')
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

    /**
     * استكمال فاتورة معلقة
     */
    public function resumeHeldInvoice(Request $request)
    {
        try {
            $invoiceId = $request->input('invoice_id');
            
            $invoice = Invoice::with(['items', 'client'])
                ->where('id', $invoiceId)
                ->where('status', 'held')
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

    /**
     * طباعة الفاتورة
     */
    public function printInvoice($id)
    {
        try {
            $invoice = Invoice::with(['items', 'client'])
                ->where('id', $id)
                ->where('created_by', auth()->id())
                ->first();

            if (!$invoice) {
                abort(404, 'الفاتورة غير موجودة');
            }

            return view('pos.invoices.print', compact('invoice'));

        } catch (\Exception $e) {
            Log::error('خطأ في طباعة الفاتورة: ' . $e->getMessage());
            
            return back()->with('error', 'حدث خطأ أثناء طباعة الفاتورة');
        }
    }

    /**
     * حذف فاتورة معلقة
     */
    public function deleteHeldInvoice($id)
    {
        try {
            $invoice = Invoice::where('id', $id)
                ->where('status', 'held')
                ->where('created_by', auth()->id())
                ->first();

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'الفاتورة غير موجودة أو لا يمكن حذفها'
                ], 404);
            }

            DB::beginTransaction();

            // حذف عناصر الفاتورة
            InvoiceItem::where('invoice_id', $id)->delete();
            
            // حذف الفاتورة
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

    /**
     * إحصائيات اليوم
     */
    public function getDailyStats()
    {
        try {
            $today = Carbon::today();
            
            $stats = [
                'total_sales' => Invoice::where('type', 'pos')
                    
                    ->whereDate('created_at', $today)
                    ->sum('grand_total'),
                
                'total_invoices' => Invoice::where('type', 'pos')
                    
                    ->whereDate('created_at', $today)
                    ->count(),
                
                'held_invoices' => Invoice::where('type', 'pos')
                    ->where('status', 'held')
                    ->whereDate('created_at', $today)
                    ->count(),
                
                'top_products' => InvoiceItem::join('invoices', 'invoice_items.invoice_id', '=', 'invoices.id')
                    ->where('invoices.type', 'pos')
                    ->where('invoices.status', 'completed')
                    ->whereDate('invoices.created_at', $today)
                    ->select('invoice_items.item', DB::raw('SUM(invoice_items.quantity) as total_quantity'))
                    ->groupBy('invoice_items.item')
                    ->orderBy('total_quantity', 'desc')
                    ->limit(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في جلب إحصائيات اليوم: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات'
            ], 500);
        }
    }
}