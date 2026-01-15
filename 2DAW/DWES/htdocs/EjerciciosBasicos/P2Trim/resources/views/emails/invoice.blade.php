<x-mail::message>
# Hola {{ $fee->client->name }},

Le adjuntamos la factura correspondiente a la cuota: **{{ $fee->concept }}**.

**Detalles de la factura:**
- **Número de Factura:** #{{ $fee->id }}
- **Fecha de Emisión:** {{ \Carbon\Carbon::parse($fee->emission_date)->format('d/m/Y') }}
- **Importe Total:** {{ number_format($fee->amount, 2) }} {{ $fee->client->currency }}

Puede encontrar la factura detallada en el archivo PDF adjunto a este correo.

Gracias por confiar en nosotros,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>