<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'base_price'  => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active'   => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Menu name is required.',
            'name.max'             => 'Menu name must not exceed 255 characters.',
            'base_price.required'  => 'Base price is required.',
            'base_price.numeric'   => 'Base price must be a number.',
            'base_price.min'       => 'Base price must be at least 0.',
            'category_id.required' => 'Category is required.',
            'category_id.exists'   => 'Selected category does not exist.',
            'is_active.boolean'    => 'is_active must be true or false.',
        ];
    }
}
