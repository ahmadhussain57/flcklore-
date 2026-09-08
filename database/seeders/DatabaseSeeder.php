<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) إنشاء الأدوار ثم الصلاحيات بالترتيب
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        // 2) مستخدم تجريبي للاختبار
        $user = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => 'password', // سيُشفَّر تلقائيًا بسبب cast في User
            ]
        );

        // 3) إسناد دور زائر في القسمين معًا (إجباري)
        $user->assignSectionRole('content', 'content_Guest');
        $user->assignSectionRole('marketing', 'marketing_Guest');
    }
}