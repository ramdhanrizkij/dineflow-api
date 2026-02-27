<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'name',
        'description',
        'base_price',
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'base_price' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MenuVariant::class, 'menu_id');
    }

    public function addons(): HasMany
    {
        return $this->hasMany(MenuAddon::class, 'menu_id');
    }

    public function defaultVariant(): HasMany
    {
        return $this->hasMany(MenuVariant::class, 'menu_id')->where('is_default', true);
    }
}
