<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🔒 {{ __('سياسة الخصوصية') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 lg:p-12">

                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-gray-200 mb-2">{{ __('سياسة الخصوصية') }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('آخر تحديث:') }} {{ now()->translatedFormat('F Y') }}
                    </p>
                </div>

                <div class="prose dark:prose-invert max-w-none text-gray-700 dark:text-gray-300 space-y-6">

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">1. مقدمة</h2>
                        <p class="leading-relaxed">
                            {{ __('نحن في منصة الفلكلور نأخذ خصوصيتك على محمل الجد. توضح هذه السياسة كيفية جمعنا واستخدامنا وحمايتنا لبياناتك الشخصية عند استخدامك للمنصة.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">2. البيانات التي نجمعها</h2>
                        <ul class="list-disc list-inside space-y-2 leading-relaxed">
                            <li>{{ __('معلومات التسجيل: الاسم، البريد الإلكتروني، كلمة المرور (مشفرة).') }}</li>
                            <li>{{ __('المحتوى الذي تنشره: المقالات، التعليقات، الصور، الفيديوهات.') }}</li>
                            <li>{{ __('بيانات الاستخدام: الصفحات التي تزورها، وقت الزيارة.') }}</li>
                            <li>{{ __('بيانات تقنية: عنوان IP، نوع المتصفح، نظام التشغيل.') }}</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">3. كيف نستخدم بياناتك</h2>
                        <ul class="list-disc list-inside space-y-2 leading-relaxed">
                            <li>{{ __('لتشغيل حسابك وتقديم خدمات المنصة.') }}</li>
                            <li>{{ __('لتحسين تجربة المستخدم وتطوير المنصة.') }}</li>
                            <li>{{ __('لإرسال إشعارات مهمة (موافقة/رفض محتوى، تعليقات جديدة).') }}</li>
                            <li>{{ __('لحماية المنصة من الاستخدام المسيء أو الاحتيال.') }}</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">4. حماية بياناتك</h2>
                        <p class="leading-relaxed">
                            {{ __('نستخدم تقنيات تشفير حديثة (SSL/HTTPS) لحماية بياناتك أثناء النقل، كما نُخزّن كلمات المرور باستخدام خوارزميات تشفير قوية (bcrypt). لا نشارك بياناتك مع أي طرف ثالث دون موافقتك.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">5. ملفات تعريف الارتباط (Cookies)</h2>
                        <p class="leading-relaxed">
                            {{ __('نستخدم ملفات تعريف الارتباط للحفاظ على جلستك، تذكر تفضيلاتك (مثل الوضع الداكن واللغة)، وتحسين أداء الموقع. يمكنك تعطيلها من إعدادات المتصفح، لكن بعض الميزات قد لا تعمل بشكل صحيح.') }}
                        </p>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">6. حقوقك</h2>
                        <ul class="list-disc list-inside space-y-2 leading-relaxed">
                            <li>{{ __('الحق في الوصول إلى بياناتك الشخصية.') }}</li>
                            <li>{{ __('الحق في تصحيح أو حذف بياناتك.') }}</li>
                            <li>{{ __('الحق في سحب موافقتك في أي وقت.') }}</li>
                            <li>{{ __('الحق في الاعتراض على معالجة بياناتك.') }}</li>
                        </ul>
                    </section>

                    <section>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200 mb-3">7. التواصل معنا</h2>
                        <p class="leading-relaxed">
                            {{ __('إذا كان لديك أي سؤال حول سياسة الخصوصية، يمكنك التواصل معنا عبر:') }}
                            <a href="mailto:privacy@folklore.com" class="text-indigo-600 dark:text-indigo-400 hover:underline">privacy@folklore.com</a>
                        </p>
                    </section>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>