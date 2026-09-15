<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\UrlGenerator;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(UrlGenerator $url): void
    {
        // فرض HTTPS في الإنتاج (مطلوب لـ Render)
        if (config('app.env') === 'production') {
            $url->forceScheme('https');
        }

        // ✅ Gate للمحاسبة (يعتمد على Spatie)
        Gate::define('view-accounting', function (User $user) {
            return $user->hasPermissionTo('view_accounting');
        });
    }
}