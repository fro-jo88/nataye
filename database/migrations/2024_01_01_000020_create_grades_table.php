<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->float('marks_obtained', 8, 2);
            $table->string('grade_letter', 5)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'exam_id']);
            $table->index('subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
