<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Content;
use App\Models\Product;
use App\Notifications\CommentApproved;
use App\Notifications\CommentRejected;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CommentReviewController extends Controller
{
    /**
     * عرض التعليقات المعلقة (للمحتوى)
     */
    public function indexContent(Request $request): View
    {
        $query = Comment::with(['user', 'commentable'])
            ->where('commentable_type', Content::class)
            ->where('status', Comment::STATUS_PENDING)
            ->latest();

        $comments = $query->paginate(20);

        // إحصائيات
        $stats = [
            'pending'  => Comment::where('commentable_type', Content::class)
                ->where('status', Comment::STATUS_PENDING)
                ->count(),
            'approved' => Comment::where('commentable_type', Content::class)
                ->where('status', Comment::STATUS_APPROVED)
                ->count(),
            'rejected' => Comment::where('commentable_type', Content::class)
                ->where('status', Comment::STATUS_REJECTED)
                ->count(),
            'my_reviews' => Comment::where('reviewed_by', Auth::id())
                ->whereNotNull('reviewed_at')
                ->count(),
        ];

        return view('admin.comments.content-index', compact('comments', 'stats'));
    }

    /**
     * عرض التعليقات المعلقة (للمنتجات)
     */
    public function indexProduct(Request $request): View
    {
        $query = Comment::with(['user', 'commentable'])
            ->where('commentable_type', Product::class)
            ->where('status', Comment::STATUS_PENDING)
            ->latest();

        $comments = $query->paginate(20);

        // إحصائيات
        $stats = [
            'pending'  => Comment::where('commentable_type', Product::class)
                ->where('status', Comment::STATUS_PENDING)
                ->count(),
            'approved' => Comment::where('commentable_type', Product::class)
                ->where('status', Comment::STATUS_APPROVED)
                ->count(),
            'rejected' => Comment::where('commentable_type', Product::class)
                ->where('status', Comment::STATUS_REJECTED)
                ->count(),
            'my_reviews' => Comment::where('reviewed_by', Auth::id())
                ->whereNotNull('reviewed_at')
                ->count(),
        ];

        return view('admin.comments.product-index', compact('comments', 'stats'));
    }

    /**
     * عرض صفحة معاينة تعليق واحد
     */
    public function show(Comment $comment): View
    {
        // التحقق من الصلاحية حسب النوع
        $this->authorizeCommentReview($comment);

        $comment->load(['user', 'commentable', 'reviewer']);

        return view('admin.comments.show', compact('comment'));
    }

    /**
     * الموافقة على التعليق
     */
    public function approve(Comment $comment): RedirectResponse
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermissionTo('approve_comment', 'web')) {
            abort(403, 'غير مصرح لك بالموافقة على التعليقات.');
        }

        // التحقق من أن التعليق معلق
        if (!$comment->isPending()) {
            return back()->with('error', 'هذا التعليق تمت معالجته مسبقاً.');
        }

        $comment->update([
            'status'      => Comment::STATUS_APPROVED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'rejection_reason' => null,
        ]);

        // ✅ إرسال إشعار للمُعلِّق
        $comment->user->notify(new CommentApproved($comment));

        return back()->with('success', '✅ تم الموافقة على التعليق ونشره.');
    }

    /**
     * رفض التعليق مع سبب
     */
    public function reject(Request $request, Comment $comment): RedirectResponse
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermissionTo('reject_comment', 'web')) {
            abort(403, 'غير مصرح لك برفض التعليقات.');
        }

        // التحقق من أن التعليق معلق
        if (!$comment->isPending()) {
            return back()->with('error', 'هذا التعليق تمت معالجته مسبقاً.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5|max:500',
        ], [
            'rejection_reason.required' => 'يجب كتابة سبب الرفض.',
            'rejection_reason.min'      => 'سبب الرفض قصير جداً (5 أحرف على الأقل).',
            'rejection_reason.max'      => 'سبب الرفض طويل جداً (500 حرف كحد أقصى).',
        ]);

        $comment->update([
            'status'           => Comment::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
        ]);

        // ✅ إرسال إشعار للمُعلِّق
        $comment->user->notify(new CommentRejected($comment, $request->rejection_reason));

        return back()->with('success', '❌ تم رفض التعليق.');
    }

    /**
     * حذف تعليق (من قِبل المدقق)
     */
    public function destroy(Comment $comment): RedirectResponse
    {
        // التحقق من الصلاحية
        $this->authorizeCommentReview($comment);

        $comment->delete();

        return back()->with('success', '🗑️ تم حذف التعليق بنجاح.');
    }

    /**
     * ✅ التحقق من صلاحية مراجعة التعليق حسب نوعه
     */
    private function authorizeCommentReview(Comment $comment): void
    {
        $user = Auth::user();

        // إذا كان التعليق على محتوى
        if ($comment->commentable_type === Content::class) {
            if (!$user->hasAnyRole(['content_admin', 'content_Reviewer'])) {
                abort(403, 'غير مصرح لك بمراجعة تعليقات المحتوى.');
            }
        }
        // إذا كان التعليق على منتج
        elseif ($comment->commentable_type === Product::class) {
            if (!$user->hasRole('marketing_admin')) {
                abort(403, 'غير مصرح لك بمراجعة تعليقات المنتجات.');
            }
        }
    }
}