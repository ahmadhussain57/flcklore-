<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_title',
        'product_price',
        'product_type',
        'product_sku',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'product_price' => 'decimal:2',
            'quantity'      => 'integer',
            'subtotal'      => 'decimal:2',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // ======================
    // دوال مساعدة
    // ======================

    public function isPhysical(): bool
    {
        return $this->product_type === Product::TYPE_PHYSICAL;
    }

    public function isDigital(): bool
    {
        return $this->product_type === Product::TYPE_DIGITAL;
    }
}