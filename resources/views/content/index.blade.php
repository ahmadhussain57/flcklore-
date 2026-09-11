<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📚 {{ __('محتوياتي') }}
            </h2>
            <a href="{{ route('content.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('محتوى جديد') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- رسائل التنبيه --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            {{-- الإحصائيات --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $stats['total'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('المجموع') }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['published'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('منشور') }}</div>
                </div>
                <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('قيد المراجعة') }}</div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['draft'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مسودة') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/30 rounded-xl shadow p-4 text-center">
                    <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['rejected'] }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مرفوض') }}</div>
                </div>
            </div>

            {{-- قائمة المحتويات --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('العنوان') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('النوع') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الحالة') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('تاريخ الإنشاء') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">{{ __('الإجراءات') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($contents as $content)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                    <td class="px-6 py-4 font-medium text-gray-800 dark:text-gray-200">
                                        {{ $content->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $content->type }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusMap = [
                                                'draft' => ['label' => 'مسودة', 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
                                                'pending_review' => ['label' => 'قيد المراجعة', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                                'pending_edit' => ['label' => 'تعديل معلق', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                                'published' => ['label' => 'منشور', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
                                                'rejected' => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
                                            ];
                                            $s = $statusMap[$content->status] ?? $statusMap['draft'];
                                        @endphp
                                        <span class="px-2 py-1 text-xs rounded-full {{ $s['class'] }}">
                                            {{ $s['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $content->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('content.show', $content) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('عرض') }}</a>

                                            @if(in_array($content->status, ['draft', 'rejected', 'pending_edit']))
                                                <a href="{{ route('content.edit', $content) }}" class="text-amber-600 dark:text-amber-400 hover:underline">{{ __('تعديل') }}</a>
                                            @endif

                                            @if(in_array($content->status, ['draft', 'rejected']))
                                                <form action="{{ route('content.submit', $content) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="text-green-600 dark:text-green-400 hover:underline">{{ __('إرسال للمراجعة') }}</button>
                                                </form>
                                            @endif

                                            <form action="{{ route('content.destroy', $content) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">{{ __('حذف') }}</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400">
                                        {{ __('لا يوجد محتوى حتى الآن.') }}
                                        <a href="{{ route('content.create') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('أنشئ محتواك الأول') }}</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($contents->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $contents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>