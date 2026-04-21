<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FamousInvestor;
use App\Models\FamousInvestorHolding;
use App\Models\FamousInvestorTrade;

class FamousInvestorsSeeder extends Seeder
{
    public function run()
    {
        $investors = [
            [
                'name' => 'Cathie Wood',
                'slug' => 'cathie-wood',
                'cik' => '0001605941',
                'avatar' => 'https://unavatar.io/twitter/ARKInvest',
                'location' => 'Florida, USA',
                'description' => 'Fundadora de ARK Invest. Especialista en innovación disruptiva y tecnologías del futuro.',
                'holdings' => [
                    ['symbol' => 'TSLA', 'name' => 'Tesla, Inc.', 'weight' => 12.5, 'shares' => 4500000, 'value' => 780000000],
                    ['symbol' => 'ROKU', 'name' => 'Roku, Inc.', 'weight' => 8.2, 'shares' => 8200000, 'value' => 512000000],
                    ['symbol' => 'COIN', 'name' => 'Coinbase Global', 'weight' => 7.8, 'shares' => 3100000, 'value' => 486000000],
                    ['symbol' => 'TDOC', 'name' => 'Teladoc Health', 'weight' => 4.5, 'shares' => 12000000, 'value' => 280000000],
                    ['symbol' => 'ZM', 'name' => 'Zoom Video', 'weight' => 4.2, 'shares' => 5500000, 'value' => 260000000],
                    ['symbol' => 'SQ', 'name' => 'Block, Inc.', 'weight' => 4.1, 'shares' => 3800000, 'value' => 255000000],
                    ['symbol' => 'PATH', 'name' => 'UiPath Inc.', 'weight' => 3.9, 'shares' => 15000000, 'value' => 240000000],
                    ['symbol' => 'CRSP', 'name' => 'CRISPR Therapeutics', 'weight' => 3.5, 'shares' => 4200000, 'value' => 215000000],
                    ['symbol' => 'PLTR', 'name' => 'Palantir Tech', 'weight' => 3.2, 'shares' => 8500000, 'value' => 195000000],
                    ['symbol' => 'DNA', 'name' => 'Ginkgo Bioworks', 'weight' => 2.8, 'shares' => 120000000, 'value' => 170000000],
                    ['symbol' => 'EXAS', 'name' => 'Exact Sciences', 'weight' => 2.5, 'shares' => 4800000, 'value' => 155000000],
                    ['symbol' => 'BEAM', 'name' => 'Beam Therapeutics', 'weight' => 2.2, 'shares' => 6500000, 'value' => 135000000],
                    ['symbol' => 'NVDA', 'name' => 'NVIDIA Corp.', 'weight' => 1.8, 'shares' => 120000, 'value' => 110000000],
                ],
                'trades' => [
                    ['symbol' => 'TSLA', 'type' => 'REDUCED', 'shares' => -250000, 'date' => '2024-04-10'],
                    ['symbol' => 'COIN', 'type' => 'INCREASED', 'shares' => 180000, 'date' => '2024-04-05'],
                    ['symbol' => 'NVDA', 'type' => 'SOLD', 'shares' => -50000, 'date' => '2024-03-28'],
                    ['symbol' => 'ZM', 'type' => 'INCREASED', 'shares' => 1200000, 'date' => '2024-03-15'],
                    ['symbol' => 'RDDT', 'type' => 'NEW', 'shares' => 150000, 'date' => '2024-03-10'],
                ]
            ],
            [
                'name' => 'Warren Buffett',
                'slug' => 'warren-buffett',
                'cik' => '0001067983',
                'avatar' => 'https://unavatar.io/twitter/WarrenBuffett',
                'location' => 'Omaha, USA',
                'description' => 'CEO de Berkshire Hathaway. El inversor de valor más exitoso de la historia.',
                'holdings' => [
                    ['symbol' => 'AAPL', 'name' => 'Apple Inc.', 'weight' => 45.1, 'shares' => 915000000, 'value' => 165000000000],
                    ['symbol' => 'BAC', 'name' => 'Bank of America', 'weight' => 10.5, 'shares' => 1030000000, 'value' => 41000000000],
                    ['symbol' => 'AXP', 'name' => 'American Express', 'weight' => 8.2, 'shares' => 151000000, 'value' => 32000000000],
                    ['symbol' => 'KO', 'name' => 'Coca-Cola Co.', 'weight' => 6.5, 'shares' => 400000000, 'value' => 24000000000],
                    ['symbol' => 'CVX', 'name' => 'Chevron Corp.', 'weight' => 5.8, 'shares' => 126000000, 'value' => 21000000000],
                    ['symbol' => 'OXY', 'name' => 'Occidental Petrol.', 'weight' => 4.2, 'shares' => 248000000, 'value' => 15000000000],
                    ['symbol' => 'KHC', 'name' => 'Kraft Heinz Co.', 'weight' => 3.5, 'shares' => 325000000, 'value' => 12000000000],
                    ['symbol' => 'MCO', 'name' => 'Moody\'s Corp.', 'weight' => 2.8, 'shares' => 24000000, 'value' => 9500000000],
                    ['symbol' => 'AMZN', 'name' => 'Amazon.com', 'weight' => 0.5, 'shares' => 10000000, 'value' => 1800000000],
                    ['symbol' => 'V', 'name' => 'Visa Inc.', 'weight' => 0.2, 'shares' => 8000000, 'value' => 2200000000],
                    ['symbol' => 'MA', 'name' => 'Mastercard Inc.', 'weight' => 0.1, 'shares' => 3000000, 'value' => 1400000000],
                ],
                'trades' => [
                    ['symbol' => 'AAPL', 'type' => 'REDUCED', 'shares' => -10000000, 'date' => '2024-03-31'],
                    ['symbol' => 'CVX', 'type' => 'INCREASED', 'shares' => 16000000, 'date' => '2024-02-14'],
                    ['symbol' => 'HPQ', 'type' => 'SOLD', 'shares' => -102000000, 'date' => '2023-12-15'],
                    ['symbol' => 'DVA', 'type' => 'INCREASED', 'shares' => 1200000, 'date' => '2024-01-20'],
                ]
            ],
            [
                'name' => 'Michael Burry',
                'slug' => 'michael-burry',
                'cik' => '0001649339',
                'avatar' => 'https://unavatar.io/twitter/michaeljburry',
                'location' => 'California, USA',
                'description' => 'Fundador de Scion Asset Management. Famoso por predecir la crisis subprime de 2008.',
                'holdings' => [
                    ['symbol' => 'BABA', 'name' => 'Alibaba Group', 'weight' => 15.2, 'shares' => 1250000, 'value' => 95000000],
                    ['symbol' => 'JD', 'name' => 'JD.com, Inc.', 'weight' => 12.8, 'shares' => 2100000, 'value' => 80000000],
                    ['symbol' => 'HCA', 'name' => 'HCA Healthcare', 'weight' => 10.5, 'shares' => 280000, 'value' => 75000000],
                    ['symbol' => 'ORCL', 'name' => 'Oracle Corp.', 'weight' => 8.2, 'shares' => 620000, 'value' => 65000000],
                    ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc.', 'weight' => 7.5, 'shares' => 450000, 'value' => 60000000],
                    ['symbol' => 'CVS', 'name' => 'CVS Health', 'weight' => 6.2, 'shares' => 780000, 'value' => 55000000],
                    ['symbol' => 'MGM', 'name' => 'MGM Resorts', 'weight' => 4.5, 'shares' => 1100000, 'value' => 48000000],
                    ['symbol' => 'BKNG', 'name' => 'Booking Holdings', 'weight' => 3.8, 'shares' => 12000, 'value' => 42000000],
                ],
                'trades' => [
                    ['symbol' => 'BABA', 'type' => 'NEW', 'shares' => 1250000, 'date' => '2024-02-10'],
                    ['symbol' => 'JD', 'type' => 'INCREASED', 'shares' => 800000, 'date' => '2024-03-01'],
                    ['symbol' => 'SPY', 'type' => 'SOLD (PUTS)', 'shares' => -10000000, 'date' => '2023-11-30'],
                    ['symbol' => 'TLT', 'type' => 'SOLD (PUTS)', 'shares' => -5000000, 'date' => '2024-01-15'],
                ]
            ],
            [
                'name' => 'Bill Ackman',
                'slug' => 'bill-ackman',
                'cik' => '0001336528',
                'avatar' => 'https://unavatar.io/twitter/BillAckman',
                'location' => 'New York, USA',
                'description' => 'CEO de Pershing Square. Inversor activista enfocado en valor a largo plazo.',
                'holdings' => [
                    ['symbol' => 'CMG', 'name' => 'Chipotle Mexican Grill', 'weight' => 18.5, 'shares' => 1100000, 'value' => 2400000000],
                    ['symbol' => 'HLT', 'name' => 'Hilton Worldwide', 'weight' => 16.2, 'shares' => 13500000, 'value' => 2100000000],
                    ['symbol' => 'GOOG', 'name' => 'Alphabet Inc.', 'weight' => 14.8, 'shares' => 12000000, 'value' => 1900000000],
                    ['symbol' => 'QSR', 'name' => 'Restaurant Brands', 'weight' => 13.5, 'shares' => 24000000, 'value' => 1800000000],
                    ['symbol' => 'LOW', 'name' => 'Lowe\'s Companies', 'weight' => 11.2, 'shares' => 7500000, 'value' => 1650000000],
                    ['symbol' => 'HHC', 'name' => 'Howard Hughes Corp', 'weight' => 9.5, 'shares' => 16000000, 'value' => 1200000000],
                    ['symbol' => 'GOOGL', 'name' => 'Alphabet Inc. (A)', 'weight' => 8.2, 'shares' => 6500000, 'value' => 1050000000],
                    ['symbol' => 'CP', 'name' => 'Canadian Pacific', 'weight' => 7.8, 'shares' => 15000000, 'value' => 950000000],
                ],
                'trades' => [
                    ['symbol' => 'NKE', 'type' => 'NEW', 'shares' => 3000000, 'date' => '2024-04-01'],
                    ['symbol' => 'GOOG', 'type' => 'INCREASED', 'shares' => 2500000, 'date' => '2024-02-15'],
                    ['symbol' => 'LOW', 'type' => 'REDUCED', 'shares' => -1200000, 'date' => '2024-01-20'],
                ]
            ],
            [
                'name' => 'Bill Gates',
                'slug' => 'bill-gates',
                'cik' => '0001166559',
                'avatar' => 'https://unavatar.io/twitter/BillGates',
                'location' => 'Washington, USA',
                'description' => 'Gestión de activos del Gates Foundation Trust. Enfoque estratégico en dividendos y estabilidad.',
                'holdings' => [
                    ['symbol' => 'MSFT', 'name' => 'Microsoft (Trust)', 'weight' => 34.2, 'shares' => 38000000, 'value' => 15000000000],
                    ['symbol' => 'WM', 'name' => 'Waste Management', 'weight' => 16.5, 'shares' => 35000000, 'value' => 7200000000],
                    ['symbol' => 'BRK.B', 'name' => 'Berkshire Hathaway', 'weight' => 14.2, 'shares' => 19000000, 'value' => 6100000000],
                    ['symbol' => 'CNI', 'name' => 'Canadian National', 'weight' => 8.5, 'shares' => 54000000, 'value' => 3800000000],
                    ['symbol' => 'CAT', 'name' => 'Caterpillar Inc.', 'weight' => 5.2, 'shares' => 7200000, 'value' => 2400000000],
                    ['symbol' => 'DE', 'name' => 'Deere & Co.', 'weight' => 4.8, 'shares' => 3800000, 'value' => 2100000000],
                    ['symbol' => 'ECL', 'name' => 'Ecolab Inc.', 'weight' => 3.9, 'shares' => 5200000, 'value' => 1800000000],
                    ['symbol' => 'CP', 'name' => 'Canadian Pacific', 'weight' => 2.5, 'shares' => 4500000, 'value' => 1200000000],
                    ['symbol' => 'UPS', 'name' => 'United Parcel Serv.', 'weight' => 1.8, 'shares' => 2500000, 'value' => 850000000],
                    ['symbol' => 'FDX', 'name' => 'FedEx Corp.', 'weight' => 1.2, 'shares' => 1100000, 'value' => 450000000],
                ],
                'trades' => [
                    ['symbol' => 'MSFT', 'type' => 'REDUCED', 'shares' => -1200000, 'date' => '2024-03-31'],
                    ['symbol' => 'DE', 'type' => 'REDUCED', 'shares' => -450000, 'date' => '2024-02-12'],
                    ['symbol' => 'CAT', 'type' => 'REDUCED', 'shares' => -800000, 'date' => '2024-01-25'],
                ]
            ]
        ];

        foreach ($investors as $iData) {
            $investor = FamousInvestor::updateOrCreate(
                ['slug' => $iData['slug']],
                [
                    'name' => $iData['name'],
                    'cik' => $iData['cik'],
                    'avatar' => $iData['avatar'],
                    'location' => $iData['location'],
                    'description' => $iData['description'],
                    'last_synced_at' => now(),
                ]
            );

            // Poblar Holdings (Desglose Completo)
            foreach ($iData['holdings'] as $hData) {
                FamousInvestorHolding::updateOrCreate(
                    ['famous_investor_id' => $investor->id, 'symbol' => $hData['symbol']],
                    [
                        'name' => $hData['name'],
                        'weight' => $hData['weight'],
                        'shares_number' => $hData['shares'],
                        'market_value' => $hData['value'],
                    ]
                );
            }

            // Poblar Historial Denso (Trades)
            foreach ($iData['trades'] as $tData) {
                FamousInvestorTrade::updateOrCreate(
                    [
                        'famous_investor_id' => $investor->id, 
                        'symbol' => $tData['symbol'], 
                        'filling_date' => $tData['date']
                    ],
                    [
                        'change_type' => $tData['type'],
                        'change_in_shares' => $tData['shares'],
                        'percent_of_portfolio' => rand(1, 40) / 10,
                    ]
                );
            }
        }
    }
}
