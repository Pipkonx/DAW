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
     * Get consolidated portfolio data with current values and yields.
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
     * Get historical net worth data for the last X months.
     */
    public function getNetWorthHistory($userId, $months = 6)
    {
        $labels = [];
        $values = [];
        $yields = [];
        $now = Carbon::now();

        // Obtener todos los activos del usuario para procesar sus transacciones una sola vez
        $assets = Asset::where('user_id', $userId)->get();
        $allTransactions = Transaction::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->groupBy('asset_id');

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i)->endOfMonth();
            $labels[] = $date->format('M');
            
            $cash = Transaction::where('user_id', $userId)
                ->where('date', '<=', $date)
                ->selectRaw("SUM(CASE 
                    WHEN type IN ('income', 'sell', 'dividend', 'gift', 'reward', 'transfer_in') THEN amount 
                    WHEN type IN ('expense', 'buy', 'transfer_out') THEN -amount 
                    ELSE 0 END) as cash")
                ->value('cash') ?? 0;

            $totalAssetsValue = 0;
            $totalAssetsCost = 0;

            foreach ($assets as $asset) {
                $assetTxs = $allTransactions->get($asset->id, collect())->filter(fn($t) => $t->date <= $date);
                
                $qty = 0;
                $costBasis = 0;
                
                foreach ($assetTxs as $tx) {
                    if (in_array($tx->type, ['buy', 'transfer_in', 'gift', 'reward'])) {
                        $costBasis += $tx->quantity * $tx->price_per_unit;
                        $qty += $tx->quantity;
                    } elseif (in_array($tx->type, ['sell', 'transfer_out'])) {
                        if ($qty > 0) {
                            $avg = $costBasis / $qty;
                            $costBasis -= $avg * $tx->quantity;
                        }
                        $qty -= $tx->quantity;
                    }
                }

                if ($qty > 0) {
                    $totalAssetsValue += $qty * ($asset->current_price ?? 0);
                    $totalAssetsCost += $costBasis;
                }
            }

            $values[] = round($cash + $totalAssetsValue, 2);
            $yields[] = $totalAssetsCost > 0 ? round((($totalAssetsValue - $totalAssetsCost) / $totalAssetsCost) * 100, 2) : 0;
        }

        return compact('labels', 'values', 'yields');
    }

    /**
     * Get historical valuation for each portfolio + orphans.
     */
    public function getPortfolioHistory($userId, $months = 6)
    {
        $portfolios = Portfolio::where('user_id', $userId)->get();
        $history = [];
        $now = Carbon::now();

        // Pre-cargar activos y transacciones
        $allAssets = Asset::where('user_id', $userId)->get()->groupBy('portfolio_id');
        $allTransactions = Transaction::where('user_id', $userId)
            ->orderBy('date', 'asc')
            ->get()
            ->groupBy('asset_id');

        $calculateHistory = function($portfolioId) use ($months, $now, $allAssets, $allTransactions) {
            $valHistory = [];
            $yieldHistory = [];
            $assets = $allAssets->get($portfolioId, collect());

            for ($i = $months - 1; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i)->endOfMonth();
                $totalVal = 0;
                $totalCost = 0;

                foreach ($assets as $asset) {
                    $assetTxs = $allTransactions->get($asset->id, collect())->filter(fn($t) => $t->date <= $date);
                    $qty = 0;
                    $costBasis = 0;

                    foreach ($assetTxs as $tx) {
                        if (in_array($tx->type, ['buy', 'transfer_in', 'gift', 'reward'])) {
                            $costBasis += $tx->quantity * $tx->price_per_unit;
                            $qty += $tx->quantity;
                        } elseif (in_array($tx->type, ['sell', 'transfer_out'])) {
                            if ($qty > 0) {
                                $avg = $costBasis / $qty;
                                $costBasis -= $avg * $tx->quantity;
                            }
                            $qty -= $tx->quantity;
                        }
                    }

                    if ($qty > 0) {
                        $totalVal += $qty * ($asset->current_price ?? 0);
                        $totalCost += $costBasis;
                    }
                }
                $valHistory[] = round($totalVal, 2);
                $yieldHistory[] = $totalCost > 0 ? round((($totalVal - $totalCost) / $totalCost) * 100, 2) : 0;
            }
            return ['values' => $valHistory, 'yields' => $yieldHistory];
        };

        foreach ($portfolios as $p) {
            $history[$p->id] = $calculateHistory($p->id);
        }

        // Orphans (Sin Cartera)
        $history['orphan'] = $calculateHistory(null);

        return $history;
    }

    private function formatAssets($assets)
    {
        return $assets->map(fn($a) => [
            'id' => $a->id, 'name' => $a->name, 'ticker' => $a->ticker, 'type' => $a->type,
            'quantity' => $a->quantity, 'current_price' => $a->current_price, 'avg_buy_price' => $a->avg_buy_price,
            'current_value' => $a->current_value, 'profit_loss_pct' => $a->profit_loss_percentage,
            'color' => $a->color, 'logo' => $a->logo,
        ]);
    }
}
