<?php
require_once __DIR__ . '/../conexion/config.php';

class Jugador
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = DB::getInstance()->getConnection();
    }
    public function getIdByLogin(string $login): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id_jugador FROM JUGADORES WHERE login = ? LIMIT 1');
        $stmt->execute([$login]);
        $row = $stmt->fetch();
        return $row ? intval($row['id_jugador']) : null;
    }

    public function verifyCredentials(string $login, string $password): bool
    {
        $stmt = $this->pdo->prepare('SELECT contrasena FROM JUGADORES WHERE login = ? LIMIT 1');
        $stmt->execute([$login]);
        $row = $stmt->fetch();
        if (!$row) return false;
        // Contraseñas en texto plano según DB.sql; si usas hash, ajusta aquí
        return $row['contrasena'] === $password;
    }

    public function createUser(string $login, string $password, bool $esAdmin = false): bool
    {
        // Evitar duplicados
        $stmt = $this->pdo->prepare('SELECT 1 FROM JUGADORES WHERE login = ? LIMIT 1');
        $stmt->execute([$login]);
        if ($stmt->fetch()) return false;
        $stmt = $this->pdo->prepare('INSERT INTO JUGADORES (login, contrasena, es_administrador) VALUES (?, ?, ?)');
        return $stmt->execute([$login, $password, $esAdmin ? 1 : 0]);
    }
}