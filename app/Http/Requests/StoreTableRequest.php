<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'     => 'required|string|max:50|unique:tables,code',
            'capacity' => 'required|integer|min:1',
            'status'   => 'required|in:available,occupied,reserved,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'     => 'Table code is required.',
            'code.unique'       => 'Table code already exists.',
            'code.max'          => 'Table code must not exceed 50 characters.',
            'capacity.required' => 'Capacity is required.',
            'capacity.integer'  => 'Capacity must be an integer.',
            'capacity.min'      => 'Capacity must be at least 1.',
            'status.required'   => 'Status is required.',
            'status.in'         => 'Status must be one of: available, occupied, reserved, inactive.',
        ];
    }
}
