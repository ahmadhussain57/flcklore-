<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="دليل الحسابات والأرصدة" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.reports.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('التقارير') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    💼 {{ __('دليل الحسابات والأرصدة') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total_accounts'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('كل الحسابات') }}</div>
                </div>
                <div class="bg-blue-50 dark:bg-blue-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['assets'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('أصول') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['liabilities'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('خصوم') }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['revenues'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('إيرادات') }}</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['expenses'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('نفقات') }}</div>
                </div>
            </div>

            {{-- الفلاتر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('accounting.accounts.index') }}">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="{{ __('ابحث بالاسم أو الكود...') }}"
                               class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">

                        <select name="type"
                                class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                            <option value="">{{ __('كل الأنواع') }}</option>
                            <option value="asset" {{ request('type') === 'asset' ? 'selected' : '' }}>💰 {{ __('أصول') }}</option>
                            <option value="liability" {{ request('type') === 'liability' ? 'selected' : '' }}>📉 {{ __('خصوم') }}</option>
                            <option value="equity" {{ request('type') === 'equity' ? 'selected' : '' }}>🏛️ {{ __('حقوق ملكية') }}</option>
                            <option value="revenue" {{ request('type') === 'revenue' ? 'selected' : '' }}>📈 {{ __('إيرادات') }}</option>
                            <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>💸 {{ __('نفقات') }}</option>
                        </select>

                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition text-sm">
                                🔍 {{ __('تصفية') }}
                            </button>
                            @if(request()->anyFilled(['q', 'type']))
                                <a href="{{ route('accounting.accounts.index') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-xl text-sm">✖</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            {{-- الجدول --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                @if($accounts->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الكود') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الحساب') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('النوع') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('مدين') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('دائن') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الرصيد') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الحركات') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($accounts as $account)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <td class="px-4 py-3 font-mono text-sm text-indigo-600 dark:text-indigo-400">
                                            {{ $account->code }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('accounting.accounts.show', $account) }}"
                                               class="font-semibold text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                                {{ $account->name }}
                                            </a>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $typeColors = [
                                                    'asset' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                                    'liability' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                                    'equity' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                                    'revenue' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                                    'expense' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 text-xs rounded-full {{ $typeColors[$account->type] ?? '' }}">
                                                {{ $account->type_label }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">
                                            ${{ number_format($account->total_debit, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400">
                                            ${{ number_format($account->total_credit, 2) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-bold {{ $account->balance >= 0 ? 'text-gray-800 dark:text-gray-200' : 'text-red-600 dark:text-red-400' }}">
                                            ${{ number_format(abs($account->balance), 2) }}
                                            <span class="text-xs font-normal">
                                                {{ $account->balance >= 0 ? '(مدين)' : '(دائن)' }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-sm text-center text-gray-500 dark:text-gray-400">
                                            {{ $account->entries_count }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="text-8xl mb-4">💼</div>
                        <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('لا توجد حسابات') }}</h3>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>