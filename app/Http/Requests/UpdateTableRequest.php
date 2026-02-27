<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tableId = $this->route('table')?->id;

        return [
            'code'     => "required|string|max:50|unique:tables,code,{$tableId}",
            'capacity' => 'required|integer|min:1',
            'status'   => 'required|in:available,occupied,reserved,inactive',
            'table_category_id' => 'required|exists:table_categories,id',
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
            'table_category_id.required' => 'Table category is required.',
            'table_category_id.exists'   => 'Table category must exist.',
        ];
    }
}
