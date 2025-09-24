<?php

namespace Modules\Account\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\CostCenter;
use App\Models\Log as ModelsLog;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class CostCentersController extends Controller
{
    public function index()
{
    // الحصول على المستخدم الحالي
    $user = auth()->user();

    // التحقق مما إذا كان للمستخدم فرع
    if ($user->branch) {
        $branch = $user->branch;

        // التحقق من صلاحية "مشاركة مراكز التكلفة"
        $shareCostCenterStatus = $branch->settings()->where('key', 'share_cost_center')->first();

        // إذا كانت الصلاحية غير مفعلة، عرض مراكز التكلفة الخاصة بالفرع فقط
        if ($shareCostCenterStatus && $shareCostCenterStatus->pivot->status == 0) {
            $accounts = CostCenter::with('children')
                ->whereHas('createdBy', function ($query) use ($branch) {
                    $query->where('branch_id', $branch->id);
                })
                ->get();
        } else {
            // إذا كانت الصلاحية مفعلة أو غير موجودة، عرض جميع مراكز التكلفة
            $accounts = CostCenter::with('children')->get();
        }
    } else {
        // إذا لم يكن لدى المستخدم فرع، عرض جميع مراكز التكلفة
        $accounts = CostCenter::with('children')->get();
    }

    return view('account::cost_centers.index', compact('accounts'));
}


    public function store_account(Request $request)
    {
        $validated = $request->validateWithBag('storeAccount', [
            'code' => 'required|unique:cost_centers,code|max:10',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'name' => 'required|string|max:255',
        ], [
            'code.required' => 'يجب إدخال الكود.',
            'code.unique' => 'هذا الكود مستخدم من قبل.',
            'parent_id.exists' => 'الحساب الرئيسي غير موجود.',
            'name.required' => 'اسم الحساب مطلوب.',
        ]);

        try {
            $newCode = $request->code;
            $existingCode = CostCenter::where('code',  '===', $newCode)->exists();
            if ($existingCode) {
                return response()->json([
                    'success' => false,
                    'message' => 'كود الحساب موجود مسبقًا.',
                    'errors' => ['code' => 'كود الحساب موجود مسبقًا.'],
                ]);
            }

            $costCenter = new CostCenter();

            $costCenter->is_main = $request->has('is_main') ? 1 : 0;

            $costCenter->code       = $request->code;
            $costCenter->parent_id  = $request->parent_id;
            $costCenter->name       = $request->name;
            $costCenter->created_by = auth()->id();
            $costCenter->save();

             // تسجيل السجل
    ModelsLog::create([
        'type' => 'finance_log',
        'type_id' => $costCenter->id, // ID النشاط المرتبط
        'type_log' => 'log', // نوع النشاط
        'description' => 'تم إضافة مركز تكلفة جديد **' . $request->name . '**',
        'created_by' => auth()->id(), // ID المستخدم الحالي
    ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الحساب بنجاح',
                'data' => $costCenter,
            ], 200);
        }
        catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدثت أخطاء في التحقق.',
                'errors' => $e->errors(),
            ], 422);
        }

    }

    public function getTree()
    {
        $accounts = CostCenter::all();

        $tree = $this->buildTree($accounts);

        return response()->json($tree);
    }

    private function buildTree($accounts, $parentId = null)
    {
        $branch = [];
        foreach ($accounts as $account) {
            if ($account->parent_id == $parentId) {
                $children = $this->buildTree($accounts, $account->id);
                $branch[] = [
                    'id' => $account->id,
                    'text' => $account->name,
                    'children' => $children
                ];
            }
        }
        return $branch;
    }

    public function getParents()
    {
        $parents = CostCenter::whereNull('parent_id')->get();
        return response()->json($parents);
    }

    public function getChildren($id)
    {
        $children = CostCenter::where('parent_id', $id)->get();
        return response()->json($children);
    }

    public function getNextCode(CostCenter $parent)
    {
        $lastChildCode = CostCenter::where('parent_id', $parent->id)->max('code');

        $nextCode = $lastChildCode ? $lastChildCode + 1 : $parent->code . '1';

        return response()->json(['nextCode' => $nextCode]);
    }

    public function getAccountDetails($parentId)
    {
        try {
            // العثور على الحساب
            $account = CostCenter::find($parentId);

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحساب غير موجود'
                ], 404);
            }

            if($account->parent_id == null)
            {
                $category = $account->name;
            }
            $category = $this->getRootParent($parentId);

            return response()->json([
                'success' => true,
                'mainAccountName' => $account->name,
                'category' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الحساب',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getRootParent($accountId)
    {
        try {
            $account = CostCenter::find($accountId);

            if (!$account) {
                return response()->json([
                    'success' => false,
                    'message' => 'الحساب غير موجود'
                ], 404);
            }

            while ($account->parent) {
                $account = $account->parent;
            }

            return $account->name;

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب الحساب الجد',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function destroy($parentId)
    {
        try
        {
            $account = CostCenter::findOrFail($parentId);
                     ModelsLog::create([
        'type' => 'finance_log',
        'type_id' => $account->id, // ID النشاط المرتبط
        'type_log' => 'log', // نوع النشاط
        'description' => 'تم حذف مركز التكلفة **' . $account->name . '**',
        'created_by' => auth()->id(), // ID المستخدم الحالي
    ]);
            $account->delete(); // الحذف

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الحساب بنجاح'
            ], 200);
        }
        catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الحساب غير موجود'
            ], 404);
        }
        catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الحذف'
            ], 500);
        }

    }

    public function edit($id)
    {
        try {
            $costCenter = CostCenter::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $costCenter,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر جلب بيانات العنصر. الرجاء المحاولة مرة أخرى.',
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'code' => 'required|unique:cost_centers,code,' . $id . '|max:10',
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:cost_centers,id',
        ], [
            'code.required' => 'يجب إدخال الكود.',
            'code.unique' => 'هذا الكود مستخدم من قبل.',
            'name.required' => 'اسم الحساب مطلوب.',
            'parent_id.exists' => 'الحساب الرئيسي غير موجود.',
        ]);

        try {
            // تحديث العنصر
            $costCenter = CostCenter::findOrFail($id);

            $costCenter->is_main = $request->has('is_main') ? 1 : 0;

            $costCenter->code = $request->code;
            $costCenter->parent_id = $request->parent_id;
            $costCenter->name = $request->name;
            $costCenter->update();

             ModelsLog::create([
        'type' => 'finance_log',
        'type_id' => $costCenter->id, // ID النشاط المرتبط
        'type_log' => 'log', // نوع النشاط
        'description' => 'تم تعديل حساب التكلفة **' . $request->name . '**',
        'created_by' => auth()->id(), // ID المستخدم الحالي
    ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث مركز التكلفة بنجاح.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء التحديث. الرجاء المحاولة مرة أخرى.',
            ], 500);
        }
    }



}
