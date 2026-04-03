<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('email');
            $table->text('bio')->nullable()->after('username');
            $table->string('banner_path')->nullable()->after('avatar');
            $table->foreignId('pinned_post_id')->nullable()->after('banner_path')->constrained('posts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['pinned_post_id']);
            $table->dropColumn(['username', 'bio', 'banner_path', 'pinned_post_id']);
        });
    }
};
