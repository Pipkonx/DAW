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
        Schema::table('usuarios', function (Blueprint $table) {
            if (!Schema::hasColumn('usuarios', 'rol')) {
                $table->string('rol', 20)->default('operario')->after('contraseña');
            }
            if (!Schema::hasColumn('usuarios', 'remember_token')) {
                $table->rememberToken();
            }
            // Laravel 11/12 users often expect timestamps
            if (!Schema::hasColumn('usuarios', 'created_at')) {
                $table->timestamps();
            }
        });

        // Hash existing passwords
        $users = \Illuminate\Support\Facades\DB::table('usuarios')->get();
        foreach ($users as $user) {
            // Check if password is already hashed (Laravel hashes are usually 60 chars)
            if (strlen($user->contraseña) < 60) {
                \Illuminate\Support\Facades\DB::table('usuarios')
                    ->where('id', $user->id)
                    ->update(['contraseña' => \Illuminate\Support\Facades\Hash::make($user->contraseña)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['rol', 'remember_token', 'created_at', 'updated_at']);
        });
    }
};
