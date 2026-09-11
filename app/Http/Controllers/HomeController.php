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
        // آخر 6 محتويات منشورة
        $latestContents = Content::published()
            ->with(['author', 'categories', 'media', 'likes', 'comments'])
            ->latest('published_at')
            ->limit(6)
            ->get();

        // آخر 4 منتجات منشورة
        $latestProducts = Product::published()
            ->with(['categories', 'media'])
            ->latest('published_at')
            ->limit(4)
            ->get();

        // التصنيفات (المحتوى) - التي تحتوي على محتوى منشور واحد على الأقل
        $contentCategories = Category::whereHas('contents', function ($q) {
                $q->where('status', Content::STATUS_PUBLISHED);
            })
            ->withCount(['contents' => function ($q) {
                $q->where('status', Content::STATUS_PUBLISHED);
            }])
            ->limit(6)
            ->get();

        // التصنيفات (المنتجات) - التي تحتوي على منتج منشور واحد على الأقل
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
            'contentCategories',
            'productCategories',
            'stats'
        ));
    }
}