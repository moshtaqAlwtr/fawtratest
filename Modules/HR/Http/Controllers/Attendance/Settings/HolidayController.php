<?php

namespace Modules\HR\Http\Controllers\Attendance\Settings;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Models\Holiday;
use App\Models\HolidayList;
use App\Models\HolyDayListCustomize;
use App\Models\JopTitle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $query = HolidayList::query();

        if ($request->has('keywords') && !empty($request->keywords)) {
            $query->where('name', 'LIKE', '%' . $request->keywords . '%');
        }

        $holiday_lists = $query->orderBy('id', 'DESC')->get();

        // إذا كان الطلب AJAX، نرجع JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $holiday_lists,
                'html' => view('hr::attendance.settings.holiday.table-content', compact('holiday_lists'))->render()
            ]);
        }

        return view('hr::attendance.settings.holiday.index', compact('holiday_lists'));
    }

    // Route جديد للبحث بـ AJAX
    public function search(Request $request)
    {
        $query = HolidayList::query();

        if ($request->has('keywords') && !empty($request->keywords)) {
            $query->where('name', 'LIKE', '%' . $request->keywords . '%');
        }

        $holiday_lists = $query->orderBy('id', 'DESC')->get();

        return response()->json([
            'success' => true,
            'data' => $holiday_lists,
            'html' => view('hr::attendance.settings.holiday.table-content', compact('holiday_lists'))->render()
        ]);
    }
    public function create()
    {
        return view('hr::attendance.settings.holiday.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|array',
            'holiday_date.*' => 'required|date',
            'named' => 'required|array',
            'named.*' => 'required|string|max:255',
        ]);

        // Create Holiday List
        $holidayList = HolidayList::create([
            'name' => $request->input('name'),
        ]);

        // Create Associated Holidays
        foreach ($request->holiday_date as $index => $date) {
            Holiday::create([
                'holiday_list_id' => $holidayList->id,
                'holiday_date' => $date,
                'named' => $request->named[$index],
            ]);
        }

        ModelsLog::create([
            'type' => 'holiday_list',
            'type_id' => $holidayList->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم اضافة  قائمة العطلات',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // Redirect or Return Response
        return redirect()
            ->route('holiday_lists.index')
            ->with(['success' => 'تمت إضافة قائمة العطلات بنجاح']);
    }



    public function edit($id)
    {
        $holiday_list = HolidayList::findOrFail($id);
        return view('hr::attendance.settings.holiday.edit', compact('holiday_list'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'holiday_date' => 'required|array',
            'holiday_date.*' => 'required|date',
            'named' => 'required|array',
            'named.*' => 'required|string|max:255',
        ]);

        // Create Holiday List
        $holidayList = HolidayList::findOrFail($id)->update([
            'name' => $request->input('name'),
        ]);

        Holiday::where('holiday_list_id', $id)->delete();

        // Create Associated Holidays
        foreach ($request->holiday_date as $index => $date) {
            Holiday::where('holiday_list_id', $id)->create([
                'holiday_list_id' => $id,
                'holiday_date' => $date,
                'named' => $request->named[$index],
            ]);
        }

        ModelsLog::create([
            'type' => 'holiday_list',
            'type_id' => $holidayList->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم تحديث  قائمة العطلات',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        // Redirect or Return Response
        return redirect()
            ->route('holiday_lists.index')
            ->with(['success' => 'تمت تحديث قائمة العطلات بنجاح']);
    }

    public function holyday_employees($id)
    {
        $holiday_list = HolidayList::findOrFail($id);
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
        return view('hr::attendance.settings.holiday.holyday_employees', compact('holiday_list', 'employees', 'branches', 'departments', 'job_titles'));
    }

    public function add_holyday_employees(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            if ($request['use_rules'] === 'employees') {
                $holiday_list_customize = HolyDayListCustomize::updateOrCreate([
                    'holiday_list_id' => $id,
                    'use_rules' => 2,
                ]);
            } elseif ($request['use_rules'] === 'rules') {
                $holiday_list_customize = HolyDayListCustomize::updateOrCreate([
                    'holiday_list_id' => $id,
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'job_title_id' => $request['job_title_id'],
                    'use_rules' => 1,
                ]);
            }

            $holiday_list_customize->employees()->sync($request['employee_id']);

            ModelsLog::create([
                'type' => 'holiday_list',
                'type_id' => $holiday_list_customize->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم اضافة الموظفين لقايمة العطلات',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
            DB::commit();

            return redirect()
                ->route('holiday_lists.show', $id)
                ->with(['success' => 'تمت اضافة الموظفين لقايمة العطلات بنجاح']);
        } catch (\Exception $exception) {
            DB::rollBack();
            return redirect()
                ->back()
                ->with(['error' => $exception->getMessage()]);
        }
    }


     public function show($id)
    {
        try {
            $holiday_list = HolidayList::with(['holidays'])->findOrFail($id);

            // جلب سجل النشاطات
            $logs = ModelsLog::where('type', 'holiday_list')
                ->where('type_id', $id)
                ->with(['user.branch'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });

            return view('hr::attendance.settings.holiday.show', compact('holiday_list', 'logs'));

        } catch (\Exception $e) {
            return redirect()->route('holiday_lists.index')
                ->with('error', 'قائمة العطل غير موجودة');
        }
    }

    /**
     * جلب عدد الموظفين المخصصين لقائمة العطل
     */
    public function employeesCount($id)
    {
        try {
            $holiday_list = HolidayList::findOrFail($id);

            // البحث عن التخصيصات المرتبطة بهذه القائمة
            $customizations = HolyDayListCustomize::where('holiday_list_id', $id)->get();

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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * جلب قائمة الموظفين المخصصين لقائمة العطل
     */
/**
 * جلب قائمة الموظفين المخصصين لقائمة العطل - محسن
 */
public function employeesList($id)
{
    try {
        $holiday_list = HolidayList::findOrFail($id);
        $employees = collect();
        $departments = collect();

        // البحث عن التخصيصات المرتبطة بهذه القائمة
        $customizations = HolyDayListCustomize::where('holiday_list_id', $id)
            ->with(['employees'])
            ->get();

        foreach ($customizations as $customization) {
            if ($customization->use_rules == 2) {
                // الموظفين المخصصين مباشرة
                $directEmployees = $customization->employees()
                    ->with(['department', 'branch', 'job_Title'])
                    ->get();

                foreach ($directEmployees as $employee) {
                    // تأكد من وجود البيانات وإنشاء structure واضح
                    $employeeData = [
                        'id' => $employee->id,
                        'full_name' => $employee->full_name ?? $employee->full_name ?? 'غير محدد',
                        'employee_id' => $employee->id ?? $employee->id ?? 'غير محدد',
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
                        'holidays_count' => $holiday_list->holidays()->count(),
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
                    // تأكد من وجود البيانات وإنشاء structure واضح
                    $employeeData = [
                        'id' => $employee->id,
                        'full_name' => $employee->full_name ?? $employee->full_name ?? 'غير محدد',
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
                        'holidays_count' => $holiday_list->holidays()->count(),
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

        Log::info('Employee data structure:', [
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
                'holiday_list_id' => $id
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error loading employees list:', [
            'holiday_list_id' => $id,
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
     * إزالة موظف من قائمة العطل
     */
    public function removeEmployee($holidayListId, Request $request)
    {
        try {
            DB::beginTransaction();

            $employeeId = $request->input('employee_id');

            // البحث عن التخصيص الذي يحتوي على هذا الموظف
            $customizations = HolyDayListCustomize::where('holiday_list_id', $holidayListId)
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
                    'type' => 'holiday_list',
                    'type_id' => $holidayListId,
                    'type_log' => 'log',
                    'description' => 'تم إزالة موظف من قائمة العطلات',
                    'created_by' => auth()->id(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'تم إزالة الموظف من القائمة بنجاح'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'الموظف غير موجود في هذه القائمة'
                ], 404);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إزالة الموظف',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إضافة موظفين جدد لقائمة العطل
     */
    public function addEmployees($holidayListId, Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        try {
            DB::beginTransaction();

            $holiday_list = HolidayList::findOrFail($holidayListId);

            // البحث عن التخصيص المباشر أو إنشاء واحد جديد
            $customization = HolyDayListCustomize::firstOrCreate([
                'holiday_list_id' => $holidayListId,
                'use_rules' => 2, // مخصص للموظفين مباشرة
            ]);

            // إضافة الموظفين الجدد
            $newEmployees = array_diff($request->employee_ids, $customization->employees()->pluck('employee_id')->toArray());

            if (!empty($newEmployees)) {
                $customization->employees()->attach($newEmployees);

                // تسجيل العملية في السجل
                ModelsLog::create([
                    'type' => 'holiday_list',
                    'type_id' => $holidayListId,
                    'type_log' => 'log',
                    'description' => 'تم إضافة ' . count($newEmployees) . ' موظف جديد لقائمة العطلات',
                    'created_by' => auth()->id(),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الموظفين بنجاح',
                'added_count' => count($newEmployees)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة الموظفين',
                'error' => $e->getMessage()
            ], 500);
        }
    }



    /**
     * جلب إحصائيات قائمة العطل
     */
    public function getStatistics($id)
    {
        try {
            $holiday_list = HolidayList::findOrFail($id);

            // عدد أيام العطل
            $holidaysCount = $holiday_list->holidays()->count();

            // عدد الموظفين المخصصين
            $employeesCount = $this->getEmployeesCountForList($id);

            // عدد الأقسام المتأثرة
            $departmentsCount = $this->getAffectedDepartmentsCount($id);

            // آخر تحديث
            $lastUpdate = $holiday_list->updated_at;

            return response()->json([
                'success' => true,
                'statistics' => [
                    'holidays_count' => $holidaysCount,
                    'employees_count' => $employeesCount,
                    'departments_count' => $departmentsCount,
                    'last_update' => $lastUpdate->diffForHumans(),
                    'creation_date' => $holiday_list->created_at->format('Y-m-d'),
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الإحصائيات'
            ], 500);
        }
    }

    /**
     * دالة مساعدة لحساب عدد الموظفين
     */
    private function getEmployeesCountForList($holidayListId)
    {
        $customizations = HolyDayListCustomize::where('holiday_list_id', $holidayListId)->get();
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
    private function getAffectedDepartmentsCount($holidayListId)
    {
        $customizations = HolyDayListCustomize::where('holiday_list_id', $holidayListId)->get();
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
}
