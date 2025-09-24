<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShiftRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'=>'required',
             'type' => 'required|in:basic,advanced', // نوع الوردية
            // التأكد من أن أوقات الأيام مدخلة بشكل صحيح
            '*.start' => 'nullable|date_format:H:i',
            '*.end' => 'nullable|date_format:H:i',
            '*.checkin_start' => 'nullable|date_format:H:i',
            '*.checkin_end' => 'nullable|date_format:H:i',
            '*.checkout_start' => 'nullable|date_format:H:i',
            '*.checkout_end' => 'nullable|date_format:H:i',
            '*.delay' => 'nullable|integer|min:0', // فترة السماح بالدقائق
        ];
    }

    public function messages()
    {
        return [
            'required'=>'هذا الحقل مطلوب'
        ];
    }
}
