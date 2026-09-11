<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('تاريخ مراجعاتي للمنتجات') }}
            </h2>
            <a href="{{ route('products.review.index') }}"
               class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← {{ __('قائمة الانتظار') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        📜 {{ __('المنتجات التي راجعتها') }}
                    </h3>
                </div>

                @if($history->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المنتج') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المتخصص') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('القرار') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('تاريخ المراجعة') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجراء') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($history as $product)
                                    <tr>
                                        <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">
                                            {{ $product->title }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $product->author->name }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($product->status === 'published')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 text-xs font-semibold rounded-full">
                                                    ✅ {{ __('منشور') }}
                                                </span>
                                            @elseif($product->status === 'rejected')
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-semibold rounded-full">
                                                    ❌ {{ __('مرفوض') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs font-semibold rounded-full">
                                                    {{ $product->status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $product->reviewed_at?->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('products.show', $product) }}"
                                               class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                                                {{ __('عرض') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($history->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $history->links() }}
                        </div>
                    @endif
                @else
                    <div class="px-6 py-16 text-center">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('لم تقم بأي مراجعة للمنتجات حتى الآن.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>