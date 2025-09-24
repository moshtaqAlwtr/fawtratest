<?php

namespace Modules\HR\Http\Controllers\TargetSales;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Commission_Products;
use App\Models\CommissionUsers;
use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;

class CommissionRulesController extends Controller
{
 public function index(Request $request)
{
    $query = Commission::query();

    // بحث بالاسم
    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    // بحث بالحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // بحث بالفترة
    if ($request->filled('period')) {
        $query->where('period', $request->period);
    }

    // بحث بنوع الهدف
    if ($request->filled('target_type')) {
        $query->where('target_type', $request->target_type);
    }

    // بحث باسم موظف (في جداول علاقات الموظفين)
    if ($request->filled('employee')) {
        $query->whereHas('users', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->employee . '%');
        });
    }

    $commissions = $query->orderBy('id', 'desc')->get();

    return view('hr::target_sales.commission_rules.index', compact('commissions'));
}

public function create(){
         $employees  = User::select('id', 'name')->whereIn('role',['manager','employee'])->get();
         $products   = Product::select('id','name')->get();
         return view('hr::commission.create', compact('employees','products'));
// $employees = Employee::all();
//     return view('target_sales.commission_rules.create',compact('employees'));
}
public function show($id){
     $commission = Commission::find($id);
     $commissionUsers = CommissionUsers::where('commission_id', $id)->get();
     $CommissionProducts = Commission_Products::where('commission_id',$id)->get();
    $actives_logs = ModelsLog::where('type_id', $id)
    ->where('type', 'Commission')
    ->orderBy('created_at', 'desc')
    ->get()
    ->groupBy(function ($item) {
        return $item->created_at->format('Y-m-d');
    });

    return view('hr::target_sales.commission_rules.show',compact('commission','commissionUsers','CommissionProducts','actives_logs'));
}

public function edit($id)
{
    return "ff";
    $commission = Commission::findOrFail($id);
    // إذا كنت تربط الموظفين في جدول مفصول CommissionUsers مثلاً
    $CommissionUsers = CommissionUsers::where('commission_id', $id)->pluck('employee_id')->toArray();
    $CommissionProducts = Commission_Products::where('commission_id', $id)->get();

    // جلب نفس الموظفين والمنتجات كما في create
    $employees  = User::select('id', 'name')->whereIn('role', ['manager', 'employee'])->get();
    $products   = Product::select('id', 'name')->get();

    return view('hr::target_sales.commission_rules.edit', compact(
        'commission',
        'employees',
        'products',
        'CommissionUsers',
        'CommissionProducts'
    ));
}


}
