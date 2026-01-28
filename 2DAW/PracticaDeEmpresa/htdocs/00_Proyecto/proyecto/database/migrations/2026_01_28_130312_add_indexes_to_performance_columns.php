<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->addIndexIfNotExists('practices', ['user_id', 'alumno_id', 'curso_id', 'target_role']);
        $this->addIndexIfNotExists('messages', ['sender_id', 'receiver_id', 'is_read']);
        $this->addIndexIfNotExists('alumnos', ['user_id', 'curso_id', 'empresa_id']);
        $this->addIndexIfNotExists('observacion_diarias', ['alumno_id', 'fecha']);
        $this->addIndexIfNotExists('incidencias', ['alumno_id', 'estado', 'tipo']);
    }

    private function addIndexIfNotExists(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) return;

        $existingIndexes = collect(DB::select("SHOW INDEX FROM {$table}"))->pluck('Key_name')->unique();

        Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $columns, $existingIndexes) {
            foreach ($columns as $column) {
                $indexName = "{$table}_{$column}_index";
                if (!$existingIndexes->contains($indexName)) {
                    $tableBlueprint->index($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->dropIndexIfExists('incidencias', ['alumno_id', 'estado', 'tipo']);
        $this->dropIndexIfExists('observacion_diarias', ['alumno_id', 'fecha']);
        $this->dropIndexIfExists('alumnos', ['user_id', 'curso_id', 'empresa_id']);
        $this->dropIndexIfExists('messages', ['sender_id', 'receiver_id', 'is_read']);
        $this->dropIndexIfExists('practices', ['user_id', 'alumno_id', 'curso_id', 'target_role']);
    }

    private function dropIndexIfExists(string $table, array $columns): void
    {
        if (!Schema::hasTable($table)) return;

        $existingIndexes = collect(DB::select("SHOW INDEX FROM {$table}"))->pluck('Key_name')->unique();

        Schema::table($table, function (Blueprint $tableBlueprint) use ($table, $columns, $existingIndexes) {
            foreach ($columns as $column) {
                $indexName = "{$table}_{$column}_index";
                if ($existingIndexes->contains($indexName)) {
                    $tableBlueprint->dropIndex([$column]);
                }
            }
        });
    }
};
