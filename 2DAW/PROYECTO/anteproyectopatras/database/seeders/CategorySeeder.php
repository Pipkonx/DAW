<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Category::truncate();
        Schema::enableForeignKeyConstraints();

        // --- INGRESOS ---
        $incomeCategories = [
            'Nómina' => [],
            'Bonus / Primas' => [],
            'Negocios' => [],
            'Dividendos' => [],
            'Regalos' => [],
            'Venta de Artículos' => [],
            'Reembolsos' => [],
            'Otros Ingresos' => []
        ];

        foreach ($incomeCategories as $name => $subs) {
            $cat = Category::create([
                'name' => $name,
                'type' => 'income',
                'parent_id' => null
            ]);
        }

        // --- GASTOS ---
        $expenseCategories = [
            'Vivienda' => [
                'Hipoteca/Alquiler',
                'Luz',
                'Agua',
                'Gas',
                'Internet/Teléfono',
                'Comunidad',
                'Mantenimiento',
                'Seguro Hogar'
            ],
            'Alimentación' => [
                'Supermercado',
                'Restaurantes',
                'Bares/Cafetería',
                'Comida a domicilio'
            ],
            'Transporte' => [
                'Gasolina',
                'Transporte Público',
                'Taxi/VTC',
                'Mantenimiento Vehículo',
                'Parking/Peajes',
                'Seguro Vehículo'
            ],
            'Salud y Bienestar' => [
                'Farmacia',
                'Médico/Dentista',
                'Gimnasio/Deporte',
                'Cuidado Personal',
                'Seguro Salud'
            ],
            'Ocio y Entretenimiento' => [
                'Suscripciones (Netflix, Spotify...)',
                'Cine/Teatro/Eventos',
                'Hobbies',
                'Vacaciones/Viajes',
                'Electrónica/Gadgets'
            ],
            'Educación' => [
                'Libros',
                'Cursos/Formación',
                'Matrículas'
            ],
            'Compras' => [
                'Ropa y Calzado',
                'Hogar/Decoración',
                'Regalos'
            ],
            'Finanzas' => [
                'Impuestos',
                'Comisiones Bancarias',
                'Préstamos',
                'Seguro Vida'
            ],
            'Otros' => [
                'Mascotas',
                'Donaciones',
                'Varios'
            ]
        ];

        foreach ($expenseCategories as $catName => $subs) {
            $parent = Category::create([
                'name' => $catName,
                'type' => 'expense',
                'parent_id' => null
            ]);

            foreach ($subs as $subName) {
                Category::create([
                    'name' => $subName,
                    'type' => 'expense',
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}
