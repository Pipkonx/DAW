<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Http;

/**
 * Clase Fee (Cuota o Factura)
 * 
 * Representa los cargos económicos que se realizan a los clientes.
 * Incluye lógica para gestionar pagos en multidivisa y conversión a Euros.
 */
class Fee extends Model
{
    /**
     * Campos que se pueden asignar masivamente.
     * @var array<string>
     */
    protected $fillable = [
        'client_id', 'concept', 'emission_date', 'amount', 
        'is_paid', 'payment_date', 'notes', 'invoice_path',
        'amount_eur', 'exchange_rate'
    ];

    /**
     * Relación: Una cuota pertenece obligatoriamente a un Cliente.
     * @return BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Marca la cuotas como pagada y calcula el importe en Euros usando una API externa.
     * Esta lógica SE MUEVE aquí para que el controlador sea más sencillo y limpio.
     * 
     * @return bool Indica si el proceso se completó con éxito.
     */
    public function markAsPaid(): bool
    {
        // Cargamos los datos del cliente para saber su moneda
        $this->load('client');
        $currency = strtolower($this->client->currency);
        
        $exchangeRate = 1;
        $amountEur = $this->amount;

        // Si la moneda no es Euro, consultamos el cambio actual
        if ($currency !== 'eur') {
            try {
                // OBLIGATORIO: Uso de HttpClient (vía el Facade Http) para consultar la API
                $response = Http::get("https://cdn.jsdelivr.net/npm/@fawazahmed0/currency-api@latest/v1/currencies/{$currency}.json");
                
                if ($response->successful()) {
                    $rates = $response->json();
                    $exchangeRate = $rates[$currency]['eur'] ?? 1;
                    $amountEur = $this->amount * $exchangeRate;
                }
            } catch (\Exception $e) {
                // Si falla la API, mantenemos el valor original por seguridad
            }
        }

        // Actualizamos los datos del registro
        return $this->update([
            'is_paid' => 'S',
            'payment_date' => now()->toDateString(),
            'exchange_rate' => $exchangeRate,
            'amount_eur' => $amountEur,
        ]);
    }
}


