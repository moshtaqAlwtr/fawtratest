<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\EmployeeTarget;
use App\Models\Target;
use Illuminate\Http\Request;

class EmployeeTargetController extends Controller
{
    public function index()
    {
        $employees = User::where('role','employee')->get(); // يمكنك تخصيصهم حسب النوع إذا أردت
        return view('employee_targets.index', compact('employees'));
    }

 

public function showGeneralTarget()
{
    // جلب الهدف الأول أو إنشائه إذا لم يكن موجوداً
    $target = Target::firstOrCreate(
        ['id' => 1],
        ['value' => 30000, 'description' => 'الهدف العام']
    );
    
    return view('employee_targets.general', compact('target'));
}

public function updateGeneralTarget(Request $request)
{
    $request->validate([
        'value' => 'required|numeric',
        
    ]);

    $target = Target::updateOrCreate(
        ['id' => 1],
        $request->only(['value'])
    );

    return redirect()->back()->with('success', 'تم تحديث الهدف بنجاح');

    
    return view('employee_targets.general', compact('target'));
    }
    

    public function storeOrUpdate(Request $request)
{
    $request->validate([
        'targets' => 'required|array',
        'targets.*.user_id' => 'required|exists:users,id',
        'targets.*.monthly_target' => 'nullable|numeric|min:0'
    ]);

    foreach ($request->targets as $targetData) {
        // تجاهل الحقول الفارغة
        if (!is_numeric($targetData['monthly_target'])) {
            continue;
        }

        EmployeeTarget::updateOrCreate(
            ['user_id' => $targetData['user_id']],
            ['monthly_target' => $targetData['monthly_target']]
        );
    }

    return redirect()->route('employee_targets.index')->with('success', 'تم تحديث التارقت بنجاح!');
}

}
