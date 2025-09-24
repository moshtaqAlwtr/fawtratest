<?php

namespace Modules\HR\Http\Controllers\Attendance;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Employee;
use App\Models\LeavePermissions;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use App\Models\Log as ModelsLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class LeaveRequestsController extends Controller
{

   public function index()
{
    $leaveRequests = LeaveRequest::with(['employee', 'approver', 'leaveType'])
        ->orderBy('created_at', 'desc')
        ->paginate(15);
        $leaveTypes = LeaveType::all();
$departments = Department::all();
$branches = Branch::all();

    return view('hr::attendance.leave_requests.index', compact('leaveRequests','leaveTypes','departments','branches'));
}


public function search(Request $request)
{
    $query = LeaveRequest::with(['employee', 'approver', 'leaveType']);


    // فلترة بواسطة الموظف (الاسم أو الكود)
    if ($request->filled('employee_search')) {
        $search = $request->employee_search;
        $query->whereHas('employee', function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('middle_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('employee_code', 'LIKE', "%{$search}%");
        });
    }

    // فلترة بواسطة تاريخ بداية الإجازة
    if ($request->filled('from_date')) {
        $query->where('start_date', '>=', $request->from_date);
    }

    // فلترة بواسطة تاريخ نهاية الإجازة
    if ($request->filled('to_date')) {
        $query->where('end_date', '<=', $request->to_date);
    }

    // فلترة بواسطة تاريخ الإنشاء
    if ($request->filled('created_date')) {
        $query->whereDate('created_at', $request->created_date);
    }

    // فلترة بواسطة نوع الإجازة
    if ($request->filled('leave_type')) {
        $query->whereHas('leaveType', function($q) use ($request) {
            $q->where('id', $request->leave_type);
        });
    }

    // فلترة بواسطة القسم
    if ($request->filled('department')) {
        $query->whereHas('employee', function($q) use ($request) {
            $q->where('department_id', $request->department);
        });
    }

    // فلترة بواسطة الفرع
    if ($request->filled('branch')) {
        $query->whereHas('employee', function($q) use ($request) {
            $q->where('branch_id', $request->branch);
        });
    }

    // فلترة بواسطة الحالة
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(15);

    // إرجاع HTML للجدول والـ pagination
    $tableHtml = view('hr::attendance.leave_requests.partials.table_rows', compact('leaveRequests'))->render();
    $paginationHtml = $leaveRequests->appends($request->all())->links()->render();

    return response()->json([
        'table' => $tableHtml,
        'pagination' => $paginationHtml,
        'total' => $leaveRequests->total()
    ]);
}

/**
 * الحصول على قائمة الموظفين للبحث
 */
public function getEmployeesList()
{
    try {
        $employees = Employee::select('id', 'first_name', 'middle_name', 'last_name')
            ->where('is_active', true) // فقط الموظفين النشطين
            ->orderBy('first_name', 'asc')
            ->get();

        // إضافة الاسم الكامل لكل موظف
        $employees->transform(function($employee) {
            $employee->full_name = trim($employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name);
            return $employee;
        });

        return response()->json($employees);

    } catch (\Exception $e) {
        Log::error('خطأ في تحميل قائمة الموظفين: ' . $e->getMessage());
        return response()->json(['error' => 'حدث خطأ في تحميل الموظفين'], 500);
    }
}

/**
 * الحصول على رصيد إجازات موظف معين
 */

    /**
     * عرض نموذج إنشاء طلب إجازة جديد
     */
    public function create()
    {
        $employees = Employee::all();

        $leaveTypes = LeaveType::all();

        return view('hr::attendance.leave_requests.create', compact('employees', 'leaveTypes'));
    }

    /**
     * حفظ طلب إجازة جديد
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'request_type' => 'required|in:leave,emergency,sick',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'days' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ], [
            'employee_id.required' => 'يجب اختيار موظف',
            'employee_id.exists' => 'الموظف المحدد غير موجود',
            'request_type.required' => 'يجب اختيار نوع الطلب',
            'request_type.in' => 'نوع الطلب غير صحيح',
            'leave_type_id.required' => 'يجب اختيار نوع الإجازة',
            'leave_type_id.exists' => 'نوع الإجازة المحدد غير موجود',
            'start_date.required' => 'يجب تحديد تاريخ البدء',
            'start_date.date' => 'تاريخ البدء غير صحيح',
            'start_date.after_or_equal' => 'تاريخ البدء يجب أن يكون اليوم أو بعده',
            'days.required' => 'يجب تحديد عدد الأيام',
            'days.integer' => 'عدد الأيام يجب أن يكون رقم صحيح',
            'days.min' => 'عدد الأيام يجب أن يكون أكبر من صفر',
            'description.max' => 'الوصف يجب ألا يزيد عن 1000 حرف',
            'attachments.*.file' => 'المرفق يجب أن يكون ملف',
            'attachments.*.mimes' => 'المرفقات المسموحة: PDF, DOC, DOCX, JPG, JPEG, PNG',
            'attachments.*.max' => 'حجم الملف يجب ألا يزيد عن 5 ميجابايت'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            // حساب تاريخ الانتهاء
            $startDate = Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addDays($request->days - 1);

            // التحقق من تداخل الإجازات
            $existingLeave = LeaveRequest::where('employee_id', $request->employee_id)
                ->where('status', '!=', 'rejected')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                              ->where('end_date', '>=', $endDate);
                        });
                })
                ->exists();

            if ($existingLeave) {
                return back()
                    ->withInput()
                    ->with('error', 'يوجد طلب إجازة أخر متداخل مع هذه الفترة');
            }

            // رفع المرفقات إذا وجدت
            $attachments = [];
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('leave_requests', $filename, 'public');
                    $attachments[] = $path;
                }
            }

            // إنشاء طلب الإجازة
            $leaveRequest = LeaveRequest::create([
                'employee_id' => $request->employee_id,
                'request_type' => $request->request_type,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $request->days,
                'description' => $request->description,
                'status' => 'pending',
                'attachments' => !empty($attachments) ? json_encode($attachments) : null,
            ]);

                        ModelsLog::create([
                'type' => 'leave_requests',
                'type_id' => $leaveRequest->id,
                'type_log' => 'log',
                'description' => 'تم إنشاء طلب إجازة جديد للموظف: ' . $leaveRequest->employee->full_name,
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('attendance.leave_requests.index')
                ->with('success', 'تم إنشاء طلب الإجازة بنجاح')
                ->with('sweet_alert', [
                    'type' => 'success',
                    'title' => 'نجح الحفظ!',
                    'message' => 'تم إنشاء طلب الإجازة بنجاح'
                ]);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء حفظ طلب الإجازة: ' . $e->getMessage())
                ->with('sweet_alert', [
                    'type' => 'error',
                    'title' => 'خطأ!',
                    'message' => 'حدث خطأ أثناء حفظ البيانات'
                ]);
        }
    }

    /**
     * عرض تفاصيل طلب إجازة محدد
     */



        public function show($id, Request $request)
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        // جلب سجل الإجازات للموظف مع الفلترة
        $leaveRecordsQuery = LeavePermissions::where('employee_id', $leaveRequest->employee_id)
            ->with('employee')
            ->orderBy('start_date', 'desc');

        // فلترة حسب الشهر والسنة إذا تم تمريرها
        if ($request->filled('filter_month')) {
            $leaveRecordsQuery->whereMonth('start_date', $request->filter_month);
        }

        if ($request->filled('filter_year')) {
            $leaveRecordsQuery->whereYear('start_date', $request->filter_year);
        }

        $leaveRecords = $leaveRecordsQuery->get();

        // جلب سجل النشاطات مع التجميع حسب التاريخ
        $logs = ModelsLog::where('type', 'leave_requests')
            ->where('type_id', $id)
            ->with('user.branch')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });

        return view('hr::attendance.leave_requests.show', compact('leaveRequest', 'leaveRecords', 'logs'));
    }


    /**
     * عرض نموذج تعديل طلب الإجازة
     */
    public function edit(LeaveRequest $leaveRequest)
    {
        // التحقق من إمكانية التعديل
        if (!$leaveRequest->isPending()) {
            return redirect()
                ->route('attendance.leave_requests.index')
                ->with('error', 'لا يمكن تعديل طلب الإجازة بعد الموافقة عليه أو رفضه')
                ->with('sweet_alert', [
                    'type' => 'warning',
                    'title' => 'تحذير!',
                    'message' => 'لا يمكن تعديل هذا الطلب'
                ]);
        }

        $employees = Employee::where('status', 'active')
            ->orderBy('frirst_name')
            ->get();

        $leaveTypes = LeaveType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('hr::attendance.leave_requests.edit', compact('leaveRequest', 'employees', 'leaveTypes'));
    }

    /**
     * تحديث طلب الإجازة
     */
    public function update(Request $request, LeaveRequest $leaveRequest)
    {
        // التحقق من إمكانية التعديل
        if (!$leaveRequest->isPending()) {
            return redirect()
                ->route('attendance.leave_requests.index')
                ->with('error', 'لا يمكن تعديل طلب الإجازة بعد الموافقة عليه أو رفضه')
                ->with('sweet_alert', [
                    'type' => 'warning',
                    'title' => 'تحذير!',
                    'message' => 'لا يمكن تعديل هذا الطلب'
                ]);
        }

        // التحقق من صحة البيانات
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|exists:employees,id',
            'request_type' => 'required|in:leave,emergency,sick',
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'days' => 'required|integer|min:1',
            'description' => 'nullable|string|max:1000',
            'attachments.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120'
        ], [
            'employee_id.required' => 'يجب اختيار موظف',
            'employee_id.exists' => 'الموظف المحدد غير موجود',
            'request_type.required' => 'يجب اختيار نوع الطلب',
            'request_type.in' => 'نوع الطلب غير صحيح',
            'leave_type_id.required' => 'يجب اختيار نوع الإجازة',
            'leave_type_id.exists' => 'نوع الإجازة المحدد غير موجود',
            'start_date.required' => 'يجب تحديد تاريخ البدء',
            'start_date.date' => 'تاريخ البدء غير صحيح',
            'days.required' => 'يجب تحديد عدد الأيام',
            'days.integer' => 'عدد الأيام يجب أن يكون رقم صحيح',
            'days.min' => 'عدد الأيام يجب أن يكون أكبر من صفر',
            'description.max' => 'الوصف يجب ألا يزيد عن 1000 حرف',
            'attachments.*.file' => 'المرفق يجب أن يكون ملف',
            'attachments.*.mimes' => 'المرفقات المسموحة: PDF, DOC, DOCX, JPG, JPEG, PNG',
            'attachments.*.max' => 'حجم الملف يجب ألا يزيد عن 5 ميجابايت'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'يرجى التحقق من البيانات المدخلة');
        }

        try {
            // حساب تاريخ الانتهاء
            $startDate = Carbon::parse($request->start_date);
            $endDate = $startDate->copy()->addDays($request->days - 1);

            // التحقق من تداخل الإجازات (باستثناء الطلب الحالي)
            $existingLeave = LeaveRequest::where('employee_id', $request->employee_id)
                ->where('id', '!=', $leaveRequest->id)
                ->where('status', '!=', 'rejected')
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date', [$startDate, $endDate])
                        ->orWhere(function ($q) use ($startDate, $endDate) {
                            $q->where('start_date', '<=', $startDate)
                              ->where('end_date', '>=', $endDate);
                        });
                })
                ->exists();

            if ($existingLeave) {
                return back()
                    ->withInput()
                    ->with('error', 'يوجد طلب إجازة أخر متداخل مع هذه الفترة');
            }

            // التعامل مع المرفقات الجديدة
            $attachments = $leaveRequest->attachments ? json_decode($leaveRequest->attachments, true) : [];

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('leave_requests', $filename, 'public');
                    $attachments[] = $path;
                }
            }

            // تحديث طلب الإجازة
            $leaveRequest->update([
                'employee_id' => $request->employee_id,
                'request_type' => $request->request_type,
                'leave_type_id' => $request->leave_type_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'days' => $request->days,
                'description' => $request->description,
                'attachments' => !empty($attachments) ? json_encode($attachments) : null,
            ]);
                        ModelsLog::create([
                'type' => 'leave_requests',
                'type_id' => $leaveRequest->id,
                'type_log' => 'log',
                'description' => 'تم تحديث طلب إجازة جديد للموظف: ' . $leaveRequest->employee->full_name,
                'created_by' => auth()->id(),
            ]);

            return redirect()
                ->route('attendance.leave_requests.index')
                ->with('success', 'تم تحديث طلب الإجازة بنجاح')
                ->with('sweet_alert', [
                    'type' => 'success',
                    'title' => 'نجح التحديث!',
                    'message' => 'تم تحديث طلب الإجازة بنجاح'
                ]);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'حدث خطأ أثناء تحديث طلب الإجازة: ' . $e->getMessage())
                ->with('sweet_alert', [
                    'type' => 'error',
                    'title' => 'خطأ!',
                    'message' => 'حدث خطأ أثناء تحديث البيانات'
                ]);
        }
    }

    /**
     * حذف طلب الإجازة
     */
    public function destroy(LeaveRequest $leaveRequest)
    {
        try {
            // التحقق من إمكانية الحذف
            if ($leaveRequest->isApproved()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن حذف طلب إجازة مقبول'
                ], 422);
            }

            // حذف المرفقات
            if ($leaveRequest->attachments) {
                $attachments = json_decode($leaveRequest->attachments, true);
                foreach ($attachments as $attachment) {
                    Storage::disk('public')->delete($attachment);
                }
            }

            // حذف الطلب
            $leaveRequest->delete();
                                    ModelsLog::create([
                'type' => 'leave_requests',
                'type_id' => $leaveRequest->id,
                'type_log' => 'log',
                'description' => 'تم حذف طلب إجازة جديد للموظف: ' . $leaveRequest->employee->full_name,
                'created_by' => auth()->id(),
            ]);


            return response()->json([
                'success' => true,
                'message' => 'تم حذف طلب الإجازة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف طلب الإجازة'
            ], 500);
        }
    }

 public function getEmployeeLeaveBalance(Request $request)
    {
        try {
            $employeeId = $request->employee_id;

            if (!$employeeId) {
                return response()->json(['error' => 'معرف الموظف مطلوب'], 400);
            }

            $employee = Employee::findOrFail($employeeId);

            // الحصول على أنواع الإجازات
            $leaveTypes = LeaveType::where('is_active', true)->get();
            $leaveTypesBalance = [];

            $totalBalance = 0;
            $totalUsed = 0;

            foreach ($leaveTypes as $leaveType) {
                // حساب الرصيد المستخدم لكل نوع إجازة هذا العام
                $usedDays = LeaveRequest::where('employee_id', $employeeId)
                    ->where('leave_type_id', $leaveType->id)
                    ->where('status', 'approved')
                    ->whereYear('start_date', now()->year)
                    ->sum('days');

                // الرصيد الكلي للنوع
                $typeBalance = $this->getEmployeeLeaveTypeBalance($employeeId, $leaveType->id);
                $remaining = max(0, $typeBalance - $usedDays);

                $leaveTypesBalance[] = [
                    'name' => $leaveType->name,
                    'total_balance' => $typeBalance,
                    'used_balance' => $usedDays,
                    'remaining_balance' => $remaining
                ];

                $totalBalance += $typeBalance;
                $totalUsed += $usedDays;
            }

            // تجهيز بيانات الموظف
            $employeeData = [
                'id' => $employee->id,
                'name' => $employee->full_name,
                'employee_code' => $employee->employee_code ?? $employee->id
            ];

            return response()->json([
                'success' => true,
                'employee' => $employeeData,
                'total_balance' => $totalBalance,
                'used_balance' => $totalUsed,
                'remaining_balance' => max(0, $totalBalance - $totalUsed),
                'leave_types' => $leaveTypesBalance
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في تحميل رصيد الإجازات: ' . $e->getMessage());
            return response()->json(['error' => 'حدث خطأ في تحميل رصيد الإجازات'], 500);
        }
    }

    /**
     * الحصول على رصيد نوع إجازة معين للموظف
     */
    private function getEmployeeLeaveTypeBalance($employeeId, $leaveTypeId)
    {
        $leaveType = LeaveType::find($leaveTypeId);

        if (!$leaveType) {
            return 0;
        }

        // يمكن تخصيص هذه القيم حسب نظام الشركة
        switch ($leaveType->code) {
            case 'annual':
                return 30; // 30 يوم إجازة سنوية
            case 'casual':
                return 7;  // 7 أيام إجازة عارضة
            case 'sick':
                return 15; // 15 يوم إجازة مرضية
            case 'emergency':
                return 3;  // 3 أيام إجازة طارئة
            case 'maternity':
                return 60; // 60 يوم إجازة أمومة
            case 'paternity':
                return 3;  // 3 أيام إجازة أبوة
            default:
                return $leaveType->default_days ?? 0;
        }
    }

    /**
     * التحقق من توفر رصيد كافي قبل الموافقة
     */
    private function checkLeaveBalance($employeeId, $leaveTypeId, $requestedDays)
    {
        // الرصيد الإجمالي لنوع الإجازة
        $totalBalance = $this->getEmployeeLeaveTypeBalance($employeeId, $leaveTypeId);

        // الأيام المستخدمة فعلاً (الطلبات المعتمدة فقط)
        $usedDays = LeaveRequest::where('employee_id', $employeeId)
            ->where('leave_type_id', $leaveTypeId)
            ->where('status', 'approved')
            ->whereYear('start_date', now()->year)
            ->sum('days');

        $availableBalance = $totalBalance - $usedDays;

        return [
            'has_sufficient_balance' => $availableBalance >= $requestedDays,
            'available_balance' => $availableBalance,
            'total_balance' => $totalBalance,
            'used_balance' => $usedDays,
            'requested_days' => $requestedDays
        ];
    }

    /**
     * الموافقة على طلب الإجازة
     */
    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        try {
            if (!$leaveRequest->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن الموافقة على هذا الطلب - الطلب ليس في حالة الانتظار'
                ], 422);
            }

            // التحقق من توفر الرصيد
            $balanceCheck = $this->checkLeaveBalance(
                $leaveRequest->employee_id,
                $leaveRequest->leave_type_id,
                $leaveRequest->days
            );

            if (!$balanceCheck['has_sufficient_balance']) {
                return response()->json([
                    'success' => false,
                    'message' => sprintf(
                        'الرصيد المتاح غير كافي. المطلوب: %d أيام، المتاح: %d أيام',
                        $balanceCheck['requested_days'],
                        $balanceCheck['available_balance']
                    )
                ], 422);
            }

            DB::transaction(function () use ($leaveRequest, $request) {
                $leaveRequest->update([
                    'status' => 'approved',

                ]);

                // تسجيل في سجل النشاطات
                ModelsLog::create([
                    'type' => 'leave_requests',
                    'type_id' => $leaveRequest->id,
                    'type_log' => 'approved',
                    'description' => sprintf(
                        'تم قبول طلب إجازة للموظف: %s - النوع: %s - المدة: %d أيام',
                        $leaveRequest->employee->full_name,
                        $leaveRequest->leaveType->name ?? 'غير محدد',
                        $leaveRequest->days
                    ),
                    'created_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'تم قبول طلب الإجازة بنجاح وخصم الأيام من الرصيد'
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في الموافقة على الطلب: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الطلب'
            ], 500);
        }
    }

    /**
     * رفض طلب الإجازة
     */
    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ], [
            'rejection_reason.required' => 'يجب إدخال سبب الرفض',
            'rejection_reason.max' => 'سبب الرفض يجب ألا يزيد عن 500 حرف'
        ]);

        try {
            if (!$leaveRequest->isPending()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن رفض هذا الطلب'
                ], 422);
            }

            DB::transaction(function () use ($leaveRequest, $request) {
                $leaveRequest->update([
                    'status' => 'rejected',
                    'approved_by' => auth()->id(),
                    'approved_at' => now(),
                    'rejection_reason' => $request->rejection_reason,
                ]);

                ModelsLog::create([
                    'type' => 'leave_requests',
                    'type_id' => $leaveRequest->id,
                    'type_log' => 'rejected',
                    'description' => sprintf(
                        'تم رفض طلب إجازة للموظف: %s - السبب: %s',
                        $leaveRequest->employee->full_name,
                        $request->rejection_reason
                    ),
                    'created_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'تم رفض طلب الإجازة'
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في رفض الطلب: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء معالجة الطلب'
            ], 500);
        }
    }

    /**
     * إلغاء الموافقة على طلب الإجازة
     */
    public function cancelApproval(Request $request, LeaveRequest $leaveRequest)
    {
        try {
            if ($leaveRequest->status !== 'approved') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إلغاء الموافقة - الطلب ليس في حالة موافق عليه'
                ], 422);
            }

            DB::transaction(function () use ($leaveRequest, $request) {
                $leaveRequest->update([
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'approval_note' => null,
                    'cancellation_reason' => $request->input('note'),
                    'cancelled_by' => auth()->id(),
                    'cancelled_at' => now()
                ]);

                ModelsLog::create([
                    'type' => 'leave_requests',
                    'type_id' => $leaveRequest->id,
                    'type_log' => 'cancelled',
                    'description' => sprintf(
                        'تم إلغاء موافقة طلب إجازة للموظف: %s - تم إرجاع %d أيام للرصيد',
                        $leaveRequest->employee->full_name,
                        $leaveRequest->days
                    ),
                    'created_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الموافقة بنجاح وإرجاع الأيام إلى رصيد الموظف'
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في إلغاء الموافقة: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إلغاء الموافقة'
            ], 500);
        }
    }

    /**
     * الرجوع من رفض طلب الإجازة
     */
    public function returnFromRejection(Request $request, LeaveRequest $leaveRequest)
    {
        try {
            if ($leaveRequest->status !== 'rejected') {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن الرجوع من الرفض - الطلب ليس في حالة مرفوض'
                ], 422);
            }

            DB::transaction(function () use ($leaveRequest, $request) {
                $leaveRequest->update([
                    'status' => 'pending',
                    'approved_by' => null,
                    'approved_at' => null,
                    'rejection_reason' => null,
                    'return_reason' => $request->input('note'),
                    'returned_by' => auth()->id(),
                    'returned_at' => now()
                ]);

                ModelsLog::create([
                    'type' => 'leave_requests',
                    'type_id' => $leaveRequest->id,
                    'type_log' => 'returned',
                    'description' => sprintf(
                        'تم الرجوع من رفض طلب إجازة للموظف: %s - أصبح الطلب في حالة انتظار',
                        $leaveRequest->employee->full_name
                    ),
                    'created_by' => auth()->id(),
                ]);
            });

            return response()->json([
                'success' => true,
                'message' => 'تم الرجوع من الرفض بنجاح - أصبح الطلب في حالة انتظار'
            ]);

        } catch (\Exception $e) {
            Log::error('خطأ في الرجوع من الرفض: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء الرجوع من الرفض'
            ], 500);
        }
    }

    /**
     * معالجة طلبات الإجراءات المختلفة
     */
    public function processAction(Request $request, LeaveRequest $leaveRequest)
    {
        $action = $request->input('action');

        switch ($action) {
            case 'approved':
                return $this->approve($request, $leaveRequest);
            case 'rejected':
                return $this->reject($request, $leaveRequest);
            case 'cancel_approval':
                return $this->cancelApproval($request, $leaveRequest);
            case 'return_from_rejection':
                return $this->returnFromRejection($request, $leaveRequest);
            default:
                return response()->json([
                    'success' => false,
                    'message' => 'إجراء غير صحيح'
                ], 400);
        }
    }

    /**
     * عرض تفاصيل طلب الإجازة مع الرصيد
     */

}
