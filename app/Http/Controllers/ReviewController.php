<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\User;
use App\Notifications\ContentApproved;
use App\Notifications\ContentRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * عرض قائمة الانتظار مع إحصائيات
     */
    public function index()
    {
        $pendingContents = Content::with(['author', 'categories', 'tags'])
            ->whereIn('status', [Content::STATUS_PENDING_REVIEW, Content::STATUS_PENDING_EDIT])
            ->orderBy('queued_at', 'asc')
            ->paginate(15);

        $stats = [
            'total' => Content::whereIn('status', [Content::STATUS_PENDING_REVIEW, Content::STATUS_PENDING_EDIT])->count(),
            'reviewed_today' => Content::where('reviewed_by', Auth::id())
                ->whereDate('reviewed_at', today())
                ->count(),
            'my_history' => Content::where('reviewed_by', Auth::id())
                ->whereNotNull('reviewed_at')
                ->count(),
        ];

        return view('review.index', compact('pendingContents', 'stats'));
    }

    /**
     * عرض صفحة المعاينة مع أزرار القبول/الرفض
     */
    public function preview(Content $content)
    {
        if (!$content->isInQueue()) {
            return redirect()->route('review.index')
                ->with('error', 'هذا المحتوى ليس في قائمة الانتظار.');
        }

        if ($content->isLocked() && !$content->isLockedByCurrentUser()) {
            return redirect()->route('review.index')
                ->with('error', 'هذا المحتوى محجوز من قبل مدقق آخر.');
        }

        if (!$content->locked_by) {
            $content->update([
                'locked_by' => Auth::id(),
                'locked_at' => now(),
            ]);
            $content->refresh();
        }

        $content->load(['author', 'categories', 'tags', 'media']);

        return view('review.preview', compact('content'));
    }

    /**
     * الموافقة على المحتوى ونشره
     */
    public function approve(Content $content)
    {
        if (!Auth::user()->hasPermissionTo('approve_content', 'web')) {
            abort(403, 'غير مصرح لك بالموافقة على المحتوى.');
        }

        if (!$content->isInQueue()) {
            return redirect()->route('review.index')
                ->with('error', 'هذا المحتوى ليس في قائمة الانتظار.');
        }

        if (!$content->isLockedByCurrentUser()) {
            return redirect()->route('review.index')
                ->with('error', 'لا يمكنك الموافقة على محتوى لم تحجزه.');
        }

        // ✅ حفظ المؤلف قبل النشر (لأن النسخة قد تُحذف)
        $author = $content->author;

        // نشر المحتوى (يُرجع المحتوى النهائي بعد النشر)
        $publishedContent = $this->publishContent($content);

        // ✅ إرسال إشعار للمؤلف
        $author->notify(new ContentApproved($publishedContent));

        return redirect()->route('review.index')
            ->with('success', '✅ تم نشر المحتوى بنجاح.');
    }

    /**
     * رفض المحتوى مع سبب
     */
    public function reject(Request $request, Content $content)
    {
        if (!Auth::user()->hasPermissionTo('reject_content', 'web')) {
            abort(403, 'غير مصرح لك برفض المحتوى.');
        }

        if (!$content->isInQueue()) {
            return redirect()->route('review.index')
                ->with('error', 'هذا المحتوى ليس في قائمة الانتظار.');
        }

        if (!$content->isLockedByCurrentUser()) {
            return redirect()->route('review.index')
                ->with('error', 'لا يمكنك رفض محتوى لم تحجزه.');
        }

        $request->validate([
            'rejection_reason' => 'required|string|min:5',
        ]);

        $content->update([
            'status' => Content::STATUS_REJECTED,
            'rejection_reason' => $request->rejection_reason,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'locked_by' => null,
            'locked_at' => null,
        ]);

        // ✅ إرسال إشعار للمؤلف مع سبب الرفض
        $content->author->notify(new ContentRejected($content, $request->rejection_reason));

        return redirect()->route('review.index')
            ->with('success', '❌ تم رفض المحتوى بنجاح.');
    }

    /**
     * عرض تاريخ مراجعات المدقق
     */
    public function history()
    {
        $history = Content::with(['author', 'categories', 'tags'])
            ->where('reviewed_by', Auth::id())
            ->whereNotNull('reviewed_at')
            ->orderBy('reviewed_at', 'desc')
            ->paginate(20);

        return view('review.history', compact('history'));
    }

    /**
     * دالة مساعدة لنشر المحتوى
     * تُرجع المحتوى النهائي المنشور (بعد معالجة النسخ pending_edit)
     */
    private function publishContent(Content $content): Content
    {
        // إذا كان المحتوى هو نسخة pending_edit لمنشور سابق
        if ($content->status === Content::STATUS_PENDING_EDIT && $content->original_id) {
            $original = Content::find($content->original_id);
            if ($original) {
                // تحديث المنشور الأصلي ببيانات النسخة الجديدة
                $original->update([
                    'title' => $content->title,
                    'slug' => $content->slug,
                    'type' => $content->type,
                    'body' => $content->body,
                    'keywords' => $content->keywords,
                    'historical_importance' => $content->historical_importance,
                    'geographic_location' => $content->geographic_location,
                    'updated_at' => now(),
                ]);

                // تحديث التصنيفات والأوسمة في المنشور الأصلي
                $original->categories()->sync($content->categories->pluck('id'));
                $original->tags()->sync($content->tags->pluck('id'));

                // حذف النسخة المؤقتة
                $content->delete();

                // نستمر مع المنشور الأصلي
                $content = $original;
            }
        }

        // نشر المحتوى (أصلي أو معدّل)
        $content->update([
            'status' => Content::STATUS_PUBLISHED,
            'published_at' => now(),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'locked_by' => null,
            'locked_at' => null,
            'rejection_reason' => null,
        ]);

        return $content;
    }
}