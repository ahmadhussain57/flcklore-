<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Content extends Model
{
    use HasUlids;

    public const TYPE_ARTICLE = 'article';
    public const TYPE_IMAGE = 'image';
    public const TYPE_VIDEO = 'video';
    public const TYPE_AUDIO = 'audio';

    public const STATUS_DRAFT = 'draft';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_PUBLISHED = 'published';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PENDING_EDIT = 'pending_edit';

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'type',
        'body',
        'keywords',
        'historical_importance',
        'geographic_location',
        'status',
        'rejection_reason',
        'queued_at',
        'published_at',
        'reviewed_by',
        'reviewed_at',
        'original_id',
        'locked_by',
        'locked_at',
    ];

    protected function casts(): array
    {
        return [
            'queued_at' => 'datetime',
            'published_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'locked_at' => 'datetime',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ContentMedia::class)->orderBy('sort_order');
    }

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
    public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}

public function likes(): MorphMany
{
    return $this->morphMany(Like::class, 'likeable');
}

// علاقات الحجز
public function lockedBy(): BelongsTo
{
    return $this->belongsTo(User::class, 'locked_by');
}

// التحقق من الحجز
public function isLocked(): bool
{
    if (!$this->locked_by || !$this->locked_at) {
        return false;
    }
    // الحجز صالح لمدة 15 دقيقة
    return $this->locked_at->diffInMinutes(now()) < 15;
}

// التحقق من أن المستخدم الحالي هو من حجز المحتوى
public function isLockedByCurrentUser(): bool
{
    return $this->locked_by === auth()->id() && $this->isLocked();
}

// فتح الحجز
public function unlock(): void
{
    $this->update([
        'locked_by' => null,
        'locked_at' => null,
    ]);
}
}