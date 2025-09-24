<?php

namespace Modules\Branches\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchSetting;
use App\Models\Log as ModelsLog;
use App\Models\BranchSettingBranch;
use App\Models\Location;
use Illuminate\Container\Attributes\Log;
use Illuminate\Http\Request;

class BranchesController extends Controller
{
    // عرض جميع الفروع
     public function index()
    {
        $branches = Branch::orderBy('created_at', 'desc')->get();
        return view('branches::index', compact('branches'));
    }

    /**
     * إحضار الفروع عبر Ajax مع الفلترة
     */
    public function getBranches(Request $request)
    {
        try {
            $query = Branch::query();

            // فلترة حسب الحالة
            if ($request->has('status') && $request->status !== '') {
                $query->where('status', $request->status);
            }

            // فلترة حسب البحث بالاسم أو الكود
            if ($request->has('search') && !empty($request->search)) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            // ترتيب النتائج
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // تطبيق التصفح إذا كان مطلوباً
            $perPage = $request->get('per_page', 15);
            $branches = $query->paginate(min($perPage, 100)); // Max 100 per page

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'html' => view('branches::_branches_list', ['branches' => $branches])->render(),
                    'message' => 'تم إحضار البيانات بنجاح'
                ]);
            }

            // Return view for regular requests
            return view('branches::index', compact('branches'));

        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إحضار البيانات',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'حدث خطأ أثناء إحضار البيانات: ' . $e->getMessage());
        }
    }


    // عرض نموذج إضافة فرع جديد

    public function switchBranch($branchId)
{
    $user = auth()->user();

    // التحقق مما إذا كان المستخدم ليس موظفًا ويمكنه تغيير الفرع
    if ($user->role !== 'employee') {
        // تحديث branch_id في جدول المستخدم
        $user->update(['branch_id' => $branchId]);

        // حفظ الفرع في الجلسة (لضمان بقاء المستخدم في الفرع المحدد)
        session(['current_branch_id' => $branchId]);
    }

    return redirect()->back();
}

    // تخزين بيانات الفرع
public function store(Request $request)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'name' => 'required|string|max:255',
        'is_main' => 'nullable|boolean',
        'phone' => 'nullable|string',
        'mobile' => 'nullable|string',
        'address1' => 'required|string|max:255',
        'address2' => 'nullable|string|max:255',
        'city' => 'required|string|max:255',
        'region' => 'nullable|string|max:255',
        'country' => 'required|string|max:255',
        'work_hours' => 'nullable|string',
        'description' => 'nullable|string|max:1000',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    // إذا كان الفرع رئيسي، نلغي أي فرع رئيسي آخر
    if ($request->is_main) {
        Branch::where('is_main', true)->update(['is_main' => false]);
    }

    // توليد الكود تلقائيًا
    $lastBranch = Branch::latest('id')->first();
    $newCode = $lastBranch ? str_pad($lastBranch->id + 1, 5, '0', STR_PAD_LEFT) : '00001';

    // إنشاء الفرع
    $branch = Branch::create([
        'name' => $request->name,
        'is_main' => $request->is_main ?? false,
        'code' => $newCode,
        'phone' => $request->phone,
        'mobile' => $request->mobile,
        'address1' => $request->address1,
        'address2' => $request->address2,
        'city' => $request->city,
        'region' => $request->region,
        'country' => $request->country,
        'work_hours' => $request->work_hours,
        'description' => $request->description,
    ]);

    // إنشاء الموقع إذا تم تحديده
    if ($request->filled('latitude') && $request->filled('longitude')) {
        Location::create([
            'branch_id' => $branch->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);
    }

    // إعدادات الصلاحيات الافتراضية
    $defaultPermissions = [
        'share_cost_center' => 0,
        // يمكنك إضافة المزيد من الصلاحيات هنا
    ];

    foreach ($defaultPermissions as $key => $status) {
        $branchSettingId = BranchSetting::where('key', $key)->value('id');
        if ($branchSettingId) {
            BranchSettingBranch::create([
                'branch_id' => $branch->id,
                'branch_setting_id' => $branchSettingId,
                'status' => $status,
            ]);
        }
    }

    // تسجيل الإجراء في السجلات
    ModelsLog::create([
        'type' => 'branch',
        'type_id' => $branch->id,
        'type_log' => 'log',
        'description' => 'تم اضافة فرع جديد **' . $branch->name . '**' .
                        ($branch->is_main ? ' (رئيسي)' : ' (فرعي)'),
        'created_by' => auth()->id(),
    ]);

    return redirect()->route('branches.index')->with('success', 'تم إضافة الفرع بنجاح');
}


public function create()
    {
        $lastBranch = Branch::latest('id')->first();

        $code = $lastBranch ? str_pad($lastBranch->id + 1, 5, '0', STR_PAD_LEFT) : '00001';
        // عرض صفحة إضافة فرع
        return view('branches::create', compact('lastBranch','code'));
    }
    // عرض تفاصيل الفرع
    public function show($id)
    {
        // استرجاع الفرع بواسطة الـ id
        $branch = Branch::findOrFail($id);

        // عرض صفحة التفاصيل
        return view('branches::show', compact('branch'));
    }

    // عرض نموذج تعديل الفرع
    public function edit($id)
    {
        // استرجاع الفرع بواسطة الـ id
        $branch = Branch::findOrFail($id);

        // عرض صفحة التعديل
        return view('branches::edit', compact('branch'));
    }

    // تحديث بيانات الفرع
public function update(Request $request, $id)
{
    // التحقق من صحة البيانات المدخلة
    $request->validate([
        'name' => 'required|string|max:255',
        'code' => 'required|string|max:255|unique:branches,code,' . $id,
        'is_main' => 'nullable|boolean',
        'phone' => 'nullable|string',
        'mobile' => 'nullable|string',
        'address1' => 'required|string|max:255',
        'address2' => 'nullable|string|max:255',
        'city' => 'required|string|max:255',
        'region' => 'nullable|string|max:255',
        'country' => 'required|string|max:255',
        'work_hours' => 'nullable|string',
        'description' => 'nullable|string|max:1000',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    // استرجاع الفرع
    $branch = Branch::findOrFail($id);
    $oldName = $branch->name;
    $wasMain = $branch->is_main;

    // معالجة حالة الفرع الرئيسي
    if ($request->is_main) {
        // إذا تم تحديده كفرع رئيسي، نلغي أي فرع رئيسي آخر
        Branch::where('is_main', true)->where('id', '!=', $id)->update(['is_main' => false]);
    } elseif ($wasMain && !$request->is_main) {
        // إذا كان رئيسيًا وتم إلغاء التحديد
        $otherMainBranches = Branch::where('is_main', true)->where('id', '!=', $id)->count();
        if ($otherMainBranches == 0) {
            return back()->with('error', 'يجب أن يكون هناك فرع رئيسي واحد على الأقل في النظام');
        }
    }

    // تحديث بيانات الفرع
    $branch->update([
        'name' => $request->name,
        'code' => $request->code,
        'is_main' => $request->is_main ?? false,
        'phone' => $request->phone,
        'mobile' => $request->mobile,
        'address1' => $request->address1,
        'address2' => $request->address2,
        'city' => $request->city,
        'region' => $request->region,
        'country' => $request->country,
        'work_hours' => $request->work_hours,
        'description' => $request->description,
    ]);

    // تحديث أو إنشاء الموقع
    if ($request->filled('latitude') && $request->filled('longitude')) {
        $locationData = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'branch_id' => $branch->id
        ];

        if ($branch->location) {
            $branch->location->update($locationData);
        } else {
            Location::create($locationData);
        }
    } elseif ($branch->location) {
        $branch->location->delete();
    }

    // تسجيل الإجراء في السجلات
    $logDescription = 'تم تعديل الفرع من **' . $oldName . '** إلى **' . $branch->name . '**';

    if ($wasMain != $branch->is_main) {
        $logDescription .= $branch->is_main ? ' (تم تعيينه كفرع رئيسي)' : ' (تم إلغاء تعيينه كفرع رئيسي)';
    }

    ModelsLog::create([
        'type' => 'branch',
        'type_id' => $branch->id,
        'type_log' => 'log',
        'description' => $logDescription,
        'created_by' => auth()->id(),
    ]);

    return redirect()->route('branches.index')->with('success', 'تم تحديث بيانات الفرع بنجاح');
}

    // حذف الفرع
    public function destroy($id)
    {
        // حذف الفرع بواسطة الـ id
        Branch::destroy($id);

        // إعادة التوجيه مع رسالة نجاح
        return redirect()->route('branches.index')->with('success', 'تم حذف الفرع بنجاح');
    }

    // عرض صفحة الإعدادات (اختياري)
    public function settings(Request $request)
    {
        // الحصول على جميع الفروع
        $branchs = Branch::all();

        // إذا كان هناك فرع مختار من الطلب، نستخدمه
        // إذا لم يكن هناك فرع مختار، نستخدم أول فرع في قاعدة البيانات
        $selectedBranchId = $request->input('branch_id', $branchs->first()->id ?? null);

        $settings = []; // تعيين مصفوفة فارغة للصلاحيات

        if ($selectedBranchId) {
            // جلب الفرع المحدد مع الصلاحيات المرتبطة به وحالة التفعيل
            $branch = Branch::with('settings')->find($selectedBranchId);

            if ($branch) {
                // تحويل الصلاحيات إلى تنسيق مناسب للعرض مع حالتها
                foreach ($branch->settings as $setting) {
                    $settings[$setting->key] = $setting->pivot->status; // استخدام الـ pivot للحصول على حالة الصلاحية
                }
            }
        }

        // تمرير البيانات إلى الـ view
        return view('branches::settings', compact('branchs', 'settings', 'selectedBranchId'));
    }

    public function settings_store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        // جلب ID الفرعsettings
        $branch_id = $request->branch_id;

        // الصلاحيات المتاحة
        $permissions = ['share_cost_center', 'share_customers', 'share_products', 'share_suppliers', 'account_tree'];

        foreach ($permissions as $permission) {
            // التحقق من حالة الصلاحية: إذا كانت مفعلّة أو غير مفعلّة
            $status = $request->has($permission) ? 1 : 0;

            // تحديث أو إضافة الصلاحية في الجدول الوسيط
            BranchSettingBranch::updateOrCreate(
                [
                    'branch_id' => $branch_id,
                    'branch_setting_id' => BranchSetting::where('key', $permission)->value('id'),
                ],
                ['status' => $status],
            );
        }

        $branch = Branch::find($branch_id);
        ModelsLog::create([
            'type' => 'branch',
            'type_id' => $branch_id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => 'تم التغيير في إعدادات الفرع **' . $branch->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);

        // إرجاع رسالة نجاح
        return redirect()->back()->with('success', 'تم تحديث الصلاحيات بنجاح');
    }

    public function getSettings(Request $request)
    {
        $selectedBranchId = $request->input('branch_id');
        $settings = [];

        if ($selectedBranchId) {
            // جلب الفرع المحدد مع الصلاحيات المرتبطة به
            $branch = Branch::with('settings')->find($selectedBranchId);

            if ($branch) {
                foreach ($branch->settings as $setting) {
                    $settings[] = [
                        'key' => $setting->key,
                        'name' => $setting->name,
                        'status' => $setting->pivot->status,
                    ];
                }
            }
        }

        return response()->json(['settings' => $settings]);
    }

    public function updateStatus($id)
    {
        $userBranchId = auth()->user()->branch_id; // جلب معرف الفرع الخاص بالمستخدم

        $branch = Branch::findOrFail($id);

        // منع المستخدم من إيقاف الفرع الذي ينتمي إليه
        if ($userBranchId == $branch->id) {
            return redirect()->route('branches.index')->with('error', 'لا يمكنك إيقاف الفرع الأساسى, عليك إختيار فرع أساسى أخر حتى يمكنك إيقاف الفرع.');
        }

        // تبديل الحالة فقط إذا كان الفرع مختلفًا عن فرع المستخدم
        $branch->update(['status' => $branch->status == 0 ? 1 : 0]);
        // تحديد النص بناءً على حالة الفرع
        $statusText = $branch->status == 1 ? 'تم تعطيل الفرع' : 'تم تنشيط الفرع';

        ModelsLog::create([
            'type' => 'branch',
            'type_id' => $branch->id, // ID النشاط المرتبط
            'type_log' => 'log', // نوع النشاط
            'description' => $statusText . ' **' . $branch->name . '**',
            'created_by' => auth()->id(), // ID المستخدم الحالي
        ]);
        return redirect()->route('branches.index')->with('success', 'تم تحديث حالة الفرع بنجاح');
    }
}
