<x-app-layout>
    <x-slot name="meta">
        <x-meta-tags title="محادثة جديدة" description="ابدأ محادثة مع مستخدم." />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ✏️ {{ __('محادثة جديدة') }}
            </h2>
            <a href="{{ route('messages.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← {{ __('العودة إلى الرسائل') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">

                <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 border-l-4 border-blue-500 rounded-lg">
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        💡 {{ __('اكتب اسم المستخدم للبحث عنه، ثم اختره لبدء محادثة.') }}
                    </p>
                </div>

                {{-- حقل البحث --}}
                <div class="mb-4" x-data="{ query: '' }">
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🔍 {{ __('ابحث عن مستخدم') }}
                    </label>
                    <input type="text"
                           id="search"
                           x-model="query"
                           placeholder="{{ __('اكتب اسماً للبحث...') }}"
                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    {{-- قائمة المستخدمين --}}
                    <div class="mt-4 max-h-96 overflow-y-auto">
                        @forelse($users as $user)
                            <form method="POST" action="{{ route('messages.store') }}"
                                  class="user-item"
                                  x-show="query === '' || '{{ strtolower($user->name) }}'.includes(query.toLowerCase()) || '{{ strtolower($user->email) }}'.includes(query.toLowerCase())">
                                @csrf
                                <input type="hidden" name="recipient_id" value="{{ $user->id }}">
                                <button type="submit"
                                        class="w-full text-start flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition border border-transparent hover:border-indigo-200 dark:hover:border-indigo-800">
                                    <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold flex-shrink-0">
                                        {{ mb_substr($user->name, 0, 2) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="font-semibold text-gray-800 dark:text-gray-200 truncate">
                                            {{ $user->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            {{ $user->email }}
                                        </div>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($user->getRoleNames() as $role)
                                                <span class="px-2 py-0.5 text-xs rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                                                    {{ $role }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <span class="text-indigo-600 dark:text-indigo-400 text-sm font-semibold flex-shrink-0">
                                        {{ __('مراسلة') }} →
                                    </span>
                                </button>
                            </form>
                        @empty
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                {{ __('لا يوجد مستخدمون آخرون.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>