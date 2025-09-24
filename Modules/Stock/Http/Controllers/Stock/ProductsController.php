<?php


namespace Modules\Stock\Http\Controllers\Stock;
use App\Http\Controllers\Controller;

use App\Http\Requests\ProductsRequest;
use App\Imports\ProductsImport;
use App\Models\Account;
use App\Models\AccountSetting;
use App\Models\Category;
use App\Models\CompiledProducts;
use App\Models\GeneralSettings;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Log;
use App\Models\PermissionSource;
use App\Models\PriceList;
use App\Models\PriceListItems;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\StoreHouse;
use App\Models\SubUnit;
use App\Models\TemplateUnit;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use Carbon\Carbon;
use Intervention\Image\Laravel\Facades\Image;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductsController extends Controller
{
public function index(Request $request)
{
    // جلب المستخدم الحالي
    $user = auth()->user();

    // بناء الاستعلام الأساسي
    $query = Product::query();

    // تطبيق قيود الفرع
    $this->applyBranchRestrictions($query, $user);

    // جلب المنتجات مع التصفح
    $products = $query->orderBy('id', 'DESC')->paginate(20);

    // إعدادات الحساب والإعدادات العامة
    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
    $generalSettings = GeneralSettings::select()->first();
    $categories = Category::select('id', 'name')->get();

    $role = $generalSettings ? $generalSettings->enable_assembly_and_compound_units == 1 : false;

    return view('stock::products.index', compact('products', 'categories', 'account_setting', 'role'));
}

public function search(Request $request)
{
    // بناء الاستعلام الأساسي
    $query = Product::query();

    // تطبيق قيود الفرع
    $this->applyBranchRestrictions($query, auth()->user());

    // تطبيق فلاتر البحث
    $this->applyFilters($query, $request);

    // جلب النتائج مع التصفح (زيادة عدد المنتجات لكل صفحة)
    $products = $query->orderBy('id', 'DESC')->paginate(24);

    // إعدادات الحساب والإعدادات العامة
    $account_setting = AccountSetting::where('user_id', auth()->user()->id)->first();
    $generalSettings = GeneralSettings::select()->first();
    $categories = Category::select('id', 'name')->get();

    $role = $generalSettings ? $generalSettings->enable_assembly_and_compound_units == 1 : false;

    // إذا كان الطلب AJAX، إرجاع العرض الجزئي فقط
    if ($request->ajax()) {
        return response()->json([
            'html' => view('stock::products.partials.products_list', compact('products'))->render(),
            'pagination' => view('stock::products.partials.pagination', compact('products'))->render(),
            'total' => $products->total(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem()
        ]);
    }

    // إذا لم يكن AJAX، إرجاع الصفحة كاملة
    return view('stock::products.index', compact('products', 'categories', 'account_setting', 'generalSettings', 'role'));
}

/**
 * تطبيق قيود الفرع على الاستعلام
 */
private function applyBranchRestrictions($query, $user)
{
    if ($user->branch) {
        $branch = $user->branch;
        $shareProductsStatus = $branch->settings()->where('key', 'share_products')->first();

        if ($shareProductsStatus && $shareProductsStatus->pivot->status == 0) {
            $query->whereHas('creator', function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            });
        }
    }
}

/**
 * تطبيق فلاتر البحث على الاستعلام
 */
private function applyFilters($query, Request $request)
{
    // البحث بكلمة مفتاحية
    if ($request->filled('keywords')) {
        $keywords = $request->keywords;
        $query->where(function($q) use ($keywords) {
            $q->where('name', 'LIKE', "%{$keywords}%")
              ->orWhere('barcode', 'LIKE', "%{$keywords}%")
              ->orWhere('serial_number', 'LIKE', "%{$keywords}%")
              ->orWhere('id', 'LIKE', "%{$keywords}%");
        });
    }

    // فلتر الماركة
    if ($request->filled('brand')) {
        $query->where('brand', $request->brand);
    }

    // فلتر التصنيف
    if ($request->filled('category')) {
        $query->where('category_id', $request->category);
    }

    // فلتر الباركود
    if ($request->filled('barcode')) {
        $query->where('barcode', 'LIKE', "%{$request->barcode}%");
    }

    // فلتر نوع التتبع
    if ($request->filled('track_inventory')) {
        $query->where('track_inventory', $request->track_inventory);
    }

    // فلتر الحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // فلتر نوع المنتج
    if ($request->filled('product_type')) {
        $productType = $request->product_type;
        switch ($productType) {
            case 'products':
                $query->where('type', 'product');
                break;
            case 'services':
                $query->where('type', 'service');
                break;
            case 'compiled':
                $query->where('type', 'compiled');
                break;
        }
    }

    // فلتر التاريخ
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('created_at', [
            $request->from_date . ' 00:00:00',
            $request->to_date . ' 23:59:59'
        ]);
    } elseif ($request->filled('from_date')) {
        $query->where('created_at', '>=', $request->from_date . ' 00:00:00');
    } elseif ($request->filled('to_date')) {
        $query->where('created_at', '<=', $request->to_date . ' 23:59:59');
    }

    // فلتر كود المنتج
    if ($request->filled('product_code')) {
        $query->where('product_code', 'LIKE', "%{$request->product_code}%");
    }

    // فلتر حالة المخزون
    if ($request->filled('stock_status')) {
        $stockStatus = $request->stock_status;
        switch ($stockStatus) {
            case 'in_stock':
                $query->where('quantity', '>', 0);
                break;
            case 'low_stock':
                $query->where('quantity', '<=', 10)->where('quantity', '>', 0);
                break;
            case 'out_of_stock':
                $query->where('quantity', '<=', 0);
                break;
        }
    }
}    /**
     * تصدير المنتجات
     */

    /**
     * جلب المنتجات مع إحصائيات سريعة
     */
    public function getProductsStats(Request $request)
    {
        $query = Product::query();
        $this->applyFilters($query, $request);

        $stats = [
            'total' => $query->count(),
            'active' => $query->clone()->where('status', 0)->count(),
            'inactive' => $query->clone()->where('status', '!=', 0)->count(),
            'in_stock' => $query->clone()->whereHas('inventory', function($q) {
                $q->where('quantity', '>', 0);
            })->count(),
            'out_of_stock' => $query->clone()->whereDoesntHave('inventory')->orWhereHas('inventory', function($q) {
                $q->where('quantity', '<=', 0);
            })->count(),
        ];

        return response()->json($stats);
    }

    /**
     * تصدير المنتجات إلى Excel
     */
    public function export(Request $request)
    {
        $query = Product::query();
        $this->applyFilters($query, $request);

        $products = $query->with(['user', 'inventory'])->get();

        // هنا يمكنك إضافة منطق التصدير
        // مثال: استخدام Laravel Excel

        return response()->json(['message' => 'تم التصدير بنجاح']);
    }

    /**
     * حذف متعدد للمنتجات
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,id'
        ]);

        try {
            Product::whereIn('id', $request->product_ids)->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتجات المحددة بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف'
            ], 500);
        }
    }

    /**
     * تحديث سريع لحالة المنتج
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:0,1,2,3'
        ]);

        try {
            $product = Product::findOrFail($id);
            $product->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة المنتج بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحديث'
            ], 500);
        }
    }

    /**
     * البحث السريع للمنتجات (للـ autocomplete)
     */
    public function quickSearch(Request $request)
    {
        $term = $request->get('term', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'LIKE', "%{$term}%")
            ->orWhere('barcode', 'LIKE', "%{$term}%")
            ->orWhere('serial_number', 'LIKE', "%{$term}%")
            ->limit(10)
            ->get(['id', 'name', 'barcode', 'serial_number']);

        $results = $products->map(function($product) {
            return [
                'id' => $product->id,
                'label' => $product->name,
                'value' => $product->name,
                'barcode' => $product->barcode,
                'serial' => $product->serial_number
            ];
        });

        return response()->json($results);
    }

    public function create()
    {
        $record_count = DB::table('products')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);
        $SubUnits = collect(); // متغير فارغ للوحدات الفرعية
        $TemplateUnit = TemplateUnit::where('status', 1)->get();
        // التأكد من أن هناك قوالب وحدات متاحة
        if ($TemplateUnit->isNotEmpty()) {
            $firstTemplateUnit = $TemplateUnit->first(); // القالب الأول افتراضيًا
            $SubUnits = SubUnit::where('template_unit_id', $firstTemplateUnit->id)->get();
        }
        $generalSettings = GeneralSettings::select()->first();
        $role = $generalSettings ? $generalSettings->enable_multi_units_system == 1 : false;

        $categories = Category::select('id', 'name')->get();
        $price_lists = PriceList::orderBy('id', 'DESC')->paginate(10);
        return view('stock::products.create', compact('categories', 'price_lists', 'role', 'serial_number', 'TemplateUnit', 'SubUnits'));
    }

    public function getSubUnits(Request $request)
    {
        // إذا لم يتم تحديد أي قالب، جلب الوحدات الفرعية لأول قالب
        if (!$request->has('template_unit_id') || !$request->template_unit_id) {
            $firstTemplateUnit = TemplateUnit::where('status', 1)->first();
            if ($firstTemplateUnit) {
                $subUnits = SubUnit::where('template_unit_id', $firstTemplateUnit->id)->get();
            } else {
                $subUnits = [];
            }
        } else {
            $subUnits = SubUnit::where('template_unit_id', $request->template_unit_id)->get();
        }

        return response()->json($subUnits);
    }

    public function traking()
    {
        $Products = Product::where('track_inventory', '!=', null)->get();

        return view('stock::products.track', compact('Products'));
    }

    public function create_services()
    {
        $record_count = DB::table('products')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);

        $categories = Category::select('id', 'name')->get();
        return view('stock::products.create_services', compact('categories', 'serial_number'));
    }

    public function edit($id)
    {
        $categories = Category::select('id', 'name')->get();
        $product = Product::findOrFail($id);
        $product_details = ProductDetails::where('product_id', $id)->first();
        return view('stock::products.edit', compact('product', 'product_details', 'categories'));
    }

    public function show($id)
{
    $product = Product::findOrFail($id);

    // البيانات الأساسية فقط
    $total_quantity = DB::table('product_details')->where('product_id', $id)->sum('quantity');
    $storeQuantities = ProductDetails::where('product_id', $id)
        ->selectRaw('store_house_id, SUM(quantity) as total_quantity')
        ->groupBy('store_house_id')
        ->with('storeHouse')
        ->get();

    $total_sold = $product->totalSold();
    $sold_last_28_days = $product->totalSoldLast28Days();
    $sold_last_7_days = $product->totalSoldLast7Days();
    $average_cost = $product->averageCost();

    $firstTemplateUnit = optional(TemplateUnit::find($product->sub_unit_id))->base_unit_name;

    if($product->type == "compiled") {
        $CompiledProducts = CompiledProducts::where('compile_id', $id)->get();
    } else {
        $CompiledProducts = collect();
    }

    return view('stock::products.show', compact(
        'product',
        'CompiledProducts',
        'firstTemplateUnit',
        'total_quantity',
        'storeQuantities',
        'total_sold',
        'sold_last_28_days',
        'sold_last_7_days',
        'average_cost'
    ));
}

// دالة جديدة لجلب حركة المخزون
// تحديث دالة getStockMovements في ProductController
public function getStockMovements(Request $request, $id)
{
    $page = $request->get('page', 1);
    $perPage = 50;

    // استلام الفلاتر
    $source_id = $request->get('source_id');
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');

    $query = WarehousePermitsProducts::where('product_id', $id)
        ->with([
            'warehousePermits' => function ($query) {
                $query->with(['storeHouse', 'fromStoreHouse', 'toStoreHouse', 'user', 'permissionSource']);
            },
        ]);

    // تطبيق فلتر المصدر
    if ($source_id) {
        $query->whereHas('warehousePermits', function ($q) use ($source_id) {
            $q->where('permission_type', $source_id);
        });
    }

    // تطبيق فلتر التاريخ من
    if ($date_from) {
        $query->whereHas('warehousePermits', function ($q) use ($date_from) {
            $q->where('permission_date', '>=', $date_from);
        });
    }

    // تطبيق فلتر التاريخ إلى
    if ($date_to) {
        $query->whereHas('warehousePermits', function ($q) use ($date_to) {
            $q->where('permission_date', '<=', $date_to);
        });
    }

    $stock_movements = $query->orderBy('created_at', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

    $product = Product::findOrFail($id);

    // جلب مصادر الأذونات للفلتر
    $permission_sources = \App\Models\PermissionSource::all();

    if ($request->ajax()) {
        return response()->json([
            'html' => view('stock::products.partials.stock_movements',
                compact('stock_movements', 'product', 'permission_sources'))->render(),
            'pagination' => [
                'current_page' => $stock_movements->currentPage(),
                'last_page' => $stock_movements->lastPage(),
                'total' => $stock_movements->total(),
                'has_more' => $stock_movements->hasMorePages()
            ]
        ]);
    }

    return view('stock::products.partials.stock_movements',
        compact('stock_movements', 'product', 'permission_sources'));
}

// دالة جديدة لجلب الجدول الزمني
public function getTimeline(Request $request, $id)
{
    $page = $request->get('page', 1);
    $perPage = 50;

    $stock_movements = WarehousePermitsProducts::where('product_id', $id)
        ->with([
            'warehousePermits' => function ($query) {
                $query->with(['storeHouse', 'user']);
            },
        ])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

    $product = Product::findOrFail($id);
    $average_cost = $product->averageCost();

    if ($request->ajax()) {
        return response()->json([
            'html' => view('stock::products.partials.timeline', compact('stock_movements', 'product', 'average_cost'))->render(),
            'pagination' => [
                'current_page' => $stock_movements->currentPage(),
                'last_page' => $stock_movements->lastPage(),
                'total' => $stock_movements->total(),
                'has_more' => $stock_movements->hasMorePages()
            ]
        ]);
    }

    return view('stock::products.partials.timeline', compact('stock_movements', 'product', 'average_cost'));
}

// دالة جديدة لجلب سجل النشاطات
public function getActivityLogs(Request $request, $id)
{
    $page = $request->get('page', 1);
    $perPage = 50;

    $logs = Log::where('type', 'product')
        ->where('type_id', $id)
        ->with(['user', 'Product'])
        ->orderBy('created_at', 'desc')
        ->paginate($perPage, ['*'], 'page', $page);

    if ($request->ajax()) {
        return response()->json([
            'html' => view('stock::products.partials.activity_logs', compact('logs'))->render(),
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'total' => $logs->total(),
                'has_more' => $logs->hasMorePages()
            ]
        ]);
    }

    return view('stock::products.partials.activity_logs', compact('logs'));
}
    public function categories(Request $request)
    {
        $search = $request->input('search');

        // البحث في قاعدة البيانات
        $categories = Category::where('name', 'like', '%' . $search . '%')->get();

        // تحضير النتائج لتتوافق مع Select2
        $results = [];
        foreach ($categories as $category) {
            $results[] = [
                'id' => $category->id,
                'text' => $category->name,
            ];
        }

        return response()->json(['results' => $results]);
    }

    public function store(ProductsRequest $request)
    {
        try {
            DB::beginTransaction();

            $product = new Product();

            $product->name = $request->name;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->sub_unit_id = $request->sub_unit_id;
            $product->serial_number = $request->serial_number;
            $product->brand = $request->brand;
            $product->supplier_id = $request->supplier_id;
            $product->barcode = $request->barcode;
            $product->track_inventory = $request->track_inventory;
            $product->barcode = $request->barcode;
            $product->inventory_type = $request->inventory_type;
            $product->low_stock_alert = $request->low_stock_alert;
            $product->sales_cost_account = $request->sales_cost_account;
            $product->sale_price = $request->sale_price;
            $product->Internal_notes = $request->Internal_notes;
            $product->tags = $request->tags;
            $product->status = $request->status;
            $product->purchase_price = $request->purchase_price;
            $product->sale_price = $request->sale_price;
            $product->purchase_unit_id = $request->purchase_unit_id;
            $product->sales_unit_id = $request->sales_unit_id;
            $product->tax1 = $request->tax1;
            $product->tax2 = $request->tax2;
            $product->min_sale_price = $request->min_sale_price;
            $product->discount = $request->discount;
            $product->discount_type = $request->discount_type;
            $product->type = $request->type;
            $product->profit_margin = $request->profit_margin;
            $product->expiry_date = $request->expiry_date;
            $product->notify_before_days = $request->notify_before_days;
            $product->created_by = Auth::user()->id;

            if ($request->has('available_online')) {
                $product->available_online = 1;
            }

            if ($request->has('featured_product')) {
                $product->featured_product = 1;
            }

            if ($request->hasFile('images')) {
                $product->images = $this->UploadImage('assets/uploads/product', $request->images);
            }

            if ($request->has('available_online')) {
                $product->available_online = 1;
            }

            if ($request->has('featured_product')) {
                $product->featured_product = 1;
            }

            if ($request->hasFile('images')) {
                $product->images = $this->UploadImage('assets/uploads/product', $request->images);
            } # End If
            $product->save();

            ProductDetails::create([
                'quantity' => 0,
                'product_id' => $product->id,
            ]);

            if ($request->has('price_list_id') && !empty($request->price_list_id)) {
                // إذا تم اختيار price_list_id، قم بحفظ البيانات
                PriceListItems::create([
                    'product_id' => $product->id,
                    'price_list_id' => $request->price_list_id,
                    'sale_price' => $request->price_list,
                ]);
            }

            // تسجيل نشاط جديد
            Log::create([
                'type' => 'product',
                'type_id' => $product->id, // ID النشاط المرتبط
                'type_log' => 'create', // نوع النشاط
                'description' => 'تم اضافة منتج جديد',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

            // تسجيل اشعار نظام جديد
            Log::create([
                'type' => 'product_log',
                'type_id' => $product->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم اضافة منتج جديد **' . $product->name . '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

            DB::commit();

            if ($product->type == 'services') {
                return redirect()
                    ->route('products.index')
                    ->with(['success' => 'تم إضافة الخدمة بنجاح !!']);
            }

            return redirect()
                ->route('products.index')
                ->with(['success' => 'تم إضافة المنتج بنجاح !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with(['error' => 'حدث خطأ أثناء إضافة المنتج: ' . $e->getMessage()]);
        }

        if ($product->type == 'services') {
            return redirect()
                ->route('products.index')
                ->with(['success' => 'تم اضافه الخدمة بنجاج !!']);
        }
        return redirect()
            ->route('products.index')
            ->with(['success' => 'تم اضافه المنتج بنجاج !!']);
    } # End Stor
    // اضافة الخدمة
public function update(ProductsRequest $request, $id)
{
    try {
        DB::beginTransaction();

        $product = Product::findOrFail($id);
        $oldName = $product->name;

        // تحديث الحقول الأساسية
        $product->name = $request->name;
        $product->description = $request->description;
        $product->category_id = $request->category_id;
        $product->serial_number = $request->serial_number;
        $product->brand = $request->brand;
        $product->supplier_id = $request->supplier_id;
        $product->barcode = $request->barcode;
        $product->Internal_notes = $request->Internal_notes;
        $product->tags = $request->tags;
        $product->status = $request->status;
        $product->purchase_price = $request->purchase_price;
        $product->sale_price = $request->sale_price;
        $product->tax1 = $request->tax1;
        $product->tax2 = $request->tax2;
        $product->min_sale_price = $request->min_sale_price;
        $product->discount = $request->discount;
        $product->discount_type = $request->discount_type;
        $product->profit_margin = $request->profit_margin;

        // معالجة الحقول الاختيارية بطريقة آمنة
        if ($request->filled('sub_unit_id')) {
            $product->sub_unit_id = $request->sub_unit_id;
        }

        if ($request->filled('purchase_unit_id')) {
            $product->purchase_unit_id = $request->purchase_unit_id;
        }

        if ($request->filled('sales_unit_id')) {
            $product->sales_unit_id = $request->sales_unit_id;
        }

        if ($request->filled('sales_account')) {
            $product->sales_account = $request->sales_account;
        }

        if ($request->filled('sales_cost_account')) {
            $product->sales_cost_account = $request->sales_cost_account;
        }

        if ($request->filled('track_inventory')) {
            $product->track_inventory = $request->track_inventory;
        }

        if ($request->filled('inventory_type')) {
            $product->inventory_type = $request->inventory_type;
        }

        if ($request->filled('low_stock_alert')) {
            $product->low_stock_alert = $request->low_stock_alert;
        }

        if ($request->filled('expiry_date')) {
            $product->expiry_date = $request->expiry_date;
        }

        if ($request->filled('notify_before_days')) {
            $product->notify_before_days = $request->notify_before_days;
        }

        // معالجة نوع المنتج (إذا كان موجود في الطلب)
        if ($request->filled('type')) {
            $product->type = $request->type;
        }

        // إعادة تعيين القيم المنطقية
        $product->available_online = $request->has('available_online') ? 1 : 0;
        $product->featured_product = $request->has('featured_product') ? 1 : 0;

        // معالجة رفع الصور
        if ($request->hasFile('images')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if (!empty($product->images) && file_exists(public_path($product->images))) {
                unlink(public_path($product->images));
            }
            $product->images = $this->UploadImage('assets/uploads/product', $request->images);
        }

        // حفظ التحديثات
        $product->save();

        // معالجة قائمة الأسعار (إذا كانت موجودة)
        if ($request->filled('price_list_id') && $request->filled('price_list')) {
            $priceListItem = PriceListItems::where('product_id', $product->id)
                                         ->where('price_list_id', $request->price_list_id)
                                         ->first();

            if ($priceListItem) {
                $priceListItem->update([
                    'sale_price' => $request->price_list,
                ]);
            } else {
                PriceListItems::create([
                    'product_id' => $product->id,
                    'price_list_id' => $request->price_list_id,
                    'sale_price' => $request->price_list,
                ]);
            }
        }

        // تسجيل نشاط التعديل
        Log::create([
            'type' => 'product',
            'type_id' => $product->id,
            'type_log' => 'edit',
            'description' => 'تم تعديل المنتج',
            'old_value' => $oldName,
            'created_by' => auth()->id(),
        ]);

        // تسجيل إشعار نظام للتعديل
        Log::create([
            'type' => 'product_log',
            'type_id' => $product->id,
            'type_log' => 'log',
            'description' => 'تم تعديل المنتج من **' . $oldName . '** إلى **' . $product->name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        // إرجاع استجابة JSON للـ Ajax
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => ($product->type == 'services') ? 'تم تحديث الخدمة بنجاح !!' : 'تم تحديث المنتج بنجاح !!',
                'redirect' => route('products.index')
            ]);
        }

        // الاستجابة العادية
        $successMessage = ($product->type == 'services') ? 'تم تحديث الخدمة بنجاح !!' : 'تم تحديث المنتج بنجاح !!';

        return redirect()
            ->route('products.index')
            ->with(['success' => $successMessage]);

    } catch (\Exception $ex) {
        DB::rollback();

        // تسجيل الخطأ في اللوج للتشخيص
        \Log::error('Product Update Error: ' . $ex->getMessage(), [
            'product_id' => $id,
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'trace' => $ex->getTraceAsString()
        ]);

        // إرجاع استجابة JSON للـ Ajax في حالة الخطأ
        if ($request->ajax()) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث المنتج',
                'error_details' => config('app.debug') ? $ex->getMessage() : null,
                'errors' => []
            ], 422);
        }

        // الاستجابة العادية للطلبات غير Ajax
        return redirect()
            ->back()
            ->with(['error' => 'حدث خطأ أثناء تحديث المنتج. يرجى المحاولة مرة أخرى.'])
            ->withInput();
    }
}

// دالة مساعدة لتشخيص المشاكل - يمكن إضافتها مؤقتاً
public function debugUpdate(Request $request, $id)
{
    try {
        $product = Product::findOrFail($id);

        return response()->json([
            'product_data' => $product->toArray(),
            'request_data' => $request->all(),
            'filled_fields' => array_filter($request->all(), function($value) {
                return !is_null($value) && $value !== '';
            })
        ]);

    } catch (\Exception $ex) {
        return response()->json([
            'error' => $ex->getMessage(),
            'trace' => $ex->getTraceAsString()
        ]);
    }
}
    public function compiled()
    {
        $record_count = DB::table('products')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);
        $SubUnits = collect(); // متغير فارغ للوحدات الفرعية
        $TemplateUnit = TemplateUnit::where('status', 1)->get();
        // التأكد من أن هناك قوالب وحدات متاحة
        if ($TemplateUnit->isNotEmpty()) {
            $firstTemplateUnit = $TemplateUnit->first(); // القالب الأول افتراضيًا
            $SubUnits = SubUnit::where('template_unit_id', $firstTemplateUnit->id)->get();
        }
        $generalSettings = GeneralSettings::select()->first();
        $role = $generalSettings ? $generalSettings->enable_multi_units_system == 1 : false;
        $storehouses = StoreHouse::orderBy('id', 'DESC')->get();
        $products = Product::where('type', 'products')->get();
        $categories = Category::select('id', 'name')->get();
        return view('stock::products.compiled', compact('categories', 'storehouses', 'products', 'role', 'serial_number', 'TemplateUnit', 'SubUnits'));
    }
    public function compiled_store(Request $request)
    {
        try {
            DB::beginTransaction();

            $product = new Product();

            $product->name = $request->name;
            $product->description = $request->description;
            $product->category_id = $request->category_id;
            $product->sub_unit_id = $request->sub_unit_id;
            $product->serial_number = $request->serial_number;
            $product->brand = $request->brand;
            $product->supplier_id = $request->supplier_id;
            $product->barcode = $request->barcode;
            $product->track_inventory = $request->track_inventory;
            $product->inventory_type = $request->inventory_type;
            $product->low_stock_alert = $request->low_stock_alert;
            $product->sales_cost_account = $request->sales_cost_account;
            $product->sale_price = $request->sale_price;
            $product->Internal_notes = $request->Internal_notes;
            $product->tags = $request->tags;
            $product->status = $request->status;
            $product->purchase_price = $request->purchase_price;
            $product->purchase_unit_id = $request->purchase_unit_id;
            $product->sales_unit_id = $request->sales_unit_id;
            $product->tax1 = $request->tax1;
            $product->tax2 = $request->tax2;
            $product->min_sale_price = $request->min_sale_price;
            $product->discount = $request->discount;
            $product->discount_type = $request->discount_type;
            $product->type = $request->type;
            $product->profit_margin = $request->profit_margin;
            $product->storehouse_id = $request->storehouse_id; // مخزن المنتجات الاوليه للمنتج التجميعي
            $product->compile_type = 'Instant'; // نوع التجميعه معد مسبقا او فوري
            $product->created_by = Auth::user()->id;

            if ($request->has('available_online')) {
                $product->available_online = 1;
            }

            if ($request->has('featured_product')) {
                $product->featured_product = 1;
            }

            if ($request->hasFile('images')) {
                $product->images = $this->UploadImage('assets/uploads/product', $request->images);
            }

            $product->save();

            // تحقق من صحة البيانات
            $request->validate([
                'products' => 'required|array', // تأكد من وجود بيانات المنتجات
                'products.*.product_id' => 'required|exists:products,id', // تأكد من وجود product_id في جدول products
                'products.*.quantity' => 'required|numeric|min:1', // تأكد من أن الكمية رقم صحيح أكبر من 0
            ]);

            foreach ($request->products as $productData) {
                $compiledProduct = new CompiledProducts();

                // تعيين compile_id إلى المنتج التجميعي
                $compiledProduct->compile_id = $product->id; // هذا هو المنتج التجميعي ويجب أن يتكرر لجميع المنتجات المرتبطة به

                // تعيين product_id إلى المنتج الفرعي
                $compiledProduct->product_id = $productData['product_id']; // معرّف المنتج الفردي

                // تعيين الكمية
                $compiledProduct->qyt = $productData['quantity']; // الكمية الخاصة بالمنتج

                // حفظ البيانات في جدول CompiledProducts
                $compiledProduct->save();
            }

            $quantity = $request->quantity;

            // إنشاء سجل جديد في `ProductDetails`
            ProductDetails::create([
                'quantity' => $quantity,
                'product_id' => $product->id,
            ]);

            DB::commit();

            if ($product->type == 'services') {
                return redirect()
                    ->route('products.index')
                    ->with(['success' => 'تم إضافة الخدمة بنجاح !!']);
            }

            return redirect()
                ->route('products.index')
                ->with(['success' => 'تم إضافة المنتج بنجاح !!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with(['error' => 'حدث خطأ أثناء إضافة المنتج: ' . $e->getMessage()]);
        }
    }
    public function delete($id)
    {
        $product = Product::findOrFail($id);

        // تسجيل اشعار نظام جديد
        Log::create([
            'type' => 'product_log',
            'type_id' => $product->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف المنتج **' . $product->name . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        ProductDetails::where('product_id', $id)->delete();
        Product::findOrFail($id)->delete();

        return redirect()
            ->route('products.index')
            ->with(['error' => 'تم حذف المنتج بنجاح المنتج بنجاج !!']);
    }

    public function manual_stock_adjust($id)
    {
        $product = Product::findOrFail($id);
        $storehouses = StoreHouse::select(['name', 'id'])->get();
        $generalSettings = GeneralSettings::select()->first();
        $role = $generalSettings ? $generalSettings->enable_multi_units_system == 1 : false;

        $SubUnits = $product->sub_unit_id ? SubUnit::where('template_unit_id', $product->sub_unit_id)->get() : collect();

        return view('stock::products.manual_stock_adjust', compact('product', 'role', 'storehouses', 'SubUnits'));
    }

public function add_manual_stock_adjust(Request $request, $id)
{
    $request->validate([
        'quantity' => 'required|numeric|min:1',
        'type' => 'required|in:1,2', // 1 = إضافة ، 2 = سحب
        'unit_price' => 'required|numeric',
        'date' => 'nullable|date',
        'time' => 'nullable|date_format:H:i',
        'attachments' => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
        'store_house_id' => 'required|exists:store_houses,id',
    ]);

    try {
        DB::beginTransaction();

        // جلب المنتج
        $product = ProductDetails::where('product_id', $id)->firstOrFail();

        if ($request->type == 2) {
            $product = ProductDetails::where('product_id', $id)
                ->where('store_house_id', $request->store_house_id)
                ->first();

            if (!$product) {
                return redirect()
                    ->back()
                    ->with(['error' => 'المنتج غير موجود في المخزن المحدد.'])
                    ->withInput();
            }
        }

        $old_quantity_in_stock = $product->quantity;

        // التحقق من توفر الكمية قبل السحب
        if ($request->type == 2 && $old_quantity_in_stock < $request->quantity) {
            return redirect()
                ->route('products.manual_stock_adjust', $id)
                ->withInput()
                ->with(['error' => 'الكميه غير متوفره في المخزن المحدد.']);
        }

        // تحديث المنتج + إنشاء إذن
        $this->updateProductAndCreatePermit($product, $request, $request->type, $id);

        // حساب المبلغ
        $amount = $request->quantity * $request->unit_price;

        // ✅ التأكد من حساب المستودع
        $storeAccount = Account::where('storehouse_id', $request->store_house_id)->first();
        if (!$storeAccount) {
            $storeHouse = StoreHouse::find($request->store_house_id);
            $storeAccount = Account::create([
                'name' => 'حساب المستودع - ' . ($storeHouse->name ?? 'مستودع غير معروف'),
                'storehouse_id' => $request->store_house_id,
                'account_type' => 'storehouse',
                'balance' => 0,
                'status' => 1,
            ]);
        }

        // إنشاء قيد اليومية
        $journalEntry = JournalEntry::create([
            'reference_number' => 'MANUAL_' . now()->timestamp,
            'warehouse_permit_id' => $request->warehouse_permit_id ?? null,
            'date' => $request->date ?? now(),
            'description' => ($request->type == 1 ? "إضافة" : "سحب") .
                " {$request->quantity} من المنتج {$product->product->name} - العملية #{$id}",
            'status' => 1,
            'currency' => 'SAR',
            'created_by_employee' => Auth::id(),
            'reference_type' => 'manual_stock_adjust',
            'reference_id' => $id,
            'amount' => $amount
        ]);

        // 1️⃣ تفصيل القيد - حساب المستودع
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $storeAccount->id,
            'description' => $journalEntry->description,
            'debit' => $request->type == 1 ? $amount : 0,
            'credit' => $request->type == 2 ? $amount : 0,
        ]);

        // 2️⃣ تفصيل القيد - أطراف دائنة أخرى
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => 33, // حساب "أطراف دائنة أخرى"
            'description' => $journalEntry->description,
            'debit' => $request->type == 2 ? $amount : 0,
            'credit' => $request->type == 1 ? $amount : 0,
        ]);

        // تحديث أرصدة المستودع
        if ($request->type == 1) {
            $storeAccount->balance += $amount; // إضافة
        } else {
            $storeAccount->balance -= $amount; // سحب
        }
        $storeAccount->save();

        DB::commit();

        $message = $request->type == 2
            ? 'تم سحب الكميه من المخزون بنجاح'
            : 'تم اضافه الكميه الي المخزون بنجاح';

        return redirect()
            ->route('products.show', $id)
            ->withInput()
            ->with(['success' => $message]);

    } catch (\Exception $ex) {
        DB::rollback();
        return redirect()
            ->back()
            ->with(['error' => $ex->getMessage()])
            ->withInput();
    }
}

private function updateProductAndCreatePermit($product, $request, $type, $id)
{
    if ($request->hasFile('attachments')) {
        $product->attachments = $this->UploadImage('assets/uploads/product/attachments', $request->attachments);
    }

    // التحقق من المخزون قبل التحديث
    if ($type == 2 && $product->quantity < $request->quantity) {
        return redirect()
            ->back()
            ->with(['error' => 'الكميه غير متوفره في المخزن المحدد.'])
            ->withInput();
    }

    // تحديث الكمية بناءً على العملية (إضافة أو سحب)
    $SubUnit = SubUnit::find($request->sub_unit_id);

    if ($SubUnit) {
        $conversionFactor = $SubUnit->conversion_factor;
    } else {
        $conversionFactor = 1; // قيمة افتراضية في حالة عدم وجود الوحدة
    }

    $product->quantity = $type == 2 ? $product->quantity - $request->quantity * $conversionFactor : $product->quantity + $request->quantity * $conversionFactor;
    $product->type = $request->type;
    $product->unit_price = $request->unit_price;
    $product->date = $request->date;
    $product->time = $request->time;
    $product->type_of_operation = $request->type_of_operation;
    $product->comments = $request->comments;
    $product->subaccount = $request->subaccount;
    $product->duration = $request->duration;
    $product->status = $request->status;
    $product->store_house_id = $request->store_house_id;
    $product->product_id = $id;

    $product->update();

    // تحديد نوع إذن المخزن (إضافة أو سحب)
    $permissionSourceName = $type == 2 ? 'اذن سحب مخزني' : 'اذن اضافة مخزن';
    $permissionSource = PermissionSource::where('name', $permissionSourceName)->first();

    if (!$permissionSource) {
        throw new \Exception("مصدر إذن '{$permissionSourceName}' غير موجود في قاعدة البيانات.");
    }

    $wareHousePermits = new WarehousePermits();
    $wareHousePermits->store_houses_id = $request->store_house_id;
    $wareHousePermits->permission_source_id = $permissionSource->id;
    $record_count = DB::table('warehouse_permits')->count();
    $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);
    $wareHousePermits->number = $serial_number;
    $wareHousePermits->grand_total = $request->quantity * $request->unit_price;
    $wareHousePermits->created_by = auth()->user()->id;
    $wareHousePermits->sub_account = $request->subaccount;
    $wareHousePermits->details = $request->comments;
    $wareHousePermits->permission_date = $request->has('date') && $request->has('time') ? Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time) : Carbon::now();
    $wareHousePermits->save();

    // حفظ تفاصيل المنتج في إذن المخزن
    $warehousePermitProduct = WarehousePermitsProducts::create([
        'quantity' => $request->quantity,
        'total' => $request->quantity * $request->unit_price,
        'unit_price' => $request->unit_price,
        'product_id' => $id,
        'warehouse_permits_id' => $wareHousePermits->id,
        'stock_before' => $product->quantity, // المخزون قبل التحديث
        'stock_after' => $type == 2 ? $product->quantity - $request->quantity * $conversionFactor : $product->quantity + $request->quantity * $conversionFactor, // المخزون بعد التحديث
    ]);

    // إذا كان المنتج تجميعيًا
    if ($product->type == 'compiled' && $product->compile_type !== 'Instant') {
        // ** الحصول على المنتجات التابعة للمنتج التجميعي **
        $CompiledProducts = CompiledProducts::where('compile_id', $id)->get();

        foreach ($CompiledProducts as $compiledProduct) {
            // ** حساب المخزون قبل وبعد التعديل للمنتج التابع **
            $total_quantity = DB::table('product_details')->where('product_id', $compiledProduct->product_id)->sum('quantity');
            $stock_before = $total_quantity;
            $stock_after = $stock_before - $compiledProduct->qyt * $request->quantity; // خصم الكمية المطلوبة

            // ** تسجيل المبيعات في حركة المخزون للمنتج التابع **
            $wareHousePermits = new WarehousePermits();
            $wareHousePermits->permission_source_id = $permissionSource->id; // استخدام نفس نوع الإذن (سحب أو إضافة)
            $wareHousePermits->permission_date = $request->has('date') && $request->has('time') ? Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time) : Carbon::now();
            $wareHousePermits->number = $product->id;
            $wareHousePermits->grand_total = $request->quantity * $request->unit_price;
            $wareHousePermits->store_houses_id = $request->store_house_id;
            $wareHousePermits->created_by = auth()->user()->id;
            $wareHousePermits->save();

            // ** تسجيل البيانات في WarehousePermitsProducts للمنتج التابع **
            WarehousePermitsProducts::create([
                'quantity' => $compiledProduct->qyt * $request->quantity,
                'total' => $request->quantity * $request->unit_price,
                'unit_price' => $request->unit_price,
                'product_id' => $compiledProduct->product_id,
                'stock_before' => $stock_before, // المخزون قبل التحديث
                'stock_after' => $stock_after, // المخزون بعد التحديث
                'warehouse_permits_id' => $wareHousePermits->id,
            ]);

            // ** تحديث المخزون للمنتج التابع **
            $compiledProductDetails = ProductDetails::where('store_house_id', $request->store_house_id)->where('product_id', $compiledProduct->product_id)->first();

            if (!$compiledProductDetails) {
                $compiledProductDetails = ProductDetails::create([
                    'store_house_id' => $request->store_house_id,
                    'product_id' => $compiledProduct->product_id,
                    'quantity' => 0,
                ]);
            }

            $compiledProductDetails->decrement('quantity', $compiledProduct->qyt * $request->quantity);
        }
    }

    // تسجيل الإشعار في جدول logs
    $product = Product::findOrFail($id);
    $movement = $warehousePermitProduct; // استخدام البيانات المسجلة حديثًا
    $average_cost = $product->averageCost();

    $description = '';

    if ($type == 2) {
        $description = sprintf('أنقص **%s** **%d** وحدة من مخزون **[#%s (%s)](%s)** يدويا (رقم العملية: **#%s**)، وسعر الوحدة: **%s ر.س**، وأصبح المخزون الباقي من المنتج: **%d** وأصبح المخزون **%s** رصيده **%d**, متوسط السعر: **%s ر.س**', $movement->warehousePermits->user->name, $movement->quantity, $product->serial_number, $product->name, route('products.show', $id), $movement->warehousePermits->number, $movement->unit_price, $movement->stock_after, $movement->warehousePermits->storeHouse->name, $movement->stock_after, $average_cost);
    } else {
        $description = sprintf('أضاف **%s** **%d** وحدة إلى مخزون **[#%s (%s)](%s)** يدويا (رقم العملية: **#%s**)، وسعر الوحدة: **%s ر.س**، وأصبح المخزون الباقي من المنتج: **%d** وأصبح المخزون **%s** رصيده **%d**, متوسط السعر: **%s ر.س**', $movement->warehousePermits->user->name, $movement->quantity, $product->serial_number, $product->name, route('products.show', $id), $movement->warehousePermits->number, $movement->unit_price, $product->quantity, $movement->warehousePermits->storeHouse->name, $product->quantity, $average_cost);
    }

    Log::create([
        'type' => 'product_log',
        'type_id' => $id, // ID النشاط المرتبط
        'type_log' => 'log', // نوع النشاط
        'description' => $description, // النص المنسق
        'created_by' => auth()->id(), // ID المستخدم الحالي
    ]);
}
    # Helper Function
    public function GenerateImage($image, $imageName)
    {
        $destinationsPath = public_path('assets/uploads');
        $img = Image::read($image->path());
        $img->cover(124, 124, 'top');
        $img->resize(124, 124, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationsPath . '/' . $imageName);
    }
    function uploadImage($folder, $image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time() . rand(1, 99) . '.' . $fileExtension;
        $image->move($folder, $fileName);

        return $fileName;
    } //end of uploadImage
    public function GenerateProductImage($image, $imageName)
    {
        $destinationsPath = public_path('assets/uploads/product');
        $img = Image::read($image->path());

        $img->cover(540, 689, 'top');
        $img->resize(540, 689, function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationsPath . '/' . $imageName);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt',
        ]);

        Excel::import(new ProductsImport(), $request->file('file'));

        return redirect()->back()->with('success', 'تم استيراد المنتجات بنجاح!');
    }
}
