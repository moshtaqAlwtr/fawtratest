<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomShiftRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'use_rules' => 'required|in:rules,employees',
            'shift_id' => 'required|exists:shifts,id',
            'branch_id' => 'nullable|exists:branches,id',
            'department_id' => 'nullable|exists:departments,id',
            'job_title_id' => 'nullable|exists:jop_titles,id',
            'shift_rule_id' => 'nullable|exists:shifts,id',
        ];
    }

    public function messages(): array
    {
        return [
        'from_date.required' => 'حقل "من التاريخ" مطلوب.',
        'from_date.date' => 'حقل "من التاريخ" يجب أن يكون تاريخًا صالحًا.',
        'to_date.required' => 'حقل "إلى التاريخ" مطلوب.',
        'name.required' => 'حقل "اسم الورديه" مطلوب.',
        'shift_id.required' => 'حقل "إالورديه" مطلوب.',
        'to_date.date' => 'حقل "إلى التاريخ" يجب أن يكون تاريخًا صالحًا.',
        'to_date.after_or_equal' => 'حقل "إلى التاريخ" يجب أن يكون مساويًا أو بعد "من التاريخ".',
        'use_rules.required' => 'يجب اختيار أحد المعايير (القواعد أو الموظفين).',
        'use_rules.in' => 'القيمة المحددة في "المعايير" غير صحيحة.',
        'employee_id.exists' => 'الموظف المختار غير موجود.',
        'branch_id.exists' => 'الفرع المختار غير موجود.',
        'department_id.exists' => 'القسم المختار غير موجود.',
        'job_title_id.exists' => 'المسمى الوظيفي المختار غير موجود.',
        'shifts_id.exists' => 'الوردية المختارة غير موجودة.',
        'shifts_rule_id.exists' => 'الوردية المختارة غير موجودة.',
        ];
    }
}
