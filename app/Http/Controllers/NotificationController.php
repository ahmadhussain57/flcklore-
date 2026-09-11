<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * عرض جميع إشعارات المستخدم (مع ترقيم الصفحات)
     */
    public function index(): View
    {
        $notifications = Auth::user()
            ->notifications()
            ->paginate(20);

        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * تعليم إشعار كمقروء + إعادة توجيه للرابط المرتبط
     * (يُستخدم عند الضغط على الإشعار)
     */
    public function read(string $id): RedirectResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        // تعليمه كمقروء (فقط إذا لم يكن مقروءاً من قبل)
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        // استخراج الرابط من بيانات الإشعار
        $data = $notification->data;
        $url = $data['url'] ?? route('dashboard');

        return redirect($url);
    }

    /**
     * تعليم إشعار كمقروء فقط (بدون إعادة توجيه)
     * يُستخدم من قائمة الإشعارات المنسدلة
     */
    public function markAsRead(string $id): RedirectResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'تم تعليم الإشعار كمقروء.');
    }

    /**
     * تعليم جميع الإشعارات كمقروءة
     */
    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'تم تعليم جميع الإشعارات كمقروءة.');
    }

    /**
     * حذف إشعار واحد
     */
    public function destroy(string $id): RedirectResponse
    {
        $notification = Auth::user()
            ->notifications()
            ->findOrFail($id);

        $notification->delete();

        return back()->with('success', 'تم حذف الإشعار.');
    }

    /**
     * حذف جميع الإشعارات
     */
    public function destroyAll(): RedirectResponse
    {
        Auth::user()->notifications()->delete();

        return back()->with('success', 'تم حذف جميع الإشعارات.');
    }

    /**
     * جلب عدد الإشعارات غير المقروءة + آخر 5 إشعارات (JSON)
     * يُستخدم لتحديث أيقونة الجرس ديناميكياً عبر AJAX
     */
    public function api(): JsonResponse
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->limit(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'title' => $n->data['title'] ?? '',
                    'message' => $n->data['message'] ?? '',
                    'icon' => $n->data['icon'] ?? '🔔',
                    'color' => $n->data['color'] ?? 'info',
                    'url' => route('notifications.read', $n->id),
                    'read' => !is_null($n->read_at),
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'total_count' => $user->notifications()->count(),
            'notifications' => $notifications,
        ]);
    }
}