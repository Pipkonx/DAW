<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Historial de Transacciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #1e293b;
        }
        .header p {
            margin: 5px 0;
            color: #64748b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #e2e8f0;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f1f5f9;
        }
        .text-right {
            text-align: right;
        }
        .type-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .buy { color: #1e40af; background-color: #dbeafe; }
        .sell { color: #9a3412; background-color: #ffedd5; }
        .dividend { color: #065f46; background-color: #d1fae5; }
        .other { color: #475569; background-color: #f1f5f9; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Historial de Transacciones</h1>
        <p>Generado el {{ date('d/m/Y H:i') }}</p>
        <p>Usuario: {{ $user->name }} ({{ $user->email }})</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Activo / Concepto</th>
                <th class="text-right">Cantidad</th>
                <th class="text-right">Precio</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
                @php
                    $typeClass = match($tx->type) {
                        'buy' => 'buy',
                        'sell' => 'sell',
                        'dividend', 'reward' => 'dividend',
                        default => 'other'
                    };
                    $typeName = match($tx->type) {
                        'buy' => 'Compra',
                        'sell' => 'Venta',
                        'dividend' => 'Dividendo',
                        'reward' => 'Recompensa',
                        'gift' => 'Regalo',
                        default => ucfirst($tx->type)
                    };
                    $assetName = $tx->asset ? ($tx->asset->ticker . ' - ' . $tx->asset->name) : ($tx->portfolio ? 'Cartera: ' . $tx->portfolio->name : 'N/A');
                @endphp
                <tr>
                    <td>{{ $tx->date->format('d/m/Y') }}</td>
                    <td><span class="type-badge {{ $typeClass }}">{{ $typeName }}</span></td>
                    <td>{{ $assetName }}</td>
                    <td class="text-right">{{ $tx->quantity != 0 ? rtrim(rtrim(number_format($tx->quantity, 8, ',', '.'), '0'), ',') : '-' }}</td>
                    <td class="text-right">{{ $tx->price != 0 ? number_format($tx->price, 2, ',', '.') . ' €' : '-' }}</td>
                    <td class="text-right">{{ number_format($tx->total, 2, ',', '.') }} €</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="font-size: 10px; color: #94a3b8; text-align: center;">
        <p>Este documento es un resumen informativo y no constituye asesoramiento financiero oficial.</p>
    </div>
</body>
</html>