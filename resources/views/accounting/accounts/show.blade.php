<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="الحساب: {{ $account->name }}" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.accounts.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('دليل الحسابات') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    💼 {{ $account->name }}
                </h2>
            </div>
            <button onclick="window.print()"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-full transition">
                🖨️ {{ __('طباعة') }}
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- معلومات الحساب --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('الكود') }}</div>
                        <div class="text-lg font-bold font-mono text-indigo-600 dark:text-indigo-400">{{ $account->code }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('النوع') }}</div>
                        <div class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $account->type_label }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('طبيعة الرصيد') }}</div>
                        <div class="text-lg font-bold text-gray-800 dark:text-gray-200">{{ $account->normal_balance_label }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">{{ __('الرصيد الحالي') }}</div>
                        <div class="text-lg font-bold {{ $balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            ${{ number_format(abs($balance), 2) }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-xl">
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('إجمالي المدين') }}</div>
                        <div class="text-xl font-bold text-green-600 dark:text-green-400">${{ number_format($totalDebit, 2) }}</div>
                    </div>
                    <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-xl">
                        <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('إجمالي الدائن') }}</div>
                        <div class="text-xl font-bold text-red-600 dark:text-red-400">${{ number_format($totalCredit, 2) }}</div>
                    </div>
                </div>
            </div>

            {{-- الحركات --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">📋 {{ __('الحركات') }}</h3>
                </div>

                @if($lines->count())
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التاريخ') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('رقم السند') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('البيان') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('مدين') }}</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('دائن') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($lines as $line)
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $line->journalEntry->entry_date->format('Y-m-d') }}
                                        </td>
                                        <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                                            {{ $line->journalEntry->entry_number }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $line->description ?? $line->journalEntry->description }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-green-600 dark:text-green-400">
                                            {{ $line->debit > 0 ? '$' . number_format($line->debit, 2) : '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold text-red-600 dark:text-red-400">
                                            {{ $line->credit > 0 ? '$' . number_format($line->credit, 2) : '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($lines->hasPages())
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                            {{ $lines->links() }}
                        </div>
                    @endif
                @else
                    <div class="p-12 text-center">
                        <div class="text-6xl mb-4">📭</div>
                        <p class="text-gray-500 dark:text-gray-400">{{ __('لا توجد حركات لهذا الحساب.') }}</p>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>