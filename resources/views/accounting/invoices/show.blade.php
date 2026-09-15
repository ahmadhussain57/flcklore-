<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="فاتورة {{ $invoice->invoice_number }}" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.invoices.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('العودة للفواتير') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    🧾 {{ __('فاتورة') }} {{ $invoice->invoice_number }}
                </h2>
            </div>

            <div class="flex gap-2">
                <button onclick="window.print()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-full transition">
                    🖨️ {{ __('طباعة') }}
                </button>
                @if(!$journalEntry)
                    <form method="POST" action="{{ route('accounting.invoices.destroy', $invoice) }}"
                          onsubmit="return confirm('{{ __('حذف هذه الفاتورة؟') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-sm font-semibold rounded-full transition">
                            🗑️ {{ __('حذف') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- رأس الفاتورة --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-6 mb-6">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('رقم الفاتورة') }}</div>
                        <div class="text-2xl font-bold font-mono text-gray-800 dark:text-gray-200">
                            {{ $invoice->invoice_number }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            📅 {{ $invoice->invoice_date->format('Y-m-d') }}
                        </div>
                        @if($invoice->due_date)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                ⏰ {{ __('الاستحقاق:') }} {{ $invoice->due_date->format('Y-m-d') }}
                            </div>
                        @endif
                    </div>

                    <div class="text-end">
                        @php
                            $typeColors = [
                                'purchase_wholesale' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'purchase_retail' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'sale_wholesale' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'sale_retail' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'sale_return' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            ];
                            $paymentColors = [
                                'unpaid' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                'partial' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            ];
                        @endphp
                        <div class="flex flex-wrap gap-2 justify-end">
                            <span class="px-3 py-1.5 text-sm font-bold rounded-full {{ $typeColors[$invoice->type] ?? '' }}">
                                {{ $invoice->type_label }}
                            </span>
                            <span class="px-3 py-1.5 text-sm font-bold rounded-full {{ $paymentColors[$invoice->payment_status] ?? '' }}">
                                {{ $invoice->payment_status_label }}
                            </span>
                            <span class="px-3 py-1.5 text-sm font-bold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                {{ $invoice->payment_type_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('الطرف') }}</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $invoice->party_name }}</div>
                        @if($invoice->party_phone)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1" dir="ltr">📱 {{ $invoice->party_phone }}</div>
                        @endif
                        @if($invoice->party_address)
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">📍 {{ $invoice->party_address }}</div>
                        @endif
                    </div>

                    <div class="text-end">
                        @if($invoice->creator)
                            <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('أنشأها') }}</div>
                            <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $invoice->creator->name }}</div>
                        @endif
                        @if($invoice->order)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ __('مرتبطة بالطلب:') }}
                                <a href="{{ route('admin.orders.show', $invoice->order) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    #{{ $invoice->order->order_number }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- العناصر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">📦 {{ __('العناصر') }} ({{ $invoice->items->count() }})</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المنتج') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الكمية') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('السعر') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الخصم') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المجموع') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $item->product_title }}</div>
                                        @if($item->product_sku)
                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->product_sku }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">
                                        {{ $item->discount > 0 ? '-$' . number_format($item->discount, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-bold text-gray-800 dark:text-gray-200">
                                        ${{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- الإجماليات --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <div class="max-w-md ms-auto space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('المجموع الفرعي') }}</span>
                        <span class="font-semibold text-gray-800 dark:text-gray-200">${{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    @if($invoice->discount > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('الخصم') }}</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">-${{ number_format($invoice->discount, 2) }}</span>
                        </div>
                    @endif
                    @if($invoice->tax > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('الضريبة') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">${{ number_format($invoice->tax, 2) }}</span>
                        </div>
                    @endif
                    @if($invoice->shipping_cost > 0)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('الشحن') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">${{ number_format($invoice->shipping_cost, 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-3 border-t-2 border-gray-200 dark:border-gray-700">
                        <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ __('الإجمالي') }}</span>
                        <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($invoice->total, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm pt-3 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('المدفوع') }}</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">${{ number_format($invoice->paid_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ __('المتبقي') }}</span>
                        <span class="font-semibold {{ $invoice->remaining_amount > 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            ${{ number_format($invoice->remaining_amount, 2) }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- الملاحظات --}}
            @if($invoice->notes)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-3">📝 {{ __('ملاحظات') }}</h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $invoice->notes }}</p>
                </div>
            @endif

            {{-- السند المرتبط --}}
            @if($journalEntry)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-3">📒 {{ __('السند المرتبط') }}</h3>
                    <a href="{{ route('accounting.journal-entries.show', $journalEntry) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-full transition">
                        {{ $journalEntry->entry_number }} →
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>