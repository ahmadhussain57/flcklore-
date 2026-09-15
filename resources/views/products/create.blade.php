<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('إضافة منتج جديد') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 lg:p-8">

                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- ✅ إضافة enctype="multipart/form-data" --}}
                    <form method="POST" action="{{ route('products.store') }}" enctype="multipart/form-data" x-data="{ type: '{{ old('type', 'physical') }}' }">
                        @csrf

                        {{-- العنوان --}}
                        <div class="mb-5">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('اسم المنتج') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('title') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- النوع --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('نوع المنتج') }} <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="physical" x-model="type" class="sr-only peer">
                                    <div class="p-4 border-2 rounded-xl text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 border-gray-200 dark:border-gray-600 hover:border-indigo-300 transition">
                                        <div class="text-3xl mb-1">🏺</div>
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ __('مادي') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('يحتاج مخزون') }}</div>
                                    </div>
                                </label>
                                <label class="cursor-pointer">
                                    <input type="radio" name="type" value="digital" x-model="type" class="sr-only peer">
                                    <div class="p-4 border-2 rounded-xl text-center peer-checked:border-indigo-500 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/30 border-gray-200 dark:border-gray-600 hover:border-indigo-300 transition">
                                        <div class="text-3xl mb-1">💾</div>
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ __('رقمي') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('بدون مخزون') }}</div>
                                    </div>
                                </label>
                            </div>
                            @error('type') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- الوصف المختصر --}}
                        <div class="mb-5">
                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('وصف مختصر') }}
                            </label>
                            <input type="text" name="short_description" id="short_description" value="{{ old('short_description') }}" maxlength="500"
                                   placeholder="جملة قصيرة تظهر في بطاقة المنتج"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('short_description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- ✅ الكلمات المفتاحية --}}
                        <div class="mb-5">
                            <label for="keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('الكلمات المفتاحية') }}
                                <span class="text-xs text-gray-400">(اختياري - مفصولة بفواصل)</span>
                            </label>
                            <input type="text" name="keywords" id="keywords" value="{{ old('keywords') }}" maxlength="500"
                                   placeholder="مثال: تراث، فخار، حرف يدوية، تحف"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                💡 {{ __('تفصل بين الكلمات بفاصلة (,) — ستظهر كروابط في صفحة المنتج للبحث.') }}
                            </p>
                            @error('keywords') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- الوصف الكامل --}}
                        <div class="mb-5">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('الوصف الكامل') }}
                            </label>
                            <textarea name="description" id="description" rows="5"
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                            @error('description') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- الأسعار --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('السعر الأصلي') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="price" id="price" value="{{ old('price') }}" required
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('price') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="sale_price" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('سعر التخفيض') }} <span class="text-xs text-gray-400">(اختياري)</span>
                                </label>
                                <input type="number" step="0.01" min="0" name="sale_price" id="sale_price" value="{{ old('sale_price') }}"
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('sale_price') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- المخزون (يظهر فقط للمنتجات المادية) --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            <div x-show="type === 'physical'" x-transition>
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('الكمية المتوفرة') }} <span class="text-red-500" x-show="type === 'physical'">*</span>
                                </label>
                                <input type="number" min="0" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity') }}"
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('stock_quantity') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('رمز المنتج (SKU)') }} <span class="text-xs text-gray-400">(اختياري)</span>
                                </label>
                                <input type="text" name="sku" id="sku" value="{{ old('sku') }}"
                                       placeholder="مثال: SKU-001"
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('sku') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- التصنيفات --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('التصنيفات') }}
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2 p-4 border border-gray-300 dark:border-gray-600 rounded-xl bg-gray-50 dark:bg-gray-900/30">
                                @forelse($categories as $category)
                                    <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                               {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        {{ $category->name }}
                                    </label>
                                @empty
                                    <div class="col-span-full text-center text-gray-500 dark:text-gray-400 text-sm">
                                        {{ __('لا توجد تصنيفات بعد.') }}
                                    </div>
                                @endforelse
                            </div>
                            @error('categories') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- ✅ رفع الوسائط (داخل الفورم) --}}
                        <div class="mb-5">
                            <label for="media" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('صور / فيديو المنتج') }}
                                <span class="text-xs text-gray-400">(اختياري - يمكن اختيار عدة ملفات)</span>
                            </label>
                            <input type="file" name="media[]" id="media" multiple
                                   accept="image/*,video/*"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('الصيغ المدعومة: jpg, png, webp, mp4. الحد الأقصى 50MB.') }}
                            </p>
                            @error('media') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            @error('media.*') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- الأزرار --}}
                        <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('products.index') }}"
                               class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full">
                                {{ __('إلغاء') }}
                            </a>
                            <button type="submit"
                                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-full shadow-md">
                                💾 {{ __('حفظ كمسودة') }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>