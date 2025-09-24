<?php

namespace Modules\RentalManagement\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use App\Models\SeasonalPrice;
use App\Models\UnitType;
use Illuminate\Http\Request;

class SeasonalPricesController extends Controller

    {
        public function index()
        {
            $prices = SeasonalPrice::all(); // جلب جميع البيانات من جدول seasonal_prices
            return view('rentalmanagement::seasonal-prices.index', compact('prices')); // تمرير البيانات إلى العرض
        }
  // في الـController
public function create()
{
    // جلب جميع أنواع الوحدات وقواعد التسعير
    $unitTypes = UnitType::all();
    $pricingRules = PricingRule::all();

    return view('rentalmanagement::seasonal-prices.create', compact('unitTypes', 'pricingRules'));
}
public function show($id)
{
    $seasonalPrice = SeasonalPrice::findOrFail($id); // جلب البيانات من قاعدة البيانات بناءً على المعرف
    return view('rentalmanagement::seasonal-prices.show', compact('seasonalPrice'));
}



    public function store(Request $request)
    {
        // تحقق من صحة البيانات القادمة من الفورم
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'unit_type_id' => 'required|exists:unit_types,id',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'pricing_rule_id' => 'required|exists:pricing_rules,id',
            'days' => 'nullable|array',
        ]);

        // قم بتنسيق بيانات أيام العمل
        $workingDays = [];
        if ($request->has('days')) {
            foreach ($request->input('days') as $dayKey => $dayData) {
                $workingDays[$dayKey] = [
                    'working_day' => isset($dayData['working_day']),
                ];
            }
        }

        // تخزين البيانات في قاعدة البيانات
        SeasonalPrice::create([
            'name' => $validatedData['name'],
            'unit_type_id' => $validatedData['unit_type_id'],
            'date_from' => $validatedData['date_from'],
            'date_to' => $validatedData['date_to'],
            'pricing_rule_id' => $validatedData['pricing_rule_id'],
            'working_days' => $workingDays,
        ]);

        // إعادة توجيه مع رسالة نجاح
        return redirect()->route('rental_management.seasonal-prices.index')->with('success', 'تم إضافة السعر الموسمي بنجاح.');
    }
    public function edit($id)
{
    // جلب السعر الموسمي بناءً على المعرف
    $seasonalPrice = SeasonalPrice::findOrFail($id);

    // جلب أنواع الوحدات وقواعد التسعير
    $unitTypes = UnitType::all();
    $pricingRules = PricingRule::all();

    // إرجاع العرض مع البيانات
    return view('rentalmanagement::seasonal-prices.edit', compact('seasonalPrice', 'unitTypes', 'pricingRules'));
}
public function update(Request $request, $id)
{
    // تحقق من صحة البيانات القادمة من الفورم
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'unit_type_id' => 'required|exists:unit_types,id',
        'date_from' => 'required|date',
        'date_to' => 'required|date|after_or_equal:date_from',
        'pricing_rule_id' => 'required|exists:pricing_rules,id',
        'days' => 'nullable|array',
    ]);

    // جلب السعر الموسمي بناءً على المعرف
    $seasonalPrice = SeasonalPrice::findOrFail($id);

    // تنسيق أيام العمل
    $workingDays = [];
    if ($request->has('days')) {
        foreach ($request->input('days') as $dayKey => $dayData) {
            $workingDays[$dayKey] = [
                'working_day' => isset($dayData['working_day']),
            ];
        }
    }

    // تحديث البيانات في قاعدة البيانات
    $seasonalPrice->update([
        'name' => $validatedData['name'],
        'unit_type_id' => $validatedData['unit_type_id'],
        'date_from' => $validatedData['date_from'],
        'date_to' => $validatedData['date_to'],
        'pricing_rule_id' => $validatedData['pricing_rule_id'],
        'working_days' => $workingDays,
    ]);

    // إعادة توجيه مع رسالة نجاح
    return redirect()->route('rental_management.seasonal-prices.index')->with('success', 'تم تحديث السعر الموسمي بنجاح.');
}
public function destroy($id)
{
    // جلب السعر الموسمي بناءً على المعرف
    $seasonalPrice = SeasonalPrice::findOrFail($id);

    // حذف السعر الموسمي من قاعدة البيانات
    $seasonalPrice->delete();

    // إعادة توجيه مع رسالة نجاح
    return redirect()->route('rental_management.seasonal-prices.index')->with('success', 'تم حذف السعر الموسمي بنجاح.');
}

}


