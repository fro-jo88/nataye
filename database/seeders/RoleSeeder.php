<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Administrator', 'slug' => 'admin', 'description' => 'System administrator with full access'],
            ['name' => 'Teacher', 'slug' => 'teacher', 'description' => 'Teaching staff member'],
            ['name' => 'Student', 'slug' => 'student', 'description' => 'Student enrolled in the system'],
            ['name' => 'Parent', 'slug' => 'parent', 'description' => 'Parent or guardian of a student'],
            ['name' => 'Staff', 'slug' => 'staff', 'description' => 'Non-teaching staff member'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}
