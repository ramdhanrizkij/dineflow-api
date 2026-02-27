<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'menu_id',
        'variant_id',
        'qty',
        'price_snapshot',
        'status',
        'notes',
    ];

    protected $casts = [
        'price_snapshot' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MenuVariant::class, 'variant_id');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(OrderItemAddon::class, 'order_item_id');
    }
}
