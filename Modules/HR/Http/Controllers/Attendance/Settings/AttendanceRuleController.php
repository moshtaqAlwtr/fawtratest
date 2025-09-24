<?php

namespace Modules\HR\Http\Controllers\Attendance\Settings;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRule;
use App\Models\Shift;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class AttendanceRuleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $attendanceRules = AttendanceRule::with('shift')
            ->when(request('keywords'), function ($query) {
                $query->where('name', 'like', '%' . request('keywords') . '%')->orWhere('description', 'like', '%' . request('keywords') . '%');
            })
            ->when(request('status'), function ($query) {
                $query->where('status', request('status'));
            })
            ->when(request('shift'), function ($query) {
                $query->where('shift_id', request('shift'));
            })
            ->latest()
            ->paginate(10);

        $shifts = Shift::all();

        return view('hr::attendance.settings.attendance_rules.index', compact('attendanceRules', 'shifts'));
    }

    /**
     * البحث في قواعد الحضور بـ AJAX
     */
    public function search(Request $request)
    {
        try {
            $attendanceRules = AttendanceRule::with('shift')
                ->when($request->keywords, function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->keywords . '%')->orWhere('description', 'like', '%' . $request->keywords . '%');
                })
                ->when($request->status, function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->when($request->shift, function ($query) use ($request) {
                    $query->where('shift_id', $request->shift);
                })
                ->latest()
                ->paginate(10);

            $html = view('hr::attendance.settings.attendance_rules.table-content', compact('attendanceRules'))->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $attendanceRules->total(),
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء البحث: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $shifts = Shift::all();

        return view('hr::attendance.settings.attendance_rules.create', compact('shifts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate(
            [
                'name' => 'required|string|max:255|unique:attendance_rules,name',
                'color' => 'required|string|max:255',

                'status' => 'required|in:active,inactive',
                'shift_id' => 'required|exists:shifts,id',
                'description' => 'nullable|string|max:1000',
                'formula' => 'nullable|string|max:500',
                'condition' => 'nullable|string|max:500',
            ],
            [
                'name.required' => 'اسم قاعدة الحضور مطلوب',
                'name.unique' => 'اسم قاعدة الحضور موجود مسبقاً',
                'name.max' => 'اسم قاعدة الحضور يجب أن يكون أقل من 255 حرف',
                'color.required' => 'اللون مطلوب',
                'color.regex' => 'تنسيق اللون غير صحيح',
                'status.required' => 'الحالة مطلوبة',
                'status.in' => 'الحالة يجب أن تكون نشط أو غير نشط',
                'shift_id.required' => 'الوردية مطلوبة',
                'shift_id.exists' => 'الوردية المحددة غير موجودة',
                'description.max' => 'الوصف يجب أن يكون أقل من 1000 حرف',
                'formula.max' => 'الصيغة الحسابية يجب أن تكون أقل من 500 حرف',
                'condition.max' => 'الشرط يجب أن يكون أقل من 500 حرف',
            ],
        );

        try {
            $validatedData = AttendanceRule::create($validatedData);
            ModelsLog::create([
                'type' => 'attendance_rules',
                'type_id' => $validatedData->id,
                'type_log' => 'log',
                'description' => 'تم اضافة قاعدة الحضور ' . $request->name,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('attendance-rules.index')->with('success', 'تم إضافة قاعدة الحضور بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء إضافة قاعدة الحضور. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AttendanceRule $attendanceRule): View
    {
        $attendanceRule = AttendanceRule::findOrFail($attendanceRule->id);

        $logs = ModelsLog::where('type', 'attendance_rules')
            ->where('type_id', $attendanceRule->id)
            ->whereHas('attendance_rules_log') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('hr::attendance.settings.attendance_rules.show', compact('attendanceRule', 'logs'));
    }


    /**
     * حذف قاعدة الحضور
     */


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AttendanceRule $attendanceRule): View
    {
        $shifts = Shift::where('status', 'active')->get();

        return view('hr::attendance.settings.attendance_rules.edit', compact('attendanceRule', 'shifts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AttendanceRule $attendanceRule): RedirectResponse
    {
        $validatedData = $request->validate(
            [
                'name' => ['required', 'string', 'max:255', Rule::unique('attendance_rules', 'name')->ignore($attendanceRule->id)],
                'color' => 'required|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
                'status' => 'required|in:active,inactive',
                'shift_id' => 'required|exists:shifts,id',
                'description' => 'nullable|string|max:1000',
                'formula' => 'nullable|string|max:500',
                'condition' => 'nullable|string|max:500',
            ],
            [
                'name.required' => 'اسم قاعدة الحضور مطلوب',
                'name.unique' => 'اسم قاعدة الحضور موجود مسبقاً',
                'name.max' => 'اسم قاعدة الحضور يجب أن يكون أقل من 255 حرف',
                'color.required' => 'اللون مطلوب',
                'color.regex' => 'تنسيق اللون غير صحيح',
                'status.required' => 'الحالة مطلوبة',
                'status.in' => 'الحالة يجب أن تكون نشط أو غير نشط',
                'shift_id.required' => 'الوردية مطلوبة',
                'shift_id.exists' => 'الوردية المحددة غير موجودة',
                'description.max' => 'الوصف يجب أن يكون أقل من 1000 حرف',
                'formula.max' => 'الصيغة الحسابية يجب أن تكون أقل من 500 حرف',
                'condition.max' => 'الشرط يجب أن يكون أقل من 500 حرف',
            ],
        );

        try {
            $attendanceRule->update($validatedData);
            ModelsLog::create([
                'type' => 'attendance_rules',
                'type_id' => $attendanceRule->id,
                'type_log' => 'log',
                'description' => 'تم تعديل قاعدة الحضور ' . $request->name,
                'created_by' => auth()->id(),
            ]);

            return redirect()->route('attendance-rules.index')->with('success', 'تم تحديث قاعدة الحضور بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'حدث خطأ أثناء تحديث قاعدة الحضور. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
public function destroy($id)
{
    try {
        $rule = AttendanceRule::findOrFail($id);
        $ruleName = $rule->name; // احفظ الاسم قبل الحذف

        $rule->delete();

        ModelsLog::create([
            'type' => 'attendance_rules',
            'type_id' => $rule->id,
            'type_log' => 'log',
            'description' => 'تم حذف قاعدة الحضور ' . $ruleName,
            'created_by' => auth()->id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "تم حذف قاعدة الحضور '{$ruleName}' بنجاح"
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء الحذف: ' . $e->getMessage()
        ], 500);
    }
}
    /**
     * Toggle the status of the attendance rule.
     */
    // في الـ Controller
public function toggleStatus($id)
{
    try {
        $rule = AttendanceRule::findOrFail($id);
        $rule->status = $rule->status === 'active' ? 'inactive' : 'active';
        $rule->save();


            ModelsLog::create([
                'type' => 'attendance_rules',
                'type_id' => $rule->id,
                'type_log' => 'log',
                'description' => 'تم ' . $rule->status . ' قاعدة الحضور في سياسة الاجازات ' . $rule->name,
                'created_by' => auth()->id(),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'تم تغيير الحالة بنجاح',
            'new_status' => $rule->status
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ أثناء تغيير الحالة: ' . $e->getMessage()
        ], 500);
    }
}
}
