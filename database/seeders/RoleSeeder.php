<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contentRoles=[
            'content_admin',
            'content_Reviewer',
            'content_author',
            'content_Guest',
        ];

        foreach ($contentRoles as $role) {
            \App\Models\Role::create([
            'name' => $role,
            'guard_name' => 'web',
            'section'=>'content']);
        }


        $marketingRoles=[
            'marketing_admin',
            'marketing_Accountant',
            'marketing_Specialist',
            'marketing_Guest',
        ];

        foreach ($marketingRoles as $role) {
            \App\Models\Role::create([
            'name' => $role,
            'guard_name' => 'web',
            'section'=>'marketing']);
        }
    }
}
