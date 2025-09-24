<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
class AttendanceDaysRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:present,absent,late',

            'absence_type' => 'nullable|in:1,2|required_if:status,absent',
            'absence_balance' => 'nullable|integer|min:0|required_if:status,absent',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'employee_id.required' => 'يجب اختيار الموظف.',
            'employee_id.exists' => 'الموظف المحدد غير موجود.',
            'attendance_date.required' => 'يجب تحديد تاريخ الحضور.',
            'attendance_date.date' => 'التاريخ غير صالح.',
            'status.required' => 'يجب اختيار الحالة.',
            'status.in' => 'الحالة المختارة غير صحيحة.',

            'login_time.required_if' => 'تسجيل الدخول مطلوب عندما تكون الحالة حاضر.',
            'login_time.date_format' => 'تسجيل الدخول يجب أن يكون بصيغة الوقت الصحيحة.',
            'logout_time.required_if' => 'تسجيل الخروج مطلوب عندما تكون الحالة حاضر.',
            'logout_time.date_format' => 'تسجيل الخروج يجب أن يكون بصيغة الوقت الصحيحة.',
            'absence_type.required_if' => 'نوع الإجازة مطلوب عندما تكون الحالة إجازة.',
            'absence_balance.required_if' => 'رصيد الإجازة مطلوب عندما تكون الحالة إجازة.',
        ];
    }
}
