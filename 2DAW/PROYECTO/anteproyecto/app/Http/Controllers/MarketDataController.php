<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketAsset;
use App\Services\MarketDataService;
use Carbon\Carbon;

class MarketDataController extends Controller
{
    protected $marketData;

    public function __construct(MarketDataService $marketData)
    {
        $this->marketData = $marketData;
    }

    /**
     * Search for assets by ticker, name, or ISIN.
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $type = $request->input('type'); // Get asset type
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        // Delegate search to service (handles API + Local DB)
        $assets = $this->marketData->search($query, $type);

        return response()->json($assets);
    }

    /**
     * Get historical price for an asset on a specific date.
     */
    public function getPrice(Request $request)
    {
        $request->validate([
            'market_asset_id' => 'nullable|integer|exists:market_assets,id',
            'ticker' => 'required_without:market_asset_id|string',
            'date' => 'required|date',
        ]);

        $date = $request->date;
        $asset = null;

        if ($request->market_asset_id) {
            $asset = MarketAsset::find($request->market_asset_id);
        } elseif ($request->ticker) {
            $ticker = strtoupper($request->ticker);
            $asset = MarketAsset::where('ticker', $ticker)->first();
            
            if (!$asset) {
                 // Try to find it via service search if not in DB
                 $results = $this->marketData->search($ticker);
                 $asset = $results->firstWhere('ticker', $ticker);
            }
        }

        if (!$asset) {
            return response()->json(['error' => 'Asset not found'], 404);
        }

        $price = $this->marketData->getHistoricalPrice($asset, $date);

        if ($price === null) {
             return response()->json(['error' => 'Price not available'], 404);
        }

        return response()->json([
            'ticker' => $asset->ticker,
            'price' => $price,
            'date' => $date,
            'currency' => $asset->currency_code,
            'source' => 'MarketDataService'
        ]);
    }
}
