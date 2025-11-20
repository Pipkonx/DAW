<?php

namespace App\DB;

use PDO;

class DB
{
    private static ?PDO $instancia = null;

    public static function getInstance(): PDO
    {
        if (self::$instancia === null) {
            $config = require __DIR__ . '/../Config/config.php';
            $host = $config['host'] ?? '127.0.0.1';
            $db = $config['db'] ?? 'laravel';
            $user = $config['user'] ?? 'root';
            $pass = $config['pass'] ?? '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
            $opt = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            self::$instancia = new PDO($dsn, $user, $pass, $opt);
        }
        return self::$instancia;
    }
}
