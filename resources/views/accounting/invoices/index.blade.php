<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="الفواتير" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                🧾 {{ __('الفواتير') }}
            </h2>
            <a href="{{ route('accounting.invoices.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                + {{ __('فاتورة جديدة') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- رسائل --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    ✗ {{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="p-4 bg-blue-100 dark:bg-blue-900/50 border-l-4 border-blue-500 text-blue-700 dark:text-blue-300 rounded-lg">
                    ℹ️ {{ session('info') }}
                </div>
            @endif

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('المجموع') }}</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['purchases'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('شراء') }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['sales'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مبيع') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['returns'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مرتجع') }}</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($stats['total_sales'], 2) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('إجمالي المبيعات') }}</div>
                </div>
            </div>

            {{-- الفلاتر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('accounting.invoices.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="md:col-span-2">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="{{ __('ابحث برقم الفاتورة أو اسم الطرف...') }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <select name="type"
                                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('كل الأنواع') }}</option>
                            <option value="purchase_wholesale" {{ request('type') === 'purchase_wholesale' ? 'selected' : '' }}>📦 {{ __('شراء جملة') }}</option>
                            <option value="purchase_retail" {{ request('type') === 'purchase_retail' ? 'selected' : '' }}>🛍️ {{ __('شراء مفرق') }}</option>
                            <option value="sale_wholesale" {{ request('type') === 'sale_wholesale' ? 'selected' : '' }}>💼 {{ __('مبيع جملة') }}</option>
                            <option value="sale_retail" {{ request('type') === 'sale_retail' ? 'selected' : '' }}>🛒 {{ __('مبيع مفرق') }}</option>
                            <option value="sale_return" {{ request('type') === 'sale_return' ? 'selected' : '' }}>↩️ {{ __('مرتجع') }}</option>
                        </select>
                        <select name="payment_status"
                                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('حالة الدفع') }}</option>
                            <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>🔴 {{ __('غير مدفوع') }}</option>
                            <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>🟡 {{ __('مدفوع جزئياً') }}</option>
                            <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>🟢 {{ __('مدفوع') }}</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition">
                                🔍 {{ __('تصفية') }}
                            </button>
                            @if(request()->anyFilled(['q', 'type', 'payment_status']))
                                <a href="{{ route('accounting.invoices.index') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-xl">✖</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- الجدول --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                @if($invoices->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('رقم الفاتورة') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('النوع') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الطرف') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التاريخ') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجمالي') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('حالة الدفع') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($invoices as $invoice)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 font-mono text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ $invoice->invoice_number }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $typeColors = [
                                                    'purchase_wholesale' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'purchase_retail' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'sale_wholesale' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'sale_retail' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'sale_return' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $typeColors[$invoice->type] ?? '' }}">
                                                {{ $invoice->type_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $invoice->party_name }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $invoice->invoice_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold text-gray-800 dark:text-gray-200">
                                            ${{ number_format($invoice->total, 2) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $paymentColors = [
                                                    'unpaid' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                    'partial' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $paymentColors[$invoice->payment_status] ?? '' }}">
                                                {{ $invoice->payment_status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('accounting.invoices.show', $invoice) }}"
                                                   class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full transition">
                                                    👁️ {{ __('عرض') }}
                                                </a>
                                                <form method="POST" action="{{ route('accounting.invoices.destroy', $invoice) }}"
                                                      onsubmit="return confirm('{{ __('حذف هذه الفاتورة؟') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="px-3 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-xs font-semibold rounded-full transition">
                                                        🗑️
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($invoices->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $invoices->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="text-8xl mb-4">🧾</div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('لا توجد فواتير') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('ابدأ بإنشاء فاتورة جديدة.') }}</p>
                        <a href="{{ route('accounting.invoices.create') }}"
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-full shadow-md transition">
                            + {{ __('فاتورة جديدة') }}
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>