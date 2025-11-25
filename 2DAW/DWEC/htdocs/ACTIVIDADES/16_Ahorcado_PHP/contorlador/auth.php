<?php
require_once __DIR__ . '/../conexion/DB.php';
require_once __DIR__ . '/utils.php';

// Conexión PDO
$pdo = Database::getInstance()->getConnection();

//! LOGIN ver si puedo poner directamente $_POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        redirect('../vistas/autentificarse/login.php?error=Credenciales+inv%C3%A1lidas');
    }

    $stmt = $pdo->prepare('SELECT id_jugador, contrasena FROM JUGADORES WHERE login = ? LIMIT 1');
    $stmt->execute([$login]);
    $user = $stmt->fetch();

    if (!$user) {
        redirect('../vistas/autentificarse/login.php?error=Usuario+no+encontrado');
    }

    $hash = $user['contrasena'];
    //! PARA MEJORARLO PODEMOS HASHEAR LA CONTRASENNA Y LUEGO COMPARAMOS LAS CONTRASENNAS HASEADAS
    $ok = $hash === $password;

    if (!$ok) {
        redirect('../vistas/autentificarse/login.php?error=Contrase%C3%B1a+incorrecta');
    }

    //REDIRECT es para redirigir a otra pagina y el URLENCODE para codificar el login para que no se produzcan errores en la url
    redirect('../vistas/juego/configurar.php?login=' . urlencode($login));
}

if ($_POST) {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';

    if ($login === '' || $password === '' || $confirm === '') {
        redirect('../vistas/autentificarse/register.php?error=Completa+todos+los+campos');
    }
    if ($password !== $confirm) {
        redirect('../vistas/autentificarse/register.php?error=Las+contrase%C3%B1as+no+coinciden');
    }

    // Comprobar si existe
    $check = $pdo->prepare('SELECT COUNT(*) AS c FROM JUGADORES WHERE login = ?');
    $check->execute([$login]);
    $exists = (int)$check->fetchColumn() > 0;
    if ($exists) {
        redirect('../vistas/autentificarse/register.php?error=El+usuario+ya+existe');
    }

    // Guardar contraseña en texto
    $ins = $pdo->prepare('INSERT INTO JUGADORES (login, contrasena, es_administrador) VALUES (?, ?, 0)');
    $ins->execute([$login, $password]);

    redirect('../vistas/autentificarse/login.php?ok=Registro+completado&login=' . urlencode($login));
}

http_response_code(400);
echo 'Acción no soportada';
