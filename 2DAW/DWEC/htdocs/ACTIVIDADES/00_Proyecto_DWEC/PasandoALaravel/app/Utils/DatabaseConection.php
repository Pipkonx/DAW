<?php

namespace App\Utils;

use PDO;
use PDOException;

class DatabaseConection
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $host = env('DB_HOST', '127.0.0.1');
            $dbname = env('DB_DATABASE', 'finanzas_db');
            $user = env('DB_USERNAME', 'root');
            $password = env('DB_PASSWORD', '');
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);

                self::$instance->exec('SET NAMES utf8mb4');

            } catch (PDOException $e) {
                http_response_code(500);
                die(json_encode([
                    'success' => false,
                    'error' => 'Error de conexión a la base de datos',
                    'detail' => $e->getMessage()
                ]));
            }
        }

        return self::$instance;
    }
}
