<?php

namespace Modules\Stock\Http\Controllers\Manufacturing;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductionMaterialRequest;
use App\Models\Account;
use App\Models\Product;

use App\Models\Log as ModelsLog;


use App\Models\ProductionMaterials;
use App\Models\ProductionMaterialsItem;
use App\Models\ProductionPath;
use App\Models\ProductionStage;
use App\Models\WorkStations;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BOMController extends Controller
{
    public function index()
{
    // جلب البيانات للفلاتر
    $products = Product::select('id', 'name')->get();
    $operations = ProductionMaterials::select('id', 'name')->get();

    return view('stock::manufacturing.bom.index', compact('products', 'operations'));
}

public function getData(Request $request)
{
    $query = ProductionMaterials::query();
    // فلترة البحث بالاسم أو الكود
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%");
        });
    }

    // فلترة حسب المنتج
    if ($request->filled('product_id')) {
        $query->where('product_id', $request->product_id);
    }

    // فلترة حسب الحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // فلترة حسب عملية الإنتاج
    if ($request->filled('operation_id')) {
        $query->where('operation_id', $request->operation_id);
    }

    // ترتيب البيانات
    $query->orderBy('created_at', 'desc');

    // جلب البيانات مع التقسيم إلى صفحات
    $materials = $query->paginate(10);

    // إرجاع البيانات كـ JSON
    return response()->json([
        'success' => true,
        'data' => $materials->items(),
        'pagination' => [
            'current_page' => $materials->currentPage(),
            'last_page' => $materials->lastPage(),
            'per_page' => $materials->perPage(),
            'total' => $materials->total(),
        ]
    ]);
}

    public function create()
    {
        $record_count = DB::table('production_materials')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);
        $products = Product::select()->get();
        $accounts = Account::select('id','name')->get();
        $paths = ProductionPath::select('id','name')->get();
        $stages = ProductionStage::select('id','stage_name')->get();
        $workstations = WorkStations::select('id','name','total_cost')->get();
        return view('stock::manufacturing.bom.create', compact('products','accounts','paths','serial_number','stages','workstations'));
    }


public function store(ProductionMaterialRequest $request)
{
    DB::beginTransaction();

    try {
        if ($request->default == 1) {
            $existingDefault = ProductionMaterials::where('default', 1)->first();
            if ($existingDefault) {
                $existingDefault->update(['default' => 0]);
            }
        }

        $productionMaterial = ProductionMaterials::create([
            'name' => $request->name,
            'code' => $request->code,
            'product_id' => $request->product_id,
            'account_id' => $request->account_id,
            'production_path_id' => $request->production_path_id,
            'quantity' => $request->quantity,
            'last_total_cost' => $request->last_total_cost ?? 0,
            'status' => $request->status ?? 0,
            'default' => $request->default ?? 0,
            'created_by' => auth()->id(),
        ]);

        // معالجة المواد الخام
        if ($request->has('raw_product_id')) {
            foreach ($request->raw_product_id as $index => $rawProductId) {
                ProductionMaterialsItem::create([
                    'production_material_id'     => $productionMaterial->id,

                    // مواد خام
                    'raw_product_id'             => $rawProductId,
                    'raw_production_stage_id'    => $request->raw_production_stage_id[$index] ?? 0,
                    'raw_unit_price'             => $request->raw_unit_price[$index] ?? 0,
                    'raw_quantity'               => $request->raw_quantity[$index] ?? 0,
                    'raw_total'                  => $request->raw_total[$index] ?? 0,

                    // مصاريف
                    'expenses_account_id'        => $request->expenses_account_id[$index] ?? 0,
                    'expenses_cost_type'         => $request->expenses_cost_type[$index] ?? 0,
                    'expenses_production_stage_id' => $request->expenses_production_stage_id[$index] ?? 0,
                    'expenses_price'             => $request->expenses_price[$index] ?? 0,
                    'expenses_description'       => $request->expenses_description[$index] ?? '',
                    'expenses_total'             => $request->expenses_total[$index] ?? 0,

                    // تشغيل
                    'workstation_id'             => $request->workstation_id[$index] ?? 0,
                    'operating_time'             => $request->operating_time[$index] ?? 0,
                    'manu_production_stage_id'   => $request->manu_production_stage_id[$index] ?? 0,
                    'manu_cost_type'             => $request->manu_cost_type[$index] ?? 0,
                    'manu_total_cost'            => $request->manu_total_cost[$index] ?? 0,
                    'manu_description'           => $request->manu_description[$index] ?? '',
                    'manu_total'                 => $request->manu_total[$index] ?? 0,

                    // منتجات نهاية العمر (يتم خصمها من التكلفة)
                    'end_life_product_id'        => $request->end_life_product_id[$index] ?? 0,
                    'end_life_unit_price'        => $request->end_life_unit_price[$index] ?? 0,
                    'end_life_production_stage_id'=> $request->end_life_production_stage_id[$index] ?? 0,
                    'end_life_quantity'          => $request->end_life_quantity[$index] ?? 0,
                    'end_life_total'             => $request->end_life_total[$index] ?? 0,
                ]);
            }
        }

        ModelsLog::create([
            'type' => 'production_material',
            'type_id' => $productionMaterial->id, // تم التصحيح هنا
            'type_log' => 'log',
            'description' => 'تم إنشاء قائمة مواد إنتاج جديدة: ' . $request->name,
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('BOM.index')->with(['success' => 'تم حفظ البيانات بنجاح.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('BOM.index')->with(['error' => 'حدث خطأ ما: ' . $e->getMessage()]);
    }
}

    public function edit($id)
    {
        $productionMaterial = ProductionMaterials::find($id);
        $products = Product::select()->get();
        $accounts = Account::select('id','name')->get();
        $paths = ProductionPath::select('id','name')->get();
        $stages = ProductionStage::select('id','stage_name')->get();
        $workstations = WorkStations::select('id','name','total_cost')->get();
        $productionMaterialItems = ProductionMaterialsItem::where('production_material_id', $id)->get();
        return view('stock::manufacturing.bom.edit', compact('productionMaterial','products','accounts','paths','stages','workstations','productionMaterialItems'));
    }

public function update(ProductionMaterialRequest $request, $id)
{
    DB::beginTransaction();

    try {
        if ($request->default == 1) {
            $existingDefault = ProductionMaterials::where('default', 1)->where('id', '!=', $id)->first();
            if ($existingDefault) {
                $existingDefault->update(['default' => 0]);
            }
        }

        $productionMaterial = ProductionMaterials::findOrFail($id);
        $productionMaterial->update([
            'name' => $request->name,
            'code' => $request->code,
            'product_id' => $request->product_id,
            'account_id' => $request->account_id,
            'production_path_id' => $request->production_path_id,
            'quantity' => $request->quantity,
            'last_total_cost' => $request->last_total_cost ?? 0,
            'status' => $request->status ?? 0,
            'default' => $request->default ?? 0,
            'updated_by' => auth()->id(),
        ]);

        // ✅ التعامل مع العناصر مثل store
        if ($request->has('raw_product_id')) {
            foreach ($request->raw_product_id as $index => $rawProductId) {
                ProductionMaterialsItem::updateOrCreate(
                    [
                        'production_material_id' => $productionMaterial->id,
                        'id' => $request->item_id[$index] ?? null, // لو عندك hidden input فيه id للعناصر القديمة
                    ],
                    [
                        // مواد خام
                        'raw_product_id'             => $rawProductId,
                        'raw_production_stage_id'    => $request->raw_production_stage_id[$index] ?? 0,
                        'raw_unit_price'             => $request->raw_unit_price[$index] ?? 0,
                        'raw_quantity'               => $request->raw_quantity[$index] ?? 0,
                        'raw_total'                  => $request->raw_total[$index] ?? 0,

                        // مصاريف
                        'expenses_account_id'        => $request->expenses_account_id[$index] ?? 0,
                        'expenses_cost_type'         => $request->expenses_cost_type[$index] ?? 0,
                        'expenses_production_stage_id'=> $request->expenses_production_stage_id[$index] ?? 0,
                        'expenses_price'             => $request->expenses_price[$index] ?? 0,
                        'expenses_description'       => $request->expenses_description[$index] ?? '',
                        'expenses_total'             => $request->expenses_total[$index] ?? 0,

                        // تشغيل
                        'workstation_id'             => $request->workstation_id[$index] ?? 0,
                        'operating_time'             => $request->operating_time[$index] ?? 0,
                        'manu_production_stage_id'   => $request->manu_production_stage_id[$index] ?? 0,
                        'manu_cost_type'             => $request->manu_cost_type[$index] ?? 0,
                        'manu_total_cost'            => $request->manu_total_cost[$index] ?? 0,
                        'manu_description'           => $request->manu_description[$index] ?? '',
                        'manu_total'                 => $request->manu_total[$index] ?? 0,

                        // منتجات نهاية العمر
                        'end_life_product_id'        => $request->end_life_product_id[$index] ?? 0,
                        'end_life_unit_price'        => $request->end_life_unit_price[$index] ?? 0,
                        'end_life_production_stage_id'=> $request->end_life_production_stage_id[$index] ?? 0,
                        'end_life_quantity'          => $request->end_life_quantity[$index] ?? 0,
                        'end_life_total'             => $request->end_life_total[$index] ?? 0,
                    ]
                );
            }
        }

        ModelsLog::create([
            'type' => 'production_material',
            'type_id' => $id,
            'type_log' => 'update',
            'description' => ($request->description ?? '') . ' تم تحديث بيانات المواد المصنعة',
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()->route('BOM.index')->with(['success' => 'تم تحديث البيانات بنجاح.']);
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('BOM.index')->with(['error' => 'حدث خطأ ما: ' . $e->getMessage()]);
    }
}

    public function show($id)
    {
        // استرجاع البيانات الحالية
        $productionMaterial = ProductionMaterials::findOrFail($id);
        $productionMaterialItems = ProductionMaterialsItem::where('production_material_id', $id)->get();

        // استرجاع البيانات الأخرى المطلوبة للنموذج
        $products = Product::all();
        $accounts = Account::all();
        $paths = ProductionPath::all();
        $stages = ProductionStage::all();
        $workstations = WorkStations::all();
                $logs = ModelsLog::where('type', 'production_material')
            ->where('type_id', $id)
            ->whereHas('production_material') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('stock::manufacturing.bom.show', compact(
            'productionMaterial',
            'productionMaterialItems',
            'products',
            'accounts',
            'paths',
            'stages',
            'logs',
            'workstations'
        ));
    }

    public function destroy($id)
    {
        $productionMaterial = ProductionMaterials::findOrFail($id);
        $productionMaterial->ProductionMaterialsItem()->delete();
        $productionMaterial->delete();
        return redirect()->route('BOM.index')->with(['error' => 'تم حذف البيانات بنجاح.']);
    }

    public function get_cost_total($id)
    {
        $workstation = WorkStations::find($id);

        if (!$workstation) {
            return response()->json(['error' => 'Workstation not found'], 404);
        }
        return response()->json(['total_cost' => $workstation->total_cost]);
    }


}
