<?php

namespace Modules\Stock\Http\Controllers\Stock;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Models\Log;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function create()
    {
        $categories = Category::select('id','name')->get();
        return view('stock::category.create',compact('categories'));
    }

    public function edit($id)
    {
        $categories = Category::select('id','name')->get();
        $category = Category::findOrFail($id);
        return view('stock::category.edit',compact('category','categories'));
    }

    public function store(CategoryRequest $request)
    {
        $category = new Category();

        if($request->hasFile('attachments'))
        {
            $category->attachments = $this->UploadImage('assets/uploads/category',$request->attachments);
        } # End If
        $category->name = $request->name;
        $category->main_category = $request->main_category;
        $category->discretion = $request->discretion;

        $category->save();

          // تسجيل اشعار نظام جديد
          $Log = Log::create([
            'type' => 'product_log',
            'type_id' => $category->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم اضافة تصنيف جديد **' . $request->name. '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        return redirect()->route('product_settings.category')->with( ['success'=>'تم اضافه التصنيف بنجاج !!']);

    }


    public function update(CategoryRequest $request ,$id)
    {
        $category = Category::findOrFail($id);
        $oldName = $category->name;
        if($request->hasFile('attachments'))
        {
            $category->attachments = $this->UploadImage('assets/uploads/category',$request->attachments);
        } # End If
        $category->name = $request->name;
        $category->main_category = $request->main_category;
        $category->discretion = $request->discretion;

        $category->update();

          // تسجيل اشعار نظام جديد
          Log::create([
            'type' => 'product_log',
            'type_id' => $category->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم تعديل التصنيف من **' . $oldName . '** إلى **' . $request->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        return redirect()->route('product_settings.category')->with( ['success'=>'تم تحديث التصنيف بنجاج !!']);

    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
          // تسجيل اشعار نظام جديد
          Log::create([
            'type' => 'product_log',
            'type_id' =>  $category->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم حذف التصنيف **' .  $category->name . '**', // النص المنسق
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        $category->delete();
        return redirect()->route('product_settings.category')->with( ['error'=>'تم حذف التصنيف بنجاج !!']);
    }

    function uploadImage($folder,$image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time().rand(1,99).'.'.$fileExtension;
        $image->move($folder,$fileName);

        return $fileName;
    }//end of uploadImage

}
