<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="إتمام الشراء" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ✅ {{ __('إتمام الشراء') }}
            </h2>
            <a href="{{ route('cart.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← {{ __('العودة للسلة') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('checkout.store') }}">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- بيانات الشحن والدفع --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- معلومات الشحن --}}
                        @if($hasPhysical)
                            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                                    📦 {{ __('معلومات الشحن') }}
                                    <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300 rounded-full">
                                        {{ __('مطلوب') }}
                                    </span>
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="shipping_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('الاسم الكامل') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="shipping_name" id="shipping_name"
                                               value="{{ old('shipping_name', auth()->user()->name) }}" required
                                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="shipping_phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('رقم الهاتف') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="tel" name="shipping_phone" id="shipping_phone"
                                               value="{{ old('shipping_phone') }}" required dir="ltr"
                                               placeholder="+963 9XX XXX XXX"
                                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div>
                                        <label for="shipping_city" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('المدينة') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="shipping_city" id="shipping_city"
                                               value="{{ old('shipping_city') }}" required
                                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="shipping_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            {{ __('العنوان التفصيلي') }} <span class="text-red-500">*</span>
                                        </label>
                                        <textarea name="shipping_address" id="shipping_address" rows="3" required
                                                  placeholder="{{ __('الحي، الشارع، رقم المبنى...') }}"
                                                  class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('shipping_address') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-2xl p-4">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    💾 {{ __('طلبك يحتوي على منتجات رقمية فقط — لا يحتاج شحن.') }}
                                </p>
                            </div>
                        @endif

                        {{-- طريقة الدفع --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">
                                💳 {{ __('طريقة الدفع') }}
                            </h3>

                            <div class="space-y-3">
                                <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition hover:border-indigo-500 {{ old('payment_method', 'cash_on_delivery') === 'cash_on_delivery' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input type="radio" name="payment_method" value="cash_on_delivery"
                                           {{ old('payment_method', 'cash_on_delivery') === 'cash_on_delivery' ? 'checked' : '' }}
                                           class="text-indigo-600 focus:ring-indigo-500">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">
                                            💵 {{ __('الدفع عند الاستلام') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('تدفع نقداً عند وصول المنتج. (للمنتجات المادية)') }}
                                        </div>
                                    </div>
                                </label>

                                <label class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition hover:border-indigo-500 {{ old('payment_method') === 'bank_transfer' ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-300 dark:border-gray-600' }}">
                                    <input type="radio" name="payment_method" value="bank_transfer"
                                           {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }}
                                           class="text-indigo-600 focus:ring-indigo-500">
                                    <div class="flex-1">
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">
                                            🏦 {{ __('تحويل بنكي') }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ __('سيتم التواصل معك لبيانات الحساب البنكي.') }}
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- ملاحظات --}}
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                📝 {{ __('ملاحظات إضافية (اختياري)') }}
                            </label>
                            <textarea name="notes" id="notes" rows="3"
                                      placeholder="{{ __('أي ملاحظات تريد إضافتها للطلب...') }}"
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    {{-- ملخص الطلب --}}
                    <div class="lg:col-span-1">
                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 sticky top-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">
                                📋 {{ __('ملخص الطلب') }}
                            </h3>

                            {{-- قائمة المنتجات --}}
                            <div class="space-y-3 max-h-60 overflow-y-auto pb-4 border-b border-gray-200 dark:border-gray-700">
                                @foreach($cart->items as $item)
                                    @if($item->product)
                                        <div class="flex items-start gap-3">
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                                @if($item->product->cover_image)
                                                    <img src="{{ $item->product->cover_image }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-xl">
                                                        {{ $item->product->isPhysical() ? '🏺' : '💾' }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-medium text-gray-800 dark:text-gray-200 line-clamp-1">
                                                    {{ $item->product->title }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $item->quantity }} × {{ number_format($item->product->effective_price, 2) }}
                                                </p>
                                            </div>
                                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ number_format($item->subtotal, 2) }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- الإجماليات --}}
                            <div class="space-y-2 py-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('المجموع الفرعي') }}</span>
                                    <span class="font-semibold text-gray-800 dark:text-gray-200">
                                        {{ number_format($cart->subtotal, 2) }}
                                    </span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('الشحن') }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ __('مجاني') }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex justify-between py-4 border-t border-gray-200 dark:border-gray-700">
                                <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ __('الإجمالي') }}</span>
                                <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ number_format($cart->subtotal, 2) }}
                                </span>
                            </div>

                            <button type="submit"
                                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md hover:shadow-lg transition flex items-center justify-center gap-2">
                                ✅ {{ __('تأكيد الطلب') }}
                            </button>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>