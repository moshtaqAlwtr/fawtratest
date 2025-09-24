<?php

namespace Modules\HR\Http\Controllers\Attendance\Settings;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\LeaveType;
use App\Models\Log as ModelsLog;
use App\Models\EmployeeLeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class EmployeeLeaveBalanceController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeLeaveBalance::with(['employee', 'leaveType'])->orderBy('created_at', 'desc');

        // تسجيل المعاملات للتشخيص
        Log::info('Filter parameters:', $request->all());

        // فلترة حسب الموظف
        if ($request->filled('employee_id') && $request->employee_id != '') {
            $query->where('employee_id', $request->employee_id);
        }

        // فلترة حسب نوع الإجازة
        if ($request->filled('leave_type_id') && $request->leave_type_id != '') {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        // فلترة حسب السنة
        if ($request->filled('year') && $request->year != '') {
            $query->where('year', $request->year);
        }

        $balances = $query->paginate(15);

        // إذا كان الطلب AJAX، إرجاع الجدول فقط
        if ($request->ajax()) {
            $html = view('hr::attendance.settings.employee_leave_balances.table_rows', compact('balances'))->render();

            return response()->json([
                'html' => $html,
                'total' => $balances->total(),
                'current_page' => $balances->currentPage(),
                'last_page' => $balances->lastPage(),
            ]);
        }

        $employees = Employee::all();
        $leaveTypes = LeaveType::all();

        return view('hr::attendance.settings.employee_leave_balances.index', compact('balances', 'employees', 'leaveTypes'));
    }
    /**
     * عرض صفحة إضافة رصيد جديد
     */
    public function create()
    {
        $employees = Employee::all();
        $leaveTypes = LeaveType::all();

        return view('hr::attendance.settings.employee_leave_balances.create', compact('employees', 'leaveTypes'));
    }

    /**
     * حفظ رصيد إجازة جديد
     */

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate(
                [
                    'employee_id' => 'required|exists:employees,id',
                    'leave_type_id' => 'required|exists:leave_types,id',
                    'year' => 'required|integer|min:2020|max:2030',
                    'initial_balance' => 'required|integer|min:0|max:365',
                    'carried_forward' => 'nullable|integer|min:0|max:365',
                    'additional_balance' => 'nullable|integer|min:0|max:365',
                    'notes' => 'nullable|string|max:1000',
                ],
                [
                    'employee_id.required' => 'يرجى اختيار الموظف',
                    'employee_id.exists' => 'الموظف المحدد غير موجود',
                    'leave_type_id.required' => 'يرجى اختيار نوع الإجازة',
                    'leave_type_id.exists' => 'نوع الإجازة المحدد غير موجود',
                    'year.required' => 'السنة مطلوبة',
                    'year.integer' => 'السنة يجب أن تكون رقماً صحيحاً',
                    'year.min' => 'السنة يجب أن تكون 2020 أو أكثر',
                    'year.max' => 'السنة يجب أن تكون 2030 أو أقل',
                    'initial_balance.required' => 'الرصيد المبدئي مطلوب',
                    'initial_balance.integer' => 'الرصيد المبدئي يجب أن يكون رقماً صحيحاً',
                    'initial_balance.min' => 'الرصيد المبدئي لا يمكن أن يكون سالباً',
                    'initial_balance.max' => 'الرصيد المبدئي لا يمكن أن يزيد عن 365 يوماً',
                    'carried_forward.integer' => 'المرحل من السنة السابقة يجب أن يكون رقماً صحيحاً',
                    'carried_forward.min' => 'المرحل من السنة السابقة لا يمكن أن يكون سالباً',
                    'carried_forward.max' => 'المرحل من السنة السابقة لا يمكن أن يزيد عن 365 يوماً',
                    'additional_balance.integer' => 'الرصيد الإضافي يجب أن يكون رقماً صحيحاً',
                    'additional_balance.min' => 'الرصيد الإضافي لا يمكن أن يكون سالباً',
                    'additional_balance.max' => 'الرصيد الإضافي لا يمكن أن يزيد عن 365 يوماً',
                    'notes.max' => 'الملاحظات لا يمكن أن تزيد عن 1000 حرف',
                ],
            );

            // منع التكرار لنفس (الموظف/النوع/السنة)
            $existingBalance = EmployeeLeaveBalance::where([
                'employee_id' => $validatedData['employee_id'],
                'leave_type_id' => $validatedData['leave_type_id'],
                'year' => $validatedData['year'],
            ])->first();

            if ($existingBalance) {
                return back()
                    ->withErrors([
                        'duplicate' => 'يوجد رصيد سابق لهذا الموظف في نفس السنة ونوع الإجازة',
                    ])
                    ->withInput();
            }

            DB::transaction(function () use ($validatedData) {
                $totalAvailable = $validatedData['initial_balance'] + ($validatedData['carried_forward'] ?? 0) + ($validatedData['additional_balance'] ?? 0);

                EmployeeLeaveBalance::create([
                    'employee_id' => $validatedData['employee_id'],
                    'leave_type_id' => $validatedData['leave_type_id'],
                    'year' => $validatedData['year'],
                    'initial_balance' => $validatedData['initial_balance'],
                    'used_balance' => 0,
                    'remaining_balance' => $totalAvailable,
                    'carried_forward' => $validatedData['carried_forward'] ?? 0,
                    'additional_balance' => $validatedData['additional_balance'] ?? 0,
                    'notes' => $validatedData['notes'] ?? null,
                ]);

                $employee = Employee::find($validatedData['employee_id']);
                $leaveType = LeaveType::find($validatedData['leave_type_id']);

                // ملاحظة مهمّة: غالبًا هذا السطر كان سبب الخطأ
                // كان مكتوب: $employee->employee->full_name
                // والأصح على الأغلب:
                ModelsLog::create([
                    'type' => 'employee_leave_balance',
                    'type_id' => $employee->id,
                    'type_log' => 'log',
                    'description' => 'تم إنشاء رصيد إجازة جديد للموظف: ' . ($employee->full_name ?? '#' . $employee->id),
                    'created_by' => auth()->id(),
                ]);
            });

            return redirect()->route('employee_leave_balances.index')->with('success', 'تم إنشاء رصيد الإجازة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // نعيد أخطاء التحقق كما هي
            return back()->withErrors($e->validator)->withInput();
        } catch (QueryException $e) {
            // أخطاء قاعدة البيانات (رسالة SQL أو القيود)
            Log::error('DB Error أثناء إنشاء رصيد الإجازة: ' . $e->getMessage(), [
                'error_info' => $e->errorInfo,
                'sql' => method_exists($e, 'getSql') ? $e->getSql() : null,
                'bindings' => method_exists($e, 'getBindings') ? $e->getBindings() : null,
                'request' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $detail = $e->errorInfo[2] ?? $e->getMessage();
            if ($request->expectsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'خطأ بقاعدة البيانات',
                        'detail' => $detail,
                    ],
                    500,
                );
            }

            return back()
                ->with('error_detail', "خطأ بقاعدة البيانات:\n{$detail}")
                ->withInput();
        } catch (Throwable $e) {
            // أي أخطاء أخرى
            Log::error('خطأ عام أثناء إنشاء رصيد الإجازة: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => collect($e->getTrace())->take(5)->all(),
                'request' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $message = $e->getMessage();
            $file = $e->getFile();
            $line = $e->getLine();
            $trace = collect($e->getTrace())
                ->take(5)
                ->map(function ($t, $i) {
                    $loc = ($t['file'] ?? 'unknown') . ':' . ($t['line'] ?? '?');
                    $fn = $t['function'] ?? 'closure';
                    return sprintf('#%d %s (%s)', $i, $fn, $loc);
                })
                ->implode("\n");

            if ($request->expectsJson()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => $message,
                        'file' => $file,
                        'line' => $line,
                        'trace' => $trace,
                    ],
                    500,
                );
            }

            // إن كنت تريد إظهاره دائمًا للمستخدم:
            $detail = "الرسالة: {$message}\nالملف: {$file}\nالسطر: {$line}\n\nTrace:\n{$trace}";

            // ولو تحب تقييده بوضع التطوير فقط فبدّل السطر التالي بشرط: if (config('app.debug')) { ... } else { رسالة عامة }
            return back()->with('error_detail', $detail)->withInput();
        }
    } /**
     * عرض صفحة تعديل رصيد الإجازة
     */
    public function edit(EmployeeLeaveBalance $employeeLeaveBalance)
    {
        $balance = $employeeLeaveBalance->load(['employee', 'leaveType']);

        // حساب معلومات إضافية للاستخدام
        $approvedRequestsCount = LeaveRequest::where('employee_id', $balance->employee_id)->where('leave_type_id', $balance->leave_type_id)->where('status', 'approved')->whereYear('start_date', $balance->year)->count();

        $lastUsage = LeaveRequest::where('employee_id', $balance->employee_id)->where('leave_type_id', $balance->leave_type_id)->where('status', 'approved')->whereYear('start_date', $balance->year)->orderBy('approved_at', 'desc')->first();

        $lastUsageDate = $lastUsage ? $lastUsage->approved_at->format('Y-m-d') : null;

        $usagePercentage = $balance->getTotalAvailableBalance() > 0 ? round(($balance->used_balance / $balance->getTotalAvailableBalance()) * 100, 2) : 0;

        return view('hr::attendance.settings.employee_leave_balances.edit', compact('balance', 'approvedRequestsCount', 'lastUsageDate', 'usagePercentage'));
    }

public function show($id)
{
    $balance = EmployeeLeaveBalance::with([
        'employee',
        'employee.department',

        'employee.branch',
        'leaveType',
        'employee.leaveRequests' => function($query) {
            $query->where('status', 'approved')
                  ->orderBy('created_at', 'desc');
        }
    ])->findOrFail($id);

    // جلب طلبات الإجازات للموظف لنفس النوع والسنة
    $leaveRequests = $balance->employee->leaveRequests()
        ->where('leave_type_id', $balance->leave_type_id)
        ->where('status', 'approved')
        ->whereYear('start_date', $balance->year)
        ->with('leaveType')
        ->orderBy('start_date', 'desc')
        ->get();

    // إحصائيات الموظف لهذه السنة
    // $employeeStats = [
    //     'total_approved_requests' => $balance->employee->leaveRequests()
    //         ->where('status', 'approved')
    //         ->whereYear('start_date', $balance->year)
    //         ->count(),
    //     'total_days_taken' => $balance->employee->leaveRequests()
    //         ->where('status', 'approved')
    //         ->whereYear('start_date', $balance->year)
    //         ->sum('total_days'),
    //     'pending_requests' => $balance->employee->leaveRequests()
    //         ->where('status', 'pending')
    //         ->whereYear('start_date', $balance->year)
    //         ->count(),
    // ];

    // حساب النسبة المئوية للاستخدام
    $usagePercentage = $balance->getTotalAvailableBalance() > 0
        ? round(($balance->used_balance / $balance->getTotalAvailableBalance()) * 100, 1)
        : 0;

    // جلب سجل النشاطات (إذا كان لديك جدول logs)
    $logs = collect();
    if (class_exists('App\Models\Log')) {
        $logs = \App\Models\Log::where('type', 'employee_leave_balances')
            ->where('type_id', $id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
    }

    // إنشاء سجل نشاطات أساسي إذا لم يكن متوفر
    if ($logs->isEmpty()) {
        $logs = collect([
            $balance->created_at->format('Y-m-d') => collect([
                (object) [
                    'id' => 1,
                    'action' => 'created',
                    'description' => 'تم إنشاء رصيد الإجازة',
                    'user' => (object) [
                        'name' => 'النظام',
                        'id' => 0
                    ],
                    'created_at' => $balance->created_at,
                ]
            ])
        ]);

        if ($balance->created_at != $balance->updated_at) {
            $updateLog = (object) [
                'id' => 2,
                'action' => 'updated',
                'description' => 'تم تحديث رصيد الإجازة',
                'user' => (object) [
                    'name' => 'النظام',
                    'id' => 0
                ],
                'created_at' => $balance->updated_at,
            ];

            $updateDate = $balance->updated_at->format('Y-m-d');
            if ($logs->has($updateDate)) {
                $logs[$updateDate]->push($updateLog);
            } else {
                $logs->put($updateDate, collect([$updateLog]));
            }
        }
    }

    return view('hr::attendance.settings.employee_leave_balances.show', compact(
        'balance',
        'leaveRequests',

        'usagePercentage',
        'logs'
    ));
}
    /**
     * تحديث رصيد الإجازة
     */
    public function update(Request $request, EmployeeLeaveBalance $employeeLeaveBalance)
    {
        try {
            $validatedData = $request->validate(
                [
                    'year' => ['required', 'integer', 'min:2020', 'max:2030', Rule::unique('employee_leave_balances')->where('employee_id', $employeeLeaveBalance->employee_id)->where('leave_type_id', $employeeLeaveBalance->leave_type_id)->ignore($employeeLeaveBalance->id)],
                    'initial_balance' => 'required|integer|min:0|max:365',
                    'carried_forward' => 'nullable|integer|min:0|max:365',
                    'additional_balance' => 'nullable|integer|min:0|max:365',
                    'notes' => 'nullable|string|max:1000',
                ],
                [
                    'year.required' => 'السنة مطلوبة',
                    'year.unique' => 'يوجد رصيد آخر لهذا الموظف في نفس السنة ونوع الإجازة',
                    'initial_balance.required' => 'الرصيد المبدئي مطلوب',
                    'initial_balance.min' => 'الرصيد المبدئي لا يمكن أن يكون سالباً',
                    'initial_balance.max' => 'الرصيد المبدئي لا يمكن أن يزيد عن 365 يوماً',
                    'carried_forward.min' => 'المرحل من السنة السابقة لا يمكن أن يكون سالباً',
                    'additional_balance.min' => 'الرصيد الإضافي لا يمكن أن يكون سالباً',
                    'notes.max' => 'الملاحظات لا يمكن أن تزيد عن 1000 حرف',
                ],
            );

            DB::transaction(function () use ($employeeLeaveBalance, $validatedData) {
                $newTotalAvailable = $validatedData['initial_balance'] + ($validatedData['carried_forward'] ?? 0) + ($validatedData['additional_balance'] ?? 0);

                $newRemainingBalance = max(0, $newTotalAvailable - $employeeLeaveBalance->used_balance);

                $updateNote = "\n" . now()->format('Y-m-d H:i:s') . ': تم التحديث بواسطة ' . (auth()->user()->name ?? '#' . auth()->id());
                if ($newTotalAvailable < $employeeLeaveBalance->used_balance) {
                    $updateNote .= ' - تحذير: الرصيد الجديد أقل من المستخدم';
                }

                $employeeLeaveBalance->update([
                    'year' => $validatedData['year'],
                    'initial_balance' => $validatedData['initial_balance'],
                    'remaining_balance' => $newRemainingBalance,
                    'carried_forward' => $validatedData['carried_forward'] ?? 0,
                    'additional_balance' => $validatedData['additional_balance'] ?? 0,
                    'notes' => ($validatedData['notes'] ?? '') . $updateNote,
                ]);

                // سجل النشاط
                ModelsLog::create([
                    'type' => 'employee_leave_balance',
                    'type_id' => $employeeLeaveBalance->id,
                    'type_log' => 'log',
                    'description' => 'تم تحديث رصيد الإجازة للموظف: ' . ($employeeLeaveBalance->employee->full_name ?? '#' . $employeeLeaveBalance->employee_id),
                    'created_by' => auth()->id(),
                ]);
            });

            return redirect()->route('employee_leave_balances.index')->with('success', 'تم تحديث رصيد الإجازة بنجاح');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        } catch (QueryException $e) {
            Log::error('DB Error أثناء تحديث رصيد الإجازة: ' . $e->getMessage(), [
                'error_info' => $e->errorInfo,
                'request' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $detail = $e->errorInfo[2] ?? $e->getMessage();
            return back()
                ->with('error_detail', "خطأ بقاعدة البيانات:\n{$detail}")
                ->withInput();
        } catch (Throwable $e) {
            Log::error('خطأ عام أثناء تحديث رصيد الإجازة: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            $detail = "الرسالة: {$e->getMessage()}\nالملف: {$e->getFile()}\nالسطر: {$e->getLine()}";
            return back()->with('error_detail', $detail)->withInput();
        }
    }

    /**
     * حذف رصيد الإجازة
     */
    public function destroy(EmployeeLeaveBalance $employeeLeaveBalance)
    {
        try {
            // التحقق من وجود طلبات معتمدة
            if ($employeeLeaveBalance->used_balance > 0) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'لا يمكن حذف هذا الرصيد لأنه مُستخدم في طلبات معتمدة',
                    ],
                    422,
                );
            }

            DB::transaction(function () use ($employeeLeaveBalance) {
                // تسجيل الحذف في السجل
                Log::info('تم حذف رصيد الإجازة', [
                    'balance_id' => $employeeLeaveBalance->id,
                    'employee' => $employeeLeaveBalance->employee->full_name,
                    'leave_type' => $employeeLeaveBalance->leaveType->name,
                    'year' => $employeeLeaveBalance->year,
                    'deleted_by' => auth()->id(),
                ]);

                $employeeLeaveBalance->delete();
            });

            return response()->json([
                'success' => true,
                'message' => 'تم حذف رصيد الإجازة بنجاح',
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في حذف رصيد الإجازة: ' . $e->getMessage(), [
                'balance_id' => $employeeLeaveBalance->id,
                'user_id' => auth()->id(),
            ]);

            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء حذف رصيد الإجازة',
                ],
                500,
            );
        }
    }

    /**
     * فحص وجود رصيد سابق (AJAX)
     */
    public function checkExisting(Request $request)
    {
        $existingBalance = EmployeeLeaveBalance::where([
            'employee_id' => $request->employee_id,
            'leave_type_id' => $request->leave_type_id,
            'year' => $request->year,
        ])->first();

        if ($existingBalance) {
            return response()->json([
                'exists' => true,
                'balance' => [
                    'initial_balance' => $existingBalance->initial_balance,
                    'used_balance' => $existingBalance->used_balance,
                    'remaining_balance' => $existingBalance->getActualRemainingBalance(),
                    'carried_forward' => $existingBalance->carried_forward,
                    'additional_balance' => $existingBalance->additional_balance,
                ],
            ]);
        }

        return response()->json(['exists' => false]);
    }

    /**
     * تحديث جميع الأرصدة بناءً على الطلبات المعتمدة
     */
    public function recalculateBalances(Request $request)
    {
        try {
            $year = $request->input('year', now()->year);
            $employeeId = $request->input('employee_id');

            $query = EmployeeLeaveBalance::where('year', $year);

            if ($employeeId) {
                $query->where('employee_id', $employeeId);
            }

            $balances = $query->get();
            $updatedCount = 0;

            DB::transaction(function () use ($balances, &$updatedCount) {
                foreach ($balances as $balance) {
                    // حساب الأيام المستخدمة من الطلبات المعتمدة
                    $usedDays = LeaveRequest::where('employee_id', $balance->employee_id)->where('leave_type_id', $balance->leave_type_id)->where('status', 'approved')->whereYear('start_date', $balance->year)->sum('days');

                    // تحديث الرصيد
                    $balance->update([
                        'used_balance' => $usedDays,
                        'remaining_balance' => max(0, $balance->getTotalAvailableBalance() - $usedDays),
                    ]);

                    $updatedCount++;
                }
            });

            return response()->json([
                'success' => true,
                'message' => "تم تحديث {$updatedCount} رصيد بنجاح",
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في إعادة حساب الأرصدة: ' . $e->getMessage());

            return response()->json(
                [
                    'success' => false,
                    'message' => 'حدث خطأ أثناء إعادة حساب الأرصدة',
                ],
                500,
            );
        }
    }
}
