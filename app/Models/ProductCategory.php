<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ProductCategory extends Model
{
    use HasUlids;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_product_category');
    }

    /**
     * عدد المنتجات المنشورة في هذا التصنيف
     */
    public function publishedProductsCount(): int
    {
        return $this->products()
            ->where('status', Product::STATUS_PUBLISHED)
            ->count();
    }
}