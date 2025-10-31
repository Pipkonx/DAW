<?php
require_once "DB/Conexion.php";

class Tarea
{
    private $conn;

    public function __construct($conn)
    {
        // para que nuestra conexion sea en todo
        // $this->conn = $conn;
        $this->conn = Conexion::getInstance()->getConnection();
    }

    public function crear($id, $nombreTarea, $descripcion, $estado, $anotaciones_anteriores, $anotaciones_posteriores, $operario_encargado)
    {
        try {
            // insertamos fecha_tarea con NOW() y dejamos fecha_actualizacion al valor por defecto
            $sql = "INSERT INTO tareas (id, nombreTarea, descripcion, estado, fecha_tarea, anotaciones_anteriores, anotaciones_posteriores, operario_encargado) VALUES (:id, :nombreTarea, :descripcion, :estado, NOW(), :anotaciones_anteriores, :anotaciones_posteriores, :operario_encargado)";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nombreTarea' => $nombreTarea,
                ':descripcion' => $descripcion,
                ':estado' => $estado,
                ':anotaciones_anteriores' => $anotaciones_anteriores,
                ':anotaciones_posteriores' => $anotaciones_posteriores,
                ':operario_encargado' => $operario_encargado
            ]);
            echo "✅ Tarea insertada correctamente<br>";
        } catch (PDOException $e) {
            echo "❌ Error al insertar tarea: " . $e->getMessage() . "<br>";
        }
    }

    public function listar()
    {
        try {
            $sql = "SELECT * FROM tareas ORDER BY id DESC";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "❌ Error al listar tareas: " . $e->getMessage() . "<br>";
        }
    }


    public function actualizar($id, $nombreTarea, $descripcion, $estado, $anotaciones_anteriores, $anotaciones_posteriores, $operario_encargado)
    {
        try {
            // actualizamos campos y marcamos fecha_actualizacion
            $sql = 'UPDATE tareas SET nombreTarea = :nombreTarea, descripcion = :descripcion, estado = :estado, anotaciones_anteriores = :anotaciones_anteriores, anotaciones_posteriores = :anotaciones_posteriores, operario_encargado = :operario_encargado, fecha_actualizacion = CURRENT_TIMESTAMP WHERE id = :id';
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':id' => $id,
                ':nombreTarea' => $nombreTarea,
                ':descripcion' => $descripcion,
                ':estado' => $estado,
                ':anotaciones_anteriores' => $anotaciones_anteriores,
                ':anotaciones_posteriores' => $anotaciones_posteriores,
                ':operario_encargado' => $operario_encargado
            ]);
            echo "✅ Tarea actualizada correctamente<br>";
        } catch (PDOException $e) {
            echo "❌ Error al actualizar tareas: " . $e->getMessage() . "<br>";
        }
    }

    public function eliminar($id)
    {
        try {
            $sql = "DELETE FROM tareas WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([':id' => $id]);
            echo "🗑️ Tarea eliminada correctamente<br>";
        } catch (PDOException $e) {
            echo "❌ Error al eliminar tarea: " . $e->getMessage() . "<br>";
        }
    }

    public function obtenerId($id)
    {
        $sql = "SELECT * FROM tareas WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
