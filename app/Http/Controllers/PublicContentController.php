<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicContentController extends Controller
{
    /**
     * عرض كل المحتويات المنشورة (للجميع)
     */
    public function index(Request $request): View
    {
        $query = Content::published()
            ->with(['author', 'categories', 'media', 'likes', 'comments']);

        // ✅ فلترة حسب النوع (اختياري)
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // ✅ فلترة حسب التصنيف (اختياري)
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->input('category'));
            });
        }

        // ✅ بحث في العنوان/الوصف (اختياري)
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhere('keywords', 'like', "%{$search}%");
            });
        }

        // ✅ الترتيب
        $sort = $request->input('sort', 'latest');
        $query = match ($sort) {
            'oldest'    => $query->oldest('published_at'),
            'popular'   => $query->withCount('likes')->orderByDesc('likes_count'),
            default     => $query->latest('published_at'),
        };

        $contents = $query->paginate(12)->withQueryString();

        // التصنيفات المتاحة للفلترة
        $categories = Category::whereHas('contents', function ($q) {
            $q->where('status', Content::STATUS_PUBLISHED);
        })->withCount(['contents' => function ($q) {
            $q->where('status', Content::STATUS_PUBLISHED);
        }])->get();

        return view('public.contents.index', compact('contents', 'categories'));
    }

    /**
     * عرض محتوى واحد (للجميع)
     */
    public function show(Content $content): View
    {
        // التأكد من أن المحتوى منشور (وإلا نُعيد 404)
        if ($content->status !== Content::STATUS_PUBLISHED) {
            abort(404);
        }

        // زيادة عدد المشاهدات (اختياري - يمكن تفعيلها لاحقاً)
        // $content->increment('views');

        $content->load(['author', 'categories', 'tags', 'media', 'comments.user', 'likes']);

        // محتويات ذات صلة (من نفس التصنيفات)
        $relatedContents = Content::published()
            ->where('id', '!=', $content->id)
            ->whereHas('categories', function ($q) use ($content) {
                $q->whereIn('categories.id', $content->categories->pluck('id'));
            })
            ->with(['author', 'media'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        return view('public.contents.show', compact('content', 'relatedContents'));
    }
}