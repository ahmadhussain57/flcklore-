<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
    ];

    // ======================
    // العلاقات
    // ======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    // ======================
    // دوال مساعدة
    // ======================

    /**
     * المجموع الفرعي للسلة (بدون شحن)
     */
    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->product->effective_price * $item->quantity;
        });
    }

    /**
     * عدد المنتجات الكلي في السلة
     */
    public function getTotalItemsAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * هل السلة فارغة؟
     */
    public function isEmpty(): bool
    {
        return $this->items()->count() === 0;
    }

    /**
     * هل تحتوي السلة على منتجات مادية؟
     */
    public function hasPhysicalProducts(): bool
    {
        return $this->items()
            ->whereHas('product', function ($q) {
                $q->where('type', Product::TYPE_PHYSICAL);
            })
            ->exists();
    }
}