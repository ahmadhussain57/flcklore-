<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(UrlGenerator $url): void
    {
        // ✅ فرض HTTPS في الإنتاج (مطلوب لـ Render)
        if (config('app.env') === 'production') {
            $url->forceScheme('https');
        }
    }
}