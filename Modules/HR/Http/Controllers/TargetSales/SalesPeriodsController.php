<?php

namespace Modules\HR\Http\Controllers\TargetSales;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JopTitle;
use App\Models\SalesPeriod;
use App\Models\SalesCommission;
use App\Models\User;
use Illuminate\Http\Request;



class SalesPeriodsController extends Controller
{
// public function index(Request $request)
// {
//      $employees  = User::select('id', 'name')->whereIn('role',['manager','employee'])->get();
//     $commissions = Commission::all();

//     $query = SalesCommission::with('employee', 'commission');

//     if ($request->filled('employee_id')) {
//         $query->where('employee_id', $request->employee_id);
//     }

//     if ($request->filled('commission_id')) {
//         $query->where('commission_id', $request->commission_id);
//     }

//     if ($request->filled('date_from')) {
//         $query->whereDate('created_at', '>=', $request->date_from);
//     }
//     if ($request->filled('date_to')) {
//         $query->whereDate('created_at', '<=', $request->date_to);
//     }

//     $SalesCommission_periods = $query
//         ->selectRaw('employee_id, commission_id, sum(sales_amount) as total_sales, sum(ratio) as total_ratio')
//         ->groupBy('employee_id', 'commission_id')
//         ->get();

//     return view('target_sales.salesPeriods.index', compact('SalesCommission_periods', 'employees', 'commissions'));
// }
// app/Http/Controllers/SalesPeriodController.php

public function index(Request $request)
{
    $employees = \App\Models\User::where('role', 'employee')->get();
    $commissions = \App\Models\Commission::all();

    $query = \App\Models\SalesPeriod::with([
        'branch', 'department', 'jobTitle',
        'commissionSales' => function($q) use ($request) {
            // جلب الموظف المحدد
            if ($request->filled('employee_id')) {
                $q->where('employee_id', $request->employee_id);
            }
            // جلب القاعدة المحددة
            if ($request->filled('commission_id')) {
                $q->where('commission_id', $request->commission_id);
            }
        },
        'commissionSales.employee', // eager load الموظف
        'commissionSales.commission', // eager load العمولة
    ]);

    if ($request->filled('date_from')) {
        $query->whereDate('from_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('to_date', '<=', $request->date_to);
    }

    $SalesPeriods = $query->orderBy('from_date', 'desc')->get();

    return view('hr::target_sales.salesPeriods.index', compact('employees', 'commissions', 'SalesPeriods'));
}






public function create()
{
    $employees = User::where('role','employee')->get();
    $branches = Branch::select('id', 'name')->get();
    $departments = Department::select('id', 'name')->get();
    $job_titles = JopTitle::select('id', 'name')->get();
    return view('hr::target_sales.salesPeriods.create',compact('employees','branches','departments','job_titles'));
}
public function store(Request $request)
{
    $request->validate([
        'from_date' => 'required|date',
        'to_date'   => 'required|date|after_or_equal:from_date',
    ]);

    $period = SalesPeriod::create([
        'name'          => $request->input('name') ?? null,
        'from_date'     => $request->from_date,
        'to_date'       => $request->to_date,
        'branch_id'     => $request->branch_id !== 'all' ? $request->branch_id : null,
        'department_id' => $request->department_id !== 'all' ? $request->department_id : null,
        'job_title_id'  => $request->job_title_id !== 'all' ? $request->job_title_id : null,
    ]);

    // جلب الموظفين بحسب الفلترة أو التحديد
    $employeesQuery = \App\Models\User::where('role', 'employee');
    if ($period->branch_id)      $employeesQuery->where('branch_id', $period->branch_id);
    if ($period->department_id)  $employeesQuery->where('department_id', $period->department_id);
    if ($period->job_title_id)   $employeesQuery->where('job_title_id', $period->job_title_id);
    if ($request->employee_id)   $employeesQuery->whereIn('id', $request->employee_id);

    $employees = $employeesQuery->get();

    // جلب كل قواعد العمولة الموجودة (لأنك تريد ربط كل قاعدة عمولة بالموظفين في الفترة)
    $commissions = \App\Models\Commission::all();

    foreach ($employees as $employee) {
        foreach ($commissions as $commission) {
            // جمع مبيعات الموظف لهذه القاعدة في الفترة
            $sales = \App\Models\SalesCommission::where('employee_id', $employee->id)
                ->where('commission_id', $commission->id)
                ->whereBetween('created_at', [$period->from_date, $period->to_date])
                ->sum('sales_amount');

            // إذا تريد فقط ربط إذا فيه مبيعات (احذف التعليق)
            // if ($sales == 0) continue;

            // حفظ في جدول الربط
            \App\Models\CommissionSalesPeriodEmployee::create([
                'sales_period_id' => $period->id,
                'employee_id'     => $employee->id,
                'commission_id'   => $commission->id,
                'sales_amount'    => $sales,
            ]);
        }
    }

    return redirect()->route('SalesPeriods.index')->with('success', 'تمت إضافة الفترة بنجاح');
}



public function show($id)
{
    $commissionPeriod = \App\Models\CommissionSalesPeriodEmployee::with(['employee', 'commission', 'salesPeriod'])->findOrFail($id);

    // جلب العمليات الحقيقية لهذه الفترة والموظف والعمولة
    $SalesCommissions = \App\Models\SalesCommission::where('employee_id', $commissionPeriod->employee_id)
        ->where('commission_id', $commissionPeriod->commission_id)
        ->whereBetween('created_at', [$commissionPeriod->salesPeriod->from_date, $commissionPeriod->salesPeriod->to_date])
        ->get();

    return view('hr::target_sales.salesPeriods.show', compact('commissionPeriod', 'SalesCommissions'));
}


}





