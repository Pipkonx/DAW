<?php
require_once "config/database.php";

class Usuario {
    private $conn;
    private $tabla = "usuarios";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->conectar();
    }

    public function crear($nombre, $email) {
        $sql = "INSERT INTO {$this->tabla} (nombre, email) VALUES (:nombre, :email)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':nombre' => $nombre, ':email' => $email]);
    }

    public function listar() {
        $sql = "SELECT * FROM {$this->tabla}";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId($id) {
        $sql = "SELECT * FROM {$this->tabla} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($id, $nombre, $email) {
        $sql = "UPDATE {$this->tabla} SET nombre = :nombre, email = :email WHERE id = :id";
        // prepare es para prevenir inyeccoin de SQL
        $stmt = $this->conn->prepare($sql);
        // execute es para ejecutar la consulta
        return $stmt->execute([':id' => $id, ':nombre' => $nombre, ':email' => $email]);
    }

    public function borrar($id) {
        $sql = "DELETE FROM {$this->tabla} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
}
