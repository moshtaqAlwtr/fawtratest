<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayableChequeRequest extends FormRequest
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
            'amount' => 'required|numeric|min:0.01',
            'cheque_number' => 'required|numeric|unique:payable_cheques,cheque_number,'.$this->route('id'),
            'bank_id' => 'required|exists:treasuries,id',
            'cheque_book_id' => 'required|exists:cheque_books,id',
            'issue_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:issue_date',
            'recipient_account_id' => 'required|numeric',
            'attachment' => 'nullable|file|max:5120|mimes:png,jpg,gif,jpeg,bmp,rar,zip,doc,docx,xls,xlsx,ppt,pptx,pdf',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'amount.required' => 'يرجى إدخال المبلغ.',
            'cheque_number.required' => 'يرجى إدخال رقم الشيك.',
            'cheque_number.unique' => 'رقم الشيك هذا موجود مسبقًا.',
            'bank_id.required' => 'يرجى اختيار البنك.',
            'cheque_book_id.required' => 'يرجى اختيار رقم دفتر الشيكات.',
            'issue_date.required' => 'يرجى تحديد تاريخ الإصدار.',
            'due_date.after_or_equal' => 'تاريخ الاستحقاق يجب أن يكون بعد أو مساوي لتاريخ الإصدار.',
            'recipient_account_id.required' => 'يرجى اختيار حساب المستلم.',
            'attachment.mimes' => 'المرفق يجب ان يكون من نوع: png, jpg, gif, bmp, rar, zip, doc, docx, xls, xlsx, ppt, pptx, pdf',
            'attachment.max' => 'حجم المرفق يجب ان لا يتجاوز 5 ميجابايت',
            'description.max' => 'الوصف يجب ان لا يتجاوز 500 حرف',
        ];
    }
}
