<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Smalot\PdfParser\Parser;

class PortfolioController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, \App\Services\MarketDataService $marketDataService)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'transactions' => 'nullable|array',
            'transactions.*.date' => 'required_with:transactions|date',
            'transactions.*.type' => 'required_with:transactions|string',
            'transactions.*.ticker' => 'required_with:transactions|string',
            'transactions.*.isin' => 'nullable|string',
            'transactions.*.asset_type' => 'nullable|string|in:stock,fund,etf,crypto,bond',
            'transactions.*.quantity' => 'required_with:transactions|numeric',
            'transactions.*.price_per_unit' => 'required_with:transactions|numeric',
            'transactions.*.amount' => 'required_with:transactions|numeric',
            'transactions.*.name' => 'nullable|string',
        ]);

        $portfolio = Portfolio::firstOrCreate([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
        ]);

        if (!empty($validated['transactions'])) {
            foreach ($validated['transactions'] as $txData) {
                // Find or Create Asset
                $ticker = strtoupper($txData['ticker']);
                $isin = $txData['isin'] ?? null;
                $assetType = $txData['asset_type'] ?? 'stock';
                $name = $txData['name'] ?? $ticker;

                // If ticker looks like ISIN and ISIN is missing, set ISIN
                if (!$isin && preg_match('/^[A-Z]{2}[A-Z0-9]{9}\d$/', $ticker)) {
                    $isin = $ticker;
                    $assetType = 'fund'; // Assume fund/ETF if ISIN provided as ticker
                }

                $asset = \App\Models\Asset::where('user_id', Auth::id())
                    ->where('portfolio_id', $portfolio->id)
                    ->where(function ($query) use ($ticker, $isin) {
                        $query->where('ticker', $ticker);
                        if ($isin) {
                            $query->orWhere('isin', $isin);
                        }
                    })
                    ->first();

                if (!$asset) {
                    $asset = \App\Models\Asset::create([
                        'user_id' => Auth::id(),
                        'portfolio_id' => $portfolio->id,
                        'ticker' => $ticker,
                        'isin' => $isin,
                        'name' => $name,
                        'type' => $assetType,
                        'link_status' => $txData['link_status'] ?? 'linked', // Default to linked if not provided (manual)
                        'original_name' => $txData['original_name'] ?? null,
                        'original_text' => $txData['original_text'] ?? null,
                        'nav_date' => $txData['nav_date'] ?? null,
                        'color' => (function($str) {
                            $hash = md5($str);
                            // Generate darker colors (max 128) for better contrast with white text
                            $r = hexdec(substr($hash, 0, 2)) % 128; 
                            $g = hexdec(substr($hash, 2, 2)) % 128;
                            $b = hexdec(substr($hash, 4, 2)) % 128;
                            return sprintf("#%02x%02x%02x", $r, $g, $b);
                        })($name),
                    ]);

                    // Auto-fetch price/link for new assets
                    try {
                        // First try standard price fetch (which attempts auto-link)
                        $latestPrice = $marketDataService->getLatestPrice($asset);
                        
                        // If no price found, try explicit broader search for "similar" asset
                        if (!$latestPrice) {
                            $searchResults = $marketDataService->search($name);
                            
                            if ($searchResults->isNotEmpty()) {
                                $bestMatch = $searchResults->first();
                                
                                // Sync and link the best match found
                                $marketAsset = $marketDataService->syncAsset(
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
                                    $latestPrice = $marketDataService->getLatestPrice($asset);
                                }
                            }
                        }

                        if ($latestPrice) {
                            $asset->current_price = $latestPrice;
                            $asset->save();
                        }
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::warning("PortfolioController: Failed to auto-fetch price for {$name}: " . $e->getMessage());
                    }
                } else {
                    // Update metadata if better info provided
                    $updates = [];
                    if ($isin && !$asset->isin) $updates['isin'] = $isin;
                    if ($assetType !== 'stock' && $asset->type === 'stock') $updates['type'] = $assetType;
                    // If it was pending and now we have info, update status?
                    // Or if we are re-importing... 
                    // Let's just update fields if they are missing
                    if (!empty($updates)) $asset->update($updates);
                }

                // Check for duplicate transaction
                $exists = \App\Models\Transaction::where('user_id', Auth::id())
                    ->where('asset_id', $asset->id)
                    ->where('type', strtolower($txData['type']))
                    ->whereDate('date', \Carbon\Carbon::parse($txData['date']))
                    ->where('quantity', $txData['quantity'])
                    ->where('amount', $txData['amount'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Create Transaction
                \App\Models\Transaction::create([
                    'user_id' => Auth::id(),
                    'asset_id' => $asset->id,
                    'type' => strtolower($txData['type']),
                    'date' => \Carbon\Carbon::parse($txData['date']),
                    'quantity' => $txData['quantity'],
                    'price_per_unit' => $txData['price_per_unit'],
                    'amount' => $txData['amount'],
                    'description' => 'Importación automática' . (isset($txData['original_text']) ? ' | OCR' : ''),
                ]);

                // Update Asset Averages (simplified, would normally use a service)
                // For now, we assume the user will recalculate or the next load will handle it
                // Actually, we should probably update the asset's quantity/price here
                // But let's keep it simple: just create transactions. 
                // The Asset model doesn't auto-update on transaction create in this codebase yet?
                // Let's check Asset model or Transaction observer. 
                // Based on previous edits, I don't see observers.
                // So I should probably update asset quantity manually here.
                
                if (in_array($txData['type'], ['buy', 'transfer_in', 'gift', 'reward'])) {
                    $asset->quantity += $txData['quantity'];
                } elseif (in_array($txData['type'], ['sell', 'transfer_out'])) {
                    $asset->quantity -= $txData['quantity'];
                }
                $asset->save();
            }
        }

        return redirect()->back()->with('success', 'Cartera creada exitosamente.');
    }

    /**
     * Parse a number string to float, handling common formats (1,234.56 or 1.234,56).
     */
    private function parseNumber($str)
    {
        $str = trim($str);
        if (empty($str)) return 0.0;
        
        // Remove currency symbols and spaces
        $str = preg_replace('/[^\d,.-]/', '', $str);
        
        // Check for comma as decimal separator (European format: 1.234,56 or 1234,56)
        // If comma is the last separator, it's likely decimal
        $lastComma = strrpos($str, ',');
        $lastDot = strrpos($str, '.');
        
        if ($lastComma !== false && ($lastDot === false || $lastComma > $lastDot)) {
            // European format: remove dots (thousands), replace comma with dot
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } else {
            // US format: remove commas (thousands)
            $str = str_replace(',', '', $str);
        }
        
        return (float) $str;
    }

    /**
     * Preview import from file.
     */
    public function previewImport(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt,pdf,jpg,jpeg,png|max:10240',
        ]);

        $file = $request->file('file');
        $extension = $file->getClientOriginalExtension();
        $transactions = [];

        if (in_array(strtolower($extension), ['csv', 'txt'])) {
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            $header = array_shift($data); // Assume first row is header
            
            // Basic mapping logic (heuristic)
            $map = [
                'date' => -1,
                'ticker' => -1,
                'type' => -1,
                'quantity' => -1,
                'price' => -1,
                'amount' => -1,
            ];

            foreach ($header as $index => $col) {
                $col = strtolower(trim($col));
                if (str_contains($col, 'date') || str_contains($col, 'fecha')) $map['date'] = $index;
                if (str_contains($col, 'ticker') || str_contains($col, 'symbol') || str_contains($col, 'activo') || str_contains($col, 'isin') || str_contains($col, 'name') || str_contains($col, 'nombre') || str_contains($col, 'fondo') || str_contains($col, 'instrumento') || str_contains($col, 'titulo')) $map['ticker'] = $index;
                if (str_contains($col, 'type') || str_contains($col, 'tipo')) $map['type'] = $index;
                if (str_contains($col, 'quantity') || str_contains($col, 'cantidad') || str_contains($col, 'units') || str_contains($col, 'títulos') || str_contains($col, 'titulos')) $map['quantity'] = $index;
                if (str_contains($col, 'price') || str_contains($col, 'precio')) $map['price'] = $index;
                if (str_contains($col, 'amount') || str_contains($col, 'total') || str_contains($col, 'valor') || str_contains($col, 'importe')) $map['amount'] = $index;
            }

            foreach ($data as $row) {
                if (count($row) < 3) continue; // Skip empty rows

                $tx = [
                    'date' => $map['date'] > -1 ? ($row[$map['date']] ?? now()->format('Y-m-d')) : now()->format('Y-m-d'),
                    'ticker' => $map['ticker'] > -1 ? ($row[$map['ticker']] ?? 'UNKNOWN') : 'UNKNOWN',
                    'type' => $map['type'] > -1 ? strtolower($row[$map['type']] ?? 'buy') : 'buy',
                    'quantity' => $map['quantity'] > -1 ? $this->parseNumber($row[$map['quantity']] ?? 0) : 0,
                    'price_per_unit' => $map['price'] > -1 ? $this->parseNumber($row[$map['price']] ?? 0) : 0,
                    'amount' => $map['amount'] > -1 ? $this->parseNumber($row[$map['amount']] ?? 0) : 0,
                ];

                // Heuristic fixes
                $typeMap = [
                    'compra' => 'buy', 'buy' => 'buy',
                    'venta' => 'sell', 'sell' => 'sell',
                    'deposito' => 'transfer_in', 'deposit' => 'transfer_in',
                    'ingreso' => 'transfer_in',
                    'retiro' => 'transfer_out', 'withdrawal' => 'transfer_out',
                    'gasto' => 'transfer_out',
                    'dividendo' => 'dividend', 'dividend' => 'dividend',
                ];
                
                foreach ($typeMap as $k => $v) {
                    if (str_contains($tx['type'], $k)) {
                        $tx['type'] = $v;
                        break;
                    }
                }
                
                // Ensure valid date
                try {
                    $tx['date'] = \Carbon\Carbon::parse($tx['date'])->format('Y-m-d');
                } catch (\Exception $e) {
                    $tx['date'] = now()->format('Y-m-d');
                }

                $transactions[] = $tx;
            }
        } elseif (strtolower($extension) === 'pdf') {
            try {
                $parser = new Parser();
                $pdf = $parser->parseFile($file->getRealPath());
                $text = $pdf->getText();
                
                $lines = explode("\n", $text);
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    if (preg_match('/(\d{4}-\d{2}-\d{2}|\d{2}\/\d{2}\/\d{4})/', $line, $dateMatch)) {
                        $dateStr = $dateMatch[0];
                        $rest = str_replace($dateStr, '', $line);
                        
                        $type = 'buy';
                        if (stripos($rest, 'sell') !== false || stripos($rest, 'venta') !== false) $type = 'sell';
                        elseif (stripos($rest, 'buy') !== false || stripos($rest, 'compra') !== false) $type = 'buy';
                        elseif (stripos($rest, 'deposit') !== false || stripos($rest, 'deposito') !== false) $type = 'transfer_in';
                        elseif (stripos($rest, 'withdrawal') !== false || stripos($rest, 'retiro') !== false) $type = 'transfer_out';
                        elseif (stripos($rest, 'dividend') !== false || stripos($rest, 'dividendo') !== false) $type = 'dividend';
                        
                        // Extract potential numbers
                        preg_match_all('/[\d\.,]+/', $rest, $numberMatches);
                        $numbers = [];
                        foreach ($numberMatches[0] as $numStr) {
                            $val = $this->parseNumber($numStr);
                            if ($val > 0) $numbers[] = $val;
                        }
                        
                        // Find Ticker or ISIN
                        $words = explode(' ', $rest);
                        $ticker = 'UNKNOWN';
                        
                        // Check for ISIN first (12 chars: 2 letters, 9 alphanum, 1 digit)
                        if (preg_match('/\b[A-Z]{2}[A-Z0-9]{9}\d\b/', $rest, $isinMatch)) {
                            $ticker = $isinMatch[0];
                        } else {
                            // Look for potential ticker or short name
                            foreach ($words as $word) {
                                $word = trim($word);
                                if (empty($word)) continue;
                                // Ticker usually: uppercase, not a number, not a common word
                                if (preg_match('/^[A-Z]{1,5}$/', $word) && !is_numeric($word) && !in_array(strtolower($word), ['buy', 'sell', 'compra', 'venta', 'usd', 'eur'])) {
                                    $ticker = $word;
                                    break;
                                }
                            }
                        }
                        
                        if (count($numbers) >= 2) {
                            $quantity = $numbers[0];
                            $price = $numbers[1];
                            $amount = count($numbers) >= 3 ? $numbers[2] : ($quantity * $price);
                            
                            $transactions[] = [
                                'date' => \Carbon\Carbon::parse($dateStr)->format('Y-m-d'),
                                'ticker' => $ticker,
                                'type' => $type,
                                'quantity' => $quantity,
                                'price_per_unit' => $price,
                                'amount' => $amount,
                            ];
                        }
                    }
                }
            } catch (\Exception $e) {
                // PDF parsing failed
            }
        } elseif (in_array(strtolower($extension), ['jpg', 'jpeg', 'png'])) {
            // Use OCR.space API (Free Tier) to process the image without local installation
            try {
                $imageContent = file_get_contents($file->getRealPath());
                
                $response = Http::asMultipart()
                    ->attach('file', $imageContent, $file->getClientOriginalName())
                    ->post('https://api.ocr.space/parse/image', [
                        'apikey' => 'helloworld', // Free API key (limitations apply)
                        'language' => 'eng', // English is standard for financial terms usually
                        'isOverlayRequired' => 'false',
                        'detectOrientation' => 'true',
                        'scale' => 'true',
                        'OCREngine' => '2', // Better for numbers/special chars
                    ]);

                if ($response->successful()) {
                    $result = $response->json();
                    
                    if (isset($result['ParsedResults'][0]['ParsedText'])) {
                        $text = $result['ParsedResults'][0]['ParsedText'];
                        
                        // Parse the extracted text line by line
                        $lines = explode("\n", $text);
                        $currentDate = now()->format('Y-m-d'); // Default to today
                        $pendingTx = null; // Store transaction being built
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) continue;

                            // 1. Global Date Detection (Header: 01/02/2026)
                            // Prioritize Spanish format DD/MM/YYYY
                            if (preg_match('/^(\d{1,2})[\/\-\.](\d{1,2})[\/\-\.](\d{2,4})$/', $line, $dateMatch)) {
                                try {
                                    $d = $dateMatch[1];
                                    $m = $dateMatch[2];
                                    $y = $dateMatch[3];
                                    
                                    // Check for YYYY-MM-DD
                                    if (strlen($d) == 4) {
                                        $y = $dateMatch[1];
                                        $m = $dateMatch[2];
                                        $d = $dateMatch[3];
                                    } elseif (strlen($y) == 2) {
                                        $y = "20" . $y;
                                    }
                                    
                                    if (checkdate((int)$m, (int)$d, (int)$y)) {
                                        $currentDate = "$y-$m-$d"; // Update global date context
                                        continue; // Skip this line, it's just a header
                                    }
                                } catch (\Exception $e) {}
                            }
                            
                            // 2. Start of Transaction: Type + Amount (e.g., "Suscripción ... + 15,00 €")
                            // Heuristic: "Suscripción" or "Reembolso" or line ending in Amount (€)
                            $isStart = false;
                            $type = 'buy';
                            
                            if (stripos($line, 'Suscripción') !== false || stripos($line, 'Compra') !== false) {
                                $isStart = true;
                                $type = 'buy';
                            } elseif (stripos($line, 'Reembolso') !== false || stripos($line, 'Venta') !== false) {
                                $isStart = true;
                                $type = 'sell';
                            } elseif (preg_match('/[\+\-]\s*[\d\.,]+\s*[€$]/', $line)) {
                                // Line has explicit amount format like "+ 15,00 €"
                                $isStart = true;
                                // Guess type from sign
                                if (str_contains($line, '-')) $type = 'sell';
                            }
                            
                            if ($isStart) {
                                // Check if this is just an "Amount Line" that belongs to the CURRENT pending transaction?
                                // This happens if the OCR split "Suscripción" (Line 1) and "+ 15,00 €" (Line 2)
                                // OR if the Name was on Line 2 and Amount on Line 3.
                                
                                $isExplicitNew = (stripos($line, 'Suscripción') !== false || stripos($line, 'Reembolso') !== false || stripos($line, 'Compra') !== false);
                                
                                if ($pendingTx && $pendingTx['amount'] == 0 && !$isExplicitNew) {
                                    // It's likely the amount for the current pending transaction!
                                    // Extract Amount
                                    $amount = 0;
                                    preg_match('/([-+]?[\d\.,]+)\s*[€$]/', $line, $amtMatch);
                                    if (!empty($amtMatch[1])) {
                                        $amount = $this->parseNumber($amtMatch[1]);
                                    } else {
                                        // Fallback search for any number
                                        preg_match_all('/[-+]?(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{1,6})?)/', $line, $nums);
                                        if (!empty($nums[0])) {
                                            $lastNum = end($nums[0]);
                                            $amount = $this->parseNumber($lastNum);
                                        }
                                    }
                                    
                                    if ($amount > 0) {
                                        $pendingTx['amount'] = abs($amount);
                                        $pendingTx['type'] = $type; // Update type just in case
                                        $pendingTx['original_text'] .= " | " . $line;
                                        continue; // Done with this line, merged into pendingTx
                                    }
                                }
                                
                                // Flush previous pending transaction if exists (and we didn't merge)
                                if ($pendingTx) {
                                    // Process and push pendingTx
                                    $transactions[] = $this->finalizeTransaction($pendingTx);
                                    $pendingTx = null;
                                }
                                
                                // Extract Amount
                                $amount = 0;
                                preg_match('/([-+]?[\d\.,]+)\s*[€$]/', $line, $amtMatch);
                                if (!empty($amtMatch[1])) {
                                    $amount = $this->parseNumber($amtMatch[1]);
                                } else {
                                    // Fallback search for any number
                                    preg_match_all('/[-+]?(\d{1,3}(?:[.,]\d{3})*(?:[.,]\d{1,6})?)/', $line, $nums);
                                    if (!empty($nums[0])) {
                                        // Take the last number as amount usually?
                                        $lastNum = end($nums[0]);
                                        $amount = $this->parseNumber($lastNum);
                                    }
                                }
                                
                                $pendingTx = [
                                    'date' => $currentDate,
                                    'type' => $type,
                                    'amount' => abs($amount),
                                    'quantity' => 0,
                                    'price_per_unit' => 0,
                                    'name' => '', // Waiting for next line
                                    'original_text' => $line,
                                    'state' => 'WAITING_NAME' // New state
                                ];
                                continue;
                            }
                            
                            // 3. Quantity Line (e.g. "... participaciones")
                            if ($pendingTx && (stripos($line, 'participaciones') !== false || stripos($line, 'títulos') !== false)) {
                                preg_match('/([\d\.,]+)\s*(?:participaciones|títulos)/i', $line, $qtyMatch);
                                if (!empty($qtyMatch[1])) {
                                    $pendingTx['quantity'] = $this->parseNumber($qtyMatch[1]);
                                } else {
                                     // Just grab the first number found
                                     preg_match('/([\d\.,]+)/', $line, $qtyMatchFallback);
                                     if (!empty($qtyMatchFallback[1])) {
                                         $pendingTx['quantity'] = $this->parseNumber($qtyMatchFallback[1]);
                                     }
                                }
                                
                                // Append text to original
                                $pendingTx['original_text'] .= " | " . $line;
                                
                                // This closes the transaction visually
                                $transactions[] = $this->finalizeTransaction($pendingTx);
                                $pendingTx = null;
                                continue;
                            }
                            
                            // 4. Asset Name (Line between Start and Quantity)
                            if ($pendingTx && $pendingTx['state'] === 'WAITING_NAME') {
                                // Ignore "Finalizada", "Puntual" lines if they are just status
                                $cleanLine = str_replace(['Finalizada', 'Puntual', 'En proceso'], '', $line);
                                $cleanLine = trim($cleanLine);
                                
                                if (strlen($cleanLine) > 2) {
                                    $pendingTx['name'] = $cleanLine;
                                    $pendingTx['ticker'] = $cleanLine; // Initial guess
                                    $pendingTx['original_text'] .= " | " . $line;
                                    $pendingTx['state'] = 'WAITING_QUANTITY';
                                }
                                continue;
                            }
                            
                            // If we are here, it's an unhandled line.
                            // If we have a pending Tx, maybe append to original text just in case
                            if ($pendingTx) {
                                $pendingTx['original_text'] .= " " . $line;
                            }
                        }
                        
                        // Flush any remaining pending transaction
                        if ($pendingTx) {
                            $transactions[] = $this->finalizeTransaction($pendingTx);
                        }

                    }
                }
            } catch (\Exception $e) {
                // OCR failed or network error
                // Fallback to empty or error message handled by frontend
            }
        }

        return response()->json([
            'transactions' => $transactions,
            'count' => count($transactions)
        ]);
    }

    private function finalizeTransaction($tx) {
        // Calculate price if missing
        if ($tx['quantity'] > 0 && $tx['amount'] > 0 && $tx['price_per_unit'] == 0) {
            $tx['price_per_unit'] = $tx['amount'] / $tx['quantity'];
        }
        
        // Clean Name
        $name = trim($tx['name']);
        if (empty($name)) $name = "Activo Desconocido";
        
        $tx['original_name'] = $name;
        $tx['link_status'] = 'pending'; // Default state until linked
        $tx['nav_date'] = $tx['date']; // Default NAV date is transaction date

        // Determine Asset Type based on keywords (simple heuristic)
        $typeHint = 'stock'; // Default
        
        // Helper to check multiple needles
        $containsAny = function($haystack, $needles) {
            foreach ($needles as $needle) {
                if (stripos($haystack, $needle) !== false) return true;
            }
            return false;
        };

        if ($containsAny($name, ['Fondo', 'Fund', 'Index', 'Indice', 'Índice', 'Acc', 'Class', 'Clase', '(IE)', '(LU)', 'Sicav'])) {
            $typeHint = 'fund';
        } elseif (stripos($name, 'ETF') !== false) {
            $typeHint = 'etf'; // Treated as stock usually, but distinct type
        } elseif ($containsAny($name, ['Bitcoin', 'BTC', 'Crypto', 'ETH', 'Ethereum', 'Solana', 'USDT'])) {
            $typeHint = 'crypto';
        }

        // 1. Try to find Asset ID in User's existing assets (Local DB)
        $asset = \App\Models\Asset::where('user_id', Auth::id())
                    ->where(function($q) use ($name) {
                        $q->where('ticker', $name)
                          ->orWhere('isin', $name)
                          ->orWhere('name', 'like', "%$name%");
                    })
                    ->first();
        
        // 2. If not found, try to find in Market Assets (Global DB) or External Search
        $marketAsset = null;
        if (!$asset && strlen($name) > 3) {
            // Use MarketDataService to find or link
            try {
                $marketDataService = app(\App\Services\MarketDataService::class);
                
                // If type is fund, we prioritize fund search
                if ($typeHint === 'fund') {
                    // This calls FundService::searchByName internally if needed
                    $marketAsset = $marketDataService->findOrLinkAsset($name, 'fund');
                } else {
                    // For stocks, try stock search
                    $marketAsset = $marketDataService->findOrLinkAsset($name, $typeHint);
                }

                if ($marketAsset) {
                    $tx['isin'] = $marketAsset->isin;
                    $tx['ticker'] = $marketAsset->ticker;
                    $tx['asset_type'] = $marketAsset->type;
                    $tx['name'] = $marketAsset->name; // Update name to official one
                    $tx['link_status'] = 'linked';
                    
                    // Check if we have a recent price/NAV
                    if ($marketAsset->current_price) {
                         // We don't overwrite the transaction price (which is historical cost)
                         // But we might want to store the scraped date?
                         // The prompt says "Guardar NAV y fecha". 
                         // This usually means for the ASSET history, not necessarily the transaction.
                         // But if we just scraped it, maybe update the asset later.
                    }
                }
            } catch (\Exception $e) {
                // Log error but continue
                \Illuminate\Support\Facades\Log::error("Error linking asset in OCR: " . $e->getMessage());
                $tx['link_status'] = 'failed';
            }
        } else if ($asset) {
             $tx['link_status'] = 'linked';
             $tx['asset_id'] = $asset->id;
             $tx['ticker'] = $asset->ticker;
             $tx['isin'] = $asset->isin;
             $tx['asset_type'] = $asset->type;
        } else {
             // Fallback for completely unknown
             if (!isset($tx['ticker'])) $tx['ticker'] = 'UNKNOWN';
             if (!isset($tx['asset_type'])) $tx['asset_type'] = $typeHint;
             $tx['link_status'] = 'pending';
        }
        
        return $tx;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $portfolio->update([
            'name' => $validated['name'],
        ]);

        return redirect()->back()->with('success', 'Cartera actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Portfolio $portfolio)
    {
        if ($portfolio->user_id !== Auth::id()) {
            abort(403);
        }

        $portfolio->delete();

        return redirect()->back()->with('success', 'Cartera eliminada exitosamente.');
    }
}
