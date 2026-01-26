<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentProofRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only tenants can upload payment proof
        return auth()->check() && auth()->user()->role === 'Tenant';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'paymentProof' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB max
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
            'paymentProof.required' => 'Payment proof file is required.',
            'paymentProof.file' => 'Payment proof must be a file.',
            'paymentProof.mimes' => 'Payment proof must be a PDF, JPG, JPEG, or PNG file.',
            'paymentProof.max' => 'Payment proof file size cannot exceed 5MB.',
        ];
    }
}

