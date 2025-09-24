<?php

namespace Modules\HR\Http\Controllers\OrganizationalStructure;
use App\Http\Controllers\Controller;
use App\Models\FunctionalLevels;
use Illuminate\Http\Request;
use App\Models\Log as ModelsLog;

class ManagingFunctionalLevelsController extends Controller
{
    public function index()
    {
        $functionalLevels = FunctionalLevels::select()->orderBy('id','desc')->get();
        return view('hr::organizational_structure.ManagingFunctionalLevels.index',compact('functionalLevels'));
    }

    public function  create()
    {
        return view('hr::organizational_structure.ManagingFunctionalLevels.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

       $level = FunctionalLevels::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ]);

                       ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $level->id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم   اضافة مستوى وظيفي  **' . $level->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);


        return redirect()->route('ManagingFunctionalLevels.index')->with('success','تم اضافة مستوى وظيفي بنجاح');
    }

    public function show($id)
    {
        $level = FunctionalLevels::find($id);
        return view('hr::organizational_structure.ManagingFunctionalLevels.show',compact('level'));
    }

    public function edit($id)
    {
        $functionalLevel = FunctionalLevels::find($id);

        return view('hr::organizational_structure.ManagingFunctionalLevels.edit',compact('functionalLevel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
        ]);

     $level =   FunctionalLevels::find($id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status
        ]);

                       ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم   تغيير مستوى وظيفي  **' . $request->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);


        return redirect()->route('ManagingFunctionalLevels.index')->with('success','تم تعديل مستوى وظيفي بنجاح');
    }

    public function delete($id)
    {
          $level = FunctionalLevels::find($id);

                            ModelsLog::create([
    'type' => 'struct_log',
    'type_id' => $id, // ID النشاط المرتبط
    'type_log' => 'log', // نوع النشاط
    'description' => 'تم   حذف المستوى الوظيفي    **' . $level->name . '**',
    'created_by' => auth()->id(), // ID المستخدم الحالي
]);

        FunctionalLevels::find($id)->delete();
        return redirect()->route('ManagingFunctionalLevels.index')->with('error','تم حذف مستوى وظيفي بنجاح');
    }

}
