<?php

namespace Modules\HR\Http\Controllers\OrganizationalStructure;
use App\Http\Controllers\Controller;
use App\Models\TypesJobs;
use Illuminate\Http\Request;
use App\Models\Log as ModelsLog;

class ManagingJobTypesController extends Controller
{
    public function index()
    {
        $types = TypesJobs::select()->orderBy('id','desc')->get();
        return view('hr::organizational_structure.ManagingJobTypes.index',compact('types'));
    }

    public function create()
    {
        return view('hr::organizational_structure.ManagingJobTypes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $types = new TypesJobs();
        $types->name = $request->name;
        $types->description = $request->description;
        $types->status = $request->status;
        $types->save();


                       ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $types->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم   اضافة نوع وظيفي  **' . $types->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);


        return redirect()->route('ManagingJobTypes.index')->with( ['success'=>'تم اضافه نوع وظيفه بنجاج !!']);
    }

    public function show($id)
    {
        $type = TypesJobs::find($id);
        return view('hr::organizational_structure.ManagingJobTypes.show',compact('type'));
    }

    public function edit($id)
    {
        $type = TypesJobs::find($id);
        return view('hr::organizational_structure.ManagingJobTypes.edit',compact('type'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
        ]);

        $types = TypesJobs::find($id);
        $types->name = $request->name;
        $oldName = $types->name;
        $types->description = $request->description;
        $types->status = $request->status;
        $types->update();

                 ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $types->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم تغيير النوع الوظيفي من **' . $oldName . '** إلى **' . $request->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

        return redirect()->route('ManagingJobTypes.index')->with( ['success'=>'تم تعديل نوع وظيفه بنجاج !!']);
    }

    public function delete($id)
    {
        $type = TypesJobs::find($id);
                                   ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم   حذف النوع الوظيفي    **' . $type->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);
        $type->delete();
        return redirect()->route('ManagingJobTypes.index')->with( ['error'=>'تم حذف نوع وظيفه بنجاج !!']);
    }

    public function updateStatus($id)
    {
        $type = TypesJobs::find($id);

        if (!$type) {
            return redirect()->route('ManagingJobTypes.show',$id)->with(['error' => 'نوع الوظيفة غير موجود!']);
        }

        $type->update(['status' => !$type->status]);

         $statusText = $type->status ? 'تم تعطيل النوع الوظيفي' : 'تم تفعيل  النوع الوظيفي';

ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => $statusText . ' **' . $type->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);


        return redirect()->route('ManagingJobTypes.show',$id)->with(['success' => 'تم تحديث حالة نوع وظيفة بنجاح!']);
    }

}
