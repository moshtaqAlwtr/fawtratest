<?php

namespace Modules\Client\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequestApi extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // نولّد الكود تلقائيًا قبل الفاليديشن في إنشاء العميل
    protected function prepareForValidation(): void
    {
        if ($this->isMethod('post') && !$this->filled('code')) {
            // عدّل المسار حسب موديلك لو مختلف
            $serial = \App\Models\SerialSetting::where('section', 'customer')->first();
            $next   = $serial?->current_number ?? 1;

            // خليه نص ممهّد لضمان ترتيب صحيح وتفادي overflow لو العمود string
            $this->merge(['code' => str_pad((string)$next, 6, '0', STR_PAD_LEFT)]);
        }
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('put') || $this->isMethod('patch');
        // يدعم route model binding باسم client أو id
        $clientId = optional($this->route('client'))->id ?? $this->route('id');

        return [
            'trade_name' => ['required','string','max:255'],

            'code' => $isUpdate
                ? ['required','string','max:50', Rule::unique('clients','code')->ignore($clientId)]
                : ['nullable','string','max:50','unique:clients,code'],

            // الأفضل تنقل هذي من الكنترولر هنا
            'code' => 'nullable',
            'currency' => 'nullable|string|max:50',
            
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:20',
            'mobile' => 'nullable|string|max:20',
            'street1' => 'nullable|string|max:255',
            'street2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'region' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'tax_number' => 'nullable|string|max:50',
            'commercial_registration' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric',
            'credit_period' => 'nullable|integer',
            'printing_method' => 'nullable|integer|in:1,2',
            'opening_balance' => 'nullable|numeric',
            'opening_balance_date' => 'nullable|date',
            
            'email' => 'required|email|max:255|unique:clients,email,' . $this->id,

            'category_id' => 'nullable|exists:categories_clients,id',
            'client_type' => 'nullable|integer|in:1,2',
            'notes' => 'nullable|string',
            'attachments' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            // 'employee_id' => 'nullable',
            'contacts' => 'nullable|array',
            'contacts.*.first_name' => 'nullable|string|max:100',
            'contacts.*.last_name' => 'nullable|string|max:100',
            'contacts.*.email' => 'nullable|email|max:255',
            'contacts.*.phone' => 'nullable|string|max:20',
            'contacts.*.mobile' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'required'          => 'هذا الحقل مطلوب',
            'email.unique'      => 'عفوا الايميل موجود مسبقا',
            'code.unique'       => 'هذا الكود مستخدم من قبل',
            'region_id.required'=> 'حقل المجموعة مطلوب.',
            'region_id.exists'  => 'المجموعة غير صحيحة.',
        ];
    }
}
