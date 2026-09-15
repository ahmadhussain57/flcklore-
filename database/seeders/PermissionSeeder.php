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
        // 1. صلاحيات طلب الترقية
        // ==========================================
        $upgradePermissions = [
            'approve_role_upgrade',
        ];

        // ==========================================
        // 2. صلاحيات المحتوى
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
        // 3. صلاحيات التفاعل
        // ==========================================
        $interactionPermissions = [
            'comment_on_content',
            'like_content',
            'delete_own_comment',
        ];

        // ==========================================
        // 4. ✅ صلاحيات مراجعة التعليقات (جديدة)
        // ==========================================
        $commentReviewPermissions = [
            'review_comment',
            'approve_comment',
            'reject_comment',
        ];

        // ==========================================
        // 5. صلاحيات المنتجات
        // ==========================================
        $productPermissions = [
            'create_product',
            'edit_own_product',
            'edit_any_product',
            'delete_own_product',
            'delete_any_product',
            'submit_product_for_review',
            'review_product',
            'approve_product',
            'reject_product',
            'publish_product',
            'manage_product_categories',
            'manage_inventory',
        ];

        // ==========================================
        // 6. صلاحيات المحاسبة
        // ==========================================
        $accountingPermissions = [
            'view_accounting',
            'manage_journal_entries',
            'manage_invoices',
            'manage_accounts',
        ];

        // ==========================================
        // 7. صلاحيات التسويق العامة
        // ==========================================
        $marketingPermissions = [
            'view_reports',
            'manage_campaigns',
            'manage_budget',
        ];

        // دمج كل الصلاحيات
        $allPermissions = array_merge(
            $upgradePermissions,
            $contentPermissions,
            $interactionPermissions,
            $commentReviewPermissions,  // ✅ جديد
            $productPermissions,
            $accountingPermissions,
            $marketingPermissions
        );

        // إنشاء الصلاحيات
        foreach ($allPermissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // ==========================================
        // 8. توزيع الصلاحيات على أدوار المحتوى
        // ==========================================

        // مدير المحتوى
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
                'review_comment', 'approve_comment', 'reject_comment',  // ✅ جديد
            ]);
        }

        // مدقق المحتوى
        $contentReviewer = Role::where('name', 'content_Reviewer')->first();
        if ($contentReviewer) {
            $contentReviewer->syncPermissions([
                'review_content', 'approve_content', 'reject_content', 'publish_content',
                'manage_categories', 'manage_tags',
                'edit_any_content', 'delete_any_content',
                'comment_on_content', 'like_content', 'delete_own_comment',
                'review_comment', 'approve_comment', 'reject_comment',  // ✅ جديد
            ]);
        }

        // مؤلف المحتوى
        $contentAuthor = Role::where('name', 'content_author')->first();
        if ($contentAuthor) {
            $contentAuthor->syncPermissions([
                'create_content', 'edit_own_content', 'delete_own_content',
                'submit_for_review',
                'comment_on_content', 'like_content', 'delete_own_comment',
            ]);
        }

        // ضيف المحتوى
        $contentGuest = Role::where('name', 'content_Guest')->first();
        if ($contentGuest) {
            $contentGuest->syncPermissions([
                'comment_on_content', 'like_content',
            ]);
        }

        // ==========================================
        // 9. توزيع الصلاحيات على أدوار التسويق
        // ==========================================

        // مدير التسويق (كل شيء + المحاسبة + مراجعة تعليقات المنتجات)
        $marketingAdmin = Role::where('name', 'marketing_admin')->first();
        if ($marketingAdmin) {
            $marketingAdmin->syncPermissions([
                'create_product', 'edit_own_product', 'edit_any_product',
                'delete_own_product', 'delete_any_product',
                'submit_product_for_review',
                'review_product', 'approve_product', 'reject_product', 'publish_product',
                'manage_product_categories', 'manage_inventory',
                'view_accounting', 'manage_journal_entries', 'manage_invoices', 'manage_accounts',
                'view_reports', 'manage_campaigns', 'manage_budget',
                'review_comment', 'approve_comment', 'reject_comment',  // ✅ جديد
            ]);
        }

        // متخصص التسويق
        $marketingSpecialist = Role::where('name', 'marketing_Specialist')->first();
        if ($marketingSpecialist) {
            $marketingSpecialist->syncPermissions([
                'create_product', 'edit_own_product', 'delete_own_product',
                'submit_product_for_review',
                'manage_inventory',
            ]);
        }

        // محاسب التسويق (المحاسبة فقط)
        $marketingAccountant = Role::where('name', 'marketing_Accountant')->first();
        if ($marketingAccountant) {
            $marketingAccountant->syncPermissions([
                'view_accounting', 'manage_journal_entries', 'manage_invoices',
                'view_reports', 'manage_budget',
            ]);
        }

        $this->command->info('✅ تم إنشاء وتوزيع جميع الصلاحيات بنجاح!');
    }
}