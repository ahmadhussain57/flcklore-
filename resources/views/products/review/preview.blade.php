<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('معاينة المنتج') }}
            </h2>
            <a href="{{ route('products.review.index') }}"
               class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← {{ __('العودة إلى قائمة الانتظار') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- العمود الرئيسي: تفاصيل المنتج --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                        <div class="p-6 lg:p-8">

                            {{-- تنبيه الحجز --}}
                            <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/30 border-l-4 border-blue-500 rounded-lg">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    🔒 {{ __('هذا المنتج محجوز لك لمدة 15 دقيقة. يرجى اتخاذ قرار سريع.') }}
                                </p>
                            </div>

                            {{-- الحالة والنوع --}}
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                @if($product->status === 'pending_edit')
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        ✏️ {{ __('نسخة تعديل') }}
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                        🆕 {{ __('جديد') }}
                                    </span>
                                @endif
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                    {{ $product->isPhysical() ? '🏺 مادي' : '💾 رقمي' }}
                                </span>
                            </div>

                            {{-- العنوان --}}
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">{{ $product->title }}</h1>

                            @if($product->short_description)
                                <p class="text-lg text-gray-600 dark:text-gray-400 mb-6">{{ $product->short_description }}</p>
                            @endif

                            {{-- معلومات المنتج --}}
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('السعر الأصلي') }}</div>
                                    <div class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ number_format($product->price, 2) }}</div>
                                </div>
                                @if($product->sale_price)
                                    <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-xl">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('سعر التخفيض') }}</div>
                                        <div class="text-lg font-bold text-green-600 dark:text-green-400">{{ number_format($product->sale_price, 2) }}</div>
                                    </div>
                                @endif
                                @if($product->isPhysical())
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('المخزون') }}</div>
                                        <div class="text-lg font-bold {{ $product->stock_quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $product->stock_quantity }}
                                        </div>
                                    </div>
                                @endif
                                @if($product->sku)
                                    <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">SKU</div>
                                        <div class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $product->sku }}</div>
                                    </div>
                                @endif
                            </div>

                            {{-- الوصف --}}
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

                            {{-- معلومات المتخصص --}}
                            <div class="text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <p>{{ __('مقدم من') }}: <strong class="text-gray-700 dark:text-gray-300">{{ $product->author->name }}</strong></p>
                                <p>{{ __('تاريخ التقديم') }}: {{ $product->queued_at?->format('Y-m-d H:i') }}</p>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- العمود الجانبي: الإجراءات --}}
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sticky top-6">

                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">
                            {{ __('اتخاذ القرار') }}
                        </h3>

                        {{-- زر الموافقة --}}
                        <form action="{{ route('products.review.approve', $product) }}" method="POST" class="mb-4">
                            @csrf
                            <button type="submit"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md transition flex items-center justify-center gap-2"
                                    onclick="return confirm('هل أنت متأكد من الموافقة على نشر هذا المنتج؟')">
                                ✅ {{ __('الموافقة والنشر') }}
                            </button>
                        </form>

                        {{-- فاصل --}}
                        <div class="relative my-6">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-gray-200 dark:border-gray-700"></div>
                            </div>
                            <div class="relative flex justify-center text-sm">
                                <span class="px-3 bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400">{{ __('أو') }}</span>
                            </div>
                        </div>

                        {{-- نموذج الرفض --}}
                        <form action="{{ route('products.review.reject', $product) }}" method="POST">
                            @csrf
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('سبب الرفض') }} <span class="text-red-500">*</span>
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="4" required
                                      placeholder="{{ __('اكتب سبباً واضحاً لرفض المنتج...') }}"
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"></textarea>
                            @error('rejection_reason')
                                <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span>
                            @enderror

                            <button type="submit"
                                    class="w-full mt-3 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition flex items-center justify-center gap-2"
                                    onclick="return confirm('هل أنت متأكد من رفض هذا المنتج؟')">
                                ❌ {{ __('رفض المنتج') }}
                            </button>
                        </form>

                        {{-- زر إلغاء الحجز --}}
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('products.review.index') }}"
                               class="block text-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                                {{ __('العودة دون قرار (سيبقى محجوزاً 15 دقيقة)') }}
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>