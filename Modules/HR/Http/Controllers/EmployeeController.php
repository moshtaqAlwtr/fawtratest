<?php
namespace Modules\HR\Http\Controllers;
use App\Exports\EmployeesExport;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\EmployeeRequest;
use App\Models\Branch;
use App\Models\Department;
use App\Models\FunctionalLevels;
use App\Models\JobRole;
use App\Models\JopTitle;
use App\Models\Shift;
use App\Models\TypesJobs;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;
use App\Models\Direction;
use App\Models\EmployeeGroup;
use App\Models\Region_groub;

class EmployeeController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        $employees = Employee::select()->orderBy('id', 'DESC')->get();
        return view('hr::employee.index', compact('clients', 'employees'));
    }

    public function send_email($id)
    {
        $employee = User::where('employee_id', $id)->first();

        if (!$employee) {
            return response()->json(['message' => 'الموظف غير موجود'], 404);
        }

        // توليد كلمة مرور جديدة عشوائية
        $newPassword = $this->generateRandomPassword();

        // تحديث كلمة المرور في قاعدة البيانات بعد تشفيرها
        $employee->password = Hash::make($newPassword);
        $employee->save();

        // إعداد بيانات البريد
        $details = [
            'name' => $employee->name,
            'email' => $employee->email,
            'password' => $newPassword, // إرسال كلمة المرور الجديدة مباشرة
        ];

        // إرسال البريد
        Mail::to($employee->email)->send(new TestMail($details));
        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $employee->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم ارسال بيانات الدخول **' . $employee->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // return back()->with('message', 'تم إرسال البريد بنجاح!');
        return redirect()
            ->back()
            ->with(['success' => 'تم  ارسال البريد بنجاح .']);
    }

    /**
     * دالة لتوليد كلمة مرور عشوائية
     */
    private function generateRandomPassword($length = 10)
    {
        return substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, $length);
    }

    public function create()
    {
        $shifts = Shift::select('id', 'name')->get();
        $branches = Branch::select('id', 'name')->get();
        $job_types = TypesJobs::select('id', 'name')->get();
        $job_levels = FunctionalLevels::select('id', 'name')->get();
        $job_titles = JopTitle::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $job_roles = JobRole::select('id', 'role_name', 'role_type')->get();
        $employees = Employee::select('id', 'first_name', 'middle_name')->get();
        $groups = Region_groub::select('id', 'name')->get(); // إضافة هذا السطر
        $directions = Direction::select('id', 'name')->get(); // إضافة هذا السطر

        return view('hr::employee.create', compact('employees', 'job_roles', 'departments', 'job_titles', 'job_levels', 'job_types', 'branches', 'shifts', 'groups', 'directions'));
    }

    public function store(EmployeeRequest $request)
    {
        DB::beginTransaction();
        $employee_data = $request->except('_token', 'allow_system_access', 'send_credentials', 'groups', 'directions');

        $employee_data['created_by'] = auth()->user()->id;

        $employee = new Employee();

        if ($request->hasFile('employee_photo')) {
            $employee->employee_photo = $this->UploadImage('assets/uploads/employee', $request->employee_photo);
        }

        if ($request->has('allow_system_access')) {
            $employee->allow_system_access = 1;
        }

        if ($request->has('send_credentials')) {
            $employee->send_credentials = 1;
        }

        $request->validate(
            [
                'email' => ['required', 'email', 'unique:users,email'],
            ],
            [
                'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
                'email.email' => 'يجب أن يكون البريد الإلكتروني صالحًا.',
                'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            ],
        );

        $new_employee = $employee->create($employee_data);


        // حفظ المجموعات والاتجاهات
        if ($request->has('groups')) {
            foreach ($request->groups as $index => $groupId) {
                if (!empty($groupId)) {
                    $directionId = $request->directions[$index] ?? null;

                    EmployeeGroup::create([
                        'employee_id' => $new_employee->id,
                        'group_id' => $groupId,

                        'direction_id' => $directionId,
                        'created_by' => auth()->id(),
                    ]);
                }
            }
        }

        $user = User::create([
            'name' => $new_employee->full_name,
            'email' => $request->email,
            'phone' => $request->phone_number,
            'role' => 'employee',
            'employee_id' => $new_employee->id,
            'branch_id' => $request->branch_id,

            'password' => Hash::make($request->phone_number),
        ]);

        $role = JobRole::where('id', $request->Job_role_id)->first();
        $role_name = $role->role_name;

        $user->assignRole($role_name);

        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $new_employee->id,
            'type_log' => 'log',
            'description' => 'تم إضافة موظف جديد **' . $new_employee->full_name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();
        return redirect()
            ->route('employee.show', $new_employee->id)
            ->with(['success' => 'تم إضافة الموظف بنجاح !!']);
    }
 public function edit($id)
{
    $shifts = Shift::select('id','name')->get();
    $branches = Branch::select('id','name')->get();
    $jobTypes = TypesJobs::select('id','name')->get();
    $jobLevels = FunctionalLevels::select('id','name')->get();
    $jobTitles = JopTitle::select('id','name')->get();
    $job_roles = JobRole::select('id','role_name','role_type')->get();
    $departments = Department::select('id','name')->get();
    $employees = Employee::select('id','first_name','middle_name')->get();
    $groups = Region_groub::select('id','name')->get(); // إضافة هذا السطر
    $directions = Direction::select('id','name')->get(); // إضافة هذا السطر
    $employee = Employee::findOrFail($id);

    // استرجاع المجموعات والاتجاهات الحالية للموظف
    $employeeGroups = EmployeeGroup::where('employee_id', $id)->get();

    return view('hr::employee.edit', compact(
        'employee', 'employees', 'job_roles', 'departments', 'jobTitles',
        'jobLevels', 'jobTypes', 'branches', 'shifts', 'groups',
        'directions', 'employeeGroups'
    ));
}
    public function show($id)
    {
        $employee = Employee::findOrFail($id);
        $employee_email = $employee->email;
        $user = User::where('email', $employee_email)->first();
        return view('hr::employee.show', compact('employee', 'user'));
    }

public function update(EmployeeRequest $request, $id)
{
    try {
        DB::beginTransaction();

        $employee_data = $request->except('_token', 'allow_system_access', 'send_credentials', 'groups', 'directions');

        $employee = Employee::findOrFail($id);

        // التحقق من البريد الإلكتروني فقط إذا كان مختلفًا عن البريد الإلكتروني الحالي
        if ($request->email !== $employee->email) {
            $existingEmployee = Employee::where('email', $request->email)->where('id', '!=', $id)->first();
            if ($existingEmployee) {
                return redirect()
                    ->route('employee.edit', $id)
                    ->with(['error' => 'البريد الإلكتروني مستخدم بالفعل.']);
            }
        }

        if ($request->hasFile('employee_photo')) {
            $employee->employee_photo = $this->UploadImage('assets/uploads/employee', $request->employee_photo);
        }

        if ($request->has('allow_system_access')) {
            $employee->allow_system_access = 1;
        } else {
            $employee->allow_system_access = 0;
        }

        if ($request->has('send_credentials')) {
            $employee->send_credentials = 1;
        } else {
            $employee->send_credentials = 0;
        }

        $employee->update($employee_data);

// تحديث الدور للمستخدم المرتبط
 $user = User::where('employee_id', $employee->id)->first();
 $jobRole = JobRole::where('role_name',$employee->job_role->role_name)->first();

if ($user && $jobRole) {
    $user->syncRoles([$jobRole->role_name]);
}

        // تحديث المجموعات والاتجاهات
        if ($request->has('groups')) {
            // حذف جميع المجموعات القديمة
            EmployeeGroup::where('employee_id', $id)->delete();

            // إضافة المجموعات الجديدة
            foreach ($request->groups as $index => $groupId) {
                if (!empty($groupId)) {
                    $directionId = $request->directions[$index] ?? null;

                    EmployeeGroup::create([
                        'employee_id' => $id,
                        'group_id' => $groupId,
                        'direction_id' => $directionId,
                        'created_by' => auth()->id()
                    ]);
                }
            }
        }

        // تحديث بيانات المستخدم
        $userData = [
            'name' => $employee->full_name,
            'email' => $request->email,
            'phone' => $request->phone_number,
            'branch_id' => $request->branch_id,
            'role' => 'employee',
        ];

        // إذا كانت هناك حاجة لتغيير كلمة المرور
        if ($request->has('reset_password')) {
            $userData['password'] = Hash::make($request->phone_number);
        }

        User::updateOrCreate(
            ['employee_id' => $id],
            $userData
        );

        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $employee->id,
            'type_log' => 'log',
            'description' => 'تم تعديل موظف **' . $employee->full_name . '**',
            'created_by' => auth()->id(),
        ]);

        DB::commit();
        return redirect()
            ->route('employee.show', $id)
            ->with(['success' => 'تم تحديث الموظف بنجاح !!']);
    } catch (\Exception $exception) {
        DB::rollback();
        return redirect()
            ->route('employee.index')
            ->with(['error' => $exception->getMessage()]);
    }
}
    public function delete($id)
    {
        $employee = Employee::findOrFail($id);
        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $employee->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف موظف  **' . $employee->full_name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $employee->delete();
        return redirect()
            ->back()
            ->with(['error' => 'تم حذف الموظف بنجاج !!']);
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate(
            [
                'password' => 'required|string|min:8|confirmed',
            ],
            [
                'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
                'password.min' => 'كلمة المرور يجب أن تحتوي على 8 أحرف على الأقل.',
                'password.confirmed' => 'تأكيد كلمة المرور لا يتطابق.',
            ],
        );
        $employee_email = Employee::findOrFail($id)->email;
        $user = User::where('email', $employee_email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم    تعديل كلمة السر ل  **' . $user->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()
            ->back()
            ->with(['success' => 'تم تغيير كلمة المرور بنجاح.']);
    }

    public function login_to($id)
    {
        if (!Auth::user()->role === 'employee') {
            abort(403, 'غير مسموح لك.');
        }

        $user = User::findOrFail($id);

        Auth::logout();
        Auth::login($user);
        return redirect()->route('dashboard_sales.index');
    }

    public function employee_management()
    {
        return view('hr::employee.employees_management');
    }

    public function manage_shifts()
    {
        return view('hr::employee.manage_shifts');
    }

    public function add_shift()
    {
        return view('hr::employee.add_shift');
    }

    public function add_new_role()
    {
        return view('hr::employee.add_new_role');
    }

    public function export_view()
    {
        return view('hr::employee.export');
    }

    public function export(Request $request)
    {
        $fields = $request->input('fields', []);

        if (empty($fields)) {
            return redirect()
                ->back()
                ->with(['error' => 'يرجى تحديد الحقول للتصدير!']);
        }

        return Excel::download(new EmployeesExport($fields), 'departments.xlsx');
    }

    # Helper Function
    function uploadImage($folder, $image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time() . rand(1, 99) . '.' . $fileExtension;
        $image->move($folder, $fileName);

        return $fileName;
    } //end of uploadImage
    public function search(Request $request)
    {
        $query = $request->input('query');

        // البحث عن الموظفين الذين تحتوي أسماؤهم على النص المُدخل
        $employees = Employee::where('name', 'LIKE', "%$query%")
            ->select('id', 'first_name') // جلب الحقول المطلوبة فقط
            ->limit(10) // تحديد عدد النتائج
            ->get();

        return response()->json($employees);
    }
    public function updateStatus($id)
    {
        $employee = Employee::find($id);

        if (!$employee) {
            return redirect()
                ->route('employee.show', $id)
                ->with(['error' => ' نوع الرصيد غير موجود!']);
        }

        $employee->update(['status' => !$employee->status]);

        $statusText = $employee->status ? 'تم تفعيل الموظف' : 'تم تعطيل الموظف';

        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => $statusText . ' **' . $employee->first_name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        return redirect()
            ->route('employee.show', $id)
            ->with(['success' => 'تم تحديث حالة الموضف  بنجاح!']);
    }
}
