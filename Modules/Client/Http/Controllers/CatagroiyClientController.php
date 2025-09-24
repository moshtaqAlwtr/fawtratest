<?php

namespace Modules\Client\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\CategoriesClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatagroiyClientController extends Controller
{
    public function index()
    {
        $categories = CategoriesClient::withCount('clients')->get();

        return view('client.setting.category.index', compact('categories'));
    }
    public function create()
    {
        return view('client.setting.category.create');
    }
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories_clients,name',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // إنشاء التصنيف الجديد
            CategoriesClient::create([
                'name' => $request->name,
                'description' => $request->description,
                'active' => $request->active ?? true,
            ]);

            return redirect()->route('categoriesClient.index')->with('success', 'تم إنشاء تصنيف العميل بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء إنشاء تصنيف العميل: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * تحديث تصنيف عميل موجود
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $category = CategoriesClient::find($id);
        return view('client.setting.category.edit', compact('category'));
    }
    public function update(Request $request, $id)
    {
        // البحث عن التصنيف
        $category = CategoriesClient::find($id);

        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories_clients,name,' . $id,
            'description' => 'nullable|string',
            'active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // تحديث بيانات التصنيف
            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'active' => $request->active ?? $category->active,
            ]);

            return redirect()->route('categoriesClient.index')->with('success', 'تم تحديث تصنيف العميل بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'حدث خطأ أثناء تحديث تصنيف العميل: ' . $e->getMessage())
                ->withInput();
        }
    }
    public function destroy($id){
        $category = CategoriesClient::find($id);
        $category->delete();
        return redirect()->route('categoriesClient.index')->with('success', 'تم حذف تصنيف العميل بنجاح');

    }

}
