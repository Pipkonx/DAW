<?php
require_once __DIR__ . '/../config/Database.php';

class Tarea
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Obtener todas las tareas
    public function getAll()
    {
        try {
            $stmt = $this->db->query(
                "SELECT t.*, u.nombre 
                AS nombre_usuario, u.email 
                AS email_usuario
                FROM tareas t
                JOIN usuarios u ON u.id = t.usuario_id
                ORDER BY t.fecha_creacion DESC"
            );
            return $stmt->fetchAll();
            // usamos el PDO exception directamtente en vez de Exception
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getById($id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tareas WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            return false;
        }
    }

    // Crear nueva tarea
    public function create($dataOrTitulo, $descripcion = null, $usuario_id = null)
    {
        try {
            // is_array es para ver si es un array o no
            if (is_array($dataOrTitulo)) {
                $data = $dataOrTitulo;
                $titulo = $data['titulo'] ?? '';
                $descripcion = $data['descripcion'] ?? '';
                $usuario_id = $data['usuario_id'] ?? null;
            } else {
                $titulo = $dataOrTitulo;
            }


            // el prepare es para prevenir inyeccion sql
            $stmt = $this->db->prepare("INSERT INTO tareas (titulo, descripcion, usuario_id) 
            VALUES (?, ?, ?)");
            return $stmt->execute([$titulo, $descripcion, $usuario_id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Actualizar tarea
    public function update($id, $dataOrTitulo, $descripcion = null)
    {
        try {
            if (is_array($dataOrTitulo)) {
                $data = $dataOrTitulo;
                $titulo = $data['titulo'] ?? '';
                $descripcion = $data['descripcion'] ?? '';
                $usuario_id = $data['usuario_id'] ?? null;
                $completada = isset($data['completada']) ? (int)$data['completada'] : 0;

                if ($completada === 1) {
                    $stmt = $this->db->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, usuario_id = ?, completada = 1, fecha_completado = NOW() WHERE id = ?");
                    // execute es para ejecutar la consulta
                    return $stmt->execute([$titulo, $descripcion, $usuario_id, $id]);
                } else {
                    $stmt = $this->db->prepare("UPDATE tareas SET titulo = ?, descripcion = ?, usuario_id = ?, completada = 0, fecha_completado = NULL WHERE id = ?");
                    return $stmt->execute([$titulo, $descripcion, $usuario_id, $id]);
                }
            } else {
                $titulo = $dataOrTitulo;
                $stmt = $this->db->prepare("UPDATE tareas SET titulo = ?, descripcion = ? WHERE id = ?");
                return $stmt->execute([$titulo, $descripcion, $id]);
            }
        } catch (PDOException $e) {
            return false;
        }
    }

    // Marcar tarea como completada
    public function marcarComoCompletada($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE tareas SET completada = 1, fecha_completado = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Marcar tarea como pendiente
    public function marcarComoPendiente($id)
    {
        try {
            $stmt = $this->db->prepare("UPDATE tareas SET completada = 0, fecha_completado = NULL WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Eliminar tarea
    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM tareas WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener tareas por usuario (con datos del usuario)
    public function getByUsuario($usuario_id)
    {
        try {
            $stmt = $this->db->prepare(
                "SELECT t.*, u.nombre 
                AS nombre_usuario, u.email 
                AS email_usuario
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

    // Obtener tareas por estado (0 pendientes, 1 completadas) con datos del usuario
    public function getByEstado($completada)
    {
        try {
            $completada = (int)$completada;
            if ($completada === 1) {
                // diferencia entre query y execute es que query es para consultas pero no devuelve datos y el execute es para ejecutar la consulta y devolver datos
                $stmt = $this->db->query(
                    "SELECT t.*, u.nombre 
                    AS nombre_usuario, u.email 
                    AS email_usuario
                    FROM tareas t
                    JOIN usuarios u ON u.id = t.usuario_id
                    WHERE t.completada = 1
                    ORDER BY t.fecha_completado DESC"
                );
            } else {
                $stmt = $this->db->query(
                    "SELECT t.*, u.nombre 
                    AS nombre_usuario, u.email 
                    AS email_usuario
                    FROM tareas t
                    JOIN usuarios u ON u.id = t.usuario_id
                    WHERE t.completada = 0
                    ORDER BY t.fecha_creacion DESC"
                );
            }
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            return [];
        }
    }
}
