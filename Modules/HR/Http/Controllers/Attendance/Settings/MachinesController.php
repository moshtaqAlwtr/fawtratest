<?php

namespace Modules\HR\Http\Controllers\Attendance\Settings;

use App\Http\Controllers\Controller;
use App\Models\Machine;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MachinesController extends Controller
{
    /**
     * عرض قائمة الماكينات
     */
      public function index(Request $request)
    {
        $query = Machine::query();

        // تطبيق الفلاتر
        $this->applyFilters($query, $request);

        // ترتيب النتائج
        $machines = $query->orderBy('created_at', 'desc')->paginate(10);

        // إذا كان الطلب AJAX، أرجع JSON مع HTML
        if ($request->ajax()) {
            return $this->ajaxResponse($machines);
        }

        return view('hr::attendance.settings.machines.index', compact('machines'));
    }

    /**
     * البحث في الماكينات باستخدام AJAX
     */
    public function search(Request $request)
    {
        try {
            $query = Machine::query();

            // تطبيق الفلاتر
            $this->applyFilters($query, $request);

            // ترتيب وترقيم النتائج
            $machines = $query->orderBy('created_at', 'desc')->paginate(10);

            return $this->ajaxResponse($machines);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء البحث: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * تطبيق فلاتر البحث على الاستعلام
     */
    private function applyFilters($query, Request $request)
    {
        // البحث بالاسم أو الرقم التسلسلي
        if ($request->filled('name')) {
            $searchTerm = $request->name;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('serial_number', 'like', '%' . $searchTerm . '%');
            });
        }

        // فلترة بالحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلترة بنوع الماكينة
        if ($request->filled('machine_type')) {
            $query->where('machine_type', $request->machine_type);
        }

        return $query;
    }

    /**
     * إرجاع استجابة AJAX للبحث
     */
    private function ajaxResponse($machines)
    {
        try {
            $html = view('hr::attendance.settings.machines.table-content', compact('machines'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $machines->total(),
                'current_page' => $machines->currentPage(),
                'last_page' => $machines->lastPage(),
                'per_page' => $machines->perPage()
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحضير النتائج: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * عرض نموذج إنشاء ماكينة جديدة
     */
    public function create()
    {
        return view('hr::attendance.settings.machines.create');
    }

    /**
     * حفظ ماكينة جديدة
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255|unique:machines,name',
                'machine_type' => 'required|string|in:zkteco,hikvision,suprema,other',
                'host_name' => 'required|string|max:255',
                'port_number' => 'required|integer|min:1|max:65535',
                'serial_number' => 'nullable|string|max:255|unique:machines,serial_number',
                'connection_key' => 'nullable|string|max:255',
                'status' => 'nullable|boolean',
            ],
            [
                'name.required' => 'اسم الماكينة مطلوب',
                'name.unique' => 'اسم الماكينة موجود مسبقاً',
                'machine_type.required' => 'نوع الماكينة مطلوب',
                'machine_type.in' => 'نوع الماكينة غير صحيح',
                'host_name.required' => 'المضيف مطلوب',
                'port_number.required' => 'رقم المنفذ مطلوب',
                'port_number.integer' => 'رقم المنفذ يجب أن يكون رقماً',
                'port_number.min' => 'رقم المنفذ يجب أن يكون أكبر من 0',
                'port_number.max' => 'رقم المنفذ يجب أن يكون أقل من 65536',
                'serial_number.unique' => 'الرقم التسلسلي موجود مسبقاً',
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'يرجى تصحيح الأخطاء المذكورة أدناه');
        }

        try {
            // إنشاء الماكينة الجديدة
            $machine = Machine::create([
                'name' => $request->name,
                'machine_type' => $request->machine_type,
                'host_name' => $request->host_name,
                'port_number' => $request->port_number,
                'serial_number' => $request->serial_number,
                'connection_key' => $request->connection_key,
                'status' => $request->has('status') ? true : false,
            ]);

            ModelsLog::create([
                'type' => 'machine_log',
                'type_id' => $machine->id,
                'type_log' => 'log',
                'description' => 'تم اضافة بيانات الماكينة: ' . $machine->name,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('machines.index')->with('success', 'تم إضافة الماكينة بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء إضافة الماكينة: ' . $e->getMessage());
        }
    }

    /**
     * عرض تفاصيل ماكينة محددة
     */
    public function show($id)
    {
        $machine = Machine::findOrFail($id);

        $logs = ModelsLog::where('type', 'machine_log')
            ->where('type_id', $id)
            ->with(['user.branch'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('hr::attendance.settings.machines.show', compact('machine', 'logs'));
    }

    /**
     * عرض نموذج تعديل الماكينة
     */
    public function edit($id)
    {
        $machine = Machine::findOrFail($id);
        return view('hr::attendance.settings.machines.edit', compact('machine'));
    }

    /**
     * تحديث بيانات الماكينة
     */
    public function update(Request $request, $id)
    {
        $machine = Machine::findOrFail($id);

        // التحقق من صحة البيانات
        $validator = Validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255|unique:machines,name,' . $machine->id,
                'machine_type' => 'required|string|in:zkteco,hikvision,suprema,other',
                'host_name' => 'required|string|max:255',
                'port_number' => 'required|integer|min:1|max:65535',
                'serial_number' => 'nullable|string|max:255|unique:machines,serial_number,' . $machine->id,
                'connection_key' => 'nullable|string|max:255',
                'status' => 'nullable|boolean',
            ],
            [
                'name.required' => 'اسم الماكينة مطلوب',
                'name.unique' => 'اسم الماكينة موجود مسبقاً',
                'machine_type.required' => 'نوع الماكينة مطلوب',
                'machine_type.in' => 'نوع الماكينة غير صحيح',
                'host_name.required' => 'المضيف مطلوب',
                'port_number.required' => 'رقم المنفذ مطلوب',
                'port_number.integer' => 'رقم المنفذ يجب أن يكون رقماً',
                'port_number.min' => 'رقم المنفذ يجب أن يكون أكبر من 0',
                'port_number.max' => 'رقم المنفذ يجب أن يكون أقل من 65536',
                'serial_number.unique' => 'الرقم التسلسلي موجود مسبقاً',
            ],
        );

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'يرجى تصحيح الأخطاء المذكورة أدناه');
        }

        try {
            // تحديث بيانات الماكينة
            $machine->update([
                'name' => $request->name,
                'machine_type' => $request->machine_type,
                'host_name' => $request->host_name,
                'port_number' => $request->port_number,
                'serial_number' => $request->serial_number,
                'connection_key' => $request->connection_key,
                'status' => $request->has('status') ? true : false,
            ]);

            ModelsLog::create([
                'type' => 'machine_log',
                'type_id' => $machine->id,
                'type_log' => 'log',
                'description' => 'تم تحديث بيانات الماكينة: ' . $machine->name,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('machines.index')->with('success', 'تم تحديث بيانات الماكينة بنجاح');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث الماكينة: ' . $e->getMessage());
        }
    }

    /**
     * حذف الماكينة
     */
    public function destroy($id)
    {
        try {
            $machine = Machine::findOrFail($id);

            // احفظ اسم المكينة والـ ID قبل الحذف
            $machineName = $machine->name;
            $machineId = $machine->id;

            // إنشاء اللوج قبل الحذف
            ModelsLog::create([
                'type' => 'machine_log',
                'type_id' => $machineId,
                'type_log' => 'log',
                'description' => 'تم حذف بيانات الماكينة: ' . $machineName,
                'created_by' => auth()->id(),
            ]);

            // حذف المكينة بعد إنشاء اللوج
            $machine->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الماكينة "' . $machineName . '" بنجاح',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الماكينة غير موجودة',
            ], 404);

        } catch (\Exception $e) {
            // إضافة تفاصيل أكثر للخطأ
            Log::error('خطأ في حذف المكينة: ' . $e->getMessage(), [
                'machine_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الماكينة. يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }

    /**
     * تغيير حالة الماكينة (تفعيل/إلغاء تفعيل)
     */
    public function toggleStatus($id)
    {
        try {
            $machine = Machine::findOrFail($id);

            // احفظ الحالة القديمة والجديدة
            $oldStatus = $machine->status;
            $newStatus = !$machine->status;

            // تحديث حالة المكينة
            $machine->status = $newStatus;
            $saved = $machine->save();

            // التحقق من نجاح الحفظ
            if (!$saved) {
                throw new \Exception('فشل في حفظ تغييرات الماكينة');
            }

            // تحديد النص المناسب
            $statusText = $newStatus ? 'تم تفعيل' : 'تم إلغاء تفعيل';

            // تسجيل في السجلات (Logs)
            ModelsLog::create([
                'type' => 'machine_log',
                'type_id' => $machine->id,
                'type_log' => 'log',
                'description' => $statusText . ' الماكينة: ' . $machine->name . ' (من ' . ($oldStatus ? 'نشط' : 'غير نشط') . ' إلى ' . ($newStatus ? 'نشط' : 'غير نشط') . ')',
                'created_by' => auth()->id(),
            ]);

            // إرجاع الرد JSON
            return response()->json([
                'success' => true,
                'message' => $statusText . ' الماكينة "' . $machine->name . '" بنجاح',
                'new_status' => $newStatus,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الماكينة غير موجودة',
            ], 404);

        } catch (\Exception $e) {
            // إضافة تفاصيل أكثر للخطأ
            Log::error('خطأ في تغيير حالة المكينة: ' . $e->getMessage(), [
                'machine_id' => $id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تغيير حالة الماكينة. يرجى المحاولة مرة أخرى.',
            ], 500);
        }
    }
}