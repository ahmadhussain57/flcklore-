<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('لوحة التحكم') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @php($user = Auth::user())

            {{-- قسم المحتوى --}}
            @if ($user->hasAnyRole(['content_admin', 'content_Reviewer', 'content_author']))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">قسم المحتوى الرقمي</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            دورك الحالي:
                            <strong>{{ $user->content_role?->name }}</strong>
                        </p>

                        @role('content_author')
                            <p>من هنا لاحقًا: إنشاء محتوى وإرساله إلى طابور المراجعة.</p>
                        @endrole

                        @role('content_Reviewer')
                            <p>من هنا لاحقًا: سحب أول عنصر من طابور المراجعة ومعالجته.</p>
                        @endrole

                        @role('content_admin')
                            <p>من هنا لاحقًا: مراقبة موافقات المدققين وإدارة طلبات الترقية.</p>
                            <a href="{{ route('admin.role-upgrade.index') }}" class="text-indigo-600 hover:underline">
    مراجعة طلبات الترقية
</a>
                        @endrole
                    </div>
                </div>
            @endif

            {{-- قسم التسويق --}}
            @if ($user->hasAnyRole(['marketing_admin', 'marketing_Accountant', 'marketing_Specialist']))
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h3 class="text-lg font-semibold mb-2">قسم تسويق المنتجات</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                            دورك الحالي:
                            <strong>{{ $user->marketing_role?->name }}</strong>
                        </p>

                        @role('marketing_Specialist')
                            <p>من هنا لاحقًا: نشر المنتجات وطلبات التعديل.</p>
                        @endrole

                        @role('marketing_Accountant')
                            <p>من هنا لاحقًا: طابور التقارير المحاسبية والفواتير.</p>
                        @endrole

                        @role('marketing_admin')
                            <p>من هنا لاحقًا: اعتماد المنتجات وطلبات الترقية والحذف.</p>
                            <a href="{{ route('admin.role-upgrade.index') }}" class="text-indigo-600 hover:underline">
    مراجعة طلبات الترقية
</a>
                        @endrole
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>