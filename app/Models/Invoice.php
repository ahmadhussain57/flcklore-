<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasUlids;

    // ======================
    // أنواع الفواتير
    // ======================
    public const TYPE_PURCHASE_WHOLESALE = 'purchase_wholesale'; // شراء جملة
    public const TYPE_PURCHASE_RETAIL    = 'purchase_retail';    // شراء مفرق
    public const TYPE_SALE_WHOLESALE     = 'sale_wholesale';     // مبيع جملة
    public const TYPE_SALE_RETAIL        = 'sale_retail';        // مبيع مفرق
    public const TYPE_SALE_RETURN        = 'sale_return';        // مرتجع مبيع

    // ======================
    // حالة الدفع
    // ======================
    public const PAYMENT_UNPAID  = 'unpaid';
    public const PAYMENT_PARTIAL = 'partial';
    public const PAYMENT_PAID    = 'paid';

    // ======================
    // طريقة الدفع
    // ======================
    public const PAYMENT_TYPE_CASH   = 'cash';   // نقداً
    public const PAYMENT_TYPE_CREDIT = 'credit'; // أجل

    protected $fillable = [
        'invoice_number',
        'type',
        'invoice_date',
        'due_date',
        'user_id',
        'party_name',
        'party_phone',
        'party_address',
        'order_id',
        'payment_status',
        'payment_type',
        'subtotal',
        'discount',
        'tax',
        'shipping_cost',
        'total',
        'paid_amount',
        'remaining_amount',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date'     => 'date',
            'due_date'         => 'date',
            'subtotal'         => 'decimal:2',
            'discount'         => 'decimal:2',
            'tax'              => 'decimal:2',
            'shipping_cost'    => 'decimal:2',
            'total'            => 'decimal:2',
            'paid_amount'      => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ======================
    // دوال مساعدة
    // ======================

    /**
     * تسمية النوع بالعربية
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_PURCHASE_WHOLESALE => 'شراء جملة',
            self::TYPE_PURCHASE_RETAIL    => 'شراء مفرق',
            self::TYPE_SALE_WHOLESALE     => 'مبيع جملة',
            self::TYPE_SALE_RETAIL        => 'مبيع مفرق',
            self::TYPE_SALE_RETURN        => 'مرتجع مبيع',
            default                       => $this->type,
        };
    }

    /**
     * تسمية حالة الدفع بالعربية
     */
    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_UNPAID  => 'غير مدفوع',
            self::PAYMENT_PARTIAL => 'مدفوع جزئياً',
            self::PAYMENT_PAID    => 'مدفوع',
            default               => $this->payment_status,
        };
    }

    /**
     * تسمية طريقة الدفع بالعربية
     */
    public function getPaymentTypeLabelAttribute(): string
    {
        return match ($this->payment_type) {
            self::PAYMENT_TYPE_CASH   => 'نقداً',
            self::PAYMENT_TYPE_CREDIT => 'أجل',
            default                   => $this->payment_type,
        };
    }

    /**
     * هل الفاتورة شراء؟
     */
    public function isPurchase(): bool
    {
        return in_array($this->type, [
            self::TYPE_PURCHASE_WHOLESALE,
            self::TYPE_PURCHASE_RETAIL,
        ]);
    }

    /**
     * هل الفاتورة مبيع؟
     */
    public function isSale(): bool
    {
        return in_array($this->type, [
            self::TYPE_SALE_WHOLESALE,
            self::TYPE_SALE_RETAIL,
            self::TYPE_SALE_RETURN,
        ]);
    }

    /**
     * هل الفاتورة مرتجع؟
     */
    public function isReturn(): bool
    {
        return $this->type === self::TYPE_SALE_RETURN;
    }

    /**
     * توليد رقم فاتورة فريد
     */
    public static function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $lastInvoice = self::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->first();

        $sequence = $lastInvoice
            ? ((int) substr($lastInvoice->invoice_number, -4)) + 1
            : 1;

        return sprintf('INV-%s-%04d', $year, $sequence);
    }
}