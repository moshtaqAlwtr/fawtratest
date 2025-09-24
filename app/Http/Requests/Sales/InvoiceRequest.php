<?php

namespace App\Http\Requests\Sales;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class InvoiceRequest extends FormRequest
{
    /**
     * تحديد إذا كان المستخدم لديه الصلاحية لاستخدام هذا الطلب.
     */
    public function authorize()
    {
        return true; // يمكنك تخصيص الصلاحيات حسب نظامك
    }

    /**
     * القواعد للتحقق من صحة الطلب.
     */
    public function rules()
    {
        return [
            // التحقق من صحة العميل
            'client_id' => 'required|exists:clients,id',
            // التحقق من صحة النوع (عادي أو مرتجع)

            // تاريخ الفاتورة اختياري
            'invoice_date' => 'nullable|date',
            // الحقول الخاصة بعناصر الفاتورة
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.tax_1' => 'nullable|numeric|min:0',
            'items.*.tax_2' => 'nullable|numeric|min:0',
            'items.*.description' => 'nullable|string|max:255',
            // شروط الدفع
            'payment_terms' => 'nullable|string|max:255',
            // العملة
            'currency' => 'nullable|string|size:3', // مثل "SAR" أو "USD"
        ];
    }

    /**
     * رسائل الخطأ المخصصة.
     */
    public function messages()
    {
        return [
            'client_id.required' => 'الرجاء اختيار العميل.',
            'client_id.exists' => 'العميل المحدد غير موجود.',

            'type.in' => 'نوع الفاتورة غير صالح.',
            'items.required' => 'يجب إضافة عناصر الفاتورة.',
            'items.*.product_id.required' => 'الرجاء اختيار المنتج.',
            'items.*.product_id.exists' => 'المنتج المحدد غير موجود.',
            'items.*.quantity.required' => 'الرجاء تحديد الكمية.',
            'items.*.quantity.min' => 'يجب أن تكون الكمية أكبر من 0.',
            'items.*.unit_price.required' => 'الرجاء تحديد السعر.',
            'currency.required' => 'الرجاء تحديد العملة.',
        ];
    }
}
