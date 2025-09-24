<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ManufacturOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'account_id' => 'required|exists:accounts,id',
            'employee_id' => 'nullable|exists:employees,id',
            'client_id' => 'nullable|exists:clients,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
            'production_material_id' => 'nullable|exists:production_materials,id',
            'production_path_id' => 'nullable|exists:production_paths,id',
            'last_total_cost' => 'nullable|numeric|min:0',
            'created_by' => 'nullable|exists:users,id',
            'updated_by' => 'nullable|exists:users,id',

            // قواعد للحقول الديناميكية
            'raw_product_id' => 'required|array|min:1',
            'raw_product_id.*' => 'required|exists:products,id',
            'raw_production_stage_id' => 'required|array|min:1',
            'raw_production_stage_id.*' => 'nullable|exists:production_stages,id',
            'raw_quantity' => 'required|array|min:1',
            'raw_quantity.*' => 'required|numeric|min:1',
            'raw_unit_price' => 'required|array|min:1',
            'raw_unit_price.*' => 'required|numeric|min:0',
            'raw_total' => 'required|array|min:1',
            'raw_total.*' => 'required|numeric|min:0',

            // قواعد للمصروفات (اختيارية)
            'expenses_account_id' => 'nullable|array',
            'expenses_account_id.*' => 'nullable|exists:accounts,id',
            'expenses_cost_type' => 'nullable|array',
            'expenses_production_stage_id' => 'nullable|array',
            'expenses_production_stage_id.*' => 'nullable|exists:production_stages,id',
            'expenses_price' => 'nullable|array',
            'expenses_price.*' => 'nullable|numeric|min:0',
            'expenses_description' => 'nullable|array',
            'expenses_total' => 'nullable|array',
            'expenses_total.*' => 'nullable|numeric|min:0',

            // قواعد للتصنيع (اختيارية)
            'workstation_id' => 'nullable|array',
            'workstation_id.*' => 'nullable|exists:work_stations,id',
            'operating_time' => 'nullable|array',
            'operating_time.*' => 'nullable|numeric|min:0',
            'manu_production_stage_id' => 'nullable|array',
            'manu_production_stage_id.*' => 'nullable|exists:production_stages,id',
            'manu_cost_type' => 'nullable|array',
            'manu_total_cost' => 'nullable|array',
            'manu_total_cost.*' => 'nullable|numeric|min:0',
            'manu_description' => 'nullable|array',
            'manu_total' => 'nullable|array',
            'manu_total.*' => 'nullable|numeric|min:0',

            // قواعد للمواد الهالكة (اختيارية)
            'end_life_product_id' => 'nullable|array',
            'end_life_product_id.*' => 'nullable|exists:products,id',
            'end_life_unit_price' => 'nullable|array',
            'end_life_unit_price.*' => 'nullable|numeric|min:0',
            'end_life_production_stage_id' => 'nullable|array',
            'end_life_production_stage_id.*' => 'nullable|exists:production_stages,id',
            'end_life_quantity' => 'nullable|array',
            'end_life_quantity.*' => 'nullable|numeric|min:0',
            'end_life_total' => 'nullable|array',
            'end_life_total.*' => 'nullable|numeric|min:0',
        ];

        // إضافة قاعدة الكود الفريد بشكل صحيح
        if ($this->isMethod('post')) {
            // للإنشاء الجديد
            $rules['code'] = 'required|string|max:50|unique:manufactur_orders,code';
        } else {
            // للتحديث
            $rules['code'] = 'required|string|max:50|unique:manufactur_orders,code,' . $this->route('id');
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'الاسم مطلوب.',
            'code.required' => 'الكود مطلوب.',
            'code.unique' => 'هذا الكود مستخدم بالفعل.',
            'from_date.required' => 'تاريخ البداية مطلوب.',
            'to_date.required' => 'تاريخ النهاية مطلوب.',
            'to_date.after_or_equal' => 'يجب أن يكون تاريخ النهاية بعد أو يساوي تاريخ البداية.',
            'account_id.required' => 'الحساب مطلوب.',
            'account_id.exists' => 'الحساب المحدد غير موجود.',
            'product_id.required' => 'المنتج مطلوب.',
            'product_id.exists' => 'المنتج المحدد غير موجود.',
            'quantity.required' => 'الكمية مطلوبة.',
            'quantity.numeric' => 'الكمية يجب أن تكون رقمية.',
            'quantity.min' => 'الكمية يجب أن تكون على الأقل 1.',

            // رسائل للحقول الديناميكية
            'raw_product_id.required' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_product_id.min' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_product_id.*.required' => 'يجب تحديد المادة الخام.',
            'raw_product_id.*.exists' => 'المادة الخام المحددة غير موجودة.',
            'raw_quantity.required' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_quantity.min' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_quantity.*.required' => 'كمية المادة الخام مطلوبة.',
            'raw_quantity.*.numeric' => 'كمية المادة الخام يجب أن تكون رقمية.',
            'raw_quantity.*.min' => 'كمية المادة الخام يجب أن تكون على الأقل 1.',
            'raw_unit_price.required' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_unit_price.min' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_unit_price.*.required' => 'سعر الوحدة للمادة الخام مطلوب.',
            'raw_unit_price.*.numeric' => 'سعر الوحدة للمادة الخام يجب أن يكون رقمية.',
            'raw_unit_price.*.min' => 'سعر الوحدة للمادة الخام يجب أن يكون على الأقل 0.',
            'raw_total.required' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_total.min' => 'يجب إضافة صف واحد على الأقل في المواد الخام.',
            'raw_total.*.required' => 'الإجمالي للمادة الخام مطلوب.',
            'raw_total.*.numeric' => 'الإجمالي للمادة الخام يجب أن يكون رقمية.',
            'raw_total.*.min' => 'الإجمالي للمادة الخام يجب أن يكون على الأقل 0.',
        ];
    }
}
