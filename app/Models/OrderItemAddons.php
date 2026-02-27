<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItemAddons extends Model
{
     public $timestamps = false;

    protected $fillable = [
        'order_item_id',
        'addon_id',
        'price_snapshot',
    ];

    protected $casts = [
        'price_snapshot' => 'decimal:2',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(MenuAddon::class, 'addon_id');
    }
}
