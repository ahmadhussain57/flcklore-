<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('تاريخ مراجعاتي') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if($history->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2 text-right">العنوان</th>
                                        <th class="px-4 py-2 text-right">المؤلف</th>
                                        <th class="px-4 py-2 text-right">الحالة</th>
                                        <th class="px-4 py-2 text-right">تاريخ المراجعة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $item)
                                    <tr class="border-t dark:border-gray-700">
                                        <td class="px-4 py-2">{{ $item->title }}</td>
                                        <td class="px-4 py-2">{{ $item->author->name }}</td>
                                        <td class="px-4 py-2">
                                            @if($item->status === 'published')
                                                <span class="text-green-600">✅ منشور</span>
                                            @elseif($item->status === 'rejected')
                                                <span class="text-red-600">❌ مرفوض</span>
                                            @else
                                                <span class="text-gray-500">{{ $item->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2">{{ $item->reviewed_at->diffForHumans() }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $history->links() }}
                    @else
                        <p class="text-center text-gray-500">لم تقم بأي مراجعة حتى الآن.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>