<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * عرض كل المنتجات المنشورة (للجميع)
     */
    public function index(Request $request)
    {
        $query = Product::published()
            ->with(['categories', 'media']);

        // ✅ فلترة حسب النوع (مادي/رقمي)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // ✅ فلترة حسب التصنيف
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // ✅ فلترة حسب السعر
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // ✅ فلترة "عليه تخفيض"
        if ($request->boolean('on_sale')) {
            $query->whereNotNull('sale_price')
                  ->whereColumn('sale_price', '<', 'price');
        }

        // ✅ فلترة "متوفر فقط"
        if ($request->boolean('in_stock')) {
            $query->where(function ($q) {
                $q->where('type', Product::TYPE_DIGITAL)
                  ->orWhere('stock_quantity', '>', 0);
            });
        }

               // ✅ بحث في العنوان/الوصف/الكلمات المفتاحية
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // ✅ الترتيب
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'price_low'  => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'popular'    => $query->withCount('likes')->orderByDesc('likes_count'),
            'oldest'     => $query->oldest('published_at'),
            default      => $query->latest('published_at'), // latest
        };

        $products = $query->paginate(12)->withQueryString();

        // التصنيفات المتاحة للفلترة
        $categories = ProductCategory::whereHas('products', function ($q) {
            $q->where('status', Product::STATUS_PUBLISHED);
        })->withCount(['products' => function ($q) {
            $q->where('status', Product::STATUS_PUBLISHED);
        }])->orderBy('name')->get();

        // إحصائيات إضافية
        $stats = [
            'total'     => Product::published()->count(),
            'on_sale'   => Product::published()->whereNotNull('sale_price')->whereColumn('sale_price', '<', 'price')->count(),
            'in_stock'  => Product::published()->where(function ($q) {
                $q->where('type', Product::TYPE_DIGITAL)->orWhere('stock_quantity', '>', 0);
            })->count(),
        ];

        return view('shop.index', compact('products', 'categories', 'stats'));
    }

    /**
     * عرض منتج واحد (للجميع)
     */
    public function show(Product $product)
    {
        // التأكد من أن المنتج منشور
        if ($product->status !== Product::STATUS_PUBLISHED) {
            abort(404);
        }

        $product->load(['media', 'categories', 'author', 'comments.user', 'likes']);

        // منتجات مشابهة
        $relatedProducts = Product::published()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('product_categories.id', $product->categories->pluck('id'));
            })
            ->with(['media'])
            ->limit(4)
            ->get();

        // إذا لم توجد منتجات مشابهة كافية، أضف منتجات عشوائية
        if ($relatedProducts->count() < 4) {
            $more = Product::published()
                ->where('id', '!=', $product->id)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->with(['media'])
                ->inRandomOrder()
                ->limit(4 - $relatedProducts->count())
                ->get();

            $relatedProducts = $relatedProducts->merge($more);
        }

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}