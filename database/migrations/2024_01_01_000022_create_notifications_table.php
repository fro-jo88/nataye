<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['system', 'email', 'push']);
            $table->string('title', 255);
            $table->text('body');
            $table->json('target_roles')->nullable();
            $table->json('target_user_ids')->nullable();
            $table->json('meta')->nullable();
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->datetime('sent_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
