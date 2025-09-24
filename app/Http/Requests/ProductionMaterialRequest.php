<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductionMaterialRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            // بيانات أساسية
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:production_materials,code,' . $this->id,
            'product_id' => 'required|exists:products,id',
            'account_id' => 'required|exists:accounts,id',
            'production_path_id' => 'nullable|exists:accounts,id',
            'quantity' => 'required|numeric|min:0',
            'status' => 'nullable|integer|in:0,1',
            'default' => 'nullable|integer|in:0,1',

            // // بيانات المواد الخام
            // 'raw_product_id' => 'nullable|array',
            // 'raw_product_id.*' => 'exists:products,id',
            // 'raw_unit_price' => 'nullable|array',
            // 'raw_unit_price.*' => 'numeric|min:0',
            // 'raw_quantity' => 'nullable|array',
            // 'raw_quantity.*' => 'integer|min:0',
            // 'raw_total' => 'nullable|array',
            // 'raw_total.*' => 'numeric|min:0',

            // // بيانات المصروفات
            // 'expenses_account_id' => 'nullable|array',
            // 'expenses_account_id.*' => 'exists:accounts,id',
            // 'expenses_cost_type' => 'nullable|array',
            // 'expenses_cost_type.*' => 'integer|in:0,1',
            // 'expenses_price' => 'nullable|array',
            // 'expenses_price.*' => 'numeric|min:0',
            // 'expenses_description' => 'nullable|array',
            // 'expenses_total' => 'nullable|array',
            // 'expenses_total.*' => 'numeric|min:0',

            // // بيانات التصنيع
            // 'workstation_id' => 'nullable|array',
            // 'workstation_id.*' => 'exists:work_stations,id',
            // 'operating_time' => 'nullable|array',
            // 'operating_time.*' => 'integer|min:0',
            // 'manu_total_cost' => 'nullable|array',
            // 'manu_total_cost.*' => 'numeric|min:0',
            // 'manu_description' => 'nullable|array',
            // 'manu_total' => 'nullable|array',
            // 'manu_total.*' => 'numeric|min:0',

            // // بيانات المنتجات النهائية
            // 'end_life_product_id' => 'nullable|array',
            // 'end_life_product_id.*' => 'exists:products,id',
            // 'end_life_unit_price' => 'nullable|array',
            // 'end_life_unit_price.*' => 'numeric|min:0',
            // 'end_life_quantity' => 'nullable|array',
            // 'end_life_quantity.*' => 'integer|min:0',
            // 'end_life_total' => 'nullable|array',
            // 'end_life_total.*' => 'numeric|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages()
    {
        return [
            'name.required' => 'حقل الاسم مطلوب.',
            'code.required' => 'حقل الكود مطلوب.',
            'code.unique' => 'الكود مستخدم بالفعل.',
            'product_id.required' => 'حقل المنتج مطلوب.',
            'product_id.exists' => 'المنتج المحدد غير موجود.',
            'account_id.required' => 'حقل الحساب مطلوب.',
            'account_id.exists' => 'الحساب المحدد غير موجود.',
            'raw_product_id.*.exists' => 'المنتج الخام المحدد غير موجود.',
            'expenses_account_id.*.exists' => 'الحساب المحدد غير موجود.',
            'workstation_id.*.exists' => 'محطة العمل المحددة غير موجودة.',
            'end_life_product_id.*.exists' => 'المنتج النهائي المحدد غير موجود.',
        ];
    }
}
