<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('إنشاء محتوى جديد') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">
                <div class="p-6 lg:p-8">

                    {{-- رسائل التنبيه --}}
                    @if ($errors->any())
                        <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('content.store') }}" enctype="multipart/form-data">
                        @csrf

                        {{-- العنوان --}}
                        <div class="mb-5">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                العنوان <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title" 
                                   value="{{ old('title') }}" 
                                   required
                                   placeholder="أدخل عنوان المحتوى..."
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                            @error('title')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- النوع --}}
                        <div class="mb-5">
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                النوع <span class="text-red-500">*</span>
                            </label>
                            <select name="type" 
                                    id="type" 
                                    required
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                                <option value="">اختر النوع...</option>
                                <option value="article" {{ old('type') == 'article' ? 'selected' : '' }}>📝 مقال</option>
                                <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>🖼️ صورة</option>
                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>🎬 فيديو</option>
                                <option value="audio" {{ old('type') == 'audio' ? 'selected' : '' }}>🎵 صوت</option>
                            </select>
                            @error('type')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- المحتوى النصي --}}
                        <div class="mb-5">
                            <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                المحتوى (نص)
                            </label>
                            <textarea name="body" 
                                      id="body" 
                                      rows="8"
                                      placeholder="اكتب محتوى المقال هنا..."
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">{{ old('body') }}</textarea>
                            @error('body')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الكلمات المفتاحية --}}
                        <div class="mb-5">
                            <label for="keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                الكلمات المفتاحية
                            </label>
                            <input type="text" 
                                   name="keywords" 
                                   id="keywords" 
                                   value="{{ old('keywords') }}"
                                   placeholder="مثال: تراث، فلكلور، أساطير (مفصولة بفواصل)"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                            @error('keywords')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الأهمية التاريخية --}}
                        <div class="mb-5">
                            <label for="historical_importance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                الأهمية التاريخية
                            </label>
                            <textarea name="historical_importance" 
                                      id="historical_importance" 
                                      rows="3"
                                      placeholder="ما أهمية هذا المحتوى تاريخياً؟"
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">{{ old('historical_importance') }}</textarea>
                            @error('historical_importance')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- الموقع الجغرافي --}}
                        <div class="mb-5">
                            <label for="geographic_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                الموقع الجغرافي
                            </label>
                            <input type="text" 
                                   name="geographic_location" 
                                   id="geographic_location" 
                                   value="{{ old('geographic_location') }}"
                                   placeholder="مثال: دمشق، سوريا"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200">
                            @error('geographic_location')
                                <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- التصنيفات والأوسمة --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                            {{-- التصنيفات --}}
                            <div>
                                <label for="categories" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    التصنيفات
                                </label>
                                <select name="categories[]" 
                                        id="categories" 
                                        multiple
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200"
                                        style="min-height: 100px;">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                            {{ in_array($category->id, old('categories', [])) ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">اضغط Ctrl (أو Cmd) لتحديد عدة تصنيفات</p>
                                @error('categories')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- الأوسمة --}}
                            <div>
                                <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    الأوسمة
                                </label>
                                <select name="tags[]" 
                                        id="tags" 
                                        multiple
                                        class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-200"
                                        style="min-height: 100px;">
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" 
                                            {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">اضغط Ctrl (أو Cmd) لتحديد عدة أوسمة</p>
                                @error('tags')
                                    <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- ✅ رفع الوسائط (داخل الفورم الآن) --}}
                        <div class="mb-5">
                            <label for="media" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('الوسائط (صور / فيديو / صوت)') }}
                                <span class="text-xs text-gray-400">(اختياري - يمكن اختيار عدة ملفات)</span>
                            </label>
                            <input type="file" name="media[]" id="media" multiple
                                   accept="image/*,video/*,audio/*"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/30 dark:file:text-indigo-300">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ __('الصيغ المدعومة: jpg, png, webp, mp4, mp3. الحد الأقصى 50MB.') }}
                            </p>
                            @error('media') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                            @error('media.*') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        {{-- أزرار --}}
                        <div class="flex flex-wrap items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('content.index') }}" 
                               class="px-6 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-full transition duration-200">
                                إلغاء
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 rounded-full shadow-md hover:shadow-lg transition duration-200">
                                💾 حفظ كمسودة
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>