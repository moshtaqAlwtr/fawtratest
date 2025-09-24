<?php


namespace Modules\Client\Http\Controllers;
use App\Http\Controllers\Controller;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Employee;
use App\Models\Log as ModelsLog;
use App\Http\Requests\AppointmentRequest;
use App\Models\Statuses;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
class AppointmentController extends Controller
{
    /**
     * عرض قائمة المواعيد.
     */
    public function index(Request $request)
    {
        $query = Appointment::query();

        // البحث حسب الحالة
        if ($request->has('status') && !empty($request->status)) {
            $statusValue = Appointment::$statusMap[$request->status] ?? null;
            if ($statusValue) {
                $query->where('status', $statusValue);
            }
        }

        // البحث حسب الموظف
        if ($request->has('employee_id') && !empty($request->employee_id)) {
            $query->where('created_by', $request->employee_id);
        }

        // البحث حسب العميل
        if ($request->has('client_id') && !empty($request->client_id)) {
            $query->where('client_id', $request->client_id);
        }

        // البحث حسب نوع الإجراء
        if ($request->has('action_type') && !empty($request->action_type)) {
            $query->where('action_type', $request->action_type);
        }

        // البحث حسب مسؤول المبيعات
        if ($request->has('sales_person_user') && !empty($request->sales_person_user)) {
            $query->where('created_by', $request->sales_person_user);
        }

        // البحث حسب التاريخ من
        if ($request->has('from_date') && !empty($request->from_date)) {
            $query->whereDate('date', '>=', $request->from_date);
        }

        // البحث حسب التاريخ إلى
        if ($request->has('to_date') && !empty($request->to_date)) {
            $query->whereDate('date', '<=', $request->to_date);
        }

        // البحث حسب الحالة (من جدول statuses)
        if ($request->has('status_id') && !empty($request->status_id)) {
            $query->whereHas('client', function($q) use ($request) {
                $q->where('status_id', $request->status_id);
            });
        }
        $appointments = $query->latest()->paginate(10);
        $employees = User::where('role','employee')->get();
        $clients = Client::all();
        $statuses = Statuses::all();
        $actionTypes = Appointment::distinct()->pluck('action_type')->filter()->values();

        return view('client::appointments.index', compact('appointments','statuses', 'employees', 'clients', 'actionTypes'));
    }
    /**
     * عرض صفحة إنشاء موعد جديد.
     */
    public function create()
    {
        $clients = Client::all();
        $employees = User::where('role','employee')->get();
        return view('client::appointments.create', compact('clients', 'employees'));
    }

    /**
     * تخزين موعد جديد.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'client_id' => 'required|exists:clients,id',
                'created_by' => 'nullable|exists:users,id',
                'date' => 'required|date|after_or_equal:today',
                'time' => 'required|date_format:H:i',
                'notes' => 'nullable|string|max:500',
            ],
            [
                'client_id.required' => 'يجب اختيار العميل',
                'client_id.exists' => 'العميل غير موجود',
                'created_by.exists' => 'الموظف غير موجود',
                'date.required' => 'يجب إدخال التاريخ',
                'date.date' => 'التاريخ غير صحيح',
                'date.after_or_equal' => 'يجب أن يكون التاريخ اليوم أو في المستقبل',
                'time.required' => 'يجب إدخال الوقت',
                'time.date_format' => 'صيغة الوقت غير صحيحة',
                'notes.max' => 'الملاحظات يجب أن تكون 500 حرف كحد أقصى',
            ],
        );
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }


        $appointment = new Appointment();
        $appointment->client_id = $request->client_id;

        $appointment->appointment_date = $request->date;
        $appointment->time = $request->time;
        $appointment->duration = $request->duration;
        $appointment->notes = $request->notes;
        $appointment->created_by = auth()->id();
        $appointment->action_type = $request->action_type;



        if (!empty($request->recurrence_type)) {
            $appointment->is_recurring = true;
            $appointment->recurrence_type = $request->recurrence_type;
            $appointment->recurrence_date = $request->recurrence_date;
        }

        $appointment->save();
           // تسجيل اشعار نظام جديد
            ModelsLog::create([
                'type' => 'client',
                'type_id' => $appointment->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
             'description' => 'تم اضافة موعد جديد',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);




        return redirect()->route('appointments.index')->with('success', 'تم إضافة الموعد بنجاح');
    }

    /**
     * عرض تفاصيل موعد.
     */
    public function show($id)
    {
        $appointment = Appointment::with(['client', 'employee'])->findOrFail($id);
        $client = Client::findOrFail($appointment->client_id);
        return view('client::appointments.show', compact('appointment', 'client'));
    }

    public function edit($id)
    {
        $appointment = Appointment::findOrFail($id);
        $clients = Client::all();
        $employees = User::where('role','employee')->get();
        return view('client::appointments.edit', compact('appointment', 'clients', 'employees'));
    }

    /**
     * تحديث بيانات الموعد
     */

    /**
     * عرض صفحة تعديل موعد.
     */

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:' . implode(',', [Appointment::STATUS_PENDING, Appointment::STATUS_COMPLETED, Appointment::STATUS_IGNORED, Appointment::STATUS_RESCHEDULED]),
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            log::error('Validation Failed', [
                'errors' => $validator->errors(),
                'input' => $request->all(),
            ]);

            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Find the appointment by ID or throw an exception
        $appointment = Appointment::findOrFail($request->id ?? $request->appointment_id);

        $oldStatus = $appointment->status;
        $appointment->status = $request->status;

        // إضافة الملاحظات إذا وجدت
        if ($request->filled('notes')) {
            $appointment->notes = $request->notes;
        }

        $appointment->save();

        log::info('Appointment Updated', [
            'id' => $appointment->id,
            'old_status' => $oldStatus,
            'new_status' => $appointment->status,
        ]);

        return redirect()
            ->route('appointments.index', $appointment->id)
            ->with('success', 'تم تحديث الموعد بنجاح');

        return redirect()
            ->back()
            ->with('error', 'حدث خطأ أثناء تحديث الموعد: ' . $e->getMessage());
    }
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->delete();

        return redirect()->back()->with('success', 'تم حذف الموعد وجميع البيانات المرتبطة به بنجاح');
    }

    /**
     * Mark appointment as ignored.
     */
    public function ignore($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'ignored';
        $appointment->save();

        return response()->json(['success' => true]);
    }

    /**
     * Mark appointment as completed.
     */
    public function complete($id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->status = 'completed';
        $appointment->save();

        return response()->json(['success' => true]);
    }

    /**
     * Add note to appointment.
     */
    public function addNote(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->notes = $appointment->notes ? $appointment->notes . "\n" . $request->note : $request->note;
        $appointment->save();

        return response()->json(['success' => true]);
    }

    /**
     * تحديث حالة الموعد
     */
    public function updateStatus($id, Request $request)
    {
        $appointment = Appointment::findOrFail($id);

        // Update status
        $oldStatus = $appointment->status;
        $appointment->status = $request->input('status');
        $appointment->save();

        // Delete appointment if status is completed (2) or ignored (3)
        if (in_array($request->input('status'), [2, 3])) {
            $appointment->delete();

            // Redirect with success message based on status
            $message = $request->input('status') == 2 ? 'تم اكتمال الموعد وحذفه بنجاح' : 'تم صرف النظر عن الموعد وحذفه بنجاح';

            return redirect()
                ->back()
                ->with([
                    'toast_type' => 'success',
                    'toast_message' => $message,
                ]);
        }

        // Redirect with success message for other status changes
        return redirect()
            ->back()
            ->with([
                'toast_type' => 'success',
                'toast_message' => 'تم تحديث حالة الموعد بنجاح',
            ]);
    }

    protected function getStatusText($status)
    {
        return Appointment::$statusArabicMap[$status] ?? 'غير معروف';
    }

    /**
     * الحصول على لون الحالة
     */
    protected function getStatusColor($status)
    {
        return match ($status) {
            Appointment::STATUS_PENDING => 'bg-warning text-dark',
            Appointment::STATUS_COMPLETED => 'bg-success text-white',
            Appointment::STATUS_IGNORED => 'bg-danger text-white',
            Appointment::STATUS_RESCHEDULED => 'bg-info text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * حذف موعد
     */
    public function destroyAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_id' => 'required|exists:appointments,id',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'errors' => $validator->errors(),
                ],
                400,
            );
        }

        $appointment = Appointment::findOrFail($request->appointment_id);
        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الموعد بنجاح',
        ]);
    }

    /**
     * Get appointments for calendar view
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function calendar()
    {
        $appointments = Appointment::with(['client', 'createdBy'])
            ->where('appointment_date', '>=', now()->subMonths(3)) // Last 3 months
            ->get()
            ->map(function ($appointment) {
                $statusText = $this->getStatusText($appointment->status);
                $statusColor = $this->getStatusColor($appointment->status);

                return [
                    'id' => $appointment->id,
                    'title' => $appointment->client->trade_name ?? 'عميل',
                    'start' => $appointment->appointment_date . ($appointment->time ? 'T' . $appointment->time : ''),
                    'allDay' => false,
                    'backgroundColor' => $this->getStatusColorCode($appointment->status),
                    'borderColor' => $this->getStatusColorCode($appointment->status),
                    'textColor' => in_array($appointment->status, [Appointment::STATUS_PENDING, Appointment::STATUS_RESCHEDULED]) ? '#000' : '#fff',
                    'extendedProps' => [
                        'client_name' => $appointment->client->trade_name ?? 'غير معروف',
                        'client_phone' => $appointment->client->phone ?? 'غير متوفر',
                        'time' => $appointment->time ?? 'غير محدد',
                        'status' => $statusText,
                        'employee' => $appointment->createdBy->name ?? 'غير معين',
                        'notes' => $appointment->notes ?? 'لا توجد ملاحظات',
                    ]
                ];
            });

        return response()->json($appointments);
    }

    /**
     * Get color code for status
     */
    protected function getStatusColorCode($status)
    {
        return match ($status) {
            Appointment::STATUS_PENDING => '#ffc107',    // Yellow
            Appointment::STATUS_COMPLETED => '#28a745',  // Green
            Appointment::STATUS_IGNORED => '#dc3545',    // Red
            Appointment::STATUS_RESCHEDULED => '#17a2b8', // Cyan
            default => '#6c757d',                        // Gray
        };
    }
}
