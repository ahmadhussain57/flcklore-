@php
    $isProduct = ($item->item_type ?? 'content') === 'product';

    $url = $isProduct
        ? route('shop.show', $item->slug)
        : route('articles.show', $item->slug);

    if ($isProduct) {
        $cover = $item->cover_image;
    } else {
        $cover = $item->media->where('media_type', 'image')->first()?->path;
    }
@endphp

<article class="h-full bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group flex flex-col">

    {{-- صورة الغلاف (مساحة محجوزة دائماً) --}}
    <a href="{{ $url }}" class="block relative overflow-hidden h-44 flex-shrink-0">
        @if($cover)
            <img src="{{ $cover }}" alt="{{ $item->title }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
        @else
            <div class="w-full h-full bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center">
                <span class="text-5xl">
                    @if($isProduct)
                        {{ $item->isPhysical() ? '🏺' : '💾' }}
                    @else
                        @php
                            $icons = ['article' => '📝', 'image' => '🖼️', 'video' => '🎬', 'audio' => '🎵'];
                        @endphp
                        {{ $icons[$item->type] ?? '📄' }}
                    @endif
                </span>
            </div>
        @endif

        {{-- Badge النوع --}}
        <div class="absolute top-2 right-2 px-2 py-0.5 bg-black/70 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
            @if($isProduct)
                🛍️ {{ __('منتج') }}
            @else
                📚 {{ __('محتوى') }}
            @endif
        </div>

        {{-- شارة التخفيض --}}
        @if($isProduct && $item->hasDiscount())
            <div class="absolute top-2 left-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                -{{ $item->discount_percentage }}%
            </div>
        @endif

        {{-- عدد التعليقات (Badge) --}}
        @if(isset($item->comments_count) && $item->comments_count > 0)
            <div class="absolute bottom-2 right-2 px-2 py-0.5 bg-blue-600/90 backdrop-blur-sm text-white text-xs font-bold rounded-full">
                💬 {{ $item->comments_count }}
            </div>
        @endif
    </a>

    {{-- المحتوى --}}
    <div class="p-4 flex-1 flex flex-col">

        {{-- العنوان --}}
        <h3 class="font-bold text-base text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
            <a href="{{ $url }}">{{ $item->title }}</a>
        </h3>

        {{-- وصف مختصر (للمنتجات) --}}
        @if($isProduct && $item->short_description)
            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-2">
                {{ $item->short_description }}
            </p>
        @endif

        {{-- مقتطف (للمحتوى) --}}
        @if(!$isProduct && $item->body)
            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 mb-2">
                {{ Str::limit(strip_tags($item->body), 70) }}
            </p>
        @endif

        {{-- السعر (للمنتجات) --}}
        @if($isProduct)
            <div class="flex items-baseline gap-2 mb-3">
                @if($item->hasDiscount())
                    <span class="text-lg font-bold text-green-600 dark:text-green-400">
                        ${{ number_format($item->sale_price, 2) }}
                    </span>
                    <span class="text-xs text-gray-400 line-through">
                        ${{ number_format($item->price, 2) }}
                    </span>
                @else
                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                        ${{ number_format($item->price, 2) }}
                    </span>
                @endif
            </div>
        @endif

        {{-- Footer --}}
        <div class="mt-auto pt-3 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                <span title="{{ __('إعجابات') }}">❤️ {{ $item->likes_count ?? 0 }}</span>
                <span title="{{ __('تعليقات') }}">💬 {{ $item->comments_count ?? 0 }}</span>
            </div>
            <a href="{{ $url }}" class="text-indigo-600 dark:text-indigo-400 text-xs font-semibold hover:underline">
                {{ __('عرض') }} →
            </a>
        </div>
    </div>
</article>