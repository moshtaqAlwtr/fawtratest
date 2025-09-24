<?php

namespace Modules\Client\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;


class ClientRequest extends FormRequest
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
            'trade_name' => 'required|string|max:255',
            // 'code' => 'required|string|unique_without:id:clients,code,' . $id, // تعديل القاعدة هنا
      'code' => ['required', Rule::unique('clients','code')->ignore($this->id)],
 // تعديل القاعدة هنا
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
            'email' => 'nullable|email|max:255',
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


    public function messages()
    {
        return [
            'required'=>'هذا الحقل مطلوب'
        ];
    }

}
