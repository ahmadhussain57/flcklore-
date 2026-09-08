<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. تعريف صلاحيات قسم الفلكلور (المحتوى)
        $contentPermissions = [
            'create_article',
            'edit_article',
            'delete_article',
            'publish_article',
        ];

        // 2. تعريف صلاحيات قسم التسويق
        $marketingPermissions = [
            'view_reports',
            'manage_campaigns',
            'manage_budget',
        ];

        // 3. إنشاء الصلاحيات في قاعدة البيانات
        $allPermissions = array_merge($contentPermissions, $marketingPermissions);
        foreach ($allPermissions as $perm) {
            // نستخدم firstOrCreate لكي لا يحدث خطأ إذا شغلنا الملف مرتين
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ==========================================
        // 4. توزيع الصلاحيات على أدوار المحتوى
        // ==========================================
        $contentAdmin = Role::where('name', 'content_admin')->first();
        if ($contentAdmin) {
            $contentAdmin->syncPermissions($contentPermissions); // المدير يأخذ كل شيء
        }

        $contentReviewer = Role::where('name', 'content_Reviewer')->first();
        if ($contentReviewer) {
            $contentReviewer->syncPermissions(['edit_article', 'publish_article']); // المراجع يحرر وينشر فقط
        }

        $contentAuthor = Role::where('name', 'content_author')->first();
        if ($contentAuthor) {
            $contentAuthor->syncPermissions(['create_article', 'edit_article']); // الكاتب ينشئ ويحرر فقط
        }

        // ==========================================
        // 5. توزيع الصلاحيات على أدوار التسويق
        // ==========================================
        $marketingAdmin = Role::where('name', 'marketing_admin')->first();
        if ($marketingAdmin) {
            $marketingAdmin->syncPermissions($marketingPermissions); // المدير يأخذ كل شيء
        }

        $marketingAccountant = Role::where('name', 'marketing_Accountant')->first();
        if ($marketingAccountant) {
            $marketingAccountant->syncPermissions(['view_reports', 'manage_budget']); // المحاسب يرى التقارير ويدير الميزانية
        }
    }
}