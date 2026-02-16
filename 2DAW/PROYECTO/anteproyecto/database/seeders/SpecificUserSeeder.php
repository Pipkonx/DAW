<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use App\Models\BankAccount;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class SpecificUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear usuario principal
        $user = User::firstOrCreate(
            ['email' => 'corderorafa0@gmail.com'],
            [
                'name' => 'Rafael Cordero',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'investment_return_rate' => 7.0,
            ]
        );

        // Limpiar datos anteriores
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Transaction::where('user_id', $user->id)->delete();
        Asset::where('user_id', $user->id)->delete();
        Portfolio::where('user_id', $user->id)->delete();
        BankAccount::where('user_id', $user->id)->delete();
        Category::where('user_id', $user->id)->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 2. Crear Categorías para el usuario
        // Clonar categorías del sistema
        $systemCategories = Category::whereNull('user_id')->whereNull('parent_id')->with('children')->get();
        foreach ($systemCategories as $systemCat) {
            $newParent = $systemCat->replicate();
            $newParent->user_id = $user->id;
            $newParent->save();
            foreach ($systemCat->children as $systemChild) {
                $newChild = $systemChild->replicate();
                $newChild->user_id = $user->id;
                $newChild->parent_id = $newParent->id;
                $newChild->save();
            }
        }

        // Obtener IDs de categorías útiles para transacciones
        $catSalario = Category::where('user_id', $user->id)->where('name', 'Salario')->first();
        $catVivienda = Category::where('user_id', $user->id)->where('name', 'Vivienda')->first();
        if (!$catVivienda) {
            // Si no existe 'Vivienda' como tal (puede estar anidada), buscamos 'Casa' y una hija 'Hipoteca/Alquiler'
            $catCasa = Category::where('user_id', $user->id)->where('name', 'Casa')->first();
            $catVivienda = Category::where('user_id', $user->id)->where('parent_id', $catCasa?->id)->where('name', 'Hipoteca/Alquiler')->first();
        }
        $catComida = Category::where('user_id', $user->id)->where('name', 'Comida')->first(); // Puede estar bajo Vida Diaria
        if (!$catComida) {
             $catVida = Category::where('user_id', $user->id)->where('name', 'Vida Diaria')->first();
             $catComida = Category::where('user_id', $user->id)->where('parent_id', $catVida?->id)->where('name', 'Comida')->first();
        }
        $catTransporte = Category::where('user_id', $user->id)->where('name', 'Transporte')->first(); // Bajo Vida Diaria

        // 3. Crear Cuentas Bancarias (Líquido)
        BankAccount::create([
            'user_id' => $user->id,
            'name' => 'BBVA Cuenta Nómina',
            'type' => 'checking',
            'balance' => 4500.50,
            'currency' => 'EUR',
        ]);

        BankAccount::create([
            'user_id' => $user->id,
            'name' => 'MyInvestor Cuenta Ahorro',
            'type' => 'savings',
            'balance' => 12000.00,
            'apy' => 2.50,
            'currency' => 'EUR',
        ]);

        // 4. Crear Carteras (Portfolios)
        $portfolioLargoPlazo = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Largo Plazo (Jubilación)',
            'description' => 'ETF y Acciones sólidas',
        ]);

        $portfolioTrade = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Trade Republic',
            'description' => 'Metales y Especulación',
        ]);

        $portfolioCripto = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Cripto Ledger',
            'description' => 'Cold storage BTC/ETH',
        ]);

        // 5. Crear Activos (Assets)
        // Largo Plazo
        $sp500 = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioLargoPlazo->id,
            'name' => 'S&P 500 ETF (VUSA)',
            'ticker' => 'VUSA',
            'type' => 'etf',
            'quantity' => 50,
            'avg_buy_price' => 75.20,
            'current_price' => 88.50,
            'color' => '#10B981', // Verde
        ]);

        $msft = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioLargoPlazo->id,
            'name' => 'Microsoft Corp',
            'ticker' => 'MSFT',
            'type' => 'stock',
            'quantity' => 10,
            'avg_buy_price' => 310.00,
            'current_price' => 405.00,
            'color' => '#0EA5E9', // Azul
        ]);

        // Trade Republic
        $gold = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioTrade->id,
            'name' => 'Oro Físico (ETC)',
            'ticker' => 'SGLD',
            'type' => 'etf', // Changed from commodity to etf as commodity is not in enum
            'quantity' => 25,
            'avg_buy_price' => 180.00,
            'current_price' => 205.50,
            'color' => '#EAB308', // Amarillo
        ]);

        // Cripto
        $btc = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $portfolioCripto->id,
            'name' => 'Bitcoin',
            'ticker' => 'BTC',
            'type' => 'crypto',
            'quantity' => 0.15,
            'avg_buy_price' => 45000.00,
            'current_price' => 62000.00,
            'color' => '#F97316', // Naranja
        ]);

        // 6. Generar Historial de Transacciones (Últimos 6 meses)
        $startDate = Carbon::now()->subMonths(6);
        
        for ($i = 0; $i <= 6; $i++) {
            $currentMonth = $startDate->copy()->addMonths($i);
            
            // Ingreso: Nómina
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'income',
                'amount' => 2400.00,
                'date' => $currentMonth->copy()->startOfMonth()->addDays(2),
                'category_id' => $catSalario?->id,
                'description' => 'Nómina Mensual',
            ]);

            // Gasto: Alquiler
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 850.00,
                'date' => $currentMonth->copy()->startOfMonth()->addDays(5),
                'category_id' => $catVivienda?->id,
                'description' => 'Alquiler Piso',
            ]);

            // Gasto: Supermercado (varios)
            for ($j=0; $j<4; $j++) {
                Transaction::create([
                    'user_id' => $user->id,
                    'type' => 'expense',
                    'amount' => rand(60, 150),
                    'date' => $currentMonth->copy()->startOfMonth()->addDays(rand(1, 28)),
                    'category_id' => $catComida?->id,
                    'description' => 'Compra Supermercado',
                ]);
            }

            // Inversión mensual (DCA)
            if ($i > 0) { // Empezamos a invertir desde el segundo mes simulado
                Transaction::create([
                    'user_id' => $user->id,
                    'portfolio_id' => $portfolioLargoPlazo->id,
                    'asset_id' => $sp500->id,
                    'type' => 'buy',
                    'amount' => 300.00,
                    'date' => $currentMonth->copy()->startOfMonth()->addDays(10),
                    'category_id' => null, // Es inversión
                    'description' => 'Compra mensual ETF S&P500',
                    'quantity' => 300 / 80, // aprox
                    'price_per_unit' => 80 + rand(-2, 2),
                ]);
            }
        }
    }
}
