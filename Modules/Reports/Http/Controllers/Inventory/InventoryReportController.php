<?php

namespace Modules\Reports\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\InventoryAdjustment;
use App\Models\InvoiceItem;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\Supplier;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InventoryReportController extends Controller
{
    // عرض الصفحة الرئيسية لتقارير المخزون
    public function index()
    {
        return view('reports::inventory.index');
    }

    // تقرير المخزون بالمخازن

public function inventorySheet()
{
    // جلب البيانات الأساسية للفلاتر
    $categories = Category::all();
    $brands = Product::distinct('brand')->whereNotNull('brand')->pluck('brand');
    $warehouses = StoreHouse::all();

    return view('reports::inventory.stock_report.inventory_sheet', compact('categories', 'brands', 'warehouses'));
}

/**
 * جلب بيانات تقرير ورقة الجرد عبر AJAX
 */
public function inventorySheetAjax(Request $request)
{
    try {
        // بناء الاستعلام الأساسي
        $query = Product::query()
            ->with(['category', 'product_details.storeHouse'])
            ->select('products.*');

        // تطبيق الفلاتر
        $this->applyInventoryFilters($query, $request);

        // تطبيق الترتيب
        $this->applySorting($query, $request);

        // جلب البيانات
        $products = $query->get();

        // معالجة البيانات للعرض
        $processedProducts = $this->processInventoryData($products, $request);

        // حساب الإجماليات
        $totals = $this->calculateInventoryTotals($processedProducts);

        // إعداد بيانات الرسم البياني
        $chartData = $this->prepareChartData($processedProducts, $request);

        return response()->json([
            'success' => true,
            'products' => $processedProducts,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $this->getGroupByLabel($request->group_by),
            'message' => 'تم تحميل البيانات بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تطبيق فلاتر الجرد
 */
private function applyInventoryFilters($query, Request $request)
{
    // فلتر التصنيف
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // فلتر العلامة التجارية
    if ($request->filled('brand')) {
        $query->where('brand', $request->brand);
    }

    // فلتر المستودع
    if ($request->filled('warehouse')) {
        $query->whereHas('product_details', function ($q) use ($request) {
            $q->where('store_house_id', $request->warehouse);
        });
    }

    // البحث النصي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    // إخفاء الرصيد الصفري
    if ($request->boolean('hide_zero')) {
        $query->whereHas('product_details', function ($q) {
            $q->where('quantity', '>', 0);
        });
    }
}

/**
 * تطبيق الترتيب
 */
private function applySorting($query, Request $request)
{
    switch ($request->sort_by) {
        case 'quantity_asc':
            $query->join('product_details', 'products.id', '=', 'product_details.product_id')
                  ->orderBy('product_details.quantity', 'asc');
            break;
        case 'quantity_desc':
            $query->join('product_details', 'products.id', '=', 'product_details.product_id')
                  ->orderBy('product_details.quantity', 'desc');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'code_asc':
            $query->orderBy('id', 'asc');
            break;
        case 'code_desc':
            $query->orderBy('id', 'desc');
            break;
        default:
            $query->orderBy('name', 'asc');
    }
}

/**
 * معالجة بيانات الجرد للعرض
 */
private function processInventoryData($products, Request $request)
{
    $processedData = [];

    foreach ($products as $product) {
        // حساب الكمية الإجمالية
        $totalQuantity = $product->product_details ? $product->product_details->quantity : 0;

        // إعداد بيانات المنتج
        $productData = [
            'id' => $product->id,
            'code' => $product->code ?? $product->id,
            'name' => $product->name,
            'category' => $product->category ? $product->category->name : null,
            'brand' => $product->brand,
            'warehouse' => $this->getWarehouseNames($product),
            'quantity_in_system' => $totalQuantity,
            'quantity_actual' => $totalQuantity, // يمكن تحديثها من قاعدة البيانات إذا كانت محفوظة
            'notes' => $product->Internal_notes ?? '',
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at
        ];

        // تطبيق التجميع إذا كان مطلوباً
        if ($request->filled('group_by')) {
            $this->groupProductData($processedData, $productData, $request->group_by);
        } else {
            $processedData[] = $productData;
        }
    }

    return $processedData;
}

/**
 * تجميع بيانات المنتجات
 */
private function groupProductData(&$processedData, $productData, $groupBy)
{
    $groupKey = '';

    switch ($groupBy) {
        case 'category':
            $groupKey = $productData['category'] ?? 'غير محدد';
            break;
        case 'brand':
            $groupKey = $productData['brand'] ?? 'غير محدد';
            break;
        case 'warehouse':
            $groupKey = $productData['warehouse'] ?? 'غير محدد';
            break;
        default:
            $groupKey = 'عام';
    }

    if (!isset($processedData[$groupKey])) {
        $processedData[$groupKey] = [
            'group_name' => $groupKey,
            'items' => [],
            'total_quantity' => 0,
            'total_products' => 0
        ];
    }

    $processedData[$groupKey]['items'][] = $productData;
    $processedData[$groupKey]['total_quantity'] += $productData['quantity_in_system'];
    $processedData[$groupKey]['total_products']++;
}

/**
 * حساب الإجماليات
 */
private function calculateInventoryTotals($products)
{
    $totals = [
        'total_products' => 0,
        'total_quantity' => 0,
        'total_categories' => 0,
        'total_warehouses' => 0
    ];

    $categories = [];
    $warehouses = [];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $totals['total_products'] += $product['total_products'];
            $totals['total_quantity'] += $product['total_quantity'];

            foreach ($product['items'] as $item) {
                if ($item['category']) {
                    $categories[$item['category']] = true;
                }
                if ($item['warehouse']) {
                    $warehouses[$item['warehouse']] = true;
                }
            }
        } else {
            // للبيانات غير المجمعة
            $totals['total_products']++;
            $totals['total_quantity'] += $product['quantity_in_system'];

            if ($product['category']) {
                $categories[$product['category']] = true;
            }
            if ($product['warehouse']) {
                $warehouses[$product['warehouse']] = true;
            }
        }
    }

    $totals['total_categories'] = count($categories);
    $totals['total_warehouses'] = count($warehouses);

    return $totals;
}

/**
 * إعداد بيانات الرسم البياني
 */
private function prepareChartData($products, Request $request)
{
    $chartData = [
        'labels' => [],
        'quantities' => []
    ];

    // تحديد عدد العناصر المراد عرضها في الرسم البياني (أعلى 10)
    $limit = 10;
    $chartProducts = [];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $chartProducts[] = [
                'name' => $product['group_name'],
                'quantity' => $product['total_quantity']
            ];
        } else {
            // للبيانات غير المجمعة
            $chartProducts[] = [
                'name' => $product['name'],
                'quantity' => $product['quantity_in_system']
            ];
        }
    }

    // ترتيب حسب الكمية (تنازلي) وأخذ أعلى 10
    usort($chartProducts, function ($a, $b) {
        return $b['quantity'] <=> $a['quantity'];
    });

    $chartProducts = array_slice($chartProducts, 0, $limit);

    foreach ($chartProducts as $item) {
        $chartData['labels'][] = $item['name'];
        $chartData['quantities'][] = $item['quantity'];
    }

    return $chartData;
}

/**
 * الحصول على أسماء المستودعات
 */


/**
 * الحصول على تسمية التجميع
 */


/**
 * تحديث الكمية الفعلية للمنتج (اختياري)
 */
public function updateActualQuantity(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'actual_quantity' => 'required|numeric|min:0'
    ]);

    try {
        // يمكن حفظ الكمية الفعلية في جدول منفصل أو في جدول التعديلات
        InventoryAdjustment::updateOrCreate(
            [
                'product_id' => $request->product_id,
                'status' => 'pending'
            ],
            [
                'quantity_in_stock' => $request->actual_quantity,
                'inventory_time' => now(),
                'updated_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الكمية الفعلية بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحديث الكمية: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تحديث ملاحظات المنتج (اختياري)
 */
public function updateNotes(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
        'notes' => 'nullable|string|max:500'
    ]);

    try {
        $product = Product::findOrFail($request->product_id);
        $product->Internal_notes = $request->notes;
        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث الملاحظات بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحديث الملاحظات: ' . $e->getMessage()
        ], 500);
    }
}

public function summaryInventory(Request $request)
{
    // جلب البيانات الأساسية للفلاتر
    $categories = Category::all();
    $brands = Product::distinct('brand')->whereNotNull('brand')->pluck('brand');
    $warehouses = StoreHouse::all();

    return view('reports::inventory.stock_report.summary_inventory', compact('categories', 'brands', 'warehouses'));
}

/**
 * جلب بيانات تقرير ملخص المخزون عبر AJAX
 */
public function summaryInventoryAjax(Request $request)
{
    try {
        // بناء الاستعلام الأساسي
        $query = Product::query()
            ->with(['category', 'product_details.storeHouse', 'invoice_items'])
            ->select('products.*');

        // تطبيق الفلاتر
        $this->applySummaryFilters($query, $request);

        // تطبيق الترتيب
        $this->applySummarySorting($query, $request);

        // جلب البيانات
        $products = $query->get();

        // معالجة البيانات للعرض
        $processedProducts = $this->processSummaryData($products, $request);

        // حساب الإجماليات
        $totals = $this->calculateSummaryTotals($processedProducts);

        // إعداد بيانات الرسم البياني
        $chartData = $this->prepareSummaryChartData($processedProducts, $request);

        return response()->json([
            'success' => true,
            'products' => $processedProducts,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $this->getGroupByLabel($request->group_by),
            'message' => 'تم تحميل البيانات بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تطبيق فلاتر ملخص المخزون
 */
private function applySummaryFilters($query, Request $request)
{
    // فلتر التصنيف
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // فلتر العلامة التجارية
    if ($request->filled('brand')) {
        $query->where('brand', $request->brand);
    }

    // فلتر المستودع
    if ($request->filled('warehouse')) {
        $query->whereHas('product_details', function ($q) use ($request) {
            $q->where('store_house_id', $request->warehouse);
        });
    }

    // فلتر النوع
    if ($request->filled('type')) {
        $query->whereHas('invoice_items', function ($q) use ($request) {
            $q->where('type', $request->type);
        });
    }

    // فلتر التاريخ
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query->whereHas('invoice_items', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        });
    }
}

/**
 * تطبيق الترتيب لملخص المخزون
 */
private function applySummarySorting($query, Request $request)
{
    switch ($request->sort_by) {
        case 'quantity_asc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) ASC');
            break;
        case 'quantity_desc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) DESC');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        default:
            $query->orderBy('name', 'asc');
    }
}

/**
 * معالجة بيانات ملخص المخزون للعرض
 */
private function processSummaryData($products, Request $request)
{
    $processedData = [];

    foreach ($products as $product) {
        // حساب الوارد
        $purchase = $this->getProductMovement($product, 'purchase', 'incoming');
        $saleReturn = $this->getProductMovement($product, 'sale_return', 'incoming');
        $transferIn = $this->getProductMovement($product, 'transfer', 'incoming');
        $manualIn = $this->getProductMovement($product, 'manual', 'incoming');

        // حساب المنصرف
        $sale = $this->getProductMovement($product, 'sale', 'outgoing');
        $purchaseReturnOut = $this->getProductMovement($product, 'purchase_return', 'outgoing');
        $transferOut = $this->getProductMovement($product, 'transfer', 'outgoing');
        $manualOut = $this->getProductMovement($product, 'manual', 'outgoing');

        // إعداد بيانات المنتج
        $productData = [
            'id' => $product->id,
            'code' => $product->code ?? $product->id,
            'name' => $product->name,
            'category' => $product->category ? $product->category->name : null,
            'brand' => $product->brand,
            'warehouse' => $this->getWarehouseNames($product),

            // الوارد
            'purchase' => $purchase,
            'sale_return' => $saleReturn,
            'transfer_in' => $transferIn,
            'manual_in' => $manualIn,
            'total_incoming' => $purchase + $saleReturn + $transferIn + $manualIn,

            // المنصرف
            'sale' => $sale,
            'purchase_return_out' => $purchaseReturnOut,
            'transfer_out' => $transferOut,
            'manual_out' => $manualOut,
            'total_outgoing' => $sale + $purchaseReturnOut + $transferOut + $manualOut,

            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at
        ];

        // حساب إجمالي الحركة
        $productData['total_movement'] = $productData['total_incoming'] + $productData['total_outgoing'];

        // تطبيق التجميع إذا كان مطلوباً
        if ($request->filled('group_by')) {
            $this->groupSummaryData($processedData, $productData, $request->group_by);
        } else {
            $processedData[] = $productData;
        }
    }

    return $processedData;
}

/**
 * حساب حركة المنتج حسب النوع والاتجاه
 */
private function getProductMovement($product, $type, $direction)
{
    $quantity = 0;

    // من فواتير المبيعات
    $salesQuantity = 0;
    if (in_array($type, ['sale', 'sale_return'])) {
        $salesQuantity = $product->invoice_items()
            ->whereHas('invoice', function($q) use ($type) {
                if ($type === 'sale') {
                    $q->where('type', 'normal'); // فاتورة مبيعات عادية
                } elseif ($type === 'sale_return') {
                    $q->where('type', 'returned'); // مرتجع مبيعات
                }
            })
            ->sum('quantity');
    }

    // من فواتير المشتريات
    $purchaseQuantity = 0;
    if (in_array($type, ['purchase', 'purchase_return'])) {
        $purchaseQuantity = $product->invoice_items()
            ->whereHas('purchaseInvoice', function($q) use ($type) {
                if ($type === 'purchase') {
                    $q->where('type', 'invoice'); // فاتورة مشتريات عادية
                } elseif ($type === 'purchase_return') {
                    $q->where('type', 'Return'); // مرتجع مشتريات
                }
            })
            ->sum('quantity');
    }

    // من تفاصيل المنتج (العمليات اليدوية والتحويلات)
    $productDetailQuantity = 0;
    if (in_array($type, ['transfer', 'manual'])) {
        $productDetailQuantity = $product->product_details()
            ->where('type_of_operation', $type)
            ->sum('quantity');
    }

    // تحديد الاتجاه (وارد أم منصرف)
    if ($direction === 'incoming') {
        if ($type === 'purchase') {
            // فواتير المشتريات - وارد
            $quantity = $purchaseQuantity;
        } elseif ($type === 'sale_return') {
            // مرتجع المبيعات - وارد للمخزن
            $quantity = $salesQuantity;
        } elseif (in_array($type, ['transfer', 'manual'])) {
            // التحويلات والعمليات اليدوية الواردة
            $quantity = $productDetailQuantity > 0 ? $productDetailQuantity : 0;
        }
    } elseif ($direction === 'outgoing') {
        if ($type === 'sale') {
            // فواتير المبيعات - منصرف
            $quantity = $salesQuantity;
        } elseif ($type === 'purchase_return') {
            // مرتجع المشتريات - منصرف من المخزن
            $quantity = $purchaseQuantity;
        } elseif (in_array($type, ['transfer', 'manual'])) {
            // التحويلات والعمليات اليدوية المنصرفة
            $quantity = $productDetailQuantity < 0 ? abs($productDetailQuantity) : 0;
        }
    }

    return $quantity;
}

/**
 * تجميع بيانات ملخص المخزون
 */
private function groupSummaryData(&$processedData, $productData, $groupBy)
{
    $groupKey = '';

    switch ($groupBy) {
        case 'category':
            $groupKey = $productData['category'] ?? 'غير محدد';
            break;
        case 'brand':
            $groupKey = $productData['brand'] ?? 'غير محدد';
            break;
        case 'warehouse':
            $groupKey = $productData['warehouse'] ?? 'غير محدد';
            break;
        default:
            $groupKey = 'عام';
    }

    if (!isset($processedData[$groupKey])) {
        $processedData[$groupKey] = [
            'group_name' => $groupKey,
            'items' => [],
            'total_products' => 0,
            'purchase' => 0,
            'sale_return' => 0,
            'transfer_in' => 0,
            'manual_in' => 0,
            'total_incoming' => 0,
            'sale' => 0,
            'purchase_return_out' => 0,
            'transfer_out' => 0,
            'manual_out' => 0,
            'total_outgoing' => 0,
            'total_movement' => 0
        ];
    }

    $processedData[$groupKey]['items'][] = $productData;
    $processedData[$groupKey]['total_products']++;

    // تجميع الوارد
    $processedData[$groupKey]['purchase'] += $productData['purchase'];
    $processedData[$groupKey]['sale_return'] += $productData['sale_return'];
    $processedData[$groupKey]['transfer_in'] += $productData['transfer_in'];
    $processedData[$groupKey]['manual_in'] += $productData['manual_in'];
    $processedData[$groupKey]['total_incoming'] += $productData['total_incoming'];

    // تجميع المنصرف
    $processedData[$groupKey]['sale'] += $productData['sale'];
    $processedData[$groupKey]['purchase_return_out'] += $productData['purchase_return_out'];
    $processedData[$groupKey]['transfer_out'] += $productData['transfer_out'];
    $processedData[$groupKey]['manual_out'] += $productData['manual_out'];
    $processedData[$groupKey]['total_outgoing'] += $productData['total_outgoing'];

    // إجمالي الحركة
    $processedData[$groupKey]['total_movement'] += $productData['total_movement'];
}

/**
 * حساب إجماليات ملخص المخزون
 */
private function calculateSummaryTotals($products)
{
    $totals = [
        'total_products' => 0,
        'total_incoming' => 0,
        'total_outgoing' => 0,
        'total_movement' => 0,
        'total_categories' => 0,
        'total_warehouses' => 0
    ];

    $categories = [];
    $warehouses = [];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $totals['total_products'] += $product['total_products'];
            $totals['total_incoming'] += $product['total_incoming'];
            $totals['total_outgoing'] += $product['total_outgoing'];
            $totals['total_movement'] += $product['total_movement'];

            foreach ($product['items'] as $item) {
                if ($item['category']) {
                    $categories[$item['category']] = true;
                }
                if ($item['warehouse']) {
                    $warehouses[$item['warehouse']] = true;
                }
            }
        } else {
            // للبيانات غير المجمعة
            $totals['total_products']++;
            $totals['total_incoming'] += $product['total_incoming'];
            $totals['total_outgoing'] += $product['total_outgoing'];
            $totals['total_movement'] += $product['total_movement'];

            if ($product['category']) {
                $categories[$product['category']] = true;
            }
            if ($product['warehouse']) {
                $warehouses[$product['warehouse']] = true;
            }
        }
    }

    $totals['total_categories'] = count($categories);
    $totals['total_warehouses'] = count($warehouses);

    return $totals;
}

/**
 * إعداد بيانات الرسم البياني لملخص المخزون
 */
private function prepareSummaryChartData($products, Request $request)
{
    $chartData = [
        'labels' => [],
        'incoming' => [],
        'outgoing' => []
    ];

    // تحديد عدد العناصر المراد عرضها في الرسم البياني (أعلى 10)
    $limit = 10;
    $chartProducts = [];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $chartProducts[] = [
                'name' => $product['group_name'],
                'incoming' => $product['total_incoming'],
                'outgoing' => $product['total_outgoing']
            ];
        } else {
            // للبيانات غير المجمعة
            $chartProducts[] = [
                'name' => $product['name'],
                'incoming' => $product['total_incoming'],
                'outgoing' => $product['total_outgoing']
            ];
        }
    }

    // ترتيب حسب إجمالي الحركة (تنازلي) وأخذ أعلى 10
    usort($chartProducts, function ($a, $b) {
        $totalA = $a['incoming'] + $a['outgoing'];
        $totalB = $b['incoming'] + $b['outgoing'];
        return $totalB <=> $totalA;
    });

    $chartProducts = array_slice($chartProducts, 0, $limit);

    foreach ($chartProducts as $item) {
        $chartData['labels'][] = $item['name'];
        $chartData['incoming'][] = $item['incoming'];
        $chartData['outgoing'][] = $item['outgoing'];
    }

    return $chartData;
}

/**
 * الحصول على أسماء المستودعات
 */
private function getWarehouseNames($product)
{
    if ($product->product_details && $product->product_details->storeHouse) {
        return $product->product_details->storeHouse->name;
    }

    return 'غير محدد';
}

/**
 * الحصول على تسمية التجميع
 */


public function detailedMovementInventory(Request $request)
{
    // جلب البيانات الأساسية للفلاتر
    $categories = Category::all();
    $brands = Product::distinct('brand')->whereNotNull('brand')->pluck('brand');
    $warehouses = StoreHouse::all();

    return view('reports::inventory.stock_report.detailed_movement_inventory',
        compact('categories', 'brands', 'warehouses'));
}

/**
 * جلب بيانات تقرير الحركة التفصيلية عبر AJAX
 */
public function detailedMovementInventoryAjax(Request $request)
{
    try {
        // بناء الاستعلام الأساسي
        $query = ProductDetails::query()
            ->with(['product.category', 'storeHouse'])
            ->select('product_details.*');

        // تطبيق الفلاتر
        $this->applyMovementFilters($query, $request);

        // تطبيق الترتيب
        $this->applyMovementSorting($query, $request);

        // جلب البيانات
        $movements = $query->get();

        // معالجة البيانات للعرض
        $processedMovements = $this->processMovementData($movements, $request);

        // حساب الإجماليات
        $totals = $this->calculateMovementTotals($processedMovements);

        // إعداد بيانات الرسم البياني
        $chartData = $this->prepareMovementChartData($processedMovements, $request);

        return response()->json([
            'success' => true,
            'movements' => $processedMovements,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $this->getGroupByLabel($request->group_by),
            'message' => 'تم تحميل البيانات بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تطبيق فلاتر الحركة التفصيلية
 */
private function applyMovementFilters($query, Request $request)
{
    // فلتر التصنيف
    if ($request->filled('category')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('category_id', $request->category);
        });
    }

    // فلتر العلامة التجارية
    if ($request->filled('brand')) {
        $query->whereHas('product', function ($q) use ($request) {
            $q->where('brand', $request->brand);
        });
    }

    // فلتر المستودع
    if ($request->filled('warehouse')) {
        $query->where('store_house_id', $request->warehouse);
    }

    // فلتر النوع
    if ($request->filled('type')) {
        $query->where('type_of_operation', $request->type);
    }

    // فلتر التاريخ
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = $request->start_date . ' 00:00:00';
        $endDate = $request->end_date . ' 23:59:59';

        $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // البحث النصي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('comments', 'like', "%{$search}%")
              ->orWhereHas('product', function ($subQ) use ($search) {
                  $subQ->where('name', 'like', "%{$search}%")
                       ->orWhere('serial_number', 'like', "%{$search}%")
                       ->orWhere('id', 'like', "%{$search}%");
              });
        });
    }

    // فلتر إظهار الوارد/المنصرف
    if ($request->boolean('show_incoming') && !$request->boolean('show_outgoing')) {
        $query->where(function ($q) {
            $q->where('quantity', '>', 0)
              ->orWhereIn('type_of_operation', ['purchase', 'sale_return', 'in']);
        });
    } elseif ($request->boolean('show_outgoing') && !$request->boolean('show_incoming')) {
        $query->where(function ($q) {
            $q->where('quantity', '<', 0)
              ->orWhereIn('type_of_operation', ['sale', 'purchase_return', 'out']);
        });
    }
}

/**
 * تطبيق الترتيب للحركة التفصيلية
 */
private function applyMovementSorting($query, Request $request)
{
    switch ($request->sort_by) {
        case 'date_desc':
            $query->orderBy('created_at', 'desc');
            break;
        case 'date_asc':
            $query->orderBy('created_at', 'asc');
            break;
        case 'quantity_desc':
            $query->orderBy('quantity', 'desc');
            break;
        case 'quantity_asc':
            $query->orderBy('quantity', 'asc');
            break;
        case 'product_name':
            $query->join('products', 'product_details.product_id', '=', 'products.id')
                  ->orderBy('products.name', 'asc');
            break;
        default:
            $query->orderBy('created_at', 'desc');
    }
}

/**
 * معالجة بيانات الحركة التفصيلية للعرض
 */
private function processMovementData($movements, Request $request)
{
    $processedData = [];

    foreach ($movements as $movement) {
        // تحديد اتجاه الحركة
        $direction = $this->determineMovementDirection($movement);

        // إعداد بيانات الحركة
        $movementData = [
            'id' => $movement->id,
            'product_id' => $movement->product_id,
            'product_code' => $movement->product->serial_number ?? $movement->product->id ?? 'N/A',
            'product_name' => $movement->product->name ?? 'N/A',
            'category' => $movement->product->category ? $movement->product->category->name : null,
            'brand' => $movement->product->brand ?? null,
            'warehouse_id' => $movement->store_house_id,
            'warehouse_name' => $movement->storeHouse->name ?? 'غير محدد',
            'type' => $movement->type_of_operation,
            'direction' => $direction,
            'quantity' => abs($movement->quantity ?? 0),
            'notes' => $movement->comments ?? null,
            'created_at' => $movement->created_at,
            'updated_at' => $movement->updated_at
        ];

        // تطبيق التجميع إذا كان مطلوباً
        if ($request->filled('group_by')) {
            $this->groupMovementData($processedData, $movementData, $request->group_by);
        } else {
            $processedData[] = $movementData;
        }
    }

    return $processedData;
}

/**
 * تحديد اتجاه الحركة (وارد أم منصرف)
 */
private function determineMovementDirection($movement)
{
    // الأنواع التي تعتبر وارد
    $incomingTypes = ['purchase', 'sale_return', 'in', 'manual_in', 'transfer_in'];

    // الأنواع التي تعتبر منصرف
    $outgoingTypes = ['sale', 'purchase_return', 'out', 'manual_out', 'transfer_out'];

    $type = $movement->type_of_operation;
    $quantity = $movement->quantity ?? 0;

    // التحقق من النوع أولاً
    if (in_array($type, $incomingTypes)) {
        return 'in';
    } elseif (in_array($type, $outgoingTypes)) {
        return 'out';
    }

    // إذا لم يكن النوع واضحاً، نعتمد على إشارة الكمية
    return $quantity >= 0 ? 'in' : 'out';
}

/**
 * تجميع بيانات الحركة التفصيلية
 */
private function groupMovementData(&$processedData, $movementData, $groupBy)
{
    $groupKey = '';

    switch ($groupBy) {
        case 'product':
            $groupKey = $movementData['product_name'];
            break;
        case 'category':
            $groupKey = $movementData['category'] ?? 'غير محدد';
            break;
        case 'brand':
            $groupKey = $movementData['brand'] ?? 'غير محدد';
            break;
        case 'warehouse':
            $groupKey = $movementData['warehouse_name'];
            break;
        case 'type':
            $groupKey = $this->getTypeLabel($movementData['type']);
            break;
        case 'date':
            $groupKey = $movementData['created_at']->format('Y-m-d');
            break;
        default:
            $groupKey = 'عام';
    }

    if (!isset($processedData[$groupKey])) {
        $processedData[$groupKey] = [
            'group_name' => $groupKey,
            'items' => [],
            'total_movements' => 0,
            'total_incoming' => 0,
            'total_outgoing' => 0,
            'total_quantity' => 0
        ];
    }

    $processedData[$groupKey]['items'][] = $movementData;
    $processedData[$groupKey]['total_movements']++;

    if ($movementData['direction'] === 'in') {
        $processedData[$groupKey]['total_incoming'] += $movementData['quantity'];
    } else {
        $processedData[$groupKey]['total_outgoing'] += $movementData['quantity'];
    }

    $processedData[$groupKey]['total_quantity'] += $movementData['quantity'];
}

/**
 * حساب إجماليات الحركة التفصيلية
 */
private function calculateMovementTotals($movements)
{
    $totals = [
        'total_movements' => 0,
        'total_incoming' => 0,
        'total_outgoing' => 0,
        'total_products' => 0,
        'total_categories' => 0,
        'total_warehouses' => 0
    ];

    $products = [];
    $categories = [];
    $warehouses = [];

    foreach ($movements as $movement) {
        if (isset($movement['group_name'])) {
            // للبيانات المجمعة
            $totals['total_movements'] += $movement['total_movements'];
            $totals['total_incoming'] += $movement['total_incoming'];
            $totals['total_outgoing'] += $movement['total_outgoing'];

            foreach ($movement['items'] as $item) {
                $products[$item['product_id']] = true;
                if ($item['category']) {
                    $categories[$item['category']] = true;
                }
                $warehouses[$item['warehouse_id']] = true;
            }
        } else {
            // للبيانات غير المجمعة
            $totals['total_movements']++;

            if ($movement['direction'] === 'in') {
                $totals['total_incoming'] += $movement['quantity'];
            } else {
                $totals['total_outgoing'] += $movement['quantity'];
            }

            $products[$movement['product_id']] = true;
            if ($movement['category']) {
                $categories[$movement['category']] = true;
            }
            $warehouses[$movement['warehouse_id']] = true;
        }
    }

    $totals['total_products'] = count($products);
    $totals['total_categories'] = count($categories);
    $totals['total_warehouses'] = count($warehouses);

    return $totals;
}

/**
 * إعداد بيانات الرسم البياني للحركة التفصيلية
 */
private function prepareMovementChartData($movements, Request $request)
{
    $chartData = [
        'labels' => [],
        'incoming' => [],
        'outgoing' => []
    ];

    // تجميع البيانات حسب التاريخ أو حسب المجموعة
    $groupedData = [];

    foreach ($movements as $movement) {
        $key = '';

        if (isset($movement['group_name'])) {
            // للبيانات المجمعة
            $key = $movement['group_name'];
            $groupedData[$key] = [
                'incoming' => $movement['total_incoming'],
                'outgoing' => $movement['total_outgoing']
            ];
        } else {
            // تجميع حسب التاريخ للبيانات غير المجمعة
            $key = $movement['created_at']->format('Y-m-d');

            if (!isset($groupedData[$key])) {
                $groupedData[$key] = ['incoming' => 0, 'outgoing' => 0];
            }

            if ($movement['direction'] === 'in') {
                $groupedData[$key]['incoming'] += $movement['quantity'];
            } else {
                $groupedData[$key]['outgoing'] += $movement['quantity'];
            }
        }
    }

    // ترتيب البيانات وأخذ أعلى 15 نقطة
    uksort($groupedData, function($a, $b) {
        return strtotime($a) - strtotime($b);
    });

    $groupedData = array_slice($groupedData, -15, 15, true);

    foreach ($groupedData as $label => $data) {
        $chartData['labels'][] = $label;
        $chartData['incoming'][] = $data['incoming'];
        $chartData['outgoing'][] = $data['outgoing'];
    }

    return $chartData;
}

/**
 * الحصول على تسمية النوع
 */
private function getTypeLabel($type)
{
    $typeLabels = [
        'sale' => 'فاتورة بيع',
        'purchase' => 'فاتورة شراء',
        'purchase_return' => 'مرتجع شراء',
        'sale_return' => 'مرتجع بيع',
        'transfer' => 'نقل',
        'manual' => 'يدوي',
        'in' => 'إدخال',
        'out' => 'إخراج',
        'adjustment' => 'تعديل جرد',
        'manufacturing' => 'تصنيع',
        'manual_in' => 'إدخال يدوي',
        'manual_out' => 'إخراج يدوي',
        'transfer_in' => 'نقل وارد',
        'transfer_out' => 'نقل منصرف'
    ];

    return $typeLabels[$type] ?? $type;
}


public function valueInventory(Request $request)
{
    // جلب البيانات الأساسية للفلاتر
    $suppliers = Supplier::all();
    $categories = Category::all();
    $warehouses = StoreHouse::all();
    $brands = Product::distinct('brand')->whereNotNull('brand')->pluck('brand');

    return view('reports::inventory.stock_report.value_inventory',
        compact('categories', 'warehouses', 'suppliers', 'brands'));
}

/**
 * جلب بيانات تقرير تقييم المخزون عبر AJAX
 */
public function valueInventoryAjax(Request $request)
{
    try {
        // بناء الاستعلام الأساسي
        $query = Product::query()
            ->with(['category', 'product_details.storeHouse', 'supplier'])
            ->select('products.*');

        // تطبيق الفلاتر
        $this->applyValueFilters($query, $request);

        // تطبيق الترتيب
        $this->applyValueSorting($query, $request);

        // جلب البيانات
        $products = $query->get();

        // معالجة البيانات للعرض
        $processedProducts = $this->processValueData($products, $request);

        // حساب الإجماليات
        $totals = $this->calculateValueTotals($processedProducts);

        // إعداد بيانات الرسم البياني
        $chartData = $this->prepareValueChartData($processedProducts, $request);

        return response()->json([
            'success' => true,
            'products' => $processedProducts,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $this->getGroupByLabel($request->group_by),
            'message' => 'تم تحميل البيانات بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تطبيق فلاتر تقييم المخزون
 */
private function applyValueFilters($query, Request $request)
{
    // فلتر المورد
    if ($request->filled('supplier')) {
        $query->where('supplier_id', $request->supplier);
    }

    // فلتر التصنيف
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // فلتر العلامة التجارية
    if ($request->filled('brand')) {
        $query->where('brand', $request->brand);
    }

    // فلتر المستودع
    if ($request->filled('warehouse')) {
        $query->whereHas('product_details', function ($q) use ($request) {
            $q->where('store_house_id', $request->warehouse);
        });
    }

    // فلتر التاريخ
    if ($request->filled('start_date') && $request->filled('end_date')) {
        $startDate = $request->start_date . ' 00:00:00';
        $endDate = $request->end_date . ' 23:59:59';

        $query->whereHas('invoice_items', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        });
    }

    // البحث النصي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    // إخفاء المنتجات بدون قيمة
    if ($request->boolean('hide_zero_value')) {
        $query->whereHas('product_details', function ($q) {
            $q->where('quantity', '>', 0);
        });
    }

    // إظهار المربحة فقط
    if ($request->boolean('show_profit_only')) {
        $query->where(function ($q) {
            $q->whereRaw('sale_price > purchase_price');
        });
    }
}

/**
 * تطبيق الترتيب لتقييم المخزون
 */
private function applyValueSorting($query, Request $request)
{
    switch ($request->sort_by) {
        case 'value_desc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity * unit_price), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) DESC');
            break;
        case 'value_asc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity * unit_price), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) ASC');
            break;
        case 'profit_desc':
            $query->orderByRaw('(sale_price - purchase_price) DESC');
            break;
        case 'profit_asc':
            $query->orderByRaw('(sale_price - purchase_price) ASC');
            break;
        case 'quantity_desc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) DESC');
            break;
        case 'quantity_asc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) ASC');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        default:
            $query->orderBy('name', 'asc');
    }
}

/**
 * معالجة بيانات تقييم المخزون للعرض
 */
private function processValueData($products, Request $request)
{
    $processedData = [];

    foreach ($products as $product) {
        // حساب الكمية الإجمالية
        $totalQuantity = $product->product_details ? $product->product_details->quantity : 0;

        // تجاهل المنتجات بدون كمية إذا كان الفلتر مفعل
        if ($request->boolean('hide_zero_value') && $totalQuantity <= 0) {
            continue;
        }

        // حساب الأسعار
        $purchasePrice = $product->purchase_price ?? 0;
        $salePrice = $product->sale_price ?? 0;

        // حساب القيم الإجمالية
        $totalPurchaseValue = $totalQuantity * $purchasePrice;
        $totalSaleValue = $totalQuantity * $salePrice;
        $expectedProfit = $totalSaleValue - $totalPurchaseValue;

        // تجاهل المنتجات غير المربحة إذا كان الفلتر مفعل
        if ($request->boolean('show_profit_only') && $expectedProfit <= 0) {
            continue;
        }

        // إعداد بيانات المنتج
        $productData = [
            'id' => $product->id,
            'code' => $product->serial_number ?? $product->id,
            'name' => $product->name,
            'category' => $product->category ? $product->category->name : null,
            'brand' => $product->brand,
            'supplier' => $product->supplier ? $product->supplier->name : null,
            'warehouse' => $this->getWarehouseNamesValue($product),
            'total_quantity' => $totalQuantity,
            'purchase_price' => $purchasePrice,
            'sale_price' => $salePrice,
            'total_purchase_value' => $totalPurchaseValue,
            'total_sale_value' => $totalSaleValue,
            'expected_profit' => $expectedProfit,
            'profit_margin' => $purchasePrice > 0 ? (($salePrice - $purchasePrice) / $purchasePrice) * 100 : 0,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at
        ];

        // تطبيق التجميع إذا كان مطلوباً
        if ($request->filled('group_by')) {
            $this->groupValueData($processedData, $productData, $request->group_by);
        } else {
            $processedData[] = $productData;
        }
    }

    return $processedData;
}

/**
 * تجميع بيانات تقييم المخزون
 */
private function groupValueData(&$processedData, $productData, $groupBy)
{
    $groupKey = '';

    switch ($groupBy) {
        case 'category':
            $groupKey = $productData['category'] ?? 'غير محدد';
            break;
        case 'brand':
            $groupKey = $productData['brand'] ?? 'غير محدد';
            break;
        case 'supplier':
            $groupKey = $productData['supplier'] ?? 'غير محدد';
            break;
        case 'warehouse':
            $groupKey = $productData['warehouse'] ?? 'غير محدد';
            break;
        default:
            $groupKey = 'عام';
    }

    if (!isset($processedData[$groupKey])) {
        $processedData[$groupKey] = [
            'group_name' => $groupKey,
            'items' => [],
            'total_products' => 0,
            'total_quantity' => 0,
            'total_purchase_value' => 0,
            'total_sale_value' => 0,
            'total_profit' => 0
        ];
    }

    $processedData[$groupKey]['items'][] = $productData;
    $processedData[$groupKey]['total_products']++;
    $processedData[$groupKey]['total_quantity'] += $productData['total_quantity'];
    $processedData[$groupKey]['total_purchase_value'] += $productData['total_purchase_value'];
    $processedData[$groupKey]['total_sale_value'] += $productData['total_sale_value'];
    $processedData[$groupKey]['total_profit'] += $productData['expected_profit'];
}

/**
 * حساب إجماليات تقييم المخزون
 */
private function calculateValueTotals($products)
{
    $totals = [
        'total_products' => 0,
        'total_quantity' => 0,
        'total_purchase_value' => 0,
        'total_sale_value' => 0,
        'total_profit' => 0,
        'total_categories' => 0,
        'total_suppliers' => 0,
        'total_warehouses' => 0
    ];

    $categories = [];
    $suppliers = [];
    $warehouses = [];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $totals['total_products'] += $product['total_products'];
            $totals['total_quantity'] += $product['total_quantity'];
            $totals['total_purchase_value'] += $product['total_purchase_value'];
            $totals['total_sale_value'] += $product['total_sale_value'];
            $totals['total_profit'] += $product['total_profit'];

            foreach ($product['items'] as $item) {
                if ($item['category']) {
                    $categories[$item['category']] = true;
                }
                if ($item['supplier']) {
                    $suppliers[$item['supplier']] = true;
                }
                if ($item['warehouse']) {
                    $warehouses[$item['warehouse']] = true;
                }
            }
        } else {
            // للبيانات غير المجمعة
            $totals['total_products']++;
            $totals['total_quantity'] += $product['total_quantity'];
            $totals['total_purchase_value'] += $product['total_purchase_value'];
            $totals['total_sale_value'] += $product['total_sale_value'];
            $totals['total_profit'] += $product['expected_profit'];

            if ($product['category']) {
                $categories[$product['category']] = true;
            }
            if ($product['supplier']) {
                $suppliers[$product['supplier']] = true;
            }
            if ($product['warehouse']) {
                $warehouses[$product['warehouse']] = true;
            }
        }
    }

    $totals['total_categories'] = count($categories);
    $totals['total_suppliers'] = count($suppliers);
    $totals['total_warehouses'] = count($warehouses);

    return $totals;
}

/**
 * إعداد بيانات الرسم البياني لتقييم المخزون
 */
private function prepareValueChartData($products, Request $request)
{
    $chartData = [
        'labels' => [],
        'purchase_values' => [],
        'sale_values' => [],
        'profit_values' => []
    ];

    // تحديد عدد العناصر المراد عرضها في الرسم البياني (أعلى 10)
    $limit = 10;
    $chartProducts = [];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $chartProducts[] = [
                'name' => $product['group_name'],
                'purchase_value' => $product['total_purchase_value'],
                'sale_value' => $product['total_sale_value'],
                'profit_value' => $product['total_profit']
            ];
        } else {
            // للبيانات غير المجمعة
            $chartProducts[] = [
                'name' => $product['name'],
                'purchase_value' => $product['total_purchase_value'],
                'sale_value' => $product['total_sale_value'],
                'profit_value' => $product['expected_profit']
            ];
        }
    }

    // ترتيب حسب القيمة الإجمالية (تنازلي) وأخذ أعلى 10
    usort($chartProducts, function ($a, $b) {
        return $b['sale_value'] <=> $a['sale_value'];
    });

    $chartProducts = array_slice($chartProducts, 0, $limit);

    foreach ($chartProducts as $item) {
        $chartData['labels'][] = $item['name'];
        $chartData['purchase_values'][] = $item['purchase_value'];
        $chartData['sale_values'][] = $item['sale_value'];
        $chartData['profit_values'][] = $item['profit_value'];
    }

    return $chartData;
}

/**
 * الحصول على أسماء المستودعات
 */
private function getWarehouseNamesValue($product)
{
    if ($product->product_details && $product->product_details->storeHouse) {
        return $product->product_details->storeHouse->name;
    }

    return 'غير محدد';
}

/**
 * الحصول على تسمية التجميع
 */


public function inventoryBlance(Request $request)
{
    // جلب البيانات الأساسية للفلاتر
    $products = Product::all();
    $categories = Category::all();
    $brands = Product::distinct('brand')->whereNotNull('brand')->pluck('brand');
    $warehouses = StoreHouse::all();

    return view('reports::inventory.stock_report.inventory_blance',
        compact('products', 'categories', 'brands', 'warehouses'));
}

/**
 * جلب بيانات ملخص رصيد المخزون عبر AJAX
 */
public function inventoryBlanceAjax(Request $request)
{
    try {
        // بناء الاستعلام الأساسي
        $query = Product::query()
            ->with(['category', 'product_details.storeHouse'])
            ->select('products.*');

        // تطبيق الفلاتر
        $this->applyBalanceFilters($query, $request);

        // تطبيق الترتيب
        $this->applyBalanceSorting($query, $request);

        // جلب البيانات
        $products = $query->get();

        // معالجة البيانات للعرض
        $processedProducts = $this->processBalanceData($products, $request);

        // حساب الإجماليات
        $totals = $this->calculateBalanceTotals($processedProducts);

        // إعداد بيانات الرسم البياني
        $chartData = $this->prepareBalanceChartData($processedProducts, $request);

        return response()->json([
            'success' => true,
            'products' => $processedProducts,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $this->getGroupByLabel($request->group_by),
            'message' => 'تم تحميل البيانات بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تطبيق فلاتر ملخص رصيد المخزون
 */
private function applyBalanceFilters($query, Request $request)
{
    // فلتر المنتج
    if ($request->filled('product')) {
        $query->where('id', $request->product);
    }

    // فلتر التصنيف
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // فلتر العلامة التجارية
    if ($request->filled('brand')) {
        $query->where('brand', $request->brand);
    }

    // فلتر المستودع
    if ($request->filled('warehouse')) {
        $query->whereHas('product_details', function ($q) use ($request) {
            $q->where('store_house_id', $request->warehouse);
        });
    }

    // فلتر الحالة
    if ($request->filled('status')) {
        switch ($request->status) {
            case '1': // متاح
                $query->whereHas('product_details', function ($q) {
                    $q->where('quantity', '>', 10);
                });
                break;
            case '2': // مخزون منخفض
                $query->whereHas('product_details', function ($q) {
                    $q->where('quantity', '>', 0)->where('quantity', '<=', 10);
                });
                break;
            case '3': // مخزون نفد
                $query->whereHas('product_details', function ($q) {
                    $q->where('quantity', '<=', 0);
                });
                break;
            case '4': // غير نشط
                $query->where('status', 'غير نشط');
                break;
        }
    }

    // البحث النصي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    // إخفاء الرصيد الصفري
    if ($request->boolean('hide_zero_balance')) {
        $query->whereHas('product_details', function ($q) {
            $q->where('quantity', '>', 0);
        });
    }
}

/**
 * تطبيق الترتيب لملخص رصيد المخزون
 */
private function applyBalanceSorting($query, Request $request)
{
    switch ($request->sort_by) {
        case 'quantity_desc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) DESC');
            break;
        case 'quantity_asc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) ASC');
            break;
        case 'value_desc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0) * COALESCE(sale_price, 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) DESC');
            break;
        case 'value_asc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0) * COALESCE(sale_price, 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) ASC');
            break;
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        default:
            $query->orderBy('name', 'asc');
    }
}

/**
 * معالجة بيانات ملخص رصيد المخزون للعرض
 */
private function processBalanceData($products, Request $request)
{
    $processedData = [];

    foreach ($products as $product) {
        // حساب الكمية الإجمالية
        $totalQuantity = $product->product_details->sum('quantity');

        // تطبيق فلتر إخفاء الرصيد الصفري
        if ($request->boolean('hide_zero_balance') && $totalQuantity <= 0) {
            continue;
        }

        // حساب القيمة الإجمالية
        $totalValue = $totalQuantity * ($product->sale_price ?? 0);

        // تحديد حالة المنتج
        $status = $this->determineProductStatus($totalQuantity, $product);

        // إعداد بيانات المنتج
        $productData = [
            'id' => $product->id,
            'code' => $product->serial_number ?? $product->id,
            'name' => $product->name,
            'category' => $product->category ? $product->category->name : null,
            'brand' => $product->brand,
            'warehouse' => $this->getWarehouseNames($product),
            'total_quantity' => $totalQuantity,
            'total_value' => $totalValue,
            'status' => $status,
            'sale_price' => $product->sale_price ?? 0,
            'purchase_price' => $product->purchase_price ?? 0,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at
        ];

        // تطبيق التجميع إذا كان مطلوباً
        if ($request->filled('group_by')) {
            $this->groupBalanceData($processedData, $productData, $request->group_by);
        } else {
            $processedData[] = $productData;
        }
    }

    return $processedData;
}

/**
 * تحديد حالة المنتج
 */
private function determineProductStatus($quantity, $product)
{
    // التحقق من الحالة المحفوظة في قاعدة البيانات أولاً
    if (isset($product->status)) {
        return $product->status;
    }

    // تحديد الحالة بناءً على الكمية
    if ($quantity > 10) {
        return 'متاح';
    } elseif ($quantity > 0) {
        return 'مخزون منخفض';
    } else {
        return 'مخزون نفد';
    }
}

/**
 * تجميع بيانات ملخص رصيد المخزون
 */
private function groupBalanceData(&$processedData, $productData, $groupBy)
{
    $groupKey = '';

    switch ($groupBy) {
        case 'category':
            $groupKey = $productData['category'] ?? 'غير محدد';
            break;
        case 'brand':
            $groupKey = $productData['brand'] ?? 'غير محدد';
            break;
        case 'warehouse':
            $groupKey = $productData['warehouse'] ?? 'غير محدد';
            break;
        case 'status':
            $groupKey = $productData['status'];
            break;
        default:
            $groupKey = 'عام';
    }

    if (!isset($processedData[$groupKey])) {
        $processedData[$groupKey] = [
            'group_name' => $groupKey,
            'items' => [],
            'total_products' => 0,
            'total_quantity' => 0,
            'total_value' => 0,
            'available_count' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
            'inactive_count' => 0
        ];
    }

    $processedData[$groupKey]['items'][] = $productData;
    $processedData[$groupKey]['total_products']++;
    $processedData[$groupKey]['total_quantity'] += $productData['total_quantity'];
    $processedData[$groupKey]['total_value'] += $productData['total_value'];

    // تحديث عدادات الحالة
    switch ($productData['status']) {
        case 'متاح':
            $processedData[$groupKey]['available_count']++;
            break;
        case 'مخزون منخفض':
            $processedData[$groupKey]['low_stock_count']++;
            break;
        case 'مخزون نفد':
            $processedData[$groupKey]['out_of_stock_count']++;
            break;
        case 'غير نشط':
            $processedData[$groupKey]['inactive_count']++;
            break;
    }
}

/**
 * حساب إجماليات ملخص رصيد المخزون
 */
private function calculateBalanceTotals($products)
{
    $totals = [
        'total_products' => 0,
        'total_quantity' => 0,
        'total_value' => 0,
        'available_count' => 0,
        'low_stock_count' => 0,
        'out_of_stock_count' => 0,
        'inactive_count' => 0
    ];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $totals['total_products'] += $product['total_products'];
            $totals['total_quantity'] += $product['total_quantity'];
            $totals['total_value'] += $product['total_value'];
            $totals['available_count'] += $product['available_count'];
            $totals['low_stock_count'] += $product['low_stock_count'];
            $totals['out_of_stock_count'] += $product['out_of_stock_count'];
            $totals['inactive_count'] += $product['inactive_count'];
        } else {
            // للبيانات غير المجمعة
            $totals['total_products']++;
            $totals['total_quantity'] += $product['total_quantity'];
            $totals['total_value'] += $product['total_value'];

            switch ($product['status']) {
                case 'متاح':
                    $totals['available_count']++;
                    break;
                case 'مخزون منخفض':
                    $totals['low_stock_count']++;
                    break;
                case 'مخزون نفد':
                    $totals['out_of_stock_count']++;
                    break;
                case 'غير نشط':
                    $totals['inactive_count']++;
                    break;
            }
        }
    }

    return $totals;
}

/**
 * إعداد بيانات الرسم البياني لملخص رصيد المخزون
 */
private function prepareBalanceChartData($products, Request $request)
{
    $chartData = [
        'available' => 0,
        'low_stock' => 0,
        'out_of_stock' => 0,
        'inactive' => 0
    ];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $chartData['available'] += $product['available_count'];
            $chartData['low_stock'] += $product['low_stock_count'];
            $chartData['out_of_stock'] += $product['out_of_stock_count'];
            $chartData['inactive'] += $product['inactive_count'];
        } else {
            // للبيانات غير المجمعة
            switch ($product['status']) {
                case 'متاح':
                    $chartData['available']++;
                    break;
                case 'مخزون منخفض':
                    $chartData['low_stock']++;
                    break;
                case 'مخزون نفد':
                    $chartData['out_of_stock']++;
                    break;
                case 'غير نشط':
                    $chartData['inactive']++;
                    break;
            }
        }
    }

    return $chartData;
}



public function getQuickBalanceReport()
{
    try {
        $products = Product::with(['product_details'])->get();

        $summary = [
            'total_products' => 0,
            'available' => 0,
            'low_stock' => 0,
            'out_of_stock' => 0,
            'total_value' => 0
        ];

        foreach ($products as $product) {
            $quantity = $product->product_details->sum('quantity');
            $value = $quantity * ($product->sale_price ?? 0);

            $summary['total_products']++;
            $summary['total_value'] += $value;

            if ($quantity > 10) {
                $summary['available']++;
            } elseif ($quantity > 0) {
                $summary['low_stock']++;
            } else {
                $summary['out_of_stock']++;
            }
        }

        return response()->json([
            'success' => true,
            'summary' => $summary
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في جلب التقرير السريع: ' . $e->getMessage()
        ], 500);
    }
}

public function trialBalance(Request $request)
{
    // جلب البيانات الأساسية للفلاتر
    $products = Product::all();
    $categories = Category::all();
    $warehouses = StoreHouse::all();

    return view('reports::inventory.stock_report.trial_balance',
        compact('products', 'categories', 'warehouses'));
}

/**
 * جلب بيانات ميزان المراجعة للمخزون عبر AJAX
 */
public function trialBalanceAjax(Request $request)
{
    try {
        // بناء الاستعلام الأساسي
        $query = Product::query()
            ->with(['category', 'product_details.storeHouse', 'invoice_items'])
            ->select('products.*');

        // تطبيق الفلاتر
        $this->applyTrialBalanceFilters($query, $request);

        // تطبيق الترتيب
        $this->applyTrialBalanceSorting($query, $request);

        // جلب البيانات
        $products = $query->get();

        // معالجة البيانات للعرض
        $processedProducts = $this->processTrialBalanceData($products, $request);

        // حساب الإجماليات
        $totals = $this->calculateTrialBalanceTotals($processedProducts);

        // إعداد بيانات الرسم البياني
        $chartData = $this->prepareTrialBalanceChartData($processedProducts, $request);

        // تحديد التواريخ للعرض
        $fromDate = $request->start_date ? Carbon::parse($request->start_date)->format('d/m/Y') : 'غير محدد';
        $toDate = $request->end_date ? Carbon::parse($request->end_date)->format('d/m/Y') : 'غير محدد';

        return response()->json([
            'success' => true,
            'products' => $processedProducts,
            'totals' => $totals,
            'chart_data' => $chartData,
            'group_by' => $this->getGroupByLabel($request->group_by),
            'from_date' => $fromDate,
            'to_date' => $toDate,
            'message' => 'تم تحميل البيانات بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات: ' . $e->getMessage()
        ], 500);
    }
}

/**
 * تطبيق فلاتر ميزان المراجعة للمخزون
 */
private function applyTrialBalanceFilters($query, Request $request)
{
    // فلتر المنتج
    if ($request->filled('product')) {
        $query->where('id', $request->product);
    }

    // فلتر التصنيف
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // فلتر المستودع
    if ($request->filled('warehouse')) {
        $query->whereHas('product_details', function ($q) use ($request) {
            $q->where('store_house_id', $request->warehouse);
        });
    }

    // البحث النصي
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('serial_number', 'like', "%{$search}%")
              ->orWhere('id', 'like', "%{$search}%");
        });
    }

    // إخفاء الأرصدة الصفرية
    if ($request->boolean('hide_zero_balance')) {
        $query->whereHas('product_details', function ($q) {
            $q->where('quantity', '!=', 0);
        });
    }

    // إظهار المنتجات بحركة فقط
    if ($request->boolean('show_movements_only')) {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($startDate && $endDate) {
            $query->whereHas('invoice_items', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }
    }
}

/**
 * تطبيق الترتيب لميزان المراجعة للمخزون
 */
private function applyTrialBalanceSorting($query, Request $request)
{
    switch ($request->sort_by) {
        case 'name_asc':
            $query->orderBy('name', 'asc');
            break;
        case 'name_desc':
            $query->orderBy('name', 'desc');
            break;
        case 'quantity_desc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) DESC');
            break;
        case 'quantity_asc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) ASC');
            break;
        case 'value_desc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity * unit_price), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) DESC');
            break;
        case 'value_asc':
            $query->orderByRaw('(
                SELECT COALESCE(SUM(quantity * unit_price), 0)
                FROM product_details
                WHERE product_details.product_id = products.id
            ) ASC');
            break;
        case 'movement_desc':
            $query->orderByRaw('(
                SELECT COALESCE(COUNT(*), 0)
                FROM invoice_items
                WHERE invoice_items.product_id = products.id
            ) DESC');
            break;
        default:
            $query->orderBy('name', 'asc');
    }
}

/**
 * معالجة بيانات ميزان المراجعة للعرض
 */
private function processTrialBalanceData($products, Request $request)
{
    $processedData = [];
    $startDate = $request->start_date;
    $endDate = $request->end_date;

    foreach ($products as $product) {
        // حساب الرصيد الابتدائي (قبل الفترة)
        $initialQuantity = $this->calculateInitialBalance($product, $startDate);
        $initialAmount = $initialQuantity * ($product->purchase_price ?? 0);

        // حساب الوارد في الفترة
        $incomingData = $this->calculateIncomingMovements($product, $startDate, $endDate);

        // حساب المنصرف في الفترة
        $outgoingData = $this->calculateOutgoingMovements($product, $startDate, $endDate);

        // حساب الرصيد النهائي
        $netQuantity = $initialQuantity + $incomingData['quantity'] - $outgoingData['quantity'];
        $netAmount = $initialAmount + $incomingData['amount'] - $outgoingData['amount'];

        // تطبيق فلتر إخفاء الأرصدة الصفرية
        if ($request->boolean('hide_zero_balance') && $netQuantity == 0) {
            continue;
        }

        // تطبيق فلتر إظهار المنتجات بحركة فقط
        if ($request->boolean('show_movements_only') &&
            ($incomingData['quantity'] == 0 && $outgoingData['quantity'] == 0)) {
            continue;
        }

        // إعداد بيانات المنتج
        $productData = [
            'id' => $product->id,
            'code' => $product->serial_number ?? $product->id,
            'name' => $product->name,
            'category' => $product->category ? $product->category->name : null,
            'brand' => $product->brand,
            'warehouse' => $this->getWarehouseNames($product),

            // الرصيد الابتدائي
            'initial_quantity' => $initialQuantity,
            'initial_amount' => $initialAmount,

            // حركات الفترة - الوارد
            'incoming_quantity' => $incomingData['quantity'],
            'incoming_amount' => $incomingData['amount'],

            // حركات الفترة - المنصرف
            'outgoing_quantity' => $outgoingData['quantity'],
            'outgoing_amount' => $outgoingData['amount'],

            // الرصيد النهائي
            'net_quantity' => $netQuantity,
            'net_amount' => $netAmount,

            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at
        ];

        // تطبيق التجميع إذا كان مطلوباً
        if ($request->filled('group_by')) {
            $this->groupTrialBalanceData($processedData, $productData, $request->group_by);
        } else {
            $processedData[] = $productData;
        }
    }

    return $processedData;
}

/**
 * حساب الرصيد الابتدائي (قبل الفترة)
 */
private function calculateInitialBalance($product, $startDate)
{
    if (!$startDate) {
        return $product->product_details->sum('quantity');
    }

    // حساب الرصيد قبل تاريخ البداية
    $purchasesBeforeDate = $product->invoice_items()
        ->where('type', 'purchase')
        ->where('created_at', '<', $startDate)
        ->sum('quantity');

    $salesBeforeDate = $product->invoice_items()
        ->where('type', 'sale')
        ->where('created_at', '<', $startDate)
        ->sum('quantity');

    $returnsBeforeDate = $product->invoice_items()
        ->whereIn('type', ['purchase_return', 'sale_return'])
        ->where('created_at', '<', $startDate)
        ->sum('quantity');

    return $purchasesBeforeDate - $salesBeforeDate + $returnsBeforeDate;
}

/**
 * حساب الحركات الواردة في الفترة
 */
private function calculateIncomingMovements($product, $startDate, $endDate)
{
    $quantity = 0;
    $amount = 0;

    if ($startDate && $endDate) {
        // المشتريات
        $purchases = $product->invoice_items()
            ->where('type', 'purchase')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($purchases as $purchase) {
            $quantity += $purchase->quantity;
            $amount += $purchase->total ?? ($purchase->quantity * $purchase->unit_price);
        }

        // مرتجع المبيعات (يعتبر وارد)
        $saleReturns = $product->invoice_items()
            ->where('type', 'sale_return')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($saleReturns as $return) {
            $quantity += $return->quantity;
            $amount += $return->total ?? ($return->quantity * $return->unit_price);
        }

        // العمليات اليدوية الواردة
        $manualIncoming = $product->product_details()
            ->where('type_of_operation', 'in')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($manualIncoming as $manual) {
            if ($manual->quantity > 0) {
                $quantity += $manual->quantity;
                $amount += $manual->quantity * ($manual->unit_price ?? $product->purchase_price ?? 0);
            }
        }
    }

    return [
        'quantity' => $quantity,
        'amount' => $amount
    ];
}

/**
 * حساب الحركات المنصرفة في الفترة
 */
private function calculateOutgoingMovements($product, $startDate, $endDate)
{
    $quantity = 0;
    $amount = 0;

    if ($startDate && $endDate) {
        // المبيعات
        $sales = $product->invoice_items()
            ->where('type', 'sale')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($sales as $sale) {
            $quantity += $sale->quantity;
            $amount += $sale->total ?? ($sale->quantity * $sale->unit_price);
        }

        // مرتجع المشتريات (يعتبر منصرف)
        $purchaseReturns = $product->invoice_items()
            ->where('type', 'purchase_return')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($purchaseReturns as $return) {
            $quantity += $return->quantity;
            $amount += $return->total ?? ($return->quantity * $return->unit_price);
        }

        // العمليات اليدوية المنصرفة
        $manualOutgoing = $product->product_details()
            ->where('type_of_operation', 'out')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        foreach ($manualOutgoing as $manual) {
            if ($manual->quantity > 0) {
                $quantity += $manual->quantity;
                $amount += $manual->quantity * ($manual->unit_price ?? $product->purchase_price ?? 0);
            }
        }
    }

    return [
        'quantity' => $quantity,
        'amount' => $amount
    ];
}

/**
 * تجميع بيانات ميزان المراجعة
 */
private function groupTrialBalanceData(&$processedData, $productData, $groupBy)
{
    $groupKey = '';

    switch ($groupBy) {
        case 'category':
            $groupKey = $productData['category'] ?? 'غير محدد';
            break;
        case 'warehouse':
            $groupKey = $productData['warehouse'] ?? 'غير محدد';
            break;
        case 'movement_type':
            // تصنيف حسب نوع الحركة (وارد أكثر، منصرف أكثر، متوازن)
            $incoming = $productData['incoming_quantity'];
            $outgoing = $productData['outgoing_quantity'];

            if ($incoming > $outgoing) {
                $groupKey = 'منتجات وارد أكثر';
            } elseif ($outgoing > $incoming) {
                $groupKey = 'منتجات منصرف أكثر';
            } else {
                $groupKey = 'منتجات متوازنة';
            }
            break;
        default:
            $groupKey = 'عام';
    }

    if (!isset($processedData[$groupKey])) {
        $processedData[$groupKey] = [
            'group_name' => $groupKey,
            'items' => [],
            'total_products' => 0,
            'initial_quantity' => 0,
            'initial_amount' => 0,
            'incoming_quantity' => 0,
            'incoming_amount' => 0,
            'outgoing_quantity' => 0,
            'outgoing_amount' => 0,
            'net_quantity' => 0,
            'net_amount' => 0
        ];
    }

    $processedData[$groupKey]['items'][] = $productData;
    $processedData[$groupKey]['total_products']++;
    $processedData[$groupKey]['initial_quantity'] += $productData['initial_quantity'];
    $processedData[$groupKey]['initial_amount'] += $productData['initial_amount'];
    $processedData[$groupKey]['incoming_quantity'] += $productData['incoming_quantity'];
    $processedData[$groupKey]['incoming_amount'] += $productData['incoming_amount'];
    $processedData[$groupKey]['outgoing_quantity'] += $productData['outgoing_quantity'];
    $processedData[$groupKey]['outgoing_amount'] += $productData['outgoing_amount'];
    $processedData[$groupKey]['net_quantity'] += $productData['net_quantity'];
    $processedData[$groupKey]['net_amount'] += $productData['net_amount'];
}

/**
 * حساب إجماليات ميزان المراجعة
 */
private function calculateTrialBalanceTotals($products)
{
    $totals = [
        'total_products' => 0,
        'total_quantity' => 0,
        'total_value' => 0,
        'total_movements' => 0,
        'initial_quantity' => 0,
        'initial_amount' => 0,
        'incoming_quantity' => 0,
        'incoming_amount' => 0,
        'outgoing_quantity' => 0,
        'outgoing_amount' => 0,
        'net_quantity' => 0,
        'net_amount' => 0
    ];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $totals['total_products'] += $product['total_products'];
            $totals['initial_quantity'] += $product['initial_quantity'];
            $totals['initial_amount'] += $product['initial_amount'];
            $totals['incoming_quantity'] += $product['incoming_quantity'];
            $totals['incoming_amount'] += $product['incoming_amount'];
            $totals['outgoing_quantity'] += $product['outgoing_quantity'];
            $totals['outgoing_amount'] += $product['outgoing_amount'];
            $totals['net_quantity'] += $product['net_quantity'];
            $totals['net_amount'] += $product['net_amount'];
            $totals['total_movements'] += ($product['incoming_quantity'] + $product['outgoing_quantity']);
        } else {
            // للبيانات غير المجمعة
            $totals['total_products']++;
            $totals['initial_quantity'] += $product['initial_quantity'];
            $totals['initial_amount'] += $product['initial_amount'];
            $totals['incoming_quantity'] += $product['incoming_quantity'];
            $totals['incoming_amount'] += $product['incoming_amount'];
            $totals['outgoing_quantity'] += $product['outgoing_quantity'];
            $totals['outgoing_amount'] += $product['outgoing_amount'];
            $totals['net_quantity'] += $product['net_quantity'];
            $totals['net_amount'] += $product['net_amount'];
            $totals['total_movements'] += ($product['incoming_quantity'] + $product['outgoing_quantity']);
        }
    }

    $totals['total_quantity'] = $totals['net_quantity'];
    $totals['total_value'] = $totals['net_amount'];

    return $totals;
}

/**
 * إعداد بيانات الرسم البياني لميزان المراجعة
 */
private function prepareTrialBalanceChartData($products, Request $request)
{
    $chartData = [
        'labels' => [],
        'initial_values' => [],
        'incoming_values' => [],
        'outgoing_values' => [],
        'final_values' => []
    ];

    // تحديد عدد العناصر المراد عرضها في الرسم البياني (أعلى 10)
    $limit = 10;
    $chartProducts = [];

    foreach ($products as $product) {
        if (isset($product['group_name'])) {
            // للبيانات المجمعة
            $chartProducts[] = [
                'name' => $product['group_name'],
                'initial_value' => $product['initial_amount'],
                'incoming_value' => $product['incoming_amount'],
                'outgoing_value' => $product['outgoing_amount'],
                'final_value' => $product['net_amount']
            ];
        } else {
            // للبيانات غير المجمعة
            $chartProducts[] = [
                'name' => $product['name'],
                'initial_value' => $product['initial_amount'],
                'incoming_value' => $product['incoming_amount'],
                'outgoing_value' => $product['outgoing_amount'],
                'final_value' => $product['net_amount']
            ];
        }
    }

    // ترتيب حسب القيمة النهائية (تنازلي) وأخذ أعلى 10
    usort($chartProducts, function ($a, $b) {
        return abs($b['final_value']) <=> abs($a['final_value']);
    });

    $chartProducts = array_slice($chartProducts, 0, $limit);

    foreach ($chartProducts as $item) {
        $chartData['labels'][] = $item['name'];
        $chartData['initial_values'][] = $item['initial_value'];
        $chartData['incoming_values'][] = $item['incoming_value'];
        $chartData['outgoing_values'][] = $item['outgoing_value'];
        $chartData['final_values'][] = $item['final_value'];
    }

    return $chartData;
}

/**
 * الحصول على أسماء المستودعات
 */

/**
 * الحصول على تسمية التجميع
 */
private function getGroupByLabel($groupBy)
{
    switch ($groupBy) {
        case 'category':
            return 'التصنيف';
        case 'warehouse':
            return 'المستودع';
        case 'movement_type':
            return 'نوع الحركة';
        default:
            return null;
    }
}


public function Inventory_mov_det_product(Request $request)
{
    // جلب البيانات الأساسية للفلترات
    $products = Product::select('id', 'name')->get();
    $categories = Category::select('id', 'name')->get();
    $brands = Product::distinct('brand')->whereNotNull('brand')->pluck('brand');
    $warehouses = StoreHouse::select('id', 'name')->get();

    // إذا كان الطلب AJAX، إرجاع البيانات المفلترة
    if ($request->ajax() || $request->expectsJson()) {
        return $this->getInventoryMovementData($request);
    }

    // عرض الصفحة الأساسية
    $movements = collect(); // مجموعة فارغة في البداية

    return view('reports::inventory.stock_report.Inventory_mov_det_product', compact(
        'products', 'categories', 'brands', 'warehouses', 'movements'
    ));
}

public function Inventory_mov_det_product_ajax(Request $request)
{
    return $this->getInventoryMovementData($request);
}

private function prepareChartDataMovement($movements, $request)
{
    try {
        $chartData = [
            'labels' => [],
            'in_values' => [],
            'out_values' => []
        ];

        // تجميع البيانات حسب التاريخ
        $dailyData = [];

        foreach ($movements as $movement) {
            $date = $movement->created_at ? $movement->created_at->format('Y-m-d') : date('Y-m-d');

            if (!isset($dailyData[$date])) {
                $dailyData[$date] = ['in' => 0, 'out' => 0];
            }

            $total = $movement->total ?? 0;
            if ($movement->quantity > 0) {
                $dailyData[$date]['in'] += $total;
            } else {
                $dailyData[$date]['out'] += abs($total);
            }
        }

        // ترتيب البيانات حسب التاريخ
        ksort($dailyData);

        foreach ($dailyData as $date => $data) {
            $chartData['labels'][] = date('d/m', strtotime($date));
            $chartData['in_values'][] = $data['in'];
            $chartData['out_values'][] = $data['out'];
        }

        return $chartData;

    } catch (\Exception $e) {
        Log::error("خطأ في تحضير بيانات الرسم البياني: " . $e->getMessage());
        return ['labels' => [], 'in_values' => [], 'out_values' => []];
    }
}
// البحث في المنتجات
public function searchProducts(Request $request)
{
    $term = $request->input('q');
    $page = $request->input('page', 1);
    $perPage = 20;

    $products = Product::select('id', 'name')
        ->where(function($query) use ($term) {
            $query->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('id', 'LIKE', "%{$term}%");
        })
        ->orderBy('name')
        ->paginate($perPage, ['*'], 'page', $page);

    $results = $products->map(function($product) {
        return [
            'id' => $product->id,
            'text' => $product->name . ($product->id ? " ({$product->id})" : '')
        ];
    });

    return response()->json([
        'results' => $results,
        'pagination' => [
            'more' => $products->hasMorePages()
        ]
    ]);
}










private function getMovementTypeLabel($type)
{
    $types = [
        '1' => 'تعديل يدوي',
        '2' => 'فاتورة مبيعات',
        '3' => 'فاتورة شراء',
        '4' => 'إشعار دائن',
        '5' => 'نقل المخزون',
        '6' => 'فاتورة مرتجعة',
        '7' => 'مرتجع مشتريات',
        '8' => 'منتج مجمع',
        '9' => 'إذن مخزن',
        '10' => 'منتج مجمع خارجي',
        '14' => 'إشعار مدين المشتريات',
        '101' => 'إذن مخزني يدوي داخلي',
        '102' => 'إذن مخزني يدوي خارجي',
        '103' => 'إذن مخزني فاتورة',
        '104' => 'إذن مخزني مرتجع مبيعات',
        '105' => 'إذن مخزني إشعار دائن',
        '106' => 'إذن مخزني فاتورة شراء',
        '107' => 'إذن مخزني مرتجع شراء',
        '108' => 'نقل إذن مخزني',
        '109' => 'نقل إذن مخزني داخلي',
        '110' => 'نقل إذن مخزني خارجي',
        '111' => 'إذن مخزني نقطة بيع داخلي',
        '112' => 'إذن مخزني نقطة بيع خارجي',
        '113' => 'جرد المخزون الخارجي',
        '114' => 'جرد المخزون الداخلي',
        '115' => 'إذن مخزني إشعار مدين',
        '116' => 'طلب التصنيع',
        // Add any additional movement types here
    ];

    return $types[$type] ?? 'نوع الحركة غير معروف: ' . $type;
}

private function getSourceTypeLabel($movement)
{
    // If movement is a string, return it directly
    if (is_string($movement)) {
        return $movement;
    }

    // If movement is an object with permission source, return its name
    if (is_object($movement) && isset($movement->permissionSource)) {
        return $movement->permissionSource->name;
    }

    // If movement is an object with type, return the movement type label
    if (is_object($movement) && isset($movement->type)) {
        return $this->getMovementTypeLabel($movement->type);
    }

    // Default fallback
    return 'غير محدد';
}



private function getInventoryMovementData(Request $request)
{
    try {
        // جلب جميع الفلترات من الطلب
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $productId = $request->input('product');
        $categoryId = $request->input('category');
        $sourceType = $request->input('source_type');
        $brand = $request->input('brand');
        $currency = $request->input('currency', 'All');
        $warehouseId = $request->input('warehouse');
        $page = $request->input('page', 1);
        $perPage = 50;

        // بناء الاستعلام لجلب تفاصيل حركة المخزون مع ربط product_details و permissionSource
        $movementsQuery = InvoiceItem::with([
            'product',
            'storeHouse',
            'permissionSource', // إضافة علاقة مصدر الإذن
            // إضافة علاقة product_details
            'product.productDetails' => function($query) use ($warehouseId) {
                if ($warehouseId) {
                    $query->where('store_house_id', $warehouseId);
                } else {
                    // جلب تفاصيل جميع المستودعات
                    $query->with('storeHouse');
                }
            }
        ]);

        // فلترة حسب المنتج
        if ($productId) {
            $movementsQuery->where('product_id', $productId);
        }

        // فلترة حسب التصنيف
        if ($categoryId) {
            $movementsQuery->whereHas('product', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            });
        }

        // فلترة حسب المصدر
        if ($sourceType) {
            $movementsQuery->where('type', $sourceType);
        }

        // فلترة حسب العلامة التجارية
        if ($brand) {
            $movementsQuery->whereHas('product', function ($query) use ($brand) {
                $query->where('brand', $brand);
            });
        }

        // فلترة حسب المستودع
        if ($warehouseId) {
            $movementsQuery->where('store_house_id', $warehouseId);
        }

        // فلترة حسب الفترة الزمنية
        if ($startDate && $endDate) {
            $movementsQuery->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        } elseif ($startDate) {
            $movementsQuery->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        } elseif ($endDate) {
            $movementsQuery->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        // ترتيب حسب التاريخ تنازلياً (الأحدث أولاً) مع ضمان الترتيب الصحيح
        $movementsQuery->orderBy('created_at', 'desc')
                     ->orderBy('id', 'desc')  // إضافة ترتيب إضافي لضمان الترتيب الصحيح
                     ->orderBy('product_id');

        // تطبيق الـ pagination
        $movements = $movementsQuery->paginate($perPage, ['*'], 'page', $page);

        // تجميع البيانات حسب المنتج
        $groupedMovements = [];
        $totalStats = [
            'total_products' => 0,
            'total_in' => 0,
            'total_out' => 0,
            'total_in_value' => 0,  // Initialize total_in_value
            'total_out_value' => 0, // Initialize total_out_value
            'net_movement' => 0,
            'total_stock_value' => 0,
            'total_movements' => 0
        ];

        $productIds = [];

        foreach ($movements->items() as $movement) {
            $productName = $movement->product ? $movement->product->name : 'منتج غير محدد';
            $productIds[] = $movement->product_id;

            if (!isset($groupedMovements[$productName])) {
                $groupedMovements[$productName] = [];
            }

            // Get the current stock from product_details
            $currentStock = $this->getCurrentStockFromProductDetails($movement->product_id, $movement->store_house_id);

            // Calculate stock after this movement
            $stockAfter = $currentStock; // Initialize with current stock

            // If this is not the first movement, get the previous movement's stock
            if (isset($previousMovement) && $previousMovement->product_id === $movement->product_id) {
                $stockAfter = $previousMovement->stock_after + $movement->quantity;
            } else {
                $stockAfter = $currentStock + $movement->quantity;
            }

            // Ensure stock is not negative
            $stockAfter = max(0, $stockAfter);

            // Store current movement's stock after for next iteration
            $movement->stock_after = $stockAfter;
            $previousMovement = $movement;

            // التحقق من وجود نقص في المخزون
            $minimumStock = $movement->product->minimum_stock ?? 0;
            $hasShortage = $stockAfter <= $minimumStock;
            $shortageMessage = $hasShortage ? 'نعم' : 'لا';
            $shortageQuantity = $hasShortage ? max(0, $minimumStock - $stockAfter) : 0;

            // تحديد نوع الحركة والقيم
            $sourceType = $movement->permissionSource ? $movement->permissionSource->name : $this->getSourceTypeLabel($movement->type);

            $movementData = [
                'id' => $movement->id,
                'created_at' => $movement->created_at,
                'type' => $this->getMovementTypeLabel($movement->type),
                'source_type' => $sourceType,
                'permission_source' => $movement->relationLoaded('permissionSource') && $movement->permissionSource ? [
                    'id' => $movement->permissionSource->id ?? null,
                    'name' => $movement->permissionSource->name ?? null,
                    'category' => $movement->permissionSource->category ?? null,
                    'description' => $movement->permissionSource->description ?? null
                ] : null,
                'quantity' => $movement->quantity ?? 0,
                'unit_price' => $movement->unit_price ?? 0,
                'stock_after' => $stockAfter, // المخزون من product_details
                'purchase_price' => $movement->purchase_price ?? 0,
                'total' => $movement->total ?? 0,
                'store_house' => $movement->storeHouse,
                'has_shortage' => $hasShortage,
                'shortage_message' => $shortageMessage,
                'shortage_quantity' => $shortageQuantity,
                'minimum_stock' => $minimumStock
            ];

            $groupedMovements[$productName][] = $movementData;

            // تحديث الإحصائيات
            if ($movement->quantity > 0) {
                $totalStats['total_in'] += $movement->quantity;
                $totalStats['total_in_value'] += $movement->total ?? 0;
            } else {
                $totalStats['total_out'] += abs($movement->quantity);
                $totalStats['total_out_value'] += abs($movement->total ?? 0);
            }

            $totalStats['total_movements']++;
        }

        // حساب عدد المنتجات الفريدة
        $totalStats['total_products'] = count(array_unique($productIds));
        $totalStats['net_movement'] = $totalStats['total_in'] - $totalStats['total_out'];

        // حساب إجمالي قيمة المخزون الحالية من product_details
        $totalStats['total_stock_value'] = $this->calculateTotalStockValue($productIds, $warehouseId);

        $chartData = $this->prepareChartDataMovement($movements->items(), $request);

        return response()->json([
            'success' => true,
            'grouped_movements' => $groupedMovements,
            'totals' => $totalStats,
            'products_count' => $totalStats['total_products'],
            'chart_data' => $chartData,
            'pagination' => [
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
                'per_page' => $movements->perPage(),
                'total' => $movements->total(),
                'from' => $movements->firstItem(),
                'to' => $movements->lastItem()
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('خطأ في تحميل بيانات حركة المخزون: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل البيانات',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * جلب المخزون الحالي من جدول product_details
 */
private function getCurrentStockFromProductDetails($productId, $storeHouseId)
{
    try {
        $productDetail = ProductDetails::where('product_id', $productId)
            ->where('store_house_id', $storeHouseId)
            ->first();

        return $productDetail ? $productDetail->quantity : 0;
    } catch (\Exception $e) {
        Log::error("خطأ في جلب المخزون من product_details: " . $e->getMessage());
        return 0;
    }
}

/**
 * حساب المخزون بعد الحركة
 */
private function calculateStockAfterMovement($movement, $currentStock)
{
    // إذا كانت الحركة قديمة، نحتاج لحساب المخزون في ذلك الوقت
    // هنا يمكنك إضافة منطق أكثر تعقيداً حسب نوع الحركة

    $movementQuantity = $movement->quantity ?? 0;

    // حسب نوع الحركة، قد نحتاج لتعديل الحساب
    switch ($movement->type) {
        case '2': // فاتورة مبيعات (خروج)
        case '6': // فاتورة مرتجعة
            $stockAfter = $currentStock - abs($movementQuantity);
            break;
        case '3': // فاتورة شراء (دخول)
        case '7': // مرتجع مشتريات
            $stockAfter = $currentStock + abs($movementQuantity);
            break;
        case '1': // تعديل يدوي
            $stockAfter = $movementQuantity; // القيمة الجديدة مباشرة
            break;
        case '5': // نقل المخزون
            // هنا نحتاج للتحقق من اتجاه النقل
            $stockAfter = $currentStock + $movementQuantity;
            break;
        default:
            $stockAfter = $currentStock + $movementQuantity;
            break;
    }

    return max(0, $stockAfter); // لا يمكن أن يكون المخزون سالب
}

/**
 * حساب إجمالي قيمة المخزون من product_details
 */
private function calculateTotalStockValue($productIds, $warehouseId = null)
{
    try {
        $query = ProductDetails::join('products', 'product_details.product_id', '=', 'products.id')
            ->selectRaw('SUM(product_details.quantity * products.purchase_price) as total_value');

        if (!empty($productIds)) {
            $query->whereIn('product_details.product_id', $productIds);
        }

        if ($warehouseId) {
            $query->where('product_details.store_house_id', $warehouseId);
        }

        $result = $query->first();
        return $result ? $result->total_value : 0;

    } catch (\Exception $e) {
        Log::error("خطأ في حساب قيمة المخزون: " . $e->getMessage());
        return 0;
    }
}

public function disbursingInventory(Request $request){
return view('reports::inventory.stock_report.disbursing_inventory');
}
}
