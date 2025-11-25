<?php

namespace App\DB;

use PDO;

// class BD
// {
//     private static $instancia = null;
//
//     public static function getInstance(): PDO
//
//     {
//         if (self::$instancia === null) {
//             $host = getenv('DB_HOST') ?: '127.0.0.1';
//             $port = getenv('DB_PORT') ?: '3306';
//             $db   = getenv('DB_DATABASE') ?: 'laravel';
//             $user = getenv('DB_USERNAME') ?: 'root';
//             $pass = getenv('DB_PASSWORD') ?: '';
//             $charset = 'utf8mb4';
//
//             $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
//             $opt = [
//                 PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//                 PDO::ATTR_EMULATE_PREPARES => false,
//             ];
//             self::$instancia = new PDO($dsn, $user, $pass, $opt);
//         }
//         return self::$instancia;
//     }
// }


/**
 * Singleton de conexión a base de datos (PDO) para la aplicación.
 * Lee configuración desde `app/Config/config.php` y entrega una única instancia.
 */


// CONEXION SINGLESTONE
class DB
{
    /**
     * Instancia única de PDO compartida en toda la aplicación.
     *
     * @var PDO|null
     */
    private static $instancia = null;

    /**
     * Constructor privado para evitar instanciación directa.
     */
    private function __construct() {}

    /**
     * Devuelve la instancia única de PDO, creándola si no existe.
     *
     * @return PDO Conexión PDO configurada.
     */
    public static function getInstance(): PDO
    {
        if (self::$instancia === null) {
            $config = require __DIR__ . '/../Config/config.php';
            $dsn = "mysql:host={$config['host']};dbname={$config['db']};charset=utf8mb4";
            $opt = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            self::$instancia = new PDO($dsn, $config['user'], $config['pass'], $opt);
        }
        return self::$instancia;
    }
}
