<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentMedia extends Model
{
    use HasUlids;

    protected $table = 'content_media';

    protected $fillable = [
        'content_id',
        'media_type',
        'path',
        'public_id',  // ✅ جديد
        'original_name',
        'mime_type',
        'size',
        'sort_order',
    ];

    public function content(): BelongsTo
    {
        return $this->belongsTo(Content::class);
    }
}