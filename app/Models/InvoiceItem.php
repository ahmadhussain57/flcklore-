<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasUlids;

    protected $fillable = [
        'invoice_id',
        'product_id',
        'product_title',
        'product_sku',
        'product_unit',
        'quantity',
        'unit_price',
        'discount',
        'subtotal',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'   => 'decimal:2',
            'unit_price' => 'decimal:2',
            'discount'   => 'decimal:2',
            'subtotal'   => 'decimal:2',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}