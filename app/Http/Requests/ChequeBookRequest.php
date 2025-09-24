<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChequeBookRequest extends FormRequest
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
            'bank_id' => 'required|exists:treasuries,id', // تحقق أن البنك موجود
            'cheque_book_number' => 'required|integer|min:1|unique:cheque_books,cheque_book_number,' . $this->route('id'),
            'start_serial_number' => 'required|integer|min:1',
            'end_serial_number' => 'required|integer|gt:start_serial_number',
            'currency' => 'required|string|max:3', // تحقق من رمز العملة (مثال: SAR) // يجب أن يكون أكبر من الرقم التسلسلي الأول وألا يتجاوز 150
            'status' => 'required|in:0,1',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'cheque_book_number.required' => 'حقل رقم الشيكات مطلوب.',
            'cheque_book_number.unique' => 'الرقم التسلسلي للشيكات مستخدم بالفعل.',
            'bank_id.required' => 'حقل البنك مطلوب.',
            'bank_id.exists' => 'البنك المحدد غير موجود.',
            'start_serial_number.required' => 'الرقم التسلسلي الأول مطلوب.',
            'start_serial_number.min' => 'الرقم التسلسلي الأول يجب أن يكون أكبر من صفر.',
            'end_serial_number.required' => 'الرقم التسلسلي الأخير مطلوب.',
            'end_serial_number.gt' => 'الرقم التسلسلي الأخير يجب أن يكون أكبر من الرقم التسلسلي الأول.',
            'currency.required' => 'حقل العملة مطلوب.',
            'currency.max' => 'رمز العملة يجب أن يكون مكونًا من 3 أحرف فقط.',
            'status.required' => 'حقل الحالة مطلوب.',
            'status.in' => 'الحالة يجب أن تكون إما نشط أو غير نشط.',
            'notes.max' => 'الملاحظات لا يجب أن تتجاوز 1000 حرف.',
        ];
    }

}
