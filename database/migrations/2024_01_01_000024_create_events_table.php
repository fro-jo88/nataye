<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->datetime('start_datetime');
            $table->datetime('end_datetime')->nullable();
            $table->string('location', 255)->nullable();
            $table->json('target_audience')->nullable()->comment('Roles or class IDs');
            $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            $table->timestamps();

            $table->index('start_datetime');
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
