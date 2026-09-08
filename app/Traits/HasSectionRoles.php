<?php

namespace App\Traits;

/**
 * @method \Illuminate\Database\Eloquent\Relations\MorphToMany roles()
 * @method $this removeRole($role)
 * @method $this assignRole($roles)
 */

use Illuminate\Support\Facades\DB;
use App\Models\Role;
use InvalidArgumentException;

trait HasSectionRoles
{
    /**
     * تعيين دور للمستخدم في قسم معين مع حماية صارمة
     */
    public function assignSectionRole(string $section, string $roleName): void
    {
        // 1. الحماية المسبقة: التأكد من أن الدور موجود وينتمي للقسم المطلوب فعلياً
        $targetRole = Role::where('name', $roleName)->where('section', $section)->first();

        if (!$targetRole) {
            throw new InvalidArgumentException("خطأ أمني: الدور '{$roleName}' غير موجود أو لا يتبع لقسم '{$section}'");
        }

        DB::transaction(function () use ($section, $targetRole) {
            // 2. جلب الأدوار الحالية للمستخدم في هذا القسم فقط
            // نستخدم $this->roles() القادمة من مكتبة Spatie
            $rolesInSection = $this->roles()->where('section', $section)->get();

            // 3. سحب هذه الأدوار من المستخدم
            foreach ($rolesInSection as $role) {
                $this->removeRole($role);
            }

            // 4. تعيين الدور الجديد بأمان تام
            $this->assignRole($targetRole);
        });
    }

         public function hasBothSectionRoles(): bool
        {
    $sections = $this->roles()->pluck('section')->unique();

    return $sections->contains('content')
        && $sections->contains('marketing');
        }

    /**
     * جلب دور المستخدم في قسم الفلكلور (المحتوى)
     */
    public function getContentRoleAttribute()
    {
        return $this->roles()->where('section', 'content')->first();
    }

    /**
     * جلب دور المستخدم في قسم التسويق
     */
    public function getMarketingRoleAttribute()
    {
        return $this->roles()->where('section', 'marketing')->first();
    }

    /**
 * هل المستخدم عضو فريق (ليس زائرًا فقط)؟
 */
public function isStaff(): bool
{
    return $this->hasAnyRole([
        'content_admin',
        'content_Reviewer',
        'content_author',
        'marketing_admin',
        'marketing_Accountant',
        'marketing_Specialist',
    ]);
}

/**
 * هل المستخدم زائر فقط في النظام؟
 */
public function isGuestOnly(): bool
{
    return $this->hasBothSectionRoles() && ! $this->isStaff();
}
}