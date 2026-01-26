<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TerminateContractRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only Lease Managers can terminate contracts
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
            'reason' => 'required|string|max:500',
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
            'reason.required' => 'Termination reason is required.',
            'reason.string' => 'Termination reason must be text.',
            'reason.max' => 'Termination reason cannot exceed 500 characters.',
        ];
    }
}
