<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WorkStationRequest extends FormRequest
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
            'code' => 'required|string|max:50|unique:work_stations,code,' . $this->id,
            'unit' => 'nullable|string|max:100',
            'description' => 'nullable|string',

            'cost_expenses' => 'nullable|array',
            'cost_expenses.*' => 'min:0',

            'account_expenses' => 'nullable|array',
            'account_expenses.*' => 'exists:accounts,id',

            'cost_wages' => 'nullable|min:0',
            'account_wages' => 'nullable|exists:accounts,id',

            'cost_origin' => 'nullable|min:0',
            'origin' => 'nullable|exists:accounts,id',

            'total_cost' => 'min:0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $cost_expenses = $this->input('cost_expenses', []);
            $cost_wages = $this->input('cost_wages', 0);
            $cost_origin = $this->input('cost_origin', 0);

            $hasValidCost = array_sum($cost_expenses) > 0 || $cost_wages > 0 || $cost_origin > 0;

            if (!$hasValidCost) {
                $validator->errors()->add('costs', 'يجب عليك إدخال تكلفة واحدة على الأقل من: الأجور، الأصول أو المصروفات الأخرى.');
            }
        });
    }

    public function messages()
    {
        return [
            'name.required' => 'يجب إدخال اسم محطة العمل.',
            'name.string' => 'يجب أن يكون الاسم نصاً.',
            'name.max' => 'يجب ألا يزيد الاسم عن 255 حرفاً.',

            'code.required' => 'يجب إدخال كود محطة العمل.',
            'code.string' => 'يجب أن يكون الكود نصاً.',
            'code.max' => 'يجب ألا يزيد الكود عن 50 حرفاً.',
            'code.unique' => 'هذا الكود مستخدم بالفعل.',

            'cost_expenses.*.min' => 'يجب ألا تكون قيمة التكلفة سالبة.',

            'cost_wages.min' => 'يجب ألا تكون تكلفة الأجور سالبة.',

            'cost_origin.min' => 'يجب ألا تكون تكلفة الأصل سالبة.',

            'total_cost.min' => 'يجب ألا يكون إجمالي التكلفة سالباً.',
        ];
    }
}
