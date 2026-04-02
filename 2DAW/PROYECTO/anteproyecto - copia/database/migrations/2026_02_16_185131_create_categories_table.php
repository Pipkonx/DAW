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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade'); // Nullable for system defaults
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // income, expense
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('usage_count')->default(0);
            $table->timestamps();
        });

        // Seed default categories
        $this->seedDefaults();
    }

    private function seedDefaults()
    {
        // Income
        $incomeParentId = DB::table('categories')->insertGetId([
            'name' => 'Ingresos',
            'type' => 'income',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $incomes = ['Salario', 'Intereses/Dividendos', 'Ingreso Efectivo', 'Aplicaciones', 'Encuestas'];

        foreach ($incomes as $subName) {
            DB::table('categories')->insert([
                'user_id' => null,
                'parent_id' => $incomeParentId,
                'name' => $subName,
                'type' => 'income',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Expenses
        $expenses = [
            'Casa' => ['Hipoteca/Alquiler', 'Seguro', 'Reparaciones', 'Servicios', 'Suministros', 'Otro'],
            'Vida Diaria' => ['Comida', 'Cuidado de Niños', 'Tutoría', 'Transporte', 'Entretenimiento', 'Salud', 'Vacaciones', 'Ocio', 'Cuotas y Suscripciones', 'Personal', 'Obligaciones Financieras', 'Pagos Varios'],
        ];

        foreach ($expenses as $parentName => $subs) {
            $parentId = DB::table('categories')->insertGetId([
                'name' => $parentName,
                'type' => 'expense',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($subs as $subName) {
                DB::table('categories')->insert([
                    'user_id' => null,
                    'parent_id' => $parentId,
                    'name' => $subName,
                    'type' => 'expense',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
