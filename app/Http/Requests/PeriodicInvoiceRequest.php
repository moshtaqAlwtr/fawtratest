<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PeriodicInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'client_id' => 'required|exists:clients,id',
            'first_invoice_date' => 'required|date',
            'repeat_type' => 'required|integer|in:1,2,3,4,5',
            'repeat_interval' => 'nullable|integer|min:1',
            'repeat_count' => 'required|integer|min:1',
            'details_subscription' => 'nullable|string',
            'before_days' => 'nullable|integer|min:0',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_1' => 'nullable|numeric|min:0|max:100',
            'items.*.tax_2' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'send_copy' => 'boolean',
            'show_dates' => 'boolean',
            'disable_partial' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'client_id.required' => 'يجب اختيار العميل',
            'client_id.exists' => 'العميل غير موجود',
            'first_invoice_date.required' => 'يجب تحديد تاريخ أول فاتورة',
            'first_invoice_date.date' => 'تاريخ أول فاتورة غير صحيح',
            'repeat_type.required' => 'يجب تحديد نوع التكرار',
            'repeat_type.in' => 'نوع التكرار غير صحيح',
            'repeat_interval.required' => 'يجب تحديد فترة التكرار',
            'repeat_interval.min' => 'فترة التكرار يجب أن تكون أكبر من صفر',
            'repeat_count.required' => 'يجب تحديد عدد مرات التكرار',
            'repeat_count.min' => 'عدد مرات التكرار يجب أن يكون أكبر من صفر',
            'items.required' => 'يجب إضافة منتج واحد على الأقل',
            'items.*.product_id.required' => 'يجب اختيار المنتج',
            'items.*.product_id.exists' => 'المنتج غير موجود',
            'items.*.quantity.required' => 'يجب تحديد الكمية',
            'items.*.quantity.min' => 'الكمية يجب أن تكون أكبر من صفر',
            'items.*.unit_price.required' => 'يجب تحديد السعر',
            'items.*.unit_price.min' => 'السعر يجب أن يكون أكبر من صفر',
        ];
    }
}
