<?php

namespace App\Services;

use App\Services\MarketData\StockService;
use App\Services\MarketData\CryptoService;
use App\Services\MarketData\BondService;
use App\Services\MarketData\FundService;
use App\Services\MarketData\MarketPriceService;
use App\Services\MarketData\MarketSearchService;
use App\Services\MarketData\MarketProfileService;
use App\Models\MarketAsset;
use App\Models\Asset;
use Illuminate\Support\Facades\Log;

/**
 * MarketDataService - Facade principal de datos de mercado.
 * 
 * Este servicio centraliza el acceso a datos financieros de múltiples fuentes
 * delegando la lógica pesada en sub-servicios especializados por responsabilidad.
 */
class MarketDataService
{
    protected $stockService;
    protected $cryptoService;
    protected $bondService;
    protected $fundService;
    protected $priceService;
    protected $searchService;
    protected $profileService;

    public function __construct(
        StockService $stockService,
        CryptoService $cryptoService,
        BondService $bondService,
        FundService $fundService,
        MarketPriceService $priceService,
        MarketSearchService $searchService,
        MarketProfileService $profileService
    ) {
        $this->stockService = $stockService;
        $this->cryptoService = $cryptoService;
        $this->bondService = $bondService;
        $this->fundService = $fundService;
        $this->priceService = $priceService;
        $this->searchService = $searchService;
        $this->profileService = $profileService;
    }

    /* -------------------------------------------------------------------------- */
    /* GESTIÓN DE PRECIOS (Delega en MarketPriceService)                          */
    /* -------------------------------------------------------------------------- */

    /**
     * Obtiene el precio más reciente (o de hoy) de un activo.
     */
    public function getLatestPrice($assetOrMarketAsset)
    {
        return $this->priceService->getLatestPrice($assetOrMarketAsset);
    }

    /**
     * Obtiene el precio de cierre para una fecha histórica específica.
     */
    public function getHistoricalPrice($marketAssetOrId, $date)
    {
        return $this->priceService->getHistoricalPrice($marketAssetOrId, $date);
    }

    /**
     * Obtiene datos para gráficos (histórico filtrado por días).
     */
    public function getChartData($marketAssetOrTicker, $days = 365)
    {
        $marketAsset = $marketAssetOrTicker instanceof MarketAsset 
            ? $marketAssetOrTicker 
            : MarketAsset::where('ticker', $marketAssetOrTicker)->first();

        if (!$marketAsset) return [];

        try {
            if ($marketAsset->type === 'crypto') {
                $identifier = $marketAsset->api_id ?? strtolower($marketAsset->name);
                return $this->cryptoService->getChartData($identifier, $days);
            }
            return $this->stockService->getHistoricalData($marketAsset->ticker, $days);
        } catch (\Exception $e) {
            Log::error("Error en datos de gráfico ({$marketAsset->ticker}): " . $e->getMessage());
            return [];
        }
    }

    /* -------------------------------------------------------------------------- */
    /* BÚSQUEDA Y SINCRONIZACIÓN (Delega en MarketSearchService)                  */
    /* -------------------------------------------------------------------------- */

    /**
     * Motor de búsqueda federado que agrega resultados de múltiples APIs.
     */
    public function search($query, $type = null)
    {
        return $this->searchService->search($query, $type);
    }

    /**
     * Sincroniza o crea un activo de mercado en el sistema local.
     */
    public function syncAsset($ticker, $type, $name, $currency = 'USD', $isin = null, $apiId = null)
    {
        return $this->searchService->syncAsset($ticker, $type, $name, $currency, $isin, $apiId);
    }

    /**
     * Asegura la sincronización de un activo a partir de su Ticker.
     */
    public function ensureAssetSynced($ticker)
    {
        return $this->searchService->ensureAssetSynced($ticker);
    }

    /* -------------------------------------------------------------------------- */
    /* PERFILES Y METADATOS (Delega en MarketProfileService)                      */
    /* -------------------------------------------------------------------------- */

    /**
     * Obtiene información detallada (descripción, mcap, pesos de ETF, etc).
     */
    public function getAssetProfile(MarketAsset $marketAsset)
    {
        return $this->profileService->getAssetProfile($marketAsset);
    }

    /* -------------------------------------------------------------------------- */
    /* DATOS DE MERCADO GLOBALES (Atajos a servicios de bajo nivel)               */
    /* -------------------------------------------------------------------------- */

    public function getStockGainers() { 
        return array_map([$this->profileService, 'enrichStockLogo'], $this->stockService->getTopGainers());
    }
    
    public function getStockLosers() { 
        return array_map([$this->profileService, 'enrichStockLogo'], $this->stockService->getTopLosers());
    }
    
    public function getStockActive() { 
        return array_map([$this->profileService, 'enrichStockLogo'], $this->stockService->getMostActive());
    }

    public function getCryptoTrending() { return $this->cryptoService->getTrendingCoins(); }
    public function getCryptoTop($limit = 10) { return $this->cryptoService->getTopByMarketCap($limit); }

    /**
     * Permite acceder al StockService directamente para operaciones complejas.
     */
    public function getStockService() { return $this->stockService; }
}
