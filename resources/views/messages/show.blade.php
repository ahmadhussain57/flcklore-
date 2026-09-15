<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="محادثة مع {{ $otherUser?->name ?? 'مستخدم' }}" />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('messages.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline text-sm lg:hidden">
                    ← {{ __('الرسائل') }}
                </a>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    💬 {{ $otherUser?->name ?? __('محادثة') }}
                </h2>
            </div>
        </div>
    </x-slot>

    <div class="py-4 lg:py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden"
x-data="chatApp(
    '{{ $conversation->id }}',
    '{{ route('messages.fetch', $conversation) }}',
    '{{ route('messages.reply', $conversation) }}',
    '{{ route('messages.read', $conversation) }}',
    '{{ csrf_token() }}',
    '{{ $messages->last()?->id ?? "" }}'
)"                 x-init="init()">

                <div class="grid grid-cols-1 lg:grid-cols-3" style="min-height: 600px; max-height: 80vh;">

                    {{-- Sidebar (قائمة المحادثات) - يختفي على الجوال عند فتح محادثة --}}
                    <div class="lg:col-span-1 border-b lg:border-b-0 lg:border-l border-gray-200 dark:border-gray-700 overflow-y-auto max-h-[600px]"
                         :class="{ 'hidden lg:block': showThreadMobile }">
                        @include('messages.partials.sidebar', ['conversations' => $conversations, 'activeId' => $conversation->id])
                    </div>

                    {{-- المحادثة --}}
                    <div class="lg:col-span-2 flex flex-col max-h-[600px]"
                         :class="{ 'hidden lg:flex': !showThreadMobile }">

                        {{-- رأس المحادثة --}}
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex items-center gap-3">
                            <button @click="showThreadMobile = false"
                                    class="lg:hidden p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition"
                                    title="{{ __('العودة') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold flex-shrink-0">
                                {{ $otherUser ? mb_substr($otherUser->name, 0, 2) : '?' }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-bold text-gray-800 dark:text-gray-200 truncate">
                                    {{ $otherUser?->name ?? __('مستخدم محذوف') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    {{ $otherUser?->email }}
                                </div>
                            </div>
                        </div>

                        {{-- الرسائل --}}
                        <div id="messages-container"
                             class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50 dark:bg-gray-900/30"
                             x-ref="messagesContainer">

                            @foreach($messages as $message)
                                @include('messages.partials.message', ['message' => $message, 'isMine' => $message->user_id === auth()->id()])
                            @endforeach

                            {{-- حاوية للرسائل الجديدة (Alpine.js) --}}
                            <template x-for="msg in newMessages" :key="msg.id">
                                <div class="flex" :class="msg.is_mine ? 'justify-end' : 'justify-start'">
                                    <div class="max-w-[75%] px-4 py-2 rounded-2xl shadow-sm"
                                         :class="msg.is_mine
                                            ? 'bg-indigo-600 text-white rounded-br-sm'
                                            : 'bg-white dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-bl-sm'">
                                        <p class="text-sm break-words" x-text="msg.body"></p>
                                        <p class="text-xs mt-1 opacity-70" x-text="msg.created_at_human"></p>
                                    </div>
                                </div>
                            </template>

                            @if($messages->isEmpty())
                                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    <div class="text-4xl mb-2">👋</div>
                                    <p>{{ __('ابدأ المحادثة بإرسال رسالة.') }}</p>
                                </div>
                            @endif
                        </div>

                        {{-- نموذج الإرسال --}}
                        <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                            <form @submit.prevent="sendMessage()" class="flex items-end gap-2">
                                <textarea x-model="newMessageBody"
                                          @keydown.enter.prevent="if (!$event.shiftKey) sendMessage()"
                                          rows="1"
                                          placeholder="{{ __('اكتب رسالتك...') }}"
                                          class="flex-1 resize-none rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          :disabled="sending"></textarea>
                                <button type="submit"
                                        :disabled="sending || newMessageBody.trim() === ''"
                                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold rounded-xl shadow-md transition flex items-center gap-1">
                                    <span x-show="!sending">📤</span>
                                    <span x-show="sending">⏳</span>
                                    <span>{{ __('إرسال') }}</span>
                                </button>
                            </form>
                            <p class="text-xs text-gray-400 mt-2">
                                💡 {{ __('اضغط Enter للإرسال، و Shift+Enter لسطر جديد.') }}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alpine.js: Chat Logic --}}
    <script>
        function chatApp(conversationId, fetchUrl, replyUrl, readUrl, csrfToken) {
            return {
                conversationId,
                fetchUrl,
                replyUrl,
                readUrl,
                csrfToken,
                newMessageBody: '',
                sending: false,
                newMessages: [],
                lastMessageId: '{{ $messages->last()?->id ?? "" }}',
                pollInterval: null,
                showThreadMobile: true, // على الجوال: عرض المحادثة افتراضياً

                init() {
                    // التمرير للأسفل عند التحميل
                    this.$nextTick(() => this.scrollToBottom());

                    // Polling كل 5 ثوان
                    this.pollInterval = setInterval(() => this.fetchNewMessages(), 5000);

                    // تعليم كمقروء
                    this.markAsRead();
                },

                async fetchNewMessages() {
                    try {
                        const url = new URL(this.fetchUrl, window.location.origin);
                        if (this.lastMessageId) {
                            url.searchParams.append('last_message_id', this.lastMessageId);
                        }

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });

                        if (!response.ok) return;

                        const data = await response.json();

                        if (data.messages && data.messages.length > 0) {
                            data.messages.forEach(msg => {
                                this.newMessages.push(msg);
                                this.lastMessageId = msg.id;
                            });

                            this.$nextTick(() => this.scrollToBottom());
                            this.markAsRead();
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                    }
                },

                async sendMessage() {
                    const body = this.newMessageBody.trim();
                    if (!body || this.sending) return;

                    this.sending = true;

                    try {
                        const response = await fetch(this.replyUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                            body: JSON.stringify({ body }),
                        });

                        if (response.ok || response.redirected) {
                            // أضف الرسالة مباشرة (Optimistic UI)
                            const tempMsg = {
                                id: 'temp-' + Date.now(),
                                body: body,
                                is_mine: true,
                                created_at_human: '{{ __("الآن") }}',
                            };
                            this.newMessages.push(tempMsg);
                            this.newMessageBody = '';
                            this.$nextTick(() => this.scrollToBottom());

                            // ثم جلب الرسائل الفعلية
                            setTimeout(() => this.fetchNewMessages(), 500);
                        }
                    } catch (error) {
                        console.error('Send error:', error);
                    } finally {
                        this.sending = false;
                    }
                },

                async markAsRead() {
                    try {
                        await fetch(this.readUrl, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            credentials: 'same-origin',
                        });
                    } catch (error) {
                        console.error('Mark read error:', error);
                    }
                },

                scrollToBottom() {
                    const container = document.getElementById('messages-container');
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                },
            };
        }
    </script>
</x-app-layout>