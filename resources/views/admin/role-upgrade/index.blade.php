<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            طلبات الترقية المعلّقة
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                @forelse ($requests as $req)
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $req->user->name }} ({{ $req->user->email }})
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                                    القسم: <strong>{{ $req->section }}</strong>
                                    — الدور المطلوب: <strong>{{ $req->requested_role }}</strong>
                                </p>
                                @if ($req->message)
                                    <p class="text-sm text-gray-500 mt-2">{{ $req->message }}</p>
                                @endif
                                <p class="text-xs text-gray-400 mt-2">{{ $req->created_at->diffForHumans() }}</p>
                            </div>

                            <div class="flex flex-col gap-2 min-w-[200px]">
                                <form method="POST" action="{{ route('admin.role-upgrade.approve', $req) }}">
                                    @csrf
                                    <x-primary-button class="w-full justify-center">موافقة</x-primary-button>
                                </form>

                                <form method="POST" action="{{ route('admin.role-upgrade.reject', $req) }}" class="space-y-2">
                                    @csrf
                                    <input type="text" name="admin_note" placeholder="سبب الرفض (اختياري)"
                                           class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                    <x-secondary-button class="w-full justify-center" type="submit">رفض</x-secondary-button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-6 text-gray-500">لا توجد طلبات معلّقة.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>