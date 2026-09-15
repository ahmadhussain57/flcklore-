<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasUlids;

    // ======================
    // الأنواع
    // ======================
    public const TYPE_ASSET     = 'asset';      // أصول
    public const TYPE_LIABILITY = 'liability';  // خصوم
    public const TYPE_EQUITY    = 'equity';     // حقوق ملكية
    public const TYPE_REVENUE   = 'revenue';    // إيرادات
    public const TYPE_EXPENSE   = 'expense';    // نفقات

    // ======================
    // طبيعة الرصيد
    // ======================
    public const BALANCE_DEBIT  = 'debit';   // مدين
    public const BALANCE_CREDIT = 'credit';  // دائن

    // ======================
    // أكواد الحسابات (ثوابت)
    // ======================
    public const CODE_CASH             = '1001';
    public const CODE_BANK             = '1002';
    public const CODE_INVENTORY        = '1003';
    public const CODE_RECEIVABLE       = '1004';
    public const CODE_PAYABLE          = '2001';
    public const CODE_CAPITAL          = '3001';
    public const CODE_SALES_REVENUE    = '4001';
    public const CODE_COGS             = '5001';
    public const CODE_OPERATING_EXPENSE= '5002';

    protected $fillable = [
        'code',
        'name',
        'name_en',
        'type',
        'normal_balance',
        'description',
        'is_system',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    // ======================
    // العلاقات (ستُستخدم لاحقاً)
    // ======================

    /**
     * سطور القيود المحاسبية المرتبطة بهذا الحساب
     */
    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class);
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
            self::TYPE_ASSET     => 'أصول',
            self::TYPE_LIABILITY => 'خصوم',
            self::TYPE_EQUITY    => 'حقوق ملكية',
            self::TYPE_REVENUE   => 'إيرادات',
            self::TYPE_EXPENSE   => 'نفقات',
            default              => $this->type,
        };
    }

    /**
     * تسمية طبيعة الرصيد بالعربية
     */
    public function getNormalBalanceLabelAttribute(): string
    {
        return match ($this->normal_balance) {
            self::BALANCE_DEBIT  => 'مدين',
            self::BALANCE_CREDIT => 'دائن',
            default              => $this->normal_balance,
        };
    }

    /**
     * جلب حساب بواسطة الكود
     */
    public static function findByCode(string $code): ?Account
    {
        return self::where('code', $code)->first();
    }
}