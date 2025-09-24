<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiNoteRequest extends FormRequest
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
        'client_id' => 'required|exists:clients,id',
        'process' => 'required|string|max:255',
        'description' => 'required|string',
        'site_type' => 'required|in:independent_booth,grocery,supplies,markets,station',
        'deposit_count' => 'nullable|integer|min:0',
        'competitor_documents' => 'nullable|integer|min:0',
        'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,txt,mp4,webm,ogg|max:102400',
        'current_latitude' => 'nullable|numeric',
        'current_longitude' => 'nullable|numeric',
    ];
}

}
