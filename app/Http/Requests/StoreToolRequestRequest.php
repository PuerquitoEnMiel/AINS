<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreToolRequestRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'tool_name' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url',
            'category' => 'required|string|exists:categories,name',
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'required|email',
        ];
    }
}
