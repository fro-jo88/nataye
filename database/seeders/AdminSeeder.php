<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        User::updateOrCreate(
            ['email' => 'admin@nataye.test'],
            [
                'uuid' => \Illuminate\Support\Str::uuid(),
                'first_name' => 'System',
                'last_name' => 'Administrator',
                'phone' => '+11234567890',
                'password' => Hash::make('Admin123!'),
                'role_id' => $adminRole->id,
                'status' => 'active',
            ]
        );
    }
}
