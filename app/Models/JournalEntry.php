<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalEntry extends Model
{
    use HasUlids;

    // ======================
    // الأنواع
    // ======================
    public const TYPE_CASH       = 'cash';       // سند نقداً
    public const TYPE_CREDIT     = 'credit';     // سند أجل
    public const TYPE_ADJUSTMENT = 'adjustment'; // سند تسوية

    protected $fillable = [
        'entry_number',
        'entry_date',
        'type',
        'description',
        'reference_type',
        'reference_id',
        'total_debit',
        'total_credit',
        'created_by',
        'is_posted',
        'posted_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'entry_date'    => 'date',
            'total_debit'   => 'decimal:2',
            'total_credit'  => 'decimal:2',
            'is_posted'     => 'boolean',
            'posted_at'     => 'datetime',
        ];
    }

    // ======================
    // العلاقات
    // ======================

    public function lines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * المرجع polymorphic (Order, Invoice, ...)
     */
    public function reference(): MorphTo
    {
        return $this->morphTo();
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
            self::TYPE_CASH       => 'نقداً',
            self::TYPE_CREDIT     => 'أجل',
            self::TYPE_ADJUSTMENT => 'تسوية',
            default               => $this->type,
        };
    }

    /**
     * هل السند متوازن؟ (مدين = دائن)
     */
    public function isBalanced(): bool
    {
        return bccomp((string) $this->total_debit, (string) $this->total_credit, 2) === 0;
    }

    /**
     * توليد رقم سند فريد
     */
    public static function generateEntryNumber(): string
    {
        $year = date('Y');
        $lastEntry = self::whereYear('created_at', $year)
            ->orderByDesc('created_at')
            ->first();

        $sequence = $lastEntry
            ? ((int) substr($lastEntry->entry_number, -4)) + 1
            : 1;

        return sprintf('JE-%s-%04d', $year, $sequence);
    }
}