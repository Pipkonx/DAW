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
use Barryvdh\DomPDF\Facade\Pdf;
use Inertia\Inertia;

use App\Models\MarketAsset;
use App\Services\MarketDataService;

class TransactionController extends Controller
{
    protected $marketDataService;

    public function __construct(MarketDataService $marketDataService)
    {
        $this->marketDataService = $marketDataService;
    }

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
        $timeframe = $request->input('timeframe', 'MAX'); // 1D, 1M, 1Y, YTD, MAX

        // Obtener fecha de la primera transacción para el filtro de exportación
        $firstTransaction = Transaction::where('user_id', $user->id)->orderBy('date', 'asc')->first();
        $minDate = $firstTransaction ? $firstTransaction->date->format('Y-m-d') : Carbon::now()->format('Y-m-d');

        // 1. Obtener Carteras
        $portfolios = Portfolio::where('user_id', $user->id)
            ->withCount('assets')
            ->with(['assets']) // Cargar activos para calcular el valor total
            ->get();
            
        // Calcular valor total de cada cartera
        $portfolios->each(function ($portfolio) {
            $portfolio->total_value = $portfolio->assets->sum(function ($asset) {
                return $asset->quantity * ($asset->current_price ?? $asset->avg_buy_price);
            });
            // Ocultar la relación assets para no enviar datos innecesarios
            $portfolio->unsetRelation('assets');
        });

        // 2. Filtrar Activos según Cartera Seleccionada
        $assetsQuery = Asset::where('user_id', $user->id);
        
        if ($portfolioId !== 'aggregated') {
            $assetsQuery->where('portfolio_id', $portfolioId);
        }
        
        $assets = $assetsQuery->get();
        
        // Self-healing: Recalcular métricas si detectamos inconsistencias (ej: importaciones sin precio medio)
        $assets->each(function ($asset) {
            if ($asset->quantity > 0 && $asset->avg_buy_price == 0) {
                 if ($asset->transactions()->exists()) {
                     $asset->recalculateMetrics();
                 }
            }
        });

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
        $transactionsQuery = $this->getTransactionsQuery($user, $portfolioId, $assetId, $assetIds);

        $transactions = $transactionsQuery->paginate(15)
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

        return Inertia::render('Transactions/Index', [
            'portfolios' => $portfolios,
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
            'minDate' => $minDate,
        ]);
    }

    /**
     * Exporta el historial de transacciones.
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        $format = $request->input('format', 'csv');
        $portfolioId = $request->input('portfolio_id', 'aggregated');
        $assetId = $request->input('asset_id');

        // Reconstruir assetIds necesario para el filtro
        $assetsQuery = Asset::where('user_id', $user->id);
        if ($portfolioId !== 'aggregated') {
            $assetsQuery->where('portfolio_id', $portfolioId);
        }
        $assetIds = $assetsQuery->pluck('id');

        $query = $this->getTransactionsQuery($user, $portfolioId, $assetId, $assetIds);

        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        $transactions = $query->get();

        if ($format === 'pdf') {
            $pdf = Pdf::loadView('exports.transactions', ['transactions' => $transactions, 'user' => $user]);
            return $pdf->download('historial-transacciones.pdf');
        } else {
            // CSV Export
            $headers = [
                "Content-type" => "text/csv",
                "Content-Disposition" => "attachment; filename=historial-transacciones.csv",
                "Pragma" => "no-cache",
                "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                "Expires" => "0"
            ];

            $callback = function() use ($transactions) {
                $file = fopen('php://output', 'w');
                
                // BOM para Excel (UTF-8)
                fputs($file, "\xEF\xBB\xBF");
                
                // Cabeceras (usando punto y coma para Excel español)
                fputcsv($file, ['Fecha', 'Tipo', 'Activo / Concepto', 'Cantidad', 'Precio', 'Total', 'Descripción'], ';');

                foreach ($transactions as $tx) {
                    $assetName = $tx->asset ? ($tx->asset->ticker . ' - ' . $tx->asset->name) : $tx->description;
                    
                    // Mapeo de tipos
                    $typeName = match($tx->type) {
                        'buy' => 'Compra',
                        'sell' => 'Venta',
                        'dividend' => 'Dividendo',
                        'reward' => 'Recompensa',
                        'gift' => 'Regalo',
                        'income' => 'Ingreso',
                        'expense' => 'Gasto',
                        default => ucfirst($tx->type)
                    };

                    fputcsv($file, [
                        $tx->date->format('d/m/Y'),
                        $typeName,
                        $assetName,
                        number_format($tx->quantity, 8, ',', ''), // Formato español
                        number_format($tx->price_per_unit, 4, ',', ''),
                        number_format($tx->amount, 2, ',', ''),
                        $tx->description
                    ], ';');
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }
    }

    /**
     * Construye la consulta de transacciones reutilizable.
     */
    private function getTransactionsQuery($user, $portfolioId, $assetId, $assetIds)
    {
        $query = Transaction::where('user_id', $user->id)->with(['asset', 'portfolio']);

        if ($assetId) {
            if (is_array($assetId)) {
                $query->whereIn('asset_id', $assetId);
            } elseif (is_string($assetId) && str_contains($assetId, ',')) {
                 $query->whereIn('asset_id', explode(',', $assetId));
            } else {
                $query->where('asset_id', $assetId);
            }
        } elseif ($portfolioId !== 'aggregated') {
            $query->where(function($q) use ($portfolioId, $assetIds) {
                $q->where('portfolio_id', $portfolioId)
                  ->orWhereIn('asset_id', $assetIds);
            });
        } else {
            $query->where(function($q) use ($assetIds) {
                $q->whereNotNull('portfolio_id')
                  ->orWhereIn('asset_id', $assetIds)
                  ->orWhereIn('type', ['buy', 'sell', 'dividend', 'reward', 'gift']);
            });
        }

        return $query->orderBy('date', 'desc')->orderBy('created_at', 'desc');
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
        
        if ($timeframe === 'MAX') {
            // Find the earliest transaction for these assets
            $firstTx = Transaction::where('user_id', $userId)
                ->whereIn('asset_id', $assetIds)
                ->orderBy('date', 'asc')
                ->first();
            
            $startDate = $firstTx ? Carbon::parse($firstTx->date) : Carbon::now()->subMonth();
        } else {
            $startDate = match($timeframe) {
                '1D' => Carbon::now()->subDay(),
                '1W' => Carbon::now()->subWeek(),
                '1M' => Carbon::now()->subMonth(),
                '3M' => Carbon::now()->subMonths(3),
                '1Y' => Carbon::now()->subYear(),
                'YTD' => Carbon::now()->startOfYear(),
                default => Carbon::now()->subMonth(),
            };
        }

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
            'asset_full_name' => 'nullable|string',
            'asset_type' => 'nullable|string|in:stock,crypto,fund,etf,bond,real_estate,other',
            'market_asset_id' => 'nullable|exists:market_assets,id',
            'isin' => 'nullable|string',
            'quantity' => 'nullable|numeric|min:0',
            'price_per_unit' => 'nullable|numeric|min:0',
            // Enforce portfolio for investment transactions
            'portfolio_id' => 'required_if:type,buy,sell,dividend|nullable|exists:portfolios,id',
            // Metadata for Assets (Deep Dive)
            'currency_code' => 'nullable|string|size:3',
            // Advanced fields
            'time' => 'nullable|date_format:H:i',
            'fees' => 'nullable|numeric|min:0',
            'exchange_fees' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
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

                // Sincronizar o encontrar MarketAsset global si no viene ID
                $marketAssetId = $validated['market_asset_id'] ?? null;
                
                if (empty($marketAssetId)) {
                    try {
                        $marketAsset = $this->marketDataService->syncAsset(
                            $validated['asset_name'], 
                            $validated['asset_type'] ?? 'stock', 
                            $validated['asset_full_name'] ?? $validated['asset_name'],
                            $validated['currency_code'] ?? 'USD',
                            $validated['isin'] ?? null
                        );
                        if ($marketAsset) {
                            $marketAssetId = $marketAsset->id;
                        }
                    } catch (\Exception $e) {
                        // Si falla la sincronización, continuamos sin market_asset_id
                        \Illuminate\Support\Facades\Log::warning("No se pudo sincronizar MarketAsset: " . $e->getMessage());
                    }
                }

                // Buscamos si el usuario ya tiene este activo en la cartera especificada
                // Si permitimos el mismo activo en diferentes carteras, incluimos portfolio_id en la búsqueda
                // Intentamos buscar por market_asset_id si existe, o por nombre/ticker
                
                $query = Asset::where('user_id', $user->id)
                    ->where('portfolio_id', $validated['portfolio_id'] ?? null);

                if (!empty($marketAssetId)) {
                    $query->where('market_asset_id', $marketAssetId);
                } else {
                    $query->where('name', $validated['asset_name']);
                }

                $asset = $query->first();

                if (!$asset) {
                    $asset = Asset::create([
                        'user_id' => $user->id,
                        'portfolio_id' => $validated['portfolio_id'] ?? null,
                        'name' => $validated['asset_full_name'] ?? $validated['asset_name'],
                        'ticker' => strtoupper($validated['asset_name']),
                        'type' => $validated['asset_type'] ?? 'stock',
                        'market_asset_id' => $marketAssetId,
                        'isin' => $validated['isin'] ?? null,
                        'quantity' => 0,
                        'avg_buy_price' => 0,
                        'current_price' => $validated['price_per_unit'] ?? 0,
                        'color' => (function($str) {
                            $hash = md5($str);
                            // Generate darker colors (max 128) for better contrast with white text
                            $r = hexdec(substr($hash, 0, 2)) % 128; 
                            $g = hexdec(substr($hash, 2, 2)) % 128;
                            $b = hexdec(substr($hash, 4, 2)) % 128;
                            return sprintf("#%02x%02x%02x", $r, $g, $b);
                        })($validated['asset_full_name'] ?? $validated['asset_name']),
                    ]);
                }
                
                // Si encontramos el activo pero no tenía market_asset_id y ahora sí lo tenemos, actualizarlo
                if (!empty($marketAssetId) && !$asset->market_asset_id) {
                    $asset->market_asset_id = $marketAssetId;
                }
                
                // Actualizar ISIN si no lo tiene
                if (!empty($validated['isin']) && !$asset->isin) {
                    $asset->isin = $validated['isin'];
                }

                $asset->save();

                // Auto-fetch price/link for new or unpriced assets
                if ($asset->wasRecentlyCreated || !$asset->market_asset_id || !$asset->current_price) {
                     try {
                         // This will attempt to link by name and fetch price
                         $latestPrice = $this->marketDataService->getLatestPrice($asset);
                         
                         // If no price found, try explicit broader search for "similar" asset
                         if (!$latestPrice) {
                             $nameToSearch = $validated['asset_full_name'] ?? $validated['asset_name'];
                             $searchResults = $this->marketDataService->search($nameToSearch);
                             
                             if ($searchResults->isNotEmpty()) {
                                 $bestMatch = $searchResults->first();
                                 
                                 // Sync and link the best match found
                                 $marketAsset = $this->marketDataService->syncAsset(
                                     $bestMatch['ticker'], 
                                     $bestMatch['type'], 
                                     $bestMatch['name'], 
                                     $bestMatch['currency'] ?? 'EUR', 
                                     $bestMatch['isin'] ?? null
                                 );
                                 
                                 if ($marketAsset) {
                                     $asset->update([
                                         'market_asset_id' => $marketAsset->id,
                                         // Update local metadata to match better info
                                         'ticker' => $marketAsset->ticker,
                                         'isin' => $marketAsset->isin ?? $asset->isin,
                                         'type' => $marketAsset->type
                                     ]);
                                     
                                     // Retry price fetch with linked asset
                                     $latestPrice = $this->marketDataService->getLatestPrice($asset);
                                 }
                             }
                         }

                         if ($latestPrice) {
                             $asset->current_price = $latestPrice;
                             $asset->save();
                         }
                     } catch (\Exception $e) {
                         \Illuminate\Support\Facades\Log::warning("Failed to auto-fetch price: " . $e->getMessage());
                     }
                }

                // Update metadata if provided (allows enriching asset data)
                $asset->update([
                    'currency_code' => $validated['currency_code'] ?? $asset->currency_code,
                ]);

                $assetId = $asset->id;

                // Actualizamos la cantidad y precio promedio del activo
                if ($validated['type'] === 'buy') {
                    // Cálculo de Precio Promedio Ponderado (Weighted Average Price)
                    // Nuevo Precio Promedio = ((QtyActual * PrecioPromedioActual) + (QtyNueva * PrecioCompra)) / (QtyActual + QtyNueva)
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
                    
                    // Validar que no vendamos más de lo que tenemos (opcional, pero recomendado)
                    // if ($asset->quantity < $quantityToSell) { throw new \Exception("No tienes suficiente cantidad para vender."); }

                    $asset->quantity = max(0, $asset->quantity - $quantityToSell);
                    
                    // Actualizamos precio de mercado
                    if ($pricePerUnit > 0) {
                        $asset->current_price = $pricePerUnit;
                    }
                    $asset->save();
                }
            } else {
                 // Si no es buy/sell/dividend, assetId es null por defecto
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
                'fees' => $validated['fees'] ?? null,
                'exchange_fees' => $validated['exchange_fees'] ?? null,
                'tax' => $validated['tax'] ?? null,
                'currency' => $validated['currency_code'] ?? 'EUR',
                'time' => $validated['time'] ?? null,
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
            'currency_code' => 'nullable|string|size:3',
            // Advanced fields
            'time' => 'nullable|date_format:H:i',
            'fees' => 'nullable|numeric|min:0',
            'exchange_fees' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Revertir impacto anterior en activo si es inversión (Simplificación: solo ajustamos si cambió cantidad)
            // NOTA: Para un sistema real, esto requiere un recálculo histórico completo.
            // Aquí asumiremos que el usuario corrige un error reciente.
            
            if ($transaction->asset_id && in_array($transaction->type, ['buy', 'sell'])) {
                $asset = $transaction->asset;
                $oldQuantity = $transaction->quantity;
                $newQuantity = $validated['quantity'] ?? $oldQuantity;
                
                if ($oldQuantity != $newQuantity) {
                    $diff = $newQuantity - $oldQuantity;
                    
                    if ($transaction->type === 'buy') {
                        $asset->quantity += $diff;
                    } elseif ($transaction->type === 'sell') {
                        $asset->quantity -= $diff;
                    }
                    $asset->save();
                }
            }

            $transaction->update([
                'amount' => $validated['amount'],
                'date' => $validated['date'],
                'category_id' => $validated['category_id'] ?? null,
                'description' => $validated['description'],
                'quantity' => $validated['quantity'] ?? $transaction->quantity,
                'price_per_unit' => $validated['price_per_unit'] ?? $transaction->price_per_unit,
                'fees' => $validated['fees'] ?? $transaction->fees,
                'exchange_fees' => $validated['exchange_fees'] ?? $transaction->exchange_fees,
                'tax' => $validated['tax'] ?? $transaction->tax,
                'currency' => $validated['currency_code'] ?? $transaction->currency,
                'time' => $validated['time'] ?? $transaction->time,
            ]);

            // Update asset metadata if provided
            if ($transaction->asset_id) {
                $transaction->asset->update([
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

    /**
     * Elimina una transacción.
     */
    public function destroy(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        DB::beginTransaction();

        try {
            $assetId = $transaction->asset_id;
            
            // Eliminar la transacción primero
            $transaction->delete();

            // Si el activo existe, recalcular sus métricas
            if ($assetId) {
                $asset = Asset::find($assetId);
                if ($asset) {
                    // Verificar si quedan transacciones
                    if ($asset->transactions()->count() === 0) {
                        $asset->delete(); // Si no quedan, eliminar el activo (huérfano)
                    } else {
                        $asset->recalculateMetrics(); // Si quedan, recalcular precio medio y cantidad
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', 'Transacción eliminada.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al eliminar: ' . $e->getMessage()]);
        }
    }

    /**
     * Elimina múltiples transacciones.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:transactions,id'
        ]);

        DB::beginTransaction();

        try {
            $transactions = Transaction::whereIn('id', $request->ids)
                ->where('user_id', Auth::id())
                ->get();

            $assetIdsToCheck = [];

            foreach ($transactions as $transaction) {
                if ($transaction->asset_id) {
                    $assetIdsToCheck[] = $transaction->asset_id;
                }
                $transaction->delete();
            }

            // Verificar activos huérfanos y recalcular métricas de los restantes
            $assetIdsToCheck = array_unique($assetIdsToCheck);
            
            foreach ($assetIdsToCheck as $assetId) {
                $asset = Asset::find($assetId);
                if ($asset) {
                    if ($asset->transactions()->count() === 0) {
                        $asset->delete();
                    } else {
                        $asset->recalculateMetrics();
                    }
                }
            }

            DB::commit();
            return redirect()->back()->with('success', count($transactions) . ' transacciones eliminadas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Error al eliminar masivamente: ' . $e->getMessage()]);
        }
    }
}
