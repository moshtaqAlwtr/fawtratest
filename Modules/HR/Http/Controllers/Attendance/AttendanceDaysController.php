<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceDaysRequest;
use App\Models\AttendanceDays;
use App\Models\Log as ModelsLog;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JopTitle;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceDaysController extends Controller
{
    public function index()
{
    // جلب جميع البيانات للعرض الأول
    $attendance_days = AttendanceDays::with('employee.department', 'employee.branch')
        ->orderBy('id', 'DESC')
        ->get();

    // جلب الأقسام والفروع للـ select
    $departments = Department::all();
    $branches = Branch::all();

    return view('hr::attendance.attendance_days.index', compact('attendance_days', 'departments', 'branches'));
}

public function filter(Request $request)
{
    $query = AttendanceDays::with('employee.department', 'employee.branch');

    // ✅ فلترة بواسطة الموظف (اسم أو كود)
    if ($request->filled('keywords')) {
        $keywords = $request->keywords;
        $query->whereHas('employee', function ($q) use ($keywords) {
            $q->where('first_name', 'LIKE', "%{$keywords}%")
              ->orWhere('id', 'LIKE', "%{$keywords}%");
        });
    }

    // ✅ فلترة بواسطة التاريخ من
    if ($request->filled('from_date')) {
        $query->where('attendance_date', '>=', $request->from_date);
    }

    // ✅ فلترة بواسطة التاريخ إلى
    if ($request->filled('to_date')) {
        $query->where('attendance_date', '<=', $request->to_date);
    }

    // ✅ فلترة بواسطة الحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // ✅ فلترة بواسطة القسم (يدعم id أو نص)
    if ($request->filled('department')) {
        $department = $request->department;
        $query->whereHas('employee.department', function ($q) use ($department) {
            $q->where('id', $department) // لو جاي id من select
              ->orWhere('name', 'LIKE', "%{$department}%"); // لو نص مكتوب يدوي
        });
    }

    // ✅ فلترة بواسطة الفرع (يدعم id أو نص)
    if ($request->filled('branch')) {
        $branch = $request->branch;
        $query->whereHas('employee.branch', function ($q) use ($branch) {
            $q->where('id', $branch) // لو جاي id من select
              ->orWhere('name', 'LIKE', "%{$branch}%"); // لو نص مكتوب يدوي
        });
    }

    $attendance_days = $query->orderBy('id', 'DESC')->get();

    // ✅ إرجاع البيانات كـ JSON للـ AJAX
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'data' => $attendance_days,
            'html' => view('hr::attendance.attendance_days.table_rows', compact('attendance_days'))->render(),
        ]);
    }

    return view('hr::attendance.attendance_days.index', compact('attendance_days'));
}

    public function create()
    {
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        return view('hr::attendance.attendance_days.create', compact('employees'));
    }

    public function store(AttendanceDaysRequest $request)
    {
        $attendance = new AttendanceDays();
        $attendance->employee_id = $request->employee_id;
        $attendance->attendance_date = $request->attendance_date;
        $attendance->status = $request->status; // حاضر أو غائب أو إجازة

        if ($request->status === 'present') {
            $attendance->start_shift = $request->start_shift;
            $attendance->end_shift = $request->end_shift;
            $attendance->login_time = $request->login_time;
            $attendance->logout_time = $request->logout_time;
        } elseif ($request->status === 'absent') {
            $attendance->absence_type = $request->absence_type;
            $attendance->absence_balance = $request->absence_balance;
        }

        $attendance->notes = $request->notes ?? null;

        $attendance->save();

        ModelsLog::create([
            'type' => 'attendance_days_log',
            'type_id' => $attendance->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم اضافة  حضور يومي',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()
            ->route('attendanceDays.index')
            ->with(['success' => 'تم انشاء ايام الحضور بنجاح']);
    }

    public function edit($id)
    {
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $attendanceDay = AttendanceDays::findOrFail($id);
        return view('hr::attendance.attendance_days.edit', compact('employees', 'attendanceDay'));
    }

public function update(AttendanceDaysRequest $request, $id)
{
    $attendance = AttendanceDays::findOrFail($id);

    // تحديث الحقول الأساسية
    $attendance->employee_id = $request->employee_id;
    $attendance->attendance_date = $request->attendance_date;
    $attendance->status = $request->status;
    $attendance->notes = $request->notes;

    // تحديث الحقول حسب الحالة وتصفير الحقول غير المستخدمة
    if ($request->status === 'present') {
        // تحديث حقول الحضور
        $attendance->start_shift = $request->start_shift;
        $attendance->end_shift = $request->end_shift;
        $attendance->login_time = $request->login_time;
        $attendance->logout_time = $request->logout_time;

        // تصفير حقول الإجازة
        $attendance->absence_type = null;
        $attendance->absence_balance = null;

    } elseif ($request->status === 'absent') {
        // تحديث حقول الإجازة
        $attendance->absence_type = $request->absence_type;
        $attendance->absence_balance = $request->absence_balance;

        // تصفير حقول الحضور
        $attendance->start_shift = null;
        $attendance->end_shift = null;
        $attendance->login_time = null;
        $attendance->logout_time = null;

    } else { // late أو أي حالة أخرى
        // تصفير جميع الحقول الاختيارية
        $attendance->start_shift = null;
        $attendance->end_shift = null;
        $attendance->login_time = null;
        $attendance->logout_time = null;
        $attendance->absence_type = null;
        $attendance->absence_balance = null;
    }

    $attendance->save();

    ModelsLog::create([
        'type' => 'attendance_days_log',
        'type_id' => $attendance->id,
        'type_log' => 'log',
        'description' => 'تم تعديل حضور يومي',
        'created_by' => auth()->id(),
    ]);

    return redirect()
        ->route('attendanceDays.index')
        ->with(['success' => 'تم تعديل ايام الحضور بنجاح']);
}
    public function show($id)
    {
        $attendance_day = AttendanceDays::findOrFail($id);
          $logs = ModelsLog::where('type', 'attendance_days_log')
            ->where('type_id', $id)
            ->whereHas('attendance_days_log') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('hr::attendance.attendance_days.show', compact('attendance_day','logs'));
    }

    public function delete($id)
    {
        $attendance = AttendanceDays::findOrFail($id);
        ModelsLog::create([
            'type' => 'atendes_log',
            'type_id' => $attendance->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف  حضور يومي',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $attendance->delete();
        return redirect()
            ->route('attendanceDays.index')
            ->with(['error' => 'تم حذف ايام الحضور بنجاح']);
    }

    public function calculation()
    {
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
        $shifts = Shift::select('id', 'name')->get();
        return view('hr::attendance.attendance_days.Calculation', compact('employees', 'branches', 'departments', 'job_titles', 'shifts'));
    }

    public function calculateAttendance(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $employees = Employee::query();

        if ($request->has('department_id')) {
            $employees->where('department_id', $request->input('department_id'));
        }
        if ($request->has('job_title')) {
            $employees->where('job_title', $request->input('job_title'));
        }
        if ($request->has('employee_id')) {
            $employees->where('id', $request->input('employee_id'));
        }

        $employees = $employees->get();

        foreach ($employees as $employee) {
            $attendanceDays = AttendanceDays::where('employee_id', $employee->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            $presentDays = $attendanceDays->where('status', 'present')->count();
            $absentDays = $attendanceDays->where('status', 'absent')->count();

            // إخراج النتائج (أو تخزينها)
            echo "Employee: {$employee->name}, Present: $presentDays, Absent: $absentDays";
        }
    }

    /* Helpers --------------------------------------------------------------*/

    public function calculateWorkHours($loginTime, $logoutTime)
    {
        $login = Carbon::parse($loginTime);
        $logout = Carbon::parse($logoutTime);

        $totalDuration = $login->diff($logout);

        $hours = $totalDuration->h;
        $minutes = $totalDuration->i;

        return [
            'hours' => $hours,
            'minutes' => $minutes,
        ];
    }
}
