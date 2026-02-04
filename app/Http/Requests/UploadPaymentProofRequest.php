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
            'paymentProof' => [
                'required',
                'file',
                'max:5120', // 5MB in KB
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    $allowed = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];
                    if (!in_array($ext, $allowed)) {
                        $fail('Payment proof must be PDF, JPG, JPEG, PNG, or WEBP.');
                    }
                },
            ],
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
            'paymentProof.mimes' => 'Payment proof must be a PDF, JPG, JPEG, PNG, or WEBP file.',
            'paymentProof.max' => 'Payment proof file size cannot exceed 5MB.',
        ];
    }
}

