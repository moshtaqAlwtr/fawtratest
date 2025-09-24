<?php

namespace App\Http\Controllers\CustomerAttendance;

use App\Http\Controllers\Controller;
use App\Models\ClientAttendance;
use App\Models\Client;
use Illuminate\Http\Request;

class CustomerAttendanceController extends Controller
{
   public function index(Request $request)
{
    $query = \App\Models\ClientAttendance::with(['client', 'creator']);

    // بحث حسب العميل
    if ($request->filled('client_id')) {
        $query->where('client_id', $request->client_id);
    }
    // بحث حسب الموظف
    if ($request->filled('created_by')) {
        $query->where('created_by', $request->created_by);
    }
    // بحث حسب التاريخ من إلى
    if ($request->filled('from_date')) {
        $query->whereDate('date', '>=', $request->from_date);
    }
    if ($request->filled('to_date')) {
        $query->whereDate('date', '<=', $request->to_date);
    }

    $ClientAttendances = $query->latest('date')->get();
    $Clients = \App\Models\Client::all();

    return view('Customer_Attendance.index', compact('ClientAttendances', 'Clients'));
}

    
    public function store(Request $request)
{
    $request->validate([
        'client_id' => 'required|exists:clients,id',
    ]);

    ClientAttendance::create([
        'client_id' => $request->client_id,
        'created_by' => auth()->id(), // إذا كان فيه تسجيل دخول
        'date' => now(),
    ]);

    return redirect()->route('customer_attendance.index')->with('success', 'تم تسجيل الحضور بنجاح!');
}
// تحديث الحضور
public function update(Request $request, $id)
{
    $request->validate([
        'date' => 'required|date',
    ]);
    $attendance = \App\Models\ClientAttendance::findOrFail($id);
    $attendance->date = $request->date;
    $attendance->save();

    return redirect()->route('customer_attendance.index')->with('success', 'تم تعديل بيانات الحضور بنجاح!');
}

// حذف الحضور
public function destroy($id)
{
    $attendance = \App\Models\ClientAttendance::findOrFail($id);
    $attendance->delete();

    return redirect()->route('customer_attendance.index')->with('success', 'تم حذف الحضور بنجاح!');
}

}
