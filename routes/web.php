<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleUpgradeRequestController;
use App\Http\Controllers\Admin\RoleUpgradeReviewController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

// ======================
// المسارات العامة
// ======================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ======================
// المتجر (عرض المنتجات المنشورة - للجميع)
// ======================
Route::prefix('shop')->name('shop.')->group(function () {
    Route::get('/', [ShopController::class, 'index'])->name('index');
    Route::get('/{product:slug}', [ShopController::class, 'show'])->name('show');

    // ✅ تفاعلات المتجر (تعليقات وإعجابات) - تتطلب تسجيل الدخول
    Route::middleware(['auth'])->group(function () {
        Route::post('/{product:slug}/comment', [CommentController::class, 'storeProduct'])->name('comment.store');
        Route::post('/{product:slug}/like', [LikeController::class, 'toggleProduct'])->name('like.toggle');
    });

    // ✅ حذف تعليق (يعمل للمحتوى والمنتجات)
    Route::middleware(['auth'])->delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
});

// ======================
// لوحة التحكم
// ======================
Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ======================
// اختبارات الأقسام (مؤقتة)
// ======================
Route::get('/test-section-content', function () {
    return 'دخول قسم المحتوى ناجح';
})->middleware(['auth', 'section:content']);

Route::get('/test-section-marketing', function () {
    return 'دخول قسم التسويق ناجح';
})->middleware(['auth', 'section:marketing']);

// ======================
// طلبات الترقية (للمستخدمين)
// ======================
Route::middleware('auth')->group(function () {
    Route::get('/role-upgrade', [RoleUpgradeRequestController::class, 'create'])
        ->name('role-upgrade.create');
    Route::post('/role-upgrade', [RoleUpgradeRequestController::class, 'store'])
        ->name('role-upgrade.store');
});

// ======================
// مراجعة طلبات الترقية (للمديرين)
// ======================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/role-upgrade-requests', [RoleUpgradeReviewController::class, 'index'])
        ->name('role-upgrade.index');
    Route::post('/role-upgrade-requests/{roleUpgradeRequest}/approve', [RoleUpgradeReviewController::class, 'approve'])
        ->name('role-upgrade.approve');
    Route::post('/role-upgrade-requests/{roleUpgradeRequest}/reject', [RoleUpgradeReviewController::class, 'reject'])
        ->name('role-upgrade.reject');
});

// ======================
// تبديل اللغة
// ======================
Route::get('/locale/{locale}', function (string $locale) {
    if (!in_array($locale, ['ar', 'en'], true)) {
        abort(400);
    }
    session(['locale' => $locale]);
    return back();
})->name('locale.switch');

// ======================
// مسارات المحتوى (للمؤلفين)
// ======================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('content')->name('content.')->group(function () {
        Route::get('/', [ContentController::class, 'index'])->name('index');
        Route::get('/create', [ContentController::class, 'create'])->name('create');
        Route::post('/', [ContentController::class, 'store'])->name('store');
        Route::get('/{content}', [ContentController::class, 'show'])->name('show');
        Route::get('/{content}/edit', [ContentController::class, 'edit'])->name('edit');
        Route::put('/{content}', [ContentController::class, 'update'])->name('update');
        Route::delete('/{content}', [ContentController::class, 'destroy'])->name('destroy');
        Route::post('/{content}/submit', [ContentController::class, 'submit'])->name('submit');

        // التعليقات والإعجابات على المحتوى
        Route::post('/{content}/comment', [CommentController::class, 'store'])->name('comment.store');
        Route::delete('/{content}/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
        Route::post('/{content}/like', [LikeController::class, 'toggle'])->name('like.toggle');
    });
});

// ======================
// مسارات مراجعة المحتوى (للمدققين والمديرين)
// ======================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('review')->name('review.')->group(function () {
        Route::get('/', [ReviewController::class, 'index'])->name('index');
        Route::get('/preview/{content}', [ReviewController::class, 'preview'])->name('preview');
        Route::post('/approve/{content}', [ReviewController::class, 'approve'])->name('approve');
        Route::post('/reject/{content}', [ReviewController::class, 'reject'])->name('reject');
        Route::get('/history', [ReviewController::class, 'history'])->name('history');
    });
});

// ======================
// مسارات المنتجات التسويقية (للمتخصصين والمديرين)
// ======================
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('products')->name('products.')->group(function () {

        // ⚠️ المسارات الثابتة أولاً (قبل {product})
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');

        // مراجعة المنتجات (ثابتة)
        Route::prefix('review')->name('review.')->group(function () {
            Route::get('/', [ProductReviewController::class, 'index'])->name('index');
            Route::get('/history', [ProductReviewController::class, 'history'])->name('history');
            Route::get('/{product}/preview', [ProductReviewController::class, 'preview'])->name('preview');
            Route::post('/{product}/approve', [ProductReviewController::class, 'approve'])->name('approve');
            Route::post('/{product}/reject', [ProductReviewController::class, 'reject'])->name('reject');
        });

        // إدارة التصنيفات (ثابتة)
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
            Route::get('/create', [ProductCategoryController::class, 'create'])->name('create');
            Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [ProductCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [ProductCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [ProductCategoryController::class, 'destroy'])->name('destroy');
        });

        // ⚠️ المسارات الديناميكية في النهاية (لتفادي التعارض)
        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/submit', [ProductController::class, 'submit'])->name('submit');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    });
});





// ======================
// الإشعارات (للمستخدمين المسجلين)
// ======================
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [App\Http\Controllers\NotificationController::class, 'index'])->name('index');
    Route::get('/{id}/read', [App\Http\Controllers\NotificationController::class, 'read'])->name('read');
    Route::post('/{id}/mark-as-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::post('/mark-all-as-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
    Route::delete('/{id}', [App\Http\Controllers\NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/', [App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('destroyAll');
    Route::get('/api/list', [App\Http\Controllers\NotificationController::class, 'api'])->name('api');
});



// ======================
// المقالات (صفحات عامة للجميع)
// ======================
Route::prefix('articles')->name('articles.')->group(function () {
    Route::get('/', [App\Http\Controllers\PublicContentController::class, 'index'])->name('index');
    Route::get('/{content:slug}', [App\Http\Controllers\PublicContentController::class, 'show'])->name('show');

    // ✅ التفاعلات (تعليقات وإعجابات) - تتطلب تسجيل الدخول
    Route::middleware(['auth'])->group(function () {
        Route::post('/{content:slug}/comment', [App\Http\Controllers\CommentController::class, 'store'])->name('comment.store');
        Route::post('/{content:slug}/like', [App\Http\Controllers\LikeController::class, 'toggle'])->name('like.toggle');
    });
});








// ======================
// الصفحات الثابتة
// ======================
Route::prefix('pages')->name('pages.')->group(function () {
    Route::get('/about', [App\Http\Controllers\PageController::class, 'about'])->name('about');
    Route::get('/contact', [App\Http\Controllers\PageController::class, 'contact'])->name('contact');
    Route::post('/contact', [App\Http\Controllers\PageController::class, 'submitContact'])->name('contact.submit');
    Route::get('/privacy', [App\Http\Controllers\PageController::class, 'privacy'])->name('privacy');
    Route::get('/terms', [App\Http\Controllers\PageController::class, 'terms'])->name('terms');
});


Route::get('/sitemap.xml', [App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');
// ======================
// مصادقة Breeze
// ======================
require __DIR__.'/auth.php';