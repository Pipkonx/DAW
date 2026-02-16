<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\BankAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Muestra la vista de Patrimonio Neto (Antes Transacciones).
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $portfolioId = $request->input('portfolio_id', 'aggregated');
        $assetId = $request->input('asset_id'); // Filtro por activo
        $timeframe = $request->input('timeframe', '1M'); // 1D, 1M, 1Y, YTD, MAX

        // 1. Obtener Carteras
        $portfolios = Portfolio::where('user_id', $user->id)->get();

        // 2. Filtrar Activos según Cartera Seleccionada
        $assetsQuery = Asset::where('user_id', $user->id);
        
        if ($portfolioId !== 'aggregated') {
            $assetsQuery->where('portfolio_id', $portfolioId);
        }
        
        // Si hay filtro de activo, solo mostramos ese activo en la lista (o todos si queremos ver el contexto, pero filtramos historia)
        // El usuario dijo "si le da que el historial de operaciones se filtre solo por ese activo".
        // Mantengamos la lista de activos visible según la cartera, para que pueda cambiar de activo.
        // PERO si el usuario quiere editar, tal vez quiera ver solo ese.
        // Vamos a mantener la lista de activos completa de la cartera, y solo filtrar transacciones.
        
        $assets = $assetsQuery->get();
        $assetIds = $assets->pluck('id');

        // 3. Calcular Totales (KPIs Principales)
        $totalInvested = $assets->sum('total_invested');
        $currentValue = $assets->sum('current_value');
        
        // Obtener Patrimonio Líquido (Cuentas Bancarias)
        $bankAccounts = BankAccount::where('user_id', $user->id)->get();
        $totalLiquid = $bankAccounts->sum('balance');

        $totalPL = $currentValue - $totalInvested;
        $totalPLPercent = ($totalInvested > 0) ? ($totalPL / $totalInvested) * 100 : 0;
        
        $totalNetWorth = $currentValue + $totalLiquid;

        // Ensure we don't return NaN or null
        $summary = [
            'total_invested' => $totalInvested ?? 0,
            'current_value' => $currentValue ?? 0,
            'total_pl' => $totalPL ?? 0,
            'total_pl_percent' => is_nan($totalPLPercent) ? 0 : $totalPLPercent,
            'total_net_worth' => $totalNetWorth ?? 0,
            'liquid_balance' => $totalLiquid ?? 0,
        ];

        // 4. Obtener Transacciones de Inversión (Historial)
        // Incluimos transacciones vinculadas a carteras (ej: depósitos/retiros/comisiones) 
        // y transacciones de activos (compra/venta).
        $transactionsQuery = Transaction::where('user_id', $user->id)
            ->with('asset');

        // Aplicar filtro de activo si existe
        if ($assetId) {
            $transactionsQuery->where('asset_id', $assetId);
        } elseif ($portfolioId !== 'aggregated') {
            $transactionsQuery->where(function($q) use ($portfolioId, $assetIds) {
                $q->where('portfolio_id', $portfolioId)
                  ->orWhereIn('asset_id', $assetIds);
            });
        } else {
            // En vista agregada, mostramos todo lo relacionado con inversiones/carteras
            $transactionsQuery->where(function($q) use ($assetIds) {
                $q->whereNotNull('portfolio_id')
                  ->orWhereIn('asset_id', $assetIds)
                  ->orWhereIn('type', ['buy', 'sell', 'dividend', 'reward', 'gift']);
            });
        }

        $transactions = $transactionsQuery->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // 5. Generar Datos para Gráfica de Rendimiento (Simulada basada en flujos de caja)
        // Nota: Sin precios históricos, mostramos el "Capital Invertido" vs "Valor Actual" (Lineal)
        // O calculamos el flujo de caja acumulado.
        $chartData = $this->getChartData($user->id, $assetIds, $timeframe, $currentValue);

        // Calculate Period PL (Change in PL over the timeframe)
        $periodPLValue = $chartData['period_pl_value'] ?? 0;
        $periodPLPercent = $chartData['period_pl_percent'] ?? 0;

        // 6. Datos de Distribución (Allocation)
        $allocations = [
            'type' => $this->getAllocation($assets, 'type'),
            'sector' => $this->getAllocation($assets, 'sector'),
            'industry' => $this->getAllocation($assets, 'industry'),
            'region' => $this->getAllocation($assets, 'region'),
            'country' => $this->getAllocation($assets, 'country'),
            'currency_code' => $this->getAllocation($assets, 'currency_code'),
            'asset' => $assets->map(fn($a) => ['label' => $a->ticker ?? $a->name, 'value' => $a->current_value, 'color' => $a->color]),
        ];

        // 7. Obtener Categorías para el Modal
        $categories = \App\Models\Category::where('user_id', $user->id)
            ->with(['children' => function($q) {
                $q->orderBy('usage_count', 'desc');
            }])
            ->orderBy('usage_count', 'desc')
            ->get();

        return inertia('Transactions/Index', [
            'portfolios' => $portfolios,
            'categories' => $categories,
            'selectedPortfolioId' => $portfolioId,
            'selectedAssetId' => $assetId,
            'summary' => $summary,
            'assets' => $assets,
            'transactions' => $transactions,
            'chart' => [
                'labels' => $chartData['labels'] ?? [],
                'data' => $chartData['data'] ?? [],
                'invested' => $chartData['invested'] ?? [],
                'period_pl_value' => $periodPLValue ?? 0,
                'period_pl_percent' => is_nan($periodPLPercent) ? 0 : $periodPLPercent,
            ],
            'allocations' => $allocations,
            'filters' => [
                'timeframe' => $timeframe,
            ],
        ]);
    }

    private function getAllocation($assets, $field)
    {
        return $assets->groupBy($field)
            ->map(function ($group, $key) {
                return [
                    'label' => $key ?: 'Otros',
                    'value' => $group->sum('current_value'),
                    'color' => '#' . substr(md5($key), 0, 6) // Color determinista basado en el nombre
                ];
            })->values();
    }

    private function getChartData($userId, $assetIds, $timeframe, $currentTotalValue)
    {
        // Definir rango de fechas
        $endDate = Carbon::now();
        $startDate = match($timeframe) {
            '1D' => Carbon::now()->subDay(),
            '1W' => Carbon::now()->subWeek(),
            '1M' => Carbon::now()->subMonth(),
            '3M' => Carbon::now()->subMonths(3),
            '1Y' => Carbon::now()->subYear(),
            'YTD' => Carbon::now()->startOfYear(),
            'MAX' => Carbon::create(2000, 1, 1),
            default => Carbon::now()->subMonth(),
        };

        // Obtener transacciones en el rango para calcular flujo
        $allTransactions = Transaction::where('user_id', $userId)
            ->whereIn('asset_id', $assetIds)
            ->whereIn('type', ['buy', 'sell']) // Solo flujo de capital principal
            ->orderBy('date', 'asc')
            ->get();

        $dataPoints = [];
        $invested = 0;
        
        // Calcular acumulado inicial hasta start date
        foreach($allTransactions as $t) {
            if (Carbon::parse($t->date)->lt($startDate)) {
                if ($t->type === 'buy') $invested += $t->amount;
                if ($t->type === 'sell') $invested -= $t->amount;
            }
        }

        // Generar serie temporal diaria
        $period = \Carbon\CarbonPeriod::create($startDate, '1 day', $endDate);
        $txByDate = $allTransactions->groupBy(fn($t) => substr($t->date, 0, 10));
        
        // Para simular el valor de mercado, calculamos la plusvalía total actual
        // y la distribuimos linealmente en el tiempo para que la gráfica sea suave.
        // Total Profit = Current Value - Current Invested (Last calculated invested)
        
        // Primero calculamos el invested final real para saber la diferencia
        $finalInvested = $invested;
        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            if (isset($txByDate[$dateStr])) {
                foreach ($txByDate[$dateStr] as $t) {
                    if ($t->type === 'buy') $finalInvested += $t->amount;
                    if ($t->type === 'sell') $finalInvested -= $t->amount;
                }
            }
        }
        
        $totalProfit = $currentTotalValue - $finalInvested;
        $totalDays = $startDate->diffInDays($endDate) ?: 1;
        
        // Reiniciamos para generar los puntos
        $currentInvested = $invested; // Reset to start value
        $dayCounter = 0;

        foreach ($period as $date) {
            $dateStr = $date->format('Y-m-d');
            
            if (isset($txByDate[$dateStr])) {
                foreach ($txByDate[$dateStr] as $t) {
                    if ($t->type === 'buy') $currentInvested += $t->amount;
                    if ($t->type === 'sell') $currentInvested -= $t->amount;
                }
            }
            
            // Interpolación lineal de la ganancia
            // Profit del día = (Día actual / Total Días) * Total Profit
            $dailyProfit = ($dayCounter / $totalDays) * $totalProfit;
            
            // Valor Estimado = Capital Invertido + Ganancia Acumulada Interpolada
            $estimatedValue = $currentInvested + $dailyProfit;

            $dataPoints[] = [
                'x' => $dateStr,
                'y' => max(0, $estimatedValue), // Evitar valores negativos
                'invested' => $currentInvested
            ];
            
            $dayCounter++;
        }

        // Calculate Period Performance
        $firstPoint = $dataPoints[0] ?? null;
        $lastPoint = end($dataPoints) ?: null;
        
        $periodPLValue = 0;
        $periodPLPercent = 0;

        if ($firstPoint && $lastPoint) {
            // PL at start = Start Value - Start Invested
            $startPL = $firstPoint['y'] - $firstPoint['invested'];
            
            // PL at end = End Value - End Invested
            $endPL = $lastPoint['y'] - $lastPoint['invested'];
            
            $periodPLValue = $endPL - $startPL;
            
            // Percent Return for Period = Period PL / Start Value (or Start Invested if Start Value is 0)
            $denominator = $firstPoint['y'] > 0 ? $firstPoint['y'] : ($firstPoint['invested'] > 0 ? $firstPoint['invested'] : 1);
            $periodPLPercent = ($periodPLValue / $denominator) * 100;
        }

        return [
            'labels' => array_column($dataPoints, 'x'),
            'data' => array_column($dataPoints, 'y'),
            'invested' => array_column($dataPoints, 'invested'),
            'period_pl_value' => $periodPLValue,
            'period_pl_percent' => $periodPLPercent,
        ];
    }

    /**
     * Almacena una nueva transacción en la base de datos.
     * 
     * Este método maneja tanto transacciones simples (ingresos/gastos) 
     * como operaciones de inversión complejas (compra/venta de activos).
     * 
     * @param Request $request Datos del formulario
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // 1. Validación de datos
        // Usamos reglas condicionales (required_if) para pedir datos extra solo si es una inversión.
        $validated = $request->validate([
            'type' => 'required|in:income,expense,buy,sell,dividend,reward,gift',
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id', // Changed from category string
            'description' => 'nullable|string',
            // Campos específicos para Inversiones
            'asset_name' => 'nullable|string',
            'asset_type' => 'nullable|string|in:stock,crypto,fund,etf,bond,real_estate,other',
            'quantity' => 'nullable|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            'portfolio_id' => 'nullable|exists:portfolios,id',
            // Metadata for Assets (Deep Dive)
            'sector' => 'nullable|string',
            'industry' => 'nullable|string',
            'region' => 'nullable|string',
            'country' => 'nullable|string',
            'currency_code' => 'nullable|string|size:3',
        ]);

        // 2. Inicio de Transacción de Base de Datos
        // Usamos DB::beginTransaction() para asegurar la integridad de los datos.
        // Si falla algo (ej: crear el activo), no se guardará la transacción.
        // Todo o nada (ACID).
        DB::beginTransaction();

        try {
            $user = Auth::user();
            $assetId = null;

            // 3. Lógica para Activos (Inversiones)
            if (in_array($validated['type'], ['buy', 'sell', 'dividend'])) {
                
                if (empty($validated['asset_name'])) {
                    throw new \Exception("El nombre del activo es obligatorio para inversiones.");
                }

                // Buscamos si el usuario ya tiene este activo en la cartera especificada
                // Si permitimos el mismo activo en diferentes carteras, incluimos portfolio_id en la búsqueda
                $asset = Asset::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'name' => $validated['asset_name'],
                        'portfolio_id' => $validated['portfolio_id'] ?? null
                    ],
                    [
                        'type' => $validated['asset_type'] ?? 'stock',
                        'ticker' => strtoupper(substr($validated['asset_name'], 0, 4)), // Generamos un ticker falso basado en el nombre
                        'quantity' => 0,
                        'avg_buy_price' => 0,
                        'current_price' => $validated['price_per_unit'] ?? 0,
                        'color' => '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT), // Color aleatorio para gráficas
                    ]
                );

                // Update metadata if provided (allows enriching asset data)
                $asset->update([
                    'sector' => $validated['sector'] ?? $asset->sector,
                    'industry' => $validated['industry'] ?? $asset->industry,
                    'region' => $validated['region'] ?? $asset->region,
                    'country' => $validated['country'] ?? $asset->country,
                    'currency_code' => $validated['currency_code'] ?? $asset->currency_code,
                ]);

                $assetId = $asset->id;

                // Actualizamos la cantidad y precio promedio del activo
                if ($validated['type'] === 'buy') {
                    // Cálculo de Precio Promedio Ponderado (Weighted Average Price)
                    // Nuevo Precio Promedio = (Valor Total Actual + Valor Compra Nueva) / Nueva Cantidad Total
                    // Nota: Si es la primera compra, currentTotalVal es 0.
                    $quantityToAdd = $validated['quantity'] ?? 0;
                    $pricePerUnit = $validated['price_per_unit'] ?? 0;
                    
                    $currentTotalVal = $asset->quantity * $asset->avg_buy_price;
                    $newBuyVal = $quantityToAdd * $pricePerUnit;
                    $newTotalQty = $asset->quantity + $quantityToAdd;
                    
                    if ($newTotalQty > 0) {
                         $asset->avg_buy_price = ($currentTotalVal + $newBuyVal) / $newTotalQty;
                    }
                    
                    $asset->quantity = $newTotalQty;
                    
                    // Actualizamos el precio actual de mercado al último precio pagado
                    if ($pricePerUnit > 0) {
                        $asset->current_price = $pricePerUnit;
                    }
                    $asset->save();

                } elseif ($validated['type'] === 'sell') {
                    // En venta, solo reducimos cantidad. El precio promedio de compra NO cambia al vender.
                    $quantityToSell = $validated['quantity'] ?? 0;
                    $pricePerUnit = $validated['price_per_unit'] ?? 0;
                    
                    $asset->quantity -= $quantityToSell;
                    
                    // Actualizamos precio de mercado
                    if (isset($validated['price_per_unit'])) {
                        $asset->current_price = $validated['price_per_unit'];
                    }
                    $asset->save();
                }
            }

            // 4. Crear la Transacción
            Transaction::create([
                'user_id' => $user->id,
                'asset_id' => $assetId,
                'type' => $validated['type'],
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'category_id' => $validated['category_id'] ?? null,
                'description' => $validated['description'],
                'quantity' => $validated['quantity'] ?? null,
                'price_per_unit' => $validated['price_per_unit'] ?? null,
                'portfolio_id' => $validated['portfolio_id'] ?? null,
            ]);

            // Increment usage count for category
            if (!empty($validated['category_id'])) {
                $category = \App\Models\Category::find($validated['category_id']);
                if ($category) {
                    $category->increment('usage_count');
                    if ($category->parent_id) {
                        \App\Models\Category::where('id', $category->parent_id)->increment('usage_count');
                    }
                }
            }

            DB::commit();

            return redirect()->back()->with('success', 'Transacción registrada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al guardar la transacción: ' . $e->getMessage()]);
        }
    }

    /**
     * Actualiza una transacción existente.
     */
    public function update(Request $request, Transaction $transaction)
    {
        // Verificar propiedad
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id', // Changed from category string
            'description' => 'nullable|string',
            // Para inversiones, permitimos editar cantidad/precio con precaución
            'quantity' => 'nullable|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            // Metadata for Assets
            'sector' => 'nullable|string',
            'industry' => 'nullable|string',
            'region' => 'nullable|string',
            'country' => 'nullable|string',
            'currency_code' => 'nullable|string|size:3',
        ]);

        DB::beginTransaction();

        try {
            // Revertir impacto anterior en activo si es inversión (Simplificación: solo ajustamos si cambió cantidad)
            // NOTA: Para un sistema real, esto requiere un recálculo histórico completo.
            // Aquí asumiremos que el usuario corrige un error reciente.
            
            $transaction->update([
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'category_id' => $validated['category_id'] ?? null,
                'description' => $validated['description'],
                'quantity' => $validated['quantity'] ?? $transaction->quantity,
                'price_per_unit' => $validated['price_per_unit'] ?? $transaction->price_per_unit,
            ]);

            // Update asset metadata if provided
            if ($transaction->asset_id) {
                $transaction->asset->update([
                    'sector' => $validated['sector'] ?? $transaction->asset->sector,
                    'industry' => $validated['industry'] ?? $transaction->asset->industry,
                    'region' => $validated['region'] ?? $transaction->asset->region,
                    'country' => $validated['country'] ?? $transaction->asset->country,
                    'currency_code' => $validated['currency_code'] ?? $transaction->asset->currency_code,
                ]);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transacción actualizada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al actualizar: ' . $e->getMessage()]);
        }
    }
}
