<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="سند قيد #{{ $journalEntry->entry_number }}" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.journal-entries.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('العودة للسندات') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    📒 {{ __('سند قيد') }} #{{ $journalEntry->entry_number }}
                </h2>
            </div>

            <div class="flex gap-2">
                @if(!$journalEntry->is_posted)
                    <form method="POST" action="{{ route('accounting.journal-entries.destroy', $journalEntry) }}"
                          onsubmit="return confirm('{{ __('حذف هذا السند؟') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-sm font-semibold rounded-full transition">
                            🗑️ {{ __('حذف') }}
                        </button>
                    </form>
                @endif
                <button onclick="window.print()"
                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold rounded-full transition">
                    🖨️ {{ __('طباعة') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            {{-- رأس السند --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                <div class="flex flex-wrap items-start justify-between gap-6">
                    <div>
                        <div class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ __('رقم السند') }}</div>
                        <div class="text-2xl font-bold font-mono text-gray-800 dark:text-gray-200">
                            {{ $journalEntry->entry_number }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            📅 {{ $journalEntry->entry_date->format('Y-m-d') }}
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @php
                            $typeColors = [
                                'cash' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                'credit' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'adjustment' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
                            ];
                        @endphp
                        <span class="px-4 py-2 text-sm font-bold rounded-full {{ $typeColors[$journalEntry->type] ?? '' }}">
                            {{ $journalEntry->type_label }}
                        </span>

                        @if($journalEntry->is_posted)
                            <span class="px-4 py-2 text-sm font-bold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300">
                                ✅ {{ __('مُرحَّل') }}
                            </span>
                        @else
                            <span class="px-4 py-2 text-sm font-bold rounded-full bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                📝 {{ __('مسودة') }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="text-sm">
                        <strong class="text-gray-600 dark:text-gray-400">{{ __('البيان:') }}</strong>
                        <span class="text-gray-800 dark:text-gray-200">{{ $journalEntry->description }}</span>
                    </div>

                    @if($journalEntry->creator)
                        <div class="text-sm mt-2">
                            <strong class="text-gray-600 dark:text-gray-400">{{ __('أنشأه:') }}</strong>
                            <span class="text-gray-800 dark:text-gray-200">{{ $journalEntry->creator->name }}</span>
                        </div>
                    @endif

                    @if($journalEntry->posted_at)
                        <div class="text-sm mt-2">
                            <strong class="text-gray-600 dark:text-gray-400">{{ __('تاريخ الترحيل:') }}</strong>
                            <span class="text-gray-800 dark:text-gray-200">{{ $journalEntry->posted_at->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif

                    @if($journalEntry->notes)
                        <div class="mt-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-sm">
                            <strong class="text-gray-600 dark:text-gray-400">📝 {{ __('ملاحظات:') }}</strong>
                            <p class="text-gray-700 dark:text-gray-300 mt-1 whitespace-pre-line">{{ $journalEntry->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- سطور السند --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-bold text-gray-800 dark:text-gray-200">📝 {{ __('سطور السند') }}</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الحساب') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الوصف') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('مدين') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('دائن') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($journalEntry->lines as $line)
                                <tr>
                                    <td class="px-4 py-3 text-sm">
                                        <div class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $line->account->code }}</div>
                                        <div class="font-semibold text-gray-800 dark:text-gray-200">{{ $line->account->name }}</div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $line->description ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold {{ $line->debit > 0 ? 'text-green-600 dark:text-green-400' : 'text-gray-400' }}">
                                        {{ $line->debit > 0 ? '$' . number_format($line->debit, 2) : '—' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold {{ $line->credit > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}">
                                        {{ $line->credit > 0 ? '$' . number_format($line->credit, 2) : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900/50 border-t-2 border-gray-300 dark:border-gray-600">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right font-bold text-gray-800 dark:text-gray-200">
                                    {{ __('الإجمالي') }}
                                </td>
                                <td class="px-4 py-3 font-bold text-green-600 dark:text-green-400">
                                    ${{ number_format($journalEntry->total_debit, 2) }}
                                </td>
                                <td class="px-4 py-3 font-bold text-red-600 dark:text-red-400">
                                    ${{ number_format($journalEntry->total_credit, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($journalEntry->isBalanced())
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 text-green-800 dark:text-green-200 text-sm text-center border-t border-green-200 dark:border-green-800">
                        ✅ {{ __('السند متوازن') }}
                    </div>
                @else
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-800 dark:text-red-200 text-sm text-center border-t border-red-200 dark:border-red-800">
                        ⚠️ {{ __('السند غير متوازن!') }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>