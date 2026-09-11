<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        {{-- ✅ الشبكة الرئيسية --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">

            {{-- 1. عن المنصة --}}
            <div class="lg:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-3xl">🏺</span>
                    <span class="font-bold text-xl text-gray-800 dark:text-gray-200">Folklore</span>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    {{ __('منصة رقمية تجمع التراث والقصص الشعبية والحرف اليدوية من كل مكان، لتحفظ الفلكلور وتنقله للأجيال القادمة.') }}
                </p>
                {{-- Social Media --}}
                <div class="flex items-center gap-3">
                    <a href="#" title="Facebook" class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-blue-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/></svg>
                    </a>
                    <a href="#" title="Twitter / X" class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-black hover:text-white transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" title="Instagram" class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-gradient-to-br hover:from-pink-500 hover:to-purple-500 hover:text-white transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                    </a>
                    <a href="#" title="YouTube" class="w-9 h-9 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 hover:bg-red-600 hover:text-white transition">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 00-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 00.502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 002.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 002.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            {{-- 2. روابط سريعة --}}
            <div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 text-sm uppercase tracking-wider">
                    🔗 {{ __('روابط سريعة') }}
                </h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('home') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            {{ __('الرئيسية') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('articles.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            {{ __('المقالات') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('shop.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            {{ __('المتجر') }}
                        </a>
                    </li>
                    @auth
                        <li>
                            <a href="{{ route('dashboard') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                {{ __('لوحة التحكم') }}
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('register') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                {{ __('إنشاء حساب') }}
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>

            {{-- 3. عن المنصة --}}
            <div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 text-sm uppercase tracking-wider">
                    ℹ️ {{ __('عن المنصة') }}
                </h3>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('pages.about') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            {{ __('من نحن') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.contact') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            {{ __('تواصل معنا') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.privacy') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            {{ __('سياسة الخصوصية') }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('pages.terms') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            {{ __('الشروط والأحكام') }}
                        </a>
                    </li>
                </ul>
            </div>

            {{-- 4. تواصل معنا --}}
            <div>
                <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4 text-sm uppercase tracking-wider">
                    📞 {{ __('تواصل معنا') }}
                </h3>
                <ul class="space-y-3">
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-lg">📧</span>
                        <a href="mailto:info@folklore.com" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            info@folklore.com
                        </a>
                    </li>
                    <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-lg">📱</span>
                        <a href="tel:+963111111111" dir="ltr" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                            +963 111 111 111
                        </a>
                    </li>
                    <li class="flex items-start gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span class="text-lg flex-shrink-0">📍</span>
                        <span>{{ __('دمشق، سوريا') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        {{-- ✅ خط فاصل + شريط سفلي --}}
        <div class="border-t border-gray-200 dark:border-gray-700 pt-6 flex flex-col md:flex-row items-center justify-between gap-4">

            {{-- حقوق النشر --}}
            <div class="text-sm text-gray-500 dark:text-gray-400 text-center md:text-start">
                &copy; {{ date('Y') }} <strong class="text-gray-700 dark:text-gray-300">Folklore</strong>.
                {{ __('جميع الحقوق محفوظة.') }}
            </div>

            {{-- شارات صغيرة --}}
            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="flex items-center gap-1">
                    <span>💚</span>
                    <span>{{ __('صُنع بحب') }}</span>
                </span>
                <span class="hidden md:inline">•</span>
                <span class="hidden md:flex items-center gap-1">
                    <span>🚀</span>
                    <span>Laravel {{ app()->version() }}</span>
                </span>
            </div>

            {{-- زر العودة للأعلى --}}
            <button
                x-data="{ show: false }"
                x-init="window.addEventListener('scroll', () => { show = window.scrollY > 500 })"
                x-show="show"
                x-transition
                @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
                class="p-2 rounded-full bg-indigo-600 hover:bg-indigo-700 text-white shadow-lg transition"
                title="{{ __('العودة للأعلى') }}"
                style="display: none;"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                </svg>
            </button>
        </div>

    </div>
</footer>