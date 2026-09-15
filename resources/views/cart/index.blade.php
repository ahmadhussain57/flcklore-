<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="السلة" description="سلة التسوق الخاصة بك." />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                🛒 {{ __('سلة التسوق') }}
            </h2>
            @if($cart->items->count() > 0)
                <form method="POST" action="{{ route('cart.clear') }}"
                      onsubmit="return confirm('{{ __('هل أنت متأكد من تفريغ السلة؟') }}')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="text-sm text-red-600 dark:text-red-400 hover:underline">
                        🗑️ {{ __('تفريغ السلة') }}
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    ✓ {{ session('success') }}
                </div>
            @endif
            {{-- ✅ رسالة خطأ محسّنة --}}
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg flex items-start gap-3">
                    <span class="text-2xl flex-shrink-0">❌</span>
                    <div>
                        <p class="font-semibold mb-1">{{ __('خطأ في العملية') }}</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
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

            @if($cart->items->count() > 0)

                {{-- تحذير إن وُجدت مشاكل في المخزون --}}
                @if($hasIssues)
                    <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/30 border-l-4 border-yellow-500 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200 font-semibold mb-1">
                            ⚠️ {{ __('بعض المنتجات في سلتك غير متوفرة بالكمية المطلوبة') }}
                        </p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                            {{ __('يرجى تعديل الكميات أو إزالة المنتجات غير المتوفرة قبل إتمام الشراء.') }}
                        </p>
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- قائمة المنتجات --}}
                    <div class="lg:col-span-2 space-y-3">
                        @foreach($cart->items as $item)
                            @php $product = $item->product; @endphp
                            @if($product)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4
                                            {{ !$item->stock_ok ? 'border-2 border-red-400 dark:border-red-600' : '' }}">
                                    <div class="flex gap-4">

                                        {{-- صورة --}}
                                        <a href="{{ route('shop.show', $product->slug) }}"
                                           class="flex-shrink-0 w-24 h-24 rounded-xl overflow-hidden bg-gray-100 dark:bg-gray-700">
                                            @if($product->cover_image)
                                                <img src="{{ $product->cover_image }}" alt="{{ $product->title }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-4xl">
                                                    {{ $product->isPhysical() ? '🏺' : '💾' }}
                                                </div>
                                            @endif
                                        </a>

                                        {{-- التفاصيل --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex-1 min-w-0">
                                                    <a href="{{ route('shop.show', $product->slug) }}"
                                                       class="font-bold text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition line-clamp-2">
                                                        {{ $product->title }}
                                                    </a>
                                                    <div class="flex flex-wrap items-center gap-2 mt-1">
                                                        <span class="text-xs px-2 py-0.5 rounded-full
                                                            {{ $product->isPhysical()
                                                                ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'
                                                                : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' }}">
                                                            {{ $product->isPhysical() ? '🏺 مادي' : '💾 رقمي' }}
                                                        </span>
                                                        @if($product->sku)
                                                            <span class="text-xs text-gray-400">SKU: {{ $product->sku }}</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- زر الحذف --}}
                                                <form method="POST" action="{{ route('cart.remove', $item) }}"
                                                      onsubmit="return confirm('{{ __('إزالة هذا المنتج من السلة؟') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 transition"
                                                            title="{{ __('إزالة') }}">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- السعر والكمية --}}
                                            <div class="flex flex-wrap items-center justify-between gap-3 mt-3">
                                                {{-- السعر --}}
                                                <div>
                                                    @if($product->hasDiscount())
                                                        <span class="text-lg font-bold text-green-600 dark:text-green-400">
                                                            {{ number_format($product->sale_price, 2) }}
                                                        </span>
                                                        <span class="text-sm text-gray-400 line-through mr-1">
                                                            {{ number_format($product->price, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">
                                                            {{ number_format($product->price, 2) }}
                                                        </span>
                                                    @endif
                                                </div>

                                                {{-- التحكم بالكمية --}}
                                                <form method="POST" action="{{ route('cart.update', $item) }}"
                                                      class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="flex items-center border border-gray-300 dark:border-gray-600 rounded-xl overflow-hidden">
                                                        <button type="button"
                                                                onclick="decrementQuantity(this)"
                                                                class="px-3 py-1.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                            −
                                                        </button>
                                                        {{-- ✅ max=99 (للسماح بالكتابة) + data-stock للمخزون --}}
                                                        <input type="number"
                                                               name="quantity"
                                                               value="{{ $item->quantity }}"
                                                               min="1"
                                                               max="99"
                                                               data-stock="{{ $product->isPhysical() ? $product->stock_quantity : 99 }}"
                                                               class="w-14 text-center border-0 focus:ring-0 bg-transparent text-gray-800 dark:text-gray-200 font-semibold"
                                                               onchange="this.form.submit()">
                                                        <button type="button"
                                                                onclick="incrementQuantity(this)"
                                                                class="px-3 py-1.5 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                            +
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>

                                            {{-- المجموع الفرعي --}}
                                            <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                                {{ __('المجموع:') }}
                                                <strong class="text-gray-800 dark:text-gray-200">
                                                    {{ number_format($item->subtotal, 2) }}
                                                </strong>
                                            </div>

                                            {{-- تحذير المخزون --}}
                                            @if(!$item->stock_ok)
                                                <div class="mt-2 p-2 bg-red-50 dark:bg-red-900/30 rounded-lg text-xs text-red-700 dark:text-red-300">
                                                    ⚠️ {{ __('الكمية المتوفرة:') }} {{ $product->stock_quantity }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        {{-- زر مواصلة التسوق --}}
                        <div class="pt-2">
                            <a href="{{ route('shop.index') }}"
                               class="inline-flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                ← {{ __('مواصلة التسوق') }}
                            </a>
                        </div>
                    </div>

                    {{-- ملخص الطلب --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 sticky top-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">
                                📋 {{ __('ملخص الطلب') }}
                            </h3>

                            <div class="space-y-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ __('عدد المنتجات') }}
                                    </span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ $cart->total_items }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ __('المجموع الفرعي') }}
                                    </span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ number_format($cart->subtotal, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">
                                        {{ __('الشحن') }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('يُحدَّد عند الدفع') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between py-4">
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                    {{ __('الإجمالي') }}
                                </span>
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($cart->subtotal, 2) }}
                                </span>
                            </div>

                            {{-- زر Checkout --}}
                            @if($hasIssues)
                                <button type="button" disabled
                                        class="w-full py-3 bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400 font-semibold rounded-xl cursor-not-allowed"
                                        title="{{ __('يرجى إصلاح المشاكل أولاً') }}">
                                    ⚠️ {{ __('أكمل الشراء') }}
                                </button>
                                <p class="text-xs text-center text-red-600 dark:text-red-400 mt-2">
                                    {{ __('يوجد مشاكل في السلة') }}
                                </p>
                            @else
                                <a href="{{ route('checkout.index') }}"
                                   class="block w-full text-center py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition">
                                    ✅ {{ __('إتمام الشراء') }}
                                </a>
                            @endif

                            {{-- ملاحظة أمان --}}
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex items-start gap-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span>🔒</span>
                                    <span>{{ __('معلوماتك محمية وآمنة') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- السلة فارغة --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">
                    <div class="text-8xl mb-4">🛒</div>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('سلتك فارغة') }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        {{ __('لم تقم بإضافة أي منتجات بعد. تصفّح المتجر لاكتشاف الكنوز الفلكلورية.') }}
                    </p>
                    <a href="{{ route('shop.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-full shadow-md transition">
                        🛍️ {{ __('تصفح المتجر') }}
                    </a>
                </div>
            @endif

        </div>
    </div>

    {{-- JS بسيط لزرّي +/- --}}
    <script>
        function decrementQuantity(btn) {
            const input = btn.nextElementSibling;
            const current = parseInt(input.value) || 1;
            if (current > 1) {
                input.value = current - 1;
                input.form.submit();
            }
        }

        // ✅ incrementQuantity: يسمح بالوصول إلى 99 (لتفعيل رسالة خطأ Laravel)
        function incrementQuantity(btn) {
            const input = btn.previousElementSibling;
            const current = parseInt(input.value) || 1;
            if (current < 99) {
                input.value = current + 1;
                input.form.submit();
            }
        }
    </script>
</x-app-layout>