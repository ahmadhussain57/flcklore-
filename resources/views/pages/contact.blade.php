<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📞 {{ __('تواصل معنا') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            {{-- Hero --}}
            <div class="mb-8 text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark:text-gray-200 mb-3">
                    {{ __('نحن هنا لمساعدتك') }}
                </h1>
                <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                    {{ __('هل لديك سؤال، اقتراح، أو ترغب بالتعاون معنا؟ املأ النموذج وسنعود إليك في أقرب وقت.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- معلومات التواصل --}}
                <div class="lg:col-span-1 space-y-4">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                        <h3 class="font-bold text-gray-800 dark:text-gray-200 mb-4">{{ __('معلومات التواصل') }}</h3>

                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xl">📧</span>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('البريد الإلكتروني') }}</div>
                                    <a href="mailto:info@folklore.com" class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        info@folklore.com
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xl">📱</span>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('الهاتف') }}</div>
                                    <a href="tel:+963111111111" dir="ltr" class="text-sm font-medium text-gray-800 dark:text-gray-200 hover:text-indigo-600 dark:hover:text-indigo-400 transition">
                                        +963 111 111 111
                                    </a>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-full bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center flex-shrink-0">
                                    <span class="text-xl">📍</span>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ __('العنوان') }}</div>
                                    <div class="text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ __('دمشق، سوريا') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl shadow-xl p-6 text-white">
                        <div class="text-4xl mb-3">💬</div>
                        <h3 class="font-bold mb-2">{{ __('استجابة سريعة') }}</h3>
                        <p class="text-sm opacity-90">
                            {{ __('نحرص على الرد على جميع الرسائل في غضون 24-48 ساعة.') }}
                        </p>
                    </div>
                </div>

                {{-- النموذج --}}
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">

                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                                ✓ {{ session('success') }}
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('pages.contact.submit') }}">
                            @csrf

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('الاسم') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        {{ __('البريد الإلكتروني') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                           class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>

                            <div class="mb-5">
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('الموضوع') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="subject" id="subject" value="{{ old('subject') }}" required
                                       class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div class="mb-5">
                                <label for="message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    {{ __('الرسالة') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea name="message" id="message" rows="6" required
                                          placeholder="{{ __('اكتب رسالتك هنا...') }}"
                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('message') }}</textarea>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-full shadow-md hover:shadow-lg transition flex items-center gap-2">
                                    <span>📤</span>
                                    {{ __('إرسال الرسالة') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>