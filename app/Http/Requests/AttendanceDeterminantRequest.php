<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceDeterminantRequest extends FormRequest
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
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'image_investigation' => 'nullable|in:0,1',
            'allowed_ips' => 'nullable|string',
            'location_investigation' => 'nullable|in:0,1',
            'enable_ip_verification' => 'required|in:0,1',
            'radius' => 'required_if:enable_location_verification,1|numeric|min:1',
            'radius_type' => 'required_if:enable_location_verification,1|in:1,2',

        ];
    }

    /**
     * رسائل التحقق المخصصة.
     */
    public function messages()
    {
        return [
            'name.required' => 'اسم الحقل مطلوب.',
            'status.required' => 'يجب اختيار الحالة.',
            'radius.required_if' => 'يجب تحديد النطاق عند تمكين التحقق من الموقع.',
            'latitude.required' => 'يجب تحديد خط العرض.',
            'longitude.required' => 'يجب تحديد خط الطول.',
        ];
    }

}
