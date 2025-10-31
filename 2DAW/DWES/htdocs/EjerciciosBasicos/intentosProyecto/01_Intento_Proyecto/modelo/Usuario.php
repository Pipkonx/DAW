<?php
require_once "DB/Conexion.php";

class Usuario
{
    private $conn;

    public function __construct($conn)
    {
        // para que nuestra conexion sea en todo
        // $this->conn = $conn;
        // obtenemos la conexión PDO desde el singleton
        $this->conn = Conexion::getInstance()->getConnection();
    }

    public function crear($nombre, $email, $nif, $cp)
    {
        try {
            $sql = "INSERT INTO usuarios (nombre, email, nif, cp) VALUES (:nombre, :email, :nif, :cp)";
            // el prepare previene inyeccion sql
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':email' => $email,
                ':nif' => $nif,
                ':cp' => $cp
            ]);

            echo "✅ Usuario insertado correctamente<br>";
        } catch (PDOException $e) {
            echo "❌ Error al insertar usuario: " . $e->getMessage() . "<br>";
        }
    }

    public function listar()
    {
        try {
            $sql = "SELECT * FROM usuarios ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "❌ Error al listar usuarios: " . $e->getMessage() . "<br>";
        }
    }

    public function eliminar($id)
    {
        try {
            $sql = "DELETE FROM usuarios WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            echo "🗑️ Usuario eliminado correctamente<br>";
        } catch (PDOException $e) {
            echo "❌ Error al eliminar usuario: " . $e->getMessage() . "<br>";
        }
    }

    public function actualizar($id, $nombre, $email, $nif, $cp)
    {
        try {
            $sql = "UPDATE usuarios SET nombre = :nombre, email = :email, nif = :nif, cp = :cp WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nombre' => $nombre,
                ':email' => $email,
                ':nif' => $nif,
                ':cp' => $cp
            ]);
            echo "✏️ Usuario actualizado correctamente<br>";
        } catch (PDOException $e) {
            echo "❌ Error al actualizar usuario: " . $e->getMessage() . "<br>";
        }
    }

    public function obtenerId($id)
    {
        $sql = "SELECT * FROM usuarios WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
