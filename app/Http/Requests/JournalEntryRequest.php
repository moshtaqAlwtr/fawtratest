<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalEntryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'reference_number' => 'nullable|string|max:50',
            'date' => 'required|date',
            'description' => 'required|string|max:500',
            'currency' => 'nullable|string|max:10',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'client_id' => 'nullable|exists:clients,id',
            'employee_id' => 'nullable|exists:employees,id',
            'invoice_id' => 'nullable|exists:invoices,id',
            'cost_center_id' => 'nullable|exists:cost_centers,id',
            
            // تفاصيل القيد
            'details' => 'required|array|min:2', // يجب أن يكون هناك على الأقل طرفين للقيد
            'details.*.account_id' => 'required|exists:chart_of_accounts,id',
            'details.*.description' => 'nullable|string|max:255',
            'details.*.debit' => 'required_without:details.*.credit|numeric|min:0',
            'details.*.credit' => 'required_without:details.*.debit|numeric|min:0',
            'details.*.cost_center_id' => 'nullable|exists:cost_centers,id',
            'details.*.reference' => 'nullable|string|max:100',
        ];
    }

    public function messages()
    {
        return [
            'reference_number.max' => 'الرقم المرجعي يجب أن لا يتجاوز 50 حرفاً',
            'date.required' => 'تاريخ القيد مطلوب',
            'date.date' => 'تاريخ القيد يجب أن يكون تاريخاً صحيحاً',
            'description.required' => 'وصف القيد مطلوب',
            'description.max' => 'وصف القيد يجب أن لا يتجاوز 500 حرف',
            'attachment.mimes' => 'المرفق يجب أن يكون من نوع: pdf, jpg, jpeg, png',
            'attachment.max' => 'حجم المرفق يجب أن لا يتجاوز 2 ميجابايت',
            
            'details.required' => 'تفاصيل القيد مطلوبة',
            'details.min' => 'يجب إضافة طرفين على الأقل للقيد',
            'details.*.account_id.required' => 'الحساب مطلوب لكل سطر',
            'details.*.account_id.exists' => 'الحساب المحدد غير موجود',
            'details.*.debit.numeric' => 'المبلغ المدين يجب أن يكون رقماً',
            'details.*.credit.numeric' => 'المبلغ الدائن يجب أن يكون رقماً',
            'details.*.debit.min' => 'المبلغ المدين يجب أن يكون 0 أو أكثر',
            'details.*.credit.min' => 'المبلغ الدائن يجب أن يكون 0 أو أكثر',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // التحقق من توازن القيد
            $totalDebit = collect($this->details)->sum('debit');
            $totalCredit = collect($this->details)->sum('credit');
            
            if ($totalDebit != $totalCredit) {
                $validator->errors()->add('details', 'مجموع المدين يجب أن يساوي مجموع الدائن');
            }

            // التحقق من أن كل سطر يحتوي على مدين أو دائن وليس كلاهما
            foreach ($this->details as $index => $detail) {
                if ((!empty($detail['debit']) && !empty($detail['credit'])) || 
                    (empty($detail['debit']) && empty($detail['credit']))) {
                    $validator->errors()->add(
                        "details.{$index}",
                        'يجب إدخال إما المدين أو الدائن وليس كلاهما أو لا شيء'
                    );
                }
            }
        });
    }
}
