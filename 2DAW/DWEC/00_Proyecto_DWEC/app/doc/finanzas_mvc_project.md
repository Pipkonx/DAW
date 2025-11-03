# Proyecto: Finanzas personales (MVC) — Código completo

**Estructura del proyecto**

```
finanzas_app/
├─ config/
│  └─ database.php
├─ controllers/
│  ├─ UsuarioController.php
│  └─ FinanzasController.php
├─ models/
│  ├─ Usuario.php
│  └─ Finanzas.php
├─ views/
│  ├─ templates/
│  │  ├─ header.php
│  │  └─ footer.php
│  ├─ login.php
│  ├─ registro.php
│  └─ dashboard.php
├─ public/
│  ├─ index.php
│  ├─ assets/
│  │  ├─ css/style.css
│  │  └─ js/
│  │     ├─ main.js
│  │     └─ graficos.js
├─ helpers/
│  └─ csrf.php
├─ sql/
│  └─ create_finanzas_db.sql
└─ README.md
```

---

## 1) SQL: `sql/create_finanzas_db.sql`

```sql
-- Crear base de datos y tablas para la app finanzas_db
CREATE DATABASE IF NOT EXISTS `finanzas_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `finanzas_db`;

-- Tabla usuarios
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `fecha_registro` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla finanzas
CREATE TABLE IF NOT EXISTS `finanzas` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_usuario` INT UNSIGNED NOT NULL,
  `tipo` ENUM('ingreso','gasto') NOT NULL,
  `monto` DECIMAL(12,2) NOT NULL,
  `descripcion` VARCHAR(255) DEFAULT NULL,
  `fecha_registro` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `idx_usuario` (`id_usuario`),
  CONSTRAINT `fk_finanzas_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 2) Configuración DB: `config/database.php`

```php
<?php
// config/database.php

$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'finanzas_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // En producción mostrar un mensaje genérico
    http_response_code(500);
    echo "Error de conexión a la base de datos.";
    // Para desarrollo puedes descomentar:
    // echo "Conexión fallida: " . $e->getMessage();
    exit;
}
```

---

## 3) Helper CSRF: `helpers/csrf.php`

```php
<?php
// helpers/csrf.php
session_start();

function generarTokenCSRF() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verificarTokenCSRF($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
```

---

## 4) Modelo: `models/Usuario.php`

```php
<?php
// models/Usuario.php
require_once __DIR__ . '/../config/database.php';

class Usuario {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function registrarUsuario($nombre, $email, $password) {
        // Validaciones básicas
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Email inválido'];
        }
        if (strlen($password) < 6) {
            return ['error' => 'La contraseña debe tener al menos 6 caracteres'];
        }

        // Comprobar si ya existe
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if ($stmt->fetch()) {
            return ['error' => 'El email ya está registrado'];
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->pdo->prepare("INSERT INTO usuarios (nombre, email, password_hash, fecha_registro) VALUES (:nombre, :email, :password_hash, NOW())");
        $stmt->execute([
            ':nombre' => $nombre,
            ':email' => $email,
            ':password_hash' => $password_hash
        ]);

        return ['success' => true, 'id' => $this->pdo->lastInsertId()];
    }

    public function iniciarSesion($email, $password) {
        $stmt = $this->pdo->prepare("SELECT id, nombre, email, password_hash FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user) return ['error' => 'Credenciales incorrectas'];

        if (!password_verify($password, $user['password_hash'])) {
            return ['error' => 'Credenciales incorrectas'];
        }

        // Exito
        return ['success' => true, 'user' => ['id' => $user['id'], 'nombre' => $user['nombre'], 'email' => $user['email']]];
    }

    public function obtenerUsuarioPorId($id) {
        $stmt = $this->pdo->prepare("SELECT id, nombre, email, fecha_registro FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
```

---

## 5) Modelo: `models/Finanzas.php`

```php
<?php
// models/Finanzas.php
require_once __DIR__ . '/../config/database.php';

class Finanzas {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function registrarMovimiento($idUsuario, $tipo, $monto, $descripcion, $fecha) {
        // Validaciones básicas
        $tipo = $tipo === 'ingreso' ? 'ingreso' : 'gasto';
        $monto = number_format((float)$monto, 2, '.', '');
        if ($monto <= 0) {
            return ['error' => 'El monto debe ser mayor que 0'];
        }
        $fecha_registro = date('Y-m-d H:i:s', strtotime($fecha));

        $stmt = $this->pdo->prepare("INSERT INTO finanzas (id_usuario, tipo, monto, descripcion, fecha_registro) VALUES (:id_usuario, :tipo, :monto, :descripcion, :fecha_registro)");
        $stmt->execute([
            ':id_usuario' => $idUsuario,
            ':tipo' => $tipo,
            ':monto' => $monto,
            ':descripcion' => $descripcion,
            ':fecha_registro' => $fecha_registro
        ]);

        return ['success' => true, 'id' => $this->pdo->lastInsertId()];
    }

    public function obtenerMovimientosPorUsuario($idUsuario) {
        $stmt = $this->pdo->prepare("SELECT id, tipo, monto, descripcion, fecha_registro FROM finanzas WHERE id_usuario = :id_usuario ORDER BY fecha_registro DESC");
        $stmt->execute([':id_usuario' => $idUsuario]);
        return $stmt->fetchAll();
    }

    public function obtenerResumenAnual($idUsuario, $anio) {
        $params = [':id_usuario' => $idUsuario, ':anio' => $anio];

        // Ingresos totales
        $stmt = $this->pdo->prepare("SELECT IFNULL(SUM(monto),0) as total_ingresos FROM finanzas WHERE id_usuario = :id_usuario AND tipo = 'ingreso' AND YEAR(fecha_registro) = :anio");
        $stmt->execute($params);
        $ingresos = (float)$stmt->fetchColumn();

        // Gastos totales
        $stmt = $this->pdo->prepare("SELECT IFNULL(SUM(monto),0) as total_gastos FROM finanzas WHERE id_usuario = :id_usuario AND tipo = 'gasto' AND YEAR(fecha_registro) = :anio");
        $stmt->execute($params);
        $gastos = (float)$stmt->fetchColumn();

        $ahorro = $ingresos - $gastos;

        return ['ingresos' => $ingresos, 'gastos' => $gastos, 'ahorro' => $ahorro];
    }

    // Método adicional para datos mensuales (para charts)
    public function obtenerIngresosGastosMensuales($idUsuario, $anio) {
        $stmt = $this->pdo->prepare(
            "SELECT MONTH(fecha_registro) AS mes,
                    SUM(CASE WHEN tipo='ingreso' THEN monto ELSE 0 END) AS ingresos,
                    SUM(CASE WHEN tipo='gasto' THEN monto ELSE 0 END) AS gastos
             FROM finanzas
             WHERE id_usuario = :id_usuario AND YEAR(fecha_registro) = :anio
             GROUP BY MONTH(fecha_registro)
             ORDER BY mes"
        );
        $stmt->execute([':id_usuario' => $idUsuario, ':anio' => $anio]);
        return $stmt->fetchAll();
    }
}
```

---

## 6) Controladores

### `controllers/UsuarioController.php`

```php
<?php
// controllers/UsuarioController.php
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/csrf.php';

session_start();
$usuarioModel = new Usuario($pdo);

// Rutas simples basadas en action POST/GET
$action = $_POST['action'] ?? $_GET['action'] ?? null;
header('Content-Type: application/json; charset=utf-8');

if ($action === 'registro') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verificarTokenCSRF($token)) {
        echo json_encode(['error' => 'Token CSRF inválido']); exit;
    }

    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $res = $usuarioModel->registrarUsuario($nombre, $email, $password);
    echo json_encode($res);
    exit;
}

if ($action === 'login') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verificarTokenCSRF($token)) {
        echo json_encode(['error' => 'Token CSRF inválido']); exit;
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $res = $usuarioModel->iniciarSesion($email, $password);
    if (isset($res['success'])) {
        // Crear sesión segura
        session_regenerate_id(true);
        $_SESSION['user'] = $res['user'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode($res);
    }
    exit;
}

if ($action === 'logout') {
    session_start();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// Si llega hasta aquí
http_response_code(400);
echo json_encode(['error' => 'Acción no válida']);
```

---

### `controllers/FinanzasController.php`

```php
<?php
// controllers/FinanzasController.php
require_once __DIR__ . '/../models/Finanzas.php';
require_once __DIR__ . '/../helpers/csrf.php';

session_start();
if (empty($_SESSION['user'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

$finModel = new Finanzas($pdo);
$action = $_POST['action'] ?? $_GET['action'] ?? null;
header('Content-Type: application/json; charset=utf-8');
$userId = (int)$_SESSION['user']['id'];

if ($action === 'registrar') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verificarTokenCSRF($token)) {
        echo json_encode(['error' => 'Token CSRF inválido']); exit;
    }

    $tipo = $_POST['tipo'] ?? 'gasto';
    $monto = $_POST['monto'] ?? 0;
    $descripcion = trim($_POST['descripcion'] ?? '');
    $fecha = $_POST['fecha'] ?? date('Y-m-d H:i:s');

    $res = $finModel->registrarMovimiento($userId, $tipo, $monto, $descripcion, $fecha);
    echo json_encode($res);
    exit;
}

if ($action === 'listar') {
    $movs = $finModel->obtenerMovimientosPorUsuario($userId);
    echo json_encode(['movimientos' => $movs]);
    exit;
}

if ($action === 'resumen_anual') {
    $anio = (int)($_GET['anio'] ?? date('Y'));
    $resumen = $finModel->obtenerResumenAnual($userId, $anio);
    echo json_encode(['resumen' => $resumen]);
    exit;
}

if ($action === 'mensual_chart') {
    $anio = (int)($_GET['anio'] ?? date('Y'));
    $data = $finModel->obtenerIngresosGastosMensuales($userId, $anio);
    echo json_encode(['data' => $data]);
    exit;
}

http_response_code(400);
echo json_encode(['error' => 'Acción no válida']);
```

---

## 7) Vistas y plantillas

### `views/templates/header.php`

```php
<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../../helpers/csrf.php';
$csrf = generarTokenCSRF();
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Finanzas Personales</title>
  <link href="/public/assets/css/style.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
  <script>const CSRF_TOKEN = '<?= htmlspecialchars($csrf, ENT_QUOTES) ?>';</script>
</head>
<body>
<header class="topbar">
  <div class="container">
    <h1>Finanzas</h1>
    <?php if (!empty($_SESSION['user'])): ?>
      <div class="user-info">Bienvenido, <?=htmlspecialchars($_SESSION['user']['nombre'])?> | <a href="#" id="logoutBtn">Salir</a></div>
    <?php endif; ?>
  </div>
</header>
<main class="container">
```

### `views/templates/footer.php`

```php
</main>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/public/assets/js/main.js"></script>
<script src="/public/assets/js/graficos.js"></script>
</body>
</html>
```

---

### `views/registro.php`

```php
<?php require_once __DIR__ . '/templates/header.php'; ?>
<section class="card">
  <h2>Registro</h2>
  <form id="registroForm" method="post">
    <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrf)?>">
    <label>Nombre<input type="text" name="nombre" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Contraseña<input type="password" name="password" required minlength="6"></label>
    <button type="submit">Registrar</button>
    <div id="registroMsg" class="msg"></div>
  </form>
  <p>¿Ya tienes cuenta? <a href="/public/index.php?page=login">Inicia sesión</a></p>
</section>
<?php require_once __DIR__ . '/templates/footer.php'; ?>
```

---

### `views/login.php`

```php
<?php require_once __DIR__ . '/templates/header.php'; ?>
<section class="card">
  <h2>Iniciar sesión</h2>
  <form id="loginForm" method="post">
    <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrf)?>">
    <label>Email<input type="email" name="email" required></label>
    <label>Contraseña<input type="password" name="password" required></label>
    <button type="submit">Entrar</button>
    <div id="loginMsg" class="msg"></div>
  </form>
  <p>¿No tienes cuenta? <a href="/public/index.php?page=registro">Regístrate</a></p>
</section>
<?php require_once __DIR__ . '/templates/footer.php'; ?>
```

---

### `views/dashboard.php`

```php
<?php
require_once __DIR__ . '/templates/header.php';
if (empty($_SESSION['user'])) {
    header('Location: /public/index.php?page=login'); exit;
}
?>
<section class="grid">
  <div class="card">
    <h2>Agregar movimiento</h2>
    <form id="movForm">
      <input type="hidden" name="csrf_token" value="<?=htmlspecialchars($csrf)?>">
      <label>Tipo
        <select name="tipo" required>
          <option value="ingreso">Ingreso</option>
          <option value="gasto">Gasto</option>
        </select>
      </label>
      <label>Monto<input type="number" name="monto" step="0.01" required></label>
      <label>Descripción<input type="text" name="descripcion"></label>
      <label>Fecha<input type="datetime-local" name="fecha"></label>
      <button type="submit">Guardar</button>
      <div id="movMsg" class="msg"></div>
    </form>
  </div>

  <div class="card">
    <h2>Movimientos</h2>
    <table id="movTable">
      <thead><tr><th>Tipo</th><th>Monto</th><th>Descripción</th><th>Fecha</th></tr></thead>
      <tbody></tbody>
    </table>
  </div>

  <div class="card">
    <h2>Resumen anual</h2>
    <label>Año <input type="number" id="anioInput" value="<?=date('Y')?>"></label>
    <div id="resumen"></div>
    <canvas id="barChart" height="120"></canvas>
    <canvas id="pieChart" height="120"></canvas>
  </div>
</section>
<?php require_once __DIR__ . '/templates/footer.php'; ?>
```

---

## 8) Public entry: `public/index.php`

```php
<?php
// public/index.php
// En un entorno real configurar virtualhost y rewrite rules
require_once __DIR__ . '/../config/database.php';

$page = $_GET['page'] ?? 'login';
$viewsPath = __DIR__ . '/../views/';

switch ($page) {
    case 'registro':
        require $viewsPath . 'registro.php';
        break;
    case 'dashboard':
        require $viewsPath . 'dashboard.php';
        break;
    case 'logout':
        // Llamar controller para logout
        require_once __DIR__ . '/../controllers/UsuarioController.php';
        break;
    case 'login':
    default:
        require $viewsPath . 'login.php';
        break;
}
```

---

## 9) Assets: CSS `public/assets/css/style.css`

```css
:root{--bg:#f6f7fb;--card:#ffffff;--muted:#6b7280;--accent:#6b8cff}
*{box-sizing:border-box;font-family:Inter,system-ui,Arial}
body{margin:0;background:var(--bg);color:#111}
.container{max-width:1000px;margin:24px auto;padding:0 16px}
.topbar{background:#fff;padding:12px 0;border-bottom:1px solid #ececec}
.topbar .container{display:flex;justify-content:space-between;align-items:center}
.card{background:var(--card);padding:16px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,0.03);margin-bottom:16px}
.grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
label{display:block;margin-bottom:8px}
input,select,button{width:100%;padding:8px;border-radius:8px;border:1px solid #e6e6e6}
button{cursor:pointer}
table{width:100%;border-collapse:collapse}
th,td{padding:8px;border-bottom:1px solid #f0f0f0;text-align:left}
.msg{margin-top:8px;color:var(--muted)}
@media(max-width:800px){.grid{grid-template-columns:1fr}}
```

---

## 10) JavaScript frontend

### `public/assets/js/main.js`

```javascript
// public/assets/js/main.js

async function postJSON(url, data){
  const res = await fetch(url, {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  });
  return res.json();
}

// Manejo registro
const registroForm = document.getElementById('registroForm');
if (registroForm){
  registroForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(registroForm);
    const payload = {
      action: 'registro',
      nombre: fd.get('nombre'),
      email: fd.get('email'),
      password: fd.get('password'),
      csrf_token: fd.get('csrf_token')
    };
    const res = await postJSON('/controllers/UsuarioController.php', payload);
    const msg = document.getElementById('registroMsg');
    if (res.success) {
      msg.textContent = 'Registrado con éxito. Redirigiendo...';
      setTimeout(()=> location.href = '/public/index.php?page=login', 800);
    } else {
      msg.textContent = res.error || 'Error';
    }
  });
}

// Manejo login
const loginForm = document.getElementById('loginForm');
if (loginForm){
  loginForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(loginForm);
    const payload = {action:'login', email: fd.get('email'), password: fd.get('password'), csrf_token: fd.get('csrf_token')};
    const res = await postJSON('/controllers/UsuarioController.php', payload);
    const msg = document.getElementById('loginMsg');
    if (res.success) {
      msg.textContent = 'Acceso correcto. Redirigiendo...';
      setTimeout(()=> location.href = '/public/index.php?page=dashboard', 500);
    } else {
      msg.textContent = res.error || 'Error';
    }
  });
}

// Logout
const logoutBtn = document.getElementById('logoutBtn');
if (logoutBtn) logoutBtn.addEventListener('click', async (e)=>{
  e.preventDefault();
  const res = await postJSON('/controllers/UsuarioController.php', {action:'logout'});
  if (res.success) location.href = '/public/index.php?page=login';
});

// Movimientos (dashboard)
const movForm = document.getElementById('movForm');
if (movForm){
  movForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(movForm);
    const payload = {
      action: 'registrar',
      tipo: fd.get('tipo'),
      monto: fd.get('monto'),
      descripcion: fd.get('descripcion'),
      fecha: fd.get('fecha') ? new Date(fd.get('fecha')).toISOString().slice(0,19).replace('T',' ') : new Date().toISOString().slice(0,19).replace('T',' '),
      csrf_token: fd.get('csrf_token')
    };
    const res = await postJSON('/controllers/FinanzasController.php', payload);
    const msg = document.getElementById('movMsg');
    if (res.success) {
      msg.textContent = 'Movimiento guardado';
      movForm.reset();
      cargarMovimientos();
      cargarGraficos();
    } else {
      msg.textContent = res.error || 'Error';
    }
  });
}

async function cargarMovimientos(){
  const res = await fetch('/controllers/FinanzasController.php?action=listar');
  const json = await res.json();
  const tbody = document.querySelector('#movTable tbody');
  tbody.innerHTML = '';
  if (json.movimientos) {
    json.movimientos.forEach(m => {
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${m.tipo}</td><td>${parseFloat(m.monto).toFixed(2)}</td><td>${escapeHtml(m.descripcion||'')}</td><td>${m.fecha_registro}</td>`;
      tbody.appendChild(tr);
    });
  }
}

function escapeHtml(text){
  return text ? text.replace(/[&<>\"]/g, function(c){return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c];}) : '';
}

// Inicialización dashboard
if (document.location.pathname.includes('dashboard')){
  cargarMovimientos();
  cargarGraficos();
  document.getElementById('anioInput').addEventListener('change', cargarGraficos);
}
```

---

### `public/assets/js/graficos.js`

```javascript
// public/assets/js/graficos.js

let barChart, pieChart;

async function cargarGraficos(){
  const anio = document.getElementById('anioInput') ? document.getElementById('anioInput').value : new Date().getFullYear();

  // Datos mensuales
  const res = await fetch('/controllers/FinanzasController.php?action=mensual_chart&anio=' + encodeURIComponent(anio));
  const json = await res.json();
  const meses = Array.from({length:12}, (_,i) => i+1);
  const ingresosArr = new Array(12).fill(0);
  const gastosArr = new Array(12).fill(0);
  if (json.data) {
    json.data.forEach(r => {
      const idx = r.mes - 1;
      ingresosArr[idx] = parseFloat(r.ingresos) || 0;
      gastosArr[idx] = parseFloat(r.gastos) || 0;
    });
  }

  const ctxBar = document.getElementById('barChart').getContext('2d');
  if (barChart) barChart.destroy();
  barChart = new Chart(ctxBar, {
    type: 'bar',
    data: {
      labels: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
      datasets: [
        {label:'Ingresos', data: ingresosArr},
        {label:'Gastos', data: gastosArr}
      ]
    },
    options: {responsive:true, maintainAspectRatio:false}
  });

  // Pie: ahorro vs gasto total
  const resumenRes = await fetch('/controllers/FinanzasController.php?action=resumen_anual&anio=' + encodeURIComponent(anio));
  const resumenJson = await resumenRes.json();
  const ingresosTot = resumenJson.resumen ? resumenJson.resumen.ingresos : 0;
  const gastosTot = resumenJson.resumen ? resumenJson.resumen.gastos : 0;
  const ahorro = ingresosTot - gastosTot;

  const ctxPie = document.getElementById('pieChart').getContext('2d');
  if (pieChart) pieChart.destroy();
  pieChart = new Chart(ctxPie, {
    type: 'pie',
    data: {
      labels: ['Ahorro','Gastos'],
      datasets: [{data: [Math.max(0, ahorro), Math.max(0,gastosTot)]}]
    },
    options: {responsive:true, maintainAspectRatio:false}
  });

  // Mostrar resumen textual
  const resumenDiv = document.getElementById('resumen');
  resumenDiv.innerHTML = `<p>Total ingresos: ${parseFloat(ingresosTot).toFixed(2)}</p><p>Total gastos: ${parseFloat(gastosTot).toFixed(2)}</p><p>Ahorro: ${parseFloat(ahorro).toFixed(2)}</p>`;
}
```

---

## 11) README.md

```md
# Finanzas Personales (PHP + JS) — Instalación

## Requisitos
- PHP 8+
- MySQL 5.7+
- Servidor local (XAMPP, Laragon, Valet, etc.)

## Pasos
1. Crear la base de datos: Ejecuta el script `sql/create_finanzas_db.sql` en tu servidor MySQL.
2. Configurar credenciales en `config/database.php` o usando variables de entorno `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
3. Colocar la carpeta `public/` como raíz del servidor web (DocumentRoot). Por ejemplo en XAMPP `htdocs/finanzas_app/public`.
4. Asegúrate de tener `mod_rewrite` activo si quieres rutas amigables.
5. Abrir en el navegador: `http://localhost/finanzas_app/public/index.php`.

## Notas de seguridad
- En producción deshabilita la impresión de errores y usa HTTPS.
- Ajusta `session.cookie_secure`, `session.cookie_httponly` y `session.use_strict_mode` en php.ini.

## Estructura
(Ver encabezado del proyecto en este documento.)

## Ejemplo de configuración (local)
```php
$host = "localhost";
$dbname = "finanzas_db";
$user = "root";
$password = "";
```

```

---

## 12) Observaciones finales

- Este proyecto sigue el patrón MVC de forma sencilla: `models/`, `controllers/`, `views/`.
- Se utilizan `password_hash` y `password_verify` para contraseñas.
- Se incluyen tokens CSRF básicos y validación tanto en JS como en PHP.
- Los controladores devuelven JSON para integrarse con `fetch()` y AJAX.

¡Listo! He generado el código completo y la guía para levantar la aplicación.

Puedes pedirme que genere archivos descargables (ZIP) o que adapte rutas/estilos/idiomas específicos.

