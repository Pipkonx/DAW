<?php

namespace App\Services\Analysis;

use App\Models\Transaction;
use App\Models\Asset;
use App\Models\Portfolio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Obtiene los datos consolidados de las carteras con sus valores actuales y rendimientos.
     * 
     * @param int $userId ID del usuario
     * @return \Illuminate\Support\Collection
     */
    public function getPortfoliosData($userId)
    {
        $portfolios = Portfolio::where('user_id', $userId)
            ->with(['assets' => fn($q) => $q->where('quantity', '>', 0)->with('marketAsset')])
            ->get();

        $portfoliosData = $portfolios->map(function ($p) {
            $val = $p->assets->sum('current_value');
            $cost = $p->assets->sum('total_invested');
            return [
                'id' => $p->id,
                'name' => $p->name,
                'total_value' => $val,
                'total_cost' => $cost,
                'yield' => $cost > 0 ? (($val - $cost) / $cost) * 100 : 0,
                'assets' => $this->formatAssets($p->assets)
            ];
        });

        // Add Orphan Assets
        $orphans = Asset::where('user_id', $userId)->whereNull('portfolio_id')->where('quantity', '>', 0)->with('marketAsset')->get();
        if ($orphans->isNotEmpty()) {
            $oVal = $orphans->sum('current_value');
            $oCost = $orphans->sum('total_invested');
            $portfoliosData->push([
                'id' => 'orphan',
                'name' => 'Sin Cartera',
                'total_value' => $oVal,
                'total_cost' => $oCost,
                'yield' => $oCost > 0 ? (($oVal - $oCost) / $oCost) * 100 : 0,
                'assets' => $this->formatAssets($orphans)
            ]);
        }

        return $portfoliosData;
    }

    /**
     * Obtiene el historial del patrimonio neto (Efectivo + Inversiones) de los últimos X meses.
     * 
     * @param int $userId ID del usuario
     * @param int $months Número de meses a recuperar
     * @return array Contiene labels, values (valor absoluto) y yields (rendimiento %)
     */
    public function getNetWorthHistory($userId, $months = null)
    {
        $labels = [];
        $values = [];
        $yields = [];
        $now = Carbon::now();

        if ($months === 'MAX' || $months === null || $months === '') {
            $firstTx = Transaction::where('user_id', $userId)->orderBy('date', 'asc')->first();
            if ($firstTx) {
                $firstDate = Carbon::parse($firstTx->date)->startOfMonth();
                $months = (int) $firstDate->diffInMonths($now->copy()->startOfMonth()) + 1;
            } else {
                $months = 6;
            }
        }
        $months = min(300, max(1, (int)$months));

        // 1. Pre-cargar TODAS las transacciones relevantes para el cálculo de efectivo histórico (una sola consulta)
        $cashTxs = Transaction::where('user_id', $userId)
            ->where('date', '<=', $now->copy()->endOfMonth())
            ->orderBy('date', 'asc')
            ->get();

        // 2. Pre-cargar activos y sus transacciones agrupadas (una sola consulta para activos)
        $assets = Asset::where('user_id', $userId)->get();
        $allTransactions = $cashTxs->groupBy('asset_id');

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i)->endOfMonth();
            $labels[] = $date->format('M');
            
            // Cálculo del efectivo histórico en memoria (acumulado hasta $date)
            $cash = $cashTxs->filter(fn($tx) => $tx->date <= $date)->sum(function ($tx) {
                if (in_array($tx->type, ['income', 'sell', 'dividend', 'gift', 'reward', 'transfer_in'])) return $tx->amount;
                if (in_array($tx->type, ['expense', 'buy', 'transfer_out'])) return -$tx->amount;
                return 0;
            });

            // Cálculo de valoración de activos en el punto temporal 'date'
            $snapshot = $this->calculateAssetsSnapshot($assets, $allTransactions, $date);

            $values[] = round($cash + $snapshot['totalValue'], 2);
            $yields[] = $snapshot['totalCost'] > 0 
                ? round((($snapshot['totalValue'] - $snapshot['totalCost']) / $snapshot['totalCost']) * 100, 2) 
                : 0;
        }

        return compact('labels', 'values', 'yields');
    }

    /**
     * Obtiene la valoración histórica individual para cada cartera (incluyendo activos sin cartera).
     * 
     * @param int $userId ID del usuario
     * @param int $months Número de meses
     * @return array Mapa de ID de cartera => historial de valores y rendimientos
     */
    public function getPortfolioHistory($userId, $months = null)
    {
        $portfolios = Portfolio::where('user_id', $userId)->get();
        $history = [];
        $now = Carbon::now();

        if ($months === 'MAX' || $months === null || $months === '') {
            $firstTx = Transaction::where('user_id', $userId)->orderBy('date', 'asc')->first();
            if ($firstTx) {
                $firstDate = Carbon::parse($firstTx->date)->startOfMonth();
                $months = (int) $firstDate->diffInMonths($now->copy()->startOfMonth()) + 1;
            } else {
                $months = 6;
            }
        }
        $months = min(300, max(1, (int)$months));

        // Pre-cargar activos y transacciones agrupados
        $allAssets = Asset::where('user_id', $userId)->get()->groupBy('portfolio_id');
        $allTransactions = Transaction::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('asset_id');

        $processPortfolio = function($portfolioId) use ($months, $now, $allAssets, $allTransactions) {
            $valHistory = [];
            $yieldHistory = [];
            $assets = $allAssets->get($portfolioId, collect());

            for ($i = $months - 1; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i)->endOfMonth();
                
                $snapshot = $this->calculateAssetsSnapshot($assets, $allTransactions, $date);

                $valHistory[] = round($snapshot['totalValue'], 2);
                $yieldHistory[] = $snapshot['totalCost'] > 0 
                    ? round((($snapshot['totalValue'] - $snapshot['totalCost']) / $snapshot['totalCost']) * 100, 2) 
                    : 0;
            }
            return ['values' => $valHistory, 'yields' => $yieldHistory];
        };

        foreach ($portfolios as $p) {
            $history[$p->id] = $processPortfolio($p->id);
        }

        // Procesar activos sin cartera asociada (Huerfanos)
        $history['orphan'] = $processPortfolio(null);

        return $history;
    }

    /**
     * Calcula la valoración (Value) y el coste base (Cost) de un conjunto de activos
     * en una fecha específica, utilizando el historial de transacciones (WAC).
     * 
     * @param \Illuminate\Support\Collection $assets
     * @param \Illuminate\Support\Collection $allTransactions
     * @param \Carbon\Carbon $date
     * @return array ['totalValue' => float, 'totalCost' => float]
     */
    private function calculateAssetsSnapshot($assets, $allTransactions, $date)
    {
        $totalValue = 0;
        $totalCost = 0;

        foreach ($assets as $asset) {
            // Filtramos las transacciones hasta la fecha de corte
            $assetTxs = $allTransactions->get($asset->id, collect())->filter(fn($t) => $t->date <= $date);
            
            $quantity = 0;
            $costBasis = 0;

            foreach ($assetTxs as $tx) {
                if (in_array($tx->type, ['buy', 'transfer_in', 'gift', 'reward'])) {
                    // Compra o entrada: incrementa cantidad y coste base
                    $costBasis += $tx->quantity * $tx->price_per_unit;
                    $quantity += $tx->quantity;
                } elseif (in_array($tx->type, ['sell', 'transfer_out'])) {
                    // Venta o salida: reduce cantidad y coste base proporcionalmente (WAC)
                    if ($quantity > 0) {
                        $averagePrice = $costBasis / $quantity;
                        $costBasis -= $averagePrice * $tx->quantity;
                    }
                    $quantity -= $tx->quantity;
                }
            }

            // Solo sumamos si el usuario aún posee el activo en esa fecha
            if ($quantity > 0) {
                // Usamos el precio actual como estimación, ya que no tenemos histórico de precios diario
                $totalValue += $quantity * ($asset->current_price ?? 0);
                $totalCost += $costBasis;
            }
        }

        return [
            'totalValue' => $totalValue,
            'totalCost' => $totalCost
        ];
    }

    /**
     * Formatea los datos de los activos para su envío al frontend.
     * 
     * @param \Illuminate\Support\Collection $assets
     * @return \Illuminate\Support\Collection
     */
    private function formatAssets($assets)
    {
        return $assets->map(fn($a) => [
            'id' => $a->id, 
            'name' => $a->name, 
            'ticker' => $a->ticker, 
            'type' => $a->type,
            'quantity' => $a->quantity, 
            'current_price' => $a->current_price, 
            'avg_buy_price' => $a->avg_buy_price,
            'current_value' => $a->current_value, 
            'profit_loss_pct' => $a->profit_loss_percentage,
            'color' => $a->color, 
            'logo' => $a->logo,
        ]);
    }

    /**
     * Obtiene el rendimiento anual condensado para el gráfico de barras vertical.
     * 
     * @param int $userId ID del usuario
     * @return array { labels: string[], data: float[] }
     */
    public function getAnnualPerformance($userId)
    {
        $now = Carbon::now();
        $startYear = Transaction::where('user_id', $userId)->min('date');
        $startYear = $startYear ? Carbon::parse($startYear)->year : $now->year - 1;
        
        $labels = [];
        $data = [];

        $allAssets = Asset::where('user_id', $userId)->get();
        $allTxs = Transaction::where('user_id', $userId)
            ->where('date', '<=', $now)
            ->get()
            ->groupBy('asset_id');

        for ($year = $startYear; $year <= $now->year; $year++) {
            $labels[] = (string)$year;
            $date = Carbon::create($year)->endOfYear();
            if ($year == $now->year) $date = $now;

            $snapshot = $this->calculateAssetsSnapshot($allAssets, $allTxs, $date);
            $profit = $snapshot['totalValue'] - $snapshot['totalCost'];
            
            $data[] = round($profit, 2);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
