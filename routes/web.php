<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleUpgradeRequestController;
use App\Http\Controllers\Admin\RoleUpgradeReviewController;
use App\Http\Controllers\Admin\CommentReviewController;
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

    Route::middleware(['auth'])->group(function () {
        Route::post('/{product:slug}/comment', [CommentController::class, 'storeProduct'])->name('comment.store');
        Route::post('/{product:slug}/like', [LikeController::class, 'toggleProduct'])->name('like.toggle');
    });

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
    Route::get('/role-upgrade', [RoleUpgradeRequestController::class, 'create'])->name('role-upgrade.create');
    Route::post('/role-upgrade', [RoleUpgradeRequestController::class, 'store'])->name('role-upgrade.store');
});

// ======================
// مراجعة طلبات الترقية (للمديرين)
// ======================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/role-upgrade-requests', [RoleUpgradeReviewController::class, 'index'])->name('role-upgrade.index');
    Route::post('/role-upgrade-requests/{roleUpgradeRequest}/approve', [RoleUpgradeReviewController::class, 'approve'])->name('role-upgrade.approve');
    Route::post('/role-upgrade-requests/{roleUpgradeRequest}/reject', [RoleUpgradeReviewController::class, 'reject'])->name('role-upgrade.reject');
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
        Route::get('/', [ProductController::class, 'index'])->name('index');
        Route::get('/create', [ProductController::class, 'create'])->name('create');
        Route::post('/', [ProductController::class, 'store'])->name('store');

        Route::prefix('review')->name('review.')->group(function () {
            Route::get('/', [ProductReviewController::class, 'index'])->name('index');
            Route::get('/history', [ProductReviewController::class, 'history'])->name('history');
            Route::get('/{product}/preview', [ProductReviewController::class, 'preview'])->name('preview');
            Route::post('/{product}/approve', [ProductReviewController::class, 'approve'])->name('approve');
            Route::post('/{product}/reject', [ProductReviewController::class, 'reject'])->name('reject');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [ProductCategoryController::class, 'index'])->name('index');
            Route::get('/create', [ProductCategoryController::class, 'create'])->name('create');
            Route::post('/', [ProductCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [ProductCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [ProductCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [ProductCategoryController::class, 'destroy'])->name('destroy');
        });

        Route::get('/{product}/edit', [ProductController::class, 'edit'])->name('edit');
        Route::put('/{product}', [ProductController::class, 'update'])->name('update');
        Route::delete('/{product}', [ProductController::class, 'destroy'])->name('destroy');
        Route::post('/{product}/submit', [ProductController::class, 'submit'])->name('submit');
        Route::get('/{product}', [ProductController::class, 'show'])->name('show');
    });
});

// ======================
// ✅ مسارات مراجعة التعليقات (موحّدة)
// ======================
Route::middleware(['auth', 'verified'])
    ->prefix('admin/comments')
    ->name('admin.comments.')
    ->group(function () {

        // ─── فهارس التعليقات (يجب أن تأتي قبل /{comment}) ───
        Route::get('/content', [CommentReviewController::class, 'indexContent'])
            ->name('content.index');

        Route::get('/product', [CommentReviewController::class, 'indexProduct'])
            ->name('product.index');

        // ─── عرض تعليق واحد ───
        Route::get('/{comment}', [CommentReviewController::class, 'show'])
            ->name('show');

        // ─── الموافقة ───
        Route::patch('/{comment}/approve', [CommentReviewController::class, 'approve'])
            ->name('approve');

        // ─── الرفض ───
        Route::patch('/{comment}/reject', [CommentReviewController::class, 'reject'])
            ->name('reject');

        // ─── الحذف ───
        Route::delete('/{comment}', [CommentReviewController::class, 'destroy'])
            ->name('destroy');
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
// الرسائل (الشات)
// ======================
Route::middleware(['auth'])->prefix('messages')->name('messages.')->group(function () {
    Route::get('/', [App\Http\Controllers\ConversationController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\ConversationController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\ConversationController::class, 'store'])->name('store');
    Route::get('/{conversation}', [App\Http\Controllers\ConversationController::class, 'show'])->name('show');
    Route::post('/{conversation}/reply', [App\Http\Controllers\MessageController::class, 'store'])->name('reply');
    Route::get('/{conversation}/fetch', [App\Http\Controllers\MessageController::class, 'fetch'])->name('fetch');
    Route::post('/{conversation}/read', [App\Http\Controllers\MessageController::class, 'markAsRead'])->name('read');
});

// ======================
// السلة
// ======================
Route::middleware(['auth'])->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [App\Http\Controllers\CartController::class, 'index'])->name('index');
    Route::post('/add/{product}', [App\Http\Controllers\CartController::class, 'add'])->name('add');
    Route::put('/update/{item}', [App\Http\Controllers\CartController::class, 'update'])->name('update');
    Route::delete('/remove/{item}', [App\Http\Controllers\CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('clear');
    Route::get('/count', [App\Http\Controllers\CartController::class, 'count'])->name('count');
});

// ======================
// الطلبات (للعملاء)
// ======================
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [App\Http\Controllers\OrderController::class, 'checkout'])->name('checkout.index');
    Route::post('/checkout', [App\Http\Controllers\OrderController::class, 'store'])->name('checkout.store');

    Route::get('/my-orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders.index');
    Route::get('/my-orders/{order}', [App\Http\Controllers\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/my-orders/{order}/cancel', [App\Http\Controllers\OrderController::class, 'cancel'])->name('orders.cancel');
});

// ======================
// إدارة الطلبات (للمدير + المحاسب)
// ======================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::middleware(['can:view-accounting'])->group(function () {
        Route::get('/orders', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::patch('/orders/{order}/confirm-payment', [App\Http\Controllers\Admin\OrderController::class, 'confirmPayment'])->name('orders.confirmPayment');
        Route::patch('/orders/{order}/cancel', [App\Http\Controllers\Admin\OrderController::class, 'cancel'])->name('orders.cancel');
        Route::patch('/orders/{order}/generate-journal-entry', [App\Http\Controllers\Admin\OrderController::class, 'generateJournalEntry'])->name('orders.generateJournalEntry');
        Route::patch('/orders/{order}/create-invoice', [App\Http\Controllers\Accounting\InvoiceController::class, 'createFromOrder'])->name('orders.createInvoice');
    });
});

// ======================
// المحاسبة (للمحاسب والمدير)
// ======================
Route::middleware(['auth'])->prefix('accounting')->name('accounting.')->group(function () {
    Route::middleware(['can:view-accounting'])->group(function () {
        // سندات القيد
        Route::get('/journal-entries', [App\Http\Controllers\Accounting\JournalEntryController::class, 'index'])->name('journal-entries.index');
        Route::get('/journal-entries/create', [App\Http\Controllers\Accounting\JournalEntryController::class, 'create'])->name('journal-entries.create');
        Route::post('/journal-entries', [App\Http\Controllers\Accounting\JournalEntryController::class, 'store'])->name('journal-entries.store');
        Route::get('/journal-entries/{journalEntry}', [App\Http\Controllers\Accounting\JournalEntryController::class, 'show'])->name('journal-entries.show');
        Route::delete('/journal-entries/{journalEntry}', [App\Http\Controllers\Accounting\JournalEntryController::class, 'destroy'])->name('journal-entries.destroy');

        // الفواتير
        Route::get('/invoices', [App\Http\Controllers\Accounting\InvoiceController::class, 'index'])->name('invoices.index');
        Route::get('/invoices/create', [App\Http\Controllers\Accounting\InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [App\Http\Controllers\Accounting\InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}', [App\Http\Controllers\Accounting\InvoiceController::class, 'show'])->name('invoices.show');
        Route::delete('/invoices/{invoice}', [App\Http\Controllers\Accounting\InvoiceController::class, 'destroy'])->name('invoices.destroy');

        // التقارير
        Route::get('/reports', [App\Http\Controllers\Accounting\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/material-movement', [App\Http\Controllers\Accounting\ReportController::class, 'materialMovement'])->name('reports.material-movement');
        Route::get('/reports/ledger', [App\Http\Controllers\Accounting\ReportController::class, 'ledger'])->name('reports.ledger');
        Route::get('/reports/warehouse', [App\Http\Controllers\Accounting\ReportController::class, 'warehouse'])->name('reports.warehouse');

        // تصدير Excel
        Route::get('/reports/material-movement/excel', [App\Http\Controllers\Accounting\ReportController::class, 'materialMovementExcel'])->name('reports.material-movement.excel');
        Route::get('/reports/ledger/excel', [App\Http\Controllers\Accounting\ReportController::class, 'ledgerExcel'])->name('reports.ledger.excel');
        Route::get('/reports/warehouse/excel', [App\Http\Controllers\Accounting\ReportController::class, 'warehouseExcel'])->name('reports.warehouse.excel');

        // الحسابات
        Route::get('/accounts', [App\Http\Controllers\Accounting\AccountController::class, 'index'])->name('accounts.index');
        Route::get('/accounts/{account}', [App\Http\Controllers\Accounting\AccountController::class, 'show'])->name('accounts.show');
    });
});

// ======================
// مصادقة Breeze
// ======================
require __DIR__.'/auth.php';