<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
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
            'client_id' => 'required|exists:clients,id',
            'employee_id' => 'required|exists:employees,id',
            'quote_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_1' => 'nullable|numeric|min:0',
            'items.*.tax_2' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'currency' => 'nullable|string|size:3',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'client_id.required' => 'يجب اختيار العميل',
            'client_id.exists' => 'العميل غير موجود',
            'employee_id.required' => 'يجب اختيار الموظف',
            'employee_id.exists' => 'الموظف غير موجود',
            'quote_date.required' => 'يجب تحديد تاريخ عرض السعر',
            'quote_date.date' => 'تاريخ عرض السعر غير صحيح',
            'items.required' => 'يجب إضافة منتج واحد على الأقل',
            'items.min' => 'يجب إضافة منتج واحد على الأقل',
            'items.*.product_id.required' => 'يجب اختيار المنتج',
            'items.*.product_id.exists' => 'المنتج غير موجود',
            'items.*.quantity.required' => 'يجب تحديد الكمية',
            'items.*.quantity.numeric' => 'الكمية يجب أن تكون رقم',
            'items.*.quantity.min' => 'الكمية يجب أن تكون 1 على الأقل',
            'items.*.unit_price.required' => 'يجب تحديد السعر',
            'items.*.unit_price.numeric' => 'السعر يجب أن يكون رقم',
            'items.*.unit_price.min' => 'السعر يجب أن يكون 0 على الأقل',
            'items.*.discount.numeric' => 'الخصم يجب أن يكون رقم',
            'items.*.discount.min' => 'الخصم يجب أن يكون 0 على الأقل',
            'items.*.tax_1.numeric' => 'الضريبة 1 يجب أن تكون رقم',
            'items.*.tax_1.min' => 'الضريبة 1 يجب أن تكون 0 على الأقل',
            'items.*.tax_2.numeric' => 'الضريبة 2 يجب أن تكون رقم',
            'items.*.tax_2.min' => 'الضريبة 2 يجب أن تكون 0 على الأقل',
        ];
    }
}
