<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\Section;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            ['name' => 'Grade 1', 'code' => 'G1'],
            ['name' => 'Grade 2', 'code' => 'G2'],
            ['name' => 'Grade 3', 'code' => 'G3'],
            ['name' => 'Grade 4', 'code' => 'G4'],
            ['name' => 'Grade 5', 'code' => 'G5'],
        ];

        foreach ($classes as $classData) {
            $class = ClassModel::updateOrCreate(
                ['code' => $classData['code']],
                $classData
            );

            // Create sections A and B for each class
            Section::updateOrCreate(
                ['class_id' => $class->id, 'name' => 'A'],
                ['capacity' => 30]
            );

            Section::updateOrCreate(
                ['class_id' => $class->id, 'name' => 'B'],
                ['capacity' => 30]
            );
        }
    }
}
