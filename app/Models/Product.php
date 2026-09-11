<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;  // ✅ جديد

class Product extends Model
{
    use HasUlids;

    // ======================
    // ثوابت النوع
    // ======================
    public const TYPE_PHYSICAL = 'physical';
    public const TYPE_DIGITAL  = 'digital';

    // ======================
    // ثوابت الحالة
    // ======================
    public const STATUS_DRAFT          = 'draft';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_PUBLISHED      = 'published';
    public const STATUS_REJECTED       = 'rejected';
    public const STATUS_PENDING_EDIT   = 'pending_edit';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'short_description',
        'description',
        'type',
        'price',
        'sale_price',
        'stock_quantity',
        'sku',
        'status',
        'rejection_reason',
        'queued_at',
        'published_at',
        'reviewed_by',
        'reviewed_at',
        'locked_by',
        'locked_at',
        'original_id',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'queued_at' => 'datetime',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_category');
    }

    public function media(): HasMany
    {
        return $this->hasMany(ProductMedia::class)->orderBy('sort_order');
    }

    // علاقة النسخة الأصلية
    public function original(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'original_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(Product::class, 'original_id');
    }

    // ======================
    // ✅ العلاقات متعددة الأشكال (جديدة)
    // ======================

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    // ======================
    // Scopes
    // ======================

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeInReviewQueue(Builder $query): Builder
    {
        return $query
            ->whereIn('status', [self::STATUS_PENDING_REVIEW, self::STATUS_PENDING_EDIT])
            ->orderBy('queued_at');
    }

    public function scopeInStock(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('type', self::TYPE_DIGITAL)
              ->orWhere('stock_quantity', '>', 0);
        });
    }

    // ======================
    // دوال مساعدة للحالة
    // ======================

    public function isInQueue(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING_REVIEW,
            self::STATUS_PENDING_EDIT,
        ], true);
    }

    public function submitToQueue(): void
    {
        $this->update([
            'status' => $this->status === self::STATUS_PUBLISHED
                ? self::STATUS_PENDING_EDIT
                : self::STATUS_PENDING_REVIEW,
            'queued_at' => now(),
            'rejection_reason' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    // ======================
    // دوال الحجز
    // ======================

    public function isLocked(): bool
    {
        if (!$this->locked_by || !$this->locked_at) {
            return false;
        }
        return $this->locked_at->diffInMinutes(now()) < 15;
    }

    public function isLockedByCurrentUser(): bool
    {
        return $this->locked_by === auth()->id() && $this->isLocked();
    }

    public function unlock(): void
    {
        $this->update([
            'locked_by' => null,
            'locked_at' => null,
        ]);
    }

    // ======================
    // دوال مساعدة للسعر والمخزون
    // ======================

    /**
     * إرجاع السعر الفعلي (سعر التخفيض إن وجد، وإلا السعر الأصلي)
     */
    public function getEffectivePriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    /**
     * هل المنتج عليه تخفيض؟
     */
    public function hasDiscount(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }

    /**
     * نسبة التخفيض
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }
        return (int) round((($this->price - $this->sale_price) / $this->price) * 100);
    }

    /**
     * هل المنتج متوفر في المخزون؟
     */
    public function isInStock(): bool
    {
        if ($this->type === self::TYPE_DIGITAL) {
            return true;
        }
        return $this->stock_quantity > 0;
    }

    /**
     * هل المنتج مادي؟
     */
    public function isPhysical(): bool
    {
        return $this->type === self::TYPE_PHYSICAL;
    }

    /**
     * هل المنتج رقمي؟
     */
    public function isDigital(): bool
    {
        return $this->type === self::TYPE_DIGITAL;
    }

    /**
     * إرجاع صورة الغلاف (أول صورة في الوسائط)
     */
    public function getCoverImageAttribute(): ?string
    {
        $image = $this->media->where('media_type', 'image')->first();
        return $image?->path;
    }
}