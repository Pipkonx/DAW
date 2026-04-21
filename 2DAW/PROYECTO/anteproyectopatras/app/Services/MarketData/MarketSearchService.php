<?php

namespace App\Services\MarketData;

use App\Models\MarketAsset;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;

/**
 * MarketSearchService - Descubrimiento y sincronización de activos.
 * 
 * Gestiona las búsquedas federadas y asegura que los activos encontrados
 * se vinculen correctamente con el sistema local (MarketAsset).
 */
class MarketSearchService
{
    protected $stockService;
    protected $cryptoService;
    protected $fundService;

    public function __construct(
        StockService $stockService,
        CryptoService $cryptoService,
        FundService $fundService
    ) {
        $this->stockService = $stockService;
        $this->cryptoService = $cryptoService;
        $this->fundService = $fundService;
    }

    /**
     * Motor de búsqueda federado (Local + Global APIs).
     */
    public function search($query, $type = null)
    {
        // 1. Búsquedaen base de datos local
        $localQuery = MarketAsset::where(function ($q) use ($query) {
            $q->where('ticker', 'LIKE', "%{$query}%")
              ->orWhere('name', 'LIKE', "%{$query}%")
              ->orWhere('isin', 'LIKE', "%{$query}%");
        });
        
        if ($type) $localQuery->where('type', $type);

        $results = collect($localQuery->limit(10)->get()->map(fn($asset) => [
            'id' => $asset->id,
            'ticker' => $asset->ticker,
            'name' => $asset->name,
            'type' => $asset->type,
            'currency' => $asset->currency_code,
            'source' => 'local',
            'isin' => $asset->isin,
            'api_id' => $asset->api_id,
        ])->toArray());

        // 2. Búsqueda en APIs externas (si la consulta es específica)
        if (strlen($query) >= 3) {
            try {
                // Suplementar con Fondos/ISIN si aplica
                if ($type === 'fund' || preg_match('/^[A-Z]{2}[A-Z0-9]{9}\d$/', $query)) {
                    $fundResult = $this->fundService->searchByName($query);
                    if ($fundResult && !$results->contains('isin', $fundResult['isin'])) {
                        $results->push([
                            'id' => null,
                            'ticker' => $fundResult['isin'], 
                            'name' => $fundResult['name'],
                            'type' => 'fund',
                            'currency_code' => 'EUR',
                            'source' => 'api',
                            'isin' => $fundResult['isin'],
                        ]);
                    }
                }

                $apiResults = $this->searchExternalApis($query, $type);
                
                foreach ($apiResults as $apiItem) {
                    $exists = $results->contains(fn($item) => $item['ticker'] === $apiItem['symbol']);
                    if (!$exists) {
                        $results->push([
                            'id' => null,
                            'ticker' => $apiItem['symbol'],
                            'name' => $apiItem['name'],
                            'type' => $apiItem['type'],
                            'currency' => $apiItem['currency'] ?? 'USD',
                            'source' => 'api',
                            'api_id' => $apiItem['api_id'] ?? null,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error en búsqueda externa: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Sincroniza o crea un activo de mercado en DB local.
     */
    public function syncAsset($ticker, $type, $name, $currency = 'USD', $isin = null, $apiId = null)
    {
        $query = MarketAsset::where('type', $type);
        
        if ($isin) {
             $query->where(fn($q) => $q->where('ticker', $ticker)->orWhere('isin', $isin));
        } elseif ($apiId) {
             $query->where('api_id', $apiId);
        } else {
             $query->where('ticker', $ticker);
        }
        
        $marketAsset = $query->first();

        if (!$marketAsset) {
            $marketAsset = MarketAsset::create([
                'ticker' => strtoupper($ticker),
                'api_id' => $apiId,
                'name' => $name,
                'type' => $type,
                'currency_code' => $currency,
                'isin' => $isin,
            ]);
        } else {
             $updates = [];
             if ($isin && !$marketAsset->isin) $updates['isin'] = $isin;
             if ($apiId && !$marketAsset->api_id) $updates['api_id'] = $apiId;
             if (!empty($updates)) $marketAsset->update($updates);
        }
        
        return $marketAsset;
    }

    /**
     * Asegura que un activo esté sincronizado buscando en múltiples fuentes por Ticker.
     */
    public function ensureAssetSynced($ticker)
    {
        $ticker = strtoupper(trim($ticker));
        $marketAsset = MarketAsset::where('ticker', $ticker)->orWhere('isin', $ticker)->first();
        if ($marketAsset) return $marketAsset;

        // Intentar FMP (Stock/ETF)
        $profile = $this->stockService->getProfile($ticker);
        if ($profile && isset($profile['symbol'])) {
            return $this->syncAsset(
                $profile['symbol'], 
                (strpos(strtoupper($profile['companyName'] ?? ''), 'ETF') !== false) ? 'etf' : 'stock',
                $profile['companyName'] ?? $ticker,
                $profile['currency'] ?? 'USD'
            );
        }

        // Intentar Crypto
        $cryptoResults = $this->cryptoService->search($ticker);
        foreach ($cryptoResults as $res) {
            if (strtoupper($res['symbol']) === $ticker) {
                return $this->syncAsset($res['symbol'], 'crypto', $res['name'], 'USD', null, $res['id']);
            }
        }

        return null;
    }

    /**
     * Lógica de agregación cruda de APIs externas.
     */
    private function searchExternalApis($query, $type = null)
    {
        $results = [];

        // Stocks/ETFs (FMP)
        if (!$type || in_array($type, ['stock', 'etf'])) {
            try {
                $stocks = $this->stockService->search($query);
                foreach ($stocks as $stock) {
                    $results[] = [
                        'symbol' => $stock['symbol'], 'name' => $stock['name'],
                        'type' => 'stock', 'currency' => $stock['currency'],
                        'exchange' => $stock['stockExchange'],
                    ];
                }
            } catch (\Exception $e) { Log::error("Error búsqueda Stock: " . $e->getMessage()); }
        }

        // Cripto (CoinGecko)
        if (!$type || $type === 'crypto') {
            try {
                $cryptos = $this->cryptoService->search($query);
                foreach ($cryptos as $crypto) {
                    $results[] = [
                        'symbol' => strtoupper($crypto['symbol']), 'name' => $crypto['name'],
                        'type' => 'crypto', 'currency' => 'USD', 'api_id' => $crypto['id'],
                    ];
                }
            } catch (\Exception $e) { Log::error("Error búsqueda Cripto: " . $e->getMessage()); }
        }

        return $results;
    }
}
