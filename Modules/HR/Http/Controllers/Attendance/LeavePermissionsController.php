<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Models\LeavePermissions;
use Illuminate\Http\Request;

class LeavePermissionsController extends Controller
{
    public function index()
    {
        $leavePermissions = LeavePermissions::select()->orderBy('id', 'DESC')->get();
        return view('hr::attendance.leave-permissions.index', compact('leavePermissions'));
    }
    public function create()
    {
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        return view('hr::attendance.leave-permissions.create', compact('employees'));
    }

    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validatedData = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:annual_leave,emergency_leave',
            'leave_type' => 'required|in:late_arrival,early_departure',

            'submission_date' => 'nullable|date',
            'notes' => 'nullable|string|max:1000',
            'attachments' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
        ]);

        try {
            // التحقق من عدم وجود تداخل في التواريخ للموظف نفسه
            $existingLeave = LeavePermissions::where('employee_id', $validatedData['employee_id'])
                ->where(function ($query) use ($validatedData) {
                    $query
                        ->whereBetween('start_date', [$validatedData['start_date'], $validatedData['end_date']])
                        ->orWhereBetween('end_date', [$validatedData['start_date'], $validatedData['end_date']])
                        ->orWhere(function ($q) use ($validatedData) {
                            $q->where('start_date', '<=', $validatedData['start_date'])->where('end_date', '>=', $validatedData['end_date']);
                        });
                })
                ->exists();

            if ($existingLeave) {
                return back()
                    ->withInput()
                    ->withErrors(['date_conflict' => 'يوجد تداخل في التواريخ مع إذن إجازة آخر لنفس الموظف']);
            }

            // إنشاء إذن الإجازة
            $leavePermission = new LeavePermissions();
            $leavePermission->employee_id = $validatedData['employee_id'];
            $leavePermission->start_date = $validatedData['start_date'];
            $leavePermission->end_date = $validatedData['end_date'];
            $leavePermission->type = $validatedData['type'];
            $leavePermission->leave_type = $validatedData['leave_type'];
            $leavePermission->submission_date = $validatedData['submission_date'] ?? now();
            $leavePermission->notes = $validatedData['notes'];

            // رفع المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('leave_permissions', $fileName, 'public');
                $leavePermission->attachments = $filePath;
            }

            $leavePermission->save();

            // إنشاء السجل
            ModelsLog::create([
                'type' => 'leave_permissions',
                'type_id' => $leavePermission->id,
                'type_log' => 'create',
                'description' => 'تم إنشاء إذن إجازة جديد للموظف: ' . $leavePermission->employee->full_name,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('leave_permissions.index')->with('success', 'تم إنشاء إذن الإجازة بنجاح');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()]);
        }
    }

    /**
     * Upload image helper method
     */

    public function edit($id)
    {
        $leavePermission = LeavePermissions::findOrFail($id);
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        return view('hr::attendance.leave-permissions.edit', compact('leavePermission', 'employees'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'type' => 'required',
            'leave_type' => 'required',
        ]);

        $leavePermission = LeavePermissions::findOrFail($id);

        $leavePermission->employee_id = $request->employee_id;
        $leavePermission->start_date = $request->start_date;
        $leavePermission->end_date = $request->end_date;
        $leavePermission->type = $request->type;
        $leavePermission->leave_type = $request->leave_type;
        $leavePermission->submission_date = $request->submission_date;
        $leavePermission->notes = $request->notes;

        if ($request->hasFile('attachments')) {
            if ($leavePermission->attachments != null) {
                unlink('assets/uploads/leave_permissions/' . $leavePermission->attachments);
            }
            $leavePermission->attachments = $this->UploadImage('assets/uploads/leave_permissions', $request->attachments);
        }

        $leavePermission->update();

        ModelsLog::create([
            'type' => 'atendes_log',
            'type_id' => $leavePermission->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم تعديل اذن اجازة',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()
            ->route('leave_permissions.index')
            ->with(['success' => 'تم تعديل اذن الاجازة بنجاح']);
    }

    public function show($id)
    {
        $leavePermission = LeavePermissions::findOrFail($id);
        return view('hr::attendance.leave-permissions.show', compact('leavePermission'));
    }

    public function delete($id)
    {
        $leavePermission = LeavePermissions::findOrFail($id);

        ModelsLog::create([
            'type' => 'atendes_log',
            'type_id' => $leavePermission->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف اذن  اجازة',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        if ($leavePermission->attachments != null) {
            unlink('assets/uploads/leave_permissions/' . $leavePermission->attachments);
        }
        $leavePermission->delete();
        return redirect()
            ->route('leave_permissions.index')
            ->with(['success' => 'تم حذف اذن الاجازة بنجاح']);
    }

    # Helpper
    function uploadImage($folder, $image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time() . rand(1, 99) . '.' . $fileExtension;
        $image->move($folder, $fileName);

        return $fileName;
    } #end of uploadImage
}
