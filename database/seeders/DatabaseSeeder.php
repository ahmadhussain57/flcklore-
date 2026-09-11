<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) تشغيل السيدرات بالترتيب
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            CategorySeeder::class,
        ]);

        // ==========================================
        // 2) إنشاء 8 مستخدمين، كل واحد بدور رئيسي
        //    والدور في القسم الثاني: ضيف
        // ==========================================

        $users = [
            // ======================
            // قسم المحتوى
            // ======================
            [
                'email' => 'content-admin@example.com',
                'name' => 'مدير المحتوى',
                'content_role' => 'content_admin',
                'marketing_role' => 'marketing_Guest',
            ],
            [
                'email' => 'content-reviewer@example.com',
                'name' => 'مدقق المحتوى',
                'content_role' => 'content_Reviewer',
                'marketing_role' => 'marketing_Guest',
            ],
            [
                'email' => 'content-author@example.com',
                'name' => 'مؤلف المحتوى',
                'content_role' => 'content_author',
                'marketing_role' => 'marketing_Guest',
            ],
            [
                'email' => 'content-guest@example.com',
                'name' => 'ضيف المحتوى',
                'content_role' => 'content_Guest',
                'marketing_role' => 'marketing_Guest',
            ],

            // ======================
            // قسم التسويق
            // ======================
            [
                'email' => 'marketing-admin@example.com',
                'name' => 'مدير التسويق',
                'content_role' => 'content_Guest',
                'marketing_role' => 'marketing_admin',
            ],
            [
                'email' => 'marketing-accountant@example.com',
                'name' => 'محاسب التسويق',
                'content_role' => 'content_Guest',
                'marketing_role' => 'marketing_Accountant',
            ],
            [
                'email' => 'marketing-specialist@example.com',
                'name' => 'متخصص التسويق',
                'content_role' => 'content_Guest',
                'marketing_role' => 'marketing_Specialist',
            ],
            [
                'email' => 'marketing-guest@example.com',
                'name' => 'ضيف التسويق',
                'content_role' => 'content_Guest',
                'marketing_role' => 'marketing_Guest',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                ]
            );

            // إسناد الدور في قسم المحتوى
            $user->assignSectionRole('content', $data['content_role']);

            // إسناد الدور في قسم التسويق
            $user->assignSectionRole('marketing', $data['marketing_role']);

            $this->command->info("✅ تم إنشاء المستخدم: {$data['email']} ({$data['name']})");
        }

        $this->command->info('🎉 تم إنشاء جميع المستخدمين بنجاح!');
    }
}