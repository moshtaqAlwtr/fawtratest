<?php


namespace Modules\HR\Http\Controllers\Salaries;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Log as ModelsLog;
use App\Models\SalaryItem;
use App\Models\SalaryTemplate;
use Illuminate\Http\Request;

class SalaryItemsController extends Controller
{
    public function index(Request $request)
{
    $query = SalaryItem::query();

    if ($request->ajax()) {
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // ✅ البحث عن الحسابات عند الكتابة
        if ($request->filled('query')) {
            $accounts = \App\Models\Account::where(function($query) use ($request) {
                $query->where('name', 'LIKE', "%" . $request->query('query') . "%")
                      ->orWhere('id', 'LIKE', "%" . $request->query('query') . "%")
                      ->orWhere('code', 'LIKE', "%" . $request->query('query') . "%");
            })->get();

            $options = '';
            foreach ($accounts as $account) {
                $options .= "<a href='#' class='list-group-item list-group-item-action account-item'>{$account->name}</a>";
            }

            return response()->json(['options' => $options]);
        }

        // ✅ جلب بيانات SalaryItems
        $salaryItems = $query->get();

        return response()->json([
            'html' => view('hr::salaries.salary_items.partials.table', compact('salaryItems'))->render()
        ]);
    }

    // تحميل جميع البيانات عند فتح الصفحة لأول مرة
    $salaryItems = $query->get();
    return view('hr::salaries.salary_items.index', compact('salaryItems'));
}
    public function create()
    {
        $accounts = Account::all();
        $salaryItems = SalaryItem::all();

        return view('hr::salaries.salary_items.create', compact('accounts', 'salaryItems'));
    }
    public function show($id)
    {
        $salaryItem = SalaryItem::findOrFail($id);
        return view('hr::salaries.salary_items.show', compact('salaryItem'));
    }
    public function edit($id)
    {
        $salaryItem = SalaryItem::findOrFail($id);
        $accounts = Account::all();
        return view('hr::salaries.salary_items.edit', compact('employees', 'salaryItem', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2',
            'status' => 'required|in:1,2,3',
            'description' => 'nullable|string',
            'salary_item_value' => 'required|in:1,2',
            'amount' => 'nullable|numeric|required_if:salary_item_value,1',
            'calculation_formula' => 'nullable|string|required_if:salary_item_value,2',
            'condition' => 'nullable|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        try {
            SalaryItem::create($validated);

           ModelsLog::create([
    'type' => 'salary_log',
    // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
 'description' => 'تم اضافة بند راتب  ',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);
            return redirect()->route('SalaryItems.index')->with('success', 'تم إضافة بند الراتب بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء إضافة بند الراتب')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2',
            'status' => 'required|in:1,2,3',
            'description' => 'nullable|string',
            'salary_item_value' => 'required|in:1,2',
            'amount' => 'nullable|numeric|required_if:salary_item_value,1',
            'calculation_formula' => 'nullable|string|required_if:salary_item_value,2',
            'condition' => 'nullable|string',
            'account_id' => 'nullable|exists:accounts,id',
        ]);

        try {
            $salaryItem = SalaryItem::findOrFail($id);
            $salaryItem->update($validated);

            return redirect()->route('SalaryItems.index')->with('success', 'تم تحديث بند الراتب بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تحديث بند الراتب')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $salaryItem = SalaryItem::findOrFail($id);
            $salaryItem->delete();

            return redirect()->route('SalaryItems.index')->with('success', 'تم حذف بند الراتب بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء حذف بند الراتب: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $salaryTemplates = SalaryTemplate::findOrFail($id);

            // Toggle status between 1 (active) and 0 (inactive)
            $salaryTemplates->status = $salaryTemplates->status == 1 ? 2 : 1;
            $salaryTemplates->save();

            $message = $salaryTemplates->status == 1 ? 'تم تنشيط قالب  الراتب بنجاح' : 'تم تعطيل بند الراتب بنجاح';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تغيير حالة قالب  الراتب');
        }
    }
}
