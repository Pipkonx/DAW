<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MarketAsset;

class MarketAssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assets = [
            // Crypto
            ['ticker' => 'BTC', 'name' => 'Bitcoin', 'type' => 'crypto', 'isin' => null, 'sector' => 'Currency', 'logo_url' => 'https://cryptologos.cc/logos/bitcoin-btc-logo.png'],
            ['ticker' => 'ETH', 'name' => 'Ethereum', 'type' => 'crypto', 'isin' => null, 'sector' => 'Smart Contracts', 'logo_url' => 'https://cryptologos.cc/logos/ethereum-eth-logo.png'],
            ['ticker' => 'SOL', 'name' => 'Solana', 'type' => 'crypto', 'isin' => null, 'sector' => 'Smart Contracts', 'logo_url' => 'https://cryptologos.cc/logos/solana-sol-logo.png'],
            ['ticker' => 'USDT', 'name' => 'Tether', 'type' => 'crypto', 'isin' => null, 'sector' => 'Stablecoin', 'logo_url' => 'https://cryptologos.cc/logos/tether-usdt-logo.png'],

            // Stocks (Tech)
            ['ticker' => 'AAPL', 'name' => 'Apple Inc.', 'type' => 'stock', 'isin' => 'US0378331005', 'sector' => 'Technology', 'logo_url' => 'https://logo.clearbit.com/apple.com'],
            ['ticker' => 'MSFT', 'name' => 'Microsoft Corporation', 'type' => 'stock', 'isin' => 'US5949181045', 'sector' => 'Technology', 'logo_url' => 'https://logo.clearbit.com/microsoft.com'],
            ['ticker' => 'NVDA', 'name' => 'NVIDIA Corporation', 'type' => 'stock', 'isin' => 'US67066G1040', 'sector' => 'Semiconductors', 'logo_url' => 'https://logo.clearbit.com/nvidia.com'],
            ['ticker' => 'TSLA', 'name' => 'Tesla Inc.', 'type' => 'stock', 'isin' => 'US88160R1014', 'sector' => 'Automotive', 'logo_url' => 'https://logo.clearbit.com/tesla.com'],

            // Stocks (Spain)
            ['ticker' => 'ITX', 'name' => 'Inditex', 'type' => 'stock', 'isin' => 'ES0148396007', 'sector' => 'Retail', 'logo_url' => 'https://logo.clearbit.com/inditex.com'],
            ['ticker' => 'SAN', 'name' => 'Banco Santander', 'type' => 'stock', 'isin' => 'ES0113900J37', 'sector' => 'Financial Services', 'logo_url' => 'https://logo.clearbit.com/santander.com'],
            ['ticker' => 'BBVA', 'name' => 'Banco Bilbao Vizcaya Argentaria', 'type' => 'stock', 'isin' => 'ES0113211835', 'sector' => 'Financial Services', 'logo_url' => 'https://logo.clearbit.com/bbva.com'],

            // ETFs
            ['ticker' => 'SPY', 'name' => 'SPDR S&P 500 ETF Trust', 'type' => 'etf', 'isin' => 'US78462F1030', 'sector' => 'Broad Market', 'logo_url' => 'https://logo.clearbit.com/ssga.com'],
            ['ticker' => 'VUSA', 'name' => 'Vanguard S&P 500 UCITS ETF', 'type' => 'etf', 'isin' => 'IE00B3XXRP09', 'sector' => 'Broad Market', 'logo_url' => 'https://logo.clearbit.com/vanguard.com'],
            ['ticker' => 'QQQ', 'name' => 'Invesco QQQ Trust', 'type' => 'etf', 'isin' => 'US46090E1038', 'sector' => 'Technology', 'logo_url' => 'https://logo.clearbit.com/invesco.com'],
            ['ticker' => 'VWCE', 'name' => 'Vanguard FTSE All-World UCITS ETF', 'type' => 'etf', 'isin' => 'IE00BK5BQT80', 'sector' => 'Global Market', 'logo_url' => 'https://logo.clearbit.com/vanguard.com'],

            // Commodities
            ['ticker' => 'GOLD', 'name' => 'Gold Spot', 'type' => 'other', 'isin' => null, 'sector' => 'Precious Metals', 'logo_url' => null],
        ];

        foreach ($assets as $asset) {
            MarketAsset::updateOrCreate(
                ['ticker' => $asset['ticker']],
                $asset
            );
        }
    }
}
