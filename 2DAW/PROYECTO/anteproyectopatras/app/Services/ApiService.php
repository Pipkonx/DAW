<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ApiService
{
    private $limits = [
        'FMP' => 250,            // Límite diario oficial del plan gratuito
        'CoinGecko' => 10000,    // Límite generoso de referencia para visualización
        'ExchangeRate' => 1500,  // Límite para divisas
        'Gemini' => 1500,        // Límite diario para el analista de IA
    ];

    /**
     * Registra una petición exitosa para una API específica.
     */
    public function trackRequest($apiName)
    {
        $key = $this->getCacheKey($apiName);
        $current = Cache::get($key, 0);
        
        // Incrementar y expirar al final del día (medianoche)
        Cache::put($key, $current + 1, now()->endOfDay());
    }

    /**
     * Obtiene los datos de consumo actuales para todas las APIs configuradas.
     */
    public function getConsumptionData()
    {
        $data = [];
        foreach ($this->limits as $api => $limit) {
            $used = Cache::get($this->getCacheKey($api), 0);
            $percentage = ($used / $limit) * 100;
            
            $data[$api] = [
                'used' => $used,
                'limit' => $limit,
                'percentage' => round($percentage, 1),
                'status' => $this->getStatusColor($percentage),
                'warning' => $percentage >= 90
            ];
        }
        return $data;
    }

    private function getCacheKey($apiName)
    {
        return 'api_usage_' . strtolower($apiName) . '_' . now()->format('Y-m-d');
    }

    private function getStatusColor($percentage)
    {
        if ($percentage >= 90) return 'rose';
        if ($percentage >= 70) return 'amber';
        return 'emerald';
    }
}
