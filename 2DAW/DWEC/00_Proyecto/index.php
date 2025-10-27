<?php
// Archivo principal del proyecto - redirige al Index.html o maneja rutas
session_start();

// Si el usuario ya está logueado, redirigir al dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: app/views/dashboard.php');
    exit;
}

// Si no está logueado, mostrar la página principal
header('Location: app/Index.html');
exit;
?>