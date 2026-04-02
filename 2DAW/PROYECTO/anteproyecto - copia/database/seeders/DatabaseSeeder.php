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

        // 2. Crear usuario principal
        $user = User::firstOrCreate(
            ['email' => 'corderorafa0@gmail.com'],
            [
                'name' => 'Rafael Cordero',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 3. Limpiar datos previos de este usuario (en caso de re-seed sin migrate:fresh)
        // Esto asegura "no agregar datos en patrimonio neto" si ya existían
        Transaction::where('user_id', $user->id)->delete();
        Asset::where('user_id', $user->id)->delete();
        Portfolio::where('user_id', $user->id)->delete();

        // NO se crean Carteras, Activos ni Transacciones de ejemplo.
        // El usuario comenzará con un Dashboard "limpio" (Zero State).
    }
}
