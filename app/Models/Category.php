<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function contents(): BelongsToMany
    {
        return $this->belongsToMany(Content::class);
    }
}