<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Models\ClassModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Role::create(['name' => 'Student', 'slug' => 'student']);
    }

    public function test_student_has_full_name_accessor(): void
    {
        $user = User::factory()->create([
            'first_name' => 'John',
            'last_name' => 'Doe',
        ]);

        $student = Student::factory()->create(['user_id' => $user->id]);

        $this->assertEquals('John Doe', $student->full_name);
    }

    public function test_student_can_calculate_age(): void
    {
        $user = User::factory()->create();
        
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'date_of_birth' => now()->subYears(10),
        ]);

        $this->assertEquals(10, $student->age);
    }

    public function test_student_belongs_to_class(): void
    {
        $user = User::factory()->create();
        $class = ClassModel::factory()->create();
        
        $student = Student::factory()->create([
            'user_id' => $user->id,
            'current_class_id' => $class->id,
        ]);

        $this->assertInstanceOf(ClassModel::class, $student->currentClass);
        $this->assertEquals($class->id, $student->currentClass->id);
    }
}
