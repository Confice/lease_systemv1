<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RenewContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Lease Managers can renew contracts
        return auth()->check() && auth()->user()->role === 'Lease Manager';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'months' => 'required|integer|min:1|max:12',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'months.required' => 'Number of months is required.',
            'months.integer' => 'Number of months must be a whole number.',
            'months.min' => 'Contract must be renewed for at least 1 month.',
            'months.max' => 'Contract cannot be renewed for more than 12 months at once.',
        ];
    }
}
