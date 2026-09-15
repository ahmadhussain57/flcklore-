<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ======================
    // دوال مساعدة
    // ======================

    /**
     * المجموع الفرعي لهذا العنصر
     */
    public function getSubtotalAttribute(): float
    {
        if (!$this->product) {
            return 0;
        }

        return (float) ($this->product->effective_price * $this->quantity);
    }

    /**
     * هل الكمية المتوفرة كافية؟
     */
    public function hasEnoughStock(): bool
    {
        if (!$this->product) {
            return false;
        }

        // المنتجات الرقمية: دائماً متوفرة
        if ($this->product->isDigital()) {
            return true;
        }

        return $this->product->stock_quantity >= $this->quantity;
    }
}