<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('مراجعة المنتجات التسويقية') }}
            </h2>
            <a href="{{ route('products.review.history') }}"
               class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                📜 {{ __('تاريخ مراجعاتي') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- رسائل التنبيه --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center">
                    <div class="rounded-full bg-yellow-100 dark:bg-yellow-900/30 p-3 ml-4">
                        <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('في انتظار المراجعة') }}</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center">
                    <div class="rounded-full bg-green-100 dark:bg-green-900/30 p-3 ml-4">
                        <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['reviewed_today'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('تمت مراجعتها اليوم') }}</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 flex items-center">
                    <div class="rounded-full bg-indigo-100 dark:bg-indigo-900/30 p-3 ml-4">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['my_history'] }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ __('إجمالي مراجعاتي') }}</div>
                    </div>
                </div>
            </div>

            {{-- قائمة الانتظار --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                        📋 {{ __('قائمة انتظار المراجعة') }}
                        <span class="text-sm font-normal text-gray-500 dark:text-gray-400 mr-2">(FIFO - الأقدم أولاً)</span>
                    </h3>
                </div>

                @if($pendingProducts->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المنتج') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المتخصص') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('النوع') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('السعر') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('تاريخ التقديم') }}</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجراء') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($pendingProducts as $product)
                                    @php
                                        $isLocked = $product->isLocked();
                                        $isLockedByMe = $product->isLockedByCurrentUser();
                                    @endphp
                                    <tr class="{{ $isLocked && !$isLockedByMe ? 'opacity-50' : '' }}">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-800 dark:text-gray-200">{{ $product->title }}</div>
                                            @if($product->status === 'pending_edit')
                                                <span class="text-xs text-yellow-600 dark:text-yellow-400">✏️ {{ __('نسخة تعديل') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $product->author->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm">
                                            @if($product->isPhysical())
                                                🏺 {{ __('مادي') }}
                                            @else
                                                💾 {{ __('رقمي') }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ number_format($product->price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $product->queued_at?->diffForHumans() }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($isLocked && !$isLockedByMe)
                                                <span class="inline-flex items-center px-3 py-1.5 bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300 text-xs font-medium rounded-full">
                                                    🔒 {{ __('محجوز') }}
                                                </span>
                                            @else
                                                <a href="{{ route('products.review.preview', $product) }}"
                                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full transition">
                                                    {{ $isLockedByMe ? '🔄 متابعة' : '👁️ معاينة' }}
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($pendingProducts->hasPages())
                        <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $pendingProducts->links() }}
                        </div>
                    @endif
                @else
                    <div class="px-6 py-16 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('لا توجد منتجات في انتظار المراجعة حالياً.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>