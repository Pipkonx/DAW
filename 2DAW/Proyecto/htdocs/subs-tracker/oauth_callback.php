<?php
require __DIR__ . '/vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/client_secret.json');
$client->addScope("email");
$client->addScope("profile");
$client->setRedirectUri('http://localhost/subs-tracker/oauth_callback.php');

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token);

    $oauth = new Google_Service_Oauth2($client);
    $userInfo = $oauth->userinfo->get();

    $_SESSION['user_email'] = $userInfo->email;
    $_SESSION['user_name'] = $userInfo->name;

    // Redirige a tu dashboard
    header('Location: dashboard.php');
    exit;
} else {
    echo "Error: no se recibió código de autorización.";
}
