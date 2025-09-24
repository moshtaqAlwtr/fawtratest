<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceSheetsRequest;
use App\Models\AttendanceDays;
use App\Models\AttendanceSheets;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JopTitle;
use App\Models\Shift;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttendanceSheetsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getAttendanceSheetsAjax($request);
        }
          $departments = Department::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
$branches = Branch::select('id', 'name')->get();

        $attendanceSheets = $this->getFilteredAttendanceSheets($request);



        return view('hr::attendance.attendance_sheets.index', compact('attendanceSheets','branches','departments','job_titles'));
    }

    public function getAttendanceSheetsAjax(Request $request)
    {
        try {
            $attendanceSheets = $this->getFilteredAttendanceSheets($request);

            $html = view('hr::attendance.attendance_sheets.table_rows', compact('attendanceSheets'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $attendanceSheets->count(),
                'message' => null,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء تحميل البيانات: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    private function getFilteredAttendanceSheets(Request $request)
{
    $query = AttendanceSheets::query();

    // Filter by employee name or code
    if ($request->filled('keywords')) {
        $keywords = $request->input('keywords');
        $query->whereHas('employees', function ($q) use ($keywords) {
            $q->where('first_name', 'LIKE', "%{$keywords}%")
              ->orWhere('id', 'LIKE', "%{$keywords}%");
        });
    }

    // Filter by date range
    if ($request->filled('from_date')) {
        $query->where('from_date', '>=', $request->input('from_date'));
    }

    if ($request->filled('to_date')) {
        $query->where('to_date', '<=', $request->input('to_date'));
    }

    // Filter by status
    if ($request->filled('status')) {
        $query->where('status', $request->input('status'));
    }

    // Filter by department
    if ($request->filled('department')) {
        $department = $request->input('department');
        $query->whereHas('employees', function ($q) use ($department) {
            $q->where('department_id', $department);
        });
    }

    // Filter by branch
    if ($request->filled('branch')) {
        $branch = $request->input('branch');
        $query->whereHas('employees', function ($q) use ($branch) {
            $q->where('branch_id', $branch);
        });
    }

    // Filter by job title
    if ($request->filled('job_title')) {
        $jobTitle = $request->input('job_title');
        $query->whereHas('employees', function ($q) use ($jobTitle) {
            $q->where('job_title_id', $jobTitle);
        });
    }

    return $query->orderBy('id', 'DESC')->paginate(15);
}

    public function create()
    {
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
        $shifts = Shift::select('id', 'name')->get();
        return view('hr::attendance.attendance_sheets.create', compact('employees', 'branches', 'departments', 'job_titles', 'shifts'));
    }

    public function show($id)
    {
        $attendanceSheet = AttendanceSheets::with([
            'employees',
            'employees.branch',
            'employees.department',
            'employees.job_title',
            'employees.shift',
            'attendanceDays', // أضف هذه
        ])->findOrFail($id);

        // جلب أيام الحضور مع الفلاتر
        $query = AttendanceDays::whereHas('employee', function ($q) use ($attendanceSheet) {
            $q->whereIn('id', $attendanceSheet->employees->pluck('id'));
        })->whereBetween('attendance_date', [$attendanceSheet->from_date, $attendanceSheet->to_date]);

        if (request('employee_filter')) {
            $query->where('employee_id', request('employee_filter'));
        }

        if (request('status_filter')) {
            $query->where('status', request('status_filter'));
        }

        $attendanceDays = $query->with('employee')->orderBy('attendance_date', 'desc')->paginate(50);

        // جلب السجلات
        $logs = ModelsLog::where('type', 'attendance_sheets_log')
            ->where('type_id', $id)
            ->whereHas('attendanceSheet') // استخدام العلاقة الصحيحة
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('hr::attendance.attendance_sheets.show', compact('attendanceSheet', 'attendanceDays', 'logs'));
    }
public function store(AttendanceSheetsRequest $request)
{
    try {
        DB::beginTransaction();

        $successCount = 0;
        $failedEmployees = [];
        $createdAttendanceIds = [];
        $totalDaysCreated = 0;

        // التحقق من وجود employee_id في الطلب
        if (!isset($request['employee_id']) || empty($request['employee_id'])) {
            return redirect()
                ->back()
                ->withInput()
                ->with(['error' => 'يرجى اختيار موظف واحد على الأقل']);
        }

        // إنشاء مصفوفة التواريخ مرة واحدة
        $dateRange = $this->generateDateRange($request['from_date'], $request['to_date']);

        // الحالة الافتراضية
        $defaultStatus = $request['default_status'] ?? 'absent';
        $autoFillShifts = $request['auto_fill_shifts'] ?? false;

        foreach ($request['employee_id'] as $employeeId) {
            // التحقق من وجود دفتر حضور للموظف في نفس الفترة
            $existingAttendance = AttendanceSheets::whereHas('employees', function ($query) use ($employeeId) {
                $query->where('employee_id', $employeeId);
            })
                ->where(function ($query) use ($request) {
                    $query
                        ->whereBetween('from_date', [$request['from_date'], $request['to_date']])
                        ->orWhereBetween('to_date', [$request['from_date'], $request['to_date']])
                        ->orWhere(function ($subQuery) use ($request) {
                            $subQuery->where('from_date', '<=', $request['from_date'])->where('to_date', '>=', $request['to_date']);
                        });
                })
                ->first();

            if ($existingAttendance) {
                // الحصول على اسم الموظف للعرض في رسالة الخطأ
                $employee = \App\Models\Employee::find($employeeId);
                $failedEmployees[] = $employee ? $employee->full_name : "موظف غير معروف (ID: $employeeId)";
                continue;
            }

            // الحصول على معلومات الموظف والوردية
            $employee = \App\Models\Employee::with('shift')->find($employeeId);
            $shiftStartTime = null;
            $shiftEndTime = null;

            if ($autoFillShifts && $employee && $employee->shift) {
                $shiftStartTime = $employee->shift->start_time;
                $shiftEndTime = $employee->shift->end_time;
            } elseif ($autoFillShifts && $request['shifts_id']) {
                $shift = \App\Models\Shift::find($request['shifts_id']);
                if ($shift) {
                    $shiftStartTime = $shift->start_time;
                    $shiftEndTime = $shift->end_time;
                }
            }

            // إنشاء دفتر حضور جديد لكل موظف
            if ($request['use_rules'] === 'employees') {
                $attendance = AttendanceSheets::create([
                    'from_date' => $request['from_date'],
                    'to_date' => $request['to_date'],
                    'use_rules' => 2,
                ]);
            } elseif ($request['use_rules'] === 'rules') {
                $attendance = AttendanceSheets::create([
                    'from_date' => $request['from_date'],
                    'to_date' => $request['to_date'],
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'job_title_id' => $request['job_title_id'],
                    'shift_id' => $request['shifts_id'],
                    'use_rules' => 1,
                ]);
            }

            // ربط الموظف بدفتر الحضور
            $attendance->employees()->attach($employeeId);

            // إنشاء سجلات يومية باستخدام Bulk Insert
            $attendanceDaysData = [];
            $dayCount = 0;

            foreach ($dateRange as $date) {
                // التحقق من عدم وجود سجل لنفس الموظف ونفس التاريخ
                $existingDay = \App\Models\AttendanceDays::where('employee_id', $employeeId)->where('attendance_date', $date)->exists();

                if (!$existingDay) {
                    $dayData = [
                        'attendance_sheets_id' => $attendance->id, // إضافة معرف دفتر الحضور
                        'employee_id' => $employeeId,
                        'attendance_date' => $date,
                        'status' => $defaultStatus,
                        'start_shift' => $shiftStartTime,
                        'end_shift' => $shiftEndTime,
                        'login_time' => null,
                        'logout_time' => null,
                        'absence_type' => $defaultStatus === 'absent' ? 1 : null,
                        'absence_balance' => null,
                        'notes' => 'تم إنشاؤه تلقائياً من دفتر الحضور',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $attendanceDaysData[] = $dayData;
                    $dayCount++;
                }
            }

            // إدراج السجلات دفعة واحدة إذا كان هناك سجلات للإدراج
            if (!empty($attendanceDaysData)) {
                \App\Models\AttendanceDays::insert($attendanceDaysData);
                $totalDaysCreated += $dayCount;
            }

            // إضافة السجل
            ModelsLog::create(attributes: [
                'type' => 'attendance_sheets_log',
                'type_id' => $attendance->id,
                'type_log' => 'log',
                'description' => "تم اضافة دفتر حضور للموظف: {$employee->full_name} (ID: {$employeeId}) مع {$dayCount} يوم حضور",
                'created_by' => auth()->id(),
            ]);

            $successCount++;
            $createdAttendanceIds[] = $attendance->id;
        }

        DB::commit();

        // تحديد نوع الرسالة بناءً على النتائج
        if ($successCount > 0 && empty($failedEmployees)) {
            // جميع الموظفين تم إضافتهم بنجاح
            return redirect()
                ->route('attendance_sheets.index')
                ->with(['success' => "تم إنشاء {$successCount} دفتر حضور بنجاح مع {$totalDaysCreated} سجل يومي!"]);
        } elseif ($successCount > 0 && !empty($failedEmployees)) {
            // بعض الموظفين تم إضافتهم والبعض فشل
            $failedList = implode('، ', $failedEmployees);
            return redirect()
                ->route('attendance_sheets.index')
                ->with([
                    'warning' => "تم إنشاء {$successCount} دفتر حضور بنجاح مع {$totalDaysCreated} سجل يومي. لكن لم يتم إضافة دفاتر للموظفين التاليين لأن لديهم دفاتر حضور في نفس الفترة: {$failedList}",
                ]);
        } else {
            // جميع الموظفين فشلوا
            $failedList = implode('، ', $failedEmployees);
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' => "لا يمكن إنشاء دفاتر حضور للموظفين المحددين لأن لديهم دفاتر حضور في نفس الفترة: {$failedList}",
                ]);
        }
    } catch (\Exception $exception) {
        DB::rollBack();

        return redirect()
            ->back()
            ->withInput()
            ->with(['error' => 'حدث خطأ ما يرجى المحاولة لاحقاً. تفاصيل الخطأ: ' . $exception->getMessage()]);
    }
}

/**
 * إنشاء مصفوفة من التواريخ بين تاريخين
 */
private function generateDateRange($fromDate, $toDate)
{
    $dates = [];
    $startDate = new \DateTime($fromDate);
    $endDate = new \DateTime($toDate);

    while ($startDate <= $endDate) {
        $dates[] = $startDate->format('Y-m-d');
        $startDate->modify('+1 day');
    }

    return $dates;
}
    /**
     * التحقق من التداخل في التواريخ (يمكن استخدامها لتحسين الأداء)
     */
    private function getExistingAttendanceDates($employeeId, $fromDate, $toDate)
    {
        return \App\Models\AttendanceDays::where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->pluck('attendance_date')
            ->toArray();
    }
    public function edit($id)
    {
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
        $shifts = Shift::select('id', 'name')->get();
        $attendanceSheet = AttendanceSheets::findOrFail($id);
        return view('hr::attendance.attendance_sheets.edit', compact('attendanceSheet', 'employees', 'branches', 'departments', 'job_titles', 'shifts'));
    }


public function update(AttendanceSheetsRequest $request, $id)
{
    try {
        DB::beginTransaction();

        $attendance = AttendanceSheets::findOrFail($id);

        // الحصول على الموظفين المرتبطين حالياً
        $currentEmployeeIds = $attendance->employees()->pluck('employee_id')->toArray();
        $newEmployeeIds = $request['employee_id'] ?? [];

        // تحديث بيانات دفتر الحضور
        if ($request['use_rules'] === 'employees') {
            $attendance->update([
                'from_date' => $request['from_date'],
                'to_date' => $request['to_date'],
                'use_rules' => 2,
                'branch_id' => null,
                'department_id' => null,
                'job_title_id' => null,
                'shift_id' => null,
            ]);
        } elseif ($request['use_rules'] === 'rules') {
            $attendance->update([
                'from_date' => $request['from_date'],
                'to_date' => $request['to_date'],
                'branch_id' => $request['branch_id'],
                'department_id' => $request['department_id'],
                'job_title_id' => $request['job_title_id'],
                'shift_id' => $request['shifts_id'],
                'use_rules' => 1,
            ]);
        }

        // حذف أيام الحضور للموظفين المحذوفين
        $removedEmployeeIds = array_diff($currentEmployeeIds, $newEmployeeIds);
        if (!empty($removedEmployeeIds)) {
            \App\Models\AttendanceDays::where('attendance_sheets_id', $attendance->id)
                ->whereIn('employee_id', $removedEmployeeIds)
                ->delete();
        }

        // إنشاء مصفوفة التواريخ الجديدة
        $dateRange = $this->generateDateRange($request['from_date'], $request['to_date']);

        // الحالة الافتراضية
        $defaultStatus = $request['default_status'] ?? 'absent';
        $autoFillShifts = $request['auto_fill_shifts'] ?? false;

        $totalDaysCreated = 0;

        // معالجة الموظفين الجدد والموظفين الحاليين
        foreach ($newEmployeeIds as $employeeId) {
            // الحصول على معلومات الموظف والوردية
            $employee = \App\Models\Employee::with('shift')->find($employeeId);
            $shiftStartTime = null;
            $shiftEndTime = null;

            if ($autoFillShifts && $employee && $employee->shift) {
                $shiftStartTime = $employee->shift->start_time;
                $shiftEndTime = $employee->shift->end_time;
            } elseif ($autoFillShifts && $request['shifts_id']) {
                $shift = \App\Models\Shift::find($request['shifts_id']);
                if ($shift) {
                    $shiftStartTime = $shift->start_time;
                    $shiftEndTime = $shift->end_time;
                }
            }

            // حذف أيام الحضور الحالية للموظف خارج النطاق الجديد
            \App\Models\AttendanceDays::where('attendance_sheets_id', $attendance->id)
                ->where('employee_id', $employeeId)
                ->where(function ($query) use ($request) {
                    $query->where('attendance_date', '<', $request['from_date'])
                        ->orWhere('attendance_date', '>', $request['to_date']);
                })
                ->delete();

            // إنشاء سجلات يومية للتواريخ الجديدة
            $attendanceDaysData = [];
            $dayCount = 0;

            foreach ($dateRange as $date) {
                // التحقق من عدم وجود سجل لنفس الموظف ونفس التاريخ في هذا الدفتر
                $existingDay = \App\Models\AttendanceDays::where('attendance_sheets_id', $attendance->id)
                    ->where('employee_id', $employeeId)
                    ->where('attendance_date', $date)
                    ->exists();

                if (!$existingDay) {
                    $dayData = [
                        'attendance_sheets_id' => $attendance->id,
                        'employee_id' => $employeeId,
                        'attendance_date' => $date,
                        'status' => $defaultStatus,
                        'start_shift' => $shiftStartTime,
                        'end_shift' => $shiftEndTime,
                        'login_time' => null,
                        'logout_time' => null,
                        'absence_type' => $defaultStatus === 'absent' ? 1 : null,
                        'absence_balance' => null,
                        'notes' => 'تم إنشاؤه تلقائياً من دفتر الحضور',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $attendanceDaysData[] = $dayData;
                    $dayCount++;
                }
            }

            // إدراج السجلات الجديدة دفعة واحدة
            if (!empty($attendanceDaysData)) {
                \App\Models\AttendanceDays::insert($attendanceDaysData);
                $totalDaysCreated += $dayCount;
            }
        }

        // تحديث ربط الموظفين
        $attendance->employees()->sync($newEmployeeIds);

        // إضافة السجل
        ModelsLog::create([
            'type' => 'attendance_sheets_log',
            'type_id' => $attendance->id,
            'type_log' => 'log',
            'description' => "تم تعديل دفتر حضور مع {$totalDaysCreated} سجل يومي جديد",
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        return redirect()
            ->route('attendance_sheets.index')
            ->with(['success' => "تم تعديل دفتر الحضور بنجاح مع {$totalDaysCreated} سجل يومي!"]);

    } catch (\Exception $exception) {
        DB::rollBack();
        return redirect()
            ->back()
            ->withInput()
            ->with(['error' => 'حدث خطأ ما يرجى المحاولة لاحقاً. تفاصيل الخطأ: ' . $exception->getMessage()]);
    }
}


    public function delete($id)
    {
        $attendance = AttendanceSheets::findOrFail($id);
        ModelsLog::create([
            'type' => 'attendance_sheets_log',
            'type_id' => $attendance->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم  حذف دفتر حضور',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        if ($attendance->status == 1) {
            return redirect()
                ->route('attendance_sheets.index')
                ->with(['error' => 'لا يمكن حذف دفتر الحضور الموافق عليه !!']);
        }
        $attendance->employees()->detach();
        $attendance->delete();
        return redirect()
            ->route('attendance_sheets.index')
            ->with(['error' => 'تم حذف دفتر الحضور بنجاج !!']);
    }
}
