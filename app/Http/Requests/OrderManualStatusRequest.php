<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderManualStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'active' => 'nullable|boolean',
            'name' => 'required|array|min:1', // يجب أن يكون مصفوفة وتحتوي على عنصر واحد على الأقل
            'name.*' => 'required|string|max:255', // كل عنصر يجب أن يكون نصاً وغير فارغ
            'color' => 'required|array',
            'color.*' => 'nullable|string|regex:/^#(?:[0-9a-fA-F]{3}){1,2}$/', // لون بصيغة HEX
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'يجب إدخال اسم واحد على الأقل.',
            'name.*.required' => 'لا يمكن ترك اسم الحالة فارغًا.',
            'color.*.regex' => 'يجب أن يكون اللون بتنسيق HEX صالح (#RRGGBB أو #RGB).',
        ];
    }
}
