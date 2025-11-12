<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $studentRole = Role::where('slug', 'student')->first();
        $classes = ClassModel::with('sections')->get();

        $firstNames = ['Alice', 'Bob', 'Charlie', 'Diana', 'Eve', 'Frank', 'Grace', 'Henry', 'Ivy', 'Jack'];
        $lastNames = ['Brown', 'Davis', 'Evans', 'Garcia', 'Harris', 'Jackson', 'King', 'Lee', 'Martin', 'Nelson'];

        $studentCount = 0;

        foreach ($classes->take(2) as $class) {
            foreach ($class->sections as $section) {
                for ($i = 0; $i < 5; $i++) {
                    $studentCount++;
                    $firstName = $firstNames[$studentCount % count($firstNames)];
                    $lastName = $lastNames[$studentCount % count($lastNames)];

                    $user = User::create([
                        'uuid' => \Illuminate\Support\Str::uuid(),
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'email' => strtolower($firstName . '.' . $lastName . $studentCount . '@student.nataye.test'),
                        'phone' => '+198765' . str_pad($studentCount, 4, '0', STR_PAD_LEFT),
                        'password' => Hash::make('Student123!'),
                        'role_id' => $studentRole->id,
                        'status' => 'active',
                    ]);

                    Student::create([
                        'user_id' => $user->id,
                        'student_code' => 'STD' . str_pad($studentCount, 6, '0', STR_PAD_LEFT),
                        'admission_no' => 'ADM' . str_pad($studentCount, 6, '0', STR_PAD_LEFT),
                        'gender' => $i % 2 === 0 ? 'male' : 'female',
                        'date_of_birth' => now()->subYears(rand(6, 12)),
                        'enrollment_date' => now()->subMonths(rand(1, 24)),
                        'current_class_id' => $class->id,
                        'section_id' => $section->id,
                        'extra' => [
                            'guardian_phone' => '+197654' . str_pad($studentCount, 4, '0', STR_PAD_LEFT),
                            'guardian_email' => 'parent' . $studentCount . '@nataye.test',
                        ],
                    ]);
                }
            }
        }
    }
}
