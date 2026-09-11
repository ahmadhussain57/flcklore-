<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Product;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    /**
     * تبديل الإعجاب على محتوى
     */
    public function toggle(Content $content)
    {
        return $this->toggleLike(
            $content,
            Content::STATUS_PUBLISHED,
            'content'
        );
    }

    /**
     * تبديل الإعجاب على منتج
     */
    public function toggleProduct(Product $product)
    {
        return $this->toggleLike(
            $product,
            Product::STATUS_PUBLISHED,
            'product'
        );
    }

    /**
     * دالة موحدة لتبديل الإعجاب (تعمل مع أي موديل)
     */
    private function toggleLike($model, string $requiredStatus, string $type)
    {
        // التحقق من الصلاحية
        if (!Auth::user()->hasPermissionTo('like_content', 'web')) {
            abort(403, 'غير مصرح لك بالإعجاب.');
        }

        // التحقق من أن العنصر منشور
        if ($model->status !== $requiredStatus) {
            $message = $type === 'product'
                ? 'لا يمكنك الإعجاب بمنتج غير منشور.'
                : 'لا يمكنك الإعجاب بمحتوى غير منشور.';
            return back()->with('error', $message);
        }

        $modelClass = get_class($model);

        // البحث عن إعجاب موجود
        $existingLike = Like::where('user_id', Auth::id())
            ->where('likeable_type', $modelClass)
            ->where('likeable_id', $model->id)
            ->first();

        if ($existingLike) {
            // إلغاء الإعجاب (Unlike)
            $existingLike->delete();
            $message = 'تم إلغاء الإعجاب.';
        } else {
            // إضافة إعجاب
            Like::create([
                'user_id' => Auth::id(),
                'likeable_type' => $modelClass,
                'likeable_id' => $model->id,
            ]);
            $message = 'تم الإعجاب!';
        }

        return back()->with('success', $message);
    }
}