<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->onUpdate('cascade')->onDelete('set null');
            $table->timestamps();

            $table->index('code');
            $table->index('class_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
