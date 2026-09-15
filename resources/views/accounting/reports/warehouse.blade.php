<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="حالة المستودع" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.reports.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('التقارير') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    🏪 {{ __('حالة المستودع') }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('accounting.reports.warehouse.excel', request()->query()) }}"
                   class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-full transition">
                    📊 {{ __('Excel') }}
                </a>
                <button onclick="window.print()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-full transition">
                    🖨️ {{ __('طباعة') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total_products'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('إجمالي المنتجات') }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['total_quantity'], 0) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('إجمالي القطع') }}</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($stats['total_value'], 2) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('قيمة المخزون') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['out_of_stock'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('نفد المخزون') }}</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['low_stock'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مخزون منخفض') }}</div>
                </div>
            </div>

            {{-- الفلاتر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('accounting.reports.warehouse') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <select name="category_id"
                                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">{{ __('كل التصنيفات') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') === $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>

                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="out_of_stock" value="1" {{ request('out_of_stock') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-red-500 focus:ring-red-500">
                            🔴 {{ __('نفد المخزون فقط') }}
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-700 dark:text-gray-300">
                            <input type="checkbox" name="low_stock" value="1" {{ request('low_stock') ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500">
                            🟡 {{ __('مخزون منخفض (≤ 5)') }}
                        </label>

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition text-sm">
                                🔍 {{ __('تصفية') }}
                            </button>
                            @if(request()->anyFilled(['category_id', 'out_of_stock', 'low_stock']))
                                <a href="{{ route('accounting.reports.warehouse') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-xl text-sm">✖</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- الجدول --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                @if($products->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المنتج') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التصنيف') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الكمية') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('السعر') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('القيمة') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الحالة') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($products as $product)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 dark:bg-gray-700 flex-shrink-0">
                                                    @if($product->cover_image)
                                                        <img src="{{ $product->cover_image }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center">🏺</div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-800 dark:text-gray-200">{{ $product->title }}</div>
                                                    @if($product->sku)
                                                        <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            @if($product->categories->count())
                                                {{ $product->categories->first()->name }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="text-lg font-bold {{ $product->stock_quantity > 5 ? 'text-green-600 dark:text-green-400' : ($product->stock_quantity > 0 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}">
                                                {{ $product->stock_quantity }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            ${{ number_format($product->effective_price, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            ${{ number_format($product->stock_quantity * $product->effective_price, 2) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($product->stock_quantity <= 0)
                                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                                    🔴 {{ __('نفد') }}
                                                </span>
                                            @elseif($product->stock_quantity <= 5)
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                                    🟡 {{ __('منخفض') }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                                    🟢 {{ __('متوفر') }}
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($products->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $products->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="text-8xl mb-4">🏪</div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('لا توجد منتجات') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('لا توجد منتجات مادية في المستودع.') }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>