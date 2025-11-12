<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploader_id')->constrained('users')->onDelete('restrict');
            $table->string('path', 500);
            $table->string('filename', 255);
            $table->string('mime_type', 100);
            $table->bigInteger('size_bytes')->default(0);
            $table->string('reference_type', 50)->nullable();
            $table->bigInteger('reference_id')->nullable();
            $table->timestamps();

            $table->index('uploader_id');
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
