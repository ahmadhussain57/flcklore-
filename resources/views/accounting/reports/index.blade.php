<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="التقارير المحاسبية" />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📊 {{ __('التقارير المحاسبية') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero --}}
            <div class="mb-8 p-8 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl shadow-xl text-white text-center">
                <div class="text-6xl mb-4">📊</div>
                <h1 class="text-3xl font-bold mb-2">{{ __('مركز التقارير') }}</h1>
                <p class="text-lg opacity-90">{{ __('تقارير محاسبية شاملة عن الحركة المالية والمخزون') }}</p>
            </div>

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_entries'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('سندات القيد') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-teal-600 dark:text-teal-400">{{ $stats['total_invoices'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('الفواتير') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-amber-600 dark:text-amber-400">{{ $stats['total_movements'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('حركات المواد') }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $stats['total_accounts'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('الحسابات') }}</div>
                </div>
            </div>

            {{-- التقارير المتاحة --}}
            <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-4">📋 {{ __('التقارير المتاحة') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- تقرير حركة المواد --}}
                <a href="{{ route('accounting.reports.material-movement') }}"
                   class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                    <div class="h-32 bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center">
                        <span class="text-6xl group-hover:scale-110 transition">📦</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-amber-600 dark:group-hover:text-amber-400 transition">
                            {{ __('حركة المواد') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('سجل كامل لعمليات الشراء والبيع والمرتجعات لكل منتج.') }}
                        </p>
                    </div>
                </a>

                {{-- تقرير دفتر الأستاذ --}}
                <a href="{{ route('accounting.reports.ledger') }}"
                   class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                    <div class="h-32 bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                        <span class="text-6xl group-hover:scale-110 transition">📒</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                            {{ __('دفتر الأستاذ') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('كل الحركات المالية لحساب معين مع الرصيد التراكمي.') }}
                        </p>
                    </div>
                </a>

                {{-- تقرير حالة المستودع --}}
                <a href="{{ route('accounting.reports.warehouse') }}"
                   class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                    <div class="h-32 bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center">
                        <span class="text-6xl group-hover:scale-110 transition">🏪</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-green-600 dark:group-hover:text-green-400 transition">
                            {{ __('حالة المستودع') }}
                        </h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            {{ __('كميات المنتجات الحالية وقيمتها الإجمالية.') }}
                        </p>
                    </div>
                </a>

                {{-- الأرصدة --}}
<a href="{{ route('accounting.accounts.index') }}"
   class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden group">
    <div class="h-32 bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center">
        <span class="text-6xl group-hover:scale-110 transition">💼</span>
    </div>
    <div class="p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-2 group-hover:text-cyan-600 dark:group-hover:text-cyan-400 transition">
            {{ __('أرصدة الحسابات') }}
        </h3>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('عرض دليل الحسابات مع الأرصدة الحالية.') }}
        </p>
    </div>
</a>
            </div>

        </div>
    </div>
</x-app-layout>