<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="تفاصيل الطلب #{{ $order->order_number }}" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('العودة للطلبات') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    📦 {{ __('طلب') }} #{{ $order->order_number }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- رسائل التنبيه --}}
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

            {{-- ✅ رأس الطلب --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <div class="flex flex-wrap items-start justify-between gap-6">

                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('رقم الطلب') }}</div>
                        <div class="text-2xl font-bold text-gray-800 dark:text-gray-200 font-mono">
                            #{{ $order->order_number }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            📅 {{ $order->placed_at->format('Y-m-d H:i') }}
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @php
                            $statusClasses = [
                                'pending'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                'paid'       => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'shipped'    => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                'cancelled'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                            ];
                        @endphp
                        <span class="px-4 py-2 text-sm font-bold rounded-full {{ $statusClasses[$order->status] ?? '' }}">
                            {{ $order->status_label }}
                        </span>

                        @if($order->payment_status === 'paid')
                            <span class="px-4 py-2 text-sm font-bold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                                🟢 {{ __('مدفوع') }}
                            </span>
                        @elseif($order->payment_status === 'refunded')
                            <span class="px-4 py-2 text-sm font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                ↩️ {{ __('مسترد') }}
                            </span>
                        @else
                            <span class="px-4 py-2 text-sm font-bold rounded-full bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                🔴 {{ __('غير مدفوع') }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- أزرار الإجراءات --}}
                @if($order->status !== 'cancelled' && $order->status !== 'shipped')
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-3 uppercase">
                            ⚡ {{ __('الإجراءات السريعة') }}
                        </h3>
                        <div class="flex flex-wrap gap-2">

                            {{-- تأكيد الدفع --}}
                            @if($order->payment_status !== 'paid' && $order->status !== 'cancelled')
                                <form method="POST" action="{{ route('admin.orders.confirmPayment', $order) }}"
                                      onsubmit="return confirm('{{ __('تأكيد استلام الدفع لهذا الطلب؟') }}')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-full shadow transition">
                                        ✅ {{ __('تأكيد الدفع') }}
                                    </button>
                                </form>
                            @endif

                            {{-- توليد سند قيد --}}
                            @if($order->payment_status === 'paid')
                                @php
                                    $existingEntry = \App\Models\JournalEntry::where('reference_type', \App\Models\Order::class)
                                        ->where('reference_id', $order->id)
                                        ->first();
                                @endphp

                                @if($existingEntry)
                                    <a href="{{ route('accounting.journal-entries.show', $existingEntry) }}"
                                       class="px-4 py-2 bg-blue-100 hover:bg-blue-200 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 text-blue-700 dark:text-blue-300 text-sm font-semibold rounded-full transition">
                                        📒 {{ __('عرض سند القيد') }} ({{ $existingEntry->entry_number }})
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.orders.generateJournalEntry', $order) }}"
                                          onsubmit="return confirm('{{ __('توليد سند قيد لهذا الطلب؟') }}')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-full shadow transition">
                                            📒 {{ __('توليد سند قيد') }}
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- ✅ إنشاء فاتورة --}}
                            @if($order->payment_status === 'paid')
                                @php
                                    $existingInvoice = \App\Models\Invoice::where('order_id', $order->id)->first();
                                @endphp

                                @if($existingInvoice)
                                    <a href="{{ route('accounting.invoices.show', $existingInvoice) }}"
                                       class="px-4 py-2 bg-teal-100 hover:bg-teal-200 dark:bg-teal-900/30 dark:hover:bg-teal-900/50 text-teal-700 dark:text-teal-300 text-sm font-semibold rounded-full transition">
                                        🧾 {{ __('عرض الفاتورة') }} ({{ $existingInvoice->invoice_number }})
                                    </a>
                                @else
                                    <form method="POST" action="{{ route('admin.orders.createInvoice', $order) }}"
                                          onsubmit="return confirm('{{ __('إنشاء فاتورة لهذا الطلب؟') }}')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-full shadow transition">
                                            🧾 {{ __('إنشاء فاتورة') }}
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- إلغاء الطلب --}}
                            <form method="POST" action="{{ route('admin.orders.cancel', $order) }}"
                                  onsubmit="return confirm('{{ __('هل أنت متأكد من إلغاء الطلب؟ سيتم إرجاع المخزون.') }}')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-sm font-semibold rounded-full transition">
                                    ❌ {{ __('إلغاء الطلب') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ✅ تحديث الحالة --}}
            @if($order->status !== 'cancelled' && $order->status !== 'shipped')
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">
                        🔄 {{ __('تحديث حالة الطلب') }}
                    </h3>

                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order) }}">
                        @csrf
                        @method('PATCH')

                        <div class="flex flex-wrap items-center gap-3">
                            <select name="status"
                                    class="flex-1 min-w-[200px] rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ {{ __('بانتظار الدفع') }}</option>
                                <option value="paid" {{ $order->status === 'paid' ? 'selected' : '' }}>✅ {{ __('مدفوع') }}</option>
                                <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>📦 {{ __('قيد التجهيز') }}</option>
                                <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>🚚 {{ __('تم الشحن') }}</option>
                                <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ {{ __('ملغى') }}</option>
                            </select>

                            <button type="submit"
                                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition">
                                💾 {{ __('حفظ') }}
                            </button>
                        </div>

                        @if(count($order->available_transitions_labels) > 0)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                ℹ️ {{ __('الحالات المتاحة:') }}
                                {{ implode(' → ', $order->available_transitions_labels) }}
                            </p>
                        @else
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                ℹ️ {{ __('لا توجد حالات أخرى متاحة لهذا الطلب.') }}
                            </p>
                        @endif
                    </form>
                </div>
            @endif

            {{-- ✅ شبكة: العميل + الشحن --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- العميل --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span>👤</span> {{ __('معلومات العميل') }}
                    </h3>

                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold">
                                {{ mb_substr($order->user->name, 0, 2) }}
                            </div>
                            <div>
                                <div class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $order->user->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $order->user->email }}
                                </div>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('messages.create') }}"
                               class="inline-flex items-center gap-2 text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                💬 {{ __('إرسال رسالة') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- الشحن --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span>📦</span> {{ __('معلومات الشحن') }}
                    </h3>

                    @if($order->shipping_address)
                        <div class="space-y-2 text-sm">
                            <div class="flex gap-2">
                                <span class="text-gray-500 dark:text-gray-400 w-20">{{ __('الاسم:') }}</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $order->shipping_name }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 dark:text-gray-400 w-20">{{ __('الهاتف:') }}</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200" dir="ltr">{{ $order->shipping_phone }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 dark:text-gray-400 w-20">{{ __('المدينة:') }}</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $order->shipping_city }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 dark:text-gray-400 w-20">{{ __('العنوان:') }}</span>
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $order->shipping_address }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                            <div class="text-4xl mb-2">💾</div>
                            <p class="text-sm">{{ __('طلب رقمي — لا يحتاج شحن') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ✅ ملاحظات العميل --}}
            @if($order->notes)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-3 flex items-center gap-2">
                        <span>📝</span> {{ __('ملاحظات العميل') }}
                    </h3>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $order->notes }}</p>
                </div>
            @endif

            {{-- ✅ المنتجات --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">
                        🛒 {{ __('المنتجات') }} ({{ $order->items->count() }})
                    </h3>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($order->items as $item)
                        <div class="p-4 flex items-center gap-4">
                            {{-- صورة --}}
                            <div class="w-16 h-16 rounded-xl bg-gray-100 dark:bg-gray-700 flex-shrink-0 overflow-hidden">
                                @if($item->product?->cover_image)
                                    <img src="{{ $item->product->cover_image }}"
                                         alt="{{ $item->product_title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-2xl">
                                        {{ $item->isPhysical() ? '🏺' : '💾' }}
                                    </div>
                                @endif
                            </div>

                            {{-- التفاصيل --}}
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $item->product_title }}
                                </div>
                                <div class="flex flex-wrap items-center gap-2 mt-1 text-xs">
                                    <span class="px-2 py-0.5 rounded-full
                                        {{ $item->isPhysical()
                                            ? 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300'
                                            : 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' }}">
                                        {{ $item->isPhysical() ? '🏺 مادي' : '💾 رقمي' }}
                                    </span>
                                    @if($item->product_sku)
                                        <span class="text-gray-400">SKU: {{ $item->product_sku }}</span>
                                    @endif
                                </div>
                                @if($item->product)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ __('السعر الحالي:') }} ${{ number_format($item->product->effective_price, 2) }}
                                    </div>
                                @endif
                            </div>

                            {{-- الكمية والسعر --}}
                            <div class="text-end flex-shrink-0">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $item->quantity }} × ${{ number_format($item->product_price, 2) }}
                                </div>
                                <div class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                    ${{ number_format($item->subtotal, 2) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ✅ ملخص الطلب --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">
                    💰 {{ __('ملخص الطلب') }}
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- الإجماليات --}}
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('المجموع الفرعي') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('الشحن') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">${{ number_format($order->shipping_cost, 2) }}</span>
                        </div>
                        <div class="flex justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ __('الإجمالي') }}</span>
                            <span class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>

                    {{-- معلومات الدفع --}}
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('طريقة الدفع') }}</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $order->payment_method_label }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ __('حالة الدفع') }}</span>
                            <span class="font-semibold {{ $order->isPaid() ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                {{ $order->payment_status_label }}
                            </span>
                        </div>
                        @if($order->paid_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">{{ __('تاريخ الدفع') }}</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $order->paid_at->format('Y-m-d H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ✅ السند المحاسبي --}}
            @php
                $journalEntry = \App\Models\JournalEntry::where('reference_type', \App\Models\Order::class)
                    ->where('reference_id', $order->id)
                    ->first();
            @endphp

            @if($journalEntry)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span>📒</span> {{ __('السند المحاسبي') }}
                    </h3>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <div>
                            <div class="font-mono text-sm text-indigo-600 dark:text-indigo-400">
                                {{ $journalEntry->entry_number }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $journalEntry->entry_date->format('Y-m-d') }} • {{ $journalEntry->type_label }}
                            </div>
                        </div>
                        <a href="{{ route('accounting.journal-entries.show', $journalEntry) }}"
                           class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-full transition">
                            👁️ {{ __('عرض السند') }}
                        </a>
                    </div>
                </div>
            @elseif($order->payment_status === 'paid')
                <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-500 rounded-2xl p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">📊</span>
                        <div>
                            <h4 class="font-semibold text-amber-900 dark:text-amber-200 mb-1">
                                {{ __('لم يتم توليد سند القيد بعد') }}
                            </h4>
                            <p class="text-sm text-amber-800 dark:text-amber-300">
                                {{ __('اضغط على "توليد سند قيد" أعلاه لإنشاء السند المحاسبي لهذا الطلب.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✅ الفاتورة --}}
            @php
                $invoice = \App\Models\Invoice::where('order_id', $order->id)->first();
            @endphp

            @if($invoice)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4 flex items-center gap-2">
                        <span>🧾</span> {{ __('الفاتورة') }}
                    </h3>
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        <div>
                            <div class="font-mono text-sm text-teal-600 dark:text-teal-400">
                                {{ $invoice->invoice_number }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $invoice->invoice_date->format('Y-m-d') }} • {{ $invoice->type_label }} • ${{ number_format($invoice->total, 2) }}
                            </div>
                        </div>
                        <a href="{{ route('accounting.invoices.show', $invoice) }}"
                           class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-full transition">
                            👁️ {{ __('عرض الفاتورة') }}
                        </a>
                    </div>
                </div>
            @elseif($order->payment_status === 'paid')
                <div class="bg-teal-50 dark:bg-teal-900/20 border-l-4 border-teal-500 rounded-2xl p-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl">🧾</span>
                        <div>
                            <h4 class="font-semibold text-teal-900 dark:text-teal-200 mb-1">
                                {{ __('لم يتم إنشاء فاتورة بعد') }}
                            </h4>
                            <p class="text-sm text-teal-800 dark:text-teal-300">
                                {{ __('اضغط على "إنشاء فاتورة" أعلاه لإنشاء فاتورة لهذا الطلب.') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>