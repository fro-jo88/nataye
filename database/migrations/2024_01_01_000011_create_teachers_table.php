<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('employee_no', 50)->unique();
            $table->date('hire_date')->nullable();
            $table->string('qualification', 255)->nullable();
            $table->json('extra')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('employee_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
