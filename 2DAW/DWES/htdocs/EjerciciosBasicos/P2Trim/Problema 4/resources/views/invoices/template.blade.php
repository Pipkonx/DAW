<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Factura #{{ $fee->id }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, .15); font-size: 16px; line-height: 24px; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.top table td.title { font-size: 45px; line-height: 45px; color: #333; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.item.last td { border-bottom: none; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="title">
                                <span style="color: #4e73df;">Empresa S.A.</span>
                            </td>
                            <td>
                                Factura #: {{ $fee->id }}<br>
                                Fecha: {{ \Carbon\Carbon::parse($fee->emission_date)->format('d/m/Y') }}<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Emisor:</strong><br>
                                Empresa de Servicios S.A.<br>
                                CIF: A12345678<br>
                                Calle Falsa 123<br>
                                Madrid, 28001
                            </td>
                            <td>
                                <strong>Cliente:</strong><br>
                                {{ $fee->client->name }}<br>
                                CIF: {{ $fee->client->cif }}<br>
                                {{ $fee->client->email }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Concepto</td>
                <td>Importe</td>
            </tr>
            <tr class="item last">
                <td>{{ $fee->concept }}</td>
                <td>{{ number_format($fee->amount, 2) }} {{ $fee->client->currency }}</td>
            </tr>
            <tr class="total">
                <td></td>
                <td>Total: {{ number_format($fee->amount, 2) }} {{ $fee->client->currency }}</td>
            </tr>
        </table>
        <div style="margin-top: 50px; font-size: 12px; color: #777;">
            <p><strong>Notas:</strong> {{ $fee->notes ?? 'Sin notas adicionales.' }}</p>
            <p><strong>Forma de Pago:</strong> Transferencia Bancaria al IBAN: {{ $fee->client->bank_account }}</p>
        </div>
    </div>
</body>
</html>