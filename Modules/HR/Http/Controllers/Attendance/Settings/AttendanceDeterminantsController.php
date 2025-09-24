<?php
namespace Modules\HR\Http\Controllers\Attendance\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceDeterminantRequest;
use App\Models\AttendanceDeterminant;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\HolyDayListCustomize;
use App\Models\JopTitle;
use App\Models\Log as ModelsLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AttendanceDeterminantsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = AttendanceDeterminant::query();

        // Search by name
        if ($request->filled('keywords')) {
            $query->where('name', 'like', '%' . $request->keywords . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Load relationships for statistics
        $attendance_determinants = $query
            ->withCount(['locations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('hr::attendance.settings.determinants.index', compact('attendance_determinants'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hr::attendance.settings.determinants.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $this->validateRequest($request);

        try {
            DB::beginTransaction();

            // Process allowed IPs
            if ($request->filled('allowed_ips')) {
                $validatedData['allowed_ips'] = $this->processAllowedIPs($request->allowed_ips);
            }

            // إنشاء محدد الحضور
            $attendanceDeterminant = AttendanceDeterminant::create($validatedData);

            // إضافة سجل النشاط
            $this->createActivityLog('create', $attendanceDeterminant->id, 'تم إضافة محدد الحضور **' . $attendanceDeterminant->name . '**');

            DB::commit();

            return redirect()->route('attendance_determinants.index')->with('success', 'تم إضافة محدد الحضور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
 public function show($id)
    {
        try {
            $attendance_determinants = AttendanceDeterminant::findOrFail($id);

            // جلب سجل النشاطات
            $logs = ModelsLog::where('type', 'attendance_determinant')
                ->where('type_id', $id)
                ->with(['user.branch'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });

            return view('hr::attendance.settings.determinants.show', compact('attendance_determinants', 'logs'));

        } catch (Exception $e) {
            return redirect()->route('attendance_determinants.index')
                ->with('error', 'محدد الحضور غير موجود');
        }
    }

    /**
     * جلب عدد الموظفين المخصصين لمحدد الحضور
     */


    public function employeesCount($id)
    {
        try {
            $attendance_determinant = AttendanceDeterminant::findOrFail($id);

            // البحث عن التخصيصات المرتبطة بهذا المحدد
            $customizations = HolyDayListCustomize::where('attendance_determinant_id', $id)->get();

            $totalEmployees = 0;

            foreach ($customizations as $customization) {
                if ($customization->use_rules == 2) {
                    // مخصصة للموظفين مباشرة
                    $totalEmployees += $customization->employees()->count();
                } elseif ($customization->use_rules == 1) {
                    // مخصصة حسب القواعد (فرع، قسم، منصب)
                    $query = Employee::query();

                    if ($customization->branch_id) {
                        $query->where('branch_id', $customization->branch_id);
                    }

                    if ($customization->department_id) {
                        $query->where('department_id', $customization->department_id);
                    }

                    if ($customization->job_title_id) {
                        $query->where('job_title_id', $customization->job_title_id);
                    }

                    $totalEmployees += $query->count();
                }
            }

            return response()->json([
                'success' => true,
                'count' => $totalEmployees,
                'message' => 'تم جلب العدد بنجاح'
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب قائمة الموظفين المخصصين لمحدد الحضور
     */
    public function employeesList($id)
    {
        try {
            $attendance_determinant = AttendanceDeterminant::findOrFail($id);
            $employees = collect();
            $departments = collect();

            // البحث عن التخصيصات المرتبطة بهذا المحدد
            $customizations = HolyDayListCustomize::where('attendance_determinant_id', $id)
                ->with(['employees'])
                ->get();

            foreach ($customizations as $customization) {
                if ($customization->use_rules == 2) {
                    // الموظفين المخصصين مباشرة
                    $directEmployees = $customization->employees()
                        ->with(['department', 'branch', 'job_Title'])
                        ->get();

                    foreach ($directEmployees as $employee) {
                        $employeeData = [
                            'id' => $employee->id,
                            'full_name' => $employee->full_name ?? $employee->name ?? 'غير محدد',
                            'employee_id' => $employee->employee_id ?? $employee->emp_id ?? 'غير محدد',
                            'department_id' => $employee->department_id ?? null,
                            'department' => $employee->department ? [
                                'id' => $employee->department->id,
                                'name' => $employee->department->name ?? $employee->department->ar_name ?? 'غير محدد'
                            ] : null,
                            'job_title' => $employee->job_Title ? [
                                'id' => $employee->job_Title->id,
                                'name' => $employee->job_Title->name ?? $employee->job_Title->ar_name ?? 'غير محدد'
                            ] : null,
                            'branch' => $employee->branch ? [
                                'id' => $employee->branch->id,
                                'name' => $employee->branch->name ?? $employee->branch->ar_name ?? 'غير محدد'
                            ] : null,
                            'determinant_name' => $attendance_determinant->name,
                            'assignment_type' => 'مباشر'
                        ];

                        $employees->push((object)$employeeData);

                        if ($employee->department) {
                            $departments->push($employee->department);
                        }
                    }

                } elseif ($customization->use_rules == 1) {
                    // الموظفين المخصصين حسب القواعد
                    $query = Employee::with(['department', 'branch', 'job_Title']);

                    if ($customization->branch_id) {
                        $query->where('branch_id', $customization->branch_id);
                    }

                    if ($customization->department_id) {
                        $query->where('department_id', $customization->department_id);
                    }

                    if ($customization->job_title_id) {
                        $query->where('job_title_id', $customization->job_title_id);
                    }

                    $ruleBasedEmployees = $query->get();

                    foreach ($ruleBasedEmployees as $employee) {
                        $employeeData = [
                            'id' => $employee->id,
                            'full_name' => $employee->full_name ?? $employee->name ?? 'غير محدد',
                            'employee_id' => $employee->employee_id ?? $employee->emp_id ?? 'غير محدد',
                            'department_id' => $employee->department_id ?? null,
                            'department' => $employee->department ? [
                                'id' => $employee->department->id,
                                'name' => $employee->department->name ?? $employee->department->ar_name ?? 'غير محدد'
                            ] : null,
                            'job_title' => $employee->job_Title ? [
                                'id' => $employee->job_Title->id,
                                'name' => $employee->job_Title->name ?? $employee->job_Title->ar_name ?? 'غير محدد'
                            ] : null,
                            'branch' => $employee->branch ? [
                                'id' => $employee->branch->id,
                                'name' => $employee->branch->name ?? $employee->branch->ar_name ?? 'غير محدد'
                            ] : null,
                            'determinant_name' => $attendance_determinant->name,
                            'assignment_type' => 'حسب القواعد'
                        ];

                        $employees->push((object)$employeeData);

                        if ($employee->department) {
                            $departments->push($employee->department);
                        }
                    }
                }
            }

            // إزالة التكرارات
            $employees = $employees->unique('id');
            $departments = $departments->unique('id');

            // تحويل البيانات إلى array للتأكد من التوافق
            $employeesArray = $employees->values()->map(function($employee) {
                return (array)$employee;
            })->toArray();

            $departmentsArray = $departments->values()->map(function($dept) {
                return [
                    'id' => $dept->id,
                    'name' => $dept->name ?? $dept->ar_name ?? 'غير محدد'
                ];
            })->toArray();

            Log::info('Attendance Determinant Employee data:', [
                'total_employees' => count($employeesArray),
                'sample_employee' => $employeesArray[0] ?? null,
                'departments_count' => count($departmentsArray)
            ]);

            return response()->json([
                'success' => true,
                'employees' => $employeesArray,
                'departments' => $departmentsArray,
                'total_count' => count($employeesArray),
                'debug_info' => [
                    'customizations_count' => $customizations->count(),
                    'attendance_determinant_id' => $id
                ]
            ]);

        } catch (Exception $e) {
            Log::error('Error loading attendance determinant employees list:', [
                'attendance_determinant_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إزالة موظف من محدد الحضور
     */
    public function removeEmployee($attendanceDeterminantId, Request $request)
    {
        try {
            DB::beginTransaction();

            $employeeId = $request->input('employee_id');

            // البحث عن التخصيص الذي يحتوي على هذا الموظف
            $customizations = HolyDayListCustomize::where('attendance_determinant_id', $attendanceDeterminantId)
                ->where('use_rules', 2) // فقط المخصصين مباشرة
                ->get();

            $removed = false;
            foreach ($customizations as $customization) {
                if ($customization->employees()->where('employee_id', $employeeId)->exists()) {
                    $customization->employees()->detach($employeeId);
                    $removed = true;
                    break;
                }
            }

            if ($removed) {
                // تسجيل العملية في السجل
                ModelsLog::create([
                    'type' => 'attendance_determinant',
                    'type_id' => $attendanceDeterminantId,
                    'type_log' => 'log',
                    'description' => 'تم إزالة موظف من محدد الحضور',
                    'created_by' => auth()->id(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'تم إزالة الموظف من المحدد بنجاح'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'الموظف غير موجود في هذا المحدد'
                ], 404);
            }

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إزالة الموظف',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة موظفين جدد لمحدد الحضور
     */
    public function addEmployees($attendanceDeterminantId, Request $request)
{
    // ✅ التحقق من البيانات
    $request->validate([
        'employee_id'   => 'nullable|array',
        'employee_id.*' => 'exists:employees,id'
    ]);

    try {
        DB::beginTransaction();

        // ✅ جلب محدد الحضور
        $attendance_determinant = AttendanceDeterminant::findOrFail($attendanceDeterminantId);

        // ✅ البحث عن التخصيص المباشر أو إنشاء واحد جديد
        $customization = HolyDayListCustomize::firstOrCreate(
            [
                'attendance_determinant_id' => $attendanceDeterminantId,
                'use_rules' => 2,
            ],
            [
                'holiday_list_id' => 1, // ⚠️ لو عندك قيمة ديناميكية مررها هنا
            ]
        );

        // ✅ استخراج الموظفين الجدد فقط
        $existingEmployees = $customization->employees()->pluck('employee_id')->toArray();
        $newEmployees = array_diff($request->employee_id, $existingEmployees);

        if (!empty($newEmployees)) {
            $customization->employees()->attach($newEmployees);

            // ✅ تسجيل العملية في السجل
            ModelsLog::create([
                'type'        => 'attendance_determinant',
                'type_id'     => $attendanceDeterminantId,
                'type_log'    => 'log',
                'description' => 'تم إضافة ' . count($newEmployees) . ' موظف جديد لمحدد الحضور',
                'created_by'  => auth()->id(),
            ]);
        }

        DB::commit();

        // ✅ إعادة التوجيه إلى show
        return redirect()
            ->route('attendance_determinants.show', $attendanceDeterminantId)
            ->with('success', 'تم إضافة الموظفين بنجاح');

    } catch (Exception $e) {
        DB::rollBack();

        return redirect()
            ->back()
            ->with('error', 'حدث خطأ أثناء إضافة الموظفين: ' . $e->getMessage());
    }
}


    /**
     * جلب إحصائيات محدد الحضور
     */
    public function getStatistics($id)
    {
        try {
            $attendance_determinant = AttendanceDeterminant::findOrFail($id);

            // عدد الموظفين المخصصين
            $employeesCount = $this->getEmployeesCountForDeterminant($id);

            // عدد الأقسام المتأثرة
            $departmentsCount = $this->getAffectedDepartmentsCount($id);

            // عدد تسجيلات الحضور
            $attendanceRecordsCount = $attendance_determinant->attendances_count ?? 0;

            // آخر تحديث
            $lastUpdate = $attendance_determinant->updated_at;

            return response()->json([
                'success' => true,
                'statistics' => [
                    'employees_count' => $employeesCount,
                    'departments_count' => $departmentsCount,
                    'attendance_records_count' => $attendanceRecordsCount,
                    'last_update' => $lastUpdate->diffForHumans(),
                    'creation_date' => $attendance_determinant->created_at->format('Y-m-d'),
                ]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات'
            ], 500);
        }
    }

    /**
     * صفحة إدارة الموظفين لمحدد الحضور
     */

    public function previewRules(Request $request)
    {
        try {
            $query = Employee::with(['department', 'branch', 'job_Title']);

            // تطبيق الفلاتر
            if ($request->branch_id) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->department_id) {
                $query->where('department_id', $request->department_id);
            }

            if ($request->job_title_id) {
                $query->where('job_title_id', $request->job_title_id);
            }

            // جلب الموظفين
            $employees = $query->get();

            // استبعاد الموظفين المحددين
            if ($request->excluded_employee_id && is_array($request->excluded_employee_id)) {
                $employees = $employees->whereNotIn('id', $request->excluded_employee_id);
            }

            // تحويل البيانات للعرض
            $employeesData = $employees->map(function($employee) {
                return [
                    'id' => $employee->id,
                    'full_name' => $employee->full_name ?? $employee->name ?? 'غير محدد',
                    'department' => $employee->department ? [
                        'id' => $employee->department->id,
                        'name' => $employee->department->name ?? $employee->department->ar_name ?? 'غير محدد'
                    ] : null,
                    'branch' => $employee->branch ? [
                        'id' => $employee->branch->id,
                        'name' => $employee->branch->name ?? $employee->branch->ar_name ?? 'غير محدد'
                    ] : null,
                    'job_title' => $employee->job_Title ? [
                        'id' => $employee->job_Title->id,
                        'name' => $employee->job_Title->name ?? $employee->job_Title->ar_name ?? 'غير محدد'
                    ] : null,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'employees' => $employeesData,
                'count' => $employeesData->count(),
                'message' => 'تم جلب المعاينة بنجاح'
            ]);

        } catch (Exception $e) {
            Log::error('Error in preview rules:', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معاينة النتائج',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * معالجة إضافة الموظفين لمحدد الحضور (من صفحة الإدارة)
     */
    public function processAddEmployees($attendanceDeterminantId, Request $request)
    {
        try {
            DB::beginTransaction();

            $attendance_determinant = AttendanceDeterminant::findOrFail($attendanceDeterminantId);

            // التحقق من صحة البيانات
            $rules = [
                'use_rules' => 'required|in:rules,employees'
            ];

            if ($request->use_rules === 'employees') {
                $rules['employee_id'] = 'required|array|min:1';
                $rules['employee_id.*'] = 'exists:employees,id';
            } else {
                // التحقق من وجود معيار واحد على الأقل للقواعد
                $request->validate([
                    'use_rules' => 'required|in:rules,employees'
                ]);

                if (!$request->branch_id && !$request->department_id && !$request->job_title_id) {
                    return redirect()->back()
                        ->withErrors(['rules' => 'يجب اختيار معيار واحد على الأقل (فرع، قسم، أو مسمى وظيفي)'])
                        ->withInput();
                }
            }

            $request->validate($rules);

            if ($request->use_rules === 'employees') {
                // التخصيص المباشر للموظفين
                $customization = HolyDayListCustomize::firstOrCreate([
                    'attendance_determinant_id' => $attendanceDeterminantId,
                    'use_rules' => 2, // مخصص للموظفين مباشرة
                ], [
                    'status' => 1
                ]);

                // إضافة الموظفين الجدد
                $existingEmployees = $customization->employees()->pluck('employee_id')->toArray();
                $newEmployees = array_diff($request->employee_id, $existingEmployees);

                if (!empty($newEmployees)) {
                    $customization->employees()->attach($newEmployees);
                    $addedCount = count($newEmployees);
                } else {
                    $addedCount = 0;
                }

                $message = $addedCount > 0 ?
                    "تم تخصيص $addedCount موظف جديد لمحدد الحضور بنجاح" :
                    "جميع الموظفين المحددين مخصصين مسبقاً";

            } else {
                // التخصيص حسب القواعد
                $customization = HolyDayListCustomize::create([
                    'attendance_determinant_id' => $attendanceDeterminantId,
                    'use_rules' => 1, // مخصص حسب القواعد
                    'branch_id' => $request->branch_id ?: null,
                    'department_id' => $request->department_id ?: null,
                    'job_title_id' => $request->job_title_id ?: null,
                    'status' => 1
                ]);

                // إضافة الموظفين المستبعدين إذا كانوا موجودين
                if ($request->excluded_employee_id && is_array($request->excluded_employee_id)) {
                    $customization->employees()->attach($request->excluded_employee_id);
                }

                // حساب عدد الموظفين المتأثرين
                $query = Employee::query();
                if ($request->branch_id) $query->where('branch_id', $request->branch_id);
                if ($request->department_id) $query->where('department_id', $request->department_id);
                if ($request->job_title_id) $query->where('job_title_id', $request->job_title_id);

                $affectedCount = $query->count();
                $excludedCount = count($request->excluded_employee_id ?? []);
                $finalCount = $affectedCount - $excludedCount;

                $message = "تم تطبيق القواعد بنجاح. عدد الموظفين المتأثرين: $finalCount";
            }

            // تسجيل العملية في السجل
            ModelsLog::create([
                'type' => 'attendance_determinant',
                'type_id' => $attendanceDeterminantId,
                'type_log' => 'delete',
                'description' => "تم حذف تخصيص محدد الحضور (تأثر $employeesCount موظف)",
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التخصيص بنجاح',
                'affected_employees' => $employeesCount
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting customization:', [
                'attendance_determinant_id' => $attendanceDeterminantId,
                'customization_id' => $customizationId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف التخصيص'
            ], 500);
        }
    }

    /**
     * جلب الموظفين المخصصين حالياً (لتجنب التكرار)
     */
    public function getCurrentAssignedEmployees($attendanceDeterminantId)
    {
        try {
            $customizations = HolyDayListCustomize::where('attendance_determinant_id', $attendanceDeterminantId)
                ->with(['employees'])
                ->get();

            $assignedEmployeeIds = collect();

            foreach ($customizations as $customization) {
                if ($customization->use_rules == 2) {
                    // الموظفين المخصصين مباشرة
                    $directIds = $customization->employees->pluck('id');
                    $assignedEmployeeIds = $assignedEmployeeIds->merge($directIds);
                } elseif ($customization->use_rules == 1) {
                    // الموظفين من القواعد
                    $query = Employee::query();

                    if ($customization->branch_id) {
                        $query->where('branch_id', $customization->branch_id);
                    }
                    if ($customization->department_id) {
                        $query->where('department_id', $customization->department_id);
                    }
                    if ($customization->job_title_id) {
                        $query->where('job_title_id', $customization->job_title_id);
                    }

                    $ruleBasedIds = $query->pluck('id');

                    // استبعاد المستثنيين
                    $excludedIds = $customization->employees->pluck('id');
                    $ruleBasedIds = $ruleBasedIds->diff($excludedIds);

                    $assignedEmployeeIds = $assignedEmployeeIds->merge($ruleBasedIds);
                }
            }

            return response()->json([
                'success' => true,
                'assigned_employee_ids' => $assignedEmployeeIds->unique()->values()->toArray()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات'
            ], 500);
        }
    }

    /**
     * تطبيق المحدد فوراً على الموظفين المخصصين
     */
    public function applyDeterminant($attendanceDeterminantId)
    {
        try {
            DB::beginTransaction();

            $attendance_determinant = AttendanceDeterminant::findOrFail($attendanceDeterminantId);

            // التحقق من أن المحدد نشط
            if ($attendance_determinant->status != 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن تطبيق محدد حضور غير نشط'
                ], 400);
            }

            // جلب جميع الموظفين المخصصين
            $assignedEmployees = $this->getAssignedEmployeesForDeterminant($attendanceDeterminantId);

            if ($assignedEmployees->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يوجد موظفين مخصصين لهذا المحدد'
                ], 400);
            }

            // تطبيق المحدد على كل موظف
            $appliedCount = 0;
            foreach ($assignedEmployees as $employee) {
                // هنا يمكنك إضافة منطق تطبيق المحدد
                // مثل تحديث جدول العلاقات أو إنشاء سجلات جديدة

                $appliedCount++;
            }

            // تسجيل العملية
            ModelsLog::create([
                'type' => 'attendance_determinant',
                'type_id' => $attendanceDeterminantId,
                'type_log' => 'apply',
                'description' => "تم تطبيق محدد الحضور على $appliedCount موظف",
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "تم تطبيق المحدد على $appliedCount موظف بنجاح",
                'applied_count' => $appliedCount
            ]);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error applying determinant:', [
                'attendance_determinant_id' => $attendanceDeterminantId,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تطبيق المحدد'
            ], 500);
        }
    }

    /**
     * دالة مساعدة لجلب الموظفين المخصصين للمحدد
     */
    private function getAssignedEmployeesForDeterminant($attendanceDeterminantId)
    {
        $employees = collect();
        $customizations = HolyDayListCustomize::where('attendance_determinant_id', $attendanceDeterminantId)
            ->with(['employees'])
            ->get();

        foreach ($customizations as $customization) {
            if ($customization->use_rules == 2) {
                // الموظفين المخصصين مباشرة
                $directEmployees = $customization->employees;
                $employees = $employees->merge($directEmployees);
            } elseif ($customization->use_rules == 1) {
                // الموظفين من القواعد
                $query = Employee::query();

                if ($customization->branch_id) {
                    $query->where('branch_id', $customization->branch_id);
                }
                if ($customization->department_id) {
                    $query->where('department_id', $customization->department_id);
                }
                if ($customization->job_title_id) {
                    $query->where('job_title_id', $customization->job_title_id);
                }

                $ruleBasedEmployees = $query->get();

                // استبعاد المستثنيين
                $excludedIds = $customization->employees->pluck('id')->toArray();
                $ruleBasedEmployees = $ruleBasedEmployees->reject(function($employee) use ($excludedIds) {
                    return in_array($employee->id, $excludedIds);
                });

                $employees = $employees->merge($ruleBasedEmployees);
            }
        }

        return $employees->unique('id');
    }

    public function manageEmployees($id)
    {
        try {
            $attendance_determinant = AttendanceDeterminant::findOrFail($id);

            // جلب جميع الموظفين
            $employees = Employee::with(['department', 'branch', 'job_Title'])->get();

            // جلب الأقسام والفروع والمناصب
            $departments = Department::all();
            $branches = Branch::all();
            $job_titles = JopTitle::all();

            return view('hr::attendance.settings.determinants.manage_employees',
                compact('attendance_determinant', 'employees', 'departments', 'branches', 'job_titles'));

        } catch (Exception $e) {
            return redirect()->route('attendance_determinants.index')
                ->with('error', 'محدد الحضور غير موجود');
        }
    }

    /**
     * دالة مساعدة لحساب عدد الموظفين
     */
    private function getEmployeesCountForDeterminant($attendanceDeterminantId)
    {
        $customizations = HolyDayListCustomize::where('attendance_determinant_id', $attendanceDeterminantId)->get();
        $totalEmployees = 0;

        foreach ($customizations as $customization) {
            if ($customization->use_rules == 2) {
                $totalEmployees += $customization->employees()->count();
            } elseif ($customization->use_rules == 1) {
                $query = Employee::query();

                if ($customization->branch_id) {
                    $query->where('branch_id', $customization->branch_id);
                }

                if ($customization->department_id) {
                    $query->where('department_id', $customization->department_id);
                }

                if ($customization->job_title_id) {
                    $query->where('job_title_id', $customization->job_title_id);
                }

                $totalEmployees += $query->count();
            }
        }

        return $totalEmployees;
    }

    /**
     * دالة مساعدة لحساب عدد الأقسام المتأثرة
     */
    private function getAffectedDepartmentsCount($attendanceDeterminantId)
    {
        $customizations = HolyDayListCustomize::where('attendance_determinant_id', $attendanceDeterminantId)->get();
        $departments = collect();

        foreach ($customizations as $customization) {
            if ($customization->use_rules == 2) {
                $employeeDepartments = $customization->employees()
                    ->with('department')
                    ->get()
                    ->pluck('department')
                    ->filter();
                $departments = $departments->merge($employeeDepartments);
            } elseif ($customization->use_rules == 1 && $customization->department_id) {
                $department = Department::find($customization->department_id);
                if ($department) {
                    $departments->push($department);
                }
            }
        }

        return $departments->unique('id')->count();
    }

    /**
     * حذف محدد الحضور
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $attendance_determinant = AttendanceDeterminant::findOrFail($id);

            // حذف جميع التخصيصات المرتبطة
            $customizations = HolyDayListCustomize::where('attendance_determinant_id', $id)->get();

            foreach ($customizations as $customization) {
                // حذف ربط الموظفين
                $customization->employees()->detach();
                // حذف التخصيص
                $customization->delete();
            }

            // تسجيل العملية في السجل
            ModelsLog::create([
                'type' => 'attendance_determinant',
                'type_id' => $id,
                'type_log' => 'delete',
                'description' => 'تم حذف محدد الحضور: ' . $attendance_determinant->name,
                'created_by' => auth()->id(),
            ]);

            // حذف محدد الحضور
            $attendance_determinant->delete();

            DB::commit();

            return redirect()->route('attendance_determinants.index')
                ->with('success', 'تم حذف محدد الحضور بنجاح');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('attendance_determinants.index')
                ->with('error', 'حدث خطأ أثناء حذف محدد الحضور');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceDeterminant $attendance_determinants)
    {
        return view('hr::attendance.settings.determinants.edit', compact('attendance_determinants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceDeterminant $attendance_determinants)
    {
        $validatedData = $this->validateRequest($request, $attendance_determinants->id);
        $oldName = $attendance_determinants->name; // حفظ الاسم القديم للسجل

        try {
            DB::beginTransaction();

            // Process allowed IPs
            if ($request->filled('allowed_ips')) {
                $validatedData['allowed_ips'] = $this->processAllowedIPs($request->allowed_ips);
            } else {
                $validatedData['allowed_ips'] = null;
            }

            // تحديث محدد الحضور
            $attendance_determinants->update($validatedData);

            // إضافة سجل النشاط
            $logMessage = $oldName !== $validatedData['name'] ? "تم تحديث محدد الحضور من **{$oldName}** إلى **{$validatedData['name']}**" : "تم تحديث محدد الحضور **{$validatedData['name']}**";

            $this->createActivityLog('update', $attendance_determinants->id, $logMessage);

            DB::commit();

            return redirect()->route('attendance_determinants.index')->with('success', 'تم تحديث محدد الحضور بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors(['error' => 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(AttendanceDeterminant $attendance_determinants)
    // {
    //     try {
    //         DB::beginTransaction();

    //         // Check if there are related records
    //         $attendanceCount = $attendance_determinants->attendances()->count();
    //         if ($attendanceCount > 0) {
    //             return back()->withErrors(['error' => 'لا يمكن حذف هذا المحدد لوجود تسجيلات حضور مرتبطة به']);
    //         }

    //         $determinantName = $attendance_determinants->name; // حفظ الاسم قبل الحذف

    //         // حذف محدد الحضور
    //         $attendance_determinants->delete();

    //         // إضافة سجل النشاط
    //         $this->createActivityLog('delete', $attendance_determinants->id, "تم حذف محدد الحضور **{$determinantName}**");

    //         DB::commit();

    //         return redirect()->route('attendance_determinants.index')->with('success', 'تم حذف محدد الحضور بنجاح');
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         return back()->withErrors(['error' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()]);
    //     }
    // }

    /**
     * إنشاء سجل نشاط
     */
    private function createActivityLog($action, $determinantId, $description)
    {
        try {
            ModelsLog::create([
                'type' => 'attendance_determinant',
                'type_id' => $determinantId,
                'type_log' => $action, // create, update, delete
                'description' => $description,
                'created_by' => auth()->id(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // في حالة فشل إنشاء السجل، نسجل الخطأ ولكن لا نوقف العملية
            Log::error('Failed to create activity log: ' . $e->getMessage());
        }
    }

    /**
     * Validate request data
     */
    private function validateRequest(Request $request, $id = null)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'status' => 'required|boolean',
            'enable_ip_verification' => 'nullable|boolean',
            'ip_investigation' => 'nullable|in:1,2',
            'allowed_ips' => 'nullable|string',
            'enable_location_verification' => 'nullable|boolean',
            'location_investigation' => 'nullable|in:1,2',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|numeric|min:1|max:10000',
            'radius_type' => 'nullable|in:1,2',
            'capture_employee_image' => 'nullable|boolean',
            'image_investigation' => 'nullable|in:1,2',
        ];

        // Add unique name rule
        if ($id) {
            $rules['name'] .= '|unique:attendance_determinants,name,' . $id;
        } else {
            $rules['name'] .= '|unique:attendance_determinants,name';
        }

        $messages = [
            'name.required' => 'اسم محدد الحضور مطلوب',
            'name.unique' => 'اسم محدد الحضور موجود مسبقاً',
            'name.max' => 'اسم محدد الحضور لا يجب أن يزيد عن 255 حرف',
            'status.required' => 'حالة محدد الحضور مطلوبة',
            'latitude.between' => 'خط العرض يجب أن يكون بين -90 و 90',
            'longitude.between' => 'خط الطول يجب أن يكون بين -180 و 180',
            'radius.min' => 'نطاق الموقع يجب أن يكون أكبر من 0',
            'radius.max' => 'نطاق الموقع كبير جداً',
        ];

        return $request->validate($rules, $messages);
    }

    /**
     * Process allowed IPs string into array
     */
    private function processAllowedIPs($ipsString)
    {
        if (empty($ipsString)) {
            return null;
        }

        // Split by comma or newline
        $ips = preg_split('/[,\n\r]+/', $ipsString);

        // Clean up IPs
        $cleanedIPs = [];
        foreach ($ips as $ip) {
            $ip = trim($ip);
            if (!empty($ip)) {
                // Basic IP validation
                if (filter_var($ip, FILTER_VALIDATE_IP) || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
                    $cleanedIPs[] = $ip;
                }
            }
        }

        return empty($cleanedIPs) ? null : $cleanedIPs;
    }

    /**
     * Get attendance determinants data for AJAX requests
     */
    public function ajax(Request $request)
    {
        $query = AttendanceDeterminant::query();

        // Search by name
        if ($request->filled('keywords')) {
            $query->where('name', 'like', '%' . $request->keywords . '%');
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('status', 0);
            } elseif ($request->status === 'inactive') {
                $query->where('status', 1);
            }
        }

        $attendance_determinants = $query
            ->withCount(['attendances', 'locations'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $attendance_determinants->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'status' => $item->status,
                    'enable_ip_verification' => $item->enable_ip_verification,
                    'enable_location_verification' => $item->enable_location_verification,
                    'capture_employee_image' => $item->capture_employee_image,
                    'attendances_count' => $item->attendances_count,
                    'locations_count' => $item->locations_count,
                    'created_at' => $item->created_at->format('Y-m-d H:i'),
                ];
            }),
        ]);
    }

    /**
     * Get active attendance determinants for API
     */
    public function getActive()
    {
        $determinants = AttendanceDeterminant::where('status', 0)->select('id', 'name', 'enable_ip_verification', 'enable_location_verification', 'capture_employee_image', 'latitude', 'longitude', 'radius', 'radius_type')->get();

        return response()->json([
            'status' => 'success',
            'data' => $determinants,
        ]);
    }

    /**
     * Validate attendance entry against determinant rules
     */
    public function validateAttendanceEntry(Request $request)
    {
        $request->validate([
            'determinant_id' => 'required|exists:attendance_determinants,id',
            'employee_id' => 'required|integer',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ip_address' => 'nullable|ip',
            'image' => 'nullable|string',
        ]);

        try {
            $determinant = AttendanceDeterminant::findOrFail($request->determinant_id);

            $entryData = [
                'employee_id' => $request->employee_id,
                'timestamp' => now(),
                'location' =>
                    $request->latitude && $request->longitude
                        ? [
                            'latitude' => $request->latitude,
                            'longitude' => $request->longitude,
                        ]
                        : null,
                'ip_address' => $request->ip_address,
                'image' => $request->image,
            ];

            $validation = $determinant->validateAttendanceEntry($request->employee_id, $entryData);

            return response()->json([
                'status' => 'success',
                'validation' => $validation,
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'حدث خطأ أثناء التحقق: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
}
