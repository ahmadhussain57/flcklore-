<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="سندات القيد" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📒 {{ __('سندات القيد') }}
            </h2>
            <a href="{{ route('accounting.journal-entries.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                + {{ __('سند جديد') }}
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

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('المجموع') }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['cash'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('نقداً') }}</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['credit'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('أجل') }}</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['adjustment'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('تسوية') }}</div>
                </div>
                <div class="bg-purple-50 dark:bg-purple-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-xl font-bold text-purple-600 dark:text-purple-400">${{ number_format($stats['total_debit'], 2) }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('إجمالي المدين') }}</div>
                </div>
            </div>

            {{-- الفلاتر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('accounting.journal-entries.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                        <div class="md:col-span-2">
                            <input type="text" name="q" value="{{ request('q') }}"
                                   placeholder="{{ __('ابحث برقم السند أو البيان...') }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        <select name="type"
                                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('كل الأنواع') }}</option>
                            <option value="cash" {{ request('type') === 'cash' ? 'selected' : '' }}>💵 {{ __('نقداً') }}</option>
                            <option value="credit" {{ request('type') === 'credit' ? 'selected' : '' }}>📅 {{ __('أجل') }}</option>
                            <option value="adjustment" {{ request('type') === 'adjustment' ? 'selected' : '' }}>⚙️ {{ __('تسوية') }}</option>
                        </select>
                        <input type="date" name="from_date" value="{{ request('from_date') }}"
                               class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition">
                                🔍 {{ __('تصفية') }}
                            </button>
                            @if(request()->anyFilled(['q', 'type', 'from_date', 'to_date']))
                                <a href="{{ route('accounting.journal-entries.index') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-xl">✖</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- الجدول --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                @if($entries->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('رقم السند') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التاريخ') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('النوع') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('البيان') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('مدين') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('دائن') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجراءات') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($entries as $entry)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 font-mono text-sm font-semibold text-indigo-600 dark:text-indigo-400">
                                            {{ $entry->entry_number }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $entry->entry_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $typeColors = [
                                                    'cash' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'credit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'adjustment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $typeColors[$entry->type] ?? '' }}">
                                                {{ $entry->type_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                            {{ $entry->description }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">
                                            ${{ number_format($entry->total_debit, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400">
                                            ${{ number_format($entry->total_credit, 2) }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('accounting.journal-entries.show', $entry) }}"
                                               class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-full transition">
                                                👁️ {{ __('عرض') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($entries->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $entries->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="text-8xl mb-4">📒</div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('لا توجد سندات') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-6">{{ __('ابدأ بإنشاء سند قيد جديد.') }}</p>
                        <a href="{{ route('accounting.journal-entries.create') }}"
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-full shadow-md transition">
                            + {{ __('سند جديد') }}
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>