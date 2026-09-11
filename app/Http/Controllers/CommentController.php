<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Product;
use App\Models\Comment;
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
        ]);

        // التحقق من أن العنصر منشور
        if ($model->status !== $requiredStatus) {
            $message = $type === 'product'
                ? 'لا يمكنك التعليق على منتج غير منشور.'
                : 'لا يمكنك التعليق على محتوى غير منشور.';
            return back()->with('error', $message);
        }

        // إنشاء التعليق
        $comment = Comment::create([
            'user_id' => Auth::id(),
            'body' => $request->body,
            'commentable_type' => get_class($model),
            'commentable_id' => $model->id,
        ]);

        // ✅ إرسال إشعار لصاحب المحتوى/المنتج (إذا لم يكن هو المعلّق نفسه)
        $this->notifyOwner($model, $comment, $type);

        return back()->with('success', 'تم إضافة تعليقك بنجاح!');
    }

    /**
     * إرسال إشعار لصاحب المحتوى/المنتج
     */
    private function notifyOwner($model, Comment $comment, string $type): void
    {
        // جلب صاحب المحتوى/المنتج
        $owner = $model->author;

        // لا نرسل إشعاراً إذا كان صاحب المحتوى هو نفسه من علّق
        if (!$owner || $owner->id === Auth::id()) {
            return;
        }

        // إرسال الإشعار المناسب حسب النوع
        if ($type === 'content') {
            $owner->notify(new NewCommentOnContent($model, $comment));
        } elseif ($type === 'product') {
            $owner->notify(new NewCommentOnProduct($model, $comment));
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