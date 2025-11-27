<?php

namespace App\Services;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $legacyConfigPath1 = __DIR__ . '/config.php';
        $legacyConfigPath2 = __DIR__ . '/../config.php';
        if (file_exists($legacyConfigPath1)) {
            require_once $legacyConfigPath1;
        } elseif (file_exists($legacyConfigPath2)) {
            require_once $legacyConfigPath2;
        }

        $host = defined('DB_HOST') ? DB_HOST : (config('database.connections.mysql.host') ?? '127.0.0.1');
        $database = defined('DB_NAME') ? DB_NAME : (config('database.connections.mysql.database') ?? 'mi_app');
        $charset = defined('DB_CHARSET') ? DB_CHARSET : (config('database.connections.mysql.charset') ?? 'utf8mb4');
        $username = defined('DB_USER') ? DB_USER : (config('database.connections.mysql.username') ?? 'root');
        $password = defined('DB_PASS') ? DB_PASS : (config('database.connections.mysql.password') ?? '');

        $dsn = "mysql:host={$host};dbname={$database};charset={$charset}";

        try {
            $this->connection = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            $projectRoot = dirname(__DIR__, 2);
            $sqliteFile = $projectRoot . '/database/database.sqlite';
            $fallbackDsn = is_file($sqliteFile) ? 'sqlite:' . $sqliteFile : 'sqlite::memory:';
            try {
                $this->connection = new PDO($fallbackDsn, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e2) {
                $this->connection = new PDO('sqlite::memory:');
            }
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
