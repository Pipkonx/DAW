<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Ejecutar Seeders de Datos Estáticos / Globales
        $this->call([
            CategorySeeder::class,     // Categorías de gastos/ingresos (necesarias para funcionalidad base)
            MarketAssetSeeder::class,  // Datos de mercado (tickers globales, no vinculados a usuario)
        ]);

        // 2. Crear/Actualizar usuarios de evaluación
        $admin = User::updateOrCreate(
            ['email' => 'admin@fintechpro.com'],
            [
                'name' => 'Administrador',
                'username' => 'admin_pro',
                'password' => Hash::make('admin1234'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]
        );

        $testUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Usuario de Prueba',
                'username' => 'test_user',
                'password' => Hash::make('password1234'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]
        );

        // 3. Limpiar datos previos del administrador (en caso de re-seed sin migrate:fresh)
        Transaction::where('user_id', $admin->id)->delete();
        Asset::where('user_id', $admin->id)->delete();
        Portfolio::where('user_id', $admin->id)->delete();

        // NO se crean Carteras, Activos ni Transacciones de ejemplo.
        // El usuario comenzará con un Dashboard "limpio" (Zero State).
    }
}
