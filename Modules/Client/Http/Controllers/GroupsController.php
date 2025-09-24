<?php

namespace Modules\Client\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Direction;
use App\Models\Region_groub;
use Illuminate\Http\Request;

class GroupsController extends Controller
{
    public function group_client(Request $request)
    {
        $query = Region_groub::query();

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->filled('directions_id')) {
            $query->where('directions_id', $request->directions_id);
        }

        $Regions_groub = $query->orderBy('id', 'desc')->get();

        $branches = Branch::all(); // لازم ترجع الفروع للفلترة
        $directions = Direction::all();

        return view('client::groups.group_client', compact('Regions_groub', 'branches', 'directions'));
    }

    public function group_client_edit($id)
    {
        $branches = Branch::all();
        $regionGroup = Region_groub::findOrFail($id);
        $directions = Direction::all();
        return view('client::groups.group_client_edit', compact('branches', 'regionGroup', 'directions'));
    }
    public function group_client_create()
    {
        $branches = Branch::all();
        $directions = Direction::all();
        return view('client::groups.group_client_create', compact('branches', 'directions'));
    }

    public function group_client_store(Request $request)
    {
        // تحقق من صحة البيانات المدخلة
        $request->validate([
            'name' => 'required|string|unique:region_groubs,name',
            'branch_id' => 'required|exists:branches,id',
            'directions_id' => 'required|exists:directions,id',
        ]);

        // إنشاء مجموعة المنطقة الجديدة
        $regionGroup = new Region_groub();
        $regionGroup->name = $request->name;
        $regionGroup->branch_id = $request->branch_id; // ✅ أضف هذا السطر

        $regionGroup->directions_id = $request->directions_id; // ✅ أضف هذا السطر

        $regionGroup->save();

        // إرجاع البيانات المحفوظة أو رسالة نجاح
        return redirect()->route('groups.group_client')->with('success', 'تم إنشاء المجموعة بنجاح');
    }
    public function destroy($id)
    {
        $Regions_groub = Region_groub::findOrFail($id);
        $Regions_groub->delete();
        return redirect()->route('groups.group_client')->with('success', 'تم حذف المجموعة بنجاح');
    }

    public function group_client_update(Request $request, $id)
    {
        // جلب المجموعة الحالية
        $regionGroup = Region_groub::findOrFail($id);

        // التحقق من صحة البيانات
        $request->validate([
            'name' => 'required|string|unique:region_groubs,name,' . $regionGroup->id,
            'branch_id' => 'required|exists:branches,id',
            'directions_id' => 'required|exists:directions,id',
        ]);

        // تحديث البيانات
        $regionGroup->name = $request->name;
        $regionGroup->branch_id = $request->branch_id;
        $regionGroup->directions_id = $request->directions_id; // ✅ أضف هذا السطر

        $regionGroup->save();

        // إرجاع رسالة نجاح
        return redirect()->route('groups.group_client')->with('success', 'تم تعديل المجموعة بنجاح');
    }
}
