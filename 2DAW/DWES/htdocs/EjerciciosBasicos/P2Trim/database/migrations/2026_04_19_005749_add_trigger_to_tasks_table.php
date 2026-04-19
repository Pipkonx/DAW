<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Añadimos un campo específico para la fecha de creación "manual" por disparador
        // aunque Laravel ya usa created_at, el enunciado pide un disparador.
        
        DB::unprepared('
            CREATE TRIGGER tr_fecha_creacion_tareas 
            BEFORE INSERT ON tasks 
            FOR EACH ROW 
            SET NEW.created_at = NOW();
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_fecha_creacion_tareas');
    }
};
