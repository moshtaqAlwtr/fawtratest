<?php


namespace Modules\HR\Http\Controllers\Salaries;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JopTitle;
use App\Models\Payroll;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollProcessController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::query();

        // Search by payroll name
        if ($request->has('name') && !empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Search by department
        if ($request->has('department_id') && !empty($request->department_id)) {
            $query->where('department_id', $request->department_id);
        }

        // Search by job title
        if ($request->has('jop_title_id') && !empty($request->jop_title_id)) {
            $query->where('jop_title_id', $request->jop_title_id);
        }

        // Search by date ranges
        if ($request->has('start_date') && !empty($request->start_date)) {
            $query->where('start_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && !empty($request->end_date)) {
            $query->where('end_date', '<=', $request->end_date);
        }

        // Search by registration date range
        if ($request->has('registration_date_from') && !empty($request->registration_date_from)) {
            $query->where('registration_date', '>=', $request->registration_date_from);
        }
        if ($request->has('registration_date_to') && !empty($request->registration_date_to)) {
            $query->where('registration_date', '<=', $request->registration_date_to);
        }

        // Search by creation date range
        if ($request->has('created_at_from') && !empty($request->created_at_from)) {
            $query->whereDate('created_at', '>=', $request->created_at_from);
        }
        if ($request->has('created_at_to') && !empty($request->created_at_to)) {
            $query->whereDate('created_at', '<=', $request->created_at_to);
        }

        // Search by branch
        if ($request->has('branch_id') && !empty($request->branch_id)) {
            $query->where('branch_id', $request->branch_id);
        }

        $payrolls = $query->get();

        // Get data for dropdowns
        $departments = Department::all();
        $branches = Branch::all();
        $jobTitles = JopTitle::all();
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();

        return view('hr::salaries.payroll_ process.index', compact('payrolls', 'departments', 'branches', 'jobTitles', 'employees'));
    }
    public function create()
    {
        $payrolls = Payroll::all();
        $branches = Branch::all();
        $departments = Department::all();
        $jop_titles = JopTitle::all();
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        return view('hr::salaries.payroll_ process.create', compact('employees', 'payrolls', 'branches', 'departments', 'jop_titles'));
    }
   public function  show()
    {
        return "k";
    }
    public function store(Request $request)
    { 
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_date' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'selection' => 'nullable|in:1,2', // تغيير من select_emp_role إلى selection
            'receiving_cycle' => 'nullable|in:1,2,3,4,5',
            'attendance_check' => 'nullable', // تغيير القاعدة
            'department_id' => 'nullable',
            'jop_title_id' => 'nullable',
            'branch_id' => 'nullable',
        ]);

        try {
            DB::beginTransaction();

            $payroll = Payroll::create([
                'name' => $validated['name'],
                'registration_date' => $validated['registration_date'] ?? null,
                'start_date' => $validated['start_date'] ?? null,
                'end_date' => $validated['end_date'] ?? null,
                'select_emp_role' => $validated['selection'], // تغيير من select_emp_role إلى selection
                'receiving_cycle' => $validated['receiving_cycle'] ?? null,
                'attendance_check' => $request->has('attendance_check'), // تغيير طريقة التحقق
                'department_id' => $validated['department_id'] !== 'all' ? $validated['department_id'] : null,
                'jop_title_id' => $validated['jop_title_id'] !== 'all' ? $validated['jop_title_id'] : null,
                'branch_id' => $validated['branch_id'] !== 'all' ? $validated['branch_id'] : null,
            ]);

                         ModelsLog::create([
    'type' => 'salary_log',

    'type_log' => 'log', // نوع النشاط
     'description' => 'تم  اضافة مسير رواتب',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

            // إذا كان الاختيار حسب الموظفين المحددين
            if ($validated['selection'] == 1 && $request->has('employee_id')) {
                $payroll->employees()->attach($request->input('employee_id'));
            }
            // إذا كان الاختيار حسب القواعد
            elseif ($validated['selection'] == 2) {
                $query = Employee::query();

                // تحقق من القيم قبل استخدامها في الاستعلام
                if ($validated['department_id'] !== 'all') {
                    $query->where('department_id', $validated['department_id']);
                }
                if ($validated['jop_title_id'] !== 'all') {
                    $query->where('job_title_id', $validated['jop_title_id']);
                }
                if ($validated['branch_id'] !== 'all') {
                    $query->where('branch_id', $validated['branch_id']);
                }

                $employees = $query->pluck('id')->toArray();
                if (!empty($employees)) {
                    $payroll->employees()->attach($employees);
                }
            }

            DB::commit();

            return redirect()->route('PayrollProcess.index')->with('success', 'تم إنشاء مسير الرواتب بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إنشاء مسير الرواتب: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        try {
            $payroll = Payroll::findOrFail($id);

                         ModelsLog::create([
    'type' => 'salary_log',
    'type_id' => $payroll->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
     'description' => 'تم  حذف مسير رواتب',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);
            // حذف العلاقات أولاً
            $payroll->employees()->detach();

            // ثم حذف مسير الرواتب
            $payroll->delete();

            return redirect()->route('PayrollProcess.index')->with('success', 'تم حذف مسير الرواتب بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف مسير الرواتب: ' . $e->getMessage());
        }
    }
}
