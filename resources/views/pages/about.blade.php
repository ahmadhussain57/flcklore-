<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            ℹ️ {{ __('من نحن') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Hero --}}
            <div class="bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl shadow-xl p-8 md:p-12 text-center text-white">
                <div class="text-6xl mb-4">🏺</div>
                <h1 class="text-3xl md:text-4xl font-bold mb-3">{{ __('منصة الفلكلور الرقمية') }}</h1>
                <p class="text-lg opacity-90 max-w-2xl mx-auto">
                    {{ __('نحفظ التراث، نروي القصص، ونربط الأجيال بجذورها.') }}
                </p>
            </div>

            {{-- رسالتنا --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                    <span>🎯</span> {{ __('رسالتنا') }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed mb-4">
                    {{ __('نسعى إلى بناء أكبر منصة رقمية عربية تجمع التراث الفلكلوري من كل مكان — قصصاً، أهازيج، حرفاً يدوية، وحكايات شعبية — لتحفظها من الاندثار وتنقلها للأجيال القادمة بأسلوب عصري وجذاب.') }}
                </p>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                    {{ __('نؤمن أن التراث ليس مجرد ماضٍ، بل هو هوية حية تستحق أن تُروى وتُشارك وتُقتنى.') }}
                </p>
            </div>

            {{-- ما نقدمه --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                    <span>✨</span> {{ __('ماذا نقدم؟') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center p-6 rounded-xl bg-indigo-50 dark:bg-indigo-900/20">
                        <div class="text-4xl mb-3">📚</div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2">{{ __('مقالات وقصص') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('محتوى فلكلوري موثق ومراجع من مؤلفين ومدققين متخصصين.') }}
                        </p>
                    </div>
                    <div class="text-center p-6 rounded-xl bg-purple-50 dark:bg-purple-900/20">
                        <div class="text-4xl mb-3">🎬</div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2">{{ __('وسائط متعددة') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('صور، فيديو، وتسجيلات صوتية للتراث الفلكلوري.') }}
                        </p>
                    </div>
                    <div class="text-center p-6 rounded-xl bg-pink-50 dark:bg-pink-900/20">
                        <div class="text-4xl mb-3">🛍️</div>
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-2">{{ __('منتجات تراثية') }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('حرف يدوية، تحف، وأزياء تقليدية أصيلة.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- إحصائيات --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-6 flex items-center gap-2">
                    <span>📊</span> {{ __('أرقامنا') }}
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                            {{ \App\Models\Content::published()->count() }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مقالة') }}</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <div class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                            {{ \App\Models\Product::published()->count() }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('منتج') }}</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <div class="text-3xl font-bold text-pink-600 dark:text-pink-400">
                            {{ \App\Models\User::count() }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مستخدم') }}</div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        <div class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                            {{ \App\Models\Category::count() + \App\Models\ProductCategory::count() }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('تصنيف') }}</div>
                    </div>
                </div>
            </div>

            {{-- Call to Action --}}
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-xl p-8 text-center text-white">
                <h2 class="text-2xl font-bold mb-3">{{ __('انضم إلينا') }}</h2>
                <p class="opacity-90 mb-6 max-w-2xl mx-auto">
                    {{ __('كن جزءاً من رحلة حفظ التراث. انشر مقالاتك، اعرض منتجاتك، وشارك في إحياء الفلكلور.') }}
                </p>
                <div class="flex flex-wrap justify-center gap-3">
                    @guest
                        <a href="{{ route('register') }}" class="px-6 py-3 bg-white text-indigo-600 font-bold rounded-full shadow-lg hover:scale-105 transition">
                            🚀 {{ __('أنشئ حسابك') }}
                        </a>
                    @endguest
                    <a href="{{ route('articles.index') }}" class="px-6 py-3 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-indigo-600 transition">
                        📚 {{ __('تصفح المقالات') }}
                    </a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>