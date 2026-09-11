<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags
            title="منصة الفلكلور"
            description="اكتشف التراث والقصص الشعبية والحرف اليدوية من كل مكان."
        />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🏺 {{ __('منصة الفلكلور') }}
        </h2>
    </x-slot>

    <div class="pb-12">

        {{-- ==========================================
             Hero Section
             ========================================== --}}
        <section class="relative bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-600 overflow-hidden">
            {{-- زخرفة خلفية --}}
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-10 left-10 text-9xl">🏺</div>
                <div class="absolute bottom-10 right-10 text-9xl">📜</div>
                <div class="absolute top-1/2 left-1/3 text-9xl">🎭</div>
            </div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
                <div class="text-center">
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-4 leading-tight">
                        {{ __('اكتشف كنوز الفلكلور') }}
                    </h1>
                    <p class="text-lg md:text-2xl text-white/90 mb-8 max-w-2xl mx-auto">
                        {{ __('منصة رقمية تجمع التراث والقصص الشعبية والحرف اليدوية من كل مكان') }}
                    </p>

                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('shop.index') }}"
                           class="px-8 py-4 bg-white text-indigo-600 font-bold rounded-full shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                            🛍️ {{ __('تصفح المتجر') }}
                        </a>
                        @guest
                            <a href="{{ route('register') }}"
                               class="px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-indigo-600 transition-all duration-300">
                                ✨ {{ __('انضم إلينا') }}
                            </a>
                        @endguest
                    </div>
                </div>
            </div>

            {{-- موجة سفلية --}}
            <svg class="absolute bottom-0 w-full h-12 text-gray-100 dark:text-gray-900" viewBox="0 0 1440 74" preserveAspectRatio="none">
                <path fill="currentColor" d="M0,32L60,37.3C120,43,240,53,360,53.3C480,53,600,43,720,42.7C840,43,960,53,1080,53.3C1200,53,1320,43,1380,37.3L1440,32L1440,74L1380,74C1320,74,1200,74,1080,74C960,74,840,74,720,74C600,74,480,74,360,74C240,74,120,74,60,74L0,74Z"></path>
            </svg>
        </section>

        {{-- ==========================================
             إحصائيات
             ========================================== --}}
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 relative z-10">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 text-center">
                    <div class="text-3xl md:text-4xl font-extrabold text-indigo-600 dark:text-indigo-400 mb-1">{{ $stats['contents'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('مقالة منشورة') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 text-center">
                    <div class="text-3xl md:text-4xl font-extrabold text-purple-600 dark:text-purple-400 mb-1">{{ $stats['products'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('منتج فلكلوري') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 text-center">
                    <div class="text-3xl md:text-4xl font-extrabold text-pink-600 dark:text-pink-400 mb-1">{{ $stats['authors'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('كاتب ومؤلف') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 text-center">
                    <div class="text-3xl md:text-4xl font-extrabold text-amber-600 dark:text-amber-400 mb-1">{{ $stats['categories'] }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">{{ __('تصنيف') }}</div>
                </div>
            </div>
        </section>

        {{-- ==========================================
             آخر المحتويات (✅ روابط articles.* العامة)
             ========================================== --}}
        @if($latestContents->count())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">📚 {{ __('أحدث القصص والمقالات') }}</h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('اكتشف التراث المكتوب') }}</p>
                    </div>
                    {{-- ✅ تصحيح: articles.index --}}
                    <a href="{{ route('articles.index') }}" class="hidden md:block text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                        {{ __('عرض الكل') }} →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($latestContents as $content)
                        <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                            {{-- صورة الغلاف --}}
                            {{-- ✅ تصحيح: articles.show --}}
                            <a href="{{ route('articles.show', $content->slug) }}" class="block relative overflow-hidden h-48">
                                @php $cover = $content->media->where('media_type', 'image')->first(); @endphp
                                @if($cover)
                                    <img src="{{ $cover->path }}" alt="{{ $content->title }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center">
                                        <span class="text-6xl">{{ $content->type === 'article' ? '📝' : ($content->type === 'video' ? '🎬' : ($content->type === 'audio' ? '🎵' : '🖼️')) }}</span>
                                    </div>
                                @endif

                                {{-- نوع المحتوى --}}
                                <div class="absolute top-3 right-3 px-3 py-1 bg-black/70 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                    {{ $content->type === 'article' ? '📝 مقال' : ($content->type === 'video' ? '🎬 فيديو' : ($content->type === 'audio' ? '🎵 صوت' : '🖼️ صورة')) }}
                                </div>
                            </a>

                            {{-- المحتوى --}}
                            <div class="p-5">
                                {{-- التصنيفات --}}
                                @if($content->categories->count())
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach($content->categories->take(2) as $category)
                                            <span class="text-xs px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- العنوان --}}
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                    {{-- ✅ تصحيح: articles.show --}}
                                    <a href="{{ route('articles.show', $content->slug) }}">{{ $content->title }}</a>
                                </h3>

                                {{-- مقتطف --}}
                                @if($content->body)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                        {{ Str::limit(strip_tags($content->body), 100) }}
                                    </p>
                                @endif

                                {{-- الكاتب + التفاعل --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold text-xs">
                                            {{ mb_substr($content->author->name, 0, 1) }}
                                        </div>
                                        <span class="truncate">{{ $content->author->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span>❤️ {{ $content->likes->count() }}</span>
                                        <span>💬 {{ $content->comments->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ==========================================
             آخر المنتجات (✅ روابط shop.* العامة)
             ========================================== --}}
        @if($latestProducts->count())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white">🛍️ {{ __('منتجات فلكلورية') }}</h2>
                        <p class="text-gray-500 dark:text-gray-400 mt-1">{{ __('اقتنِ كنوزاً أصلية') }}</p>
                    </div>
                    <a href="{{ route('shop.index') }}" class="hidden md:block text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                        {{ __('تصفح المتجر') }} →
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($latestProducts as $product)
                        <a href="{{ route('shop.show', $product->slug) }}"
                           class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                            {{-- صورة --}}
                            <div class="relative h-48 overflow-hidden">
                                @php $cover = $product->cover_image; @endphp
                                @if($cover)
                                    <img src="{{ $cover }}" alt="{{ $product->title }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-amber-400 to-pink-500 flex items-center justify-center">
                                        <span class="text-6xl">{{ $product->isPhysical() ? '🏺' : '💾' }}</span>
                                    </div>
                                @endif

                                {{-- شارة التخفيض --}}
                                @if($product->hasDiscount())
                                    <div class="absolute top-3 right-3 px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                        -{{ $product->discount_percentage }}%
                                    </div>
                                @endif
                            </div>

                            {{-- المعلومات --}}
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                    {{ $product->title }}
                                </h3>

                                <div class="flex items-baseline gap-2">
                                    @if($product->hasDiscount())
                                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                            {{ number_format($product->sale_price, 2) }}
                                        </span>
                                        <span class="text-sm text-gray-400 line-through">
                                            {{ number_format($product->price, 2) }}
                                        </span>
                                    @else
                                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ number_format($product->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- ==========================================
             استكشف حسب التصنيف
             ========================================== --}}
        @if($contentCategories->count() || $productCategories->count())
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-16">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">🔍 {{ __('استكشف حسب التصنيف') }}</h2>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('اختر اهتمامك وابدأ الرحلة') }}</p>
                </div>

                @if($contentCategories->count())
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">📚 {{ __('تصنيفات المحتوى') }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
                        @foreach($contentCategories as $category)
                            <a href="{{ route('articles.index', ['category' => $category->slug]) }}"
                               class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition p-4 text-center block">
                                <div class="text-3xl mb-2">📖</div>
                                <div class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $category->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $category->contents_count }} {{ __('محتوى') }}</div>
                            </a>
                        @endforeach
                    </div>
                @endif

                @if($productCategories->count())
                    <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-4">🏷️ {{ __('تصنيفات المنتجات') }}</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($productCategories as $category)
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                               class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg transition p-4 text-center block">
                                <div class="text-3xl mb-2">🏺</div>
                                <div class="font-semibold text-gray-800 dark:text-gray-200 text-sm">{{ $category->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $category->products_count }} {{ __('منتج') }}</div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        {{-- ==========================================
             CTA: انضم كمؤلف
             ========================================== --}}
        @guest
            <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-20">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-3xl shadow-2xl p-8 md:p-12 text-center relative overflow-hidden">
                    <div class="absolute top-4 right-4 text-6xl opacity-20">✍️</div>
                    <div class="absolute bottom-4 left-4 text-6xl opacity-20">📜</div>

                    <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                        {{ __('هل أنت راوي قصص أو كاتب؟') }}
                    </h2>
                    <p class="text-lg text-white/90 mb-8 max-w-2xl mx-auto">
                        {{ __('انضم إلى مجتمعنا وشارك تراثك مع العالم. انشر مقالاتك، قصصك، وصورك الفلكلورية.') }}
                    </p>
                    <div class="flex flex-wrap justify-center gap-4">
                        <a href="{{ route('register') }}"
                           class="px-8 py-4 bg-white text-indigo-600 font-bold rounded-full shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300">
                            🚀 {{ __('أنشئ حسابك') }}
                        </a>
                        <a href="{{ route('login') }}"
                           class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-indigo-600 transition-all duration-300">
                            {{ __('تسجيل الدخول') }}
                        </a>
                    </div>
                </div>
            </section>
        @endguest

    </div>
</x-app-layout>