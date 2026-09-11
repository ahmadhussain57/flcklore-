<x-app-layout>
    <x-slot name="meta">
    <x-meta-tags
        :title="$product->title"
        :description="$product->short_description ?? $product->title"
        :image="$product->cover_image"
        type="product"
    />
</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $product->title }}
            </h2>
            <a href="{{ route('shop.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← {{ __('العودة إلى المتجر') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- ✅ رسائل التنبيه --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- تفاصيل المنتج --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">

                    {{-- معرض وسائط المنتج --}}
                    <div class="bg-gray-100 dark:bg-gray-900 p-4">
                        @php $cover = $product->cover_image; @endphp
                        @if($product->media->count())
                            <div class="space-y-3">
                                {{-- الصورة الرئيسية --}}
                                @if($cover)
                                    <img src="{{ $cover }}" alt="{{ $product->title }}"
                                         id="main-image"
                                         class="w-full h-80 md:h-96 object-cover rounded-2xl shadow-lg">
                                @endif

                                {{-- الصور المصغرة --}}
                                @if($product->media->where('media_type', 'image')->count() > 1)
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach($product->media->where('media_type', 'image') as $media)
                                            <img src="{{ $media->path }}" alt="{{ $media->original_name }}"
                                                 onclick="document.getElementById('main-image').src = '{{ $media->path }}'"
                                                 class="w-full h-20 object-cover rounded-lg shadow cursor-pointer hover:opacity-80 transition">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="h-80 md:h-96 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center rounded-2xl">
                                <span class="text-9xl">{{ $product->isPhysical() ? '🏺' : '💾' }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- معلومات --}}
                    <div class="p-6 lg:p-8">

                        {{-- الحالة --}}
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ $product->isPhysical() ? '🏺 ' . __('مادي') : '💾 ' . __('رقمي') }}
                            </span>
                            @if($product->isPhysical())
                                @if($product->isInStock())
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                        ✅ {{ __('متوفر') }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        ❌ {{ __('نفد المخزون') }}
                                    </span>
                                @endif
                            @endif
                        </div>

                        {{-- العنوان --}}
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ $product->title }}</h1>

                        @if($product->short_description)
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $product->short_description }}</p>
                        @endif

                        {{-- السعر --}}
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            @if($product->hasDiscount())
                                <div class="flex items-baseline gap-3">
                                    <span class="text-3xl font-bold text-green-600 dark:text-green-400">
                                        {{ number_format($product->sale_price, 2) }}
                                    </span>
                                    <span class="text-lg text-gray-400 line-through">
                                        {{ number_format($product->price, 2) }}
                                    </span>
                                    <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-bold rounded-full">
                                        -{{ $product->discount_percentage }}%
                                    </span>
                                </div>
                            @else
                                <span class="text-3xl font-bold text-gray-900 dark:text-white">
                                    {{ number_format($product->price, 2) }}
                                </span>
                            @endif
                        </div>

                        {{-- زر الإضافة للسلة (مؤقت) --}}
                        <button type="button"
                                class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md transition mb-4 flex items-center justify-center gap-2">
                            🛒 {{ __('أضف إلى السلة') }}
                            <span class="text-xs opacity-75">({{ __('قريباً') }})</span>
                        </button>

                        {{-- ✅ الإعجاب والتعليقات --}}
                        <div class="flex items-center gap-6 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            {{-- زر الإعجاب --}}
                            <div class="flex items-center gap-2">
                                @auth
                                    <form action="{{ route('shop.like.toggle', $product->slug) }}" method="POST" class="inline">
                                        @csrf
                                        @php
                                            $userLiked = auth()->user()->likes()
                                                ->where('likeable_type', 'App\Models\Product')
                                                ->where('likeable_id', $product->id)
                                                ->exists();
                                        @endphp
                                        <button type="submit" class="group flex items-center gap-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition duration-200">
                                            <span class="text-2xl group-hover:scale-110 transition-transform">
                                                {{ $userLiked ? '❤️' : '🤍' }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $product->likes()->count() }}
                                            </span>
                                        </button>
                                    </form>
                                @else
                                    <div class="flex items-center gap-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 text-gray-500">
                                        <span class="text-2xl">🤍</span>
                                        <span class="text-sm font-medium">{{ $product->likes()->count() }}</span>
                                    </div>
                                @endauth
                            </div>

                            {{-- عدد التعليقات --}}
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                💬 {{ $product->comments()->count() }} {{ __('تعليق') }}
                            </div>
                        </div>

                        <p class="text-xs text-gray-500 dark:text-gray-400 text-center mt-4">
                            {{ __('الدفع الإلكتروني سيكون متاحاً في المرحلة القادمة.') }}
                        </p>

                    </div>
                </div>
            </div>

            {{-- الوصف الكامل --}}
            @if($product->description)
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">📖 {{ __('الوصف الكامل') }}</h2>
                    <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
            @endif

            {{-- التصنيفات --}}
            @if($product->categories->count())
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">{{ __('التصنيفات') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->categories as $category)
                            <span class="px-4 py-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 rounded-full text-sm">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ✅ قسم التعليقات --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">💬 {{ __('التعليقات') }}</h3>

                {{-- نموذج إضافة تعليق --}}
                @auth
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <form action="{{ route('shop.comment.store', $product->slug) }}" method="POST">
                            @csrf
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold flex-shrink-0">
                                    {{ mb_substr(auth()->user()->name, 0, 2) }}
                                </div>
                                <div class="flex-1">
                                    <textarea name="body" rows="2" placeholder="{{ __('اكتب تعليقك...') }}"
                                              class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none">{{ old('body') }}</textarea>
                                    @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    <div class="mt-2 flex justify-end">
                                        <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-full shadow-sm transition">
                                            {{ __('أضف تعليقاً') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-center text-gray-600 dark:text-gray-400">
                        <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('سجل الدخول') }}</a>
                        {{ __('لتتمكن من التعليق والإعجاب.') }}
                    </div>
                @endauth

                {{-- قائمة التعليقات --}}
                @php
                    $comments = $product->comments()->with('user')->latest()->get();
                @endphp

                @if($comments->count())
                    <div class="space-y-4">
                        @foreach($comments as $comment)
                            <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-200 dark:border-gray-700">
                                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold flex-shrink-0">
                                    {{ mb_substr($comment->user->name, 0, 2) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">· {{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        @auth
                                            @if(auth()->id() === $comment->user_id || auth()->user()->hasPermissionTo('delete_any_content', 'web'))
                                                <form action="{{ route('shop.comment.destroy', $comment) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا التعليق؟') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 text-xs transition">
                                                        {{ __('حذف') }}
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                    <p class="mt-1 text-gray-700 dark:text-gray-300 break-words">{{ $comment->body }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-gray-500 dark:text-gray-400 py-6">
                        {{ __('لا توجد تعليقات بعد. كن أول من يعلق!') }}
                    </div>
                @endif
            </div>

            {{-- منتجات مشابهة --}}
            @if($relatedProducts->count())
                <div class="mt-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">✨ {{ __('منتجات مشابهة') }}</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach($relatedProducts as $related)
                            <a href="{{ route('shop.show', $related->slug) }}"
                               class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-xl transition overflow-hidden group">
                                <div class="h-32 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center">
                                    @if($related->cover_image)
                                        <img src="{{ $related->cover_image }}" alt="{{ $related->title }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-4xl">{{ $related->isPhysical() ? '🏺' : '💾' }}</span>
                                    @endif
                                </div>
                                <div class="p-3">
                                    <h3 class="font-semibold text-sm text-gray-800 dark:text-gray-200 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                        {{ $related->title }}
                                    </h3>
                                    <div class="mt-1 text-indigo-600 dark:text-indigo-400 font-bold text-sm">
                                        {{ number_format($related->effective_price, 2) }}
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>