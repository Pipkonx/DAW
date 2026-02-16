<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BankAccount;
use App\Models\Asset;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FinancialPlanningController extends Controller
{
    /**
     * Display the financial planning dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $bankAccounts = BankAccount::where('user_id', $user->id)->get();

        // Calculate projections
        $projections = $bankAccounts->map(function ($account) {
            $balance = $account->balance;
            $apy = $account->apy;
            
            // Simple compound interest projection for 1, 5, 10 years
            // Future Value = P * (1 + r/n)^(nt)
            // Assuming monthly compounding (n=12) if it's APY usually it's annual yield, so (1+r)^t for simple annual
            // Let's use simple annual compounding for APY
            $r = $apy / 100;
            
            return [
                'id' => $account->id,
                'name' => $account->name,
                'current_balance' => $balance,
                'apy' => $apy,
                'projected_1y' => $balance * pow(1 + $r, 1),
                'projected_5y' => $balance * pow(1 + $r, 5),
                'projected_10y' => $balance * pow(1 + $r, 10),
                'monthly_earnings' => ($balance * $r) / 12,
            ];
        });

        $totalBalance = $bankAccounts->sum('balance');
        $weightedApy = $totalBalance > 0 
            ? $bankAccounts->sum(fn($a) => $a->balance * $a->apy) / $totalBalance 
            : 0;

        // Investment Projections (Assume user defined return or 7% default)
        $assets = Asset::where('user_id', $user->id)->get();
        $totalInvestedValue = $assets->sum(function ($asset) {
            return $asset->current_value;
        });
        
        $investmentReturnRate = ($user->investment_return_rate ?? 7.00) / 100; // From DB or default 7%

        $invested1y = $totalInvestedValue * pow(1 + $investmentReturnRate, 1);
        $invested5y = $totalInvestedValue * pow(1 + $investmentReturnRate, 5);
        $invested10y = $totalInvestedValue * pow(1 + $investmentReturnRate, 10);
            
        $aggregatedProjection = [
            'current_balance' => $totalBalance + $totalInvestedValue,
            'liquid_balance' => $totalBalance,
            'invested_balance' => $totalInvestedValue,
            'apy' => $weightedApy,
            'investment_return_rate' => $investmentReturnRate * 100,
            'projected_1y' => $projections->sum('projected_1y') + $invested1y,
            'projected_5y' => $projections->sum('projected_5y') + $invested5y,
            'projected_10y' => $projections->sum('projected_10y') + $invested10y,
            'monthly_earnings' => $projections->sum('monthly_earnings') + (($totalInvestedValue * $investmentReturnRate) / 12),
        ];

        return Inertia::render('FinancialPlanning/Index', [
            'bankAccounts' => $bankAccounts,
            'projections' => $projections,
            'aggregated' => $aggregatedProjection,
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'investment_return_rate' => 'required|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        $user->update([
            'investment_return_rate' => $validated['investment_return_rate'],
        ]);

        return redirect()->back()->with('success', 'Configuración actualizada correctamente.');
    }
}
