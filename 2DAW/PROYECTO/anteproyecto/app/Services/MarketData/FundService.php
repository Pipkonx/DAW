<?php

namespace App\Services\MarketData;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;
use Carbon\Carbon;

class FundService
{
    public function getPrice($isin)
    {
        $cacheKey = "fund_nav_{$isin}";
        $cached = Cache::get($cacheKey);

        if ($cached) {
            if ($cached === 'null_result') return null;
            return $cached;
        }

        try {
            // 1. Try Morningstar (Best for Mutual Funds)
            $data = $this->scrapeMorningstar($isin);
            
            // 2. Try JustETF (Best for ETFs)
            if (!$data) {
                $data = $this->scrapeJustETF($isin);
            }

            // 3. Try Financial Times (Fallback)
            if (!$data) {
                $data = $this->scrapeFinancialTimes($isin);
            }

            if ($data) {
                // Cache valid result for 24 hours
                Cache::put($cacheKey, $data, 86400);
                return $data;
            } else {
                // Cache failure for 10 minutes to avoid spamming
                Cache::put($cacheKey, 'null_result', 600);
                return null;
            }
        } catch (\Exception $e) {
            Log::error("FundService Error for {$isin}: " . $e->getMessage());
            return null;
        }
    }

    public function searchByName($name)
    {
        Log::info("FundService::searchByName called for: {$name}");
        // Cache search results for 24h to avoid spamming searches
        $cacheKey = "fund_search_" . md5(strtolower(trim($name)));
        
        return Cache::remember($cacheKey, 86400, function () use ($name) {
            Log::info("FundService: Cache miss for {$name}, searching...");
            
            // 1. Try Financial Times Search (Most reliable for general search)
            $result = $this->searchFT($name);
            if ($result) {
                Log::info("FundService: Found match on FT (exact): " . json_encode($result));
                return $result;
            }

            // 2. Try cleaned name on FT
            $cleanedName = $this->cleanName($name);
            if ($cleanedName !== $name && strlen($cleanedName) > 3) {
                Log::info("FundService: Trying cleaned name: {$cleanedName}");
                $result = $this->searchFT($cleanedName);
                if ($result) {
                    Log::info("FundService: Found match on FT (cleaned): " . json_encode($result));
                    return $result;
                }
            }

            // 3. Try JustETF Search as fallback
            $result = $this->searchJustETF($name);
            if ($result) {
                 Log::info("FundService: Found match on JustETF: " . json_encode($result));
                 return $result;
            }
            
            return null;
        });
    }

    private function cleanName($name)
    {
        // Remove common fund/ETF suffixes and noise words to improve search matching
        $remove = [
            '/\(IE\)/i', '/\(LU\)/i', '/\(FR\)/i', '/\(DE\)/i', // Country codes in parens
            '/ ACC /i', '/ DIST /i', '/ INC /i', // Distribution types
            '/ CLASE S/i', '/ CLASS S/i', '/ CLASS [A-Z]/i', // Classes
            '/ INDEX FUND/i', '/ FUND/i', '/ ETF/i', // Types
            '/ UCITS /i'
        ];
        
        // Add spaces around to ensure matching words
        $cleaned = ' ' . $name . ' ';
        $cleaned = preg_replace($remove, ' ', $cleaned);
        
        // Remove multiple spaces and trim
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        return trim($cleaned);
    }

    private function searchFT($query)
    {
        try {
            $url = "https://markets.ft.com/data/search?query=" . urlencode($query);
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get($url);

            if (!$response->successful()) return null;

            $html = $response->body();
            
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            libxml_clear_errors();

            // Find first row in results table
            $rows = $xpath->query("//tr[@data-mod-primary='true']");

            if ($rows->length > 0) {
                $row = $rows->item(0);
                $cols = $xpath->query(".//td", $row);
                
                if ($cols->length >= 2) {
                    $linkNode = $xpath->query(".//a", $cols->item(0))->item(0);
                    $symbolNode = $cols->item(1); 
                    
                    if ($linkNode && $symbolNode) {
                        $name = trim($linkNode->textContent);
                        $symbol = trim($symbolNode->textContent);
                        
                        return [
                            'name' => $name,
                            'isin' => $symbol 
                        ];
                    }
                }
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error("FT Search Error: " . $e->getMessage());
            return null;
        }
    }

    private function searchJustETF($query)
    {
        // Placeholder for JustETF search if needed
        return null;
    }

    private function scrapeMorningstar($isin)
    {
        try {
            $url = "https://www.morningstar.es/es/funds/Security/Search.aspx?q={$isin}";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get($url);

            if (!$response->successful()) return null;

            $html = $response->body();
            
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            libxml_clear_errors();

            $row = $xpath->query("//tr[td[contains(text(), '{$isin}')]]")->item(0);

            if ($row) {
                $cols = $row->getElementsByTagName('td');
                // Iterate columns to find the price (looks like number with comma)
                for ($i = 3; $i < $cols->length; $i++) {
                     $text = trim($cols->item($i)->textContent);
                     // Match "12,34" or "1.234,56" (European format)
                     if (preg_match('/^\d{1,3}(\.\d{3})*,\d{2}$/', $text)) {
                         
                         $navClean = str_replace('.', '', $text);
                         $navClean = str_replace(',', '.', $navClean);
                         $price = (float) $navClean;
                         
                         if ($price > 0) {
                             // Try to get date from next column
                             $date = Carbon::today()->format('Y-m-d');
                             if ($i + 1 < $cols->length) {
                                 $dateRaw = trim($cols->item($i+1)->textContent);
                                 try {
                                     $date = Carbon::createFromFormat('d/m/Y', $dateRaw)->format('Y-m-d');
                                 } catch (\Exception $e) {}
                             }
                             
                             return ['price' => $price, 'date' => $date];
                         }
                     }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Morningstar Scraping Error: " . $e->getMessage());
            return null;
        }
    }

    private function scrapeJustETF($isin)
    {
        try {
            $url = "https://www.justetf.com/en/etf-profile.html?isin={$isin}";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get($url);

            if (!$response->successful()) return null;

            $html = $response->body();

            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            libxml_clear_errors();
            
            $nodes = $xpath->query("//div[contains(@class, 'val')]");
            foreach ($nodes as $node) {
                $text = trim($node->textContent);
                if (preg_match('/[0-9,\.]+/', $text)) {
                     $clean = preg_replace('/[A-Z]{3}/', '', $text);
                     $clean = trim($clean);
                     
                     $price = (float) str_replace(',', '', $clean);
                     
                     $dateNode = $xpath->query("//div[contains(@class, 'date')]")->item(0);
                     $date = Carbon::today()->format('Y-m-d');
                     if ($dateNode) {
                         $dateText = trim($dateNode->textContent);
                         if (preg_match('/(\d{2})\.(\d{2})\.(\d{4})/', $dateText, $matches)) {
                             $date = "{$matches[3]}-{$matches[2]}-{$matches[1]}";
                         }
                     }
                     
                     if ($price > 0) {
                         return ['price' => $price, 'date' => $date];
                     }
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error("JustETF Scraping Error: " . $e->getMessage());
            return null;
        }
    }

    private function scrapeFinancialTimes($symbol)
    {
        try {
            // If symbol is ISIN, try appending :EUR if not present
            // If it's a ticker (like VHGEX), use as is
            $s = $symbol;
            if (preg_match('/^[A-Z]{2}[A-Z0-9]{9}\d$/', $symbol)) {
                 $s = "{$symbol}:EUR";
            }

            $url = "https://markets.ft.com/data/funds/tearsheet/summary?s={$s}";
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
            ])->get($url);

            if (!$response->successful()) return null;

            $html = $response->body();
            
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            $xpath = new DOMXPath($dom);
            libxml_clear_errors();

            // Look for the price in .mod-ui-data-list__value
            // The first item usually corresponds to the Price/NAV
            $nodes = $xpath->query("//span[contains(@class, 'mod-ui-data-list__value')]");
            
            if ($nodes->length > 0) {
                $text = trim($nodes->item(0)->textContent);
                
                // FT format usually "55.08" (dot decimal)
                // Remove commas just in case (e.g. 1,234.56)
                $clean = str_replace(',', '', $text);
                $price = (float) $clean;

                if ($price > 0) {
                     // Default to today for date
                     $date = Carbon::today()->format('Y-m-d');
                     
                     // Try to find date in "Data as of ..."
                     // Class: mod-tearsheet-overview__quote__timestamp
                     $dateNodes = $xpath->query("//div[contains(@class, 'mod-tearsheet-overview__quote__timestamp')]");
                     if ($dateNodes->length > 0) {
                         $dateText = trim($dateNodes->item(0)->textContent);
                         // "Data as of Feb 14 2025"
                         // Remove "Data as of " prefix
                         $dateText = str_ireplace('Data as of', '', $dateText);
                         $dateText = trim($dateText);
                         
                         try {
                             $date = Carbon::parse($dateText)->format('Y-m-d');
                         } catch (\Exception $e) {}
                     }
                     
                     return ['price' => $price, 'date' => $date];
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error("FinancialTimes Scraping Error: " . $e->getMessage());
            return null;
        }
    }
}
