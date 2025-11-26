<?php
require_once __DIR__ . '/../conexion/config.php';

class Partida
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function insert(int $idJugador, int $idPalabra, string $fecha, int $letrasAcertadas, int $letrasFalladas, int $palabraAcertada, int $puntuacion): void
    {
        $sql = 'INSERT INTO PARTIDAS (id_jugador, id_palabra_jugada, fecha_partida, letras_acertadas, letras_falladas, palabra_acertada, puntuacion_obtenida) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idJugador, $idPalabra, $fecha, $letrasAcertadas, $letrasFalladas, $palabraAcertada, $puntuacion]);
    }
}