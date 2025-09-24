<?php

namespace Modules\HR\Http\Controllers\Attendance\Settings;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JopTitle;
use App\Models\Log as ModelsLog;
use App\Models\LeavePolicy;
use App\Models\LeavePolicyCustomize;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeavePoliciesController extends Controller
{
    public function index(Request $request)
    {
        $query = LeavePolicy::query();

        if ($request->has('keywords') && !empty($request->keywords)) {
            $query->where('name', 'LIKE', '%' . $request->keywords . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $leave_policies = $query->orderBy('id', 'DESC')->get();

        // إذا كان الطلب AJAX، نرجع JSON
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'data' => $leave_policies,
                'html' => view('hr::attendance.settings.leave_policies.table-content', compact('leave_policies'))->render()
            ]);
        }

        return view('hr::attendance.settings.leave_policies.index', compact('leave_policies'));
    }

    // Route جديد للبحث بـ AJAX
    public function search(Request $request)
    {
        $query = LeavePolicy::query();

        if ($request->has('keywords') && !empty($request->keywords)) {
            $query->where('name', 'LIKE', '%' . $request->keywords . '%');
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $leave_policies = $query->orderBy('id', 'DESC')->get();

        return response()->json([
            'success' => true,
            'data' => $leave_policies,
            'html' => view('hr::attendance.settings.leave_policies.table-content', compact('leave_policies'))->render()
        ]);
    }

    public function create()
    {
        $leave_types = LeaveType::select('id','name')->get();
        return view('hr::attendance.settings.leave_policies.create',compact('leave_types'));
    }

    public function store(Request $request)
    {
        try{
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|in:0,1',
                'leave_type_id' => 'required|array',
                'leave_type_id.*' => 'required|integer|exists:leave_types,id',
            ]);

            $leave_policy = LeavePolicy::create([
                'name' => $request->name,
                'status' => $request->status,
                'description' => $request->description
            ]);

            $leave_policy->leaveType()->attach($request->leave_type_id);

            ModelsLog::create([
                'type' => 'leave_policy',
                'type_id' => $leave_policy->id,
                'type_log' => 'log',
                'description' => 'تم اضافة سياسة الاجازات ' . $request->name,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('leave_policy.index')->with(['success'=>'تمت اضافة سياسة الاجازات بنجاح']);
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with(['error'=>$exception->getMessage()]);
        }
    }

    public function edit($id)
    {
        $leave_policy = LeavePolicy::findOrFail($id);
        $leave_types = LeaveType::select('id','name')->get();
        return view('hr::attendance.settings.leave_policies.edit',compact('leave_policy','leave_types'));
    }

    public function update(Request $request, $id)
    {
        try{
            DB::beginTransaction();

            $request->validate([
                'name' => 'required|string|max:255',
                'status' => 'required|in:0,1',
                'leave_type_id' => 'required|array',
                'leave_type_id.*' => 'required|integer|exists:leave_types,id',
            ]);

            $leave_policy = LeavePolicy::findOrFail($id);

            $leave_policy->update([
                'name' => $request->name,
                'status' => $request->status,
                'description' => $request->description
            ]);

            $leave_policy->leaveType()->sync($request->leave_type_id);

            ModelsLog::create([
                'type' => 'leave_policy',
                'type_id' => $leave_policy->id,
                'type_log' => 'log',
                'description' => 'تم تعديل سياسة الاجازات ' . $request->name,
                'created_by' => auth()->id(),
            ]);

            DB::commit();
            return redirect()->route('leave_policy.index')->with(['success'=>'تم تعديل سياسة الاجازات بنجاح']);
        }
        catch(\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with(['error'=>$exception->getMessage()]);
        }
    }

    public function show($id)
    {
            $leave_policy = LeavePolicy::with(['leaveType', 'leavePolicyCustomize.employees'])->findOrFail($id);

            // جلب سجل النشاطات
            $logs = ModelsLog::where('type', 'leave_policy')
                ->where('type_id', $id)
                ->with(['user.branch'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function ($item) {
                    return $item->created_at->format('Y-m-d');
                });

            return view('hr::attendance.settings.leave_policies.show', compact('leave_policy', 'logs'));


    }

    public function delete($id)
    {
        $leave_policy = LeavePolicy::findOrFail($id);
        $name = $leave_policy->name;
        $leave_policy->delete();

        ModelsLog::create([
            'type' => 'leave_policy',
            'type_id' => $id,
            'type_log' => 'log',
            'description' => 'تم حذف سياسة الاجازات ' . $name,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('leave_policy.index')->with(['success'=>'تم حذف سياسة الاجازات بنجاح']);
    }

    public function updateStatus($id)
    {
        $leave_policy = LeavePolicy::find($id);

        if (!$leave_policy) {
            return redirect()->route('leave_policy.show',$id)->with(['error' => 'سياسة الاجازات غير موجودة!']);
        }

        $leave_policy->status = !$leave_policy->status;
        $leave_policy->save();

        ModelsLog::create([
            'type' => 'leave_policy',
            'type_id' => $leave_policy->id,
            'type_log' => 'log',
            'description' => 'تم تغيير حالة سياسة الاجازات ' . $leave_policy->name,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('leave_policy.show',$id)->with(['success' => 'تم تغيير حالة سياسة الاجازات بنجاح']);
    }

    public function leave_policy_employees($id)
    {
        $leave_policy = LeavePolicy::find($id);
        $employees = Employee::select('id', 'first_name','middle_name')->get();
        $branches = Branch::select('id','name')->get();
        $departments = Department::select('id','name')->get();
        $job_titles = JopTitle::select('id','name')->get();
        return view('hr::attendance.settings.leave_policies.leave_policy_employees',compact('leave_policy','employees','branches','departments','job_titles'));
    }

    public function add_leave_policy_employees(Request $request,$id)
    {
        try {
            DB::beginTransaction();

            if ($request['use_rules'] === 'employees') {
                $leave_policy_customize = LeavePolicyCustomize::updateOrCreate([
                    'leave_policy_id' => $id,
                    'use_rules' => 2,
                ]);
            } elseif ($request['use_rules'] === 'rules') {
                $leave_policy_customize = LeavePolicyCustomize::updateOrCreate([
                    'leave_policy_id' => $id,
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'job_title_id' => $request['job_title_id'],
                    'use_rules' => 1,
                ]);
            }

            $leave_policy_customize->employees()->sync($request['employee_id']);

            ModelsLog::create([
                'type' => 'leave_policy',
                'type_id' => $leave_policy_customize->id,
                'type_log' => 'log',
                'description' => 'تم اضافة الموظفين لسياسة الاجازات',
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()->route('leave_policy.show',$id)->with(['success'=>'تمت اضافة الموظفين لسياسة الاجازات بنجاح']);

        }catch (\Exception $exception){
            DB::rollBack();
            return redirect()->back()->with(['error' => $exception->getMessage()]);
        }
    }

    /**
     * جلب عدد الموظفين المخصصين لسياسة الإجازة
     */
    public function employeesCount($id)
    {
        try {
            $leave_policy = LeavePolicy::findOrFail($id);

            // البحث عن التخصيصات المرتبطة بهذه السياسة
            $customizations = LeavePolicyCustomize::where('leave_policy_id', $id)->get();

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
     * جلب قائمة الموظفين المخصصين لسياسة الإجازة
     */
    public function employeesList($id)
    {
        try {
            $leave_policy = LeavePolicy::findOrFail($id);
            $employees = collect();
            $departments = collect();

            // البحث عن التخصيصات المرتبطة بهذه السياسة
            $customizations = LeavePolicyCustomize::where('leave_policy_id', $id)
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
                            'full_name' => $employee->full_name ?? 'غير محدد',
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
                            'leave_types_count' => $leave_policy->leaveType()->count(),
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
                            'full_name' => $employee->full_name ?? 'غير محدد',
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
                            'leave_types_count' => $leave_policy->leaveType()->count(),
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

            Log::info('Employee data structure for leave policy:', [
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
                    'leave_policy_id' => $id
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error loading employees list for leave policy:', [
                'leave_policy_id' => $id,
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
     * إزالة موظف من سياسة الإجازة
     */
    public function removeEmployee($leavePolicyId, Request $request)
    {
        try {
            DB::beginTransaction();

            $employeeId = $request->input('employee_id');

            // البحث عن التخصيص الذي يحتوي على هذا الموظف
            $customizations = LeavePolicyCustomize::where('leave_policy_id', $leavePolicyId)
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
                ModelsLog::create([
                    'type' => 'leave_policy',
                    'type_id' => $leavePolicyId,
                    'type_log' => 'log',
                    'description' => 'تم إزالة موظف من سياسة الإجازات',
                    'created_by' => auth()->id(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'تم إزالة الموظف من السياسة بنجاح'
                ]);
            } else {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'الموظف غير موجود في هذه السياسة'
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
     * إضافة موظفين جدد لسياسة الإجازة
     */
    public function addEmployees($leavePolicyId, Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id'
        ]);

        try {
            DB::beginTransaction();

            $leave_policy = LeavePolicy::findOrFail($leavePolicyId);

            // البحث عن التخصيص المباشر أو إنشاء واحد جديد
            $customization = LeavePolicyCustomize::firstOrCreate([
                'leave_policy_id' => $leavePolicyId,
                'use_rules' => 2, // مخصص للموظفين مباشرة
            ]);

            // إضافة الموظفين الجدد
            $newEmployees = array_diff($request->employee_ids, $customization->employees()->pluck('employee_id')->toArray());

            if (!empty($newEmployees)) {
                $customization->employees()->attach($newEmployees);

                ModelsLog::create([
                    'type' => 'leave_policy',
                    'type_id' => $leavePolicyId,
                    'type_log' => 'log',
                    'description' => 'تم إضافة ' . count($newEmployees) . ' موظف جديد لسياسة الإجازات',
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
     * جلب إحصائيات سياسة الإجازة
     */
    public function getStatistics($id)
    {
        try {
            $leave_policy = LeavePolicy::findOrFail($id);

            // عدد أنواع الإجازات
            $leaveTypesCount = $leave_policy->leaveType()->count();

            // عدد الموظفين المخصصين
            $employeesCount = $this->getEmployeesCountForPolicy($id);

            // عدد الأقسام المتأثرة
            $departmentsCount = $this->getAffectedDepartmentsCount($id);

            // آخر تحديث
            $lastUpdate = $leave_policy->updated_at;

            return response()->json([
                'success' => true,
                'statistics' => [
                    'leave_types_count' => $leaveTypesCount,
                    'employees_count' => $employeesCount,
                    'departments_count' => $departmentsCount,
                    'last_update' => $lastUpdate->diffForHumans(),
                    'creation_date' => $leave_policy->created_at->format('Y-m-d'),
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
    private function getEmployeesCountForPolicy($leavePolicyId)
    {
        $customizations = LeavePolicyCustomize::where('leave_policy_id', $leavePolicyId)->get();
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
    private function getAffectedDepartmentsCount($leavePolicyId)
    {
        $customizations = LeavePolicyCustomize::where('leave_policy_id', $leavePolicyId)->get();
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