<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasUlids;

    // ======================
    // الحالات
    // ======================
    public const STATUS_PENDING    = 'pending';
    public const STATUS_PAID       = 'paid';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED    = 'shipped';
    public const STATUS_CANCELLED  = 'cancelled';

    // ======================
    // طرق الدفع
    // ======================
    public const PAYMENT_COD           = 'cash_on_delivery';
    public const PAYMENT_BANK_TRANSFER = 'bank_transfer';
    public const PAYMENT_STRIPE        = 'stripe';
    public const PAYMENT_PAYPAL        = 'paypal';

    // ======================
    // حالة الدفع
    // ======================
    public const PAYMENT_STATUS_UNPAID   = 'unpaid';
    public const PAYMENT_STATUS_PAID     = 'paid';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';

    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'shipping_cost',
        'total',
        'payment_method',
        'payment_status',
        'notes',
        'shipping_name',
        'shipping_phone',
        'shipping_city',
        'shipping_address',
        'placed_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'      => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total'         => 'decimal:2',
            'placed_at'     => 'datetime',
            'paid_at'       => 'datetime',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ======================
    // دوال مساعدة
    // ======================

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_STATUS_PAID;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * هل يمكن إلغاء الطلب؟
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_PAID,
        ]);
    }

    /**
     * عدد المنتجات الكلي
     */
    public function getTotalItemsAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }

    /**
     * تسمية الحالة بالعربية
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING    => 'بانتظار الدفع',
            self::STATUS_PAID       => 'مدفوع',
            self::STATUS_PROCESSING => 'قيد التجهيز',
            self::STATUS_SHIPPED    => 'تم الشحن',
            self::STATUS_CANCELLED  => 'ملغى',
            default                 => $this->status,
        };
    }

    /**
     * تسمية حالة الدفع بالعربية
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_STATUS_UNPAID   => 'غير مدفوع',
            self::PAYMENT_STATUS_PAID     => 'مدفوع',
            self::PAYMENT_STATUS_REFUNDED => 'مسترد',
            default                       => $this->payment_status,
        };
    }

    /**
     * تسمية طريقة الدفع بالعربية
     */
    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            self::PAYMENT_COD           => 'الدفع عند الاستلام',
            self::PAYMENT_BANK_TRANSFER => 'تحويل بنكي',
            self::PAYMENT_STRIPE        => 'بطاقة ائتمان',
            self::PAYMENT_PAYPAL        => 'PayPal',
            default                     => $this->payment_method,
        };
    }

    /**
     * هل يحتوي الطلب على منتجات مادية؟
     */
    public function hasPhysicalProducts(): bool
    {
        return $this->items()
            ->where('product_type', Product::TYPE_PHYSICAL)
            ->exists();
    }

    /**
     * توليد رقم طلب فريد
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        $lastOrder = self::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->first();

        $sequence = $lastOrder
            ? ((int) substr($lastOrder->order_number, -4)) + 1
            : 1;

        return sprintf('ORD-%s-%04d', $year, $sequence);
    }

    // ======================
    // ✅ الحالات المتاحة (جديدة)
    // ======================

    /**
     * الحالات المتاحة من الحالة الحالية
     * (تُستخدم لمنع الانتقالات غير المنطقية)
     */
    public function availableTransitions(): array
    {
        return match ($this->status) {
            self::STATUS_PENDING    => [self::STATUS_PAID, self::STATUS_CANCELLED],
            self::STATUS_PAID       => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
            self::STATUS_PROCESSING => [self::STATUS_SHIPPED, self::STATUS_CANCELLED],
            default                 => [],
        };
    }

    /**
     * تسميات الحالات المتاحة (عربية) للعرض
     */
    public function getAvailableTransitionsLabelsAttribute(): array
    {
        $labels = [
            self::STATUS_PENDING    => 'بانتظار الدفع',
            self::STATUS_PAID       => 'مدفوع',
            self::STATUS_PROCESSING => 'قيد التجهيز',
            self::STATUS_SHIPPED    => 'تم الشحن',
            self::STATUS_CANCELLED  => 'ملغى',
        ];

        return array_map(
            fn($status) => $labels[$status] ?? $status,
            $this->availableTransitions()
        );
    }

        /**
     * الفاتورة المرتبطة بالطلب (إن وُجدت)
     */
    public function invoice(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}