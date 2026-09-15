<div class="flex {{ $isMine ? 'justify-end' : 'justify-start' }}">
    <div class="max-w-[75%] flex items-end gap-2 {{ $isMine ? 'flex-row-reverse' : '' }}">
        {{-- صورة رمزية --}}
        @if(!$isMine)
            <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold text-xs flex-shrink-0">
                {{ mb_substr($message->sender->name, 0, 1) }}
            </div>
        @endif

        {{-- الفقاعة --}}
        <div class="px-4 py-2 rounded-2xl shadow-sm
                    {{ $isMine
                        ? 'bg-indigo-600 text-white rounded-br-sm'
                        : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-sm' }}">
            <p class="text-sm break-words whitespace-pre-wrap">{{ $message->body }}</p>
            <p class="text-xs mt-1 {{ $isMine ? 'text-indigo-100' : 'text-gray-400' }} opacity-70">
                {{ $message->created_at->diffForHumans() }}
            </p>
        </div>
    </div>
</div>