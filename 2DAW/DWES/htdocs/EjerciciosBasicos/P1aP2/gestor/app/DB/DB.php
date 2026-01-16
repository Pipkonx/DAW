<?php

namespace App\DB;

use PDO;

/**
 * CONEXION SINGLESTONE
 * Lee configuración viene de `app/Config/config.php`
 */

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
