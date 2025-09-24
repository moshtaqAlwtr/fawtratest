<?php

namespace Modules\RentalManagement\Http\Controllers;
use App\Models\Unit;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Log as ModelsLog;
use App\Models\UnitType; // موديل الوحدات
use App\Models\PricingRule; // موديل قواعد التسعير

class UnitsController extends Controller
{
    public function index()
    {
        // استرجاع جميع الوحدات من قاعدة البيانات
        $units = UnitType::all();

        // إرسال الوحدات إلى الـ View
        return view('rentalmanagement::units.index', compact('units'));
    }

    /**
     * عرض صفحة إضافة وحدة جديدة.
     */
    public function create()
    {
        $unitTypes = UnitType::all(); // جلب أنواع الوحدات
        return view('rentalmanagement::units.create', compact('unitTypes'));
    }




        public function show($id)
        {
            // جلب بيانات الوحدة من قاعدة البيانات باستخدام المعرف (ID)
            $unit = Unit::findOrFail($id);

            // تمرير البيانات إلى العرض (view)
            return view('rentalmanagement::units.show', compact('unit'));
        }


            // دالة التعديل
            public function edit($id)
            {
                // جلب الوحدة المطلوبة من قاعدة البيانات
                $unit = Unit::findOrFail($id);

                // جلب أنواع الوحدات لإضافتها إلى قائمة الخيارات
                $unitTypes = UnitType::all();

                // عرض صفحة التعديل مع تمرير البيانات
                return view('rental_management::units.edit', compact('unit', 'unitTypes'));
            }




    /**
     * تخزين وحدة جديدة.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات المدخلة
        $request->validate([
            'name' => 'required|string|max:255',
            'unit_type_id' => 'required|exists:unit_types,id', // التأكد من أن النوع موجود في جدول unit_types
            'priority' => 'nullable|integer',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string|max:1000',
        ]);

        // إنشاء الوحدة في قاعدة البيانات
       $UNIT =  Unit::create([
            'name' => $request->name,
            'unit_type_id' => $request->unit_type_id,
            'priority' => $request->priority,
            'status' => $request->status,
            'description' => $request->description,
        ]);

                        ModelsLog::create([
    'type' => 'unit',
    'type_id' => $UNIT->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم اضافة  وحدة  **' . $request->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('rental_management.units.index')->with('success', 'تمت إضافة الوحدة بنجاح');
    }
    public function update(Request $request, $id)
{
    // التحقق من المدخلات
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'unit_type_id' => 'required|exists:unit_types,id',
        'priority' => 'nullable|integer',
        'status' => 'required|in:active,inactive',
        'description' => 'nullable|string',
    ]);

    // تحديث الوحدة
    $unit = Unit::findOrFail($id);
    $unit->update($validated);

    // إعادة التوجيه مع رسالة نجاح
    return redirect()->route('rental_management.units.index')->with('success', 'تم تحديث الوحدة بنجاح.');
}
public function delete($id)
{
    // البحث عن الوحدة باستخدام الـ ID
    $unit = Unit::findOrFail($id);

    // حذف الوحدة
    $unit->delete();

    // إعادة توجيه أو إرسال استجابة (يمكن تعديل هذه حسب الحاجة)
    return redirect()->route('rental_management.units.index')->with('success', 'تم حذف الوحدة بنجاح');
}


}
