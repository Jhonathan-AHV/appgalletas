<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyInventory extends Model
{
    protected $fillable = ['product_id', 'date', 'initial_stock', 'current_stock'];

    protected $casts = [
        'date' => 'date',
        'initial_stock' => 'integer',
        'current_stock' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}