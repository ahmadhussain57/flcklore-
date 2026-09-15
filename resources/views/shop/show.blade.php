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
            @if(session('warning'))
                <div class="mb-4 p-4 bg-yellow-100 dark:bg-yellow-900/50 border-l-4 border-yellow-500 text-yellow-800 dark:text-yellow-200 rounded-lg">
                    {{ session('warning') }}
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
                                @if($cover)
                                    <img src="{{ $cover }}" alt="{{ $product->title }}"
                                         id="main-image"
                                         class="w-full h-80 md:h-96 object-cover rounded-2xl shadow-lg">
                                @endif

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

                        {{-- زر الإضافة للسلة --}}
                        @auth
                            @if($product->user_id === auth()->id())
                                <div class="w-full py-3 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-semibold rounded-xl mb-4 text-center">
                                    {{ __('هذا منتجك الخاص') }}
                                </div>
                            @elseif(!$product->isInStock())
                                <button type="button" disabled
                                        class="w-full py-3 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold rounded-xl mb-4 cursor-not-allowed">
                                    ❌ {{ __('نفد المخزون') }}
                                </button>
                            @else
                                <form method="POST" action="{{ route('cart.add', $product) }}" class="mb-4">
                                    @csrf
                                    <div class="flex gap-2">
                                        <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-xl overflow-hidden bg-white dark:bg-gray-700">
                                            <button type="button" onclick="this.nextElementSibling.stepDown()"
                                                    class="px-3 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition">−</button>
                                            <input type="number"
                                                   name="quantity"
                                                   value="1"
                                                   min="1"
                                                   max="{{ $product->isPhysical() ? $product->stock_quantity : 99 }}"
                                                   class="w-16 text-center border-0 focus:ring-0 bg-transparent text-gray-800 dark:text-gray-200 font-semibold">
                                            <button type="button" onclick="this.previousElementSibling.stepUp()"
                                                    class="px-3 py-3 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 transition">+</button>
                                        </div>
                                        <button type="submit"
                                                class="flex-1 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                                            🛒 {{ __('أضف إلى السلة') }}
                                        </button>
                                    </div>
                                </form>
                            @endif
                        @else
                            <a href="{{ route('login', ['redirect_to' => url()->current()]) }}"
                               class="block w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md transition text-center mb-4">
                                🔐 {{ __('سجّل الدخول للشراء') }}
                            </a>
                        @endauth

                        {{-- الإعجاب والتعليقات --}}
                        <div class="flex items-center gap-6 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
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

                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                💬 {{ $product->comments()->approved()->count() }} {{ __('تعليق') }}
                            </div>
                        </div>

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
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">🏷️ {{ __('التصنيفات') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($product->categories as $category)
                            <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                               class="px-4 py-2 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 rounded-full text-sm hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition">
                                #{{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- الكلمات المفتاحية --}}
            @if(!empty($product->keywords))
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">🔍 {{ __('كلمات مفتاحية') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach(array_filter(array_map('trim', explode(',', $product->keywords))) as $keyword)
                            <a href="{{ route('shop.index', ['q' => $keyword]) }}"
                               class="inline-flex items-center gap-1 px-3 py-1 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full text-sm hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition border border-indigo-200 dark:border-indigo-800">
                                🔍 {{ $keyword }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- ✅ قسم التعليقات (معدّل) --}}
            <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">💬 {{ __('التعليقات') }}</h3>

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
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        ℹ️ {{ __('تم إرسال تعليقك. سيظهر بعد موافقة المدقق.') }}
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                @else
                    <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-center text-gray-600 dark:text-gray-400">
                        <a href="{{ route('login', ['redirect_to' => url()->current()]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('سجل الدخول') }}</a>
                        {{ __('لتتمكن من التعليق والإعجاب.') }}
                    </div>
                @endauth

                @php
                    // ✅ عرض التعليقات:
                    // - approved: للجميع (عبر Scope)
                    // - pending/rejected: للمستخدم صاحب التعليق فقط (عبر Scopes)
                    $approvedComments = $product->comments()
                        ->approved()
                        ->with('user')
                        ->latest()
                        ->get();

                    $myPendingComments = collect();
                    $myRejectedComments = collect();

                    if (auth()->check()) {
                        $myPendingComments = $product->comments()
                            ->where('user_id', auth()->id())
                            ->pending()
                            ->with('user')
                            ->latest()
                            ->get();

                        $myRejectedComments = $product->comments()
                            ->where('user_id', auth()->id())
                            ->rejected()
                            ->with('user')
                            ->latest()
                            ->get();
                    }
                @endphp

                @if($approvedComments->count() || $myPendingComments->count() || $myRejectedComments->count())
                    <div class="space-y-4">

                        {{-- ✅ تعليقاتي المعلقة (تظهر لي فقط) --}}
                        @foreach($myPendingComments as $comment)
                            <div class="flex items-start gap-3 p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border-2 border-yellow-300 dark:border-yellow-700">
                                <div class="w-10 h-10 rounded-full bg-yellow-200 dark:bg-yellow-800 flex items-center justify-center text-yellow-800 dark:text-yellow-200 font-bold flex-shrink-0">
                                    {{ mb_substr($comment->user->name, 0, 2) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">· {{ $comment->created_at->diffForHumans() }}</span>
                                            <span class="px-2 py-0.5 bg-yellow-200 dark:bg-yellow-800 text-yellow-800 dark:text-yellow-200 text-xs font-bold rounded-full">
                                                ⏳ {{ __('قيد المراجعة') }}
                                            </span>
                                        </div>
                                        <form action="{{ route('shop.comment.destroy', $comment) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا التعليق؟') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 text-xs transition">
                                                {{ __('حذف') }}
                                            </button>
                                        </form>
                                    </div>
                                    <p class="mt-1 text-gray-700 dark:text-gray-300 break-words">{{ $comment->body }}</p>
                                    <p class="mt-2 text-xs text-yellow-700 dark:text-yellow-300">
                                        ℹ️ {{ __('تم إرسال تعليقك. سيظهر بعد موافقة المدقق.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach

                        {{-- ✅ تعليقاتي المرفوضة (تظهر لي فقط) --}}
                        @foreach($myRejectedComments as $comment)
                            <div class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border-2 border-red-300 dark:border-red-700">
                                <div class="w-10 h-10 rounded-full bg-red-200 dark:bg-red-800 flex items-center justify-center text-red-800 dark:text-red-200 font-bold flex-shrink-0">
                                    {{ mb_substr($comment->user->name, 0, 2) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div>
                                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $comment->user->name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">· {{ $comment->created_at->diffForHumans() }}</span>
                                            <span class="px-2 py-0.5 bg-red-200 dark:bg-red-800 text-red-800 dark:text-red-200 text-xs font-bold rounded-full">
                                                ❌ {{ __('مرفوض') }}
                                            </span>
                                        </div>
                                        <form action="{{ route('shop.comment.destroy', $comment) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا التعليق؟') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 text-xs transition">
                                                {{ __('حذف') }}
                                            </button>
                                        </form>
                                    </div>
                                    <p class="mt-1 text-gray-700 dark:text-gray-300 break-words">{{ $comment->body }}</p>
                                    @if($comment->rejection_reason)
                                        <p class="mt-2 text-xs text-red-700 dark:text-red-300">
                                            ⚠️ {{ __('سبب الرفض:') }} {{ $comment->rejection_reason }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        {{-- ✅ التعليقات الموافق عليها (للجميع) --}}
                        @foreach($approvedComments as $comment)
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