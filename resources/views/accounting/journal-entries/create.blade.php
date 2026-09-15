<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="سند قيد جديد" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('accounting.journal-entries.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm">
                    ← {{ __('العودة للسندات') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    ➕ {{ __('سند قيد جديد') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="journalEntryForm()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('accounting.journal-entries.store') }}">
                @csrf

                {{-- معلومات السند --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">📋 {{ __('معلومات السند') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('التاريخ') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="date" name="entry_date" required
                                   value="{{ old('entry_date', date('Y-m-d')) }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('النوع') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="type" required
                                    class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="cash" {{ old('type') === 'cash' ? 'selected' : '' }}>💵 {{ __('نقداً') }}</option>
                                <option value="credit" {{ old('type') === 'credit' ? 'selected' : '' }}>📅 {{ __('أجل') }}</option>
                                <option value="adjustment" {{ old('type') === 'adjustment' ? 'selected' : '' }}>⚙️ {{ __('تسوية') }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('رقم السند') }}
                            </label>
                            <input type="text" disabled value="{{ \App\Models\JournalEntry::generateEntryNumber() }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-900 dark:text-gray-400 shadow-sm font-mono">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('البيان') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="description" required value="{{ old('description') }}"
                                   placeholder="{{ __('وصف مختصر للعملية...') }}"
                                   class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                {{ __('ملاحظات') }}
                            </label>
                            <textarea name="notes" rows="2"
                                      class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- سطور السند --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200">📝 {{ __('سطور السند') }}</h3>
                        <button type="button" @click="addLine()"
                                class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-full transition">
                            + {{ __('إضافة سطر') }}
                        </button>
                    </div>

                    {{-- رأس الجدول --}}
                    <div class="hidden md:grid grid-cols-12 gap-2 px-3 py-2 bg-gray-50 dark:bg-gray-900/50 rounded-lg text-xs font-semibold text-gray-600 dark:text-gray-400 mb-2">
                        <div class="col-span-5">{{ __('الحساب') }}</div>
                        <div class="col-span-2">{{ __('الوصف') }}</div>
                        <div class="col-span-2 text-center">{{ __('مدين') }}</div>
                        <div class="col-span-2 text-center">{{ __('دائن') }}</div>
                        <div class="col-span-1"></div>
                    </div>

                    {{-- السطور --}}
                    <template x-for="(line, index) in lines" :key="index">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 mb-2 p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                            <div class="md:col-span-5">
                                <select :name="`lines[${index}][account_id]`" required
                                        x-model="line.account_id"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">{{ __('اختر الحساب...') }}</option>
                                    @foreach($accounts as $account)
                                        <option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <input type="text" :name="`lines[${index}][description]`"
                                       x-model="line.description"
                                       placeholder="{{ __('وصف') }}"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="md:col-span-2">
                                <input type="number" step="0.01" min="0"
                                       :name="`lines[${index}][debit]`"
                                       x-model.number="line.debit"
                                       @input="if(line.debit > 0) line.credit = 0"
                                       placeholder="0.00"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-center focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="md:col-span-2">
                                <input type="number" step="0.01" min="0"
                                       :name="`lines[${index}][credit]`"
                                       x-model.number="line.credit"
                                       @input="if(line.credit > 0) line.debit = 0"
                                       placeholder="0.00"
                                       class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-center focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div class="md:col-span-1 flex justify-center">
                                <button type="button" @click="removeLine(index)"
                                        x-show="lines.length > 2"
                                        class="p-2 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition">
                                    🗑️
                                </button>
                            </div>
                        </div>
                    </template>

                    {{-- الإجماليات --}}
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-3 bg-green-50 dark:bg-green-900/30 rounded-xl text-center">
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('إجمالي المدين') }}</div>
                            <div class="text-xl font-bold text-green-600 dark:text-green-400" x-text="'$' + totalDebit.toFixed(2)"></div>
                        </div>
                        <div class="p-3 bg-red-50 dark:bg-red-900/30 rounded-xl text-center">
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('إجمالي الدائن') }}</div>
                            <div class="text-xl font-bold text-red-600 dark:text-red-400" x-text="'$' + totalCredit.toFixed(2)"></div>
                        </div>
                        <div class="p-3 rounded-xl text-center"
                             :class="isBalanced ? 'bg-blue-50 dark:bg-blue-900/30' : 'bg-yellow-50 dark:bg-yellow-900/30'">
                            <div class="text-xs text-gray-600 dark:text-gray-400 mb-1">{{ __('الفرق') }}</div>
                            <div class="text-xl font-bold" x-text="'$' + (totalDebit - totalCredit).toFixed(2)"
                                 :class="isBalanced ? 'text-blue-600 dark:text-blue-400' : 'text-yellow-600 dark:text-yellow-400'"></div>
                        </div>
                    </div>

                    <div x-show="!isBalanced" class="mt-3 p-3 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 text-sm rounded-lg">
                        ⚠️ {{ __('السند غير متوازن! يجب أن يكون مجموع المدين = مجموع الدائن.') }}
                    </div>
                </div>

                {{-- الأزرار --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('accounting.journal-entries.index') }}"
                       class="px-6 py-3 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition">
                        {{ __('إلغاء') }}
                    </a>
                    <button type="submit" :disabled="!isBalanced"
                            class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl shadow-md transition">
                        💾 {{ __('حفظ السند') }}
                    </button>
                </div>
            </form>

        </div>
    </div>

    <script>
        function journalEntryForm() {
            return {
                lines: [
                    { account_id: '', description: '', debit: 0, credit: 0 },
                    { account_id: '', description: '', debit: 0, credit: 0 },
                ],

                addLine() {
                    this.lines.push({ account_id: '', description: '', debit: 0, credit: 0 });
                },

                removeLine(index) {
                    if (this.lines.length > 2) {
                        this.lines.splice(index, 1);
                    }
                },

                get totalDebit() {
                    return this.lines.reduce((sum, line) => sum + (parseFloat(line.debit) || 0), 0);
                },

                get totalCredit() {
                    return this.lines.reduce((sum, line) => sum + (parseFloat(line.credit) || 0), 0);
                },

                get isBalanced() {
                    return Math.abs(this.totalDebit - this.totalCredit) < 0.01 && this.totalDebit > 0;
                },
            };
        }
    </script>
</x-app-layout>