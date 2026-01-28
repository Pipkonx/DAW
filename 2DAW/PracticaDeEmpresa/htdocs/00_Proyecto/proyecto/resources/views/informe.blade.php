<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Informe de Prácticas - {{ $alumno->nombre }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; }
        .section { margin-bottom: 20px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #eee; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Informe de Seguimiento de Prácticas</h1>
        <p>Generado el {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <div class="section-title">Datos del Alumno</div>
        <table>
            <tr>
                <th>Nombre:</th>
                <td>{{ $alumno->nombre }} {{ $alumno->apellidos }}</td>
                <th>DNI:</th>
                <td>{{ $alumno->dni }}</td>
            </tr>
            <tr>
                <th>Email:</th>
                <td>{{ $alumno->email }}</td>
                <th>Teléfono:</th>
                <td>{{ $alumno->telefono }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Empresa y Tutor</div>
        <table>
            <tr>
                <th>Empresa:</th>
                <td>{{ $alumno->empresa?->nombre ?? 'N/A' }}</td>
            </tr>
            <tr>
                <th>Tutor de Empresa:</th>
                <td>{{ $alumno->tutorEmpresa?->name ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Observaciones Diarias</div>
        @if($alumno->observacionesDiarias->count() > 0)
            <table>
                <thead>
                    <tr>
                        <th style="width: 15%;">Fecha</th>
                        <th style="width: 10%;">Horas</th>
                        <th>Actividades</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($alumno->observacionesDiarias as $obs)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($obs->fecha)->format('d/m/Y') }}</td>
                            <td>{{ $obs->horasRealizadas }}</td>
                            <td>{{ $obs->actividades }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>No hay observaciones registradas hasta la fecha.</p>
        @endif
    </div>

    <div class="footer">
        Este documento es un informe oficial de seguimiento de prácticas en empresa.
    </div>
</body>
</html>
