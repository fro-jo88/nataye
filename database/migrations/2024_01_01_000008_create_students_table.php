<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
            $table->string('student_code', 50)->unique();
            $table->string('admission_no', 50)->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->foreignId('current_class_id')->nullable()->constrained('classes')->onUpdate('cascade')->onDelete('set null');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onUpdate('cascade')->onDelete('set null');
            $table->string('address', 255)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('student_code');
            $table->index('admission_no');
            $table->index('current_class_id');
            $table->index('section_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
