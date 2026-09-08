<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $contentRoles = [
            'content_admin',
            'content_Reviewer',
            'content_author',
            'content_Guest',
        ];

        foreach ($contentRoles as $roleName) {
            Role::firstOrCreate(
                [
                    'name' => $roleName,
                    'guard_name' => 'web',
                ],
                [
                    'section' => 'content',
                ]
            );
        }

        $marketingRoles = [
            'marketing_admin',
            'marketing_Accountant',
            'marketing_Specialist',
            'marketing_Guest',
        ];

        foreach ($marketingRoles as $roleName) {
            Role::firstOrCreate(
                [
                    'name' => $roleName,
                    'guard_name' => 'web',
                ],
                [
                    'section' => 'marketing',
                ]
            );
        }
    }
}