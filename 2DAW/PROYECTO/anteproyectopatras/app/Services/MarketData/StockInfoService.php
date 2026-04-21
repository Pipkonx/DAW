<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Services\ApiService;

/**
 * StockInfoService - Gestor de metadatos de activos financieros.
 * 
 * Se encarga de la búsqueda de símbolos, perfiles de compañía, 
 * desglose de ETFs y estructuras institucionales.
 */
class StockInfoService
{
    private $apiKey;
    private $baseUrl = 'https://financialmodelingprep.com/api/v3';
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiKey = config('services.fmp.key') ?? env('FMP_API_KEY');
        $this->apiService = $apiService;
    }

    /**
     * Realiza una búsqueda global de activos financieros por nombre o ticker.
     */
    public function search(string $query): array
    {
        return Cache::remember("stock_search_{$query}", 86400, function () use ($query) {
            if (empty($this->apiKey)) return $this->getMockSearch($query);

            try {
                $response = Http::get("{$this->baseUrl}/search?query={$query}&limit=10&apikey={$this->apiKey}");
                if ($response->successful()) {
                    $this->apiService->trackRequest('FMP');
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error("Error buscando {$query}: " . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Obtiene el perfil detallado de la compañía (Logo, sector, descripción).
     */
    public function getProfile(string $symbol): array
    {
        $symbol = strtoupper(trim($symbol));
        return Cache::remember("stock_profile_{$symbol}", 2592000, function () use ($symbol) {
            if (empty($this->apiKey)) return $this->getMockProfile($symbol);

            try {
                $response = Http::get("{$this->baseUrl}/profile/{$symbol}?apikey={$this->apiKey}");
                if ($response->successful() && !empty($response->json())) {
                    $this->apiService->trackRequest('FMP');
                    $profile = $response->json()[0];
                    
                    // Enriquecimiento para ETFs (gastos operativos)
                    if (strpos(strtoupper($profile['description'] ?? ''), 'ETF') !== false || empty($profile['sector'])) {
                        $etfInfo = $this->getEtfInfo($symbol);
                        if ($etfInfo) $profile['expenseRatio'] = $etfInfo['expenseRatio'] ?? null;
                    }
                    return $profile;
                }
            } catch (\Exception $e) {
                Log::error("Error perfil {$symbol}: " . $e->getMessage());
            }
            return $this->getMockProfile($symbol);
        });
    }

    /**
     * Obtiene información financiera específica de ETFs.
     */
    public function getEtfInfo(string $symbol): ?array
    {
        return Cache::remember("etf_info_{$symbol}", 604800, function () use ($symbol) {
            return $this->fetchFromFMP("etf-info/{$symbol}");
        });
    }

    /**
     * Desglose sectorial de la cartera de un ETF.
     */
    public function getEtfSectorWeightings(string $symbol): array
    {
        return Cache::remember("etf_sectors_{$symbol}", 604800, function () use ($symbol) {
            return $this->fetchFromFMP("etf-sector-weightings/{$symbol}") ?: $this->getMockSectors();
        });
    }

    /**
     * Desglose geográfico (países) de la cartera de un ETF.
     */
    public function getEtfCountryWeightings(string $symbol): array
    {
        return Cache::remember("etf_countries_{$symbol}", 604800, function () use ($symbol) {
            return $this->fetchFromFMP("etf-country-weightings/{$symbol}") ?: [['country' => 'EE.UU.', 'weightPercentage' => 98.5]];
        });
    }

    /**
     * Obtiene la composición accionaria institucional de una empresa.
     */
    public function getInstitutionalHoldings(string $symbol): array
    {
        return Cache::remember("stock_institutional_{$symbol}", 604800, function () use ($symbol) {
            return $this->fetchFromFMP("institutional-holder/{$symbol}") ?: [];
        });
    }

    /**
     * Obtiene el historial de cambios en las tenencias institucionales.
     */
    public function getInstitutionalHoldingsHistory(string $symbol): array
    {
        return Cache::remember("stock_institutional_history_{$symbol}", 604800, function () use ($symbol) {
            return $this->fetchFromFMP("institutional-ownership-portfolio-composition-summary/{$symbol}") ?: [];
        });
    }

    // --- MÉTODOS DE SOPORTE E INFORMACIÓN INSTITUCIONAL ---

    private function fetchFromFMP(string $endpoint, array $params = []): ?array
    {
        if (empty($this->apiKey)) return null;
        try {
            $response = Http::get("{$this->baseUrl}/{$endpoint}", array_merge($params, ['apikey' => $this->apiKey]));
            if ($response->successful()) {
                $this->apiService->trackRequest('FMP');
                return $response->json();
            }
        } catch (\Exception $e) { Log::error("FMP API Exception @ {$endpoint}: " . $e->getMessage()); }
        return null;
    }

    private function getMockProfile($symbol)
    {
        return [
            'symbol' => $symbol, 'companyName' => "$symbol Inc.",
            'image' => "https://financialmodelingprep.com/image-stock/{$symbol}.png",
            'description' => "Analítica y gestión de datos financieros para el símbolo $symbol.",
            'sector' => 'Tecnología', 'industry' => 'Software de Consumo',
            'website' => 'https://pipkonx.app', 'mktCap' => 500000000000,
            'expenseRatio' => 0.0009
        ];
    }

    private function getMockSearch($q)
    {
        return [['symbol' => 'AAPL', 'name' => 'Apple Inc.'], ['symbol' => 'MSFT', 'name' => 'Microsoft Corp.']];
    }

    private function getMockSectors()
    {
        return [['sector' => 'Tecnología', 'weightPercentage' => 45.2], ['sector' => 'Finanzas', 'weightPercentage' => 20.8]];
    }
}
