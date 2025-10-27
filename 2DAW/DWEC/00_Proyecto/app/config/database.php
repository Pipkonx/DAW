<?php
// Conexión a MySQL usando PDO con manejo de errores y UTF-8

class Database {
    public static function getConnection(): PDO {
        $host = getenv('DB_HOST') ?: 'localhost';
        $dbname = getenv('DB_NAME') ?: 'finanzas_db';
        $user = getenv('DB_USER') ?: 'root';
        $password = getenv('DB_PASSWORD') ?: '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host={$host};dbname={$dbname};charset={$charset}";

        try {
            $pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            // Asegurar UTF-8
            $pdo->exec('SET NAMES utf8mb4');
            return $pdo;
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'error' => 'Error de conexión a la base de datos',
                'detail' => $e->getMessage()
            ]));
        }
    }
}

// Utilidad de sesión/CSRF
function ensure_session(): void {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}

function verify_csrf_token(?string $token): bool {
    ensure_session();
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], (string)$token);
}