<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndirectCostsRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'account_id' => 'required|exists:accounts,id',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'based_on' => 'required|in:1,2',
            'total' => 'required|numeric|min:0',

            // التحقق من القيود
            'restriction_id' => 'nullable|array',
            'restriction_id.*' => 'nullable|exists:journal_entries,id',
            'restriction_total' => 'nullable|array',
            'restriction_total.*' => 'nullable|numeric|min:0',

            // التحقق من أوامر التصنيع
            'manufacturing_order_id' => 'nullable|array',
            'manufacturing_order_id.*' => 'nullable|exists:manufactur_orders,id',
            'manufacturing_price' => 'nullable|array',
            'manufacturing_price.*' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'account_id' => 'الحساب',
            'from_date' => 'التاريخ من',
            'to_date' => 'التاريخ إلى',
            'based_on' => 'نوع التوزيع',
            'total' => 'المجموع الكلي',
            'restriction_id.*' => 'القيد المحاسبي',
            'restriction_total.*' => 'مجموع القيد',
            'manufacturing_order_id.*' => 'أمر التصنيع',
            'manufacturing_price.*' => 'سعر التصنيع',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'account_id.required' => 'حقل الحساب مطلوب.',
            'account_id.exists' => 'الحساب المحدد غير موجود.',
            'from_date.required' => 'حقل التاريخ من مطلوب.',
            'from_date.date' => 'حقل التاريخ من يجب أن يكون تاريخاً صحيحاً.',
            'to_date.required' => 'حقل التاريخ إلى مطلوب.',
            'to_date.date' => 'حقل التاريخ إلى يجب أن يكون تاريخاً صحيحاً.',
            'to_date.after_or_equal' => 'التاريخ إلى يجب أن يكون بعد أو يساوي التاريخ من.',
            'based_on.required' => 'حقل نوع التوزيع مطلوب.',
            'based_on.in' => 'قيمة نوع التوزيع غير صحيحة.',
            'total.required' => 'حقل المجموع الكلي مطلوب.',
            'total.numeric' => 'حقل المجموع الكلي يجب أن يكون رقماً.',
            'total.min' => 'حقل المجموع الكلي يجب أن يكون أكبر من أو يساوي صفر.',

            'restriction_id.*.exists' => 'القيد المحاسبي المحدد غير موجود.',
            'restriction_total.*.numeric' => 'مجموع القيد يجب أن يكون رقماً.',
            'restriction_total.*.min' => 'مجموع القيد يجب أن يكون أكبر من أو يساوي صفر.',

            'manufacturing_order_id.*.exists' => 'أمر التصنيع المحدد غير موجود.',
            'manufacturing_price.*.numeric' => 'سعر التصنيع يجب أن يكون رقماً.',
            'manufacturing_price.*.min' => 'سعر التصنيع يجب أن يكون أكبر من أو يساوي صفر.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // التحقق من وجود بيانات في أحد الجدولين على الأقل
            $hasRestrictions = !empty(array_filter($this->restriction_id ?? []));
            $hasManufacturing = !empty(array_filter($this->manufacturing_order_id ?? []));

            if (!$hasRestrictions && !$hasManufacturing) {
                $validator->errors()->add('general', 'يجب إدخال قيد محاسبي واحد على الأقل أو أمر تصنيع واحد على الأقل.');
            }

            // التحقق من تطابق المجموع المدخل مع المحسوب
            $calculatedTotal = 0;

            if ($this->restriction_total) {
                $calculatedTotal += array_sum(array_filter($this->restriction_total, 'is_numeric'));
            }

            if ($this->manufacturing_price) {
                $calculatedTotal += array_sum(array_filter($this->manufacturing_price, 'is_numeric'));
            }

            $enteredTotal = floatval($this->total);

            if (abs($calculatedTotal - $enteredTotal) > 0.01) { // السماح بفرق بسيط في العشرية
                $validator->errors()->add('total', 'المجموع الكلي لا يطابق مجموع القيود وأوامر التصنيع.');
            }
        });
    }
}