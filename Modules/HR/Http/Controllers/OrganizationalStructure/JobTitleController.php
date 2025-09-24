<?php

namespace Modules\HR\Http\Controllers\OrganizationalStructure;
use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\JopTitle;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;

class JobTitleController extends Controller
{
    public function index()
    {
        $titles = JopTitle::select()->orderBy('id', 'desc')->get();
        return view('hr::organizational_structure.JobTitle.index',compact('titles'));
    }

    public function create()
    {
        $departments = Department::select('id', 'name')->get();
        return view('hr::organizational_structure.JobTitle.create',compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $title = new JopTitle();
        $title->name = $request->name;
        $title->description = $request->description;
        $title->status = $request->status;
        $title->department_id = $request->department_id;
        $title->save();

         ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $title->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم   اضافة مسمى وظيفي  **' . $title->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

        return redirect()->route('JobTitles.index')->with( ['success'=>'تم اضافه مسميه وظيفي بنجاج !!']);
    }

    public function show($id)
    {
        $title = JopTitle::find($id);
        return view('hr::organizational_structure.JobTitle.show',compact('title'));
    }
    public function edit($id)
    {
        $departments = Department::select('id', 'name')->get();
        $title = JopTitle::find($id);
        return view('hr::organizational_structure.JobTitle.edit',compact('title','departments'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $title = JopTitle::find($id);
        $oldName =  $title ->name;
        $title->name = $request->name;
        $title->description = $request->description;
        $title->status = $request->status;
        $title->department_id = $request->department_id;
        $title->save();

                    ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $title->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم تغيير الاسم الوظيفي من **' . $oldName . '** إلى **' . $request->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

        return redirect()->route('JobTitles.index')->with( ['success'=>'تم تعديل مسمى وظيفي بنجاج !!']);
    }

    public function delete($id)
    {
        $title = JopTitle::find($id);
               ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $title->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم حذف المسمى الوظيفي **' . $title->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);
        $title->delete();
        return redirect()->route('JobTitles.index')->with( ['error'=>'تم حذف مسمى وظيفي بنجاج !!']);
    }

    public function updateStatus($id)
    {
        $title = JopTitle::find($id);

        if (!$title) {
            return redirect()->route('JobTitles.show',$id)->with(['error' => 'نوع الوظيفة غير موجود!']);
        }

        $title->update(['status' => !$title->status]);

        $statusText = $title->status ? 'تم تعطيل المسمى الوظيفي' : 'تم تفعيل  المسمى الوظيفي';

ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => $statusText . ' **' . $title->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

        return redirect()->route('JobTitles.show',$id)->with(['success' => 'تم تحديث حالة نوع وظيفة بنجاح!']);
    }

}
