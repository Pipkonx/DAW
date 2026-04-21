<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

/**
 * AdminService - Inteligencia de negocio para el centro de control.
 * 
 * Gestiona la telemetría del sistema, el consumo de cuotas de API 
 * y las estadísticas agregadas de la plataforma.
 */
class AdminService
{
    /**
     * Recupera el estado general de salud del sistema.
     */
    public function getSystemHealth(): array
    {
        return [
            'api_status' => $this->checkApiHealth(),
            'db_size' => $this->getDbSize(),
            'cache_enabled' => config('cache.default') !== 'array',
        ];
    }

    /**
     * Calcula estadísticas de usuarios y suscripciones.
     */
    public function getUserStats(): array
    {
        return [
            'users'         => User::count(),
            'premium_users' => DB::table('subscriptions')->where('stripe_status', 'active')->where('stripe_price', config('services.stripe.price_premium'))->count(),
            'pro_users'     => DB::table('subscriptions')->where('stripe_status', 'active')->where('stripe_price', config('services.stripe.price_pro'))->count(),
        ];
    }

    /**
     * Calcula estadísticas de activos y transacciones.
     */
    public function getGlobalStats(): array
    {
        return [
            'assets' => Asset::count(),
            'transactions' => Transaction::count(),
        ];
    }

    /**
     * Verifica la disponibilidad de las APIs críticas de mercado.
     */
    private function checkApiHealth(): array
    {
        $apis = [
            'FMP' => 'https://financialmodelingprep.com/api/v3/actives?apikey=' . (config('services.fmp.key') ?? env('FMP_API_KEY')),
            'CoinGecko' => 'https://api.coingecko.com/api/v3/ping'
        ];

        $results = [];
        foreach ($apis as $name => $url) {
            try {
                $response = Http::timeout(5)->get($url);
                $results[$name] = $response->successful();
            } catch (\Exception $e) {
                $results[$name] = false;
            }
        }
        return $results;
    }

    /**
     * Calcula el tamaño ocupado por la base de datos (Soporte MySQL y SQLite).
     */
    private function getDbSize(): string
    {
        try {
            $dbName = config('database.connections.mysql.database');
            if (!$dbName) throw new \Exception("No MySQL Connection");

            $result = DB::select("SELECT SUM(data_length + index_length) / 1024 / 1024 AS size FROM information_schema.TABLES WHERE table_schema = ?", [$dbName]);
            return round($result[0]->size, 2) . ' MB';
        } catch (\Exception $e) {
            $path = database_path('database.sqlite');
            return File::exists($path) ? round(File::size($path) / 1024 / 1024, 2) . ' MB' : '0.00 MB';
        }
    }
}
