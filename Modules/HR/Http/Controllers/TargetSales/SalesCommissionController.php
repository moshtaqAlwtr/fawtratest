<?php

namespace Modules\HR\Http\Controllers\TargetSales;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Commission_Products;
use App\Models\SalesCommission;
use Illuminate\Http\Request;

class SalesCommissionController extends Controller
{
public function index(Request $request)
{
    $query = SalesCommission::query()->with('employee'); // علاقات لجلب اسم الموظف

    // فلتر باسم الموظف
    if ($request->filled('name')) {
        $query->whereHas('employee', function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->name . '%');
        });
    }

    // فلتر بقواعد العمولة (مثلاً حسب commission_id أو حسب نوع العملية)
    if ($request->filled('commission_rule_id')) {
        $query->where('commission_id', $request->commission_rule_id);
    }



    // فلتر بالتاريخ (من - إلى)
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // ترتيب وحدود
    $SalesCommissions = $query->latest()->paginate(20);

    return view('hr::target_sales.sales_commission.index', compact('SalesCommissions'));
}

    public function show($id){
        $SalesCommission     = SalesCommission::find($id);
        $comissions = Commission::find($SalesCommission->commission_id);
        $SalesCommission_Products = SalesCommission::where('id', $id)->get();
        return view('hr::target_sales.sales_commission.show',compact('SalesCommission','comissions','SalesCommission_Products'));

    }

}
