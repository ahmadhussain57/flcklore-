<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Product;
use App\Models\RoleUpgradeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = Auth::user();

        // تحديد الأدوار
        $isContentStaff    = $user->hasAnyRole(['content_admin', 'content_Reviewer', 'content_author']);
        $isMarketingStaff  = $user->hasAnyRole(['marketing_admin', 'marketing_Specialist', 'marketing_Accountant']);
        $isContentReviewer = $user->hasAnyRole(['content_Reviewer', 'content_admin']);
        $isMarketingAdmin  = $user->hasRole('marketing_admin');
        $isAdmin           = $user->hasAnyRole(['content_admin', 'marketing_admin']);
        $isContentAuthor   = $user->hasRole('content_author');

        // ======================
        // إحصائيات المحتوى
        // ======================
        $contentStats = null;
        if ($isContentStaff) {
            $query = $isContentReviewer
                ? Content::query() // المدققون يرون كل المحتوى
                : Content::where('user_id', $user->id); // المؤلفون يرون محتواهم فقط

            $contentStats = [
                'total'     => (clone $query)->count(),
                'published' => (clone $query)->where('status', Content::STATUS_PUBLISHED)->count(),
                'pending'   => (clone $query)->whereIn('status', [Content::STATUS_PENDING_REVIEW, Content::STATUS_PENDING_EDIT])->count(),
                'draft'     => (clone $query)->where('status', Content::STATUS_DRAFT)->count(),
                'rejected'  => (clone $query)->where('status', Content::STATUS_REJECTED)->count(),
            ];
        }

        // ======================
        // إحصائيات المنتجات
        // ======================
        $productStats = null;
        if ($isMarketingStaff) {
            $query = $isMarketingAdmin
                ? Product::query() // المدير يرى كل المنتجات
                : Product::where('user_id', $user->id); // المتخصص يرى منتجاته فقط

            $productStats = [
                'total'     => (clone $query)->count(),
                'published' => (clone $query)->where('status', Product::STATUS_PUBLISHED)->count(),
                'pending'   => (clone $query)->whereIn('status', [Product::STATUS_PENDING_REVIEW, Product::STATUS_PENDING_EDIT])->count(),
                'draft'     => (clone $query)->where('status', Product::STATUS_DRAFT)->count(),
                'rejected'  => (clone $query)->where('status', Product::STATUS_REJECTED)->count(),
            ];
        }

        // ======================
        // آخر محتويات المستخدم
        // ======================
        $recentContents = collect();
        if ($isContentStaff) {
            $recentContents = Content::with(['categories'])
                ->when(!$isContentReviewer, fn($q) => $q->where('user_id', $user->id))
                ->latest()
                ->limit(5)
                ->get();
        }

        // ======================
        // آخر منتجات المستخدم
        // ======================
        $recentProducts = collect();
        if ($isMarketingStaff) {
            $recentProducts = Product::with(['categories', 'media'])
                ->when(!$isMarketingAdmin, fn($q) => $q->where('user_id', $user->id))
                ->latest()
                ->limit(5)
                ->get();
        }

        // ======================
        // الطابور (للمدققين)
        // ======================
        $pendingContentReviews = 0;
        if ($isContentReviewer) {
            $pendingContentReviews = Content::whereIn('status', [Content::STATUS_PENDING_REVIEW, Content::STATUS_PENDING_EDIT])->count();
        }

        $pendingProductReviews = 0;
        if ($isMarketingAdmin) {
            $pendingProductReviews = Product::whereIn('status', [Product::STATUS_PENDING_REVIEW, Product::STATUS_PENDING_EDIT])->count();
        }

        // ======================
        // طلبات الترقية (للمديرين)
        // ======================
        $pendingUpgrades = 0;
        if ($isAdmin) {
            $sections = [];
            if ($user->hasRole('content_admin')) $sections[] = 'content';
            if ($user->hasRole('marketing_admin')) $sections[] = 'marketing';

            $pendingUpgrades = RoleUpgradeRequest::whereIn('section', $sections)
                ->where('status', 'pending')
                ->count();
        }

        // ======================
        // آخر الإشعارات (5)
        // ======================
        $recentNotifications = $user->notifications()->limit(5)->get();

        return view('dashboard', compact(
            'user',
            'isContentStaff',
            'isMarketingStaff',
            'isContentReviewer',
            'isMarketingAdmin',
            'isAdmin',
            'isContentAuthor',
            'contentStats',
            'productStats',
            'recentContents',
            'recentProducts',
            'pendingContentReviews',
            'pendingProductReviews',
            'pendingUpgrades',
            'recentNotifications',
        ));
    }
}