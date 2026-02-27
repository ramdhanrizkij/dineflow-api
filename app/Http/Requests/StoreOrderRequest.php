<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'table_id'               => 'required|integer|exists:tables,id',
            'customer_name'          => 'nullable|string|max:255',
            'items'                  => 'required|array|min:1',
            'items.*.menu_id'        => 'required|integer|exists:menus,id',
            'items.*.variant_id'     => 'nullable|integer|exists:menu_variants,id',
            'items.*.qty'            => 'required|integer|min:1',
            'items.*.notes'          => 'nullable|string',
            'items.*.addon_ids'      => 'nullable|array',
            'items.*.addon_ids.*'    => 'integer|exists:menu_addons,id',
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required'            => 'Table is required.',
            'table_id.exists'              => 'Selected table does not exist.',
            'items.required'               => 'Order must have at least one item.',
            'items.min'                    => 'Order must have at least one item.',
            'items.*.menu_id.required'     => 'Each item must specify a menu.',
            'items.*.menu_id.exists'       => 'One or more selected menus do not exist.',
            'items.*.variant_id.exists'    => 'One or more selected variants do not exist.',
            'items.*.qty.required'         => 'Each item must have a quantity.',
            'items.*.qty.min'              => 'Item quantity must be at least 1.',
            'items.*.addon_ids.*.exists'   => 'One or more selected addons do not exist.',
        ];
    }
}
