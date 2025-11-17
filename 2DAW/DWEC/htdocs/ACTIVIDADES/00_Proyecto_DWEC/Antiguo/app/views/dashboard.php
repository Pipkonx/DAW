<?php
require_once __DIR__ . '/../config/database.php';
ensure_session();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$csrf = $_SESSION['csrf_token'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../public/css/styles.css">
</head>
<body>
    <header>
        <h1>Tu Panel Financiero</h1>
    </header>
    <main class="container">
        <section class="form-card">
            <h2>Agregar movimiento</h2>
            <form id="movForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf) ?>">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" required>
                    <option value="ingreso">Ingreso</option>
                    <option value="gasto">Gasto</option>
                </select>

                <label for="monto">Monto</label>
                <input type="number" id="monto" name="monto" step="0.01" min="0" required>

                <label for="descripcion">Descripción</label>
                <input type="text" id="descripcion" name="descripcion" placeholder="Opcional">

                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" value="<?= date('Y-m-d') ?>" required>

                <button type="submit" class="btn">Guardar</button>
            </form>
        </section>

        <section class="card">
            <h2>Historial de movimientos</h2>
            <table width="100%">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody id="movementsBody"></tbody>
            </table>
        </section>

        <section class="card">
            <h2>Gráficos</h2>
            <canvas id="chartMensual" height="120"></canvas>
            <canvas id="chartAhorro" height="120"></canvas>
        </section>
    </main>

    <footer>
        <small><a href="#" id="logoutLink">Cerrar sesión</a></small>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script type="module" src="../../public/js/graficos.js"></script>
    <script type="module" src="../../public/js/main.js"></script>
</body>
</html>