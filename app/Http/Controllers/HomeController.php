<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // ✅ أحدث المنتجات (4)
        $latestProducts = Product::published()
            ->with(['categories', 'media'])
            ->withCount(['likes', 'comments'])
            ->latest('published_at')
            ->limit(4)
            ->get()
            ->map(function ($item) {
                $item->item_type = 'product';
                return $item;
            });

        // ✅ أحدث المحتوى الرقمي (4)
        $latestContents = Content::published()
            ->with(['author', 'categories', 'media'])
            ->withCount(['likes', 'comments'])
            ->latest('published_at')
            ->limit(4)
            ->get()
            ->map(function ($item) {
                $item->item_type = 'content';
                return $item;
            });

        // ✅ الأكثر مشاركة (4 - مختلط: منتجات + محتويات)
        $mostCommented = $this->getMixedItems('comments');

        // ✅ الأعلى تقييماً (4 - مختلط: منتجات + محتويات)
        $mostLiked = $this->getMixedItems('likes');

        // التصنيفات (المحتوى)
        $contentCategories = Category::whereHas('contents', function ($q) {
                $q->where('status', Content::STATUS_PUBLISHED);
            })
            ->withCount(['contents' => function ($q) {
                $q->where('status', Content::STATUS_PUBLISHED);
            }])
            ->limit(6)
            ->get();

        // التصنيفات (المنتجات)
        $productCategories = ProductCategory::whereHas('products', function ($q) {
                $q->where('status', Product::STATUS_PUBLISHED);
            })
            ->withCount(['products' => function ($q) {
                $q->where('status', Product::STATUS_PUBLISHED);
            }])
            ->limit(4)
            ->get();

        // إحصائيات عامة
        $stats = [
            'contents' => Content::published()->count(),
            'products' => Product::published()->count(),
            'authors' => User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['content_author', 'content_admin']);
            })->count(),
            'categories' => Category::count() + ProductCategory::count(),
        ];

        return view('home', compact(
            'latestContents',
            'latestProducts',
            'mostCommented',
            'mostLiked',
            'contentCategories',
            'productCategories',
            'stats'
        ));
    }

    /**
     * ✅ جلب العناصر المختلطة (منتجات + محتويات) الأكثر تعليقاً أو إعجاباً
     *
     * @param string $type 'comments' | 'likes'
     */
    private function getMixedItems(string $type)
    {
        $countField = $type === 'comments' ? 'comments_count' : 'likes_count';

        // المحتوى الرقمي
        $contents = Content::published()
            ->has($type)
            ->withCount($type)
            ->with(['author', 'media', 'categories'])
            ->orderByDesc($countField)
            ->limit(4)
            ->get()
            ->map(function ($item) {
                $item->item_type = 'content';
                return $item;
            });

        // المنتجات
        $products = Product::published()
            ->has($type)
            ->withCount($type)
            ->with(['media', 'categories'])
            ->orderByDesc($countField)
            ->limit(4)
            ->get()
            ->map(function ($item) {
                $item->item_type = 'product';
                return $item;
            });

        // دمج + ترتيب + أخذ أعلى 4
        return $contents->concat($products)
            ->sortByDesc($countField)
            ->take(4)
            ->values();
    }
}