<?php

namespace App\Http\Controllers;

use App\Http\Requests\BulkStoreTransactionsRequest;
use App\Http\Requests\PreviewImportRequest;
use App\Services\Financial\ImportService;
use App\Services\Financial\Import\ImportHelper;
use App\Services\Transaction\TransactionService;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Portfolio;

class TransactionImportController extends Controller
{
    protected $transactionService;
    protected $importService;

    public function __construct(TransactionService $transactionService, ImportService $importService)
    {
        $this->transactionService = $transactionService;
        $this->importService = $importService;
    }

    /**
     * Previsualizar importación de transacciones desde un archivo.
     */
    public function previewImport(PreviewImportRequest $request)
    {
        try {
            $rawTransactions = $this->importService->previewFromFile($request->file('file'));
            $transactions = [];

            // Obtener categorías para emparejar por nombre
            $categories = Category::where(function($q) {
                $q->where('user_id', Auth::id())->orWhereNull('user_id');
            })->get();

            // Detectar si hay operaciones de inversión para auto-crear cartera
            $hasInvestments = false;

            foreach ($rawTransactions as $tx) {
                $tx['type'] = $tx['type'] ?? 'expense';
                $tx['amount'] = abs($tx['amount'] ?? 0);
                $tx['date'] = $tx['date'] ?? now()->format('Y-m-d');
                
                // Usar el nombre real del concepto del CSV
                $conceptName = $tx['ticker'] ?? $tx['name'] ?? null;
                if (empty($conceptName) || $conceptName === 'UNKNOWN' || $conceptName === 'Operación') {
                    $conceptName = 'Operación Importada';
                }
                $tx['asset_name'] = $conceptName;

                // Para la descripción, usar el nombre real en vez del genérico
                if (empty($tx['description']) || $tx['description'] === 'Importado vía asistente') {
                    $tx['description'] = $conceptName;
                }

                // Detectar si esta operación es de tipo inversión
                $isInvestmentType = in_array($tx['type'], ['buy', 'sell', 'dividend']);
                
                if ($isInvestmentType) {
                    $hasInvestments = true;
                    // Asignar datos de inversión
                    $tx['asset_type'] = ImportHelper::guessAssetType($conceptName);
                    $tx['quantity'] = $tx['quantity'] ?? 1;
                    $tx['price_per_unit'] = $tx['price_per_unit'] ?? $tx['amount'];
                }
                
                // Intentar emparejar categoría si viene el nombre en el archivo
                if (!empty($tx['category_name'])) {
                    $matchedCategory = $categories->first(fn($c) => 
                        mb_strtolower($c->name) === mb_strtolower($tx['category_name'])
                    );
                    if ($matchedCategory) {
                        $tx['category_id'] = $matchedCategory->id;
                    }
                }

                // Si no hay categoría asignada y NO es inversión, intentamos adivinar
                if (empty($tx['category_id']) && !$isInvestmentType) {
                    $tx['category_id'] = $this->guessCategoryId($tx['asset_name'], $categories, $tx['type']);
                    
                    if (empty($tx['category_id'])) {
                        $tx['category_name'] = $tx['asset_name'];
                    }
                }

                $transactions[] = $tx;
            }

            // Si hay inversiones, buscar carteras existentes o sugerir una nueva
            $portfolioId = null;
            $portfolioName = 'Mi Cartera';

            if ($hasInvestments) {
                $existingPortfolio = Portfolio::where('user_id', Auth::id())->first();
                if ($existingPortfolio) {
                    $portfolioId = $existingPortfolio->id;
                    $portfolioName = $existingPortfolio->name;
                }

                // Asignar portfolio_id o portfolio_name a las transacciones de inversión
                foreach ($transactions as &$tx) {
                    if (in_array($tx['type'], ['buy', 'sell', 'dividend'])) {
                        $tx['portfolio_id'] = $portfolioId;
                        if (!$portfolioId) {
                            $tx['portfolio_name'] = $portfolioName;
                        }
                    }
                }
                unset($tx);
            }

            return response()->json([
                'transactions' => $transactions,
                'count' => count($transactions),
                'portfolio_id' => $portfolioId,
                'portfolio_name' => $portfolioName,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al procesar archivo: ' . $e->getMessage()], 422);
        }
    }

    /**
     * Almacenamiento masivo de transacciones confirmadas.
     */
    public function bulkStore(BulkStoreTransactionsRequest $request)
    {
        try {
            $count = 0;
            foreach ($request->validated()['transactions'] as $txData) {
                $this->transactionService->store($txData);
                $count++;
            }

            return redirect()->back()->with('success', "Se han importado {$count} operaciones correctamente.");
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Error en importación masiva: ' . $e->getMessage()]);
        }
    }

    /**
     * Intenta adivinar el ID de la categoría basándose en palabras clave.
     */
    private function guessCategoryId($description, $categories, $type)
    {
        $desc = mb_strtolower($description);
        
        $keywords = [
            'Alimentación' => ['mercadona', 'lidl', 'carrefour', 'aldi', 'dia', 'supermercado', 'hipercor', 'eroski', 'consum'],
            'Restaurantes' => ['restaurante', 'bar', 'cafeteria', 'burger', 'mcdonald', 'kfc', 'pizzeria', 'uber eats', 'glovo', 'just eat', 'starbucks'],
            'Vivienda' => ['alquiler', 'comunidad', 'hipoteca', 'endesa', 'iberdrola', 'naturgy', 'agua', 'luz', 'gas'],
            'Transporte' => ['gasoliner', 'repsol', 'cepsa', 'bp', 'shell', 'taxi', 'vtc', 'uber', 'cabify', 'renfe', 'metro', 'autobus', 'peaje'],
            'Suscripciones (Netflix, Spotify...)' => ['netflix', 'spotify', 'hbo', 'disney', 'amazon prime', 'dazn', 'apple.com/bill', 'google storage', 'youtube'],
            'Salud y Bienestar' => ['farmacia', 'medico', 'dentista', 'gimnasio', 'gym', 'crossfit', 'peluqueria'],
            'Nómina' => ['nomina', 'payroll', 'sueldo', 'haberes'],
            'Ocio y Entretenimiento' => ['cine', 'teatro', 'concierto', 'videojuegos', 'steam', 'playstation', 'nintendo'],
            'Ropa y Calzado' => ['zara', 'h&m', 'pull&bear', 'stradivarius', 'nike', 'adidas', 'primark', 'decathlon'],
        ];

        foreach ($keywords as $catName => $keys) {
            foreach ($keys as $key) {
                if (str_contains($desc, $key)) {
                    $cat = $categories->first(fn($c) => 
                        mb_strtolower($c->name) === mb_strtolower($catName) && $c->type === $type
                    );
                    if ($cat) return $cat->id;
                }
            }
        }

        return null;
    }
}
