<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
            $table->datetime('start_datetime')->nullable();
            $table->datetime('end_datetime')->nullable();
            $table->float('total_marks', 8, 2)->default(0);
            $table->boolean('is_online')->default(false);
            $table->float('passing_marks', 8, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->timestamps();

            $table->index('code');
            $table->index(['class_id', 'section_id']);
            $table->index('status');
            $table->index('start_datetime');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
