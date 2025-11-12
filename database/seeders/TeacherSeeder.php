<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacherRole = Role::where('slug', 'teacher')->first();

        $teachers = [
            ['first_name' => 'John', 'last_name' => 'Smith', 'email' => 'john.smith@nataye.test'],
            ['first_name' => 'Mary', 'last_name' => 'Johnson', 'email' => 'mary.johnson@nataye.test'],
            ['first_name' => 'David', 'last_name' => 'Williams', 'email' => 'david.williams@nataye.test'],
        ];

        foreach ($teachers as $index => $teacherData) {
            $user = User::updateOrCreate(
                ['email' => $teacherData['email']],
                [
                    'uuid' => \Illuminate\Support\Str::uuid(),
                    'first_name' => $teacherData['first_name'],
                    'last_name' => $teacherData['last_name'],
                    'phone' => '+1234567' . str_pad($index + 1, 4, '0', STR_PAD_LEFT),
                    'password' => Hash::make('Teacher123!'),
                    'role_id' => $teacherRole->id,
                    'status' => 'active',
                ]
            );

            Teacher::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'employee_no' => 'EMP' . str_pad($index + 1, 6, '0', STR_PAD_LEFT),
                    'hire_date' => now()->subYears(rand(1, 5)),
                    'qualification' => 'Bachelor of Education',
                ]
            );
        }
    }
}
