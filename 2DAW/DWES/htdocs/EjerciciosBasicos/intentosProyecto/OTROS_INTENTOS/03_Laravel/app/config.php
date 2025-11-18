<?php
// Configuración legacy basada en constantes, compatible con el proyecto 01_MVC_PDO_Singleton
// Se alimenta de .env de Laravel si existen esas variables

if (!defined('DB_HOST')) {
    define('DB_HOST', env('DB_HOST', '127.0.0.1'));
}

if (!defined('DB_NAME')) {
    define('DB_NAME', env('DB_DATABASE', 'mi_app'));
}

if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));
}

if (!defined('DB_USER')) {
    define('DB_USER', env('DB_USERNAME', 'root'));
}

if (!defined('DB_PASS')) {
    define('DB_PASS', env('DB_PASSWORD', ''));
}