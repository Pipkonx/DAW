<?php
require_once __DIR__ . '/../conexion/config.php';

class Palabra
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = DB::getInstance()->getConnection();
    }

    public function randomByCategoriaAndDificultad(int $categoria, string $dificultad): ?array
    {
        $lenCond = '';
        switch ($dificultad) {
            case 'facil':
                $lenCond = 'CHAR_LENGTH(texto_palabra) <= 5';
                break;
            case 'media':
                $lenCond = 'CHAR_LENGTH(texto_palabra) BETWEEN 6 AND 8';
                break;
            case 'dificil':
                $lenCond = 'CHAR_LENGTH(texto_palabra) >= 9';
                break;
            default:
                return null;
        }
        $sql = "SELECT id_palabra, texto_palabra FROM PALABRAS WHERE id_categoria = ? AND $lenCond ORDER BY RAND() LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$categoria]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}