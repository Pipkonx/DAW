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

        $taxRate = $user->tax_rate / 100;
        $enableTax = $user->enable_tax_projection;

        // Calculate projections
        $projections = $bankAccounts->map(function ($account) use ($taxRate, $enableTax) {
            $balance = $account->balance;
            $apy = $account->apy;
            
            $r = $apy / 100;
            
            // Si hay impuestos, el rendimiento neto es r * (1 - tax)
            $netR = $enableTax ? $r * (1 - $taxRate) : $r;
            
            return [
                'id' => $account->id,
                'name' => $account->name,
                'current_balance' => $balance,
                'apy' => $apy,
                'projected_1y' => $balance * pow(1 + $netR, 1),
                'projected_5y' => $balance * pow(1 + $netR, 5),
                'projected_10y' => $balance * pow(1 + $netR, 10),
                'monthly_earnings' => ($balance * $netR) / 12,
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
        
        $investmentReturnRate = ($user->investment_return_rate ?? 7.00) / 100; 
        $netInvestmentRate = $enableTax ? $investmentReturnRate * (1 - $taxRate) : $investmentReturnRate;

        $invested1y = $totalInvestedValue * pow(1 + $netInvestmentRate, 1);
        $invested5y = $totalInvestedValue * pow(1 + $netInvestmentRate, 5);
        $invested10y = $totalInvestedValue * pow(1 + $netInvestmentRate, 10);
            
        $aggregatedProjection = [
            'current_balance' => $totalBalance + $totalInvestedValue,
            'liquid_balance' => $totalBalance,
            'invested_balance' => $totalInvestedValue,
            'apy' => $weightedApy,
            'investment_return_rate' => round($investmentReturnRate * 100, 2),
            'net_investment_return_rate' => round($netInvestmentRate * 100, 2),
            'tax_rate' => $user->tax_rate,
            'enable_tax_projection' => $enableTax,
            'projected_1y' => $projections->sum('projected_1y') + $invested1y,
            'projected_5y' => $projections->sum('projected_5y') + $invested5y,
            'projected_10y' => $projections->sum('projected_10y') + $invested10y,
            'monthly_earnings' => $projections->sum('monthly_earnings') + (($totalInvestedValue * $netInvestmentRate) / 12),
        ];

        return Inertia::render('FinancialPlanning/Index', [
            'bankAccounts' => $bankAccounts,
            'projections' => $projections,
            'aggregated' => $aggregatedProjection,
            'settings' => [
                'investment_return_rate' => $user->investment_return_rate,
                'tax_rate' => $user->tax_rate,
                'enable_tax_projection' => $user->enable_tax_projection,
            ]
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'investment_return_rate' => 'required|numeric|min:0|max:100',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'enable_tax_projection' => 'required|boolean',
        ]);

        $user = Auth::user();
        
        $user->update([
            'investment_return_rate' => round($validated['investment_return_rate'], 2),
            'tax_rate' => round($validated['tax_rate'], 2),
            'enable_tax_projection' => $validated['enable_tax_projection'],
        ]);

        return redirect()->back()->with('success', 'Configuración de planificación actualizada correctamente.');
    }
}
