<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('تعديل المحتوى') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="POST" action="{{ route('content.update', $content) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- العنوان --}}
                        <div class="mb-4">
                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">العنوان</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $content->title) }}" required
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- النوع --}}
                        <div class="mb-4">
                            <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">النوع</label>
                            <select name="type" id="type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="article" {{ old('type', $content->type) == 'article' ? 'selected' : '' }}>مقال</option>
                                <option value="image" {{ old('type', $content->type) == 'image' ? 'selected' : '' }}>صورة</option>
                                <option value="video" {{ old('type', $content->type) == 'video' ? 'selected' : '' }}>فيديو</option>
                                <option value="audio" {{ old('type', $content->type) == 'audio' ? 'selected' : '' }}>صوت</option>
                            </select>
                            @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- المحتوى النصي --}}
                        <div class="mb-4">
                            <label for="body" class="block text-sm font-medium text-gray-700 dark:text-gray-300">المحتوى (نص)</label>
                            <textarea name="body" id="body" rows="8"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('body', $content->body) }}</textarea>
                            @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- الكلمات المفتاحية --}}
                        <div class="mb-4">
                            <label for="keywords" class="block text-sm font-medium text-gray-700 dark:text-gray-300">الكلمات المفتاحية</label>
                            <input type="text" name="keywords" id="keywords" value="{{ old('keywords', $content->keywords) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('keywords') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- الأهمية التاريخية --}}
                        <div class="mb-4">
                            <label for="historical_importance" class="block text-sm font-medium text-gray-700 dark:text-gray-300">الأهمية التاريخية</label>
                            <textarea name="historical_importance" id="historical_importance" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('historical_importance', $content->historical_importance) }}</textarea>
                            @error('historical_importance') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- الموقع الجغرافي --}}
                        <div class="mb-4">
                            <label for="geographic_location" class="block text-sm font-medium text-gray-700 dark:text-gray-300">الموقع الجغرافي</label>
                            <input type="text" name="geographic_location" id="geographic_location" value="{{ old('geographic_location', $content->geographic_location) }}"
                                   class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('geographic_location') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- التصنيفات --}}
                        <div class="mb-4">
                            <label for="categories" class="block text-sm font-medium text-gray-700 dark:text-gray-300">التصنيفات</label>
                            <select name="categories[]" id="categories" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ in_array($category->id, old('categories', $content->categories->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categories') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- الأوسمة --}}
                        <div class="mb-4">
                            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">الأوسمة</label>
                            <select name="tags[]" id="tags" multiple
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $content->tags->pluck('id')->toArray())) ? 'selected' : '' }}>
                                        {{ $tag->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tags') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        {{-- أزرار --}}
                        <div class="flex justify-end space-x-4 space-x-reverse">
                            <a href="{{ route('content.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-700 text-white rounded-md">إلغاء</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">تحديث</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>