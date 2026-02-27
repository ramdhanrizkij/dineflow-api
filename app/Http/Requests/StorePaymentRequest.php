<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => 'required|in:cash,transfer,qris,other',
            'amount_paid'    => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in'       => 'Payment method must be one of: cash, transfer, qris, other.',
            'amount_paid.required'    => 'Amount paid is required.',
            'amount_paid.numeric'     => 'Amount paid must be a number.',
            'amount_paid.min'         => 'Amount paid must be at least 0.',
        ];
    }
}
