<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="تقرير حركة المواد" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.reports.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('التقارير') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    📦 {{ __('تقرير حركة المواد') }}
                </h2>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('accounting.reports.material-movement.excel', request()->query()) }}"
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
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-green-50 dark:bg-green-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['total_in'], 0) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">📥 {{ __('إجمالي الدخول') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['total_out'], 0) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">📤 {{ __('إجمالي الخروج') }}</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['total_adjustments'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">⚙️ {{ __('التسويات') }}</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($stats['total_cost'], 2) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">💰 {{ __('القيمة الإجمالية') }}</div>
                </div>
            </div>

            {{-- الفلاتر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('accounting.reports.material-movement') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <select name="product_id"
                                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">{{ __('كل المنتجات') }}</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') === $product->id ? 'selected' : '' }}>
                                    {{ $product->title }}
                                </option>
                            @endforeach
                        </select>

                        <select name="movement_type"
                                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">{{ __('كل الأنواع') }}</option>
                            <option value="purchase" {{ request('movement_type') === 'purchase' ? 'selected' : '' }}>📥 {{ __('شراء') }}</option>
                            <option value="sale" {{ request('movement_type') === 'sale' ? 'selected' : '' }}>📤 {{ __('بيع') }}</option>
                            <option value="return" {{ request('movement_type') === 'return' ? 'selected' : '' }}>↩️ {{ __('مرتجع') }}</option>
                            <option value="adjustment" {{ request('movement_type') === 'adjustment' ? 'selected' : '' }}>⚙️ {{ __('تسوية') }}</option>
                        </select>

                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                               class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">

                        <input type="date" name="to_date" value="{{ request('to_date') }}"
                               class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition text-sm">
                                🔍 {{ __('تصفية') }}
                            </button>
                            @if(request()->anyFilled(['product_id', 'movement_type', 'from_date', 'to_date']))
                                <a href="{{ route('accounting.reports.material-movement') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-xl text-sm">✖</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- الجدول --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                @if($movements->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التاريخ') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المنتج') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('النوع') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الكمية') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('قبل') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('بعد') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التكلفة') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('البيان') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($movements as $movement)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                            {{ $movement->movement_date->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ $movement->product?->title ?? __('منتج محذوف') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $typeColors = [
                                                    'purchase' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'sale' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                    'return' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'adjustment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $typeColors[$movement->movement_type] ?? '' }}">
                                                {{ $movement->movement_type_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold {{ $movement->quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                            {{ $movement->quantity > 0 ? '+' : '' }}{{ number_format($movement->quantity, 0) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ number_format($movement->stock_before, 0) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-gray-800 dark:text-gray-200">
                                            {{ number_format($movement->stock_after, 0) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            ${{ number_format($movement->total_cost, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 max-w-xs truncate">
                                            {{ $movement->description ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($movements->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $movements->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="text-8xl mb-4">📦</div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('لا توجد حركات') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('لم يتم تسجيل أي حركة مواد بعد.') }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>