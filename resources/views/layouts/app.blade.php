<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
><head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- ✅ Meta Tags --}}
    {{ $meta ?? '' }}

    <!-- منع وميض الثيم قبل تحميل Alpine -->
    <script>
        (function () {
            const theme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            if (theme === 'dark' || (!theme && prefersDark)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8F%BA%3C/text%3E%3C/svg%3E">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>


<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 flex flex-col">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        {{-- ✅ Footer --}}
        @include('layouts.footer')
    </div>

    {{-- ======================
         Alpine.js: دالة الجرس (Notifications)
         ====================== --}}
    @auth
    <script>
        function notificationBell() {
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

                fetchNotifications() {
                    fetch('{{ route('notifications.api') }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                    .then(response => response.json())
                    .then(data => {
                        this.unreadCount = data.unread_count;
                        this.notifications = data.notifications;
                    })
                    .catch(error => console.error('Notifications error:', error));
                },

                markAllAsRead() {
                    fetch('{{ route('notifications.markAllAsRead') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                    })
                    .then(() => this.fetchNotifications())
                    .catch(error => console.error('Mark all as read error:', error));
                }
            };
        }
    </script>
    @endauth
</body>
</html>