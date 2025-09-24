<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarehousePermitsRequest extends FormRequest
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
               'permission_type' => 'required|exists:permission_sources,id',
            'permission_date'   => 'required|date',
            'sub_account'       => 'required|string',
            'number'            => 'required|string|unique:warehouse_permits,number,' . $this->id,
            'store_houses_id'   => 'nullable|exists:store_houses,id',
            'details'           => 'nullable|string',
            'grand_total'       => 'required|numeric|min:0',
            'attachments'       => 'nullable|file|mimes:jpeg,png,pdf|max:2048',
            'quantity.*'        => 'required|integer|min:1',
            'unit_price.*'      => 'required|numeric|min:0',
            'total.*'           => 'required|numeric|min:0',
            'product_id.*'      => 'required|exists:products,id',
        ];
    }

    /**
     * تخصيص رسائل الخطأ.
     */
    public function messages()
    {
        return [
            'permission_type.required'   => 'نوع الإذن مطلوب.',
            'permission_date.required'   => 'التاريخ مطلوب.',
            'sub_account.required'       => 'يجب تحديد الحساب الفرعي.',
            'number.required'            => 'رقم الإذن مطلوب.',
            'number.unique'              => 'رقم الإذن مستخدم مسبقًا.',
            'store_houses_id.required'   => 'يجب اختيار المستودع.',
            'store_houses_id.exists'     => 'المستودع المحدد غير صالح.',
            'grand_total.required'       => 'يجب إدخال المجموع الإجمالي.',
            'quantity.*.required'        => 'الكمية مطلوبة لكل منتج.',
            'unit_price.*.required'      => 'سعر الوحدة مطلوب لكل منتج.',
            'total.*.required'           => 'الإجمالي مطلوب لكل منتج.',
            'product_id.*.exists'        => 'المنتج المحدد غير صالح.',
        ];
    }
}
