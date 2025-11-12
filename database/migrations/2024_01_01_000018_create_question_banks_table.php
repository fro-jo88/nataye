<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->nullable()->constrained('exams')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('restrict');
            $table->enum('type', ['mcq', 'short', 'essay']);
            $table->text('question_text');
            $table->json('options')->nullable()->comment('For MCQ questions');
            $table->text('answer')->nullable()->comment('Correct answer or marking scheme');
            $table->float('marks', 8, 2)->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('exam_id');
            $table->index('author_id');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_banks');
    }
};
