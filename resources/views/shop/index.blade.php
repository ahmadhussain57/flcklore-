<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags
            title="المتجر الفلكلوري"
            description="تصفح منتجات تراثية أصيلة: حرف يدوية، تحف، أزياء تقليدية."
        />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                🛍️ {{ __('المتجر الفلكلوري') }}
            </h2>
            @auth
                @if(auth()->user()->hasAnyRole(['marketing_admin', 'marketing_Specialist']))
                    <a href="{{ route('products.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                        + {{ __('منتج جديد') }}
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ✅ رسائل التنبيه --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    ✗ {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="mb-4 p-4 bg-blue-100 dark:bg-blue-900/50 border-l-4 border-blue-500 text-blue-700 dark:text-blue-300 rounded-lg">
                    ℹ️ {{ session('info') }}
                </div>
            @endif
            {{-- ✅ رسالة التحذير (الكمية محدودة) --}}
            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900/50 border-l-4 border-yellow-500 text-yellow-800 dark:text-yellow-200 rounded-lg">
                    {{ session('warning') }}
                </div>
            @endif

            {{-- ✅ Hero Section --}}
            <div class="mb-8 p-8 bg-gradient-to-r from-amber-500 via-pink-500 to-purple-600 rounded-2xl shadow-xl text-white text-center relative overflow-hidden">
                <div class="absolute top-4 right-4 text-6xl opacity-20">🏺</div>
                <div class="absolute bottom-4 left-4 text-6xl opacity-20">🎭</div>
                <h1 class="text-3xl md:text-4xl font-bold mb-2 relative">{{ __('اكتشف كنوز الفلكلور') }}</h1>
                <p class="text-lg opacity-90 relative">{{ __('منتجات تراثية أصيلة من كل مكان') }}</p>
            </div>

            {{-- ✅ إحصائيات سريعة --}}
            <div class="grid grid-cols-3 gap-3 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('إجمالي المنتجات') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-500">{{ $stats['on_sale'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('عليها تخفيض') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['in_stock'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('متوفرة') }}</div>
                </div>
            </div>

            {{-- ✅ شريط الفلاتر والبحث --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">

                {{-- البحث والترتيب (الصف الأول) --}}
                <form method="GET" action="{{ route('shop.index') }}" id="filterForm">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                        {{-- بحث --}}
                        <div class="md:col-span-2">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="{{ __('ابحث عن منتج...') }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        {{-- الترتيب --}}
                        <div>
                            <select name="sort" onchange="document.getElementById('filterForm').submit()"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>🆕 {{ __('الأحدث') }}</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>🕐 {{ __('الأقدم') }}</option>
                                <option value="price_low" {{ request('sort') === 'price_low' ? 'selected' : '' }}>💰 {{ __('الأرخص أولاً') }}</option>
                                <option value="price_high" {{ request('sort') === 'price_high' ? 'selected' : '' }}>💎 {{ __('الأغلى أولاً') }}</option>
                                <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>🔥 {{ __('الأكثر إعجاباً') }}</option>
                            </select>
                        </div>

                        {{-- زر البحث --}}
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition">
                                🔍 {{ __('بحث') }}
                            </button>
                            @if(request()->anyFilled(['q', 'type', 'category', 'sort', 'min_price', 'max_price', 'on_sale', 'in_stock']))
                                <a href="{{ route('shop.index') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition"
                                   title="{{ __('مسح الفلاتر') }}">
                                    ✖
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- الفلاتر المتقدمة --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">

                        {{-- النوع --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('النوع') }}</label>
                            <select name="type" onchange="document.getElementById('filterForm').submit()"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('الكل') }}</option>
                                <option value="physical" {{ request('type') === 'physical' ? 'selected' : '' }}>🏺 {{ __('مادي') }}</option>
                                <option value="digital" {{ request('type') === 'digital' ? 'selected' : '' }}>💾 {{ __('رقمي') }}</option>
                            </select>
                        </div>

                        {{-- السعر الأدنى --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('السعر الأدنى') }}</label>
                            <input type="number" name="min_price" value="{{ request('min_price') }}" min="0" step="0.01"
                                   placeholder="0"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        {{-- السعر الأقصى --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('السعر الأقصى') }}</label>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" min="0" step="0.01"
                                   placeholder="1000"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        {{-- خيارات إضافية --}}
                        <div class="flex flex-col justify-end gap-2">
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="on_sale" value="1" {{ request('on_sale') ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()"
                                       class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                                🔥 {{ __('عليه تخفيض فقط') }}
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="in_stock" value="1" {{ request('in_stock') ? 'checked' : '' }}
                                       onchange="document.getElementById('filterForm').submit()"
                                       class="rounded border-gray-300 text-green-500 focus:ring-green-500">
                                ✅ {{ __('المتوفر فقط') }}
                            </label>
                        </div>
                    </div>
                </form>

                {{-- التصنيفات السريعة --}}
                @if($categories->count())
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 self-center">{{ __('التصنيفات:') }}</span>
                            <a href="{{ route('shop.index', request()->except('category')) }}"
                               class="px-3 py-1 text-sm rounded-full transition {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                {{ __('الكل') }}
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('shop.index', array_merge(request()->except('category'), ['category' => $cat->slug])) }}"
                                   class="px-3 py-1 text-sm rounded-full transition {{ request('category') === $cat->slug ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                    {{ $cat->name }}
                                    <span class="text-xs opacity-75">({{ $cat->products_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ✅ عدد النتائج --}}
            @if(request()->anyFilled(['q', 'type', 'category', 'min_price', 'max_price', 'on_sale', 'in_stock']))
                <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('عدد النتائج:') }} <strong class="text-gray-900 dark:text-white">{{ $products->total() }}</strong>
                    @if(request('q'))
                        {{ __('عن') }} "<strong>{{ request('q') }}</strong>"
                    @endif
                </div>
            @endif

            {{-- ✅ شبكة المنتجات --}}
            @if($products->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                            {{-- صورة الغلاف --}}
                            <a href="{{ route('shop.show', $product->slug) }}" class="block relative overflow-hidden">
                                <div class="h-48 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                    @php $cover = $product->cover_image; @endphp
                                    @if($cover)
                                        <img src="{{ $cover }}" alt="{{ $product->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                    @else
                                        <span class="text-6xl">{{ $product->isPhysical() ? '🏺' : '💾' }}</span>
                                    @endif
                                </div>

                                {{-- شارة التخفيض --}}
                                @if($product->hasDiscount())
                                    <div class="absolute top-3 right-3 px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full shadow-lg">
                                        -{{ $product->discount_percentage }}%
                                    </div>
                                @endif

                                {{-- نوع المنتج --}}
                                <div class="absolute top-3 left-3 px-2 py-1 bg-black/70 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                    {{ $product->isPhysical() ? '🏺' : '💾' }}
                                </div>

                                {{-- حالة نفاد المخزون --}}
                                @if($product->isPhysical() && !$product->isInStock())
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                        <span class="px-4 py-2 bg-red-600 text-white font-bold rounded-full">
                                            {{ __('نفد المخزون') }}
                                        </span>
                                    </div>
                                @endif
                            </a>

                            {{-- محتوى البطاقة --}}
                            <div class="p-4">
                                {{-- التصنيفات --}}
                                @if($product->categories->count())
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach($product->categories->take(2) as $category)
                                            <span class="text-xs px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- العنوان --}}
                                <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                    <h3 class="font-bold text-lg text-gray-800 dark:text-gray-200 mb-1 line-clamp-2 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        {{ $product->title }}
                                    </h3>
                                </a>

                                {{-- الوصف المختصر --}}
                                @if($product->short_description)
                                    <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                                        {{ $product->short_description }}
                                    </p>
                                @endif

                                {{-- السعر --}}
                                <div class="flex items-baseline gap-2 mb-3">
                                    @if($product->hasDiscount())
                                        <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ number_format($product->sale_price, 2) }}
                                        </span>
                                        <span class="text-sm text-gray-400 line-through">
                                            {{ number_format($product->price, 2) }}
                                        </span>
                                    @else
                                        <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                            {{ number_format($product->price, 2) }}
                                        </span>
                                    @endif
                                </div>

                                {{-- الإعجابات + الأزرار --}}
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span>❤️ {{ $product->likes()->count() }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('shop.show', $product->slug) }}"
                                           class="px-3 py-1.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full transition">
                                            {{ __('عرض') }}
                                        </a>
                                        @auth
                                            @if($product->isInStock() && $product->user_id !== auth()->id())
                                                <form method="POST" action="{{ route('cart.add', $product) }}" class="inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full transition flex items-center gap-1"
                                                            title="{{ __('أضف إلى السلة') }}">
                                                        🛒 {{ __('أضف') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- الترقيم --}}
                @if($products->hasPages())
                    <div class="mt-8">
                        {{ $products->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
                    <div class="text-8xl mb-4">📭</div>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('لا توجد منتجات') }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if(request()->anyFilled(['q', 'type', 'category', 'min_price', 'max_price', 'on_sale', 'in_stock']))
                            {{ __('لا توجد نتائج مطابقة لبحثك.') }}
                            <a href="{{ route('shop.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline block mt-2">{{ __('مسح الفلاتر') }}</a>
                        @else
                            {{ __('عد قريباً لاستكشاف الكنوز الفلكلورية!') }}
                        @endif
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>