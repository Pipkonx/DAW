<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FamousInvestor;
use App\Models\FamousInvestorHolding;
use App\Models\FamousInvestorTrade;
use App\Services\MarketDataService;
use Illuminate\Support\Facades\Log;

class SyncFamousPortfolios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'famous-portfolios:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza los datos 13F (SEC) de inversores famosos desde FMP';

    protected $marketDataService;

    public function __construct(MarketDataService $marketDataService)
    {
        parent::__construct();
        $this->marketDataService = $marketDataService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando sincronización de carteras de famosos...');
        $investors = FamousInvestor::all();
        $stockService = $this->marketDataService->getStockService();

        foreach ($investors as $investor) {
            $this->info("Sincronizando: {$investor->name} (CIK: {$investor->cik})");
            
            try {
                // Probar CIK original y sin ceros
                $cikToTry = [ $investor->cik, ltrim($investor->cik, '0') ];
                $holdings = [];
                
                foreach($cikToTry as $cik) {
                    $this->comment("Probando CIK: $cik...");
                    $holdings = $stockService->getInstitutionalHoldings($cik);
                    if(!empty($holdings)) break;
                }
                
                if (!empty($holdings)) {
                    $this->info("Holdings encontrados: " . count($holdings));
                    // Limpiar posiciones anteriores
                    $investor->holdings()->delete();
                    
                    foreach ($holdings as $item) {
                        FamousInvestorHolding::create([
                            'famous_investor_id' => $investor->id,
                            'symbol' => $item['symbol'] ?? 'N/A',
                            'name' => $item['name'] ?? 'Unknown',
                            'shares_number' => $item['sharesNumber'] ?? 0,
                            'market_value' => $item['marketValue'] ?? 0,
                            'weight' => $item['weight'] ?? 0,
                        ]);
                    }
                } else {
                    $this->warn("No se encontraron holdings para {$investor->name} tras probar ambos formatos de CIK.");
                }

                // 2. Obtener Historial de Transacciones (Trades)
                $history = [];
                foreach($cikToTry as $cik) {
                    $history = $stockService->getInstitutionalHoldingsHistory($cik);
                    if(!empty($history)) break;
                }
                
                if (!empty($history)) {
                    // Limpiar historial previo
                    $investor->trades()->delete();
                    
                    // Tomar las 50 más recientes
                    $recentHistory = array_slice($history, 0, 50);
                    
                    foreach ($recentHistory as $trade) {
                        FamousInvestorTrade::create([
                            'famous_investor_id' => $investor->id,
                            'symbol' => $trade['symbol'] ?? 'N/A',
                            'name' => $trade['name'] ?? null,
                            'change_in_shares' => $trade['changeInShares'] ?? 0,
                            'change_type' => $trade['changeType'] ?? 'N/A',
                            'filling_date' => $trade['fillingDate'] ?? now()->toDateString(),
                            'percent_of_portfolio' => $trade['percentOfPortfolio'] ?? 0,
                        ]);
                    }
                }

                // Actualizar timestamp de sincronización
                $investor->update(['last_synced_at' => now()]);
                $this->info("Sincronización completada para {$investor->name}");

            } catch (\Exception $e) {
                $this->error("Error sincronizando {$investor->name}: " . $e->getMessage());
                Log::error("SyncFamousPortfolios error for {$investor->cik}: " . $e->getMessage());
            }
        }

        $this->info('Sincronización global finalizada.');
    }
}
