<?php

namespace Modules\HR\Http\Controllers\Attendance\Settings;
use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypesController extends Controller
{
    public function index()
    {
        $leave_types = LeaveType::select()->orderBy('id','DESC')->get();
        return view('hr::attendance.settings.leave_types.index',compact('leave_types'));
    }
    public function create()
    {
        return view('hr::attendance.settings.leave_types.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $leaveType = new LeaveType();

        if($request->has('requires_approval')){
            $leaveType->requires_approval = 1;
        }

        if($request->has('replace_weekends')){
            $leaveType->replace_weekends = 1;
        }

        $leaveType->name = $request->name;
        $leaveType->color = $request->color;
        $leaveType->max_days_per_year = $request->max_days_per_year;
        $leaveType->max_consecutive_days = $request->max_consecutive_days;
        $leaveType->description = $request->description;
        $leaveType->applicable_after = $request->applicable_after;

        $leaveType->save();

        return redirect()->route('leave_types.index')->with(['success'=>'تمت إضافة نوع الاجازة بنجاح']);
    }

    public function edit($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('hr::attendance.settings.leave_types.edit',compact('leaveType'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $leaveType = LeaveType::findOrFail($id);

        if($request->has('requires_approval')){
            $leaveType->requires_approval = 1;
        }

        if($request->has('replace_weekends')){
            $leaveType->replace_weekends = 1;
        }

        $leaveType->name = $request->name;
        $leaveType->color = $request->color;
        $leaveType->max_days_per_year = $request->max_days_per_year;
        $leaveType->max_consecutive_days = $request->max_consecutive_days;
        $leaveType->description = $request->description;
        $leaveType->applicable_after = $request->applicable_after;

        $leaveType->update();

        return redirect()->route('leave_types.show',$id)->with(['success'=>'تم تعديل نوع الاجازة بنجاح']);
    }

    public function show($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        return view('hr::attendance.settings.leave_types.show',compact('leaveType'));
    }

    public function delete($id)
    {
        $leaveType = LeaveType::findOrFail($id);
        $leaveType->delete();
        return redirect()->route('leave_types.index')->with(['success'=>'تم حذف نوع الاجازة بنجاح']);
    }

}
