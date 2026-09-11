<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                📊 {{ __('لوحة التحكم') }}
            </h2>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ now()->translatedFormat('l، j F Y') }}
            </span>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- ✅ بطاقة الترحيب --}}
            <div class="relative bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-2xl shadow-xl overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute top-4 right-4 text-7xl">🏺</div>
                    <div class="absolute bottom-4 left-4 text-7xl">📜</div>
                </div>
                <div class="relative p-6 md:p-8 flex flex-col md:flex-row items-start md:items-center gap-4">
                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur flex items-center justify-center text-white text-2xl font-bold flex-shrink-0">
                        {{ mb_substr($user->name, 0, 2) }}
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-1">
                            {{ __('أهلاً بك، :name!', ['name' => $user->name]) }}
                        </h1>
                        <p class="text-white/90 mb-3">
                            {{ __('هذه لوحتك الخاصة لمتابعة نشاطك على المنصة.') }}
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->getRoleNames() as $role)
                                <span class="px-3 py-1 bg-white/20 backdrop-blur text-white rounded-full text-xs font-semibold">
                                    {{ $role }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ إجراءات سريعة --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200 mb-4">⚡ {{ __('إجراءات سريعة') }}</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">

                    @if($isContentAuthor || $user->hasRole('content_admin'))
                        <a href="{{ route('content.create') }}"
                           class="p-4 rounded-xl border-2 border-dashed border-indigo-300 dark:border-indigo-700 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition text-center group">
                            <div class="text-3xl mb-2 group-hover:scale-110 transition">📝</div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('محتوى جديد') }}</div>
                        </a>
                    @endif

                    @if($isMarketingStaff)
                        <a href="{{ route('products.create') }}"
                           class="p-4 rounded-xl border-2 border-dashed border-purple-300 dark:border-purple-700 hover:bg-purple-50 dark:hover:bg-purple-900/20 transition text-center group">
                            <div class="text-3xl mb-2 group-hover:scale-110 transition">📦</div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('منتج جديد') }}</div>
                        </a>
                    @endif

                    @if($isContentReviewer)
                        <a href="{{ route('review.index') }}"
                           class="p-4 rounded-xl border-2 border-dashed border-green-300 dark:border-green-700 hover:bg-green-50 dark:hover:bg-green-900/20 transition text-center group relative">
                            <div class="text-3xl mb-2 group-hover:scale-110 transition">✅</div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('مراجعة المحتوى') }}</div>
                            @if($pendingContentReviews > 0)
                                <span class="absolute top-2 right-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                                    {{ $pendingContentReviews }}
                                </span>
                            @endif
                        </a>
                    @endif

                    @if($isMarketingAdmin)
                        <a href="{{ route('products.review.index') }}"
                           class="p-4 rounded-xl border-2 border-dashed border-amber-300 dark:border-amber-700 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition text-center group relative">
                            <div class="text-3xl mb-2 group-hover:scale-110 transition">🔍</div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('مراجعة المنتجات') }}</div>
                            @if($pendingProductReviews > 0)
                                <span class="absolute top-2 right-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                                    {{ $pendingProductReviews }}
                                </span>
                            @endif
                        </a>
                    @endif

                    @if($isAdmin)
                        <a href="{{ route('admin.role-upgrade.index') }}"
                           class="p-4 rounded-xl border-2 border-dashed border-pink-300 dark:border-pink-700 hover:bg-pink-50 dark:hover:bg-pink-900/20 transition text-center group relative">
                            <div class="text-3xl mb-2 group-hover:scale-110 transition">⬆️</div>
                            <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('طلبات الترقية') }}</div>
                            @if($pendingUpgrades > 0)
                                <span class="absolute top-2 right-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                                    {{ $pendingUpgrades }}
                                </span>
                            @endif
                        </a>
                    @endif

                    <a href="{{ route('shop.index') }}"
                       class="p-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition text-center group">
                        <div class="text-3xl mb-2 group-hover:scale-110 transition">🛍️</div>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('تصفح المتجر') }}</div>
                    </a>

                    <a href="{{ route('notifications.index') }}"
                       class="p-4 rounded-xl border-2 border-dashed border-blue-300 dark:border-blue-700 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition text-center group relative">
                        <div class="text-3xl mb-2 group-hover:scale-110 transition">🔔</div>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('الإشعارات') }}</div>
                        @if($recentNotifications->where('read_at', null)->count() > 0)
                            <span class="absolute top-2 right-2 px-2 py-0.5 bg-red-500 text-white text-xs font-bold rounded-full">
                                {{ $recentNotifications->where('read_at', null)->count() }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('profile.edit') }}"
                       class="p-4 rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition text-center group">
                        <div class="text-3xl mb-2 group-hover:scale-110 transition">👤</div>
                        <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('الملف الشخصي') }}</div>
                    </a>
                </div>
            </div>

            {{-- ✅ إحصائيات المحتوى --}}
            @if($contentStats)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                            📝 {{ $isContentReviewer ? __('إحصائيات المحتوى (كل المنصة)') : __('إحصائيات محتواي') }}
                        </h2>
                        <a href="{{ route('content.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ __('عرض الكل') }} →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $contentStats['total'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('المجموع') }}</div>
                        </div>
                        <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $contentStats['published'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('منشور') }}</div>
                        </div>
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $contentStats['pending'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('قيد المراجعة') }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $contentStats['draft'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مسودة') }}</div>
                        </div>
                        <div class="p-4 bg-red-50 dark:bg-red-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $contentStats['rejected'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مرفوض') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✅ إحصائيات المنتجات --}}
            @if($productStats)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                            📦 {{ $isMarketingAdmin ? __('إحصائيات المنتجات (كل المنصة)') : __('إحصائيات منتجاتي') }}
                        </h2>
                        <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                            {{ __('عرض الكل') }} →
                        </a>
                    </div>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-gray-800 dark:text-gray-200">{{ $productStats['total'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('المجموع') }}</div>
                        </div>
                        <div class="p-4 bg-green-50 dark:bg-green-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $productStats['published'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('منشور') }}</div>
                        </div>
                        <div class="p-4 bg-yellow-50 dark:bg-yellow-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $productStats['pending'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('قيد المراجعة') }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $productStats['draft'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مسودة') }}</div>
                        </div>
                        <div class="p-4 bg-red-50 dark:bg-red-900/30 rounded-xl text-center">
                            <div class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $productStats['rejected'] }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('مرفوض') }}</div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ✅ آخر الأنشطة (شبكة عمودين) --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- آخر المحتويات --}}
                @if($recentContents->count())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                🕐 {{ __('آخر محتوياتي') }}
                            </h2>
                            <a href="{{ route('content.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ __('عرض الكل') }}
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($recentContents as $content)
                                <a href="{{ route('content.show', $content) }}"
                                   class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition group">
                                    <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center flex-shrink-0">
                                        @php
                                            $icon = match($content->type) {
                                                'article' => '📝',
                                                'image'   => '🖼️',
                                                'video'   => '🎬',
                                                'audio'   => '🎵',
                                                default   => '📄',
                                            };
                                        @endphp
                                        <span>{{ $icon }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-800 dark:text-gray-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                            {{ $content->title }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            @php
                                                $statusMap = [
                                                    'draft'          => ['label' => 'مسودة', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                                                    'pending_review' => ['label' => 'قيد المراجعة', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                                    'pending_edit'   => ['label' => 'تعديل معلق', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                                    'published'      => ['label' => 'منشور', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                                                    'rejected'       => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                                                ];
                                                $s = $statusMap[$content->status] ?? $statusMap['draft'];
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
                                            <span class="text-xs text-gray-400">{{ $content->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- آخر المنتجات --}}
                @if($recentProducts->count())
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-bold text-gray-800 dark:text-gray-200">
                                🕐 {{ __('آخر منتجاتي') }}
                            </h2>
                            <a href="{{ route('products.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">
                                {{ __('عرض الكل') }}
                            </a>
                        </div>
                        <div class="space-y-3">
                            @foreach($recentProducts as $product)
                                <a href="{{ route('products.show', $product) }}"
                                   class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700/30 transition group">
                                    @if($product->cover_image)
                                        <img src="{{ $product->cover_image }}" alt="{{ $product->title }}"
                                             class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/50 flex items-center justify-center flex-shrink-0">
                                            <span>{{ $product->isPhysical() ? '🏺' : '💾' }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <p class="font-semibold text-sm text-gray-800 dark:text-gray-200 truncate group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">
                                            {{ $product->title }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            @php
                                                $statusMap = [
                                                    'draft'          => ['label' => 'مسودة', 'class' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'],
                                                    'pending_review' => ['label' => 'قيد المراجعة', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                                    'pending_edit'   => ['label' => 'تعديل معلق', 'class' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300'],
                                                    'published'      => ['label' => 'منشور', 'class' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300'],
                                                    'rejected'       => ['label' => 'مرفوض', 'class' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300'],
                                                ];
                                                $s = $statusMap[$product->status] ?? $statusMap['draft'];
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs rounded-full {{ $s['class'] }}">{{ $s['label'] }}</span>
                                            <span class="text-xs text-gray-400">{{ $product->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- ✅ التنبيهات المعلقة (إن وُجدت) --}}
            @if($pendingContentReviews > 0 || $pendingProductReviews > 0 || $pendingUpgrades > 0)
                <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700 rounded-2xl p-6">
                    <h2 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-3 flex items-center gap-2">
                        <span class="text-2xl">⚠️</span>
                        {{ __('بحاجة إلى انتباهك') }}
                    </h2>
                    <ul class="space-y-2 text-amber-800 dark:text-amber-200">
                        @if($pendingContentReviews > 0)
                            <li class="flex items-center justify-between">
                                <span>📝 {{ __('محتوى في انتظار مراجعتك:') }} <strong>{{ $pendingContentReviews }}</strong></span>
                                <a href="{{ route('review.index') }}" class="text-sm hover:underline font-semibold">{{ __('اذهب للمراجعة') }} →</a>
                            </li>
                        @endif
                        @if($pendingProductReviews > 0)
                            <li class="flex items-center justify-between">
                                <span>📦 {{ __('منتج في انتظار مراجعتك:') }} <strong>{{ $pendingProductReviews }}</strong></span>
                                <a href="{{ route('products.review.index') }}" class="text-sm hover:underline font-semibold">{{ __('اذهب للمراجعة') }} →</a>
                            </li>
                        @endif
                        @if($pendingUpgrades > 0)
                            <li class="flex items-center justify-between">
                                <span>⬆️ {{ __('طلب ترقية في انتظار قرارك:') }} <strong>{{ $pendingUpgrades }}</strong></span>
                                <a href="{{ route('admin.role-upgrade.index') }}" class="text-sm hover:underline font-semibold">{{ __('اذهب للطلبات') }} →</a>
                            </li>
                        @endif
                    </ul>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>