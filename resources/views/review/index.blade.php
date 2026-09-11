<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('مراجعة المحتوى') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- الإحصائيات -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">في انتظار المراجعة</div>
                    <div class="text-2xl font-bold">{{ $stats['total'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">تمت مراجعتها اليوم</div>
                    <div class="text-2xl font-bold">{{ $stats['reviewed_today'] }}</div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">إجمالي مراجعاتي</div>
                    <div class="text-2xl font-bold">{{ $stats['my_history'] }}</div>
                </div>
            </div>

            <!-- قائمة الانتظار -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($pendingContents->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-right">العنوان</th>
                                        <th class="px-4 py-2 text-right">المؤلف</th>
                                        <th class="px-4 py-2 text-right">النوع</th>
                                        <th class="px-4 py-2 text-right">تاريخ التقديم</th>
                                        <th class="px-4 py-2 text-right">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingContents as $content)
                                    <tr class="border-t dark:border-gray-700">
                                        <td class="px-4 py-2">{{ $content->title }}</td>
                                        <td class="px-4 py-2">{{ $content->author->name }}</td>
                                        <td class="px-4 py-2">{{ $content->type }}</td>
                                        <td class="px-4 py-2">{{ $content->queued_at->diffForHumans() }}</td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('review.preview', $content) }}"
                                               class="inline-flex items-center px-3 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                                معاينة
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $pendingContents->links() }}
                    @else
                        <p class="text-center text-gray-500">لا يوجد محتوى في انتظار المراجعة.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>