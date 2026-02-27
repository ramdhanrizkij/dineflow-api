<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'additional_price' => 'required|numeric|min:0',
            'is_default'       => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'             => 'Variant name is required.',
            'name.max'                  => 'Variant name must not exceed 255 characters.',
            'additional_price.required' => 'Additional price is required.',
            'additional_price.numeric'  => 'Additional price must be a number.',
            'additional_price.min'      => 'Additional price must be at least 0.',
            'is_default.boolean'        => 'is_default must be true or false.',
        ];
    }
}
