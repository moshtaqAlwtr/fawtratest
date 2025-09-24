<?php

namespace Modules\RentalManagement\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\PricingRule;
use Illuminate\Http\Request;
use App\Models\Log as ModelsLog;
class RentalPriceRuleController extends Controller
{
    public function index()
    {
        $pricingRules = PricingRule::all();
        return view('rentalmanagement::rental_price_rule.index', compact('pricingRules'));
    }


    public function create()
    {
        return view('rentalmanagement::rental_price_rule.create');
    }

    public function edit($id)
    {
        // البحث عن قاعدة التسعير باستخدام ID
        $pricingRule = PricingRule::findOrFail($id);

        // تمرير البيانات إلى صفحة التعديل
        return view('rentalmanagement::rental_price_rule.edit', compact('pricingRule'));
    }

    public function update(Request $request, $id)
    {
        // التحقق من صحة البيانات
        $validatedData = $request->validate([
            'pricingName' => 'nullable|string|max:255',
            'status' => 'nullable|integer|in:1,2',
            'currency' => 'nullable|string',
            'pricingMethod' => 'nullable|integer|in:1,2,3,4,5,6',
            'dailyPrice' => 'nullable|numeric',
        ]);

        // البحث عن قاعدة التسعير
        $rule = PricingRule::findOrFail($id);

        // تحديث البيانات
        $rule->update($validatedData);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('rental_management.rental_price_rule.show', $rule->id)
            ->with('success', 'تم تحديث قاعدة التسعير بنجاح!');

            }public function destroy($id)
            {
                // حذف قاعدة التسعير
                $rule = PricingRule::findOrFail($id);
                $rule->delete();

                // إعادة التوجيه مع رسالة نجاح
                return redirect()->route('rental_management.rental_price_rule.index')
                    ->with('success', 'تم حذف قاعدة التسعير بنجاح!');
                // العثور على قاعدة التسعير باستخدام ID
            }


    public function show($id)
    {
        // استرجاع قاعدة التسعير بناءً على ID
        $rule = PricingRule::findOrFail($id);

        // عرض القاعدة
        return view('rentalmanagement::rental_price_rule.show', compact('rule'));
    }

    public function store(Request $request)
    {

        // التحقق من صحة البيانات
        $validatedData = $request->validate([
            'pricingName' => 'required|string|max:255',
            'status' => 'required|integer|in:1,2',
            'currency' => 'required|string',
            'pricingMethod' => 'required|integer|in:1,2,3,4,5,6',
            'dailyPrice' => 'required|numeric',
        ]);

        // إنشاء قاعدة تسعير جديدة
     $PRICE =   PricingRule::create($validatedData);


                        ModelsLog::create([
    'type' => 'unit',
    'type_id' => $PRICE->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم اضافة  قاعدة تسعير  **' . $PRICE->pricingName . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);
        // إعادة التوجيه بعد الحفظ
        return redirect()->route('rental_management.rental_price_rule.index')
            ->with('success', 'تم إضافة قاعدة التسعير بنجاح!');
    }

    public function getPricingByUnitType($unitTypeId)
    {
        // استرجاع قواعد التسعير المرتبطة بنوع الوحدة
        $pricing = PricingRule::where('unit_type_id', $unitTypeId)->first();

        // التأكد من وجود قواعد تسعير لهذا النوع
        if (!$pricing) {
            return response()->json(['message' => 'Pricing not found'], 404);
        }

        // إعادة الأسعار مع وبدون الضريبة
        return response()->json([
            'price_with_tax' => $pricing->price_with_tax,
            'price_without_tax' => $pricing->price_without_tax
        ]);
    }


}
