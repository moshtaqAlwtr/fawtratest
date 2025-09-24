<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomShiftRequest;
use App\Models\Branch;
use App\Models\CustomShift;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JopTitle;
use App\Models\Shift;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomShiftsController extends Controller
{
public function index()
{
    $custom_shifts = CustomShift::with(['shift', 'employees', 'employees.branch', 'employees.department', 'employees.job_Title'])
        ->orderBy('id', 'desc')
        ->get();

    return view('hr::attendance.custom_shifts.index', compact('custom_shifts'));
}

public function filter(Request $request)
{
    $query = CustomShift::with(['shift', 'employees', 'employees.branch', 'employees.department', 'employees.job_Title'])
        ->orderBy('custom_shifts.id', 'desc'); // تحديد الجدول بوضوح

    // فلترة بالكلمات المفتاحية
    if ($request->has('keywords') && !empty($request->keywords)) {
        $keywords = $request->keywords;
        $query->where(function($q) use ($keywords) {
            $q->where('custom_shifts.name', 'like', "%$keywords%")
              ->orWhereHas('employees', function($q2) use ($keywords) {
                  $q2->where('employees.first_name', 'like', "%$keywords%")
                     ->orWhere('employees.id', 'like', "%$keywords%") // استخدام code بدلاً من id
                     ->orWhere('employees.middle_name', 'like', "%$keywords%");
              });
        });
    }

    // فلترة بتاريخ البدء
    if ($request->has('from_date') && !empty($request->from_date)) {
        $query->where('custom_shifts.from_date', '>=', $request->from_date);
    }

    // فلترة بتاريخ الانتهاء
    if ($request->has('to_date') && !empty($request->to_date)) {
        $query->where('custom_shifts.to_date', '<=', $request->to_date);
    }

    $custom_shifts = $query->get();

    if ($request->ajax()) {
        $html = view('hr::attendance.custom_shifts.partials.table_rows', compact('custom_shifts'))->render();

        return response()->json([
            'html' => $html,
            'count' => $custom_shifts->count()
        ]);
    }

    return view('hr::attendance.custom_shifts.index', compact('custom_shifts'));
}
    public function create()
    {
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
        $shifts = Shift::select('id', 'name')->get();

        return view('hr::attendance.custom_shifts.create',
            compact('employees', 'branches', 'departments', 'job_titles', 'shifts'));
    }

    public function store(CustomShiftRequest $request)
    {
        try {
            DB::beginTransaction();

            $customShiftData = [
                'name' => $request['name'],
                'from_date' => $request['from_date'],
                'to_date' => $request['to_date'],
                'shift_id' => $request['shift_id'],
                'is_main' => $request->has('is_main') ? true : false,
            ];

            if ($request['use_rules'] === 'employees') {
                $customShiftData['use_rules'] = 2;
            } elseif ($request['use_rules'] === 'rules') {
                $customShiftData = array_merge($customShiftData, [
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'job_title_id' => $request['job_title_id'],
                    'shift_rule_id' => $request['shift_rule_id'],
                    'use_rules' => 1,
                ]);
            }

            $custom_shift = CustomShift::create($customShiftData);

            // Attach employees based on selection type
            if ($request['use_rules'] === 'employees' && $request->has('employee_id')) {
                $custom_shift->employees()->attach($request['employee_id']);
            } elseif ($request['use_rules'] === 'rules') {
                // Handle rule-based employee selection
                $this->attachEmployeesByRules($custom_shift, $request);

                // Handle excluded employees if any
                if ($request->has('excluded_employee_id')) {
                    $custom_shift->employees()->detach($request['excluded_employee_id']);
                }
            }

            // Log the action
            ModelsLog::create([
                'type' => 'shift_custom_log',
                'type_id' => $custom_shift->id,
                'type_log' => 'create',
                'description' => 'تم إضافة وردية متخصصة جديدة: ' . $custom_shift->name,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('custom_shifts.index')
                ->with(['success' => 'تم إضافة الوردية المتخصصة بنجاح!']);

        } catch (\Exception $exception) {
            DB::rollBack();

            // Log the error
            Log::error('Error creating custom shift: ' . $exception->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with(['error' => 'حدث خطأ أثناء إضافة الوردية. يرجى المحاولة لاحقاً.']);
        }
    }

    public function edit($id)
    {
        $custom_shift = CustomShift::with(['employees', 'shift', 'branch', 'department', 'jobTitle'])
            ->findOrFail($id);

        $employees = Employee::select('id', 'first_name', 'middle_name', 'last_name')->get();
        $branches = Branch::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
        $shifts = Shift::select('id', 'name')->get();

        return view('hr::attendance.custom_shifts.edit',
            compact('custom_shift', 'employees', 'branches', 'departments', 'job_titles', 'shifts'));
    }

    public function update(CustomShiftRequest $request, $id)
    {
        try {
            DB::beginTransaction();

            $custom_shift = CustomShift::findOrFail($id);
            $oldName = $custom_shift->name;

            $customShiftData = [
                'name' => $request['name'],
                'from_date' => $request['from_date'],
                'to_date' => $request['to_date'],
                'shift_id' => $request['shift_id'],
                'is_main' => $request->has('is_main') ? true : false,
            ];

            if ($request['use_rules'] === 'employees') {
                $customShiftData = array_merge($customShiftData, [
                    'use_rules' => 2,
                    'branch_id' => null,
                    'department_id' => null,
                    'job_title_id' => null,
                    'shift_rule_id' => null,
                ]);
            } elseif ($request['use_rules'] === 'rules') {
                $customShiftData = array_merge($customShiftData, [
                    'branch_id' => $request['branch_id'],
                    'department_id' => $request['department_id'],
                    'job_title_id' => $request['job_title_id'],
                    'shifts_rule_id' => $request['shifts_rule_id'],
                    'use_rules' => 1,
                ]);
            }

            $custom_shift->update($customShiftData);

            // Update employee associations
            if ($request['use_rules'] === 'employees' && $request->has('employee_id')) {
                $custom_shift->employees()->sync($request['employee_id']);
            } elseif ($request['use_rules'] === 'rules') {
                // Handle rule-based employee selection
                $this->attachEmployeesByRules($custom_shift, $request, true);

                // Handle excluded employees if any
                if ($request->has('excluded_employee_id')) {
                    $custom_shift->employees()->detach($request['excluded_employee_id']);
                }
            }

            // Log the action
            ModelsLog::create([
                'type' => 'shift_custom_log',
                'type_id' => $custom_shift->id,
                'type_log' => 'update',
                'description' => 'تم تعديل وردية متخصصة: ' . $oldName . ' → ' . $custom_shift->name,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('custom_shifts.index')
                ->with(['success' => 'تم تعديل الوردية المتخصصة بنجاح!']);

        } catch (\Exception $exception) {
            DB::rollBack();

            // Log the error
            \Log::error('Error updating custom shift: ' . $exception->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with(['error' => 'حدث خطأ أثناء تعديل الوردية. يرجى المحاولة لاحقاً.']);
        }
    }

    public function show($id)
    {
        $custom_shift = CustomShift::findOrFail($id);
        $logs = ModelsLog::where('type', 'shift_management')
            ->where('type_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('hr::attendance.custom_shifts.show', compact('custom_shift','logs'));
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();

            $custom_shift = CustomShift::findOrFail($id);
            $shiftName = $custom_shift->name;

            // Log the action before deletion
            ModelsLog::create([
                'type' => 'shift_custom_log',
                'type_id' => $custom_shift->id,
                'type_log' => 'delete',
                'description' => 'تم حذف وردية متخصصة: ' . $shiftName,
                'created_by' => auth()->id(),
            ]);

            // Detach all employees
            $custom_shift->employees()->detach();

            // Delete the custom shift
            $custom_shift->delete();

            DB::commit();

            return redirect()
                ->route('custom_shifts.index')
                ->with(['success' => 'تم حذف الوردية المتخصصة بنجاح!']);

        } catch (\Exception $exception) {
            DB::rollBack();

            // Log the error
            \Log::error('Error deleting custom shift: ' . $exception->getMessage());

            return redirect()
                ->route('custom_shifts.index')
                ->with(['error' => 'حدث خطأ أثناء حذف الوردية. يرجى المحاولة لاحقاً.']);
        }
    }

    /**
     * Get employees based on rules and attach them to custom shift
     */
    private function attachEmployeesByRules($custom_shift, $request, $isUpdate = false)
    {
        $employeeQuery = Employee::query();

        // Apply filters based on rules
        if ($request['branch_id']) {
            $employeeQuery->where('branch_id', $request['branch_id']);
        }

        if ($request['department_id']) {
            $employeeQuery->where('department_id', $request['department_id']);
        }

        if ($request['job_title_id']) {
            $employeeQuery->where('job_title_id', $request['job_title_id']);
        }

        if ($request['shifts_rule_id']) {
            // Filter employees who currently have this shift
            $employeeQuery->where('shift_id', $request['shifts_rule_id']);
        }

        // Get employee IDs
        $employeeIds = $employeeQuery->pluck('id')->toArray();

        // Attach or sync employees
        if ($isUpdate) {
            $custom_shift->employees()->sync($employeeIds);
        } else {
            $custom_shift->employees()->attach($employeeIds);
        }
    }

    /**
     * Get employees count for preview (AJAX endpoint)
     */
    public function getEmployeeCountPreview(Request $request)
    {
        try {
            $employeeQuery = Employee::query();

            if ($request->branch_id) {
                $employeeQuery->where('branch_id', $request->branch_id);
            }

            if ($request->department_id) {
                $employeeQuery->where('department_id', $request->department_id);
            }

            if ($request->job_title_id) {
                $employeeQuery->where('job_title_id', $request->job_title_id);
            }

            if ($request->shift_rule_id) {
                $employeeQuery->where('shift_id', $request->shift_rule_id);
            }

            $count = $employeeQuery->count();
            $employees = $employeeQuery->select('id', 'first_name', 'middle_name', 'last_name', 'employee_code')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'count' => $count,
                'employees' => $employees,
                'message' => $count > 0 ? "سيتم تطبيق الوردية على {$count} موظف" : 'لا يوجد موظفين يطابقون هذه المعايير'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب البيانات'
            ], 500);
        }
    }

    /**
     * Duplicate custom shift
     */
    public function duplicate($id)
    {
        try {
            DB::beginTransaction();

            $originalShift = CustomShift::with('employees')->findOrFail($id);

            // Create new shift with duplicated data
            $newShift = $originalShift->replicate();
            $newShift->name = $originalShift->name . ' - نسخة';
            $newShift->created_at = now();
            $newShift->updated_at = now();
            $newShift->save();

            // Duplicate employee associations
            $employeeIds = $originalShift->employees->pluck('id')->toArray();
            $newShift->employees()->attach($employeeIds);

            // Log the action
            ModelsLog::create([
                'type' => 'shift_custom_log',
                'type_id' => $newShift->id,
                'type_log' => 'duplicate',
                'description' => 'تم تكرار وردية متخصصة من: ' . $originalShift->name,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return redirect()
                ->route('custom_shifts.edit', $newShift->id)
                ->with(['success' => 'تم تكرار الوردية بنجاح! يمكنك تعديل البيانات حسب الحاجة.']);

        } catch (\Exception $exception) {
            DB::rollBack();

            return redirect()
                ->route('custom_shifts.index')
                ->with(['error' => 'حدث خطأ أثناء تكرار الوردية.']);
        }
    }

    /**
     * Toggle shift status (activate/deactivate)
     */
    public function toggleStatus($id)
    {
        try {
            $custom_shift = CustomShift::findOrFail($id);
            $custom_shift->is_active = !$custom_shift->is_active;
            $custom_shift->save();

            $status = $custom_shift->is_active ? 'تفعيل' : 'إلغاء تفعيل';

            // Log the action
            ModelsLog::create([
                'type' => 'shift_custom_log',
                'type_id' => $custom_shift->id,
                'type_log' => 'status_change',
                'description' => 'تم ' . $status . ' وردية متخصصة: ' . $custom_shift->name,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'is_active' => $custom_shift->is_active,
                'message' => 'تم ' . $status . ' الوردية بنجاح'
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة الوردية'
            ], 500);
        }
    }

    /**
     * Get custom shifts data for DataTables (AJAX)
     */
    public function getCustomShiftsData(Request $request)
    {
        $query = CustomShift::with(['shift:id,name', 'branch:id,name', 'department:id,name'])
            ->withCount('employees');

        // Apply filters
        if ($request->has('is_main') && $request->is_main !== '') {
            $query->where('is_main', $request->is_main);
        }

        if ($request->has('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('from_date', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('to_date', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->has('search') && $request->search['value']) {
            $searchValue = $request->search['value'];
            $query->where(function($q) use ($searchValue) {
                $q->where('name', 'like', "%{$searchValue}%")
                  ->orWhereHas('shift', function($subQ) use ($searchValue) {
                      $subQ->where('name', 'like', "%{$searchValue}%");
                  });
            });
        }

        $totalRecords = $query->count();

        // Apply pagination
        if ($request->has('start') && $request->has('length')) {
            $query->skip($request->start)->take($request->length);
        }

        // Apply sorting
        if ($request->has('order')) {
            $orderColumn = $request->columns[$request->order[0]['column']]['data'];
            $orderDirection = $request->order[0]['dir'];

            switch ($orderColumn) {
                case 'name':
                    $query->orderBy('name', $orderDirection);
                    break;
                case 'from_date':
                    $query->orderBy('from_date', $orderDirection);
                    break;
                case 'to_date':
                    $query->orderBy('to_date', $orderDirection);
                    break;
                default:
                    $query->orderBy('id', 'desc');
            }
        } else {
            $query->orderBy('id', 'desc');
        }

        $customShifts = $query->get();

        $data = $customShifts->map(function($shift) {
            return [
                'id' => $shift->id,
                'name' => $shift->name,
                'shift_name' => $shift->shift->name ?? 'غير محدد',
                'is_main' => $shift->is_main,
                'from_date' => $shift->from_date,
                'to_date' => $shift->to_date,
                'employees_count' => $shift->employees_count,
                'is_active' => $shift->is_active ?? true,
                'created_at' => $shift->created_at->format('Y-m-d H:i'),
            ];
        });

        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $data
        ]);
    }
}
