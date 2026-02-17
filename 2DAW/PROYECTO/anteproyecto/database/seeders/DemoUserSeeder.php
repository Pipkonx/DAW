<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Asset;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\MarketAsset;
use App\Models\AssetPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or Get User
        $user = User::firstOrCreate(
            ['email' => 'corderorafa0@gmail.com'],
            [
                'name' => 'Rafael Cordero',
                'password' => Hash::make('password'), // Default password
                // 'currency_code' => 'EUR', // Removed as it doesn't exist in users table
            ]
        );

        $this->command->info('User created/found: ' . $user->email);

        // Clear existing data for this user to avoid duplicates if re-run
        // Be careful with production, but for a demo seeder this is expected
        Transaction::where('user_id', $user->id)->delete();
        Asset::where('user_id', $user->id)->delete();
        Portfolio::where('user_id', $user->id)->delete();
        
        // 2. Create Portfolios
        $mainPortfolio = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Cartera Principal',
            'description' => 'Inversiones a largo plazo',
            'currency' => 'EUR', // Changed from currency_code to currency
            // 'is_default' => true, // Removed as it doesn't exist
        ]);

        $cryptoPortfolio = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Cripto Activos',
            'description' => 'Bitcoin y Altcoins',
            'currency' => 'USD', // Changed from currency_code to currency
        ]);

        $retirementPortfolio = Portfolio::create([
            'user_id' => $user->id,
            'name' => 'Jubilación',
            'description' => 'Plan de pensiones y bonos',
            'currency' => 'EUR', // Changed from currency_code to currency
        ]);

        // 3. Create Market Assets (if they don't exist)
        // Ensure we have some market data to link to
        $btc = MarketAsset::firstOrCreate(
            ['ticker' => 'BTC'], // Changed symbol to ticker
            [
                'name' => 'Bitcoin',
                'type' => 'crypto',
                'currency_code' => 'USD',
                // 'current_price' => 52000.00, // Removed as it doesn't exist in market_assets
            ]
        );

        $aapl = MarketAsset::firstOrCreate(
            ['ticker' => 'AAPL'], // Changed symbol to ticker
            [
                'name' => 'Apple Inc.',
                'type' => 'stock',
                'currency_code' => 'USD',
                // 'current_price' => 180.00, // Removed
            ]
        );

        $msci = MarketAsset::firstOrCreate(
            ['ticker' => 'IWDA.AS'], // Changed symbol to ticker
            [
                'name' => 'iShares Core MSCI World UCITS ETF',
                'type' => 'etf',
                'currency_code' => 'EUR',
                // 'current_price' => 88.50, // Removed
            ]
        );

         // 4. Create Historical Prices for Charts
        $this->createHistoricalPrices($btc, 50000, 60000);
        $this->createHistoricalPrices($aapl, 150, 200);
        $this->createHistoricalPrices($msci, 70, 95);

        // 5. Create User Assets & Transactions
        
        // --- Bitcoin in Crypto Portfolio ---
        $btcAsset = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $cryptoPortfolio->id,
            'market_asset_id' => $btc->id,
            'name' => 'Bitcoin',
            'ticker' => 'BTC',
            'type' => 'crypto',
            'quantity' => 0.5,
            'avg_buy_price' => 45000.00,
            'current_price' => 52000.00,
            'currency_code' => 'USD',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'portfolio_id' => $cryptoPortfolio->id,
            'asset_id' => $btcAsset->id,
            'type' => 'buy',
            'amount' => 22500.00, // 0.5 * 45000
            'quantity' => 0.5,
            'price_per_unit' => 45000.00,
            'date' => Carbon::now()->subMonths(6),
            'description' => 'Compra inicial de BTC',
        ]);

        // --- Apple in Main Portfolio ---
        $aaplAsset = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $mainPortfolio->id,
            'market_asset_id' => $aapl->id,
            'name' => 'Apple Inc.',
            'ticker' => 'AAPL',
            'type' => 'stock',
            'quantity' => 10,
            'avg_buy_price' => 150.00,
            'current_price' => 180.00,
            'currency_code' => 'USD',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'portfolio_id' => $mainPortfolio->id,
            'asset_id' => $aaplAsset->id,
            'type' => 'buy',
            'amount' => 1500.00,
            'quantity' => 10,
            'price_per_unit' => 150.00,
            'date' => Carbon::now()->subYear(1),
            'description' => 'Compra de acciones Apple',
        ]);

        // Dividend from Apple
        Transaction::create([
            'user_id' => $user->id,
            'portfolio_id' => $mainPortfolio->id,
            'asset_id' => $aaplAsset->id,
            'type' => 'dividend',
            'amount' => 5.00,
            'date' => Carbon::now()->subMonths(3),
            'description' => 'Dividendo Apple Trimestral',
        ]);

        // --- MSCI World in Main Portfolio ---
        $msciAsset = Asset::create([
            'user_id' => $user->id,
            'portfolio_id' => $mainPortfolio->id,
            'market_asset_id' => $msci->id,
            'name' => 'MSCI World ETF',
            'ticker' => 'IWDA',
            'type' => 'etf',
            'quantity' => 50,
            'avg_buy_price' => 75.00,
            'current_price' => 88.50,
            'currency_code' => 'EUR',
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'portfolio_id' => $mainPortfolio->id,
            'asset_id' => $msciAsset->id,
            'type' => 'buy',
            'amount' => 3750.00, // 50 * 75
            'quantity' => 50,
            'price_per_unit' => 75.00,
            'date' => Carbon::now()->subMonths(8),
            'description' => 'Inversión recurrente ETF',
        ]);

        // 6. Create Expenses and Incomes (Cash Flow)
        
        // Categories need to exist first. Assuming CategorySeeder has run.
        $salaryCategory = Category::where('type', 'income')->where('name', 'Salario')->first() 
            ?? Category::create(['user_id' => null, 'name' => 'Salario', 'type' => 'income', 'color' => '#10B981']);

        $rentCategory = Category::where('type', 'expense')->where('name', 'Alquiler')->first() 
            ?? Category::create(['user_id' => null, 'name' => 'Alquiler', 'type' => 'expense', 'color' => '#EF4444']);
        
        $foodCategory = Category::where('type', 'expense')->where('name', 'Alimentación')->first() 
            ?? Category::create(['user_id' => null, 'name' => 'Alimentación', 'type' => 'expense', 'color' => '#F59E0B']);

        // Monthly Salary (last 3 months)
        for ($i = 0; $i < 3; $i++) {
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'income',
                'amount' => 2500.00,
                'date' => Carbon::now()->subMonths($i)->startOfMonth()->addDays(28),
                'category_id' => $salaryCategory->id,
                'description' => 'Nómina Mensual',
            ]);
        }

        // Monthly Rent
        for ($i = 0; $i < 3; $i++) {
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 800.00,
                'date' => Carbon::now()->subMonths($i)->startOfMonth()->addDays(5),
                'category_id' => $rentCategory->id,
                'description' => 'Alquiler Piso',
            ]);
        }

        // Daily Food Expenses (Random)
        for ($i = 0; $i < 10; $i++) {
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => rand(15, 60),
                'date' => Carbon::now()->subDays(rand(1, 30)),
                'category_id' => $foodCategory->id,
                'description' => 'Compra Supermercado',
            ]);
        }

        $this->command->info('Demo data seeded successfully for ' . $user->email);
    }

    private function createHistoricalPrices($marketAsset, $minPrice, $maxPrice)
    {
        // Generate prices for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            
            // Random walk logic for somewhat realistic charts
            $price = $minPrice + (rand(0, 100) / 100 * ($maxPrice - $minPrice));
            
            // Ensure unique constraint
            AssetPrice::updateOrCreate(
                ['market_asset_id' => $marketAsset->id, 'date' => $date],
                [
                    'price' => $price,
                    'volume' => rand(100000, 1000000),
                    'source' => 'seeder'
                ]
            );
        }
    }
}
