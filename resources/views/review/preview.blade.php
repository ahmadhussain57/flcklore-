<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('معاينة المحتوى') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- معلومات المحتوى -->
                    <h1 class="text-2xl font-bold mb-4">{{ $content->title }}</h1>
                    <div class="grid grid-cols-2 gap-4 mb-6 text-sm">
                        <div><strong>المؤلف:</strong> {{ $content->author->name }}</div>
                        <div><strong>النوع:</strong> {{ $content->type }}</div>
                        <div><strong>التصنيفات:</strong> {{ $content->categories->pluck('name')->join(', ') }}</div>
                        <div><strong>الأوسمة:</strong> {{ $content->tags->pluck('name')->join(', ') }}</div>
                        @if($content->geographic_location)
                            <div><strong>الموقع:</strong> {{ $content->geographic_location }}</div>
                        @endif
                        @if($content->historical_importance)
                            <div><strong>الأهمية التاريخية:</strong> {{ $content->historical_importance }}</div>
                        @endif
                    </div>

                    <!-- النص الأساسي -->
                    @if($content->body)
                        <div class="prose dark:prose-invert max-w-none mb-6">
                            {!! nl2br(e($content->body)) !!}
                        </div>
                    @endif

                    <!-- الوسائط (إن وجدت) -->
                    @if($content->media->count())
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">الوسائط</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                @foreach($content->media as $media)
                                    <div class="border rounded p-2">
                                        <img src="{{ $media->path }}" alt="{{ $media->original_name }}" class="w-full h-32 object-cover rounded">
                                        <p class="text-xs mt-1">{{ $media->original_name }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- نموذج المراجعة -->
                    <form action="{{ route('review.reject', $content) }}" method="POST" class="mt-6 border-t pt-6">
                        @csrf
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-4">
                                <button type="submit" form="approve-form"
                                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500">
                                    ✅ موافقة
                                </button>
                                <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500">
                                    ❌ رفض
                                </button>
                            </div>
                            <a href="{{ route('review.index') }}"
                               class="text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                                ← العودة للقائمة
                            </a>
                        </div>

                        <!-- سبب الرفض -->
                        <div class="mt-4">
                            <label for="rejection_reason" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                سبب الرفض (إجباري عند الرفض)
                            </label>
                            <textarea name="rejection_reason" id="rejection_reason" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                      placeholder="اذكر سبب رفض المحتوى..."></textarea>
                        </div>
                    </form>

                    <!-- نموذج الموافقة (منفصل لتجنب تداخل الأزرار) -->
                    <form id="approve-form" action="{{ route('review.approve', $content) }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>