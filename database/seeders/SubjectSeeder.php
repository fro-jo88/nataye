<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\ClassModel;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            'Mathematics',
            'English',
            'Science',
            'Social Studies',
            'Physical Education',
            'Art',
            'Music',
        ];

        $classes = ClassModel::all();

        foreach ($classes as $class) {
            foreach ($subjects as $subjectName) {
                Subject::updateOrCreate(
                    [
                        'name' => $subjectName,
                        'class_id' => $class->id,
                    ],
                    [
                        'code' => strtoupper(substr($subjectName, 0, 3)) . $class->code,
                    ]
                );
            }
        }
    }
}
