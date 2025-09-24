<?php

namespace App\Http\Requests\Sales;

use Illuminate\Foundation\Http\FormRequest;

class ClientPaymentRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'invoice_id' => 'nullable|exists:invoices,id',
            'employee_id' => 'nullable|exists:users,id',
            'installments_id' => 'nullable|exists:installment,id',
            'treasury_id' => 'nullable|exists:treasuries,id',

            'payment_date' => 'nullable|date',
            'type' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|in:1,2,3,4,5',
            'status_payment' => 'nullable|in:1,2,3,4,5',
            'payment_data' => 'nullable|string',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ];

        // إذا كانت العملية تحديث، نجعل القواعد أكثر مرونة
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = array_map(function ($rule) {
                return 'sometimes|' . $rule;
            }, $rules);
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'invoice_id.required' => 'رقم الفاتورة مطلوب',
            'invoice_id.exists' => 'رقم الفاتورة غير موجود',
            'payment_date.required' => 'تاريخ الدفع مطلوب',
            'payment_date.date' => 'تاريخ الدفع يجب أن يكون تاريخاً صحيحاً',
            'amount.required' => 'المبلغ مطلوب',

            'amount.numeric' => 'المبلغ يجب أن يكون رقماً',
            'amount.min' => 'المبلغ يجب أن يكون أكبر من صفر',
            'payment_type.required' => 'نوع الدفع مطلوب',
            'payment_type.in' => 'نوع الدفع غير صحيح',
            'status_payment.required' => 'حالة الدفع مطلوبة',
            'status_payment.in' => 'حالة الدفع غير صحيحة',
            'attachments.file' => 'المرفق يجب أن يكون ملفاً',
            'attachments.mimes' => 'نوع الملف غير مسموح به. الأنواع المسموحة: PDF, JPG, JPEG, PNG',
            'attachments.max' => 'حجم الملف لا يجب أن يتجاوز 2 ميجابايت',
        ];
    }
}
