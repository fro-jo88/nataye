<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->string('slug', 255)->unique();
            $table->text('body');
            $table->foreignId('author_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->datetime('published_at')->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->json('tags')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('author_id');
            $table->index('status');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
