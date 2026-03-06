<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'payment_method',
        'total_amount',
        'is_paid',
        'sale_date'
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'total_amount' => 'integer',
        'sale_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Helper: verificar si aún debe dinero
    public function getPendingAmountAttribute(): int
    {
        if ($this->payment_method !== 'credito') return 0;
        
        $paid = $this->payments->sum('amount');
        return max(0, $this->total_amount - $paid);
    }
}