<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleUpgradeRequest extends Model
{
    use HasUlids;

    protected $fillable = [
        'user_id',
        'section',
        'requested_role',
        'message',
        'status',
        'admin_note',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public static function allowedRolesFor(string $section): array
    {
        return match ($section) {
            'content' => ['content_author', 'content_Reviewer'],
            'marketing' => ['marketing_Specialist', 'marketing_Accountant'],
            default => [],
        };
    }
}