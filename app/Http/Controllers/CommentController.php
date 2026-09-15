<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Product;
use App\Models\Comment;
use App\Models\User;
use App\Notifications\NewCommentPending;
use App\Notifications\NewCommentOnContent;
use App\Notifications\NewCommentOnProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * إضافة تعليق على محتوى
     */
    public function store(Request $request, Content $content)
    {
        return $this->storeComment(
            $request,
            $content,
            Content::STATUS_PUBLISHED,
            'content'
        );
    }

    /**
     * إضافة تعليق على منتج
     */
    public function storeProduct(Request $request, Product $product)
    {
        return $this->storeComment(
            $request,
            $product,
            Product::STATUS_PUBLISHED,
            'product'
        );
    }

    /**
     * دالة موحدة لإضافة التعليق (تعمل مع أي موديل)
     */
    private function storeComment(Request $request, $model, string $requiredStatus, string $type)
    {
        // التحقق من صلاحية التعليق
        if (!Auth::user()->hasPermissionTo('comment_on_content', 'web')) {
            abort(403, 'غير مصرح لك بالتعليق.');
        }

        $request->validate([
            'body' => 'required|string|min:2|max:1000',
        ], [
            'body.required' => 'لا يمكن إرسال تعليق فارغ.',
            'body.min'      => 'التعليق قصير جداً (حرفين على الأقل).',
            'body.max'      => 'التعليق طويل جداً (الحد 1000 حرف).',
        ]);

        // التحقق من أن العنصر منشور
        if ($model->status !== $requiredStatus) {
            $message = $type === 'product'
                ? 'لا يمكنك التعليق على منتج غير منشور.'
                : 'لا يمكنك التعليق على محتوى غير منشور.';
            return back()->with('error', $message);
        }

        // ✅ إنشاء التعليق بحالة pending
        $comment = Comment::create([
            'user_id'          => Auth::id(),
            'body'             => $request->body,
            'commentable_type' => get_class($model),
            'commentable_id'   => $model->id,
            'status'           => Comment::STATUS_PENDING,  // ✅ جديد
        ]);

        // ✅ إرسال إشعار للمدققين المناسبين حسب النوع
        $this->notifyReviewers($comment, $model, $type);

        return back()->with('success', 'تم إرسال تعليقك بنجاح! سيظهر بعد موافقة المدقق.');
    }

    /**
     * ✅ إرسال إشعار للمدققين المناسبين حسب نوع التعليق
     */
    private function notifyReviewers(Comment $comment, $model, string $type): void
    {
        // تحديد الأدوار المستهدفة
        if ($type === 'content') {
            $targetRoles = ['content_admin', 'content_Reviewer'];
        } elseif ($type === 'product') {
            $targetRoles = ['marketing_admin'];
        } else {
            return;
        }

        // جلب المستخدمين الذين لديهم الصلاحية
        $reviewers = User::whereHas('roles', function ($q) use ($targetRoles) {
            $q->whereIn('name', $targetRoles);
        })->get();

        // إرسال الإشعار لكل مدقق (مع استثناء صاحب التعليق نفسه)
        foreach ($reviewers as $reviewer) {
            if ($reviewer->id === Auth::id()) {
                continue;
            }

            $reviewer->notify(new NewCommentPending($comment, $model, $type));
        }
    }

    /**
     * حذف تعليق
     */
    public function destroy(Comment $comment)
    {
        // التحقق من الملكية أو صلاحية الحذف العام (للمدير)
        if ($comment->user_id !== Auth::id() && !Auth::user()->hasPermissionTo('delete_any_content', 'web')) {
            abort(403, 'غير مصرح لك بحذف هذا التعليق.');
        }

        // التحقق من صلاحية حذف تعليقه الخاص
        if ($comment->user_id === Auth::id() && !Auth::user()->hasPermissionTo('delete_own_comment', 'web')) {
            abort(403, 'غير مصرح لك بحذف تعليقك.');
        }

        $comment->delete();

        return back()->with('success', 'تم حذف التعليق بنجاح.');
    }
}