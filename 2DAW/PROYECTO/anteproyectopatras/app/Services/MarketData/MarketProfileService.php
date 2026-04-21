<?php

namespace App\Services\MarketData;

use App\Models\MarketAsset;
use Illuminate\Support\Facades\Log;

/**
 * MarketProfileService - Gestión de metadatos y perfiles.
 * 
 * Este servicio extrae información cualitativa de los activos, como 
 * descripciones, sectores y la composición interna de los ETFs.
 */
class MarketProfileService
{
    protected $stockService;
    protected $cryptoService;

    public function __construct(
        StockService $stockService,
        CryptoService $cryptoService
    ) {
        $this->stockService = $stockService;
        $this->cryptoService = $cryptoService;
    }

    /**
     * Obtiene el perfil completo de un activo (sector, descripción, etc).
     */
    public function getAssetProfile(MarketAsset $marketAsset)
    {
        try {
            switch ($marketAsset->type) {
                case 'crypto':
                    return [
                        'description' => "Criptomoneda: {$marketAsset->name} ({$marketAsset->ticker})",
                        'sector' => 'Criptoactivos',
                        'industry' => 'Blockchain',
                        'mcap' => 0,
                    ];
                case 'stock':
                case 'etf':
                case 'fund':
                    $profile = $this->stockService->getProfile($marketAsset->ticker);
                    $data = [
                        'description' => $profile['description'] ?? 'Sin descripción disponible.',
                        'sector' => $profile['sector'] ?? 'Otros',
                        'industry' => $profile['industry'] ?? 'General',
                        'mcap' => $profile['mktCap'] ?? 0,
                        'market_cap' => $profile['mktCap'] ?? 0,
                        'image' => $profile['image'] ?? "https://financialmodelingprep.com/image-stock/{$marketAsset->ticker}.png",
                        'companyName' => $profile['companyName'] ?? $marketAsset->name,
                        'ter' => $profile['expenseRatio'] ?? null,
                    ];

                    // Si se trata de un ETF, añadir pesos de sectores y países
                    if ($marketAsset->type === 'etf' || ($profile['sector'] ?? '') === 'ETF') {
                        $data['sectorWeightings'] = $this->stockService->getEtfSectorWeightings($marketAsset->ticker);
                        $data['countryWeightings'] = $this->stockService->getEtfCountryWeightings($marketAsset->ticker);
                        
                        // Si el TER (Expense Ratio) no está en el perfil, buscarlo específicamente
                        if (!isset($data['ter'])) {
                             $etfInfo = $this->stockService->getEtfInfo($marketAsset->ticker);
                             $data['ter'] = $etfInfo['expenseRatio'] ?? null;
                        }
                    }
                    return $data;
                default:
                    return null;
            }
        } catch (\Exception $e) {
            Log::error("Error al obtener perfil de activo ({$marketAsset->ticker}): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Construye la URL del logo de una acción a partir de su símbolo 
     * y normaliza los tipos numéricos para el frontend (Vue).
     */
    public function enrichStockLogo($item) {
        if (isset($item['symbol'])) {
            $symbol = strtoupper($item['symbol']);
            $item['image'] = "https://financialmodelingprep.com/image-stock/{$symbol}.png";
        }
        
        if (isset($item['price'])) $item['price'] = (float)$item['price'];
        if (isset($item['changesPercentage'])) $item['changesPercentage'] = (float)$item['changesPercentage'];
        if (isset($item['change'])) $item['change'] = (float)$item['change'];
        
        return $item;
    }
}
