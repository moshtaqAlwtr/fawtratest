<?php


namespace Modules\HR\Http\Controllers\Salaries;
use App\Http\Controllers\Controller;
use App\Models\SalaryItem;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use App\Models\SalaryTemplate;

class SalaryTemplatesController extends Controller
{
    public function index(Request $request)
    {
        $query = SalaryTemplate::query();

        // البحث حسب اسم قالب الراتب
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // البحث في البنود
        if ($request->filled('item_search')) {
            $query->whereHas('salaryItems', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->item_search . '%');
            });
        }

        // البحث حسب النوع
        if ($request->filled('type')) {
            $query->whereHas('salaryItems', function ($q) use ($request) {
                $q->where('type', $request->type);
            });
        }

        // الترتيب
        $query->orderBy('created_at', 'desc');

        $SalaryTemplates = $query->paginate(25);

        // جلب الأنواع للقائمة المنسدلة
        $types = [
            '' => 'كل الأنواع',
            '1' => 'مستحقات',
            '2' => 'مستقطعات',
        ];

        return view('hr::salaries.salary_templates.index', compact('SalaryTemplates', 'types'));
    }

    public function create()
    {
        $additionItems = SalaryItem::where('type', 1)->select('id', 'name', 'calculation_formula', 'amount')->get();

        $deductionItems = SalaryItem::where('type', 2)->select('id', 'name', 'calculation_formula', 'amount')->get();

        return view('hr::salaries.salary_templates.create', compact('additionItems', 'deductionItems'));
    }
    public function show($id)
    {
        $salaryTemplates = SalaryTemplate::findOrFail($id);
        $addition = SalaryItem::where('type', 1)->select('id', 'name', 'calculation_formula', 'amount')->get();

        $deductionItems = SalaryItem::where('type', 2)->select('id', 'name', 'calculation_formula', 'amount')->get();
        return view('hr::salaries.salary_templates.show', compact('salaryTemplates', 'addition', 'deductionItems'));
    }

    public function edit($id)
    {
        $salaryTemplates = SalaryTemplate::findOrFail($id);
        $additionItems = SalaryItem::where('type', 1)->select('id', 'name', 'calculation_formula', 'amount')->get();
        $deductionItems = SalaryItem::where('type', 2)->select('id', 'name', 'calculation_formula', 'amount')->get();

        return view('hr::salaries.salary_templates.edit', compact('salaryTemplates', 'additionItems', 'deductionItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:1,2',
            'description' => 'nullable|string',
            'receiving_cycle' => 'required|in:1,2,3,4,5',
            'basic_amount' => 'nullable|numeric',
            'addition_type' => 'nullable|array',
            'deduction_type' => 'nullable|array',
        ]);

        try {
            // إنشاء قالب الراتب
            $SalaryTemplate = SalaryTemplate::create([
                'name' => $validated['name'],
                'status' => $validated['status'],
                'description' => $validated['description'],
                'receiving_cycle' => $validated['receiving_cycle'],
            ]);

            // حفظ البنود المختارة في المستحقات
            if (!empty($request->addition_type)) {
                foreach ($request->addition_type as $key => $type) {
                    if (!empty($type)) {
                        $updateData = [
                            'salary_template_id' => $SalaryTemplate->id
                        ];

                        // نضيف المبلغ فقط إذا كان موجوداً
                        if (isset($request->addition_amount[$key])) {
                            $updateData['amount'] = $request->addition_amount[$key];
                        }

                        // نضيف الصيغة الحسابية فقط إذا كانت موجودة
                        if (isset($request->addition_calculation_formula[$key])) {
                            $updateData['calculation_formula'] = $request->addition_calculation_formula[$key];
                        }

                        SalaryItem::where('id', $type)->update($updateData);
                    }
                }
            }

            // حفظ البنود المختارة في المستقطعات
            if (!empty($request->deduction_type)) {
                foreach ($request->deduction_type as $key => $type) {
                    if (!empty($type)) {
                        $updateData = [
                            'salary_template_id' => $SalaryTemplate->id
                        ];

                        // نضيف المبلغ فقط إذا كان موجوداً
                        if (isset($request->deduction_amount[$key])) {
                            $updateData['amount'] = $request->deduction_amount[$key];
                        }

                        // نضيف الصيغة الحسابية فقط إذا كانت موجودة
                        if (isset($request->deduction_calculation_formula[$key])) {
                            $updateData['calculation_formula'] = $request->deduction_calculation_formula[$key];
                        }

                        SalaryItem::where('id', $type)->update($updateData);
                    }
                }
            }

                      ModelsLog::create([
    'type' => 'salary_log',
    // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
 'description' => 'تم اضافة قالب راتب  ',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

            return redirect()->route('SalaryTemplates.index')->with('success', 'تم إضافة قالب الراتب بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة قالب الراتب: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'required|in:1,2',
            'description' => 'nullable|string',
            'receiving_cycle' => 'required|in:1,2,3,4,5',
            'basic_amount' => 'nullable|numeric',
            'addition_type' => 'nullable|array',
            'deduction_type' => 'nullable|array',
        ]);

        try {
            $SalaryTemplate = SalaryTemplate::findOrFail($id);
            $SalaryTemplate->update($validated);

            // حفظ البنود المختارة في المستحقات
            if (!empty($request->addition_type)) {
                foreach ($request->addition_type as $type) {
                    if (!empty($type)) {
                        SalaryItem::where('id', $type)->update([
                            'salary_template_id' => $SalaryTemplate->id,
                            'calculation_formula' => $SalaryTemplate->id,
                            'amount' => $SalaryTemplate->id,
                        ]);
                    }
                }
            }

            // حف�� البنود المختارة في المستقطعات
            if (!empty($request->deduction_type)) {
                foreach ($request->deduction_type as $type) {
                    if (!empty($type)) {
                        SalaryItem::where('id', $type)->update([
                            'salary_template_id' => $SalaryTemplate->id,
                            'calculation_formula' => $SalaryTemplate->id,
                            'amount' => $SalaryTemplate->id,
                        ]);
                    }
                }
            }

            return redirect()->route('SalaryTemplates.index')->with('success', 'تم تحديث قالب الراتب بنجا��');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطاء في تحديث قالب الراتب: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $SalaryTemplate = SalaryTemplate::findOrFail($id);
            $SalaryTemplate->delete();
            return redirect()->route('SalaryTemplates.index')->with('success', 'تم حذف قالب الراتب بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطاء في حذف قالب الراتب: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $salaryItem = SalaryItem::findOrFail($id);

            // Toggle status between 1 (active) and 0 (inactive)
            $salaryItem->status = $salaryItem->status == 1 ? 2 : 1;
            $salaryItem->save();

            $message = $salaryItem->status == 1 ? 'تم تنشيط بند الراتب بنجاح' : 'تم تعطيل بند الراتب بنجاح';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'حدث خطأ أثناء تغيير حالة بند الراتب');
        }
    }
}

