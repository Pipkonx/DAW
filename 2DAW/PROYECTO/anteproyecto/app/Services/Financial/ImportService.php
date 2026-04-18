<?php

namespace App\Services\Financial;

use App\Services\Asset\AssetService;
use App\Services\Financial\Import\CsvImportEngine;
use App\Services\Financial\Import\ImportHelper;
use App\Services\Financial\Import\PdfImportEngine;
use App\Services\Financial\Import\OcrImportEngine;

class ImportService
{
    protected $assetService;
    protected $csvEngine;
    protected $pdfEngine;
    protected $ocrEngine;

    public function __construct(
        AssetService $assetService,
        CsvImportEngine $csvEngine,
        PdfImportEngine $pdfEngine,
        OcrImportEngine $ocrEngine
    ) {
        $this->assetService = $assetService;
        $this->csvEngine = $csvEngine;
        $this->pdfEngine = $pdfEngine;
        $this->ocrEngine = $ocrEngine;
    }

    /**
     * Parse a file (CSV, PDF, or Image) and return a list of preview transactions.
     */
    public function previewFromFile($file)
    {
        $extension = strtolower($file->getClientOriginalExtension());
        
        switch ($extension) {
            case 'csv':
            case 'txt':
                return $this->csvEngine->parse($file);
            case 'pdf':
                return $this->pdfEngine->parse($file);
            case 'jpg':
            case 'jpeg':
            case 'png':
                return $this->ocrEngine->parse($file);
            default:
                return [];
        }
    }

    /**
     * Finalize and link a preview transaction.
     */
    public function finalizeTransaction(array $tx, $userId)
    {
        $name = trim($tx['name'] ?? 'Activo Desconocido');
        if (empty($name)) $name = "Activo Desconocido";

        // Calculate price if missing
        if (($tx['quantity'] ?? 0) > 0 && ($tx['amount'] ?? 0) > 0 && ($tx['price_per_unit'] ?? 0) == 0) {
            $tx['price_per_unit'] = $tx['amount'] / $tx['quantity'];
        }

        $typeHint = ImportHelper::guessAssetType($name);
        
        // Use AssetService to find or link
        $asset = $this->assetService->findOrCreateAndLink($userId, [
            'name' => $name,
            'ticker' => $tx['ticker'] ?? $name,
            'type' => $typeHint,
            'price_per_unit' => $tx['price_per_unit'] ?? 0
        ]);

        return array_merge($tx, [
            'asset_id' => $asset->id,
            'ticker' => $asset->ticker,
            'isin' => $asset->isin,
            'asset_type' => $asset->type,
            'name' => $asset->name,
            'link_status' => $asset->market_asset_id ? 'linked' : 'pending',
            'original_name' => $name,
            'nav_date' => $tx['date']
        ]);
    }
}
