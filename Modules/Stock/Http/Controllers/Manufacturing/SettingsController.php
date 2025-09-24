<?php

namespace Modules\Stock\Http\Controllers\Manufacturing;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderManualStatusRequest;
use App\Models\ManualManufacturingOrderStatuses;
use App\Models\ManufacturingGeneralSetting;
use App\Models\ManufacturingOrderStatuses;
use App\Models\ProductionPath;


use App\Models\Log as ModelsLog;



use App\Models\ProductionStage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{
    public function index()
    {
        return view('stock::manufacturing.settings.index');
    }
    public function General()
    {
        $general_settings = ManufacturingGeneralSetting::first();
        return view('stock::manufacturing.settings.general', compact('general_settings'));
    }

    public function Manual()
    {
        $order_manual_status = ManufacturingOrderStatuses::with('manualOrderStatus')->first();
        return view('stock::manufacturing.settings.manual.index', compact('order_manual_status'));
    }






    public function general_settings(Request $request)
    {
        ManufacturingGeneralSetting::updateOrCreate(
            ['id' => 1],
            ['quantity_exceeded' => $request->has('quantity_exceeded') ? 1 : 0]
        );

        return redirect()->route('manufacturing.settings.general')->with(['success' => 'تم تحديث الإعدادات بنجاح']);
    }

    public function order_manual_status(Request $request)
    {
        $order_manual_status = ManufacturingOrderStatuses::first();

        if (!$order_manual_status) {
            $order_manual_status = ManufacturingOrderStatuses::create([
                'active' => $request->has('active') ? 1 : 0
            ]);
        } else {
            $order_manual_status->update([
                'active' => $request->has('active') ? 1 : 0
            ]);
        }

        ManualManufacturingOrderStatuses::where('order_status_id', $order_manual_status->id)->delete();

        foreach ($request->name as $index => $manualName) {
            ManualManufacturingOrderStatuses::updateOrCreate(
                [
                    'order_status_id' => $order_manual_status->id,
                    'name' => $manualName
                ],
                [
                    'color' => $request->color[$index] ?? '#ffffff'
                ]
            );
        }

        return redirect()->route('Manufacturing.settings.index')->with(['success' => 'تم تحديث الإعداد بنجاح']);
    }


public function paths_index(Request $request)
{
    $production_stages = ProductionStage::select('id', 'stage_name')->get();

    // إذا لم يكن هناك طلب Ajax، عرض الصفحة الرئيسية فقط
    if (!$request->ajax()) {
        return view('stock::manufacturing.settings.Paths.index', compact('production_stages'));
    }

    // إذا كان Ajax request، إرجاع البيانات
    return $this->getPathsData($request);
}

// Method جديد للتعامل مع Ajax requests
public function paths_ajax(Request $request)
{
    return $this->getPathsData($request);
}

// Method مساعد لجلب البيانات
private function getPathsData(Request $request)
{
    $query = ProductionPath::query();

    // البحث بالاسم أو الكود
    if ($request->filled('search')) {
        $searchTerm = $request->search;
        $query->where(function($q) use ($searchTerm) {
            $q->where('name', 'LIKE', "%{$searchTerm}%")
              ->orWhere('code', 'LIKE', "%{$searchTerm}%");
        });
    }

    // فلترة حسب مرحلة الإنتاج
    if ($request->filled('production_stage_id')) {
        $query->whereHas('stages', function ($q) use ($request) {
            $q->where('id', $request->production_stage_id);
        });
    }

    $paths = $query->orderBy('created_at', 'desc')->get();

    // إرجاع الـ partial view
    return view('stock::manufacturing.settings.Paths.table', compact('paths'))->render();
}

    public function paths_create()
    {
        $record_count = DB::table('production_paths')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);

        return view('stock::manufacturing.settings.Paths.create', compact('serial_number'));
    }

    public function paths_store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:production_paths,code',
            'stage_name' => 'required|array|min:1',
            'stage_name.*' => 'required|string|max:255',
        ]);

        $path = ProductionPath::create([
            'name' => $request->name,
            'code' => $request->code,
            'created_by' => auth()->user()->id
        ]);

        foreach ($request->stage_name as $stageName) {
            ProductionStage::create([
                'production_paths_id' => $path->id,
                'stage_name' => $stageName,
            ]);
        }
    ModelsLog::create([
            'type' => 'pathMan',
            'type_id' =>  $path->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم  اضافة  مسار الانتاج :  **' . $path->name . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()->route('manufacturing.paths.show', $path->id)->with('success')->with(['success'=>'تمت إضافة مسار الإنتاج بنجاح']);
    }

    public function paths_show($id)
    {
        $path = ProductionPath::findOrFail($id);
        $paths = ProductionPath::with('stages')->orderBy('created_at', 'desc')->get();
$logs = ModelsLog::where('type', 'pathMan')
            ->where('type_id', $id)
            ->whereHas('pathMan') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('stock::manufacturing.settings.Paths.show', compact('logs','path', 'paths'));
    }

    public function paths_edit($id)
    {
        $path = ProductionPath::findOrFail($id);
        return view('stock::manufacturing.settings.Paths.edit', compact('path'));
    }

    public function paths_update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:production_paths,code,' . $id,
            'stage_name' => 'required|array|min:1',
            'stage_name.*' => 'required|string|max:255',
        ]);

        $path = ProductionPath::findOrFail($id);
        $path->update([
            'name' => $request->name,
            'code' => $request->code,
            'updated_by' => auth()->user()->id
        ]);

        // حذف المراحل القديمة وإعادة إدخال الجديدة
        ProductionStage::where('production_paths_id', $id)->delete();
        foreach ($request->stage_name as $stageName) {
            ProductionStage::create([
                'production_paths_id' => $path->id,
                'stage_name' => $stageName,
            ]);
        }
ModelsLog::create([
            'type' => 'pathMan',
            'type_id' =>  $path->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم   تحديث  مسار الانتاج :  **' . $path->name . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()->route('manufacturing.paths.show', $path->id)->with('success')->with(['success' => 'تم تحديث مسار الإنتاج بنجاح']);
    }

    public function paths_destroy($id)
    {
        $path = ProductionPath::findOrFail($id);

        ProductionStage::where('production_paths_id', $id)->delete();

        $path->delete();
ModelsLog::create([
            'type' => 'pathMan',
            'type_id' =>  $path->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم   حذف  مسار الانتاج :  **' . $path->name . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()->route('manufacturing.paths.index')->with(['error' => 'تم حذف مسار الإنتاج بنجاح']);
    }


}
