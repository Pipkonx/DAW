<?php
require_once __DIR__ . '/../conexion/config.php';

class Categoria
{
    private $pdo;
    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }
    public function all(): array
    {
        $stmt = $this->pdo->query('SELECT id_categoria, nombre_categoria FROM CATEGORIAS ORDER BY nombre_categoria');
        return $stmt->fetchAll();
    }
}