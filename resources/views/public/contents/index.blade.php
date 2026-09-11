<x-app-layout>
    <x-slot name="meta">
    <x-meta-tags
        title="المقالات والقصص الفلكلورية"
        description="اقرأ أحدث المقالات والقصص الشعبية الموثقة من تراثنا العربي."
    />
</x-slot>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📚 {{ __('المقالات والقصص الفلكلورية') }}
            </h2>
            @auth
                @if(auth()->user()->hasAnyRole(['content_author', 'content_admin']))
                    <a href="{{ route('content.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                        + {{ __('مقال جديد') }}
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ✅ Hero Section --}}
            <div class="mb-8 p-8 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 rounded-2xl shadow-xl text-white text-center">
                <h1 class="text-3xl md:text-4xl font-bold mb-2">{{ __('اكتشف كنوز الفلكلور') }}</h1>
                <p class="text-lg opacity-90">{{ __('مقالات، قصص، صور، فيديو، وصوت من التراث الشعبي') }}</p>
            </div>

            {{-- ✅ شريط الفلاتر والبحث --}}
            <div class="mb-6 bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
                <form method="GET" action="{{ route('articles.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">

                    {{-- بحث --}}
                    <div class="md:col-span-2">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="{{ __('ابحث في العنوان أو المحتوى...') }}"
                               class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    {{-- النوع --}}
                    <div>
                        <select name="type"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">{{ __('كل الأنواع') }}</option>
                            <option value="article" {{ request('type') === 'article' ? 'selected' : '' }}>📝 {{ __('مقال') }}</option>
                            <option value="image" {{ request('type') === 'image' ? 'selected' : '' }}>🖼️ {{ __('صورة') }}</option>
                            <option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>🎬 {{ __('فيديو') }}</option>
                            <option value="audio" {{ request('type') === 'audio' ? 'selected' : '' }}>🎵 {{ __('صوت') }}</option>
                        </select>
                    </div>

                    {{-- الترتيب --}}
                    <div>
                        <select name="sort"
                                class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="latest" {{ request('sort', 'latest') === 'latest' ? 'selected' : '' }}>🆕 {{ __('الأحدث') }}</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>🕐 {{ __('الأقدم') }}</option>
                            <option value="popular" {{ request('sort') === 'popular' ? 'selected' : '' }}>🔥 {{ __('الأكثر إعجاباً') }}</option>
                        </select>
                    </div>

                    {{-- زر البحث --}}
                    <div class="md:col-span-4 flex gap-2">
                        <button type="submit"
                                class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow transition">
                            🔍 {{ __('تطبيق') }}
                        </button>
                        @if(request()->anyFilled(['q', 'type', 'sort', 'category']))
                            <a href="{{ route('articles.index') }}"
                               class="px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl transition">
                                ✖ {{ __('مسح') }}
                            </a>
                        @endif
                    </div>
                </form>

                {{-- التصنيفات السريعة --}}
                @if($categories->count())
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap gap-2">
                            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 self-center">{{ __('التصنيفات:') }}</span>
                            <a href="{{ route('articles.index') }}"
                               class="px-3 py-1 text-sm rounded-full transition {{ !request('category') ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                {{ __('الكل') }}
                            </a>
                            @foreach($categories as $cat)
                                <a href="{{ route('articles.index', array_merge(request()->query(), ['category' => $cat->slug])) }}"
                                   class="px-3 py-1 text-sm rounded-full transition {{ request('category') === $cat->slug ? 'bg-indigo-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600' }}">
                                    {{ $cat->name }}
                                    <span class="text-xs opacity-75">({{ $cat->contents_count }})</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ✅ شبكة المقالات --}}
            @if($contents->count())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($contents as $content)
                        <article class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden group">
                            {{-- صورة الغلاف --}}
                            <a href="{{ route('articles.show', $content->slug) }}" class="block relative overflow-hidden h-48">
                                @php $cover = $content->media->where('media_type', 'image')->first(); @endphp
                                @if($cover)
                                    <img src="{{ $cover->path }}" alt="{{ $content->title }}"
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-indigo-400 via-purple-500 to-pink-500 flex items-center justify-center">
                                        <span class="text-6xl">
                                            @php
                                                $icon = match($content->type) {
                                                    'article' => '📝',
                                                    'image'   => '🖼️',
                                                    'video'   => '🎬',
                                                    'audio'   => '🎵',
                                                    default   => '📄',
                                                };
                                            @endphp
                                            {{ $icon }}
                                        </span>
                                    </div>
                                @endif

                                {{-- نوع المحتوى --}}
                                <div class="absolute top-3 right-3 px-3 py-1 bg-black/70 backdrop-blur-sm text-white text-xs font-semibold rounded-full">
                                    @php
                                        $typeLabels = [
                                            'article' => '📝 مقال',
                                            'image'   => '🖼️ صورة',
                                            'video'   => '🎬 فيديو',
                                            'audio'   => '🎵 صوت',
                                        ];
                                    @endphp
                                    {{ $typeLabels[$content->type] ?? '📄' }}
                                </div>
                            </a>

                            {{-- المحتوى --}}
                            <div class="p-5">
                                {{-- التصنيفات --}}
                                @if($content->categories->count())
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach($content->categories->take(2) as $category)
                                            <span class="text-xs px-2 py-0.5 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 rounded-full">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- العنوان --}}
                                <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                    <a href="{{ route('articles.show', $content->slug) }}">{{ $content->title }}</a>
                                </h3>

                                {{-- مقتطف --}}
                                @if($content->body)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                                        {{ Str::limit(strip_tags($content->body), 100) }}
                                    </p>
                                @endif

                                {{-- الكاتب + التفاعل --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center gap-2 text-sm text-gray-500 dark:text-gray-400 min-w-0">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center text-indigo-800 dark:text-indigo-200 font-bold text-xs flex-shrink-0">
                                            {{ mb_substr($content->author->name, 0, 1) }}
                                        </div>
                                        <span class="truncate">{{ $content->author->name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-400 flex-shrink-0">
                                        <span>❤️ {{ $content->likes->count() }}</span>
                                        <span>💬 {{ $content->comments->count() }}</span>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- الترقيم --}}
                @if($contents->hasPages())
                    <div class="mt-8">
                        {{ $contents->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl shadow-xl">
                    <div class="text-8xl mb-4">📭</div>
                    <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('لا توجد مقالات بعد') }}
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        @if(request()->anyFilled(['q', 'type', 'category', 'sort']))
                            {{ __('لا توجد نتائج مطابقة لبحثك.') }}
                            <a href="{{ route('articles.index') }}" class="text-indigo-600 dark:text-indigo-400 hover:underline block mt-2">{{ __('مسح الفلاتر') }}</a>
                        @else
                            {{ __('عد قريباً لاكتشاف المزيد من المحتوى الفلكلوري!') }}
                        @endif
                    </p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>