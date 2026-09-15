<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Comment;
use App\Notifications\ProductApproved;
use App\Notifications\ProductRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    /**
     * عرض قائمة انتظار مراجعة المنتجات مع إحصائيات
     */
    public function index()
    {
        $pendingProducts = Product::with(['author', 'categories', 'media'])
            ->whereIn('status', [Product::STATUS_PENDING_REVIEW, Product::STATUS_PENDING_EDIT])
            ->orderBy('queued_at', 'asc')
            ->paginate(15);

        $stats = [
            'total' => Product::whereIn('status', [Product::STATUS_PENDING_REVIEW, Product::STATUS_PENDING_EDIT])->count(),
            'reviewed_today' => Product::where('reviewed_by', Auth::id())
                ->whereDate('reviewed_at', today())
                ->count(),
            'my_history' => Product::where('reviewed_by', Auth::id())
                ->whereNotNull('reviewed_at')
                ->count(),

            // ✅ إحصائيات التعليقات المعلقة (للمنتجات)
            'pending_comments' => Comment::where('status', Comment::STATUS_PENDING)
                ->where('commentable_type', Product::class)
                ->count(),

            // ✅ إحصائيات التعليقات التي راجعها المستخدم
            'my_comment_reviews' => Comment::where('reviewed_by', Auth::id())
                ->whereNotNull('reviewed_at')
                ->count(),
        ];

        return view('products.review.index', compact('pendingProducts', 'stats'));
    }

    /**
     * عرض صفحة المعاينة مع الحجز المؤقت
     */
    public function preview(Product $product)
    {
        if (!$product->isInQueue()) {
            return redirect()->route('products.review.index')
                ->with('error', 'هذا المنتج ليس في قائمة الانتظار.');
        }

        if ($product->isLocked() && !$product->isLockedByCurrentUser()) {
            return redirect()->route('products.review.index')
                ->with('error', 'هذا المنتج محجوز من قبل مدقق آخر.');
        }

        if (!$product->locked_by) {
            $product->update([
                'locked_by' => Auth::id(),
                'locked_at' => now(),
            ]);
            $product->refresh();
        }

        $product->load(['author', 'categories', 'media']);

        return view('products.review.preview', compact('product'));
    }

    /**
     * الموافقة على المنتج ونشره
     */
    public function approve(Product $product)
    {
        if (!Auth::user()->hasPermissionTo('approve_product', 'web')) {
            abort(403, 'غير مصرح لك بالموافقة على المنتج.');
        }

        if (!$product->isInQueue()) {
            return redirect()->route('products.review.index')
                ->with('error', 'هذا المنتج ليس في قائمة الانتظار.');
        }

        if (!$product->isLockedByCurrentUser()) {
            return redirect()->route('products.review.index')
                ->with('error', 'لا يمكنك الموافقة على منتج لم تحجزه.');
        }

        // ✅ حفظ المؤلف قبل النشر (لأن النسخة قد تُحذف)
        $author = $product->author;

        // نشر المنتج (يُرجع المنتج النهائي)
        $publishedProduct = $this->publishProduct($product);

        // ✅ إرسال إشعار للمتخصص
        $author->notify(new ProductApproved($publishedProduct));

        return redirect()->route('products.review.index')
            ->with('success', '✅ تم نشر المنتج بنجاح.');
    }

    /**
     * رفض المنتج مع سبب
     */
    public function reject(Request $request, Product $product)
    {
        if (!Auth::user()->hasPermissionTo('reject_product', 'web')) {
            abort(403, 'غير مصرح لك برفض المنتج.');
        }

        if (!$product->isInQueue()) {
            return redirect()->route('products.review.index')
                ->with('error', 'هذا المنتج ليس في قائمة الانتظار.');
        }

        if (!$product->isLockedByCurrentUser()) {
            return redirect()->route('products.review.index')
                ->with('error', 'لا يمكنك رفض منتج لم تحجزه.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ], [
            'rejection_reason.required' => 'يجب كتابة سبب الرفض.',
            'rejection_reason.min' => 'سبب الرفض قصير جداً (5 أحرف على الأقل).',
        ]);

        $product->update([
            'status' => Product::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'locked_by' => null,
            'locked_at' => null,
        ]);

        // ✅ إرسال إشعار للمتخصص مع سبب الرفض
        $product->author->notify(new ProductRejected($product, $request->rejection_reason));

        return redirect()->route('products.review.index')
            ->with('success', '❌ تم رفض المنتج بنجاح.');
    }

    /**
     * عرض تاريخ مراجعات المدقق
     */
    public function history()
    {
        $history = Product::with(['author', 'categories', 'media'])
            ->where('reviewed_by', Auth::id())
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->paginate(20);

        return view('products.review.history', compact('history'));
    }

    /**
     * دالة مساعدة لنشر المنتج
     */
    private function publishProduct(Product $product): Product
    {
        if ($product->status === Product::STATUS_PENDING_EDIT && $product->original_id) {
            $original = Product::find($product->original_id);

            if ($original) {
                $original->update([
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'short_description' => $product->short_description,
                    'description' => $product->description,
                    'type' => $product->type,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'stock_quantity' => $product->stock_quantity,
                    'sku' => $product->sku,
                    'updated_at' => now(),
                ]);

                $original->categories()->sync($product->categories->pluck('id'));

                foreach ($product->media as $media) {
                    $original->media()->create([
                        'media_type' => $media->media_type,
                        'path' => $media->path,
                        'public_id' => $media->public_id,
                        'original_name' => $media->original_name,
                        'mime_type' => $media->mime_type,
                        'size' => $media->size,
                        'sort_order' => $media->sort_order,
                    ]);
                }

                $product->media()->delete();
                $product->delete();

                $product = $original;
            }
        }

        $product->update([
            'status' => Product::STATUS_PUBLISHED,
            'published_at' => now(),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'locked_by' => null,
            'locked_at' => null,
            'rejection_reason' => null,
        ]);

        return $product;
    }
}