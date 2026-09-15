<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    💬 {{ __('مراجعة تعليقات المنتجات') }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('التعليقات المعلقة على المنتجات التسويقية بانتظار المراجعة') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.comments.content.index') }}"
                   class="px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition">
                    📄 {{ __('تعليقات المحتوى') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ✅ رسائل التنبيه --}}
            @if(session('success'))
                <div class="p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    ✓ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    ✗ {{ session('error') }}
                </div>
            @endif

            {{-- ✅ بطاقات الإحصائيات --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">⏳ {{ __('قيد المراجعة') }}</div>
                            <div class="text-3xl font-bold text-yellow-600 dark:text-yellow-400 mt-1">
                                {{ number_format($stats['pending']) }}
                            </div>
                        </div>
                        <div class="text-4xl opacity-30">⏳</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">✅ {{ __('موافق عليها') }}</div>
                            <div class="text-3xl font-bold text-green-600 dark:text-green-400 mt-1">
                                {{ number_format($stats['approved']) }}
                            </div>
                        </div>
                        <div class="text-4xl opacity-30">✅</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">❌ {{ __('مرفوضة') }}</div>
                            <div class="text-3xl font-bold text-red-600 dark:text-red-400 mt-1">
                                {{ number_format($stats['rejected']) }}
                            </div>
                        </div>
                        <div class="text-4xl opacity-30">❌</div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow p-5 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">🧑‍⚖️ {{ __('مراجعاتي') }}</div>
                            <div class="text-3xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                                {{ number_format($stats['my_reviews']) }}
                            </div>
                        </div>
                        <div class="text-4xl opacity-30">🧑‍⚖️</div>
                    </div>
                </div>
            </div>

            {{-- ✅ قائمة التعليقات --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                        📋 {{ __('التعليقات المعلقة') }}
                    </h3>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __(':count تعليق', ['count' => $comments->total()]) }}
                    </span>
                </div>

                @if($comments->count())
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($comments as $comment)
                            <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-900/30 transition"
                                 x-data="{ rejectOpen: false }">

                                {{-- رأس التعليق --}}
                                <div class="flex items-start gap-4">
                                    {{-- صورة رمزية --}}
                                    <div class="w-12 h-12 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold flex-shrink-0">
                                        {{ mb_substr($comment->user->name, 0, 2) }}
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        {{-- معلومات صاحب التعليق --}}
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <span class="font-semibold text-gray-800 dark:text-gray-200">
                                                {{ $comment->user->name }}
                                            </span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                · {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                            <span class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/50 text-yellow-800 dark:text-yellow-200 text-xs font-bold rounded-full">
                                                ⏳ {{ __('قيد المراجعة') }}
                                            </span>
                                        </div>

                                        {{-- نص التعليق --}}
                                        <p class="text-gray-700 dark:text-gray-300 break-words line-clamp-3">
                                            {{ $comment->body }}
                                        </p>

                                        {{-- رابط المنتج --}}
                                        @if($comment->commentable)
                                            <div class="mt-2 text-sm flex items-center gap-2 flex-wrap">
                                                <span class="text-gray-500 dark:text-gray-400">🛒 {{ __('على المنتج:') }}</span>
                                                <a href="{{ route('shop.show', $comment->commentable->slug) }}"
                                                   target="_blank"
                                                   class="text-indigo-600 dark:text-indigo-400 hover:underline font-medium">
                                                    {{ $comment->commentable->title }}
                                                </a>
                                                @if($comment->commentable->isPhysical())
                                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full">
                                                        🏺 {{ __('مادي') }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-full">
                                                        💾 {{ __('رقمي') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif

                                        {{-- أزرار الإجراءات --}}
                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            {{-- عرض --}}
                                            <a href="{{ route('admin.comments.show', $comment) }}"
                                               class="px-3 py-1.5 text-xs font-medium bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition">
                                                👁️ {{ __('عرض') }}
                                            </a>

                                            {{-- موافقة --}}
                                            <form action="{{ route('admin.comments.approve', $comment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-medium bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                                                    ✅ {{ __('موافقة') }}
                                                </button>
                                            </form>

                                            {{-- رفض --}}
                                            <button type="button"
                                                    @click="rejectOpen = !rejectOpen"
                                                    class="px-3 py-1.5 text-xs font-medium bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                                ❌ {{ __('رفض') }}
                                            </button>

                                            {{-- حذف --}}
                                            <form action="{{ route('admin.comments.destroy', $comment) }}"
                                                  method="POST"
                                                  class="inline"
                                                  onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا التعليق نهائياً؟') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1.5 text-xs font-medium text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition">
                                                    🗑️ {{ __('حذف') }}
                                                </button>
                                            </form>
                                        </div>

                                        {{-- نموذج الرفض (يظهر عند الضغط على زر الرفض) --}}
                                        <div x-show="rejectOpen"
                                             x-transition
                                             x-cloak
                                             class="mt-3 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border border-red-200 dark:border-red-800">
                                            <form action="{{ route('admin.comments.reject', $comment) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <label class="block text-sm font-medium text-red-800 dark:text-red-200 mb-2">
                                                    ⚠️ {{ __('سبب الرفض') }} <span class="text-red-500">*</span>
                                                </label>
                                                <textarea name="rejection_reason"
                                                          rows="2"
                                                          required
                                                          minlength="5"
                                                          maxlength="500"
                                                          placeholder="{{ __('اكتب سبباً واضحاً للمُعلِّق (5-500 حرف)...') }}"
                                                          class="w-full rounded-lg border-red-300 dark:border-red-700 dark:bg-gray-800 dark:text-white text-sm focus:border-red-500 focus:ring-red-500 resize-none"></textarea>
                                                <div class="mt-2 flex justify-end gap-2">
                                                    <button type="button"
                                                            @click="rejectOpen = false"
                                                            class="px-3 py-1.5 text-xs font-medium bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg transition">
                                                        {{ __('إلغاء') }}
                                                    </button>
                                                    <button type="submit"
                                                            class="px-3 py-1.5 text-xs font-medium bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                                                        ❌ {{ __('تأكيد الرفض') }}
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- ✅ Pagination --}}
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        {{ $comments->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <h4 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">
                            {{ __('لا توجد تعليقات معلقة') }}
                        </h4>
                        <p class="text-gray-500 dark:text-gray-400">
                            {{ __('كل التعليقات على المنتجات تمت مراجعتها. عمل رائع!') }}
                        </p>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Alpine x-cloak style --}}
    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>