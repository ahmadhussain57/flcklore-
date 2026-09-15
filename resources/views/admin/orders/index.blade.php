<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="إدارة الطلبات" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📦 {{ __('إدارة الطلبات') }}
            </h2>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ __('مرحباً،') }} {{ auth()->user()->name }}
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

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

            {{-- ✅ الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center text-xl">
                            📊
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('إجمالي الطلبات') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-900/30 flex items-center justify-center text-xl">
                            ⏳
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('بانتظار الدفع') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-xl">
                            ✅
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['paid'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('مدفوع') }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center text-xl">
                            💰
                        </div>
                        <div>
                            <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">
                                {{ number_format($stats['total_revenue'], 2) }}
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('إجمالي الإيرادات') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ شريط الفلاتر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('admin.orders.index') }}">

                    {{-- الصف الأول: البحث والأزرار --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                        <div class="md:col-span-2">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="{{ __('ابحث برقم الطلب أو اسم العميل...') }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <select name="sort"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>🆕 {{ __('الأحدث') }}</option>
                                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>🕐 {{ __('الأقدم') }}</option>
                                <option value="total_high" {{ request('sort') === 'total_high' ? 'selected' : '' }}>💎 {{ __('الأعلى قيمة') }}</option>
                                <option value="total_low" {{ request('sort') === 'total_low' ? 'selected' : '' }}>💰 {{ __('الأقل قيمة') }}</option>
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition">
                                🔍 {{ __('تصفية') }}
                            </button>
                            @if(request()->anyFilled(['q', 'status', 'payment_status', 'from_date', 'to_date', 'sort']))
                                <a href="{{ route('admin.orders.index') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl transition"
                                   title="{{ __('مسح الفلاتر') }}">
                                    ✖
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- الصف الثاني: الفلاتر المتقدمة --}}
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('حالة الطلب') }}</label>
                            <select name="status" onchange="this.form.submit()"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('الكل') }}</option>
                                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>⏳ {{ __('بانتظار الدفع') }}</option>
                                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>✅ {{ __('مدفوع') }}</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>📦 {{ __('قيد التجهيز') }}</option>
                                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>🚚 {{ __('تم الشحن') }}</option>
                                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>❌ {{ __('ملغى') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('حالة الدفع') }}</label>
                            <select name="payment_status" onchange="this.form.submit()"
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('الكل') }}</option>
                                <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>🔴 {{ __('غير مدفوع') }}</option>
                                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>🟢 {{ __('مدفوع') }}</option>
                                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>↩️ {{ __('مسترد') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('من تاريخ') }}</label>
                            <input type="date" name="from_date" value="{{ request('from_date') }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('إلى تاريخ') }}</label>
                            <input type="date" name="to_date" value="{{ request('to_date') }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        </div>
                    </div>
                </form>
            </div>

            {{-- ✅ عدد النتائج --}}
            @if(request()->anyFilled(['q', 'status', 'payment_status', 'from_date', 'to_date']))
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('عدد النتائج:') }} <strong class="text-gray-900 dark:text-white">{{ $orders->total() }}</strong>
                </div>
            @endif

            {{-- ✅ جدول الطلبات --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                @if($orders->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('رقم الطلب') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('العميل') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التاريخ') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('المنتجات') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجمالي') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الحالة') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الدفع') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="font-mono text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                                #{{ $order->order_number }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                                {{ $order->user->name }}
                                            </div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[150px]">
                                                {{ $order->user->email }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                            {{ $order->placed_at->format('Y-m-d') }}
                                            <div class="text-xs">{{ $order->placed_at->format('H:i') }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ $order->total_items }} {{ __('منتج') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="text-sm font-bold text-gray-800 dark:text-gray-200">
                                                ${{ number_format($order->total, 2) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @php
                                                $statusClasses = [
                                                    'pending'    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                    'paid'       => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'processing' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'shipped'    => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                                    'cancelled'  => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $statusClasses[$order->status] ?? '' }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($order->payment_status === 'paid')
                                                <span class="text-xs text-green-600 dark:text-green-400 font-semibold">🟢 {{ __('مدفوع') }}</span>
                                            @elseif($order->payment_status === 'refunded')
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">↩️ {{ __('مسترد') }}</span>
                                            @else
                                                <span class="text-xs text-red-600 dark:text-red-400 font-semibold">🔴 {{ __('غير مدفوع') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('admin.orders.show', $order) }}"
                                                   class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full transition">
                                                    👁️ {{ __('عرض') }}
                                                </a>

                                                @if($order->payment_status === 'unpaid' && $order->status !== 'cancelled')
                                                    <form method="POST"
                                                          action="{{ route('admin.orders.confirmPayment', $order) }}"
                                                          onsubmit="return confirm('{{ __('تأكيد دفع هذا الطلب؟') }}')"
                                                          class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit"
                                                                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-semibold rounded-full transition">
                                                            ✅ {{ __('تأكيد الدفع') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $orders->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="text-8xl mb-4">📭</div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('لا توجد طلبات') }}
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400">
                            @if(request()->anyFilled(['q', 'status', 'payment_status', 'from_date', 'to_date']))
                                {{ __('لا توجد نتائج مطابقة للفلاتر.') }}
                                <a href="{{ route('admin.orders.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('مسح الفلاتر') }}</a>
                            @else
                                {{ __('لم يتم إنشاء أي طلب بعد.') }}
                            @endif
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>