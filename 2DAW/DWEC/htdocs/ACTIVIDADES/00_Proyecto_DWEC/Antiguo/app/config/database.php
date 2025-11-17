<?php
// Conexión a MySQL usando PDO con manejo de errores y UTF-8

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $host = getenv('DB_HOST') ?: 'localhost';
            $dbname = getenv('DB_NAME') ?: 'finanzas_db';
            $user = getenv('DB_USER') ?: 'root';
            $password = getenv('DB_PASSWORD') ?: '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

            try {
                self::$instance = new PDO($dsn, $user, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                // Asegurar UTF-8
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
