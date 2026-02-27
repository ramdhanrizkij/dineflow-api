<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuAddons extends Model
{
    protected $fillable = [
        'menu_id',
        'name',
        'price',
        'is_required',
    ];

    protected $casts = [
        'price'       => 'decimal:2',
        'is_required' => 'boolean',
    ];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function orderItemAddons(): HasMany
    {
        return $this->hasMany(OrderItemAddon::class, 'addon_id');
    }
}
