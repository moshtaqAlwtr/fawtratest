<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
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
            // معلومات الموظف
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'nickname' => 'nullable|string|max:255',
            'employee_photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string',
            'email' => ['required', Rule::unique('employees', 'email')->ignore($this->id)],
            'employee_type' => 'required|string',
            'status' => 'required',

            'language' => 'required|string|max:2',
            'Job_role_id' => 'required|exists:job_roles,id',
            // 'access_branches_id' => 'nullable|exists:branches,id',

            // معلومات شخصية
            'date_of_birth' => 'nullable|date',
            'nationality_status' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',

            // معلومات تواصل
            'mobile_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
            'phone_number' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:8|max:15',
            // 'personal_email' => 'nullable|email|unique:employees,personal_email',

            // العنوان الحالي
            'current_address_1' => 'nullable|string|max:255',
            'current_address_2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'region' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',

            // معلومات الوظيفة
            'job_title' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'job_level' => 'nullable|string|max:255',
            'job_type' => 'nullable|string|max:255',
            // 'branch_id' => 'required|exists:branches,id',
            'direct_manager_id' => 'nullable|exists:employees,id',
            'hire_date' => 'nullable|date',
            // 'shift_id' => 'nullable|exists:shifts,id',
            'custom_financial_month' => 'nullable|integer|between:1,12',
            'custom_financial_day' => 'nullable|integer|between:1,31',
        ];
    }

    /**
     * Customize the error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'حقل الاسم الأول مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'gender.in' => 'القيمة المحددة للجنس غير صحيحة.',
            'branch_id' => 'حقل الفرع مطلوب',
            'branch_id.exists' => 'الفرع المحدد غير موجود.',
            'custom_financial_month.between' => 'الشهر المالي يجب أن يكون بين 1 و 12.',
            'custom_financial_day.between' => 'اليوم المالي يجب أن يكون بين 1 و 31.',
            'employee_photo.image' => 'يجب أن يكون الملف صورة من نوع JPEG أو PNG أو JPG.',
            'employee_photo.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت.',
            // 'personal_email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            // 'personal_email.email' => 'يجب أن يكون البريد الإلكتروني صالحًا.',
        ];
    }
}
