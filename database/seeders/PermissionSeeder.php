<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. صلاحيات طلبات الترقية
        // ==========================================
        $upgradePermissions = [
            'approve_role_upgrade',
        ];

        // ==========================================
        // 2. صلاحيات نظام المحتوى
        // ==========================================
        $contentPermissions = [
            'create_content',
            'edit_own_content',
            'edit_any_content',
            'delete_own_content',
            'delete_any_content',
            'submit_for_review',
            'review_content',
            'approve_content',
            'reject_content',
            'publish_content',
            'manage_categories',
            'manage_tags',
        ];

        // ==========================================
        // 3. صلاحيات التفاعل (تعليقات وإعجابات)
        // ==========================================
        $interactionPermissions = [
            'comment_on_content',
            'like_content',
            'delete_own_comment',
        ];

        // ==========================================
        // 4. صلاحيات المنتجات التسويقية (جديدة)
        // ==========================================
        $productPermissions = [
            // CRUD
            'create_product',
            'edit_own_product',
            'edit_any_product',
            'delete_own_product',
            'delete_any_product',
            'submit_product_for_review',

            // المراجعة
            'review_product',
            'approve_product',
            'reject_product',
            'publish_product',

            // الإدارة
            'manage_product_categories',
            'manage_inventory',
        ];

        // ==========================================
        // 5. صلاحيات التسويق (محاسبة/تقارير - لاحقاً)
        // ==========================================
        $marketingPermissions = [
            'view_reports',
            'manage_campaigns',
            'manage_budget',
        ];

        // دمج جميع الصلاحيات
        $allPermissions = array_merge(
            $upgradePermissions,
            $contentPermissions,
            $interactionPermissions,
            $productPermissions,
            $marketingPermissions
        );

        // إنشاء كل صلاحية إذا لم تكن موجودة
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ==========================================
        // 6. توزيع الصلاحيات على أدوار المحتوى
        // ==========================================

        // 6.1 مدير المحتوى (كل صلاحيات المحتوى والتفاعل)
        $contentAdmin = Role::where('name', 'content_admin')->first();
        if ($contentAdmin) {
            $contentAdmin->syncPermissions([
                'approve_role_upgrade',
                'create_content', 'edit_own_content', 'edit_any_content',
                'delete_own_content', 'delete_any_content',
                'submit_for_review',
                'review_content', 'approve_content', 'reject_content', 'publish_content',
                'manage_categories', 'manage_tags',
                'comment_on_content', 'like_content', 'delete_own_comment',
            ]);
        }

        // 6.2 مدقق المحتوى
        $contentReviewer = Role::where('name', 'content_Reviewer')->first();
        if ($contentReviewer) {
            $contentReviewer->syncPermissions([
                'review_content', 'approve_content', 'reject_content', 'publish_content',
                'manage_categories', 'manage_tags',
                'edit_any_content', 'delete_any_content',
                'comment_on_content', 'like_content', 'delete_own_comment',
            ]);
        }

        // 6.3 مؤلف المحتوى
        $contentAuthor = Role::where('name', 'content_author')->first();
        if ($contentAuthor) {
            $contentAuthor->syncPermissions([
                'create_content', 'edit_own_content', 'delete_own_content',
                'submit_for_review',
                'comment_on_content', 'like_content', 'delete_own_comment',
            ]);
        }

        // 6.4 ضيف المحتوى
        $contentGuest = Role::where('name', 'content_Guest')->first();
        if ($contentGuest) {
            $contentGuest->syncPermissions([
                'comment_on_content', 'like_content',
            ]);
        }

        // ==========================================
        // 7. توزيع الصلاحيات على أدوار التسويق
        // ==========================================

        // 7.1 مدير التسويق (كل صلاحيات المنتجات + التسويق)
        $marketingAdmin = Role::where('name', 'marketing_admin')->first();
        if ($marketingAdmin) {
            $marketingAdmin->syncPermissions([
                'create_product', 'edit_own_product', 'edit_any_product',
                'delete_own_product', 'delete_any_product',
                'submit_product_for_review',
                'review_product', 'approve_product', 'reject_product', 'publish_product',
                'manage_product_categories', 'manage_inventory',
                'view_reports', 'manage_campaigns', 'manage_budget',
            ]);
        }

        // 7.2 متخصص التسويق (إنشاء المنتجات وتقديمها)
        $marketingSpecialist = Role::where('name', 'marketing_Specialist')->first();
        if ($marketingSpecialist) {
            $marketingSpecialist->syncPermissions([
                'create_product', 'edit_own_product', 'delete_own_product',
                'submit_product_for_review',
                'manage_inventory',
            ]);
        }

        // 7.3 محاسب التسويق (يرى التقارير فقط حالياً)
        $marketingAccountant = Role::where('name', 'marketing_Accountant')->first();
        if ($marketingAccountant) {
            $marketingAccountant->syncPermissions([
                'view_reports', 'manage_budget',
            ]);
        }

        // 7.4 ضيف التسويق (لا صلاحيات إدارية)
        // لا شيء

        $this->command->info('✅ تم إنشاء وتوزيع جميع الصلاحيات بنجاح!');
    }
}