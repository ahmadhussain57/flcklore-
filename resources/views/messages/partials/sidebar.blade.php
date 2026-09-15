<div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
    <h3 class="font-bold text-gray-800 dark:text-gray-200">💬 {{ __('المحادثات') }}</h3>
</div>

<div class="divide-y divide-gray-100 dark:divide-gray-700">
    @forelse($conversations as $conv)
        <a href="{{ route('messages.show', $conv) }}"
           class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition
                  {{ isset($activeId) && $activeId === $conv->id ? 'bg-indigo-50 dark:bg-indigo-900/20 border-r-4 border-indigo-500' : '' }}">
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold flex-shrink-0 text-sm">
                    {{ $conv->other_user ? mb_substr($conv->other_user->name, 0, 2) : '?' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="font-semibold text-sm text-gray-800 dark:text-gray-200 truncate">
                            {{ $conv->other_user?->name ?? __('محذوف') }}
                        </span>
                        @if($conv->unread_count > 0)
                            <span class="px-1.5 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full flex-shrink-0">
                                {{ $conv->unread_count }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                        @if($conv->last_message)
                            {{ Str::limit($conv->last_message->body, 30) }}
                        @else
                            <span class="italic">{{ __('لا توجد رسائل') }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </a>
    @empty
        <div class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">
            {{ __('لا توجد محادثات أخرى.') }}
        </div>
    @endforelse
</div>