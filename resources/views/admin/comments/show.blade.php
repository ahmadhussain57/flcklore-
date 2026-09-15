<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    🔍 {{ __('تفاصيل التعليق #:id', ['id' => mb_substr($comment->id, -6)]) }}
                </h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('مراجعة تعليق واحد بكل تفاصيله') }}
                </p>
            </div>
            <div>
                @php
                    $isContentComment = $comment->commentable_type === \App\Models\Content::class;
                @endphp
                <a href="{{ $isContentComment
                        ? route('admin.comments.content.index')
                        : route('admin.comments.product.index') }}"
                   class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                    ← {{ __('العودة إلى فهرس التعليقات') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6" x-data="{ rejectOpen: false }">

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

            {{-- ==========================================
                 ✅ بطاقة حالة التعليق + الإجراءات السريعة
                 ========================================== --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
                @php
                    $statusColors = [
                        \App\Models\Comment::STATUS_PENDING  => ['bg' => 'bg-yellow-50 dark:bg-yellow-900/20', 'border' => 'border-yellow-500', 'text' => 'text-yellow-800 dark:text-yellow-200', 'icon' => '⏳', 'label' => __('قيد المراجعة')],
                        \App\Models\Comment::STATUS_APPROVED => ['bg' => 'bg-green-50 dark:bg-green-900/20', 'border' => 'border-green-500', 'text' => 'text-green-800 dark:text-green-200', 'icon' => '✅', 'label' => __('موافق عليه')],
                        \App\Models\Comment::STATUS_REJECTED => ['bg' => 'bg-red-50 dark:bg-red-900/20', 'border' => 'border-red-500', 'text' => 'text-red-800 dark:text-red-200', 'icon' => '❌', 'label' => __('مرفوض')],
                    ];
                    $current = $statusColors[$comment->status] ?? $statusColors[\App\Models\Comment::STATUS_PENDING];
                @endphp

                <div class="{{ $current['bg'] }} border-l-4 {{ $current['border'] }} p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="text-5xl">{{ $current['icon'] }}</div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ __('حالة التعليق') }}
                                </div>
                                <div class="text-2xl font-bold {{ $current['text'] }}">
                                    {{ $current['label'] }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            @if($comment->isPending())
                                {{-- موافقة --}}
                                <form action="{{ route('admin.comments.approve', $comment) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl shadow-md transition flex items-center gap-2">
                                        ✅ {{ __('الموافقة والنشر') }}
                                    </button>
                                </form>

                                {{-- رفض --}}
                                <button type="button"
                                        @click="rejectOpen = !rejectOpen"
                                        class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-xl shadow-md transition flex items-center gap-2">
                                    ❌ {{ __('رفض التعليق') }}
                                </button>
                            @endif

                            {{-- حذف --}}
                            <form action="{{ route('admin.comments.destroy', $comment) }}"
                                  method="POST"
                                  onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا التعليق نهائياً؟ لا يمكن التراجع.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-semibold rounded-xl transition">
                                    🗑️ {{ __('حذف') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- نموذج الرفض --}}
                    @if($comment->isPending())
                        <div x-show="rejectOpen"
                             x-transition
                             x-cloak
                             class="mt-5 p-5 bg-white dark:bg-gray-900 rounded-xl border-2 border-red-300 dark:border-red-700">
                            <form action="{{ route('admin.comments.reject', $comment) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <label class="block text-sm font-bold text-red-800 dark:text-red-200 mb-2">
                                    ⚠️ {{ __('سبب الرفض') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea name="rejection_reason"
                                          rows="3"
                                          required
                                          minlength="5"
                                          maxlength="500"
                                          placeholder="{{ __('اكتب سبباً واضحاً سيُرسل إلى صاحب التعليق (5-500 حرف)...') }}"
                                          class="w-full rounded-lg border-red-300 dark:border-red-700 dark:bg-gray-800 dark:text-white focus:border-red-500 focus:ring-red-500 resize-none">{{ old('rejection_reason') }}</textarea>
                                @error('rejection_reason')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror

                                <div class="mt-3 flex justify-end gap-2">
                                    <button type="button"
                                            @click="rejectOpen = false"
                                            class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 font-medium rounded-lg transition">
                                        {{ __('إلغاء') }}
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition">
                                        ❌ {{ __('تأكيد الرفض وإرسال الإشعار') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ==========================================
                 ✅ نص التعليق
                 ========================================== --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-3">
                    💬 {{ __('نص التعليق') }}
                </h3>
                <div class="p-5 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-200 dark:border-gray-700">
                    <p class="text-gray-800 dark:text-gray-200 leading-relaxed whitespace-pre-wrap break-words">
                        {{ $comment->body }}
                    </p>
                </div>
            </div>

            {{-- ==========================================
                 ✅ معلومات المُعلِّق
                 ========================================== --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">
                    👤 {{ __('معلومات المُعلِّق') }}
                </h3>
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold text-xl flex-shrink-0">
                        {{ mb_substr($comment->user->name, 0, 2) }}
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-lg text-gray-800 dark:text-gray-200">
                            {{ $comment->user->name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $comment->user->email }}
                        </div>
                        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ __('معرّف المستخدم:') }} <code class="font-mono">{{ $comment->user->id }}</code>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ==========================================
                 ✅ العنصر المرتبط (محتوى أو منتج)
                 ========================================== --}}
            @if($comment->commentable)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">
                        @if($comment->commentable_type === \App\Models\Content::class)
                            📄 {{ __('المحتوى المرتبط') }}
                        @else
                            🛒 {{ __('المنتج المرتبط') }}
                        @endif
                    </h3>
                    <div class="flex flex-wrap items-center justify-between gap-4 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-200 dark:border-gray-700">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-gray-800 dark:text-gray-200">
                                {{ $comment->commentable->title }}
                            </div>
                            @if(!empty($comment->commentable->slug))
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <code class="font-mono">{{ $comment->commentable->slug }}</code>
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($comment->commentable_type === \App\Models\Content::class)
                                <a href="{{ route('articles.show', $comment->commentable->slug) }}"
                                   target="_blank"
                                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                    👁️ {{ __('عرض المحتوى') }}
                                </a>
                            @else
                                <a href="{{ route('shop.show', $comment->commentable->slug) }}"
                                   target="_blank"
                                   class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition">
                                    👁️ {{ __('عرض المنتج') }}
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ==========================================
                 ✅ بيانات المراجعة (إن وُجدت)
                 ========================================== --}}
            @if($comment->reviewed_at || $comment->reviewer)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                    <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">
                        🧑‍⚖️ {{ __('بيانات المراجعة') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- من راجعه --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                {{ __('راجعه') }}
                            </div>
                            @if($comment->reviewer)
                                <div class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $comment->reviewer->name }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $comment->reviewer->email }}
                                </div>
                            @else
                                <div class="text-gray-400 italic">
                                    {{ __('غير معروف') }}
                                </div>
                            @endif
                        </div>

                        {{-- متى --}}
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl">
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                {{ __('تاريخ المراجعة') }}
                            </div>
                            @if($comment->reviewed_at)
                                <div class="font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $comment->reviewed_at->format('Y-m-d H:i') }}
                                </div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $comment->reviewed_at->diffForHumans() }}
                                </div>
                            @else
                                <div class="text-gray-400 italic">
                                    {{ __('لم تتم المراجعة بعد') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- سبب الرفض --}}
                    @if($comment->status === \App\Models\Comment::STATUS_REJECTED && $comment->rejection_reason)
                        <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl border-l-4 border-red-500">
                            <div class="text-xs font-semibold text-red-700 dark:text-red-300 mb-1">
                                ⚠️ {{ __('سبب الرفض') }}
                            </div>
                            <p class="text-red-800 dark:text-red-200">
                                {{ $comment->rejection_reason }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ==========================================
                 ✅ البيانات الوصفية
                 ========================================== --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6 lg:p-8">
                <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">
                    ℹ️ {{ __('بيانات إضافية') }}
                </h3>
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('معرّف التعليق') }}</dt>
                        <dd class="font-mono text-gray-800 dark:text-gray-200 break-all mt-1">{{ $comment->id }}</dd>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('تاريخ الإنشاء') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200 mt-1">
                            {{ $comment->created_at->format('Y-m-d H:i') }}
                            <span class="text-xs text-gray-500 block">{{ $comment->created_at->diffForHumans() }}</span>
                        </dd>
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-900/30 rounded-lg">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ __('آخر تحديث') }}</dt>
                        <dd class="text-gray-800 dark:text-gray-200 mt-1">
                            {{ $comment->updated_at->format('Y-m-d H:i') }}
                            <span class="text-xs text-gray-500 block">{{ $comment->updated_at->diffForHumans() }}</span>
                        </dd>
                    </div>
                </dl>
            </div>

        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</x-app-layout>