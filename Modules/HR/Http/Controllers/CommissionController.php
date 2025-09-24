<?php

namespace Modules\HR\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Log as ModelsLog;
use App\Models\Commission_Products;
use App\Models\CommissionUsers;
use App\Models\Employee;
use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function index()
    {

        $commissions = Commission::all();
        return view('hr::target_sales.commission_rules.index','commissions');

    }

    public function create()
    {
         $employees  = User::select('id', 'name')->get();
         $products   = Product::select('id','name')->get();
         return view('hr::commission.create', compact('employees','products'));
    }



    public function searchProducts(Request $request)
{
    $search = $request->search;

    $products = Product::where('name', 'LIKE', "%{$search}%")
                        ->select('id', 'name')
                        ->take(10) // حد النتائج
                        ->get();

    return response()->json($products);
}
   public function store(Request $request)
{
    // 1. الفاليديشن الأساسي
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'period' => 'required|in:quarterly,yearly,monthly',
        'status' => 'required|in:active,deactive',
        'commission_calculation' => 'required|in:fully_paid,partially_paid',
        'employee_id' => 'required|array|min:1',
        'employee_id.*' => 'exists:users,id',
        'target_type' => 'required|in:amount,quantity',
        'value' => 'required|numeric|min:0',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|distinct',
        'items.*.commission_percentage' => 'required|numeric|min:0|max:100',
        // يمكنك إضافة المزيد حسب الحاجة
    ], [
        'items.*.product_id.distinct' => 'لا يمكن تكرار نفس البند أكثر من مرة.',
        'items.*.commission_percentage.required' => 'النسبة مطلوبة لكل بند.',
        'items.*.commission_percentage.numeric' => 'النسبة يجب أن تكون رقمية.',
        'items.*.commission_percentage.max' => 'النسبة يجب أن لا تتجاوز 100.',
    ]);

    // 2. تحقق برمجي إضافي (تكرار "كل المنتجات" مع منتجات أخرى)
    $product_ids = collect($request->input('items'))->pluck('product_id')->toArray();
    if (in_array("0", $product_ids) && count(array_unique($product_ids)) > 1) {
        return back()->withInput()->withErrors(['items' => 'لا يمكنك اختيار "كل المنتجات" مع منتجات محددة في نفس الوقت.']);
    }

  $activeEmployees = CommissionUsers::whereIn('employee_id', $request->employee_id)
        ->whereHas('commission', function($q) {
            $q->where('status', 'active');
        })
        ->pluck('employee_id')
        ->toArray();

    if (!empty($activeEmployees)) {
        // جلب أسماء الموظفين (للتنبيه)
        $employeeNames = \App\Models\User::whereIn('id', $activeEmployees)->pluck('name')->implode(', ');
        return back()->withInput()->withErrors([
            'employee_id' => 'لا يمكن إضافة الموظف/ين (' . $employeeNames . ') لأكثر من قاعدة عمولة نشطة في نفس الوقت.'
        ]);
    }
    // 3. إنشاء قاعدة العمولة
    $Commission = new Commission();
    $Commission->name = $request->name;
    $Commission->period = $request->period;
    $Commission->status = $request->status;
    $Commission->commission_calculation = $request->commission_calculation;
    $Commission->currency = $request->currency  ?? "sa";
    $Commission->notes = $request->notes ?? "لا يوجد ملاحظة";
    $Commission->target_type = $request->target_type;
    $Commission->value = $request->value;
    $Commission->save();

    foreach ($request->employee_id  as $employee_id) {
        CommissionUsers::create([
            'commission_id' => $Commission->id,
            'employee_id' => $employee_id,
        ]);
    }

    foreach ($request->items as $item) {
        Commission_Products::create([
            'commission_id' => $Commission->id,
            'product_id' => $item['product_id'],
            'commission_percentage' => $item['commission_percentage'],
        ]);
    }

    ModelsLog::create([
        'type' => 'Commission',
        'type_id' => $Commission->id,
        'type_log' => 'log',
        'description' => 'تم اضافة قاعدة عمولة  **' . $request->name . '**',
        'created_by' => auth()->id(),
    ]);

    return redirect()->route('CommissionRules.show', $Commission->id)->with('success', 'تمت الإضافة بنجاح');
}


    public function edit($id)
    {
        // $employees = User::select('id', 'name')->get();
        $CommissionProducts = Commission_Products::where('commission_id', $id)->get();
        $products = Product::all();
        $CommissionUsers = CommissionUsers::where('commission_id', $id)->pluck('employee_id')->toArray();
        $employees = User::all(); // أو اجلب الموظفين حسب الحاجة
        $Commission = Commission::find($id);

        return view('hr::commission.edit', compact('employees','products','CommissionProducts','CommissionUsers','Commission'));
    }

    public function update(Request $request, $id)
    {
        // 1. جلب جميع القواعد النشطة (غير الحالية) لنفس الموظفين
$activeEmployees = \App\Models\CommissionUsers::whereIn('employee_id', $request->employee_id)
    ->whereHas('commission', function ($q) use ($id, $request) {
        $q->where('status', 'active')
          ->where('id', '!=', $id); // ليست القاعدة الحالية
    })
    ->pluck('employee_id')
    ->unique()
    ->toArray();

if (!empty($activeEmployees) && $request->status == 'active') {
    $employeeNames = \App\Models\User::whereIn('id', $activeEmployees)->pluck('name')->implode(', ');
    return back()->withInput()->withErrors([
        'employee_id' => 'لا يمكن إضافة الموظف/ين ('. $employeeNames .') لأكثر من قاعدة عمولة نشطة في نفس الوقت.'
    ]);
}

        // تحديث بيانات الـ Commission بناءً على الـ ID
        $Commission = Commission::findOrFail($id);
        $Commission->name = $request->name;
        $Commission->period = $request->period;
        $Commission->status = $request->status;
        $Commission->commission_calculation = $request->commission_calculation;
        $Commission->currency = $request->currency ?? "sa";
        $Commission->notes = $request->notes;
        $Commission->target_type = $request->target_type;
        $Commission->value = $request->value;
        $Commission->save();

        // تحديث الموظفين المرتبطين بـ Commission
        // أولاً حذف الموظفين الحاليين المرتبطين بـ Commission
        CommissionUsers::where('commission_id', $id)->delete();

        // ثم إضافة الموظفين الجدد
        foreach ($request->employee_id as $employee_id) {
            CommissionUsers::create([
                'commission_id' => $Commission->id,
                'employee_id' => $employee_id,
            ]);
        }

        // تحديث المنتجات المرتبطة بالـ Commission
        // أولاً حذف المنتجات الحالية المرتبطة بـ Commission
        Commission_Products::where('commission_id', $id)->delete();

        // ثم إضافة المنتجات الجديدة
        foreach ($request->items as $item) {
            // تأكد من أن المنتج ونسبة العمولة غير فارغين
            if (!empty($item['product_id']) && !empty($item['commission_percentage'])) {
                Commission_Products::create([
                    'commission_id' => $Commission->id,
                    'product_id' => $item['product_id'],
                    'commission_percentage' => $item['commission_percentage'],
                ]);
            }
        }

         ModelsLog::create([
        'type' => 'Commission',
        'type_id' => $Commission->id,
        'type_log' => 'log',
        'description' => 'تم تعديل قاعدة عمولة  **' . $request->name . '**',
        'created_by' => auth()->id(),
    ]);
        return redirect()->route('CommissionRules.index')->with('success', 'تم التعديل بنجاح');
    }

    public function show($id)
    {

    }

    public function delete($id)
    {

    }
}
