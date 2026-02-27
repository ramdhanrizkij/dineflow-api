<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status'                 => 'sometimes|in:draft,submitted,in_kitchen,ready,served,closed,cancelled',
            'customer_name'          => 'sometimes|nullable|string|max:255',
            'items'                  => 'sometimes|array|min:1',
            'items.*.menu_id'        => 'required_with:items|integer|exists:menus,id',
            'items.*.variant_id'     => 'nullable|integer|exists:menu_variants,id',
            'items.*.qty'            => 'required_with:items|integer|min:1',
            'items.*.notes'          => 'nullable|string',
            'items.*.addon_ids'      => 'nullable|array',
            'items.*.addon_ids.*'    => 'integer|exists:menu_addons,id',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in'                    => 'Invalid status. Must be one of: draft, submitted, in_kitchen, ready, served, closed, cancelled.',
            'items.min'                    => 'Order must have at least one item.',
            'items.*.menu_id.required_with'=> 'Each item must specify a menu.',
            'items.*.menu_id.exists'       => 'One or more selected menus do not exist.',
            'items.*.variant_id.exists'    => 'One or more selected variants do not exist.',
            'items.*.qty.required_with'    => 'Each item must have a quantity.',
            'items.*.qty.min'              => 'Item quantity must be at least 1.',
            'items.*.addon_ids.*.exists'   => 'One or more selected addons do not exist.',
        ];
    }
}
