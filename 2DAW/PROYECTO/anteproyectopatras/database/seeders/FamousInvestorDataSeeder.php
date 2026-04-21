<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamousInvestor;
use App\Models\FamousInvestorHolding;
use App\Models\FamousInvestorTrade;

class FamousInvestorDataSeeder extends Seeder
{
    public function run(): void
    {
        $gates = FamousInvestor::where('slug', 'bill-gates')->first();

        if ($gates) {
            // 1. Holdings Reales (Simulados con precisión 2024)
            $holdings = [
                ['symbol' => 'MSFT', 'name' => 'Microsoft Corp', 'shares_number' => 36477000, 'market_value' => 14667000000, 'weight' => 33.98],
                ['symbol' => 'BRK-B', 'name' => 'Berkshire Hathaway Inc.', 'shares_number' => 24622012, 'market_value' => 10267000000, 'weight' => 23.79],
                ['symbol' => 'WM', 'name' => 'Waste Management Inc.', 'shares_number' => 35234344, 'market_value' => 7480000000, 'weight' => 17.31],
                ['symbol' => 'CNI', 'name' => 'Canadian National Railway', 'shares_number' => 54826135, 'market_value' => 6520000000, 'weight' => 15.11],
                ['symbol' => 'DE', 'name' => 'Deere & Co.', 'shares_number' => 3923456, 'market_value' => 1570000000, 'weight' => 3.64],
            ];

            foreach ($holdings as $h) {
                FamousInvestorHolding::updateOrCreate(
                    ['famous_investor_id' => $gates->id, 'symbol' => $h['symbol']],
                    $h
                );
            }

            // 2. Transacciones Recientes (Historial Comercial)
            $trades = [
                ['symbol' => 'DE', 'name' => 'Deere & Co.', 'change_in_shares' => -320000, 'change_type' => 'REDUCED', 'filling_date' => '2024-11-14', 'percent_of_portfolio' => -0.25],
                ['symbol' => 'MSFT', 'name' => 'Microsoft Corp', 'change_in_shares' => -1000000, 'change_type' => 'REDUCED', 'filling_date' => '2024-11-14', 'percent_of_portfolio' => -0.85],
                ['symbol' => 'WM', 'name' => 'Waste Management Inc.', 'change_in_shares' => 0, 'change_type' => 'NO_CHANGE', 'filling_date' => '2024-11-14', 'percent_of_portfolio' => 0],
            ];

            foreach ($trades as $t) {
                FamousInvestorTrade::create(array_merge($t, ['famous_investor_id' => $gates->id]));
            }

            $gates->update(['last_synced_at' => now()]);
        }
    }
}
