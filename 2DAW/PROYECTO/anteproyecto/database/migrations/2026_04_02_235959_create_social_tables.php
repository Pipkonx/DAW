<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Posts (debates)
        if (!Schema::hasTable('posts')) {
            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('market_asset_id')->constrained()->cascadeOnDelete();
                $table->text('content');
                $table->timestamps();
            });
        }

        // Comments (nested)
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->text('content');
                $table->timestamps();
                $table->foreign('parent_id')->references('id')->on('comments')->cascadeOnDelete();
            });
        }

        // Polymorphic Likes (post or comment)
        if (!Schema::hasTable('likes')) {
            Schema::create('likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('likeable_id');
                $table->string('likeable_type');
                $table->timestamps();
                $table->unique(['user_id', 'likeable_id', 'likeable_type']);
            });
        }

        // Reposts
        if (!Schema::hasTable('reposts')) {
            Schema::create('reposts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'post_id']);
            });
        }

        // Bookmarks
        if (!Schema::hasTable('bookmarks')) {
            Schema::create('bookmarks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('post_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'post_id']);
            });
        }

        // Reports (polymorphic)
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->unsignedBigInteger('reportable_id');
                $table->string('reportable_type');
                $table->string('reason');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('reposts');
        Schema::dropIfExists('likes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
    }
};
