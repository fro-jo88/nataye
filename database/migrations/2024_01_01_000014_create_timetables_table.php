<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
            $table->tinyInteger('weekday')->comment('1=Monday, 7=Sunday');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('room', 50)->nullable();
            $table->timestamps();

            $table->index(['class_id', 'section_id', 'weekday']);
            $table->index('teacher_id');
            $table->index('subject_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
