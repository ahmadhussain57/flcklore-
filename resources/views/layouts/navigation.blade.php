<nav
    x-data="{
        open: false,
        dark: document.documentElement.classList.contains('dark'),
        toggleTheme() {
            this.dark = !this.dark;
            localStorage.setItem('theme', this.dark ? 'dark' : 'light');
            document.documentElement.classList.toggle('dark', this.dark);
        }
    }"
    class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                {{-- الشعار --}}
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                {{-- روابط سطح المكتب --}}
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        {{ __('messages.home') }}
                    </x-nav-link>

                    @auth
                        <x-nav-link :href="route('role-upgrade.create')" :active="request()->routeIs('role-upgrade.*')">
                            {{ __('messages.role_upgrade') }}
                        </x-nav-link>

                        @if (Auth::user()->isStaff())
                            <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                                {{ __('messages.dashboard') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            {{-- يمين الشريط --}}
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                {{-- اللغة --}}
                <div class="me-3 flex items-center gap-1">
                    <a href="{{ route('locale.switch', 'ar') }}"
                       class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'ar' ? 'bg-gray-200 dark:bg-gray-700 font-bold text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">
                        ع
                    </a>
                    <a href="{{ route('locale.switch', 'en') }}"
                       class="text-xs px-2 py-1 rounded {{ app()->getLocale() === 'en' ? 'bg-gray-200 dark:bg-gray-700 font-bold text-gray-900 dark:text-gray-100' : 'text-gray-500 dark:text-gray-400' }}">
                        EN
                    </a>
                </div>

                {{-- الثيم --}}
                <button
                    type="button"
                    @click="toggleTheme()"
                    class="me-3 p-2 rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                >
                    {{-- شمس: يظهر في الوضع الداكن --}}
                    <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{-- قمر: يظهر في الوضع الفاتح --}}
                    <svg x-show="!dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>
                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('messages.profile') }}
                            </x-dropdown-link>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('messages.logout') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <div class="space-x-4">
                        <a href="{{ route('login') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            {{ __('messages.login') }}
                        </a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white">
                            {{ __('messages.register') }}
                        </a>
                    </div>
                @endauth
            </div>

            {{-- زر القائمة للجوال --}}
            <div class="-me-2 flex items-center sm:hidden">
                {{-- ثيم الجوال --}}
                <button
                    type="button"
                    @click="toggleTheme()"
                    class="me-1 p-2 rounded-md text-gray-500 dark:text-gray-400"
                >
                    <svg x-show="dark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg x-show="!dark" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>

                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- قائمة الجوال --}}
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                {{ __('messages.home') }}
            </x-responsive-nav-link>

            @auth
                <x-responsive-nav-link :href="route('role-upgrade.create')" :active="request()->routeIs('role-upgrade.*')">
                    {{ __('messages.role_upgrade') }}
                </x-responsive-nav-link>

                @if (Auth::user()->isStaff())
                    <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('messages.dashboard') }}
                    </x-responsive-nav-link>
                @endif
            @endauth

            {{-- لغة الجوال --}}
            <div class="px-4 py-2 flex gap-2">
                <a href="{{ route('locale.switch', 'ar') }}" class="text-sm {{ app()->getLocale() === 'ar' ? 'font-bold' : '' }}">ع</a>
                <a href="{{ route('locale.switch', 'en') }}" class="text-sm {{ app()->getLocale() === 'en' ? 'font-bold' : '' }}">EN</a>
            </div>
        </div>

        @auth
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('messages.profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                            {{ __('messages.logout') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600 space-y-1">
                <x-responsive-nav-link :href="route('login')">
                    {{ __('messages.login') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('register')">
                    {{ __('messages.register') }}
                </x-responsive-nav-link>
            </div>
        @endauth
    </div>
</nav>