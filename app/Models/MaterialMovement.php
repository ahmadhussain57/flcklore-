<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class MaterialMovement extends Model
{
    use HasUlids;

    // ======================
    // أنواع الحركات
    // ======================
    public const TYPE_PURCHASE   = 'purchase';   // شراء
    public const TYPE_SALE       = 'sale';       // بيع
    public const TYPE_RETURN     = 'return';     // مرتجع
    public const TYPE_ADJUSTMENT = 'adjustment'; // تسوية

    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity',
        'stock_before',
        'stock_after',
        'unit_cost',
        'total_cost',
        'reference_type',
        'reference_id',
        'description',
        'movement_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'quantity'      => 'decimal:2',
            'stock_before'  => 'decimal:2',
            'stock_after'   => 'decimal:2',
            'unit_cost'     => 'decimal:2',
            'total_cost'    => 'decimal:2',
            'movement_date' => 'datetime',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المرجع Polymorphic (Order, Invoice, ...)
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // ======================
    // دوال مساعدة
    // ======================

    /**
     * تسمية نوع الحركة بالعربية
     */
    public function getMovementTypeLabelAttribute(): string
    {
        return match ($this->movement_type) {
            self::TYPE_PURCHASE   => 'شراء',
            self::TYPE_SALE       => 'بيع',
            self::TYPE_RETURN     => 'مرتجع',
            self::TYPE_ADJUSTMENT => 'تسوية',
            default               => $this->movement_type,
        };
    }

    /**
     * هل الحركة دخول (زيادة)؟
     */
    public function isIncoming(): bool
    {
        return $this->quantity > 0;
    }

    /**
     * هل الحركة خروج (نقصان)؟
     */
    public function isOutgoing(): bool
    {
        return $this->quantity < 0;
    }

    /**
     * لون الحركة (للواجهة)
     */
    public function getMovementColorAttribute(): string
    {
        return match ($this->movement_type) {
            self::TYPE_PURCHASE   => 'green',
            self::TYPE_SALE       => 'red',
            self::TYPE_RETURN     => 'blue',
            self::TYPE_ADJUSTMENT => 'yellow',
            default               => 'gray',
        };
    }

    // ======================
    // دوال مساعدة (تسجيل حركة)
    // ======================

    /**
     * تسجيل حركة جديدة (تُستدعى تلقائياً عند البيع/الشراء)
     */
    public static function record(
        Product $product,
        string $type,
        float $quantity,
        ?float $unitCost = null,
        ?string $referenceType = null,
        ?string $referenceId = null,
        ?string $description = null,
        ?string $createdBy = null
    ): self {
        $stockBefore = $product->stock_quantity ?? 0;
        $stockAfter = $stockBefore + $quantity;

        return self::create([
            'product_id'     => $product->id,
            'movement_type'  => $type,
            'quantity'       => $quantity,
            'stock_before'   => $stockBefore,
            'stock_after'    => $stockAfter,
            'unit_cost'      => $unitCost ?? $product->price,
            'total_cost'     => ($unitCost ?? $product->price) * abs($quantity),
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
            'description'    => $description,
            'movement_date'  => now(),
            'created_by'     => $createdBy,
        ]);
    }
}