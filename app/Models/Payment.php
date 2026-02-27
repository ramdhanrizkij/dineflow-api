<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount_paid',
        'change_amount',
        'paid_by',
        'paid_at',
    ];

    protected $casts = [
        'amount_paid'   => 'decimal:2',
        'change_amount' => 'decimal:2',
        'paid_at'       => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
