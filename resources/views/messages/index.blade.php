<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="الرسائل" description="محادثاتك مع المستخدمين الآخرين." />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📬 {{ __('الرسائل') }}
            </h2>
            <a href="{{ route('messages.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                {{ __('محادثة جديدة') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

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

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 min-h-[600px]">

                    {{-- قائمة المحادثات --}}
                    <div class="lg:col-span-1 border-b lg:border-b-0 lg:border-l border-gray-200 dark:border-gray-700">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                            <h3 class="font-bold text-gray-800 dark:text-gray-200">💬 {{ __('المحادثات') }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $conversations->total() }} {{ __('محادثة') }}
                            </p>
                        </div>

                        @if($conversations->count())
                            <div class="divide-y divide-gray-100 dark:divide-gray-700 max-h-[600px] overflow-y-auto">
                                @foreach($conversations as $conversation)
                                    <a href="{{ route('messages.show', $conversation) }}"
                                       class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                                        <div class="flex items-start gap-3">
                                            {{-- صورة رمزية --}}
                                            <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold flex-shrink-0">
                                                {{ $conversation->other_user ? mb_substr($conversation->other_user->name, 0, 2) : '?' }}
                                            </div>

                                            {{-- المحتوى --}}
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center justify-between gap-2 mb-1">
                                                    <span class="font-semibold text-gray-800 dark:text-gray-200 truncate">
                                                        {{ $conversation->other_user?->name ?? __('مستخدم محذوف') }}
                                                    </span>
                                                    @if($conversation->unread_count > 0)
                                                        <span class="px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full flex-shrink-0">
                                                            {{ $conversation->unread_count }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">
                                                    @if($conversation->last_message)
                                                        @if($conversation->last_message->user_id === auth()->id())
                                                            <span class="text-gray-400">{{ __('أنت:') }}</span>
                                                        @endif
                                                        {{ Str::limit($conversation->last_message->body, 40) }}
                                                    @else
                                                        <span class="italic">{{ __('لا توجد رسائل بعد') }}</span>
                                                    @endif
                                                </p>
                                                @if($conversation->last_message_at)
                                                    <p class="text-xs text-gray-400 mt-1">
                                                        {{ $conversation->last_message_at->diffForHumans() }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>

                            @if($conversations->hasPages())
                                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                                    {{ $conversations->links() }}
                                </div>
                            @endif
                        @else
                            <div class="p-12 text-center">
                                <div class="text-6xl mb-4">📭</div>
                                <h3 class="font-bold text-gray-700 dark:text-gray-300 mb-2">
                                    {{ __('لا توجد محادثات') }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                    {{ __('ابدأ محادثة جديدة مع أي مستخدم.') }}
                                </p>
                                <a href="{{ route('messages.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-full transition">
                                    + {{ __('محادثة جديدة') }}
                                </a>
                            </div>
                        @endif
                    </div>

                    {{-- منطقة فارغة (تُملأ عند فتح محادثة) --}}
                    <div class="lg:col-span-2 flex items-center justify-center bg-gray-50 dark:bg-gray-900/30 hidden lg:flex">
                        <div class="text-center p-12">
                            <div class="text-8xl mb-4">💬</div>
                            <h3 class="text-xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                                {{ __('اختر محادثة للبدء') }}
                            </h3>
                            <p class="text-gray-500 dark:text-gray-400">
                                {{ __('اختر محادثة من القائمة الجانبية، أو ابدأ محادثة جديدة.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>