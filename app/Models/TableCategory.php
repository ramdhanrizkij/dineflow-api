<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }
}
