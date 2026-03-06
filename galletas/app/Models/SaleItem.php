<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'product_id', 'quantity', 'price_at_moment'];

    protected $casts = [
        'quantity' => 'integer',
        'price_at_moment' => 'integer',
    ];

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // Helper: subtotal del item
    public function getSubtotalAttribute(): int
    {
        return $this->quantity * $this->price_at_moment;
    }
}