<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            if (!Schema::hasColumn('assets', 'link_status')) {
                $table->enum('link_status', ['linked', 'pending', 'failed'])->default('linked')->after('type');
            }
            if (!Schema::hasColumn('assets', 'original_name')) {
                $table->string('original_name')->nullable()->after('name');
            }
            if (!Schema::hasColumn('assets', 'original_text')) {
                $table->text('original_text')->nullable()->after('original_name');
            }
            if (!Schema::hasColumn('assets', 'nav_date')) {
                $table->date('nav_date')->nullable()->after('current_price');
            }
            if (!Schema::hasColumn('assets', 'last_scraped_at')) {
                $table->timestamp('last_scraped_at')->nullable()->after('nav_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['link_status', 'original_name', 'original_text', 'nav_date', 'last_scraped_at']);
        });
    }
};
