<?php

namespace Modules\Client\Http\Controllers\LoyaltyPoints;
use App\Http\Controllers\Controller;
use App\Models\BalanceType;
use App\Models\LoyaltySetting;
use Illuminate\Http\Request;

class LoyaltyPointsSittingController extends Controller
{
public function create(){
$balanceTypes = BalanceType::all();
    return view('client::loyalty_points.sitting.create', compact('balanceTypes'));
}
public function store(Request $request)
{
    // التحقق من صحة المدخلات
    $request->validate([
        'minimum_import_points' => 'required|numeric',
        'client_credit_type_id' => 'required|exists:balance_types,id',
        'client_loyalty_conversion_factor' => 'required|numeric',
        'allow_decimal' => 'boolean',
    ]);

    // إنشاء سجل جديد في جدول loyalty_sittings
    $loyaltySetting = new LoyaltySetting();
    $loyaltySetting->minimum_import_points = $request->minimum_import_points;
    $loyaltySetting->client_credit_type_id = $request->client_credit_type_id;
    $loyaltySetting->client_loyalty_conversion_factor = $request->client_loyalty_conversion_factor;
    $loyaltySetting->allow_decimal = $request->allow_decimal ? true : false; // تعيين القيمة بناءً على المدخلات
    $loyaltySetting->save(); // حفظ السجل في قاعدة البيانات

    // إعادة توجيه المستخدم مع رسالة نجاح
    return redirect()->route('sittingLoyalty.sitting')->with('success', 'تم حفظ إعدادات نقاط الولاء بنجاح.');
}
}
