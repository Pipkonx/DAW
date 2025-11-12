<?php

namespace App\Legacy;

use App\Services\Database;
use PDO;
use PDOException;

class Tarea
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll(): array
    {
        try {
            $stmt = $this->db->query(
                "SELECT t.*, u.nombre AS nombre_usuario, u.email AS email_usuario
                 FROM tareas t
                 JOIN usuarios u ON u.id = t.usuario_id
                 ORDER BY t.fecha_creacion DESC"
            );
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById(int $id): array|false
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tareas WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function create(array $datos): bool
    {
        try {
            $stmt = $this->db->prepare(
                "INSERT INTO tareas (titulo, descripcion, usuario_id, completada, fecha_creacion)
                 VALUES (?, ?, ?, 0, NOW())"
            );
            return $stmt->execute([
                $datos['titulo'] ?? '',
                $datos['descripcion'] ?? '',
                $datos['usuario_id'] ?? null,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function update(int $id, array $datos): bool
    {
        try {
            $stmt = $this->db->prepare(
                "UPDATE tareas SET titulo = ?, descripcion = ?, usuario_id = ?, completada = ? WHERE id = ?"
            );
            return $stmt->execute([
                $datos['titulo'] ?? '',
                $datos['descripcion'] ?? '',
                $datos['usuario_id'] ?? null,
                $datos['completada'] ?? 0,
                $id,
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function delete(int $id): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tareas WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getByUsuario(int $usuario_id): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT t.*, u.nombre AS nombre_usuario, u.email AS email_usuario
                 FROM tareas t
                 JOIN usuarios u ON u.id = t.usuario_id
                 WHERE t.usuario_id = ?
                 ORDER BY t.fecha_creacion DESC"
            );
            $stmt->execute([$usuario_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getByEstado(int|string $completada): array
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT t.*, u.nombre AS nombre_usuario, u.email AS email_usuario
                 FROM tareas t
                 JOIN usuarios u ON u.id = t.usuario_id
                 WHERE t.completada = ?
                 ORDER BY t.fecha_creacion DESC"
            );
            $stmt->execute([$completada]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
