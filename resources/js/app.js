import Alpine from 'alpinejs';
window.Alpine = Alpine;

// ✅ دالة الشات - تُعرَّف قبل Alpine.start()
window.chatApp = function(conversationId, fetchUrl, replyUrl, readUrl, csrfToken, lastMessageId) {
    return {
        conversationId,
        fetchUrl,
        replyUrl,
        readUrl,
        csrfToken,
        lastMessageId,
        newMessageBody: '',
        sending: false,
        newMessages: [],
        pollInterval: null,
        showThreadMobile: true,

        init() {
            this.$nextTick(() => this.scrollToBottom());
            this.pollInterval = setInterval(() => this.fetchNewMessages(), 5000);
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
                    // Optimistic UI
                    this.newMessages.push({
                        id: 'temp-' + Date.now(),
                        body: body,
                        is_mine: true,
                        created_at_human: 'الآن',
                    });
                    this.newMessageBody = '';
                    this.$nextTick(() => this.scrollToBottom());

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
};

// ✅ دالة الجرس (Notifications) - للـ polling
window.notificationBell = function() {
    return {
        open: false,
        unreadCount: 0,
        notifications: [],

        init() {
            this.fetchNotifications();
            setInterval(() => this.fetchNotifications(), 30000);
            this.$watch('open', (value) => {
                if (value) this.fetchNotifications();
            });
        },

        async fetchNotifications() {
            try {
                const response = await fetch('/notifications/api/list', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) return;
                const data = await response.json();
                this.unreadCount = data.unread_count;
                this.notifications = data.notifications;
            } catch (error) {
                console.error('Notifications error:', error);
            }
        },

        async markAllAsRead() {
            try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                await fetch('/notifications/mark-all-as-read', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });
                this.fetchNotifications();
            } catch (error) {
                console.error('Mark all as read error:', error);
            }
        },
    };
};

Alpine.start();