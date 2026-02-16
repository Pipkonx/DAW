<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear usuario principal si no existe
        $user = User::firstOrCreate(
            ['email' => 'corderorafa0@gmail.com'],
            [
                'name' => 'Rafael Cordero',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Limpiar datos anteriores del usuario para evitar duplicados en re-seed
        Transaction::where('user_id', $user->id)->delete();
        Asset::where('user_id', $user->id)->delete();
        Portfolio::where('user_id', $user->id)->delete();

        // 0. Crear Carteras (Portfolios)
        $portfolioLargoPlazo = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Largo Plazo (Jubilación)',
            'description' => 'Inversiones conservadoras para retiro',
        ]);

        $portfolioCripto = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Cripto & Alto Riesgo',
            'description' => 'Apuestas especulativas y crecimiento agresivo',
        ]);

        $portfolioInmobiliario = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Inmobiliario',
            'description' => 'Propiedades y REITs',
        ]);

        // 1. Crear Activos (Inversiones)
        $bitcoin = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioCripto->id,
            'name' => 'Bitcoin',
            'ticker' => 'BTC',
            'type' => 'crypto',
            'quantity' => 0.45,
            'avg_buy_price' => 35000,
            'current_price' => 42000,
            'color' => '#F7931A',
        ]);

        $apple = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioLargoPlazo->id,
            'name' => 'Apple Inc.',
            'ticker' => 'AAPL',
            'type' => 'stock',
            'quantity' => 15,
            'avg_buy_price' => 150,
            'current_price' => 185.50,
            'color' => '#000000',
        ]);

        $sp500 = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioLargoPlazo->id,
            'name' => 'Vanguard S&P 500 ETF',
            'ticker' => 'VOO',
            'type' => 'etf',
            'quantity' => 10,
            'avg_buy_price' => 380,
            'current_price' => 450.20,
            'color' => '#BF1234',
        ]);

        $bond = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioLargoPlazo->id,
            'name' => 'Bono del Estado 10Y',
            'ticker' => 'ES10Y',
            'type' => 'bond',
            'quantity' => 5000,
            'avg_buy_price' => 1,
            'current_price' => 1.02,
            'color' => '#10B981',
        ]);

        $realEstate = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioInmobiliario->id,
            'name' => 'Apartamento Playa',
            'type' => 'real_estate',
            'quantity' => 1,
            'avg_buy_price' => 150000,
            'current_price' => 165000,
            'color' => '#8B5CF6',
        ]);

        // 2. Generar Historial de Transacciones (Últimos 6 meses)
        $startDate = Carbon::now()->subMonths(6);
        
        $categories = [
            'Gasolina' => ['Repsol', 'Shell', 'Gasolinera LowCost', 'BP'],
            'Supermercado' => ['Mercadona', 'Carrefour', 'Lidl', 'Aldi'],
            'Comida' => ['Restaurante X', 'Burger King', 'Pizzeria', 'Cafetería'],
            'Transporte' => ['Uber', 'Metro', 'Autobús', 'Taxi'],
            'Suscripciones' => ['Netflix', 'Spotify', 'Amazon Prime', 'HBO'],
            'Otros' => ['Regalo cumpleaños', 'Cine', 'Farmacia', 'Ferretería']
        ];

        for ($i = 0; $i <= 6; $i++) {
            $currentMonth = $startDate->copy()->addMonths($i);
            
            // Ingreso: Nómina (día 1-5 del mes)
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'income',
                'amount' => 2850.00,
                'date' => $currentMonth->copy()->startOfMonth()->addDays(rand(0, 4)),
                'category' => 'Salario',
                'description' => 'Nómina mensual empresa',
            ]);

            // Bonus trimestral (mes 3 y 6)
            if ($i % 3 == 0 && $i > 0) {
                 Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'reward',
                    'amount' => 500.00,
                    'date' => $currentMonth->copy()->startOfMonth()->addDays(15),
                    'category' => 'Bonus',
                    'description' => 'Bonus de productividad',
                ]);
            }

            // Gastos fijos
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 950.00,
                'date' => $currentMonth->copy()->startOfMonth()->addDays(5),
                'category' => 'Vivienda',
                'description' => 'Alquiler apartamento',
            ]);

            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 140.00,
                'date' => $currentMonth->copy()->startOfMonth()->addDays(5),
                'category' => 'Servicios',
                'description' => 'Luz, Agua e Internet',
            ]);

            // Gastos variables DIARIOS (Gasolina, Coche, etc)
            for ($j = 0; $j < rand(10, 20); $j++) { // Aumentamos frecuencia de gastos
                $cat = array_rand($categories);
                $desc = $categories[$cat][array_rand($categories[$cat])];
                $amount = match($cat) {
                    'Gasolina' => rand(40, 80),
                    'Supermercado' => rand(20, 150),
                    'Comida' => rand(10, 50),
                    'Transporte' => rand(2, 20),
                    'Suscripciones' => rand(10, 20),
                    default => rand(5, 100),
                };

                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'expense',
                    'amount' => $amount,
                    'date' => $currentMonth->copy()->startOfMonth()->addDays(rand(1, 28)),
                    'category' => $cat,
                    'description' => $desc,
                ]);
            }
        }

        // 3. Transacciones de Inversión (Compras Históricas)
        
        // Compra Inmobiliaria (Hace 6 meses)
        Transaction::create([
            'user_id' => $user->id,
            'asset_id' => $realEstate->id,
            'type' => 'buy',
            'amount' => 150000,
            'date' => Carbon::now()->subMonths(6),
            'category' => 'Inversión',
            'description' => 'Compra Apartamento Playa',
            'quantity' => 1,
            'price_per_unit' => 150000,
        ]);

        // Compra Bonos (Hace 4 meses)
        Transaction::create([
            'user_id' => $user->id,
            'asset_id' => $bond->id,
            'type' => 'buy',
            'amount' => 5000,
            'date' => Carbon::now()->subMonths(4),
            'category' => 'Inversión',
            'description' => 'Bonos del Tesoro',
            'quantity' => 5000,
            'price_per_unit' => 1,
        ]);

        // Compra inicial de BTC hace 5 meses
        Transaction::create([
            'user_id' => $user->id,
            'asset_id' => $bitcoin->id,
            'type' => 'buy',
            'amount' => 0.2 * 30000,
            'date' => Carbon::now()->subMonths(5),
            'category' => 'Inversión',
            'description' => 'Compra inicial BTC',
            'quantity' => 0.2,
            'price_per_unit' => 30000,
        ]);

        // Compra reciente BTC
        Transaction::create([
            'user_id' => $user->id,
            'asset_id' => $bitcoin->id,
            'type' => 'buy',
            'amount' => 0.25 * 39000,
            'date' => Carbon::now()->subMonth(),
            'category' => 'Inversión',
            'description' => 'DCA Bitcoin',
            'quantity' => 0.25,
            'price_per_unit' => 39000,
        ]);

        // Dividendo Apple (Recibido hace 2 meses)
        Transaction::create([
            'user_id' => $user->id,
            'asset_id' => $apple->id,
            'type' => 'dividend',
            'amount' => 15.50,
            'date' => Carbon::now()->subMonths(2),
            'category' => 'Dividendos',
            'description' => 'Dividendo Trimestral Apple',
        ]);
        
        // Venta parcial de acciones (Hace 1 mes) - Ejemplo de Venta
        Transaction::create([
            'user_id' => $user->id,
            'asset_id' => $apple->id,
            'type' => 'sell',
            'amount' => 5 * 180, // Venta de 5 acciones a 180
            'date' => Carbon::now()->subMonth(),
            'category' => 'Venta Inversión',
            'description' => 'Toma de beneficios Apple',
            'quantity' => 5,
            'price_per_unit' => 180,
        ]);

        // Regalo recibido (Hace 3 meses)
        Transaction::create([
            'user_id' => $user->id,
            'type' => 'gift',
            'amount' => 100.00,
            'date' => Carbon::now()->subMonths(3),
            'category' => 'Regalos',
            'description' => 'Cumpleaños abuela',
        ]);
    }
}
