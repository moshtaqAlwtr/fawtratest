<?php

namespace Modules\Client\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\Visit;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function store(Request $request)
{
    // تحقق من البيانات المرسلة
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'client_id' => 'required|exists:clients,id',
        'visit_date' => 'required|date',
        'status' => 'required|in:present,absent',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    // جلب أحدث موقع مسجل للعميل والموظف
    $clientLocation = Location::where('client_id', $request->client_id)
        ->where('employee_id', $request->employee_id)
        ->latest()
        ->first();

    // إذا كان العميل والموظف عندهم موقع مسجل
    if ($clientLocation) {
        // حساب المسافة بين موقع الموظف وموقع العميل
        $distance = $this->calculateDistance(
            $clientLocation->latitude, $clientLocation->longitude,
            $request->latitude, $request->longitude
        );

        // إذا كان الموظف داخل نطاق 100 متر من العميل
        if ($distance < 100) {
            // تسجيل الزيارة
            $visit = Visit::create([
                'employee_id' => $request->employee_id,
                'client_id' => $request->client_id,
                'visit_date' => $request->visit_date,
                'status' => $request->status,
                'employee_latitude' => $request->latitude,
                'employee_longitude' => $request->longitude,
            ]);

            // إرسال إشعار للمدير
            $this->sendNotificationToManager($visit);

            return response()->json($visit, 201);
        } else {
            return response()->json(['message' => 'أنت لست قريبًا من العميل'], 400);
        }
    }

    return response()->json(['message' => 'العميل أو الموظف ليس لديه موقع مسجل'], 400);
}
}
