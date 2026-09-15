<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-2">
                        <span class="text-2xl">🏺</span>
                        <span class="font-bold text-lg text-gray-800 dark:text-gray-200 hidden md:block">Folklore</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:-my-px sm:ms-6 lg:ms-10 sm:flex items-center">
                    @auth
                        @php
                            $user = auth()->user();
                            $isContentStaff = $user->hasAnyRole(['content_admin', 'content_Reviewer', 'content_author']);
                            $isMarketingStaff = $user->hasAnyRole(['marketing_admin', 'marketing_Specialist', 'marketing_Accountant']);
                            $isContentReviewer = $user->hasAnyRole(['content_Reviewer', 'content_admin']);
                            $isMarketingReviewer = $user->hasRole('marketing_admin');
                            $isAdmin = $user->hasAnyRole(['content_admin', 'marketing_admin']);
                            $canManageOrders = $user->hasAnyRole(['marketing_admin', 'marketing_Accountant']);

                            // ✅ عدّادات التعليقات المعلقة
                            $pendingContentComments = $isContentReviewer
                                ? \App\Models\Comment::where('commentable_type', \App\Models\Content::class)
                                    ->where('status', \App\Models\Comment::STATUS_PENDING)
                                    ->count()
                                : 0;

                            $pendingProductComments = $isMarketingReviewer
                                ? \App\Models\Comment::where('commentable_type', \App\Models\Product::class)
                                    ->where('status', \App\Models\Comment::STATUS_PENDING)
                                    ->count()
                                : 0;
                        @endphp

                        {{-- الرئيسية --}}
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="text-sm">
                            🏠 {{ __('الرئيسية') }}
                        </x-nav-link>

                        {{-- المقالات --}}
                        <x-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')" class="text-sm">
                            📚 {{ __('المقالات') }}
                        </x-nav-link>

                        {{-- المتجر --}}
                        <x-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.*')" class="text-sm">
                            🛍️ {{ __('المتجر') }}
                        </x-nav-link>

                        {{-- لوحة التحكم --}}
                        @if($isContentStaff || $isMarketingStaff)
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-sm">
                                📊 {{ __('لوحة التحكم') }}
                            </x-nav-link>
                        @endif

                        {{-- محتوياتي --}}
                        @if($isContentStaff)
                            <x-nav-link :href="route('content.index')" :active="request()->routeIs('content.index') || request()->routeIs('content.create') || request()->routeIs('content.edit') || request()->routeIs('content.show')" class="text-sm">
                                📝 {{ __('محتوياتي') }}
                            </x-nav-link>
                        @endif

                        {{-- مراجعة المحتوى --}}
                        @if($isContentReviewer)
                            <x-nav-link :href="route('review.index')" :active="request()->routeIs('review.*')" class="text-sm">
                                ✅ {{ __('مراجعة المحتوى') }}
                            </x-nav-link>
                        @endif

                        {{-- منتجاتي --}}
                        @if($isMarketingStaff)
                            <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.index') || request()->routeIs('products.create') || request()->routeIs('products.edit') || request()->routeIs('products.show')" class="text-sm">
                                📦 {{ __('منتجاتي') }}
                            </x-nav-link>
                        @endif

                        {{-- مراجعة المنتجات --}}
                        @if($isMarketingReviewer)
                            <x-nav-link :href="route('products.review.index')" :active="request()->routeIs('products.review.*')" class="text-sm">
                                🔍 {{ __('مراجعة المنتجات') }}
                            </x-nav-link>
                        @endif

                        {{-- التصنيفات --}}
                        @if($isMarketingReviewer)
                            <x-nav-link :href="route('products.categories.index')" :active="request()->routeIs('products.categories.*')" class="text-sm">
                                🏷️ {{ __('التصنيفات') }}
                            </x-nav-link>
                        @endif

                        {{-- ✅ مراجعة تعليقات المحتوى --}}
                        @if($isContentReviewer)
                            <x-nav-link :href="route('admin.comments.content.index')"
                                        :active="request()->routeIs('admin.comments.content.*') || request()->routeIs('admin.comments.show')"
                                        class="text-sm">
                                💬 {{ __('تعليقات المحتوى') }}
                                @if($pendingContentComments > 0)
                                    <span class="ms-1 px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                        {{ $pendingContentComments > 99 ? '99+' : $pendingContentComments }}
                                    </span>
                                @endif
                            </x-nav-link>
                        @endif

                        {{-- ✅ مراجعة تعليقات المنتجات --}}
                        @if($isMarketingReviewer)
                            <x-nav-link :href="route('admin.comments.product.index')"
                                        :active="request()->routeIs('admin.comments.product.*')"
                                        class="text-sm">
                                💬 {{ __('تعليقات المنتجات') }}
                                @if($pendingProductComments > 0)
                                    <span class="ms-1 px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                        {{ $pendingProductComments > 99 ? '99+' : $pendingProductComments }}
                                    </span>
                                @endif
                            </x-nav-link>
                        @endif

                        {{-- ✅ قائمة المحاسبة المنسدلة --}}
                        @if($canManageOrders)
                            <div class="relative" x-data="{ accountOpen: false }" @click.away="accountOpen = false">
                                <button @click="accountOpen = !accountOpen"
                                        class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition
                                               {{ request()->routeIs('accounting.*') || request()->routeIs('admin.orders.*') ? 'border-indigo-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}">
                                    📊 {{ __('المحاسبة') }}
                                    <svg class="ms-1 h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                </button>

                                <div x-show="accountOpen"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute left-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                                     style="display: none;">
                                    <div class="py-1">
                                        <a href="{{ route('admin.orders.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('admin.orders.*') ? 'bg-gray-50 dark:bg-gray-700/50' : '' }}">
                                            📦 {{ __('الطلبات') }}
                                        </a>
                                        <a href="{{ route('accounting.journal-entries.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('accounting.journal-entries.*') ? 'bg-gray-50 dark:bg-gray-700/50' : '' }}">
                                            📒 {{ __('سندات القيد') }}
                                        </a>
                                        <a href="{{ route('accounting.invoices.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('accounting.invoices.*') ? 'bg-gray-50 dark:bg-gray-700/50' : '' }}">
                                            🧾 {{ __('الفواتير') }}
                                        </a>
                                        <a href="{{ route('accounting.accounts.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('accounting.accounts.*') ? 'bg-gray-50 dark:bg-gray-700/50' : '' }}">
                                            💰 {{ __('أرصدة الحسابات') }}
                                        </a>
                                        <a href="{{ route('accounting.reports.index') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition {{ request()->routeIs('accounting.reports.*') ? 'bg-gray-50 dark:bg-gray-700/50' : '' }}">
                                            📊 {{ __('التقارير') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- طلبات الترقية --}}
                        @if($isAdmin)
                            <x-nav-link :href="route('admin.role-upgrade.index')" :active="request()->routeIs('admin.role-upgrade.*')" class="text-sm">
                                ⬆️ {{ __('الترقيات') }}
                            </x-nav-link>
                        @endif
                    @else
                        {{-- للزوار غير المسجلين --}}
                        <x-nav-link :href="route('home')" :active="request()->routeIs('home')" class="text-sm">
                            🏠 {{ __('الرئيسية') }}
                        </x-nav-link>
                        <x-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')" class="text-sm">
                            📚 {{ __('المقالات') }}
                        </x-nav-link>
                        <x-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.*')" class="text-sm">
                            🛍️ {{ __('المتجر') }}
                        </x-nav-link>
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-2">

                {{-- Dark Mode Toggle --}}
                <button
                    x-data="{
                        dark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.dark = !this.dark;
                            document.documentElement.classList.toggle('dark', this.dark);
                            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                        }
                    }"
                    @click="toggle()"
                    class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                    title="{{ __('الوضع الداكن/الفاتح') }}"
                >
                    <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                    <svg x-show="dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </button>

                {{-- Language Switcher --}}
                <x-dropdown align="right" width="40">
                    <x-slot name="trigger">
                        <button class="p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition flex items-center gap-1" title="{{ __('اللغة') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                            </svg>
                            <span class="text-xs font-bold uppercase">{{ app()->getLocale() }}</span>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('locale.switch', 'ar')">
                            🇸🇾 {{ __('العربية') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('locale.switch', 'en')">
                            🇬🇧 English
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>

                @auth
                    @php
                        $navCart = auth()->user()->cart;
                        $navCartCount = $navCart ? $navCart->total_items : 0;
                        $unreadMessages = auth()->user()->unreadMessagesCount();
                    @endphp

                    {{-- 🛒 أيقونة السلة --}}
                    <a href="{{ route('cart.index') }}"
                       class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                       title="{{ __('السلة') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        @if($navCartCount > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-indigo-600 rounded-full transform translate-x-1/4 -translate-y-1/4">
                                {{ $navCartCount > 99 ? '99+' : $navCartCount }}
                            </span>
                        @endif
                    </a>

                    {{-- 💬 أيقونة الرسائل --}}
                    <a href="{{ route('messages.index') }}"
                       class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                       title="{{ __('الرسائل') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        @if($unreadMessages > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full transform translate-x-1/4 -translate-y-1/4">
                                {{ $unreadMessages > 99 ? '99+' : $unreadMessages }}
                            </span>
                        @endif
                    </a>
                @endauth

                {{-- 🔔 أيقونة الإشعارات --}}
                @auth
                    <div class="relative" x-data="notificationBell()" x-init="init()">
                        <button @click="open = !open"
                                class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition"
                                title="{{ __('الإشعارات') }}">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full transform translate-x-1/4 -translate-y-1/4"
                                      x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                            </template>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                             style="display: none;">

                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <h3 class="font-bold text-sm text-gray-800 dark:text-gray-200">
                                    🔔 {{ __('الإشعارات') }}
                                    <span x-show="unreadCount > 0" class="text-xs text-red-500" x-text="`(${unreadCount})`"></span>
                                </h3>
                                <button @click="markAllAsRead()"
                                        x-show="unreadCount > 0"
                                        class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('تعليم الكل كمقروء') }}
                                </button>
                            </div>

                            <div class="max-h-96 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        <div class="text-4xl mb-2">🔕</div>
                                        {{ __('لا توجد إشعارات حالياً') }}
                                    </div>
                                </template>

                                <template x-for="notif in notifications" :key="notif.id">
                                    <a :href="notif.url"
                                       class="block px-4 py-3 border-b border-gray-100 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                                       :class="{ 'bg-indigo-50/50 dark:bg-indigo-900/10': !notif.read }">
                                        <div class="flex items-start gap-3">
                                            <div class="flex-shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-lg"
                                                 :class="{
                                                     'bg-green-100 dark:bg-green-900/30': notif.color === 'success',
                                                     'bg-red-100 dark:bg-red-900/30': notif.color === 'danger',
                                                     'bg-yellow-100 dark:bg-yellow-900/30': notif.color === 'warning',
                                                     'bg-blue-100 dark:bg-blue-900/30': notif.color === 'info'
                                                 }">
                                                <span x-text="notif.icon"></span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate" x-text="notif.title"></p>
                                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5 line-clamp-2" x-text="notif.message"></p>
                                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1" x-text="notif.created_at"></p>
                                            </div>
                                            <template x-if="!notif.read">
                                                <span class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0 mt-2"></span>
                                            </template>
                                        </div>
                                    </a>
                                </template>
                            </div>

                            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 text-center">
                                <a href="{{ route('notifications.index') }}"
                                   class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                    {{ __('عرض كل الإشعارات') }} →
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth

                @auth
                    {{-- User Dropdown --}}
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold text-xs">
                                        {{ mb_substr(Auth::user()->name, 0, 2) }}
                                    </div>
                                    <span class="hidden lg:block">{{ Auth::user()->name }}</span>
                                </div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Auth::user()->email }}</p>
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach(Auth::user()->getRoleNames() as $role)
                                        <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-300">
                                            {{ $role }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <x-dropdown-link :href="route('profile.edit')">
                                👤 {{ __('الملف الشخصي') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('cart.index')">
                                🛒 {{ __('السلة') }}
                                @if($navCartCount > 0)
                                    <span class="ms-2 px-2 py-0.5 text-xs font-bold text-white bg-indigo-600 rounded-full">
                                        {{ $navCartCount }}
                                    </span>
                                @endif
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('orders.index')">
                                📦 {{ __('طلباتي') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('messages.index')">
                                💬 {{ __('الرسائل') }}
                                @if($unreadMessages > 0)
                                    <span class="ms-2 px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                        {{ $unreadMessages }}
                                    </span>
                                @endif
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('role-upgrade.create')">
                                ⬆️ {{ __('طلب ترقية') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    🚪 {{ __('تسجيل الخروج') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    {{-- Guest Links (Desktop) --}}
                    <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 transition">
                        {{ __('تسجيل الدخول') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register', ['redirect_to' => url()->current()]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-full transition">
                            {{ __('إنشاء حساب') }}
                        </a>
                    @endif
                @endauth
            </div>

            <!-- Hamburger (Mobile) -->
            <div class="-me-2 flex items-center sm:hidden gap-2">
                @auth
                    @if($navCartCount > 0)
                        <a href="{{ route('cart.index') }}"
                           class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-indigo-600 rounded-full transform translate-x-1/4 -translate-y-1/4">
                                {{ $navCartCount > 99 ? '99+' : $navCartCount }}
                            </span>
                        </a>
                    @endif

                    <div class="relative" x-data="notificationBell()" x-init="init()">
                        <button @click="open = !open"
                                class="relative p-2 rounded-full text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <template x-if="unreadCount > 0">
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-500 rounded-full transform translate-x-1/4 -translate-y-1/4"
                                      x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
                            </template>
                        </button>

                        <div x-show="open"
                             @click.away="open = false"
                             class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-xl shadow-2xl border border-gray-200 dark:border-gray-700 z-50 overflow-hidden"
                             style="display: none;">
                            <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                <h3 class="font-bold text-sm text-gray-800 dark:text-gray-200">
                                    🔔 {{ __('الإشعارات') }}
                                </h3>
                                <a href="{{ route('notifications.index') }}" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ __('عرض الكل') }}
                                </a>
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                        <div class="text-4xl mb-2">🔕</div>
                                        {{ __('لا توجد إشعارات') }}
                                    </div>
                                </template>
                                <template x-for="notif in notifications" :key="notif.id">
                                    <a :href="notif.url"
                                       class="block px-4 py-3 border-b border-gray-100 dark:border-gray-700/50"
                                       :class="{ 'bg-indigo-50/50 dark:bg-indigo-900/10': !notif.read }">
                                        <div class="flex items-start gap-2">
                                            <span class="text-lg" x-text="notif.icon"></span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-gray-800 dark:text-gray-200 truncate" x-text="notif.title"></p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1" x-text="notif.message"></p>
                                            </div>
                                        </div>
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                @endauth

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu (Mobile) -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @auth
                @php
                    $user = auth()->user();
                    $isContentStaff = $user->hasAnyRole(['content_admin', 'content_Reviewer', 'content_author']);
                    $isMarketingStaff = $user->hasAnyRole(['marketing_admin', 'marketing_Specialist', 'marketing_Accountant']);
                    $isContentReviewer = $user->hasAnyRole(['content_Reviewer', 'content_admin']);
                    $isMarketingReviewer = $user->hasRole('marketing_admin');
                    $isAdmin = $user->hasAnyRole(['content_admin', 'marketing_admin']);
                    $canManageOrders = $user->hasAnyRole(['marketing_admin', 'marketing_Accountant']);

                    // ✅ عدّادات التعليقات المعلقة (للموبايل)
                    $pendingContentComments = $isContentReviewer
                        ? \App\Models\Comment::where('commentable_type', \App\Models\Content::class)
                            ->where('status', \App\Models\Comment::STATUS_PENDING)
                            ->count()
                        : 0;

                    $pendingProductComments = $isMarketingReviewer
                        ? \App\Models\Comment::where('commentable_type', \App\Models\Product::class)
                            ->where('status', \App\Models\Comment::STATUS_PENDING)
                            ->count()
                        : 0;
                @endphp

                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    🏠 {{ __('الرئيسية') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                    📚 {{ __('المقالات') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.*')">
                    🛍️ {{ __('المتجر') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('cart.index')" :active="request()->routeIs('cart.*')">
                    🛒 {{ __('السلة') }} @if($navCartCount > 0) ({{ $navCartCount }}) @endif
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                    📦 {{ __('طلباتي') }}
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                    💬 {{ __('الرسائل') }} @if($unreadMessages > 0) ({{ $unreadMessages }}) @endif
                </x-responsive-nav-link>

                <x-responsive-nav-link :href="route('notifications.index')" :active="request()->routeIs('notifications.*')">
                    🔔 {{ __('الإشعارات') }}
                </x-responsive-nav-link>

                @if($isContentStaff || $isMarketingStaff)
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        📊 {{ __('لوحة التحكم') }}
                    </x-responsive-nav-link>
                @endif

                @if($isContentStaff)
                    <x-responsive-nav-link :href="route('content.index')" :active="request()->routeIs('content.*')">
                        📝 {{ __('محتوياتي') }}
                    </x-responsive-nav-link>
                @endif

                @if($isContentReviewer)
                    <x-responsive-nav-link :href="route('review.index')" :active="request()->routeIs('review.*')">
                        ✅ {{ __('مراجعة المحتوى') }}
                    </x-responsive-nav-link>
                @endif

                @if($isMarketingStaff)
                    <x-responsive-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                        📦 {{ __('منتجاتي') }}
                    </x-responsive-nav-link>
                @endif

                @if($isMarketingReviewer)
                    <x-responsive-nav-link :href="route('products.review.index')" :active="request()->routeIs('products.review.*')">
                        🔍 {{ __('مراجعة المنتجات') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('products.categories.index')" :active="request()->routeIs('products.categories.*')">
                        🏷️ {{ __('التصنيفات') }}
                    </x-responsive-nav-link>
                @endif

                {{-- ✅ مراجعة تعليقات المحتوى (موبايل) --}}
                @if($isContentReviewer)
                    <x-responsive-nav-link :href="route('admin.comments.content.index')"
                                           :active="request()->routeIs('admin.comments.content.*') || request()->routeIs('admin.comments.show')">
                        💬 {{ __('تعليقات المحتوى') }}
                        @if($pendingContentComments > 0)
                            <span class="ms-1 px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $pendingContentComments > 99 ? '99+' : $pendingContentComments }}
                            </span>
                        @endif
                    </x-responsive-nav-link>
                @endif

                {{-- ✅ مراجعة تعليقات المنتجات (موبايل) --}}
                @if($isMarketingReviewer)
                    <x-responsive-nav-link :href="route('admin.comments.product.index')"
                                           :active="request()->routeIs('admin.comments.product.*')">
                        💬 {{ __('تعليقات المنتجات') }}
                        @if($pendingProductComments > 0)
                            <span class="ms-1 px-1.5 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                                {{ $pendingProductComments > 99 ? '99+' : $pendingProductComments }}
                            </span>
                        @endif
                    </x-responsive-nav-link>
                @endif

                {{-- ✅ قسم المحاسبة (جوال) --}}
                @if($canManageOrders)
                    <div class="pt-4 pb-2 border-t border-gray-200 dark:border-gray-600">
                        <div class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                            📊 {{ __('المحاسبة') }}
                        </div>

                        <x-responsive-nav-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                            📦 {{ __('الطلبات') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('accounting.journal-entries.index')" :active="request()->routeIs('accounting.journal-entries.*')">
                            📒 {{ __('سندات القيد') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('accounting.invoices.index')" :active="request()->routeIs('accounting.invoices.*')">
                            🧾 {{ __('الفواتير') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('accounting.accounts.index')" :active="request()->routeIs('accounting.accounts.*')">
                            💰 {{ __('أرصدة الحسابات') }}
                        </x-responsive-nav-link>

                        <x-responsive-nav-link :href="route('accounting.reports.index')" :active="request()->routeIs('accounting.reports.*')">
                            📊 {{ __('التقارير') }}
                        </x-responsive-nav-link>
                    </div>
                @endif

                @if($isAdmin)
                    <x-responsive-nav-link :href="route('admin.role-upgrade.index')" :active="request()->routeIs('admin.role-upgrade.*')">
                        ⬆️ {{ __('طلبات الترقية') }}
                    </x-responsive-nav-link>
                @endif
            @else
                <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                    🏠 {{ __('الرئيسية') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('articles.index')" :active="request()->routeIs('articles.*')">
                    📚 {{ __('المقالات') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('shop.index')" :active="request()->routeIs('shop.*')">
                    🛍️ {{ __('المتجر') }}
                </x-responsive-nav-link>
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        👤 {{ __('الملف الشخصي') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('role-upgrade.create')">
                        ⬆️ {{ __('طلب ترقية') }}
                    </x-responsive-nav-link>

                    <button
                        x-data="{
                            dark: document.documentElement.classList.contains('dark'),
                            toggle() {
                                this.dark = !this.dark;
                                document.documentElement.classList.toggle('dark', this.dark);
                                localStorage.setItem('theme', this.dark ? 'dark' : 'light');
                            }
                        }"
                        @click="toggle()"
                        class="block w-full text-start px-4 py-2 text-base font-medium text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                    >
                        <span x-show="!dark">🌙 {{ __('الوضع الداكن') }}</span>
                        <span x-show="dark">☀️ {{ __('الوضع الفاتح') }}</span>
                    </button>

                    <a href="{{ route('locale.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
                       class="block w-full text-start px-4 py-2 text-base font-medium text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        🌐 {{ app()->getLocale() === 'ar' ? 'English' : 'العربية' }}
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            🚪 {{ __('تسجيل الخروج') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                {{-- Guest Links (Mobile) --}}
                <div class="mt-3 space-y-1 px-4">
                    <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="block py-2 text-base font-medium text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200">
                        {{ __('تسجيل الدخول') }}
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register', ['redirect_to' => url()->current()]) }}" class="block py-2 text-base font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200">
                            {{ __('إنشاء حساب') }}
                        </a>
                    @endif
                </div>
            @endauth
        </div>
    </div>
</nav>