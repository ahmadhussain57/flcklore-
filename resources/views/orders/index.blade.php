<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="طلباتي" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📦 {{ __('طلباتي') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            @if($orders->count())
                <div class="space-y-4">
                    @foreach($orders as $order)
                        <a href="{{ route('orders.show', $order) }}"
                           class="block bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-bold text-gray-800 dark:text-gray-200">
                                            #{{ $order->order_number }}
                                        </span>
                                        @php
                                            $statusClasses = [
                                                'pending'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                'paid'       => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                'shipped'    => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                                'cancelled'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                            ];
                                        @endphp
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                            {{ $order->status_label }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $order->placed_at->format('Y-m-d H:i') }} • {{ $order->total_items }} {{ __('منتج') }}
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ number_format($order->total, 2) }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-1">
                                        {{ $order->payment_method_label }}
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if($orders->hasPages())
                    <div class="mt-6">
                        {{ $orders->links() }}
                    </div>
                @endif
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">
                    <div class="text-8xl mb-4">📦</div>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('لا توجد طلبات') }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">
                        {{ __('لم تقم بأي عملية شراء بعد.') }}
                    </p>
                    <a href="{{ route('shop.index') }}"
                       class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-full shadow-md transition">
                        🛍️ {{ __('تصفح المتجر') }}
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>