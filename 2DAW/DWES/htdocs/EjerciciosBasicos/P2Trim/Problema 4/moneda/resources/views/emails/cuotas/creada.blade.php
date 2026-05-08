<x-mail::message>
# Hola {{ $clientName }},

Se ha generado una nueva cuota en su cuenta de **Nosecaen S.L.**.

**Detalles de la cuota:**
- **Concepto:** {{ $cuota->concept }}
- **Importe:** {{ number_format($amount, 2) }} {{ $currency }}

Puede realizar el pago de forma segura a través de nuestra plataforma.

<x-mail::button :url="config('app.url') . '/login'">
Acceder al Panel de Pago
</x-mail::button>

Gracias,<br>
El equipo de {{ config('app.name') }}
</x-mail::message>
