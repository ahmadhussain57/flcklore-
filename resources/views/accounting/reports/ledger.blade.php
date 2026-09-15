<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="دفتر الأستاذ" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.reports.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('التقارير') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    📒 {{ __('دفتر الأستاذ') }}
                </h2>
            </div>
            @if($selectedAccount)
                <div class="flex gap-2">
                    <a href="{{ route('accounting.reports.ledger.excel', request()->query()) }}"
                       class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-full transition">
                        📊 {{ __('Excel') }}
                    </a>
                    <button onclick="window.print()"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-full transition">
                        🖨️ {{ __('طباعة') }}
                    </button>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- الفلاتر --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('accounting.reports.ledger') }}">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">{{ __('الحساب') }}</label>
                            <select name="account_id" required
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                <option value="">{{ __('اختر الحساب...') }}</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ request('account_id') === $account->id ? 'selected' : '' }}>
                                        {{ $account->code }} - {{ $account->name }}
                                    </option>
                                @endforeach
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

                        <div class="flex items-end gap-2">
                            <button type="submit"
                                    class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition text-sm">
                                🔍 {{ __('عرض') }}
                            </button>
                            @if(request()->anyFilled(['account_id', 'from_date', 'to_date']))
                                <a href="{{ route('accounting.reports.ledger') }}"
                                   class="px-3 py-2 bg-gray-200 dark:bg-gray-700 rounded-xl text-sm">✖</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            @if($selectedAccount)
                {{-- معلومات الحساب --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $selectedAccount->code }}</div>
                            <div class="text-xl font-bold text-gray-800 dark:text-gray-200">{{ $selectedAccount->name }}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                {{ $selectedAccount->type_label }} • {{ $selectedAccount->normal_balance_label }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('الرصيد الحالي') }}</div>
                            <div class="text-3xl font-bold {{ $balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                ${{ number_format(abs($balance), 2) }}
                                <span class="text-sm">{{ $balance >= 0 ? '(مدين)' : '(دائن)' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- الجدول --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                    @if($entries->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('التاريخ') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('رقم السند') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('البيان') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('مدين') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('دائن') }}</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الرصيد') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($entries as $entry)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
                                                {{ $entry['date']->format('Y-m-d') }}
                                            </td>
                                            <td class="px-4 py-3 font-mono text-xs text-indigo-600 dark:text-indigo-400">
                                                {{ $entry['entry_number'] }}
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 max-w-xs truncate">
                                                {{ $entry['description'] }}
                                            </td>
                                            <td class="px-4 py-3 text-sm {{ $entry['debit'] > 0 ? 'font-semibold text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                                                {{ $entry['debit'] > 0 ? '$' . number_format($entry['debit'], 2) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm {{ $entry['credit'] > 0 ? 'font-semibold text-red-600 dark:text-red-400' : 'text-gray-400' }}">
                                                {{ $entry['credit'] > 0 ? '$' . number_format($entry['credit'], 2) : '—' }}
                                            </td>
                                            <td class="px-4 py-3 text-sm font-bold text-gray-800 dark:text-gray-200">
                                                ${{ number_format(abs($entry['balance']), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="p-12 text-center">
                            <div class="text-6xl mb-4">📭</div>
                            <p class="text-gray-500 dark:text-gray-400">{{ __('لا توجد حركات لهذا الحساب.') }}</p>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">
                    <div class="text-8xl mb-4">📒</div>
                    <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">{{ __('اختر حساباً') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400">{{ __('اختر حساباً من القائمة أعلاه لعرض دفتر الأستاذ الخاص به.') }}</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>