<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parentRole = Role::where('slug', 'parent')->first();
        $students = Student::with('user')->limit(10)->get();

        $parentCount = 0;

        foreach ($students->chunk(2) as $studentPair) {
            $parentCount++;

            $user = User::create([
                'uuid' => \Illuminate\Support\Str::uuid(),
                'first_name' => 'Parent',
                'last_name' => 'User' . $parentCount,
                'email' => 'parent' . $parentCount . '@nataye.test',
                'phone' => '+197654' . str_pad($parentCount, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('Parent123!'),
                'role_id' => $parentRole->id,
                'status' => 'active',
            ]);

            $parent = ParentModel::create([
                'user_id' => $user->id,
                'name' => 'Parent User' . $parentCount,
                'email' => $user->email,
                'phone' => $user->phone,
                'relation' => 'Parent',
            ]);

            // Link to students
            foreach ($studentPair as $index => $student) {
                $parent->linkToStudent($student, $index === 0);
            }
        }
    }
}
