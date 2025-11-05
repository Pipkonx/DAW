<?php
require_once __DIR__ . '/../conexion/config.php';

class Jugador
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getIdByLogin(string $login): ?int
    {
        $stmt = $this->pdo->prepare('SELECT id_jugador FROM JUGADORES WHERE login = ? LIMIT 1');
        $stmt->execute([$login]);
        $row = $stmt->fetch();
        return $row ? intval($row['id_jugador']) : null;
    }
}