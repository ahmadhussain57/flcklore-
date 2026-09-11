<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $content->title }}
            </h2>
            <a href="{{ route('content.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                ← {{ __('العودة إلى قائمتي') }}
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            {{-- رسائل التنبيه --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/50 border-l-4 border-green-500 text-green-700 dark:text-green-300 rounded-lg">
                    <span class="font-medium">✓</span> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/50 border-l-4 border-red-500 text-red-700 dark:text-red-300 rounded-lg">
                    <span class="font-medium">✗</span> {{ session('error') }}
                </div>
            @endif

            {{-- بطاقة المحتوى الرئيسية --}}
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-200 dark:border-gray-700">

                {{-- صورة الغلاف (أول صورة في الوسائط) --}}
                @php
                    $coverMedia = $content->media->where('media_type', 'image')->first();
                @endphp
                @if($coverMedia)
                    <div class="w-full h-64 md:h-96 overflow-hidden">
                        <img src="{{ $coverMedia->path }}" alt="{{ $content->title }}" class="w-full h-full object-cover">
                    </div>
                @else
                    <div class="w-full h-48 md:h-64 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center">
                        <span class="text-white text-4xl md:text-5xl font-bold">
                            {{ $content->type === 'article' ? '📝' : ($content->type === 'video' ? '🎬' : ($content->type === 'audio' ? '🎵' : '🖼️')) }}
                            {{ strtoupper($content->type) }}
                        </span>
                    </div>
                @endif

                <div class="p-6 lg:p-8">

                    {{-- شارات الحالة والنوع --}}
                    <div class="flex items-center gap-3 mb-4 flex-wrap">
                        <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-800 dark:text-indigo-200 rounded-full text-xs font-semibold uppercase tracking-wide">
                            {{ $content->type }}
                        </span>
                        @php
                            $statusMap = [
                                'draft' => ['label' => 'مسودة', 'class' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300'],
                                'pending_review' => ['label' => 'قيد المراجعة', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                'pending_edit' => ['label' => 'تعديل معلق', 'class' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                'published' => ['label' => 'منشور', 'class' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300'],
                                'rejected' => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300'],
                            ];
                            $s = $statusMap[$content->status] ?? $statusMap['draft'];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide {{ $s['class'] }}">
                            {{ $s['label'] }}
                        </span>
                    </div>

                    {{-- العنوان --}}
                    <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white leading-tight mb-4">
                        {{ $content->title }}
                    </h1>

                    {{-- معلومات الكاتب والتاريخ --}}
                    <div class="flex flex-wrap items-center gap-4 mb-6 text-sm text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700 pb-6">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold text-xs">
                                {{ mb_substr($content->author->name, 0, 2) }}
                            </div>
                            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $content->author->name }}</span>
                        </div>
                        <span>•</span>
                        <span>{{ $content->created_at->format('j M, Y') }}</span>
                        @if($content->published_at)
                            <span>•</span>
                            <span class="text-green-600 dark:text-green-400">
                                ✓ {{ __('نُشر في') }} {{ $content->published_at->format('j M, Y') }}
                            </span>
                        @endif
                    </div>

                    {{-- المحتوى النصي --}}
                    @if($content->body)
                        <div class="prose prose-lg dark:prose-invert max-w-none mb-6 text-gray-800 dark:text-gray-200">
                            {!! nl2br(e($content->body)) !!}
                        </div>
                    @endif

                    {{-- معرض الوسائط (صور، فيديو، صوت) --}}
                    @if($content->media->count() > 0)
                        <div class="mb-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase mb-4">
                                🖼️ {{ __('الوسائط المرفقة') }} ({{ $content->media->count() }})
                            </h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($content->media as $media)
                                    @if($media->media_type === 'image')
                                        {{-- صورة --}}
                                        <a href="{{ $media->path }}" target="_blank" class="block group relative overflow-hidden rounded-xl shadow-md hover:shadow-xl transition">
                                            <img src="{{ $media->path }}"
                                                 alt="{{ $media->original_name ?? $content->title }}"
                                                 class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition flex items-center justify-center">
                                                <span class="text-white opacity-0 group-hover:opacity-100 transition text-2xl">🔍</span>
                                            </div>
                                        </a>

                                    @elseif($media->media_type === 'video')
                                        {{-- فيديو --}}
                                        <div class="rounded-xl overflow-hidden shadow-md bg-black">
                                            <video controls class="w-full h-48" preload="metadata">
                                                <source src="{{ $media->path }}" type="{{ $media->mime_type ?? 'video/mp4' }}">
                                                {{ __('متصفحك لا يدعم تشغيل الفيديو.') }}
                                            </video>
                                            @if($media->original_name)
                                                <div class="px-3 py-2 bg-gray-900 text-white text-xs truncate">
                                                    🎬 {{ $media->original_name }}
                                                </div>
                                            @endif
                                        </div>

                                    @elseif($media->media_type === 'audio')
                                        {{-- صوت --}}
                                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl shadow-md">
                                            <div class="flex items-center gap-2 mb-3">
                                                <span class="text-2xl">🎵</span>
                                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 truncate">
                                                    {{ $media->original_name ?? __('ملف صوتي') }}
                                                </span>
                                            </div>
                                            <audio controls class="w-full">
                                                <source src="{{ $media->path }}" type="{{ $media->mime_type ?? 'audio/mpeg' }}">
                                                {{ __('متصفحك لا يدعم تشغيل الصوت.') }}
                                            </audio>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- معلومات إضافية --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                        @if($content->keywords)
                            <div>
                                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('كلمات مفتاحية') }}</span>
                                <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $content->keywords }}</p>
                            </div>
                        @endif
                        @if($content->geographic_location)
                            <div>
                                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('الموقع الجغرافي') }}</span>
                                <p class="text-gray-800 dark:text-gray-200 mt-1">📍 {{ $content->geographic_location }}</p>
                            </div>
                        @endif
                        @if($content->historical_importance)
                            <div class="md:col-span-2">
                                <span class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">{{ __('الأهمية التاريخية') }}</span>
                                <p class="text-gray-800 dark:text-gray-200 mt-1">{{ $content->historical_importance }}</p>
                            </div>
                        @endif
                    </div>

                    {{-- التصنيفات والأوسمة --}}
                    @if($content->categories->count() || $content->tags->count())
                        <div class="flex flex-wrap gap-2 mt-6">
                            @foreach($content->categories as $category)
                                <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/50 text-indigo-800 dark:text-indigo-200 rounded-full text-sm">
                                    #{{ $category->name }}
                                </span>
                            @endforeach
                            @foreach($content->tags as $tag)
                                <span class="px-3 py-1 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-full text-sm">
                                    #{{ $tag->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    {{-- سبب الرفض --}}
                    @if($content->rejection_reason)
                        <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg">
                            <h3 class="text-sm font-semibold text-red-700 dark:text-red-300 mb-1">
                                ⚠️ {{ __('سبب الرفض') }}
                            </h3>
                            <p class="text-red-800 dark:text-red-200">{{ $content->rejection_reason }}</p>
                        </div>
                    @endif

                    {{-- قسم الإعجابات والتعليقات (للمحتوى المنشور فقط) --}}
                    @if($content->status === 'published')
                        <div class="flex items-center gap-6 mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            {{-- زر الإعجاب --}}
                            <div class="flex items-center gap-2">
                                @auth
                                    <form action="{{ route('content.like.toggle', $content) }}" method="POST" class="inline">
                                        @csrf
                                        @php
                                            $userLiked = auth()->user()->likes()
                                                ->where('likeable_type', 'App\Models\Content')
                                                ->where('likeable_id', $content->id)
                                                ->exists();
                                        @endphp
                                        <button type="submit" class="group flex items-center gap-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 hover:border-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition duration-200">
                                            <span class="text-2xl group-hover:scale-110 transition-transform">
                                                {{ $userLiked ? '❤️' : '🤍' }}
                                            </span>
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                                {{ $content->likes()->count() }}
                                            </span>
                                        </button>
                                    </form>
                                @else
                                    <div class="flex items-center gap-1 px-4 py-2 rounded-full border border-gray-300 dark:border-gray-600 text-gray-500">
                                        <span class="text-2xl">🤍</span>
                                        <span class="text-sm font-medium">{{ $content->likes()->count() }}</span>
                                    </div>
                                @endauth
                            </div>

                            {{-- عدد التعليقات --}}
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                💬 {{ $content->comments()->count() }} {{ __('تعليق') }}
                            </div>
                        </div>

                        {{-- قسم التعليقات --}}
                        <div class="mt-10 pt-6 border-t-2 border-dashed border-gray-200 dark:border-gray-700">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">💬 {{ __('التعليقات') }}</h3>

                            {{-- نموذج إضافة تعليق --}}
                            @auth
                                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl">
                                    <form action="{{ route('content.comment.store', $content) }}" method="POST">
                                        @csrf
                                        <div class="flex items-start gap-3">
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold flex-shrink-0">
                                                {{ mb_substr(auth()->user()->name, 0, 2) }}
                                            </div>
                                            <div class="flex-1">
                                                <textarea name="body" rows="2" placeholder="{{ __('اكتب تعليقك...') }}"
                                                          class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 resize-none">{{ old('body') }}</textarea>
                                                @error('body') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                                <div class="mt-2 flex justify-end">
                                                    <button type="submit" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-full shadow-sm transition">
                                                        {{ __('أضف تعليقاً') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl text-center text-gray-600 dark:text-gray-400">
                                    <a href="{{ route('login') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('سجل الدخول') }}</a>
                                    {{ __('لتتمكن من التعليق والإعجاب.') }}
                                </div>
                            @endauth

                            {{-- قائمة التعليقات --}}
                            @php
                                $comments = $content->comments()->with('user')->latest()->get();
                            @endphp

                            @if($comments->count())
                                <div class="space-y-4">
                                    @foreach($comments as $comment)
                                        <div class="flex items-start gap-3 p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-200 dark:border-gray-700">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold flex-shrink-0">
                                                {{ mb_substr($comment->user->name, 0, 2) }}
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center justify-between gap-2">
                                                    <div>
                                                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $comment->user->name }}</span>
                                                        <span class="text-xs text-gray-500 dark:text-gray-400 mr-2">· {{ $comment->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    @auth
                                                        @if(auth()->id() === $comment->user_id || auth()->user()->hasPermissionTo('delete_any_content', 'web'))
                                                            <form action="{{ route('content.comment.destroy', ['content' => $content, 'comment' => $comment]) }}" method="POST" onsubmit="return confirm('{{ __('هل أنت متأكد من حذف هذا التعليق؟') }}')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="text-gray-400 hover:text-red-500 dark:text-gray-500 dark:hover:text-red-400 text-xs transition">
                                                                    {{ __('حذف') }}
                                                                </button>
                                                            </form>
                                                        @endif
                                                    @endauth
                                                </div>
                                                <p class="mt-1 text-gray-700 dark:text-gray-300 break-words">{{ $comment->body }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 dark:text-gray-400 py-6">
                                    {{ __('لا توجد تعليقات بعد. كن أول من يعلق!') }}
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- رسالة للمحتوى غير المنشور --}}
                        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-lg">
                            <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                ℹ️ {{ __('التعليقات والإعجابات متاحة فقط للمحتوى المنشور.') }}
                            </p>
                        </div>
                    @endif

                </div>
            </div>

            {{-- أزرار الإجراءات (للمؤلف) --}}
            @if(auth()->id() === $content->user_id)
                <div class="mt-6 flex flex-wrap gap-3">
                    @if(in_array($content->status, ['draft', 'rejected', 'pending_edit']))
                        <a href="{{ route('content.edit', $content) }}"
                           class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-full transition">
                            ✏️ {{ __('تعديل') }}
                        </a>
                    @endif

                    @if(in_array($content->status, ['draft', 'rejected']))
                        <form action="{{ route('content.submit', $content) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-full transition">
                                📤 {{ __('إرسال للمراجعة') }}
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('content.destroy', $content) }}" method="POST"
                          onsubmit="return confirm('{{ __('هل أنت متأكد من الحذف؟') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="px-5 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-full transition">
                            🗑️ {{ __('حذف') }}
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>