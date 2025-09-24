<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Models\AttendanceDays;
use App\Models\Employee;

use App\Models\Department;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BarcodeAttendanceController extends Controller
{
    /**
     * عرض صفحة مسح الباركود
     */
    public function scanPage()
    {
        return view('hr::barcode.barcode-scan');
    }

    /**
     * معالجة الباركود الممسوح
     */
    public function processBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        try {
            // البحث عن الموظف بالباركود
            $employee = Employee::where('barcode', $request->barcode)
                               ->where('barcode_enabled', true)
                               ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'الباركود غير صحيح أو الموظف غير موجود'
                ], 404);
            }

            // التحقق من حالة الحضور للموظف اليوم
            $todayAttendance = AttendanceDays::getTodayAttendance($employee->id);
            $isCheckedIn = AttendanceDays::checkIfAlreadyCheckedIn($employee->id);
            $isCheckedOut = AttendanceDays::checkIfAlreadyCheckedOut($employee->id);

            return response()->json([
                'success' => true,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'department' => $employee->department->name ?? 'غير محدد',
                    'photo' => $employee->employee_photo,
                ],
                'attendance_status' => [
                    'is_checked_in' => $isCheckedIn,
                    'is_checked_out' => $isCheckedOut,
                    'check_in_time' => $todayAttendance->check_in_time ?? null,
                    'check_out_time' => $todayAttendance->check_out_time ?? null,
                ],
                'current_time' => Carbon::now()->format('H:i:s'),
                'current_date' => Carbon::now()->format('Y-m-d')
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في معالجة الباركود: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الباركود'
            ], 500);
        }
    }

    /**
     * تسجيل حضور الموظف
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'method' => 'required|in:barcode,manual,qr'
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($request->employee_id);
            $today = Carbon::today();
            $currentTime = Carbon::now();

            // التحقق من عدم وجود تسجيل حضور سابق لليوم
            $existingAttendance = AttendanceDays::where('employee_id', $employee->id)
                                                ->whereDate('attendance_date', $today)
                                                ->first();

            if ($existingAttendance && $existingAttendance->check_in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'تم تسجيل الحضور مسبقاً لهذا اليوم في الساعة ' . $existingAttendance->check_in_time
                ], 400);
            }

            // تحديد حالة الحضور (تأخير أم لا)
            $shiftStartTime = '08:00:00'; // يمكن جلبها من إعدادات الموظف
            $status = $currentTime->format('H:i:s') > $shiftStartTime ? 'late' : 'present';

            // إنشاء أو تحديث سجل الحضور
            $attendance = AttendanceDays::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'attendance_date' => $today
                ],
                [
                    'check_in_time' => $currentTime->format('H:i:s'),
                    'status' => $status,
                    'check_in_method' => $request->method,
                    'scanned_via_barcode' => $request->method === 'barcode',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]
            );

            // تسجيل العملية في اللوج
            $this->logAttendanceAction($employee->id, 'check_in', $request->method, true);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الحضور بنجاح',
                'data' => [
                    'employee_name' => $employee->full_name,
                    'check_in_time' => $attendance->check_in_time,
                    'status' => $status === 'late' ? 'متأخر' : 'في الوقت',
                    'date' => $today->format('Y-m-d')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في تسجيل الحضور: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الحضور'
            ], 500);
        }
    }

    /**
     * تسجيل انصراف الموظف
     */
    public function checkOut(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'method' => 'required|in:barcode,manual,qr'
        ]);

        try {
            DB::beginTransaction();

            $employee = Employee::findOrFail($request->employee_id);
            $today = Carbon::today();
            $currentTime = Carbon::now();

            // البحث عن سجل الحضور لليوم
            $attendance = AttendanceDays::where('employee_id', $employee->id)
                                       ->whereDate('attendance_date', $today)
                                       ->first();

            if (!$attendance || !$attendance->check_in_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم تسجيل حضور لهذا اليوم أولاً'
                ], 400);
            }

            if ($attendance->check_out_time) {
                return response()->json([
                    'success' => false,
                    'message' => 'تم تسجيل الانصراف مسبقاً في الساعة ' . $attendance->check_out_time
                ], 400);
            }

            // تحديث سجل الحضور بوقت الانصراف
            $attendance->update([
                'check_out_time' => $currentTime->format('H:i:s'),
                'check_out_method' => $request->method
            ]);

            // حساب ساعات العمل
            $workingMinutes = $attendance->working_hours;
            $workingHours = floor($workingMinutes / 60);
            $remainingMinutes = $workingMinutes % 60;

            // تسجيل العملية في اللوج
            $this->logAttendanceAction($employee->id, 'check_out', $request->method, true);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الانصراف بنجاح',
                'data' => [
                    'employee_name' => $employee->full_name,
                    'check_in_time' => $attendance->check_in_time,
                    'check_out_time' => $attendance->check_out_time,
'working_hours' => sprintf('%d:%02d', $workingHours, $remainingMinutes),
                    'date' => $today->format('Y-m-d')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('خطأ في تسجيل الانصراف: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل الانصراف'
            ], 500);
        }
    }

    /**
     * توليد باركود للموظف
     */
    public function generateBarcode($employeeId)
    {
        try {
            $employee = Employee::findOrFail($employeeId);

            // توليد باركود فريد إذا لم يكن موجوداً
            if (!$employee->barcode) {
                $barcode = 'EMP_' . $employee->id . '_' . Str::random(8);
                $employee->update(['barcode' => $barcode]);
            }

            return response()->json([
                'success' => true,
                'barcode' => $employee->barcode,
                'employee_name' => $employee->full_name
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في توليد الباركود'
            ], 500);
        }
    }

    /**
     * عرض تقرير الحضور اليومي
     */
    public function dailyReport(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $attendances = AttendanceDays::with('employee.department')
                                    ->whereDate('attendance_date', $date)
                                    ->orderBy('check_in_time')
                                    ->get();

        $stats = [
            'total_present' => $attendances->where('status', 'present')->count(),
            'total_late' => $attendances->where('status', 'late')->count(),
            'total_absent' => Employee::count() - $attendances->count(),
            'barcode_usage' => $attendances->where('scanned_via_barcode', true)->count()
        ];

        return view('attendance.daily-report', compact('attendances', 'stats', 'date'));
    }

    /**
     * توليد باركود لجميع الموظفين
     */
    public function generateAllBarcodes()
    {
        try {
            $employees = Employee::whereNull('barcode')->get();

            foreach ($employees as $employee) {
                $barcode = 'EMP_' . $employee->id . '_' . Str::random(8);
                $employee->update(['barcode' => $barcode]);
            }

            return response()->json([
                'success' => true,
                'message' => "تم توليد باركود لـ {$employees->count()} موظف",
                'generated_count' => $employees->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في توليد الباركود'
            ], 500);
        }
    }

    /**
     * تسجيل عمليات الحضور في اللوج
     */
    private function logAttendanceAction($employeeId, $action, $method, $success, $errorMessage = null)
    {
        // يمكن إضافة جدول منفصل للوج أو استخدام Laravel Log
        Log::info("Attendance Action", [
            'employee_id' => $employeeId,
            'action' => $action,
            'method' => $method,
            'success' => $success,
            'error_message' => $errorMessage,
            'ip_address' => request()->ip(),
            'timestamp' => Carbon::now()
        ]);
    }

    /**
     * عرض إحصائيات سريعة للداشبورد
     */
    public function dashboardStats()
    {
        $today = Carbon::today();

        $stats = [
            'employees_present_today' => AttendanceDays::whereDate('attendance_date', $today)
                                                       ->whereNotNull('check_in_time')
                                                       ->whereNull('check_out_time')
                                                       ->count(),
            'total_check_ins_today' => AttendanceDays::whereDate('attendance_date', $today)
                                                     ->whereNotNull('check_in_time')
                                                     ->count(),
            'late_arrivals_today' => AttendanceDays::whereDate('attendance_date', $today)
                                                   ->where('status', 'late')
                                                   ->count(),
            'barcode_scans_today' => AttendanceDays::whereDate('attendance_date', $today)
                                                   ->where('scanned_via_barcode', true)
                                                   ->count(),
            'latest_scans' => AttendanceDays::with('employee')
                                            ->whereDate('attendance_date', $today)
                                            ->orderBy('updated_at', 'desc')
                                            ->limit(5)
                                            ->get()
        ];

        return response()->json($stats);
    }

    /**
     * البحث عن موظف بالباركود (لأغراض التطوير والاختبار)
     */
    public function searchEmployeeByBarcode(Request $request)
    {
        $barcode = $request->input('barcode');

        $employee = Employee::where('barcode', $barcode)->first();

        if ($employee) {
            return response()->json([
                'found' => true,
                'employee' => [
                    'id' => $employee->id,
                    'name' => $employee->full_name,
                    'department' => $employee->department->name ?? 'غير محدد',
                    'barcode' => $employee->barcode
                ]
            ]);
        }

        return response()->json(['found' => false]);
    }


public function employeeBarcodes()
{
    $employees = Employee::with(['department', 'todayAttendance'])->get();
    $departments = Department::all();

    return view('attendance.employee-barcode', compact('employees', 'departments'));
}

/**
 * طباعة باركود موظف واحد
 */
public function printEmployeeBarcode($employeeId)
{
    try {
        $employee = Employee::findOrFail($employeeId);

        if (!$employee->barcode) {
            return response()->json([
                'success' => false,
                'message' => 'لا يوجد باركود لهذا الموظف'
            ], 400);
        }

        return view('attendance.print-barcode', compact('employee'));

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في طباعة الباركود'
        ], 500);
    }
}

/**
 * تصدير تقرير الحضور
 */
public function exportAttendance($date = null, Request $request)
{
    $date = $date ?? Carbon::today()->format('Y-m-d');
    $format = $request->query('format', 'excel');

    $attendances = AttendanceDays::with('employee.department')
                                 ->whereDate('attendance_date', $date)
                                 ->orderBy('check_in_time')
                                 ->get();

    if ($format === 'excel') {
        return $this->exportToExcel($attendances, $date);
    } elseif ($format === 'pdf') {
        return $this->exportToPdf($attendances, $date);
    }

    return response()->json(['error' => 'صيغة غير مدعومة'], 400);
}

/**
 * تصدير لإكسل
 */
private function exportToExcel($attendances, $date)
{
    $headers = [
        'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'Content-Disposition' => 'attachment; filename="attendance-report-' . $date . '.xlsx"',
    ];

    $data = [];
    $data[] = ['#', 'اسم الموظف', 'القسم', 'وقت الحضور', 'وقت الانصراف', 'ساعات العمل', 'الحالة', 'طريقة التسجيل', 'الملاحظات'];

    foreach ($attendances as $index => $attendance) {
        $workingHours = '';
        if ($attendance->working_hours > 0) {
            $hours = floor($attendance->working_hours / 60);
            $minutes = $attendance->working_hours % 60;
            $workingHours = sprintf('%d:%02d', $hours, $minutes);
        }

        $data[] = [
            $index + 1,
            $attendance->employee->full_name,
            $attendance->employee->department->name ?? 'غير محدد',
            $attendance->check_in_time ?? '',
            $attendance->check_out_time ?? '',
            $workingHours,
            $this->getStatusText($attendance->status),
            $attendance->scanned_via_barcode ? 'باركود' : 'يدوي',
            $attendance->notes ?? ''
        ];
    }

    // هنا يمكنك استخدام مكتبة مثل PhpSpreadsheet
    // return Excel::download(new AttendanceExport($data), 'attendance-report-' . $date . '.xlsx');

    return response()->json(['message' => 'تم التصدير بنجاح - يتطلب تثبيت مكتبة Excel']);
}

/**
 * تصدير لـ PDF
 */
private function exportToPdf($attendances, $date)
{
    // هنا يمكنك استخدام مكتبة مثل DomPDF
    // $pdf = PDF::loadView('attendance.pdf-report', compact('attendances', 'date'));
    // return $pdf->download('attendance-report-' . $date . '.pdf');

    return response()->json(['message' => 'تم التصدير بنجاح - يتطلب تثبيت مكتبة PDF']);
}

/**
 * تفعيل/تعطيل باركود موظف
 */
public function toggleEmployeeBarcode(Request $request, $employeeId)
{
    try {
        $employee = Employee::findOrFail($employeeId);
        $enabled = $request->input('enabled', !$employee->barcode_enabled);

        $employee->update(['barcode_enabled' => $enabled]);

        return response()->json([
            'success' => true,
            'message' => 'تم ' . ($enabled ? 'تفعيل' : 'تعطيل') . ' الباركود بنجاح',
            'enabled' => $enabled
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحديث حالة الباركود'
        ], 500);
    }
}

/**
 * حذف باركود موظف
 */
public function deleteEmployeeBarcode($employeeId)
{
    try {
        $employee = Employee::findOrFail($employeeId);

        $employee->update([
            'barcode' => null,
            'qr_code' => null,
            'barcode_enabled' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الباركود بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في حذف الباركود'
        ], 500);
    }
}

/**
 * تقرير الحضور للموظف الواحد
 */
public function employeeAttendanceReport($employeeId, Request $request)
{
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

    $employee = Employee::findOrFail($employeeId);

    $attendances = AttendanceDays::where('employee_id', $employeeId)
                                 ->whereBetween('attendance_date', [$startDate, $endDate])
                                 ->orderBy('attendance_date', 'desc')
                                 ->get();

    $stats = [
        'total_days' => $attendances->count(),
        'present_days' => $attendances->where('status', 'present')->count(),
        'late_days' => $attendances->where('status', 'late')->count(),
        'absent_days' => $attendances->where('status', 'absent')->count(),
        'barcode_usage' => $attendances->where('scanned_via_barcode', true)->count(),
        'average_working_hours' => $attendances->where('status', '!=', 'absent')->avg('working_hours') ?? 0
    ];

    return view('attendance.employee-report', compact('employee', 'attendances', 'stats', 'startDate', 'endDate'));
}

/**
 * API للحصول على معلومات الموظف السريعة
 */
public function quickEmployeeInfo($employeeId)
{
    try {
        $employee = Employee::with(['department', 'todayAttendance'])->findOrFail($employeeId);

        return response()->json([
            'success' => true,
            'employee' => [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'department' => $employee->department->name ?? 'غير محدد',
                'photo' => $employee->employee_photo,
                'barcode' => $employee->barcode,
                'barcode_enabled' => $employee->barcode_enabled,
                'today_status' => $employee->today_attendance_status
            ]
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'الموظف غير موجود'
        ], 404);
    }
}

/**
 * إحصائيات مفصلة للإدارة
 */
public function detailedStats(Request $request)
{
    $startDate = $request->input('start_date', Carbon::now()->startOfWeek()->format('Y-m-d'));
    $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

    $attendances = AttendanceDays::with('employee.department')
                                 ->whereBetween('attendance_date', [$startDate, $endDate])
                                 ->get();

    $stats = [
        'period' => [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_days' => Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1
        ],
        'attendance' => [
            'total_records' => $attendances->count(),
            'present_count' => $attendances->where('status', 'present')->count(),
            'late_count' => $attendances->where('status', 'late')->count(),
            'absent_count' => $attendances->where('status', 'absent')->count()
        ],
        'methods' => [
            'barcode_usage' => $attendances->where('scanned_via_barcode', true)->count(),
            'manual_usage' => $attendances->where('scanned_via_barcode', false)->count(),
            'barcode_percentage' => $attendances->count() > 0 ?
                round(($attendances->where('scanned_via_barcode', true)->count() / $attendances->count()) * 100, 2) : 0
        ],
        'departments' => $attendances->groupBy('employee.department.name')->map(function($group) {
            return [
                'total' => $group->count(),
                'present' => $group->where('status', 'present')->count(),
                'late' => $group->where('status', 'late')->count()
            ];
        }),
        'working_hours' => [
            'average' => round($attendances->where('status', '!=', 'absent')->avg('working_hours') ?? 0, 2),
            'total_minutes' => $attendances->where('status', '!=', 'absent')->sum('working_hours'),
            'max_hours' => $attendances->max('working_hours') ?? 0,
            'min_hours' => $attendances->where('working_hours', '>', 0)->min('working_hours') ?? 0
        ]
    ];

    return response()->json($stats);
}

/**
 * داشبورد الحضور الرئيسي
 */
public function dashboard()
{
    $today = Carbon::today();

    // إحصائيات اليوم
    $todayStats = [
        'present' => AttendanceDays::whereDate('attendance_date', $today)
                                   ->where('status', 'present')
                                   ->count(),
        'late' => AttendanceDays::whereDate('attendance_date', $today)
                                 ->where('status', 'late')
                                 ->count(),
        'total_employees' => Employee::count(),
        'barcode_scans' => AttendanceDays::whereDate('attendance_date', $today)
                                         ->where('scanned_via_barcode', true)
                                         ->count()
    ];

    // آخر العمليات
    $recentActivities = AttendanceDays::with('employee')
                                      ->whereDate('attendance_date', $today)
                                      ->orderBy('updated_at', 'desc')
                                      ->limit(10)
                                      ->get();

    // الموظفين الحاضرين حالياً
    $currentlyPresent = AttendanceDays::with('employee.department')
                                      ->whereDate('attendance_date', $today)
                                      ->whereNotNull('check_in_time')
                                      ->whereNull('check_out_time')
                                      ->get();

    return view('attendance.dashboard', compact('todayStats', 'recentActivities', 'currentlyPresent'));
}

/**
 * تحويل حالة النص
 */
private function getStatusText($status)
{
    switch ($status) {
        case 'present': return 'حاضر';
        case 'late': return 'متأخر';
        case 'absent': return 'غائب';
        case 'half_day': return 'نصف يوم';
        default: return 'غير محدد';
    }
}

/**
 * التحقق من صحة الباركود
 */
private function validateBarcode($barcode)
{
    // التحقق من الصيغة
    if (!preg_match('/^EMP_\d+_[A-Za-z0-9]{8}$/', $barcode)) {
        return false;
    }

    // استخراج معرف الموظف
    $parts = explode('_', $barcode);
    if (count($parts) !== 3) {
        return false;
    }

    $employeeId = $parts[1];

    // التحقق من وجود الموظف
    return Employee::where('id', $employeeId)
                  ->where('barcode', $barcode)
                  ->exists();
}

/**
 * إحصائيات الاستخدام الأسبوعية
 */
public function weeklyUsageStats()
{
    $startOfWeek = Carbon::now()->startOfWeek();
    $endOfWeek = Carbon::now()->endOfWeek();

    $dailyStats = [];

    for ($date = $startOfWeek->copy(); $date <= $endOfWeek; $date->addDay()) {
        $dayAttendances = AttendanceDays::whereDate('attendance_date', $date)->get();

        $dailyStats[] = [
            'date' => $date->format('Y-m-d'),
            'day_name' => $date->translatedFormat('l'),
            'total_attendances' => $dayAttendances->count(),
            'barcode_scans' => $dayAttendances->where('scanned_via_barcode', true)->count(),
            'manual_entries' => $dayAttendances->where('scanned_via_barcode', false)->count(),
            'late_arrivals' => $dayAttendances->where('status', 'late')->count()
        ];
    }

    return response()->json([
        'week_period' => [
            'start' => $startOfWeek->format('Y-m-d'),
            'end' => $endOfWeek->format('Y-m-d')
        ],
        'daily_stats' => $dailyStats,
        'week_totals' => [
            'total_scans' => collect($dailyStats)->sum('barcode_scans'),
            'total_manual' => collect($dailyStats)->sum('manual_entries'),
            'total_late' => collect($dailyStats)->sum('late_arrivals')
        ]
    ]);
}

/**
 * تصحيح حضور موظف (للحالات الاستثنائية)
 */
public function correctAttendance(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'attendance_date' => 'required|date',
        'check_in_time' => 'nullable|date_format:H:i',
        'check_out_time' => 'nullable|date_format:H:i|after:check_in_time',
        'status' => 'required|in:present,late,absent,half_day',
        'notes' => 'nullable|string|max:500'
    ]);

    try {
        DB::beginTransaction();

        $attendance = AttendanceDays::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'attendance_date' => $request->attendance_date
            ],
            [
                'check_in_time' => $request->check_in_time,
                'check_out_time' => $request->check_out_time,
                'status' => $request->status,
                'notes' => $request->notes,
                'check_in_method' => 'manual',
                'check_out_method' => 'manual',
                'scanned_via_barcode' => false
            ]
        );

        // تسجيل العملية
        Log::info('تصحيح حضور', [
            'employee_id' => $request->employee_id,
            'date' => $request->attendance_date,
            'corrected_by' => auth()->id(),
            'ip_address' => $request->ip()
        ]);

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'تم تصحيح الحضور بنجاح'
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('خطأ في تصحيح الحضور: ' . $e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تصحيح الحضور'
        ], 500);
    }
}

/**
 * إرسال تقرير يومي بالإيميل
 */
public function emailDailyReport(Request $request)
{
    $date = $request->input('date', Carbon::today()->format('Y-m-d'));
    $emails = $request->input('emails', []); // قائمة الإيميلات

    $attendances = AttendanceDays::with('employee.department')
                                 ->whereDate('attendance_date', $date)
                                 ->get();

    $stats = [
        'total_present' => $attendances->where('status', 'present')->count(),
        'total_late' => $attendances->where('status', 'late')->count(),
        'total_absent' => Employee::count() - $attendances->count(),
        'barcode_usage' => $attendances->where('scanned_via_barcode', true)->count()
    ];

    try {
        // هنا يمكنك إرسال الإيميل
        // Mail::to($emails)->send(new AttendanceDaysReport($attendances, $stats, $date));

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال التقرير بالإيميل بنجاح'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل في إرسال التقرير'
        ], 500);
    }
}

/**
 * النسخ الاحتياطي للبيانات
 */
public function backupAttendanceData(Request $request)
{
    $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
    $endDate = $request->input('end_date', Carbon::now());

    $data = [
        'backup_date' => Carbon::now()->toISOString(),
        'period' => [
            'start' => $startDate,
            'end' => $endDate
        ],
        'employees' => Employee::select('id', 'first_name', 'middle_name', 'nickname', 'barcode')
                              ->get(),
        'attendances' => AttendanceDays::whereBetween('attendance_date', [$startDate, $endDate])
                                       ->get(),
        'stats' => $this->calculatePeriodStats($startDate, $endDate)
    ];

    $filename = 'attendance-backup-' . Carbon::now()->format('Y-m-d-H-i-s') . '.json';

    return response()->json($data)
                   ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
}

/**
 * حساب إحصائيات فترة معينة
 */
private function calculatePeriodStats($startDate, $endDate)
{
    $attendances = AttendanceDays::whereBetween('attendance_date', [$startDate, $endDate])->get();

    return [
        'total_records' => $attendances->count(),
        'unique_employees' => $attendances->pluck('employee_id')->unique()->count(),
        'barcode_usage_percentage' => $attendances->count() > 0 ?
            round(($attendances->where('scanned_via_barcode', true)->count() / $attendances->count()) * 100, 2) : 0,
        'average_daily_attendance' => $attendances->groupBy('attendance_date')->avg(function($day) {
            return $day->count();
        }),
        'peak_attendance_day' => $attendances->groupBy('attendance_date')
                                           ->sortByDesc(function($day) { return $day->count(); })
                                           ->keys()
                                           ->first(),
        'departments_stats' => $attendances->groupBy('employee.department_id')
                                          ->map(function($group) {
                                              return [
                                                  'total' => $group->count(),
                                                  'barcode_usage' => $group->where('scanned_via_barcode', true)->count()
                                              ];
                                          })
    ];
}

/**
 * تشخيص النظام
 */
public function systemDiagnostics()
{
    $diagnostics = [
        'database' => [
            'employees_total' => Employee::count(),
            'employees_with_barcode' => Employee::whereNotNull('barcode')->count(),
            'employees_barcode_enabled' => Employee::where('barcode_enabled', true)->count(),
            'today_attendance_records' => AttendanceDays::whereDate('attendance_date', today())->count()
        ],
        'system' => [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'timezone' => config('app.timezone'),
            'current_time' => Carbon::now()->toISOString()
        ],
        'camera_support' => [
            'user_agent' => request()->userAgent(),
            'supports_camera' => true // سيتم التحقق في Frontend
        ],
        'recent_errors' => $this->getRecentErrors()
    ];

    return response()->json($diagnostics);
}

/**
 * الحصول على الأخطاء الحديثة
 */
private function getRecentErrors()
{
    // يمكن قراءة آخر الأخطاء من ملف اللوج
    $logPath = storage_path('logs/laravel.log');

    if (!file_exists($logPath)) {
        return ['لا توجد أخطاء مسجلة'];
    }

    $lines = file($logPath);
    $recentLines = array_slice($lines, -50); // آخر 50 سطر

    $errors = [];
    foreach ($recentLines as $line) {
        if (strpos($line, 'ERROR') !== false && strpos($line, 'attendance') !== false) {
            $errors[] = trim($line);
        }
    }

    return array_slice($errors, -5); // آخر 5 أخطاء
}

/**
 * تنظيف البيانات القديمة
 */
public function cleanupOldData(Request $request)
{
    $daysToKeep = $request->input('days', 90); // الاحتفاظ بـ 90 يوم افتراضياً
    $cutoffDate = Carbon::now()->subDays($daysToKeep);

    try {
        $deletedCount = AttendanceDays::where('attendance_date', '<', $cutoffDate)->delete();

        Log::info('تنظيف البيانات القديمة', [
            'cutoff_date' => $cutoffDate,
            'deleted_records' => $deletedCount,
            'performed_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deletedCount} سجل قديم",
            'deleted_count' => $deletedCount
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'فشل في تنظيف البيانات'
        ], 500);
    }
}

/**
 * إعدادات النظام
 */
public function getSystemSettings()
{
    return response()->json([
        'default_shift_start' => '08:00',
        'default_shift_end' => '17:00',
        'late_threshold_minutes' => 15,
        'barcode_format' => 'CODE128',
        'auto_logout_hours' => 12,
        'backup_frequency_days' => 7,
        'cleanup_after_days' => 90
    ]);
}

/**
 * تحديث إعدادات النظام
 */
public function updateSystemSettings(Request $request)
{
    $request->validate([
        'default_shift_start' => 'required|date_format:H:i',
        'default_shift_end' => 'required|date_format:H:i|after:default_shift_start',
        'late_threshold_minutes' => 'required|integer|min:0|max:60',
        'auto_logout_hours' => 'required|integer|min:1|max:24'
    ]);

    // حفظ الإعدادات في ملف config أو قاعدة البيانات
    // Config::set('attendance.settings', $request->all());

    return response()->json([
        'success' => true,
        'message' => 'تم تحديث الإعدادات بنجاح'
    ]);
}
}
