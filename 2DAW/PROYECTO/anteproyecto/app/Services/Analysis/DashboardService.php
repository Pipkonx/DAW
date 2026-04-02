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
        $now = Carbon::now();

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

            $assetsValue = 0;
            $assets = Asset::where('user_id', $userId)->get();
            foreach ($assets as $asset) {
                $qty = Transaction::where('user_id', $userId)->where('asset_id', $asset->id)->where('date', '<=', $date)
                    ->selectRaw("SUM(CASE WHEN type = 'buy' THEN quantity WHEN type = 'sell' THEN -quantity ELSE 0 END) as q")
                    ->value('q') ?? 0;
                $assetsValue += $qty * ($asset->current_price ?? 0);
            }
            $values[] = round($cash + $assetsValue, 2);
        }

        return compact('labels', 'values');
    }

    /**
     * Get historical valuation for each portfolio + orphans.
     */
    public function getPortfolioHistory($userId, $months = 6)
    {
        $portfolios = Portfolio::where('user_id', $userId)->get();
        $history = [];
        $now = Carbon::now();

        foreach ($portfolios as $p) {
            $history[$p->id] = [];
            for ($i = $months - 1; $i >= 0; $i--) {
                $date = $now->copy()->subMonths($i)->endOfMonth();
                $val = 0;
                $assets = Asset::where('portfolio_id', $p->id)->get();
                foreach ($assets as $asset) {
                    $qty = Transaction::where('user_id', $userId)->where('asset_id', $asset->id)->where('date', '<=', $date)
                        ->selectRaw("SUM(CASE WHEN type = 'buy' THEN quantity WHEN type = 'sell' THEN -quantity ELSE 0 END) as q")
                        ->value('q') ?? 0;
                    $val += $qty * ($asset->current_price ?? 0);
                }
                $history[$p->id][] = round($val, 2);
            }
        }

        // Orphans (Sin Cartera) - key 'orphan' matches Dashboard.vue logic
        $history['orphan'] = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = $now->copy()->subMonths($i)->endOfMonth();
            $val = 0;
            $orphans = Asset::where('user_id', $userId)->whereNull('portfolio_id')->get();
            foreach ($orphans as $asset) {
                $qty = Transaction::where('user_id', $userId)->where('asset_id', $asset->id)->where('date', '<=', $date)
                    ->selectRaw("SUM(CASE WHEN type = 'buy' THEN quantity WHEN type = 'sell' THEN -quantity ELSE 0 END) as q")
                    ->value('q') ?? 0;
                $val += $qty * ($asset->current_price ?? 0);
            }
            $history['orphan'][] = round($val, 2);
        }

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
