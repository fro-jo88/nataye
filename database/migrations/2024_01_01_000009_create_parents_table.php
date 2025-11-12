<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name', 150);
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('relation', 50)->nullable();
            $table->string('address', 255)->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('phone');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
