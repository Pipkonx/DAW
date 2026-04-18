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
        Schema::table('login_activities', function (Blueprint $table) {
            $table->string('city')->nullable()->after('ip_address');
            $table->string('country')->nullable()->after('city');
            $table->string('browser')->nullable()->after('user_agent');
            $table->string('browser_version')->nullable()->after('browser');
            $table->string('os')->nullable()->after('browser_version');
            $table->string('device')->nullable()->after('os');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('login_activities', function (Blueprint $table) {
            $table->dropColumn(['city', 'country', 'browser', 'browser_version', 'os', 'device']);
        });
    }
};
