<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleUpgradeRequestController;
use App\Http\Controllers\Admin\RoleUpgradeReviewController;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/test-section-content', function () {
    return 'دخول قسم المحتوى ناجح';
})->middleware(['auth', 'section:content']);

Route::get('/test-section-marketing', function () {
    return 'دخول قسم التسويق ناجح';
})->middleware(['auth', 'section:marketing']);


Route::middleware('auth')->group(function () {
    Route::get('/role-upgrade', [RoleUpgradeRequestController::class, 'create'])
        ->name('role-upgrade.create');
    Route::post('/role-upgrade', [RoleUpgradeRequestController::class, 'store'])
        ->name('role-upgrade.store');
});

// مراجعة المدير
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/role-upgrade-requests', [RoleUpgradeReviewController::class, 'index'])
        ->name('role-upgrade.index');
    Route::post('/role-upgrade-requests/{roleUpgradeRequest}/approve', [RoleUpgradeReviewController::class, 'approve'])
        ->name('role-upgrade.approve');
    Route::post('/role-upgrade-requests/{roleUpgradeRequest}/reject', [RoleUpgradeReviewController::class, 'reject'])
        ->name('role-upgrade.reject');
});


Route::get('/locale/{locale}', function (string $locale) {
    if (! in_array($locale, ['ar', 'en'], true)) {
        abort(400);
    }
    session(['locale' => $locale]);
    return back();
})->name('locale.switch');

require __DIR__.'/auth.php';