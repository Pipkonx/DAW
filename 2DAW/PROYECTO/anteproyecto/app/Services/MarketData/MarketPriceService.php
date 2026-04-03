<?php

namespace App\Services\MarketData;

use App\Models\MarketAsset;
use App\Models\Asset;
use App\Models\AssetPrice;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * MarketPriceService - Gestión centralizada de precios.
 * 
 * Este servicio se encarga de obtener, cachear y persistir los precios
 * de los activos, optimizando las llamadas a APIs externas.
 */
class MarketPriceService
{
    protected $stockService;
    protected $cryptoService;
    protected $bondService;
    protected $fundService;

    public function __construct(
        StockService $stockService,
        CryptoService $cryptoService,
        BondService $bondService,
        FundService $fundService
    ) {
        $this->stockService = $stockService;
        $this->cryptoService = $cryptoService;
        $this->bondService = $bondService;
        $this->fundService = $fundService;
    }

    /**
     * Obtiene el último precio disponible para un activo.
     * Prioriza la base de datos local (caché del día) antes de consultar la API.
     */
    public function getLatestPrice($assetOrMarketAsset)
    {
        $marketAsset = null;
        $userAsset = null;

        if ($assetOrMarketAsset instanceof Asset) {
            $userAsset = $assetOrMarketAsset;
            $marketAsset = $userAsset->marketAsset;
        } elseif ($assetOrMarketAsset instanceof MarketAsset) {
            $marketAsset = $assetOrMarketAsset;
        }

        if (!$marketAsset) return null;

        // 1. Verificar si tenemos un precio reciente en DB (hoy)
        $today = Carbon::today();
        $latestPrice = AssetPrice::where('market_asset_id', $marketAsset->id)
            ->where('date', $today)
            ->first();

        if ($latestPrice && $latestPrice->price > 0) {
            return $latestPrice->price;
        }

        // 2. Si no, consultar la API correspondiente
        $apiResult = $this->fetchPriceFromApi($marketAsset);
        
        $price = null;
        $date = $today;

        if (is_array($apiResult) && isset($apiResult['price'])) {
            $price = $apiResult['price'];
            if (isset($apiResult['date'])) {
                $date = $apiResult['date'];
            }
        } elseif (is_numeric($apiResult)) {
            $price = $apiResult;
        }

        // 3. Persistir el resultado si es válido
        if ($price) {
            $this->storePrice($marketAsset, $price, $date, $userAsset);
        }

        return $price ?? ($userAsset ? $userAsset->current_price : $marketAsset->current_price);
    }

    /**
     * Obtiene el precio histórico para una fecha específica.
     */
    public function getHistoricalPrice($marketAssetOrId, $date)
    {
        $marketAsset = $marketAssetOrId instanceof MarketAsset 
            ? $marketAssetOrId 
            : MarketAsset::find($marketAssetOrId);

        if (!$marketAsset) return null;

        // Consultar DB primero
        $existingPrice = AssetPrice::where('market_asset_id', $marketAsset->id)
            ->where('date', $date)
            ->first();

        if ($existingPrice) return $existingPrice->price;

        // Consultar API histórica
        $price = $this->fetchHistoricalPriceFromApi($marketAsset, $date);

        if ($price) {
            $this->storePrice($marketAsset, $price, $date);
        }

        return $price;
    }

    /**
     * Centraliza el guardado de precios en DB y actualización de modelos.
     */
    private function storePrice(MarketAsset $marketAsset, $price, $date, Asset $userAsset = null)
    {
        AssetPrice::updateOrCreate(
            ['market_asset_id' => $marketAsset->id, 'date' => $date],
            ['price' => $price, 'source' => 'api', 'volume' => 0]
        );

        $marketAsset->update(['current_price' => $price]);
        
        if ($userAsset) {
            $userAsset->update(['current_price' => $price]);
        }
    }

    /**
     * Delega la obtención del precio actual a la API correcta según el tipo.
     */
    private function fetchPriceFromApi(MarketAsset $marketAsset)
    {
        try {
            switch ($marketAsset->type) {
                case 'crypto':
                    $identifier = $marketAsset->api_id ?? strtolower($marketAsset->name);
                    return $this->cryptoService->getPrice($identifier);
                case 'bond':
                    $identifier = $marketAsset->isin ?? $marketAsset->ticker;
                    return $this->bondService->getPrice($identifier);
                case 'fund':
                    if (!$marketAsset->isin) return null;
                    return $this->fundService->getPrice($marketAsset->isin);
                case 'stock':
                case 'etf':
                default:
                    return $this->stockService->getPrice($marketAsset->ticker);
            }
        } catch (\Exception $e) {
            Log::error("Error de API (Precio) para {$marketAsset->ticker}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delega la obtención del precio histórico a la API correcta.
     */
    private function fetchHistoricalPriceFromApi(MarketAsset $marketAsset, $date)
    {
        try {
            switch ($marketAsset->type) {
                case 'crypto':
                    $identifier = strtolower($marketAsset->name);
                    return $this->cryptoService->getHistoricalPrice($identifier, $date);
                case 'bond':
                    $identifier = $marketAsset->isin ?? $marketAsset->ticker;
                    return $this->bondService->getHistoricalPrice($identifier, $date);
                case 'fund':
                    // El scraping histórico es limitado, devolvemos el actual si la fecha es reciente
                    if (Carbon::parse($date)->diffInDays(now()) <= 7) {
                         return $this->fundService->getPrice($marketAsset->isin);
                    }
                    return null;
                case 'stock':
                case 'etf':
                default:
                    return $this->stockService->getHistoricalPrice($marketAsset->ticker, $date);
            }
        } catch (\Exception $e) {
            Log::error("Error de API Histórica para {$marketAsset->ticker}: " . $e->getMessage());
            return null;
        }
    }
}
