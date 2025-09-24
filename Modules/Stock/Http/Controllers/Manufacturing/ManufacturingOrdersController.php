<?php

namespace Modules\Stock\Http\Controllers\Manufacturing;
use App\Http\Controllers\Controller;
use App\Http\Requests\ManufacturOrderRequest;
use App\Models\Account;
use App\Models\Client;
use App\Models\ClientRelation;
use App\Models\Employee;
use App\Models\ManufacturOrders;
use App\Models\ManufacturOrdersItem;
use App\Models\Product;
use App\Models\ProductionMaterials;
use App\Models\ProductionPath;
use App\Models\Log as ModelsLog;
use App\Models\notifications;
use App\Models\ProductDetails;
use App\Models\ProductionStage;
use App\Models\StoreHouse;
use App\Models\WarehousePermits;
use App\Models\WarehousePermitsProducts;
use App\Models\WorkStations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManufacturingOrdersController extends Controller
{
    public function index()
    {
        // جلب البيانات الأساسية للفلاتر
        $products = Product::select('id', 'name')->get();
        $clients = Client::select('id', 'trade_name', 'code')->get();
        $materialLists = ProductionMaterials::select('id', 'name')->get();
        $productionStages = ProductionStage::select('id', 'stage_name')->get();

        return view('stock::manufacturing.orders.index', compact(
            'products',
            'clients',
            'materialLists',
            'productionStages'
        ));
    }

    public function getData(Request $request)
    {
        try {
            $query = ManufacturOrders::with(['product', 'client']);

            // تطبيق الفلاتر
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('code', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('material_list_id')) {
                $query->where('material_list_id', $request->material_list_id);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('client_id')) {
                $query->where('client_id', $request->client_id);
            }

            if ($request->filled('production_stage_id')) {
                $query->where('production_stage_id', $request->production_stage_id);
            }

            // ترتيب النتائج
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // التقسيم
            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage);

            // تحضير البيانات للإرسال
            $data = [
                'orders' => $orders->items(),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ],
                'links' => $orders->links()->render()
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
                'message' => 'تم جلب البيانات بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب البيانات: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getFilterOptions()
    {
        try {
            $data = [
                'products' => Product::select('id', 'name')->get(),
                'clients' => Client::select('id', 'trade_name', 'code')->get(),
                'material_lists' => ProductionMaterials::select('id', 'name')->get(),
                'production_stages' => ProductionStage::select('id', 'stage_name')->get(),
                'statuses' => [
                    ['value' => 'active', 'label' => 'نشط'],
                    ['value' => 'in_progress', 'label' => 'قيد التنفيذ'],
                    ['value' => 'completed', 'label' => 'منتهي'],
                    ['value' => 'cancelled', 'label' => 'ملغى']
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب خيارات الفلترة'
            ], 500);
        }
    }


    public function create(){
        $record_count = DB::table('manufactur_orders')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);
        $accounts = Account::select('id', 'name')->get();
        $products = Product::select()->get();
        $employees = Employee::select('id', 'first_name','middle_name')->get();
        $clients = Client::select('id','trade_name')->get();
        $paths = ProductionPath::select('id', 'name')->get();
        $production_materials = ProductionMaterials::select()->get();
        $stages = ProductionStage::select('id', 'stage_name')->get();
        $workstations = WorkStations::select('id', 'name')->get();

        return view('stock::manufacturing.orders.create', compact('serial_number', 'accounts', 'products', 'employees', 'clients', 'paths', 'production_materials', 'stages', 'workstations'));
    }

    public function store(ManufacturOrderRequest $request)
    {
        DB::beginTransaction();
        try {

            $order = ManufacturOrders::create([
                'name' => $request->name,
                'code' => $request->code,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'account_id' => $request->account_id,
                'employee_id' => $request->employee_id,
                'client_id' => $request->client_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'production_material_id' => $request->production_material_id,
                'production_path_id' => $request->production_path_id,
                'last_total_cost' => $request->last_total_cost ?? 0,
                'created_by' => auth()->id(),
            ]);

            if ($request->has('raw_product_id')) {
                foreach ($request->raw_product_id as $index => $rawProductId) {
                    ManufacturOrdersItem::create([
                        'manufactur_order_id' => $order->id,
                        'raw_product_id' => $rawProductId,
                        'raw_production_stage_id' => $request->raw_production_stage_id[$index],
                        'raw_unit_price' => $request->raw_unit_price[$index],
                        'raw_quantity' => $request->raw_quantity[$index],
                        'raw_total' => $request->raw_total[$index],

                        'expenses_account_id' => $request->expenses_account_id[$index] ?? null,
                        'expenses_cost_type' => $request->expenses_cost_type[$index] ?? null,
                        'expenses_production_stage_id' => $request->expenses_production_stage_id[$index] ?? null,
                        'expenses_price' => $request->expenses_price[$index] ?? null,
                        'expenses_description' => $request->expenses_description[$index] ?? null,
                        'expenses_total' => $request->expenses_total[$index] ?? null,

                        'workstation_id' => $request->workstation_id[$index] ?? null,
                        'operating_time' => $request->operating_time[$index] ?? null,
                        'manu_production_stage_id' => $request->manu_production_stage_id[$index] ?? null,
                        'manu_cost_type' => $request->manu_cost_type[$index] ?? null,
                        'manu_total_cost' => $request->manu_total_cost[$index] ?? null,
                        'manu_description' => $request->manu_description[$index] ?? null,
                        'manu_total' => $request->manu_total[$index] ?? null,

                        'end_life_product_id' => $request->end_life_product_id[$index] ?? null,
                        'end_life_unit_price' => $request->end_life_unit_price[$index] ?? null,
                        'end_life_production_stage_id' => $request->end_life_production_stage_id[$index] ?? null,
                        'end_life_quantity' => $request->end_life_quantity[$index] ?? null,
                        'end_life_total' => $request->end_life_total[$index] ?? null,
                    ]);
                }
            }

              // تسجيل اشعار نظام جديد
            ModelsLog::create([
                'type' => 'manufacturing_order',
                'type_id' => $order->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم اضافة  امر تصنيع **' . $order->name . '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

            // تأكيد حفظ البيانات
            DB::commit();

            return redirect()->route('manufacturing.orders.index')->with(['success'=>'تم حفظ البيانات بنجاح.']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Manufacturing Order Creation Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with(['error' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()]);
        }
    }

    public function edit($id){
        $order = ManufacturOrders::find($id);
        $accounts = Account::select('id', 'name')->get();
        $products = Product::select()->get();
        $employees = Employee::select('id', 'first_name','middle_name')->get();
        $clients = Client::select('id','trade_name')->get();
        $paths = ProductionPath::select('id', 'name')->get();
        $production_materials = ProductionMaterials::select()->get();
        $stages = ProductionStage::select('id', 'stage_name')->get();
        $workstations = WorkStations::select('id', 'name')->get();
        return view('stock::manufacturing.orders.edit', compact('order', 'accounts', 'products', 'employees', 'clients', 'paths', 'production_materials', 'stages', 'workstations'));
    }

    public function update(ManufacturOrderRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $order = ManufacturOrders::findOrFail($id);

            $order->update([
                'name' => $request->name,
                'code' => $request->code,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'account_id' => $request->account_id,
                'employee_id' => $request->employee_id,
                'client_id' => $request->client_id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'production_material_id' => $request->production_material_id,
                'production_path_id' => $request->production_path_id,
                'last_total_cost' => $request->last_total_cost ?? 0,
                'updated_by' => auth()->id(),
            ]);

            $order->manufacturOrdersItem()->delete();

            if ($request->has('raw_product_id')) {
                foreach ($request->raw_product_id as $index => $rawProductId) {
                    ManufacturOrdersItem::create([
                        'manufactur_order_id' => $order->id,
                        'raw_product_id' => $rawProductId,
                        'raw_production_stage_id' => $request->raw_production_stage_id[$index],
                        'raw_unit_price' => $request->raw_unit_price[$index],
                        'raw_quantity' => $request->raw_quantity[$index],
                        'raw_total' => $request->raw_total[$index],

                        'expenses_account_id' => $request->expenses_account_id[$index] ?? null,
                        'expenses_cost_type' => $request->expenses_cost_type[$index] ?? null,
                        'expenses_production_stage_id' => $request->expenses_production_stage_id[$index] ?? null,
                        'expenses_price' => $request->expenses_price[$index] ?? null,
                        'expenses_description' => $request->expenses_description[$index] ?? null,
                        'expenses_total' => $request->expenses_total[$index] ?? null,

                        'workstation_id' => $request->workstation_id[$index] ?? null,
                        'operating_time' => $request->operating_time[$index] ?? null,
                        'manu_production_stage_id' => $request->manu_production_stage_id[$index] ?? null,
                        'manu_cost_type' => $request->manu_cost_type[$index] ?? null,
                        'manu_total_cost' => $request->manu_total_cost[$index] ?? null,
                        'manu_description' => $request->manu_description[$index] ?? null,
                        'manu_total' => $request->manu_total[$index] ?? null,

                        'end_life_product_id' => $request->end_life_product_id[$index] ?? null,
                        'end_life_unit_price' => $request->end_life_unit_price[$index] ?? null,
                        'end_life_production_stage_id' => $request->end_life_production_stage_id[$index] ?? null,
                        'end_life_quantity' => $request->end_life_quantity[$index] ?? null,
                        'end_life_total' => $request->end_life_total[$index] ?? null,
                    ]);
                }
            }
               ModelsLog::create([
                'type' => 'manufacturing_order',
                'type_id' => $order->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم تحديث  امر تصنيع **' . $order->name . '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);


            DB::commit();

            return redirect()->route('manufacturing.orders.index')->with(['success' => 'تم تحديث البيانات بنجاح.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('manufacturing.orders.index')->with(['error' => 'حدث خطأ أثناء تحديث البيانات.']);
        }
    }

    public function show($id)
    {
        $order = ManufacturOrders::findOrFail($id);
$storehouse = StoreHouse::all();
  $logs = ModelsLog::where('type', 'manufacturing_order')
            ->where('type_id', $id)
            ->whereHas('manufacturing_order') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('stock::manufacturing.orders.show', compact('order','logs','storehouse'));
    }
    // أضف هذا Method إلى ManufacturingOrdersController
public function finish($id)
{
    $order = ManufacturOrders::findOrFail($id);

    // التحقق من إمكانية إنهاء الأمر
    if (!$order->canBeCompleted()) {
        return redirect()->back()->with(['error' => 'لا يمكن إنهاء هذا الأمر في الوضع الحالي']);
    }

    $warehouses = StoreHouse::all(); // أو حسب الشروط المطلوبة

    return view('manufacturing.orders.finish', compact('order', 'warehouses'));
}

// الحالة الأولى: إذا كنت تريد حذف إذن مخزني موجود مسبقاً عند الإنهاء
// (مثل حذف إذن مواد خام لتحويله لإذن منتج نهائي)


// ===============================================================

// الحالة الثانية: إذا كنت تريد حذف جميع الإذونات المرتبطة بالأمر عند الإنهاء
// (لإنهاء جميع العمليات المخزنية المؤقتة)







// دالة لإضافة سجل في product_details للمنتج المُصنع
private function addProductDetailsForManufacturedProduct(ManufacturOrders $order, Request $request, $actualQuantity, $unitPrice, $totalCost)
{
    // إضافة سجل المنتج الرئيسي
    ProductDetails::create([
        'product_id' => $order->product_id,
        'store_house_id' => $request->main_warehouse_id,
        'quantity' => $actualQuantity,
        'unit_price' => $unitPrice,
        'date' => $request->delivery_date,
        'time' => now()->format('H:i:s'),
        'type_of_operation' => 'إنتاج', // أو 'manufacturing'
        'type' => 'in', // وارد
        'comments' => 'منتج من أمر التصنيع: ' . $order->name . ($request->notes ? ' - ' . $request->notes : ''),
        'subaccount' => 'manufacturing_order_' . $order->id, // ربط بأمر التصنيع
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // إضافة المواد الهالكة إذا كانت موجودة
    $this->addWasteMaterialsToProductDetails($order, $request);
}

// دالة لإضافة المواد الهالكة في product_details
private function addWasteMaterialsToProductDetails(ManufacturOrders $order, Request $request)
{
    $wasteMaterials = $order->manufacturOrdersItem()
        ->whereNotNull('end_life_product_id')
        ->where('end_life_quantity', '>', 0)
        ->get();

    foreach ($wasteMaterials as $wasteMaterial) {
        if ($wasteMaterial->endLifeProduct) {
            ProductDetails::create([
                'product_id' => $wasteMaterial->end_life_product_id,
                'store_house_id' => $request->waste_warehouse_id,
                'quantity' => $wasteMaterial->end_life_quantity,
                'unit_price' => $wasteMaterial->end_life_unit_price ?? 0,
                'date' => $request->delivery_date,
                'time' => now()->format('H:i:s'),
                'type_of_operation' => 'مواد هالكة من التصنيع',
                'type' => 'in', // وارد
                'comments' => 'مواد هالكة من أمر التصنيع: ' . $order->name,
                'subaccount' => 'manufacturing_waste_' . $order->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}


// دالة لحذف سجلات product_details عند التراجع
private function removeProductDetailsForManufacturingOrder(ManufacturOrders $order)
{
    // حذف سجل المنتج الرئيسي
    ProductDetails::where('product_id', $order->product_id)
        ->where('subaccount', 'manufacturing_order_' . $order->id)
        ->delete();

    // حذف سجلات المواد الهالكة
    $wasteMaterials = $order->manufacturOrdersItem()
        ->whereNotNull('end_life_product_id')
        ->pluck('end_life_product_id')
        ->toArray();

    if (!empty($wasteMaterials)) {
        ProductDetails::whereIn('product_id', $wasteMaterials)
            ->where('subaccount', 'manufacturing_waste_' . $order->id)
            ->delete();
    }

    // تسجيل عملية الحذف
    ModelsLog::create([
        'type' => 'product_details',
        'type_id' => $order->id,
        'type_log' => 'log',
        'description' => 'تم حذف سجلات product_details المرتبطة بأمر التصنيع **' . $order->name . '** عند التراجع',
        'created_by' => auth()->id(),
    ]);
}




// دالة لحساب إجمالي تكلفة المنتج


// دالة للحصول على تفاصيل التكلفة (للعرض أو التقارير)
public function getCostBredown(ManufacturOrders $order)
{
    $rawMaterialsCost = $order->manufacturOrdersItem()
        ->whereNotNull('product_id')
        ->sum(DB::raw('quantity * unit_price'));

    $laborCost = $order->labor_cost ?? 0;
    $additionalCosts = $order->additional_costs ?? 0;
    $totalCost = $rawMaterialsCost + $laborCost + $additionalCosts;
    $unitCost = $this->calculateUnitPrice($order);

    return [
        'raw_materials_cost' => $rawMaterialsCost,
        'labor_cost' => $laborCost,
        'additional_costs' => $additionalCosts,
        'total_cost' => $totalCost,
        'unit_cost' => $unitCost,
        'quantity' => $order->quantity,
        'total_value' => $totalCost,
    ];
}


// دالة للحصول على الكمية المتاحة للمنتج
private function getProductAvailableQuantity($productId)
{
    return ProductDetails::where('product_id', $productId)
        ->where('type', 'in')
        ->sum('quantity') -
        ProductDetails::where('product_id', $productId)
        ->where('type', 'out')
        ->sum('quantity');
}
// طريقة لإغلاق الأمر
public function closeOrder(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $order = ManufacturOrders::findOrFail($id);

        // التحقق من أن الأمر مكتمل
        if (!$order->isCompleted()) {
            return redirect()->back()->with(['error' => 'يجب إنهاء الأمر قبل إغلاقه']);
        }

        // تحديث حالة الأمر إلى مغلق
        $order->update([
            'status' => 'closed',
            'closed_at' => now(),
            'updated_by' => auth()->id(),
        ]);

        // تسجيل النشاط
        ModelsLog::create([
            'type' => 'manufacturing_order',
            'type_id' => $order->id,
            'type_log' => 'log',
            'description' => 'تم إغلاق أمر التصنيع **' . $order->name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('manufacturing.orders.show', $order->id)
            ->with(['success' => 'تم إغلاق أمر التصنيع بنجاح']);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with(['error' => 'حدث خطأ أثناء إغلاق الأمر: ' . $e->getMessage()]);
    }
}

// طريقة لإعادة فتح الأمر
public function reopenOrder($id)
{
    DB::beginTransaction();
    try {
        $order = ManufacturOrders::findOrFail($id);

        // التحقق من أن الأمر مغلق
        if ($order->status !== 'closed') {
            return redirect()->back()->with(['error' => 'هذا الأمر غير مغلق']);
        }

        // إعادة فتح الأمر
        $order->update([
            'status' => 'completed',
            'closed_at' => null,
            'updated_by' => auth()->id(),
        ]);

        // تسجيل النشاط
        ModelsLog::create([
            'type' => 'manufacturing_order',
            'type_id' => $order->id,
            'type_log' => 'log',
            'description' => 'تم إعادة فتح أمر التصنيع **' . $order->name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('manufacturing.orders.show', $order->id)
            ->with(['success' => 'تم إعادة فتح أمر التصنيع بنجاح']);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with(['error' => 'حدث خطأ أثناء إعادة فتح الأمر: ' . $e->getMessage()]);
    }
}

    public function destroy($id)
    {
        $order = ManufacturOrders::findOrFail($id);
        $order->manufacturOrdersItem()->delete();
        $order->delete();
        return redirect()->route('manufacturing.orders.index')->with(['success' => 'تم حذف البيانات بنجاح.']);
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

            $order = ManufacturOrders::findOrFail($id);

            // التعامل مع المرفق إذا تم رفعه
            $attachmentPath = null;
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $attachmentPath = $file->storeAs('purchase_invoices/notes', $fileName, 'public');
            }

            // إنشاء الملاحظة باستخدام ClientRelation
            $clientRelation = ClientRelation::create([
                'process' => $request->process,
                'time' => $request->time,
                'date' => $request->date,
                // 'quotation_id' => $id,
                'employee_id' => auth()->user()->id,
                'description' => $request->description,
                'attachment' => $attachmentPath,
                'type' => 'manufacturing_order', // نوع مختلف للفواتير
            ]);

            // إرسال إشعار
            notifications::create([
                'user_id' => $order->user_id,
                'receiver_id' => $order->user_id,
                'title' => 'ملاحظة جديدة',
                'description' => 'تم إضافة ملاحظة جديدة  امر تصنيع رقم ' . $order->code,
            ]);

            // تسجيل النشاط في سجل الأنشطة
            ModelsLog::create([
                'type' => 'manufacturing_order',
                'type_id' => $order->id,
                'type_log' => 'log',
                'icon' => 'create',
                'description' => sprintf(
                    'تم إضافة ملاحظة جديدة لفاتورة الشراء رقم **%s** بعنوان: %s',
                    $order->code ?? '',
                    $request->process
                ),
                'created_by' => auth()->id(),
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
                    'attachment_url' => $attachmentPath ? asset('storage/' . $attachmentPath) : null,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حفظ الملاحظة: ' . $e->getMessage(),
            ], 500);
        }
    }

    // جلب الملاحظات
    public function getNotes($id)
    {
        try {
            $notes = ClientRelation::where('type', 'manufacturing_order')
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
                'notes' => $formattedNotes->toArray(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الملاحظات: ' . $e->getMessage(),
            ], 500);
        }
    }

    // حذف الملاحظة
    public function deleteNote($noteId)
    {
        try {
            $note = ClientRelation::findOrFail($noteId);

            // حذف المرفق إذا كان موجود
            if ($note->attachment && Storage::disk('public')->exists($note->attachment)) {
                Storage::disk('public')->delete($note->attachment);
            }

$order = $note;
$order->type = 'manufacturing_order';
            $process = $note->process;

            $note->delete();

            // تسجيل النشاط
            ModelsLog::create(attributes: [
                'type' => 'manufacturing_order',
                'type_id' => $order->id,
                'type_log' => 'log',
                'icon' => 'delete',
                'description' => sprintf('تم حذف ملاحظة "%s" من امر التصنيع رقم %s', $process, $order->code),
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الملاحظة بنجاح',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الملاحظة: ' . $e->getMessage(),
            ], 500);
        }
    }



private function calculateCostFromProductDetails(ManufacturOrders $order)
{
    $totalCost = 0;

    $orderItems = $order->manufacturOrdersItem()

        ->get();

    foreach ($orderItems as $item) {
        // البحث في product_details عن آخر سعر شراء للمنتج
        $lastProductDetail = \App\Models\ProductDetails::where('product_id', $item->product_id)
            ->where('type', 'in') // العمليات الواردة
            ->where('type_of_operation', '!=', 'إنتاج') // استبعاد عمليات الإنتاج السابقة
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastProductDetail) {
            // استخدام آخر سعر من product_details
            $unitPrice = $lastProductDetail->unit_price ?? $lastProductDetail->price ?? 0;
        } else {
            // إذا لم توجد تفاصيل، استخدم من جدول المنتجات
            $product = \App\Models\Product::find($item->product_id);
            $unitPrice = $product ? ($product->unit_price ?? $product->price ?? $product->cost_price ?? 0) : 0;
        }

        $totalCost += ($item->quantity * $unitPrice);
    }

    return $totalCost;
}

// دالة لحساب التكلفة من آخر حركات الشراء
private function calculateCostFromPurchaseHistory(ManufacturOrders $order)
{
    $totalCost = 0;

    $orderItems = $order->manufacturOrdersItem()
        ->whereNotNull('product_id')
        ->get();

    foreach ($orderItems as $item) {
        // البحث عن آخر سعر شراء في product_details
        $lastPurchase = \App\Models\ProductDetails::where('product_id', $item->product_id)
            ->where('type', 'in')
            ->whereIn('type_of_operation', ['شراء', 'purchase', 'فاتورة شراء'])
            ->orderBy('date', 'desc')
            ->first();

        if ($lastPurchase) {
            $unitPrice = $lastPurchase->unit_price ?? $lastPurchase->price ?? 0;
        } else {
            // البحث في جدول المنتجات
            $product = \App\Models\Product::find($item->product_id);
            $unitPrice = $product ? ($product->cost_price ?? $product->unit_price ?? $product->price ?? 0) : 0;
        }

        $totalCost += ($item->quantity * $unitPrice);
    }

    return $totalCost;
}

// طريقة بديلة شاملة لحساب التكلفة
private function calculateCostAlternativeMethod(ManufacturOrders $order)
{
    // الطريقة الأولى: من تفاصيل المنتج
    $costFromDetails = $this->calculateCostFromProductDetails($order);
    if ($costFromDetails > 0) {
        return $costFromDetails;
    }

    // الطريقة الثانية: من تاريخ الشراء
    $costFromPurchases = $this->calculateCostFromPurchaseHistory($order);
    if ($costFromPurchases > 0) {
        return $costFromPurchases;
    }

    // الطريقة الثالثة: استخدام القيمة المحفوظة مسبقاً
    if ($order->total_cost && $order->total_cost > 0) {
        return $order->total_cost;
    }

    // الطريقة الرابعة: تقدير من المنتج النهائي
    $finalProduct = \App\Models\Product::find($order->product_id);
    if ($finalProduct) {
        $estimatedCost = ($finalProduct->cost_price ?? $finalProduct->unit_price ?? $finalProduct->price ?? 0) * $order->quantity * 0.8;
        return $estimatedCost;
    }

    return 0;
}




// دالة لحذف الإذن المخزني السابق
private function deleteOldWarehousePermit(ManufacturOrders $order)
{
    try {
        $oldWarehousePermit = WarehousePermits::find($order->warehouse_permit_id);
        if ($oldWarehousePermit) {
            WarehousePermitsProducts::where('warehouse_permits_id', $oldWarehousePermit->id)->delete();

            ModelsLog::create([
                'type' => 'warehouse_log',
                'type_id' => $oldWarehousePermit->id,
                'type_log' => 'log',
                'description' => 'تم حذف الإذن المخزني السابق رقم **' . $oldWarehousePermit->number . '** عند إعادة إنهاء أمر التصنيع',
                'created_by' => auth()->id(),
            ]);

            $oldWarehousePermit->delete();
        }
    } catch (\Exception $e) {
        \Log::warning('فشل في حذف الإذن المخزني السابق: ' . $e->getMessage());
    }
}

// دالة محدثة لإنشاء الإذن المخزني



// الحالة الأولى: إذا كنت تريد حذف إذن مخزني موجود مسبقاً عند الإنهاء
// (مثل حذف إذن مواد خام لتحويله لإذن منتج نهائي)

public function finishOrder(Request $request, $id)
{
    $request->validate([
        'main_warehouse_id' => 'required|exists:store_houses,id',
        'waste_warehouse_id' => 'required|exists:store_houses,id',
        'delivery_date' => 'required|date',
        'actual_quantity' => 'nullable|numeric|min:0',
        'notes' => 'nullable|string'
    ]);

    DB::beginTransaction();
    try {
        $order = ManufacturOrders::findOrFail($id);

        // التحقق من إمكانية إنهاء الأمر
        if (!$order->canBeCompleted()) {
            return redirect()->back()->with(['error' => 'لا يمكن إنهاء هذا الأمر في الوضع الحالي']);
        }

        // حذف الإذن المخزني السابق إذا كان موجوداً (مثل إذن المواد الخام)
        if ($order->warehouse_permit_id) {
            $oldWarehousePermit = WarehousePermits::find($order->warehouse_permit_id);
            if ($oldWarehousePermit) {
                // حذف عناصر الإذن السابق
                WarehousePermitsProducts::where('warehouse_permits_id', $oldWarehousePermit->id)->delete();

                // تسجيل نشاط الحذف
                ModelsLog::create([
                    'type' => 'warehouse_log',
                    'type_id' => $oldWarehousePermit->id,
                    'type_log' => 'log',
                    'description' => 'تم حذف الإذن المخزني السابق رقم **' . $oldWarehousePermit->number . '** عند إنهاء أمر التصنيع',
                    'created_by' => auth()->id(),
                ]);

                // حذف الإذن نفسه
                $oldWarehousePermit->delete();
            }
        }

        // إنشاء إذن مخزني جديد للمنتج النهائي
        $warehousePermit = $this->createWarehousePermitForOrder($order, $request);

        // تحديث حالة الأمر مع الإذن الجديد
        $order->update([
            'status' => 'completed',
            'finished_at' => $request->delivery_date,
            'main_warehouse_id' => $request->main_warehouse_id,
            'waste_warehouse_id' => $request->waste_warehouse_id,
            'actual_quantity' => $request->actual_quantity ?? $order->quantity,
            'finish_notes' => $request->notes,
            'warehouse_permit_id' => $warehousePermit->id, // الإذن الجديد
            'updated_by' => auth()->id(),
        ]);

        // تسجيل النشاط
        ModelsLog::create([
            'type' => 'manufacturing_order',
            'type_id' => $order->id,
            'type_log' => 'log',
            'description' => 'تم إنهاء أمر التصنيع **' . $order->name . '** وحذف الإذن السابق وإنشاء إذن جديد رقم **' . $warehousePermit->number . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('manufacturing.orders.show', $order->id)
            ->with(['success' => 'تم إنهاء أمر التصنيع وتحديث الإذن المخزني بنجاح']);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with(['error' => 'حدث خطأ أثناء إنهاء أمر التصنيع: ' . $e->getMessage()]);
    }
}

// ===============================================================

// الحالة الثانية: إذا كنت تريد حذف جميع الإذونات المرتبطة بالأمر عند الإنهاء
// (لإنهاء جميع العمليات المخزنية المؤقتة)

public function finishOrderWithPermitCleanup(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $order = ManufacturOrders::findOrFail($id);

        // التحقق من إمكانية إنهاء الأمر
        if (!$order->canBeCompleted()) {
            return redirect()->back()->with(['error' => 'لا يمكن إنهاء هذا الأمر في الوضع الحالي']);
        }

        // البحث عن جميع الإذونات المرتبطة بأمر التصنيع
        $relatedPermits = WarehousePermits::where('manufacturing_order_id', $order->id)->get();

        // حذف جميع الإذونات المرتبطة
        foreach ($relatedPermits as $permit) {
            // حذف عناصر الإذن
            WarehousePermitsProducts::where('warehouse_permits_id', $permit->id)->delete();

            // تسجيل الحذف
            ModelsLog::create([
                'type' => 'warehouse_log',
                'type_id' => $permit->id,
                'type_log' => 'log',
                'description' => 'تم حذف الإذن المخزني رقم **' . $permit->number . '** عند إنهاء أمر التصنيع **' . $order->name . '**',
                'created_by' => auth()->id(),
            ]);

            $permit->delete();
        }

        // تحديث حالة الأمر بدون إذن مخزني
        $order->update([
            'status' => 'completed',
            'finished_at' => $request->delivery_date,
            'main_warehouse_id' => $request->main_warehouse_id,
            'waste_warehouse_id' => $request->waste_warehouse_id,
            'actual_quantity' => $request->actual_quantity ?? $order->quantity,
            'finish_notes' => $request->notes,
            'warehouse_permit_id' => null, // لا يوجد إذن
            'updated_by' => auth()->id(),
        ]);

        // تسجيل النشاط
        ModelsLog::create([
            'type' => 'manufacturing_order',
            'type_id' => $order->id,
            'type_log' => 'log',
            'description' => 'تم إنهاء أمر التصنيع **' . $order->name . '** وحذف جميع الإذونات المخزنية المرتبطة',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('manufacturing.orders.show', $order->id)
            ->with(['success' => 'تم إنهاء أمر التصنيع وحذف الإذونات المخزنية بنجاح']);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with(['error' => 'حدث خطأ أثناء إنهاء أمر التصنيع: ' . $e->getMessage()]);
    }
}

// ===============================================================

// الحالة الثالثة: حذف إذونات معينة فقط (مثل إذونات المواد الخام)

public function finishOrderWithSelectivePermitDeletion(Request $request, $id)
{
    DB::beginTransaction();
    try {
        $order = ManufacturOrders::findOrFail($id);

        // حذف إذونات المواد الخام فقط (مثال)
        $rawMaterialPermits = WarehousePermits::where('manufacturing_order_id', $order->id)
            ->where('permission_source_id', 14) // مثال: معرف إذونات المواد الخام
            ->get();

        foreach ($rawMaterialPermits as $permit) {
            WarehousePermitsProducts::where('warehouse_permits_id', $permit->id)->delete();

            ModelsLog::create([
                'type' => 'warehouse_log',
                'type_id' => $permit->id,
                'type_log' => 'log',
                'description' => 'تم حذف إذن المواد الخام رقم **' . $permit->number . '** عند إنهاء أمر التصنيع',
                'created_by' => auth()->id(),
            ]);

            $permit->delete();
        }

        // إنشاء إذن جديد للمنتج النهائي
        $finalProductPermit = $this->createWarehousePermitForOrder($order, $request);

        // تحديث الأمر
        $order->update([
            'status' => 'completed',
            'finished_at' => $request->delivery_date,
            'main_warehouse_id' => $request->main_warehouse_id,
            'waste_warehouse_id' => $request->waste_warehouse_id,
            'actual_quantity' => $request->actual_quantity ?? $order->quantity,
            'finish_notes' => $request->notes,
            'warehouse_permit_id' => $finalProductPermit->id,
            'updated_by' => auth()->id(),
        ]);

        ModelsLog::create([
            'type' => 'manufacturing_order',
            'type_id' => $order->id,
            'type_log' => 'log',
            'description' => 'تم إنهاء أمر التصنيع **' . $order->name . '** وحذف إذونات المواد الخام وإنشاء إذن المنتج النهائي',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('manufacturing.orders.show', $order->id)
            ->with(['success' => 'تم إنهاء أمر التصنيع وتحديث الإذونات المخزنية بنجاح']);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with(['error' => 'حدث خطأ أثناء إنهاء أمر التصنيع: ' . $e->getMessage()]);
    }
}

// ===============================================================

// تحديث دالة التراجع للتعامل مع الحالات المختلفة
public function undoCompletion($id)
{
    DB::beginTransaction();
    try {
        $order = ManufacturOrders::findOrFail($id);

        if (!$order->isCompleted()) {
            return redirect()->back()->with(['error' => 'هذا الأمر غير منتهي بالأصل']);
        }

        // استرداد الإذونات المحذوفة من السجل إذا كانت مطلوبة
        // أو إنشاء إذونات جديدة للمواد الخام

        // حذف إذن المنتج النهائي إذا كان موجوداً
        if ($order->warehouse_permit_id) {
            $warehousePermit = WarehousePermits::find($order->warehouse_permit_id);
            if ($warehousePermit) {
                WarehousePermitsProducts::where('warehouse_permits_id', $warehousePermit->id)->delete();
                $warehousePermit->delete();
            }
        }

        $order->update([
            'status' => 'in_progress',
            'finished_at' => null,
            'main_warehouse_id' => null,
            'waste_warehouse_id' => null,
            'finish_notes' => null,
            'warehouse_permit_id' => null,
            'updated_by' => auth()->id(),
        ]);

        ModelsLog::create([
            'type' => 'manufacturing_order',
            'type_id' => $order->id,
            'type_log' => 'log',
            'description' => 'تم التراجع عن إنهاء أمر التصنيع **' . $order->name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('manufacturing.orders.show', $order->id)
            ->with(['success' => 'تم التراجع عن إنهاء أمر التصنيع بنجاح']);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with(['error' => 'حدث خطأ أثناء التراجع: ' . $e->getMessage()]);
    }
}


private function calculateOrderTotalCost(ManufacturOrders $order)
{
    return $order->last_total_cost ?? 0;
}


private function calculateProductTotal(ManufacturOrders $order, $quantity)
{
    $unitPrice = $this->calculateUnitPrice($order);
    return $unitPrice * $quantity;
}

private function calculateUnitPrice(ManufacturOrders $order)
{
    $totalCost = $this->calculateOrderTotalCost($order);
    return $order->quantity > 0 ? ($totalCost / $order->quantity) : 0;
}


// دالة مساعدة لإنشاء الإذن المخزني - إذن واحد فقط
private function createWarehousePermitForOrder(ManufacturOrders $order, Request $request)
{
    // إنشاء رقم إذن مخزني فريد
    $permitNumber = 'MO-' . $order->code . '-' . date('Ymd') . '-' . rand(1000, 9999);

    // حساب المجموع الإجمالي للإذن (المنتج الرئيسي + المواد الهالكة)
    $grandTotal = $this->calculateOrderTotalCost($order);
    $wasteMaterialsTotal = $this->calculateWasteMaterialsTotal($order);
    $totalAmount = $grandTotal + $wasteMaterialsTotal;

    // إنشاء الإذن المخزني الواحد
    $warehousePermit = WarehousePermits::create([
        'store_houses_id' => $request->main_warehouse_id,
        'from_store_houses_id' => null, // لا يوجد مستودع مصدر (إنتاج جديد)
        'to_store_houses_id' => $request->main_warehouse_id, // المستودع المستهدف
        'permission_source_id' => 3, // معرف مصدر أوامر التصنيع
         // إذن استلام
        'permission_date' => $request->delivery_date,
        'number' => $permitNumber,
        'details' => 'إذن استلام من أمر التصنيع: ' . $order->name . ' (يشمل المنتج الرئيسي والمواد الهالكة)',
        'grand_total' => $totalAmount,
        'created_by' => auth()->user()->id,
        'status' => 'approved',
        'manufacturing_order_id' => $order->id, // ربط بأمر التصنيع
    ]);

    // إضافة المنتج الرئيسي إلى الإذن
    $actualQuantity = $request->actual_quantity ?? $order->quantity;
    $mainProductStockBefore = $this->getProductStockBefore($order->product_id, $request->main_warehouse_id);

    WarehousePermitsProducts::create([
        'quantity' => $actualQuantity,
        'total' => $this->calculateProductTotal($order, $actualQuantity),
        'unit_price' => $this->calculateUnitPrice($order),
        'product_id' => $order->product_id,
        'warehouse_permits_id' => $warehousePermit->id,
        'stock_before' => $mainProductStockBefore,
        'stock_after' => $mainProductStockBefore + $actualQuantity,
        'notes' => 'المنتج الرئيسي من أمر التصنيع',
    ]);

    // إضافة المواد الهالكة إلى نفس الإذن
    $this->addWasteMaterialsToSamePermit($order, $warehousePermit, $request);

    // تسجيل حركة المخزون
    $this->recordInventoryMovements($order, $warehousePermit, $request);

    // تسجيل نشاط إنشاء الإذن
    ModelsLog::create([
        'type' => 'warehouse_log',
        'type_id' => $warehousePermit->id,
        'type_log' => 'log',
        'description' => 'تم إنشاء إذن مخزني رقم **' . $warehousePermit->number . '** من أمر التصنيع **' . $order->name . '** (المنتج الرئيسي + المواد الهالكة)',
        'created_by' => auth()->id(),
    ]);

    return $warehousePermit;
}

// إضافة المواد الهالكة إلى نفس الإذن
private function addWasteMaterialsToSamePermit(ManufacturOrders $order, WarehousePermits $warehousePermit, Request $request)
{
    // التحقق من وجود مواد هالكة في أمر التصنيع
    $wasteMaterials = $order->manufacturOrdersItem()
        ->whereNotNull('end_life_product_id')
        ->where('end_life_quantity', '>', 0)
        ->get();

    foreach ($wasteMaterials as $wasteMaterial) {
        if ($wasteMaterial->endLifeProduct) {
            // إضافة المواد الهالكة إلى المستودع الرئيسي أو مستودع المواد الهالكة
            $targetWarehouseId = $request->waste_warehouse_id; // أو يمكن أن تكون نفس المستودع الرئيسي
            $wasteStockBefore = $this->getProductStockBefore($wasteMaterial->end_life_product_id, $targetWarehouseId);

            WarehousePermitsProducts::create([
                'quantity' => $wasteMaterial->end_life_quantity,
                'total' => $wasteMaterial->end_life_total ?? 0,
                'unit_price' => $wasteMaterial->end_life_unit_price ?? 0,
                'product_id' => $wasteMaterial->end_life_product_id,
                'warehouse_permits_id' => $warehousePermit->id,
                'stock_before' => $wasteStockBefore,
                'stock_after' => $wasteStockBefore + $wasteMaterial->end_life_quantity,
                'target_warehouse_id' => $targetWarehouseId, // تحديد المستودع المستهدف للمواد الهالكة
                'notes' => 'مواد هالكة من أمر التصنيع - المستودع المستهدف: ' . $this->getWarehouseName($targetWarehouseId),
            ]);

            // تسجيل حركة منفصلة للمواد الهالكة إلى مستودعها
            $this->recordWasteMaterialMovement($wasteMaterial, $targetWarehouseId, $warehousePermit->id);
        }
    }
}

// تسجيل حركات المخزون
private function recordInventoryMovements(ManufacturOrders $order, WarehousePermits $warehousePermit, Request $request)
{
    // تسجيل حركة المنتج الرئيسي
    $this->recordProductMovement([
        'product_id' => $order->product_id,
        'from_warehouse_id' => null, // إنتاج جديد
        'to_warehouse_id' => $request->main_warehouse_id,
        'quantity' => $request->actual_quantity ?? $order->quantity,
        'movement_type' => 'production_receipt',
        'reference_type' => 'manufacturing_order',
        'reference_id' => $order->id,
        'warehouse_permit_id' => $warehousePermit->id,
        'notes' => 'استلام من أمر التصنيع: ' . $order->name,
        'created_by' => auth()->id(),
    ]);

    // تحديث رصيد المنتج في المستودع الرئيسي
    $this->updateProductInventory($order->product_id, $request->main_warehouse_id,
        ($request->actual_quantity ?? $order->quantity), 'add');
}

// تسجيل حركة المواد الهالكة
private function recordWasteMaterialMovement($wasteMaterial, $targetWarehouseId, $permitId)
{
    $this->recordProductMovement([
        'product_id' => $wasteMaterial->end_life_product_id,
        'from_warehouse_id' => null, // إنتاج جديد
        'to_warehouse_id' => $targetWarehouseId,
        'quantity' => $wasteMaterial->end_life_quantity,
        'movement_type' => 'waste_material_receipt',
        'reference_type' => 'manufacturing_order',
        'reference_id' => $wasteMaterial->manufactur_orders_id,
        'warehouse_permit_id' => $permitId,
        'notes' => 'استلام مواد هالكة من أمر التصنيع',
        'created_by' => auth()->id(),
    ]);

    // تحديث رصيد المواد الهالكة في المستودع المستهدف
    $this->updateProductInventory($wasteMaterial->end_life_product_id, $targetWarehouseId,
        $wasteMaterial->end_life_quantity, 'add');
}

// دالة عامة لتسجيل حركة المنتجات
private function recordProductMovement($data)
{
    // تسجيل في جدول حركات المخزون
    // يمكنك تخصيص هذا حسب بنية قاعدة البيانات الخاصة بك
    /*
    StockMovement::create([
        'product_id' => $data['product_id'],
        'from_warehouse_id' => $data['from_warehouse_id'],
        'to_warehouse_id' => $data['to_warehouse_id'],
        'quantity' => $data['quantity'],
        'movement_type' => $data['movement_type'],
        'reference_type' => $data['reference_type'],
        'reference_id' => $data['reference_id'],
        'warehouse_permit_id' => $data['warehouse_permit_id'],
        'movement_date' => now(),
        'notes' => $data['notes'],
        'created_by' => $data['created_by'],
    ]);
    */
}

// تحديث رصيد المنتج في المستودع
private function updateProductInventory($productId, $warehouseId, $quantity, $operation = 'add')
{
    // تحديث رصيد المنتج في المستودع
    // يمكنك تخصيص هذا حسب بنية قاعدة البيانات الخاصة بك
    /*
    $inventory = ProductInventory::firstOrCreate([
        'product_id' => $productId,
        'warehouse_id' => $warehouseId,
    ], [
        'quantity' => 0,
        'reserved_quantity' => 0,
    ]);

    if ($operation === 'add') {
        $inventory->increment('quantity', $quantity);
    } else {
        $inventory->decrement('quantity', $quantity);
    }
    */
}

// حساب مجموع المواد الهالكة
private function calculateWasteMaterialsTotal(ManufacturOrders $order)
{
    return $order->manufacturOrdersItem()
        ->whereNotNull('end_life_product_id')
        ->where('end_life_quantity', '>', 0)
        ->sum('end_life_total') ?? 0;
}

// الحصول على اسم المستودع
private function getWarehouseName($warehouseId)
{
    // يمكنك تحسين هذا حسب نموذج المستودعات الخاص بك
    $warehouse = \App\Models\StoreHouse::find($warehouseId);
    return $warehouse ? $warehouse->name : 'مستودع غير معروف';
}

// تحديث دالة التراجع لحذف الإذن الواحد والحركات

// التراجع عن حركة المخزون
private function reverseInventoryMovement($permitProduct, $order)
{
    // تحديد المستودع المستهدف
    $targetWarehouseId = $permitProduct->target_warehouse_id ?? $order->main_warehouse_id;

    // خصم الكمية من المستودع (التراجع عن الإضافة)
    $this->updateProductInventory($permitProduct->product_id, $targetWarehouseId,
        $permitProduct->quantity, 'subtract');
}

// حذف حركات المخزون المرتبطة
private function deleteRelatedStockMovements($permitId)
{
    // حذف حركات المخزون المرتبطة بهذا الإذن
    /*
    StockMovement::where('warehouse_permit_id', $permitId)->delete();
    */
}

// تحديث دالة getProductStockBefore لتعكس المخزون الحقيقي
private function getProductStockBefore($productId, $warehouseId)
{
    // الحصول على الرصيد الحالي من قاعدة البيانات
    /*
    $inventory = ProductInventory::where('product_id', $productId)
        ->where('warehouse_id', $warehouseId)
        ->first();

    return $inventory ? $inventory->quantity : 0;
    */
    return 0; // مؤقت - يجب تحديثه حسب نظامك
}
}
