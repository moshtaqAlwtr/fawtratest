<?php

namespace Modules\Account\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Employee;
use App\Models\Treasury;
use App\Models\Log as ModelsLog;
use App\Models\TreasuryEmployee;
use Illuminate\Http\Request;

class TreasuryEmployeeController extends Controller
{
    public function index()
    {
        $treasury_employees = TreasuryEmployee::orderBy('id', 'DESC')->paginate(10);
        $employees = Employee::select('id', 'first_name', 'middle_name', 'nickname')->get();
        // $treasuries = Treasury::select('id','name')->get();
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return view('account::finance.treasury_employee.index', compact('treasury_employees', 'treasuries', 'employees'));
    }
    public function create()
    {
        $employees = Employee::select('id', 'first_name', 'middle_name', 'nickname')->get();
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return view('account::finance.treasury_employee.create', compact('treasuries', 'employees'));
    }

    public function store(Request $request)
    {
        // إنشاء سجل جديد في TreasuryEmployee
        $default = TreasuryEmployee::create([
            'treasury_id' => $request->treasury_id,
            'employee_id' => $request->employee_id,
        ]);

        // تسجيل النشاط في ModelsLog
        ModelsLog::create([
            'type' => 'product_log',
            'type_id' => $default->id, // استخدام $default بدلاً من $default_warehouse
            'type_log' => 'log', // نوع النشاط
            'description' => sprintf(
                'تم تعيين الخزينة **%s** كخزينة افتراضية للموظف **%s %s %s**',
                $default->treasury->name, // اسم الخزينة
                $default->employee->first_name, // الاسم الأول للموظف
                $default->employee->middle_name, // الاسم الأوسط للموظف
                $default->employee->nickname // اللقب
            ),
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()
            ->route('finance_settings.treasury_employee')
            ->with(['success' => 'تم إضافة خزينة الموظف بنجاح !!']);
    }

    public function edit($id)
    {
        $treasury_employee = TreasuryEmployee::findOrFail($id);
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $treasuries = Account::whereIn('parent_id', [13, 15])
            ->orderBy('id', 'DESC')
            ->paginate(10);
        return view('account::finance.treasury_employee.edit', compact('treasury_employee', 'treasuries', 'employees'));
    }

    public function update(Request $request, $id)
    {
        // تحديث السجل
        TreasuryEmployee::findOrFail($id)->update([
            'treasury_id' => $request->treasury_id,
            'employee_id' => $request->employee_id,
        ]);

        // تحميل السجل المحدث مع العلاقات
        $default = TreasuryEmployee::with('employee', 'treasury')->findOrFail($id);

        // تحميل الخزينة الجديد والموظف الجديد
        $newStorehouse = Account::find($request->storehouse_id);
        $newEmployee = Employee::find($request->employee_id);

        // بناء النص بناءً على التغييرات
        $description = '';

        if ($oldStorehouseId != $request->storehouse_id && $oldEmployeeId != $request->employee_id) {
            // تم تغيير الخزينة والموظف
            $description = sprintf(
                'تم تغيير الخزينة الافتراضي والموظف من **%s** (الموظف: **%s %s %s**) إلى **%s** (الموظف: **%s %s %s**)',
                $oldStorehouse->name, // الخزينة القديم
                $oldEmployee->first_name,
                $oldEmployee->middle_name,
                $oldEmployee->nickname, // الموظف القديم
                $newStorehouse->name, // الخزينة الجديد
                $newEmployee->first_name,
                $newEmployee->middle_name,
                $newEmployee->nickname, // الموظف الجديد
            );
        } elseif ($oldStorehouseId != $request->storehouse_id) {
            // تم تغيير الخزينة فقط
            $description = sprintf(
                'تم تغيير الخزينة الافتراضي من **%s** إلى **%s** للموظف **%s %s %s**',
                $oldStorehouse->name, // الخزينة القديم
                $newStorehouse->name, // الخزينة الجديد
                $default->employee->first_name,
                $default->employee->middle_name,
                $default->employee->nickname, // الموظف
            );
        } elseif ($oldEmployeeId != $request->employee_id) {
            // تم تغيير الموظف فقط
            $description = sprintf(
                'تم تغيير الموظف للمستودع الافتراضي **%s** من **%s %s %s** إلى **%s %s %s**',
                $default->treasury->name, // الخزينة
                $oldEmployee->first_name,
                $oldEmployee->middle_name,
                $oldEmployee->nickname, // الموظف القديم
                $newEmployee->first_name,
                $newEmployee->middle_name,
                $newEmployee->nickname, // الموظف الجديد
            );
        } else {
            // لم يتم تغيير شيء
            $description = 'لم يتم تغيير أي شيء.';
        }

        // تسجيل اشعار نظام جديد
        Log::create([
            'type' => 'product_log',
            'type_id' => $default->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => $description, // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()
            ->route('finance_settings.treasury_employee')
            ->with(['success' => 'تم تحديث خزينة الموظف بنجاج !!']);
    }

    public function delete($id)
    {
        TreasuryEmployee::findOrFail($id)->delete();
        return redirect()
            ->route('finance_settings.treasury_employee')
            ->with(['error' => 'تم حذف خزينة الموظف بنجاج !!']);
    }
}
