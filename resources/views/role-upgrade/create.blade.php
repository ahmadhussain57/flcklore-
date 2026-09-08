<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            طلب ترقية الدور
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 rounded">{{ session('error') }}</div>
            @endif
            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">
                    دورك الحالي — المحتوى:
                    <strong>{{ $contentRole ?? '—' }}</strong>
                    |
                    التسويق:
                    <strong>{{ $marketingRole ?? '—' }}</strong>
                </p>

                <form method="POST" action="{{ route('role-upgrade.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <x-input-label for="section" value="القسم" />
                        <select id="section" name="section" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">اختر القسم</option>
                            <option value="content" @selected(old('section') === 'content')>المحتوى الرقمي</option>
                            <option value="marketing" @selected(old('section') === 'marketing')>تسويق المنتجات</option>
                        </select>
                        <x-input-error :messages="$errors->get('section')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="requested_role" value="الدور المطلوب" />
                        <select id="requested_role" name="requested_role" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                            <option value="">اختر الدور</option>
                            <optgroup label="المحتوى">
                                @foreach ($contentRoles as $role)
                                    <option value="{{ $role }}" @selected(old('requested_role') === $role)>{{ $role }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="التسويق">
                                @foreach ($marketingRoles as $role)
                                    <option value="{{ $role }}" @selected(old('requested_role') === $role)>{{ $role }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                        <x-input-error :messages="$errors->get('requested_role')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="message" value="سبب الطلب (اختياري)" />
                        <textarea id="message" name="message" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('message') }}</textarea>
                        <x-input-error :messages="$errors->get('message')" class="mt-2" />
                    </div>

                    <div class="flex justify-end">
                        <x-primary-button>إرسال الطلب</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>