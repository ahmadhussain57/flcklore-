<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // ======================
        // 1. أدوار قسم المحتوى
        // ======================
        Role::firstOrCreate(['name' => 'content_admin', 'guard_name' => 'web'], ['section' => 'content']);
        Role::firstOrCreate(['name' => 'content_Reviewer', 'guard_name' => 'web'], ['section' => 'content']);
        Role::firstOrCreate(['name' => 'content_author', 'guard_name' => 'web'], ['section' => 'content']);
        Role::firstOrCreate(['name' => 'content_Guest', 'guard_name' => 'web'], ['section' => 'content']);

        // ======================
        // 2. أدوار قسم التسويق
        // ======================
        Role::firstOrCreate(['name' => 'marketing_admin', 'guard_name' => 'web'], ['section' => 'marketing']);
        Role::firstOrCreate(['name' => 'marketing_Specialist', 'guard_name' => 'web'], ['section' => 'marketing']);
        Role::firstOrCreate(['name' => 'marketing_Accountant', 'guard_name' => 'web'], ['section' => 'marketing']);
        Role::firstOrCreate(['name' => 'marketing_Guest', 'guard_name' => 'web'], ['section' => 'marketing']);

        $this->command->info('✅ تم إنشاء جميع الأدوار بنجاح!');
    }
}