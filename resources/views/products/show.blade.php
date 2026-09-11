<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $product->title }}
            </h2>
            <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← {{ __('العودة إلى منتجاتي') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 lg:p-8">

                    {{-- الحالة والنوع --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        @php
                            $statusMap = [
                                'draft' => ['label' => 'مسودة', 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
                                'pending_review' => ['label' => 'قيد المراجعة', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                'pending_edit' => ['label' => 'تعديل معلق', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                'published' => ['label' => 'منشور', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
                                'rejected' => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
                            ];
                            $s = $statusMap[$product->status] ?? $statusMap['draft'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $s['class'] }}">{{ $s['label'] }}</span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                            {{ $product->isPhysical() ? '🏺 مادي' : '💾 رقمي' }}
                        </span>
                    </div>

                    {{-- العنوان --}}
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $product->title }}</h1>

                    @if($product->short_description)
                        <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">{{ $product->short_description }}</p>
                    @endif

                    {{-- ✅ معرض الوسائط --}}
                    @if($product->media->count())
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">
                                🖼️ {{ __('الوسائط') }} ({{ $product->media->count() }})
                            </h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($product->media as $media)
                                    @if($media->media_type === 'image')
                                        <a href="{{ $media->path }}" target="_blank"
                                           class="block group overflow-hidden rounded-xl shadow-md hover:shadow-xl transition">
                                            <img src="{{ $media->path }}"
                                                 alt="{{ $media->original_name ?? $product->title }}"
                                                 class="w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300">
                                        </a>
                                    @elseif($media->media_type === 'video')
                                        <div class="rounded-xl overflow-hidden shadow-md bg-black">
                                            <video controls class="w-full h-64" preload="metadata">
                                                <source src="{{ $media->path }}" type="{{ $media->mime_type ?? 'video/mp4' }}">
                                                {{ __('متصفحك لا يدعم تشغيل الفيديو.') }}
                                            </video>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- صورة افتراضية إن لم توجد وسائط --}}
                        <div class="mb-6 h-64 md:h-80 bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center rounded-2xl">
                            <span class="text-9xl">{{ $product->isPhysical() ? '🏺' : '💾' }}</span>
                        </div>
                    @endif

                    {{-- السعر --}}
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                        @if($product->hasDiscount())
                            <div class="flex items-baseline gap-3">
                                <span class="text-3xl font-bold text-green-600 dark:text-green-400">{{ number_format($product->sale_price, 2) }}</span>
                                <span class="text-lg text-gray-400 line-through">{{ number_format($product->price, 2) }}</span>
                                <span class="px-2 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-bold rounded-full">
                                    -{{ $product->discount_percentage }}%
                                </span>
                            </div>
                        @else
                            <span class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($product->price, 2) }}</span>
                        @endif
                    </div>

                    {{-- المخزون و SKU --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        @if($product->isPhysical())
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('المخزون') }}</div>
                                @if($product->stock_quantity > 0)
                                    <div class="text-lg font-semibold text-green-600 dark:text-green-400">{{ $product->stock_quantity }} {{ __('قطعة') }}</div>
                                @else
                                    <div class="text-lg font-semibold text-red-600 dark:text-red-400">{{ __('نفد المخزون') }}</div>
                                @endif
                            </div>
                        @endif
                        @if($product->sku)
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">SKU</div>
                                <div class="text-lg font-mono text-gray-800 dark:text-gray-200">{{ $product->sku }}</div>
                            </div>
                        @endif
                    </div>

                    {{-- الوصف الكامل --}}
                    @if($product->description)
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">{{ __('الوصف') }}</h3>
                            <div class="prose dark:prose-invert max-w-none text-gray-800 dark:text-gray-200">
                                {!! nl2br(e($product->description)) !!}
                            </div>
                        </div>
                    @endif

                    {{-- التصنيفات --}}
                    @if($product->categories->count())
                        <div class="mb-6">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-2">{{ __('التصنيفات') }}</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($product->categories as $category)
                                    <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 rounded-full text-sm">
                                        {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- سبب الرفض --}}
                    @if($product->rejection_reason)
                        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                            <h3 class="text-sm font-semibold text-red-700 dark:text-red-300 mb-1">{{ __('سبب الرفض') }}</h3>
                            <p class="text-red-800 dark:text-red-200">{{ $product->rejection_reason }}</p>
                        </div>
                    @endif

                    {{-- معلومات إضافية --}}
                    <div class="text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-4">
                        <p>{{ __('أُنشئ في') }}: {{ $product->created_at->format('Y-m-d H:i') }}</p>
                        @if($product->published_at)
                            <p>{{ __('نُشر في') }}: {{ $product->published_at->format('Y-m-d H:i') }}</p>
                        @endif
                    </div>

                    {{-- الأزرار --}}
                    <div class="flex flex-wrap gap-3 mt-6">
                        @if(in_array($product->status, ['draft', 'rejected', 'pending_edit']))
                            <a href="{{ route('products.edit', $product) }}"
                               class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-full transition">
                                ✏️ {{ __('تعديل') }}
                            </a>
                        @endif

                        @if(in_array($product->status, ['draft', 'rejected']))
                            <form action="{{ route('products.submit', $product) }}" method="POST">
                                @csrf
                                <button type="submit"
                                        class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-full transition">
                                    📤 {{ __('إرسال للمراجعة') }}
                                </button>
                            </form>
                        @endif

                        @if($product->status === 'published')
                            <a href="{{ route('shop.show', $product->slug) }}" target="_blank"
                               class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-full transition">
                                👁️ {{ __('عرض في المتجر') }}
                            </a>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>