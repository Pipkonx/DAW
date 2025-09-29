<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

// Cargar variables del archivo .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Configuración del cliente Google
$client = new Google_Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setRedirectUri($_ENV['GOOGLE_REDIRECT_URI']);
$client->addScope("email");
$client->addScope("profile");

// Si no hay código en la URL, redirige al login de Google
if (!isset($_GET['code'])) {
    $auth_url = $client->createAuthUrl();
    header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    exit;
} else {
    // Intercambiar el código por un token de acceso
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

    if (isset($token['error'])) {
        die('Error en el token: ' . htmlspecialchars($token['error_description'] ?? $token['error']));
    }

    $client->setAccessToken($token);

    // Obtener datos del usuario
    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    // Guardar en sesión
    $_SESSION['user_email'] = $userInfo->email;
    $_SESSION['user_name'] = $userInfo->name;

    // Redirigir al dashboard
    header('Location: dashboard.php');
    exit;
}
