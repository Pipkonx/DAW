<?php

namespace App\Services\MarketData;

/**
 * StockService - Orquestador de Datos de Mercado.
 * 
 * Este servicio actúa como punto de entrada unificado para la obtención 
 * de datos bursátiles, delegando la lógica técnica a servicios especializados 
 * de alta cohesión: StockPriceService e StockInfoService.
 */
class StockService
{
    protected $priceService;
    protected $infoService;

    public function __construct(StockPriceService $priceService, StockInfoService $infoService)
    {
        $this->priceService = $priceService;
        $this->infoService = $infoService;
    }

    /**
     * Obtiene el precio en tiempo real de un símbolo.
     */
    public function getPrice(string $symbol)
    {
        return $this->priceService->getPrice($symbol);
    }

    /**
     * Busca activos en el mercado por término.
     */
    public function search(string $query)
    {
        return $this->infoService->search($query);
    }

    /**
     * Obtiene el precio de cierre en una fecha pasada específica.
     */
    public function getHistoricalPrice(string $symbol, string $date)
    {
        return $this->priceService->getHistoricalPrice($symbol, $date);
    }

    /**
     * Recupera el historial de precios para su representación gráfica.
     */
    public function getHistoricalData(string $symbol, int $days = 365)
    {
        return $this->priceService->getHistoricalData($symbol, $days);
    }

    /**
     * Listado de acciones con mayor subida diaria.
     */
    public function getTopGainers()
    {
        return $this->priceService->getTopGainers();
    }

    /**
     * Listado de acciones con mayor caída diaria.
     */
    public function getTopLosers()
    {
        return $this->priceService->getTopLosers();
    }

    /**
     * Listado de acciones con mayor volumen de negociación.
     */
    public function getMostActive()
    {
        return $this->priceService->getMostActive();
    }

    /**
     * Obtiene el perfil completo de la compañía o ETF.
     */
    public function getProfile(string $symbol)
    {
        return $this->infoService->getProfile($symbol);
    }

    /**
     * Recupera el desglose sectorial de un ETF.
     */
    public function getEtfSectorWeightings(string $symbol)
    {
        return $this->infoService->getEtfSectorWeightings($symbol);
    }

    /**
     * Recupera el desglose geográfico de un ETF.
     */
    public function getEtfCountryWeightings(string $symbol)
    {
        return $this->infoService->getEtfCountryWeightings($symbol);
    }

    /**
     * Métodos adicionales de información institucional (Proxy).
     */
    public function getInstitutionalHoldings(string $cik)
    {
        return $this->infoService->getInstitutionalHoldings($cik);
    }

    /**
     * Obtiene información financiera detallada del ETF (TER, etc).
     */
    public function getEtfInfo(string $symbol)
    {
        return $this->infoService->getEtfInfo($symbol);
    }
}
