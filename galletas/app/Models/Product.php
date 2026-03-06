<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = ['name', 'price'];

    protected $casts = [
        'price' => 'integer',
    ];

    public function dailyInventories(): HasMany
    {
        return $this->hasMany(DailyInventory::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}