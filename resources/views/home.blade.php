<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('الفلكلور') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- آخر المحتوى المنشور --}}
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        آخر المحتوى المنشور
                    </h3>
                    @forelse ($latestContents as $item)
                        {{-- بطاقة محتوى لاحقًا --}}
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            لا يوجد محتوى منشور بعد.
                        </p>
                    @endforelse
                </div>
            </section>

            {{-- آخر المنتجات المنشورة --}}
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        آخر المنتجات المنشورة
                    </h3>
                    @forelse ($latestProducts as $item)
                        {{-- بطاقة منتج لاحقًا --}}
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            لا توجد منتجات منشورة بعد.
                        </p>
                    @endforelse
                </div>
            </section>

            {{-- أكثر المواضيع مشاركة (تعليقات) --}}
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        أكثر المواضيع مشاركة
                    </h3>
                    @forelse ($mostCommented as $item)
                        {{-- لاحقًا --}}
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            لا توجد بيانات مشاركة بعد.
                        </p>
                    @endforelse
                </div>
            </section>

            {{-- أكثر المواضيع اهتمامًا (إعجابات) --}}
            <section class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        أكثر المواضيع اهتمامًا
                    </h3>
                    @forelse ($mostLiked as $item)
                        {{-- لاحقًا --}}
                    @empty
                        <p class="text-gray-500 dark:text-gray-400 text-sm">
                            لا توجد بيانات اهتمام بعد.
                        </p>
                    @endforelse
                </div>
            </section>

        </div>
    </div>
</x-app-layout>