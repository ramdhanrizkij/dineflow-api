<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuAddonsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'price'       => 'required|numeric|min:0',
            'is_required' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => 'Addon name is required.',
            'name.max'         => 'Addon name must not exceed 255 characters.',
            'price.required'   => 'Price is required.',
            'price.numeric'    => 'Price must be a number.',
            'price.min'        => 'Price must be at least 0.',
            'is_required.boolean' => 'is_required must be true or false.',
        ];
    }
}
