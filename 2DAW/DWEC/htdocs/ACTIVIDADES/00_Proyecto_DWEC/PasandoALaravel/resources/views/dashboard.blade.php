<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DAW Finance</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="app-url" content="{{ url('/') }}">
    <style>
        select {
            width: 100%;
            margin-top: 6px;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(0, 0, 0, 0.3);
            color: var(--text);
        }
        table {
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        th {
            color: var(--muted);
            font-weight: normal;
            text-transform: uppercase;
            font-size: 0.8rem;
        }
        canvas {
            margin-top: 20px;
            max-height: 250px;
        }
        .logout-container {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <h1>Mi Panel Financiero</h1>
    </header>
    <main class="container">
        <section class="form-card">
            <h2>Nuevo Movimiento</h2>
            <form id="movForm">
                @csrf
                <label for="tipo">Tipo de Operación</label>
                <select id="tipo" name="tipo" required>
                    <option value="ingreso">Ingreso (+)</option>
                    <option value="gasto">Gasto (-)</option>
                </select>

                <label for="monto">Monto (€)</label>
                <input type="number" id="monto" name="monto" step="0.01" min="0.01" required placeholder="0.00">

                <label for="descripcion">Concepto / Descripción</label>
                <input type="text" id="descripcion" name="descripcion" placeholder="Ej. Compra supermercado">

                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="{{ date('Y-m-d') }}" required>

                <button type="submit" class="btn">Registrar Movimiento</button>
            </form>
        </section>

        <section class="card" style="margin-top: 24px;">
            <h2>Historial Reciente</h2>
            <div style="overflow-x: auto;">
                <table width="100%">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Monto</th>
                            <th>Descripción</th>
                        </tr>
                    </thead>
                    <tbody id="movementsBody">
                        <!-- Se carga vía JS -->
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card" style="margin-top: 24px;">
            <h2>Visualización de Datos</h2>
            <div>
                <canvas id="chartMensual"></canvas>
            </div>
            <div style="margin-top: 30px;">
                <canvas id="chartAhorro"></canvas>
            </div>
        </section>

        <div class="logout-container">
            <a href="#" id="logoutLink" class="btn secondary">Cerrar Sesión</a>
        </div>
    </main>

    <footer>
        <p>&copy; 2026 Gestión de Finanzas Personales</p>
    </footer>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module" src="{{ asset('js/main.js') }}"></script>
</body>
</html>
