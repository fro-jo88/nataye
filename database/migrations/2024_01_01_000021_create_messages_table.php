<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_user_id')->constrained('users')->onDelete('cascade');
            $table->bigInteger('thread_id')->nullable();
            $table->string('subject', 255)->nullable();
            $table->text('body');
            $table->boolean('is_read')->default(false);
            $table->json('attachments')->nullable();
            $table->timestamps();

            $table->index('from_user_id');
            $table->index('to_user_id');
            $table->index('thread_id');
            $table->index('is_read');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
