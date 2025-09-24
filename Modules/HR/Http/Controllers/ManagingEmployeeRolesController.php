<?php
namespace Modules\HR\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\JobRole;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class ManagingEmployeeRolesController extends Controller
{
    /*************  ✨ Codeium Command ⭐  *************/
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /******  d4803714-49da-47ab-b8ac-18a2d264e3e1  *******/
    public function index()
    {
        $roles = JobRole::select()->orderBy('id', 'DESC')->get();
        return view('hr::managing_employee_roles.index', compact('roles'));
    }

    public function create()
    {
        $role = Role::all();
        return view('hr::managing_employee_roles.create', compact('role'));
    }

    public function create_test()
    {
        $role = Role::all();
        return view('hr::managing_employee_roles.create_test', compact('role'));
    }

    public function store(Request $request)
    {
        // try {
        DB::beginTransaction();
        $role = new JobRole();
        $role->role_name = $request->role_name;
        $role->role_type = $request->customRadio === 'user' ? 1 : 2;

        foreach (JobRole::$job_roles as $roleKey) {
            if ($request->has($roleKey)) {
                $role->{$roleKey} = 1;
            }
        }
        $role->save();

        $role = Role::create(['name' => $request->role_name]);
        $role_name = $role->name;
        $role = Role::findByName($role_name);

        foreach (JobRole::$job_roles as $roleName) {
            if ($request->has($roleName)) {
                $role->givePermissionTo($roleName);
            }
        }

        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $role->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم إضافة دور وظيفي جديد **' . $request->role_name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        DB::commit();
        return redirect()
            ->route('managing_employee_roles.index')
            ->with(['success' => 'تم اضافه دور وظيفي بنجاج !!']);
        // } catch (\Exception $exception) {
        DB::rollBack();
        return redirect()
            ->route('managing_employee_roles.index')
            ->with(['error' => $exception->getMessage()]);
        // }
    }

    public function edit($id)
    {
        $role = JobRole::findOrFail($id);
        return view('hr::managing_employee_roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $role = JobRole::findOrFail($id);
        $role->role_name = $request->role_name;
        $role->role_type = $request->customRadio === 'user' ? 1 : 2;

        foreach (JobRole::$job_roles as $roleKey) {
            $role->{$roleKey} = $request->has($roleKey) ? 1 : 0;
        }
        $role->update();

        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $role->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم إضافة تعديل وظيفي  **' . $request->role_name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $role = Role::findByName($request->role_name);
        if (!$role) {
            return redirect()
                ->back()
                ->with(['error' => 'لا يوجد دور وظيفي بهذا الاسم !!']);
        }
        $role->syncPermissions([]);

        foreach (JobRole::$job_roles as $roleName) {
            if ($request->has($roleName)) {
                $role->givePermissionTo($roleName); // إضافة الصلاحية للدور
            }
        }
        return redirect()
            ->route('managing_employee_roles.index')
            ->with(['success' => 'تم تحديث دور وظيفي بنجاج !!']);
    }

    public function delete($id)
    {
        $role = JobRole::findOrFail($id);
        ModelsLog::create([
            'type' => 'hr_log',
            'type_id' => $role->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف دور  وظيفي  **' . $role->role_name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $role->delete();
        return redirect()
            ->route('managing_employee_roles.index')
            ->with(['error' => 'تم حذف دور وظيفي بنجاج !!']);
    }
}
