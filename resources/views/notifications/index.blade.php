<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                🔔 {{ __('الإشعارات') }}
                @if($unreadCount > 0)
                    <span class="ms-2 px-2 py-0.5 text-xs font-bold text-white bg-red-500 rounded-full">
                        {{ $unreadCount }} {{ __('جديد') }}
                    </span>
                @endif
            </h2>
            <div class="flex items-center gap-3">
                @if($unreadCount > 0)
                    <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
                        @csrf
                        <button type="submit"
                                class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                            ✅ {{ __('تعليم الكل كمقروء') }}
                        </button>
                    </form>
                @endif

                @if($notifications->total() > 0)
                    <form method="POST" action="{{ route('notifications.destroyAll') }}"
                          onsubmit="return confirm('{{ __('هل أنت متأكد من حذف جميع الإشعارات؟') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-sm text-red-600 dark:text-red-400 hover:underline font-medium">
                            🗑️ {{ __('حذف الكل') }}
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- رسائل التنبيه --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            {{-- قائمة الإشعارات --}}
            @if($notifications->count())
                <div class="space-y-2">
                    @foreach($notifications as $notification)
                        @php
                            $data = $notification->data;
                            $isUnread = is_null($notification->read_at);
                            $color = $data['color'] ?? 'info';
                            $colorClasses = [
                                'success' => 'bg-green-100 dark:bg-green-900/30 border-green-500',
                                'danger'  => 'bg-red-100 dark:bg-red-900/30 border-red-500',
                                'warning' => 'bg-yellow-100 dark:bg-yellow-900/30 border-yellow-500',
                                'info'    => 'bg-blue-100 dark:bg-blue-900/30 border-blue-500',
                            ];
                            $bgClass = $colorClasses[$color] ?? $colorClasses['info'];
                        @endphp

                        <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-sm hover:shadow-md transition
                                    border-l-4 {{ $isUnread ? $bgClass : 'border-gray-300 dark:border-gray-600' }}
                                    {{ $isUnread ? '' : 'opacity-75' }}">
                            <div class="flex items-start gap-4 p-4">

                                {{-- أيقونة --}}
                                <div class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center text-2xl
                                            {{ $isUnread ? 'bg-white/60 dark:bg-gray-900/40' : 'bg-gray-100 dark:bg-gray-700' }}">
                                    {{ $data['icon'] ?? '🔔' }}
                                </div>

                                {{-- المحتوى --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-2">
                                        <a href="{{ route('notifications.read', $notification->id) }}"
                                           class="block flex-1 group">
                                            <h3 class="font-bold text-gray-800 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition
                                                       {{ $isUnread ? '' : 'font-semibold' }}">
                                                {{ $data['title'] ?? __('إشعار') }}
                                                @if($isUnread)
                                                    <span class="inline-block w-2 h-2 bg-indigo-500 rounded-full ms-1"></span>
                                                @endif
                                            </h3>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ $data['message'] ?? '' }}
                                            </p>
                                        </a>

                                        {{-- أزرار الإجراءات --}}
                                        <div class="flex items-center gap-1 flex-shrink-0">
                                            @if($isUnread)
                                                <form method="POST" action="{{ route('notifications.markAsRead', $notification->id) }}">
                                                    @csrf
                                                    <button type="submit"
                                                            title="{{ __('تعليم كمقروء') }}"
                                                            class="p-1.5 rounded-full text-gray-400 hover:text-green-600 dark:hover:text-green-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                        ✓
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}"
                                                  onsubmit="return confirm('{{ __('حذف هذا الإشعار؟') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        title="{{ __('حذف') }}"
                                                        class="p-1.5 rounded-full text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- التاريخ --}}
                                    <div class="mt-2 flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400">
                                        <span title="{{ $notification->created_at->format('Y-m-d H:i') }}">
                                            🕐 {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if($isUnread)
                                            <span class="px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full font-semibold">
                                                {{ __('جديد') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- الترقيم --}}
                @if($notifications->hasPages())
                    <div class="mt-6">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @else
                {{-- حالة عدم وجود إشعارات --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-12 text-center">
                    <div class="text-8xl mb-4">🔕</div>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('لا توجد إشعارات') }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        {{ __('ستظهر هنا الإشعارات المتعلقة بنشاطك على المنصة.') }}
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>