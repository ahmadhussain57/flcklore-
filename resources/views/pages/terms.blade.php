<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            📜 {{ __('الشروط والأحكام') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 lg:p-12">

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{ __('الشروط والأحكام') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('آخر تحديث:') }} {{ now()->translatedFormat('F Y') }}
                    </p>
                </div>

                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 space-y-6">

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">1. القبول بالشروط</h2>
                        <p class="leading-relaxed">
                            {{ __('باستخدامك لمنصة الفلكلور، فإنك توافق على الالتزام بهذه الشروط والأحكام. إذا كنت لا توافق، يرجى عدم استخدام المنصة.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">2. استخدام المنصة</h2>
                        <ul class="list-disc list-inside space-y-2 leading-relaxed">
                            <li>{{ __('يجب أن يكون عمرك 13 عاماً على الأقل لاستخدام المنصة.') }}</li>
                            <li>{{ __('أنت مسؤول عن الحفاظ على سرية حسابك وكلمة المرور.') }}</li>
                            <li>{{ __('يُمنع استخدام المنصة لأي أغراض غير قانونية أو ضارة.') }}</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">3. المحتوى المنشور</h2>
                        <ul class="list-disc list-inside space-y-2 leading-relaxed">
                            <li>{{ __('تحتفظ بحقوق ملكية المحتوى الذي تنشره.') }}</li>
                            <li>{{ __('تمنحنا ترخيصاً غير حصري لعرض وتوزيع المحتوى على المنصة.') }}</li>
                            <li>{{ __('يُمنع نشر محتوى مسيء، عنصري، أو ينتهك حقوق الآخرين.') }}</li>
                            <li>{{ __('نحتفظ بالحق في حذف أي محتوى يخالف الشروط دون إشعار.') }}</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">4. الملكية الفكرية</h2>
                        <p class="leading-relaxed">
                            {{ __('جميع حقوق المنصة (التصميم، الكود، الشعارات) محفوظة. لا يجوز نسخ أو إعادة استخدام أي جزء من المنصة دون إذن كتابي مسبق.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">5. المنتجات والمشتريات</h2>
                        <ul class="list-disc list-inside space-y-2 leading-relaxed">
                            <li>{{ __('الأسعار المعروضة قابلة للتغيير دون إشعار مسبق.') }}</li>
                            <li>{{ __('المنتجات المادية قابلة للاسترجاع خلال 7 أيام (حسب سياسة البائع).') }}</li>
                            <li>{{ __('المنتجات الرقمية غير قابلة للاسترجاع بعد التحميل.') }}</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">6. إخلاء المسؤولية</h2>
                        <p class="leading-relaxed">
                            {{ __('نقدم المنصة "كما هي" دون ضمانات. لا نتحمل مسؤولية أي أضرار ناتجة عن استخدام المنصة أو تعطّل الخدمة.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">7. تعديل الشروط</h2>
                        <p class="leading-relaxed">
                            {{ __('نحتفظ بالحق في تعديل هذه الشروط في أي وقت. سيتم إشعارك بالتغييرات الجوهرية عبر البريد الإلكتروني أو إشعار داخل المنصة.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">8. القانون المطبق</h2>
                        <p class="leading-relaxed">
                            {{ __('تخضع هذه الشروط لأحكام القوانين المعمول بها في بلد تسجيل المنصة.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">9. التواصل</h2>
                        <p class="leading-relaxed">
                            {{ __('لأي استفسار حول هذه الشروط:') }}
                            <a href="mailto:legal@folklore.com" class="text-indigo-600 dark:text-indigo-400 hover:underline">legal@folklore.com</a>
                        </p>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>