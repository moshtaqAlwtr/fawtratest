<?php


namespace Modules\HR\Http\Controllers\Salaries;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Models\FunctionalLevels;
use App\Models\JopTitle;
use App\Models\SalaryItem;
use App\Models\SalaryTemplate;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ContractsController extends Controller
{
    public function index(Request $request)
    {
        // بناء الاستعلام الأساسي مع العلاقات
        $query = Contract::with(['employee', 'employee.department', 'jobTitle', 'salaryTemplate', 'salaryItems', 'creator']);

        // البحث حسب الموظف
        if ($request->filled('employee_search')) {
            $query->where('employee_id', $request->employee_search);
        }

        // البحث حسب الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // البحث حسب تاريخ الانتهاء
        if ($request->filled('end_date_from')) {
            $query->whereDate('end_date', '>=', $request->end_date_from);
        }
        if ($request->filled('end_date_to')) {
            $query->whereDate('end_date', '<=', $request->end_date_to);
        }

        // البحث حسب المسمى الوظيفي
        if ($request->filled('job_title_id')) {
            $query->where('job_title_id', $request->job_title_id);
        }

        // البحث حسب القسم (من خلال الموظف)
        if ($request->filled('department_id')) {
            $query->whereHas('employee.department', function ($q) use ($request) {
                $q->where('id', $request->department_id);
            });
        }

        // البحث في وصف العقد
        if ($request->filled('contract_component')) {
            $query->where('description', 'LIKE', '%' . $request->contract_component . '%');
        }

        // البحث حسب تاريخ البداية
        if ($request->filled('start_date_from')) {
            $query->whereDate('start_date', '>=', $request->start_date_from);
        }
        if ($request->filled('start_date_to')) {
            $query->whereDate('start_date', '<=', $request->start_date_to);
        }

        // البحث حسب بند الراتب
        if ($request->filled('salary_item_id')) {
            $query->whereHas('salaryItems', function ($q) use ($request) {
                $q->where('id', $request->salary_item_id);
            });
        }

        // البحث حسب تاريخ المباشرة
        if ($request->filled('join_date_from')) {
            $query->whereDate('join_date', '>=', $request->join_date_from);
        }
        if ($request->filled('join_date_to')) {
            $query->whereDate('join_date', '<=', $request->join_date_to);
        }

        // البحث حسب قالب الراتب
        if ($request->filled('salary_temp_id')) {
            $query->where('salary_temp_id', $request->salary_temp_id);
        }

        // البحث حسب تاريخ نهاية التجربة
        if ($request->filled('probation_date_from')) {
            $query->whereDate('probation_end_date', '>=', $request->probation_date_from);
        }
        if ($request->filled('probation_date_to')) {
            $query->whereDate('probation_end_date', '<=', $request->probation_date_to);
        }

        // البحث حسب حالة الموظف
        if ($request->filled('employee_status')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('status', $request->employee_status);
            });
        }

        // البحث حسب نوع الانتهاء
        if ($request->filled('termination_type')) {
            $query->where('termination_type', $request->termination_type);
        }

        // البحث حسب من أضاف العقد
        if ($request->filled('created_by')) {
            $query->where('created_by', $request->created_by);
        }

        // البحث حسب الفرع (من خلال الموظف)
        if ($request->filled('branch_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('branch_id', $request->branch_id);
            });
        }

        // تنفيذ الاستعلام مع الترتيب والترقيم
        $contracts = $query->orderBy('created_at', 'desc')->get();

        // تجهيز البيانات للعرض
        $employees = Employee::all();
        $jobTitles = JopTitle::all();
        $salaryTemplates = SalaryTemplate::all();
        $departments = Department::all();
        $salaryItems = SalaryItem::all();
        $users = User::all();
        $branches = Branch::all();

        return view('hr::salaries.contracts.index', compact('contracts', 'employees', 'jobTitles', 'salaryTemplates', 'departments', 'salaryItems', 'users', 'branches'));
    }
    public function create()
    {
        // حساب الرقم التالي
        $lastContract = Contract::orderBy('code', 'desc')->first();
        $nextNumber   = $lastContract ? (int) $lastContract->code + 1 : 7;
        

        return view('hr::salaries.contracts.create', [
            'employees' => Employee::all(),
            'jopTitle' => JopTitle::all(),
            'functionalLevels' => FunctionalLevels::all(),
            'salaryTemplates' => SalaryTemplate::all(),
            'additionItems' => SalaryItem::where('type', 1)->get(),
            'deductionItems' => SalaryItem::where('type', 1)->get(),
            'nextNumber' => $nextNumber, // إرسال الرقم التالي إلى النموذج
        ]);
    }

    public function store(Request $request)
    {
        // dd($request->all());
        // try {
            DB::beginTransaction();

            // التحقق من البيانات (نحذف validation للكود)
            $request->validate([
                'employee_id' => 'required',
                'job_title_id' => 'required',
                'job_level_id' => 'required',
                // 'salary_temp_id' => 'required',
                'start_date' => 'required|date',
                'join_date' => 'required|date',
                'probation_end_date' => 'required|date',
                'contract_date' => 'required|date',
                'amount' => 'required|numeric',
            ]);

            // إنشاء الكود التلقائي
            $lastContract = Contract::orderBy('code', 'desc')->first();
            $nextNumber = $lastContract ? (int) $lastContract->code + 1 : 7;
            $code = (string) $nextNumber;
            // إنشاء العقد
            $contract = Contract::create([
                'employee_id' => $request->employee_id,
                'job_title_id' => $request->job_title_id,
                'job_level_id' => $request->job_level_id,
                'salary_temp_id' => $request->salary_temp_id,
                'code' => $code, // استخدام الكود التلقائي
                'description' => $request->description,
                'parent_contract_id' => $request->parent_contract_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'type_contract' => $request->type_contract,
                'duration_unit' => $request->duration_unit,
                'duration' => $request->duration,
                'amount' => $request->amount,
                'join_date' => $request->join_date,
                'probation_end_date' => $request->probation_end_date,
                'contract_date' => $request->contract_date,
                'receiving_cycle' => $request->receiving_cycle,
                'currency' => $request->currency ?? 'SAR',
                'attachments' => $request->attachments,
            ]);

              ModelsLog::create([
    'type' => 'salary_log',
    'type_id' => $contract->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
 'description' => 'تم انشاء قصد  ',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

            // حفظ البنود المختارة في المستحقات
            if (!empty($request->addition_type)) {
                foreach ($request->addition_type as $key => $type) {
                    if (!empty($type)) {
                        $updateData = [
                            'contracts_id' => $contract->id,
                        ];

                        if (isset($request->addition_amount[$key])) {
                            $updateData['amount'] = $request->addition_amount[$key];
                        }

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
                            'contracts_id' => $contract->id,
                        ];

                        if (isset($request->deduction_amount[$key])) {
                            $updateData['amount'] = $request->deduction_amount[$key];
                        }

                        if (isset($request->deduction_calculation_formula[$key])) {
                            $updateData['calculation_formula'] = $request->deduction_calculation_formula[$key];
                        }

                        SalaryItem::where('id', $type)->update($updateData);
                    }
                }
            }

            // معالجة المرفقات إذا وجدت
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/contracts'), $filename);
                    $contract->attachments = $filename;
                    $contract->save();
                }
            }

            DB::commit();

            return redirect()->route('Contracts.index')->with('success', 'تم إضافة العقد الراتب بنجاح');
        // } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة العقد الراتب: ' . $e->getMessage());
        // }
    }

    public function show($id)
    {
        $contract = Contract::findOrFail($id);
        $additionItems = SalaryItem::where('type', 1)->select('id', 'name', 'calculation_formula', 'amount')->get();
        $deductionItems = SalaryItem::where('type', 2)->select('id', 'name', 'calculation_formula', 'amount')->get();
        return view('hr::salaries.contracts.show', compact('contract', 'additionItems', 'deductionItems'));
    }
    public function edit($id)
    {
        $contract = Contract::findOrFail($id);
        $employees = Employee::all();
        $jopTitle = JopTitle::all();
        $functionalLevels = FunctionalLevels::all();
        $salaryTemplates = SalaryTemplate::all();
        $additionItems = SalaryItem::where('type', 1)->select('id', 'name', 'calculation_formula', 'amount')->get();

        $deductionItems = SalaryItem::where('type', 2)->select('id', 'name', 'calculation_formula', 'amount')->get();

        return view('hr::salaries.contracts.edit', compact('contract', 'employees', 'jopTitle', 'functionalLevels', 'salaryTemplates', 'additionItems', 'deductionItems'));
    }
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            // البحث عن العقد
            $contract = Contract::findOrFail($id);

            // تجميع الحقول التي تم تغييرها فقط
            $updateData = [];

            // التحقق من كل حقل وإضافته فقط إذا تم تغييره وكانت قيمته صالحة
            if ($request->filled('employee_id') && is_numeric($request->employee_id)) {
                $updateData['employee_id'] = $request->employee_id;
            }
            if ($request->filled('job_title_id') && is_numeric($request->job_title_id)) {
                $updateData['job_title_id'] = $request->job_title_id;
            }
            if ($request->filled('job_level_id') && is_numeric($request->job_level_id)) {
                $updateData['job_level_id'] = $request->job_level_id;
            }
            if ($request->filled('salary_temp_id') && is_numeric($request->salary_temp_id)) {
                $updateData['salary_temp_id'] = $request->salary_temp_id;
            }
            if ($request->filled('description')) {
                $updateData['description'] = $request->description;
            }
            if ($request->filled('parent_contract_id') && is_numeric($request->parent_contract_id)) {
                $updateData['parent_contract_id'] = $request->parent_contract_id;
            }
            if ($request->filled('start_date')) {
                $updateData['start_date'] = $request->start_date;
            }
            if ($request->filled('end_date')) {
                $updateData['end_date'] = $request->end_date;
            }
            if ($request->filled('type_contract')) {
                $updateData['type_contract'] = $request->type_contract;
            }
            if ($request->filled('duration_unit')) {
                $updateData['duration_unit'] = $request->duration_unit;
            }
            if ($request->filled('duration')) {
                $updateData['duration'] = $request->duration;
            }
            if ($request->filled('amount') && is_numeric($request->amount)) {
                $updateData['amount'] = $request->amount;
            }
            if ($request->filled('join_date')) {
                $updateData['join_date'] = $request->join_date;
            }
            if ($request->filled('probation_end_date')) {
                $updateData['probation_end_date'] = $request->probation_end_date;
            }
            if ($request->filled('contract_date')) {
                $updateData['contract_date'] = $request->contract_date;
            }
            if ($request->filled('receiving_cycle')) {
                $updateData['receiving_cycle'] = $request->receiving_cycle;
            }
            if ($request->filled('currency')) {
                $updateData['currency'] = $request->currency;
            }

            // تحديث العقد فقط بالحقول التي تم تغييرها وكانت قيمها صالحة
            if (!empty($updateData)) {
                $contract->update($updateData);
            }

            // تحديث البنود المختارة في المستحقات
            if (!empty($request->addition_type)) {
                // إعادة تعيين البنود المستحقة القديمة
                SalaryItem::where('contracts_id', $contract->id)
                    ->where('type', 1)
                    ->update(['contracts_id' => null]);

                foreach ($request->addition_type as $key => $type) {
                    if (!empty($type) && is_numeric($type)) {
                        $updateData = [
                            'contracts_id' => $contract->id,
                        ];

                        if (isset($request->addition_amount[$key]) && is_numeric($request->addition_amount[$key])) {
                            $updateData['amount'] = $request->addition_amount[$key];
                        }

                        if (isset($request->addition_calculation_formula[$key])) {
                            $updateData['calculation_formula'] = $request->addition_calculation_formula[$key];
                        }

                        SalaryItem::where('id', $type)->update($updateData);
                    }
                }
            }

            // تحديث البنود المختارة في المستقطعات
            if (!empty($request->deduction_type)) {
                // إعادة تعيين البنود المستقطعة القديمة
                SalaryItem::where('contracts_id', $contract->id)
                    ->where('type', 2)
                    ->update(['contracts_id' => null]);

                foreach ($request->deduction_type as $key => $type) {
                    if (!empty($type) && is_numeric($type)) {
                        $updateData = [
                            'contracts_id' => $contract->id,
                        ];

                        if (isset($request->deduction_amount[$key]) && is_numeric($request->deduction_amount[$key])) {
                            $updateData['amount'] = $request->deduction_amount[$key];
                        }

                        if (isset($request->deduction_calculation_formula[$key])) {
                            $updateData['calculation_formula'] = $request->deduction_calculation_formula[$key];
                        }

                        SalaryItem::where('id', $type)->update($updateData);
                    }
                }
            }

            // معالجة المرفقات
            if ($request->hasFile('attachments')) {
                $file = $request->file('attachments');
                if ($file->isValid()) {
                    // حذف الملف القديم إذا وجد
                    if ($contract->attachments) {
                        $oldFilePath = public_path('assets/uploads/contracts/' . $contract->attachments);
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }

                    // حفظ الملف الجديد
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/contracts'), $filename);
                    $contract->attachments = $filename;
                    $contract->save();
                }
            }

            DB::commit();

                       ModelsLog::create([
    'type' => 'salary_log',
    'type_id' => $id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم  تعدي عقد مرتب رقم   **' .  $contract->code . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

            return redirect()->route('Contracts.index')->with('success', 'تم تحديث العقد بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث العقد: ' . $e->getMessage());
        }
    }
    public function printContract1($id)
    {
        try {
            // جلب العقد
            $contract = Contract::findOrFail($id);

            // جلب البنود المستحقة
            $additionItems = SalaryItem::where('type', 1)
                ->where('contract_id', $id)
                ->select('id', 'name', 'calculation_formula', 'amount')
                ->get();

            // جلب البنود المستقطعة
            $deductionItems = SalaryItem::where('type', 2)
                ->where('contract_id', $id)
                ->select('id', 'name', 'calculation_formula', 'amount')
                ->get();

            // عرض صفحة الطباعة
            return view('hr::salaries.contracts.contract_print.contract_prin1',
                compact('contract', 'additionItems', 'deductionItems')
            );
        } catch (\Exception $e) {
            Log::error('Error in printContract1: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء عرض العقد');
        }
    }

    // دالة طباعة PDF
    public function printContract($id)
    {
        try {
            // جلب العقد
            $contract = Contract::findOrFail($id);

            // جلب البنود المستحقة
            $additionItems = SalaryItem::where('type', 1)
                ->where('contract_id', $id)
                ->select('id', 'name', 'calculation_formula', 'amount')
                ->get();

            // جلب البنود المستقطعة
            $deductionItems = SalaryItem::where('type', 2)
                ->where('contract_id', $id)
                ->select('id', 'name', 'calculation_formula', 'amount')
                ->get();

            // تهيئة PDF
            $pdf = PDF::loadView('salaries.contracts.contract_print.contract_prin1',
                compact('contract', 'additionItems', 'deductionItems')
            );

            // إعدادات PDF
            $pdf->setPaper('A4');

            // اسم الملف
            $fileName = 'contract_' . $contract->id . '_' . date('Y-m-d_H-i-s') . '.pdf';

            // عرض PDF
            return $pdf->stream($fileName);

        } catch (\Exception $e) {
            Log::error('Error in printContract: ' . $e->getMessage());
            return redirect()->back()->with('error', 'حدث خطأ أثناء طباعة العقد');
        }
    }
}
