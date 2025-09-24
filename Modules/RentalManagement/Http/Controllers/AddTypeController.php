<?php

namespace Modules\RentalManagement\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Log as ModelsLog;
use App\Models\UnitType;

class AddTypeController extends Controller
{
    /**
     * عرض صفحة إضافة وحدة جديدة.
     */
    public function index()
    {
        $unitstype = UnitType::all(); // استرداد البيانات من قاعدة البيانات
        return view('rentalmanagement::settings.add_type.index', compact('unitstype')); // تمرير المتغير إلى العرض
    }

    public function create()
    {
        // جلب قواعد التسعير من قاعدة البيانات
        $pricingRules = PricingRule::all();

        // إرسال البيانات إلى الـ view
        return view('rentalmanagement::settings.add_type.create', compact('pricingRules'));
    }

    public function show($id)
    {
        $unitType = UnitType::findOrFail($id); // جلب نوع الوحدة حسب الـ ID
        $units = Unit::where('unit_type_id', $id)->get(); // جلب الوحدات المرتبطة
        return view('rentalmanagement::Settings.Add_Type.show', compact('unitType', 'units'));
    }

    /**
     * تخزين وحدة جديدة في قاعدة البيانات.
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'unitName' => 'required|string|max:255',
            'status' => 'required|in:1,2',
            'pricingMethod' => 'required|exists:pricing_rules,id',
            'pricingRule' => 'nullable|string|max:255',
            'checkInTime' => 'required|date_format:H:i',
            'checkOutTime' => 'required|date_format:H:i',
            'tax1' => 'nullable|numeric|min:0|max:100',
            'tax2' => 'nullable|numeric|min:0|max:100',
            'description' => 'nullable|string|max:1000',
        ]);

        // تخزين البيانات في قاعدة البيانات
   $UnitType =     UnitType::create([
            'name' => $request->unitName,
            'status' => $request->status,
            'pricing_rule_id' => $request->pricingMethod,
            'check_in_time' => $request->checkInTime,
            'check_out_time' => $request->checkOutTime,
            'tax1' => $request->tax1,
            'tax2' => $request->tax2,
            'description' => $request->description,
        ]);

        ModelsLog::create([
    'type' => 'unit',
    'type_id' => $UnitType->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم اضافة   نوع وحدة  **' . $UnitType->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('Rental_Management.Settings.Add_Type.index')->with('success', 'تم إضافة الوحدة بنجاح');
    }

    /**
     * تعديل وحدة موجودة.
     */
    public function edit($id)
    {
        // جلب نوع الوحدة من قاعدة البيانات
        $type = UnitType::findOrFail($id);

        // جلب قواعد التسعير إذا كانت مطلوبة في صفحة التعديل
        $pricingRules = PricingRule::all();

        // تمرير البيانات إلى صفحة التعديل
        return view('rentalmanagement::Settings.Add_Type.edit', compact('type', 'pricingRules'));
    }
    public function update(Request $request, $id)
{
     //التحقق من صحة البيانات
     $request->validate([
        'unitName' => 'required|string|max:255',
        'status' => 'required|in:1,2',
        'pricingMethod' => 'required|exists:pricing_rules,id',
        'pricingRule' => 'nullable|string|max:255',
        'checkInTime' => 'required|date_format:H:i',
        'checkOutTime' => 'required|date_format:H:i',
        'tax1' => 'nullable|numeric|min:0|max:100',
        'tax2' => 'nullable|numeric|min:0|max:100',
        'description' => 'nullable|string|max:1000',
    ]);

    // جلب الوحدة من قاعدة البيانات
    $unitType = UnitType::findOrFail($id);

    // تحديث البيانات
    $unitType->update([
        'name' => $request->unitName,
        'status' => $request->status,
        'pricing_rule_id' => $request->pricingMethod,
        'check_in_time' => $request->checkInTime,
        'check_out_time' => $request->checkOutTime,
        'tax1' => $request->tax1,
        'tax2' => $request->tax2,
        'description' => $request->description,
    ]);

    // إعادة التوجيه مع رسالة نجاح
    return redirect()->route('rental_management.Settings.Add_Type.index')->with('success', 'تم تحديث البيانات بنجاح.');
}
public function destroy($id)
{
    // جلب نوع الوحدة بناءً على الـ ID
    $type = UnitType::findOrFail($id);

    // حذف النوع
    $type->delete();

    // إعادة التوجيه مع رسالة نجاح
    return redirect()->route('rental_management.Settings.Add_Type.index')->with('success', 'تم حذف نوع الوحدة بنجاح');
}

    }

