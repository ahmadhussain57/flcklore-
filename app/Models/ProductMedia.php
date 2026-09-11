<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductMedia extends Model
{
    use HasUlids;

    protected $table = 'product_media';

    protected $fillable = [
        'product_id',
        'media_type',
        'path',
        'public_id',  // ✅ جديد
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * هل هذه الوسائط صورة؟
     */
    public function isImage(): bool
    {
        return $this->media_type === 'image';
    }

    /**
     * هل هذه الوسائط فيديو؟
     */
    public function isVideo(): bool
    {
        return $this->media_type === 'video';
    }
}