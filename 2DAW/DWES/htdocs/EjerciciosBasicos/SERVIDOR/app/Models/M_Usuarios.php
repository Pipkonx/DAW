<?php

namespace App\Models;

use PDO;
use App\DB\DB;

/**
 * Acceso a la tabla `usuarios` mediante PDO.
 * Campos: id, nombre, contraseña, sesion, guardar_clave.
 */
class M_Usuarios
{
    /**
     * Obtiene la conexión PDO desde el singleton.
     *
     * @return PDO Conexión a la base de datos.
     */
    private function bd(): PDO
    {
        return DB::getInstance();
    }

    /**
     * Busca un usuario por su nombre.
     *
     * @param string $nombre Nombre del usuario.
     * @return array|null Usuario como array asociativo o null si no existe.
     */
    public function buscarPorNombre(string $nombre): ?array
    {
        // Con la estructura actual, siempre se usan las columnas reales: nombre y contraseña
        $sql = "SELECT id, nombre AS usuario, contraseña AS clave, rol, sesion, guardar_clave 
                FROM usuarios 
                WHERE nombre = ? 
                LIMIT 1";

        $st = $this->bd()->prepare($sql);
        $st->execute([$nombre]);
        $fila = $st->fetch(PDO::FETCH_ASSOC);

        return $fila ?: null;
    }

    /**
     * Busca un usuario por su ID.
     *
     * @param int $id ID del usuario.
     * @return array|null Usuario como array asociativo o null si no existe.
     */
    public function buscar(int $id): ?array
    {
        $sql = "SELECT id, nombre, contraseña, rol, sesion, guardar_clave 
                FROM usuarios 
                WHERE id = ? 
                LIMIT 1";

        $st = $this->bd()->prepare($sql);
        $st->execute([$id]);
        $fila = $st->fetch(PDO::FETCH_ASSOC);

        return $fila ?: null;
    }

    /**
     * Lista usuarios con paginación.
     *
     * @param int $limit Número de usuarios por página.
     * @param int $offset Desplazamiento.
     * @return array Lista de usuarios.
     */
    public function listar(int $limit, int $offset): array
    {
        $sql = "SELECT id, nombre, rol FROM usuarios LIMIT :limit OFFSET :offset";
        $st = $this->bd()->prepare($sql);
        $st->bindParam(':limit', $limit, PDO::PARAM_INT);
        $st->bindParam(':offset', $offset, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el número total de usuarios.
     *
     * @return int Número total de usuarios.
     */
    public function contar(): int
    {
        $sql = "SELECT COUNT(*) FROM usuarios";
        $st = $this->bd()->query($sql);
        return $st->fetchColumn();
    }

    /**
     * Inserta un nuevo usuario.
     *
     * @param array $datos Datos del usuario (nombre, contraseña, rol).
     * @return bool True si se inserta correctamente.
     */
    public function insertar(array $datos): bool
    {
        $sql = 'INSERT INTO usuarios (nombre, contraseña, rol) VALUES (?, ?, ?)';
        $st = $this->bd()->prepare($sql);
        return $st->execute([$datos['nombre'], password_hash($datos['contraseña'], PASSWORD_DEFAULT), $datos['rol']]);
    }

    /**
     * Actualiza los datos de un usuario.
     *
     * @param array $datos Datos del usuario (id, nombre, contraseña, rol).
     * @return bool True si se actualiza correctamente.
     */
    public function actualizarUsuario(array $datos): bool
    {
        $sql = 'UPDATE usuarios SET nombre = ?, rol = ?';
        $params = [$datos['nombre'], $datos['rol']];

        if (!empty($datos['contraseña'])) {
            $sql .= ', contraseña = ?';
            $params[] = password_hash($datos['contraseña'], PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = ?';
        $params[] = $datos['id'];

        $st = $this->bd()->prepare($sql);
        return $st->execute($params);
    }

    /**
     * Elimina un usuario por su ID.
     *
     * @param int $id ID del usuario.
     * @return bool True si se elimina correctamente.
     */
    public function eliminar(int $id): bool
    {
        $sql = 'DELETE FROM usuarios WHERE id = ?';
        $st = $this->bd()->prepare($sql);
        return $st->execute([$id]);
    }

    /**
     * Actualiza la sesión y la preferencia de guardar clave del usuario.
     *
     * @param int $id ID del usuario.
     * @param string|null $sesion Valor de sesión (por ejemplo, session_id) o null.
     * @param bool $guardarClave Si el usuario desea guardar la clave.
     * @return bool True si se actualiza correctamente.
     */
    public function actualizar(int $id, ?string $sesion, bool $guardarClave): bool
    {
        $sql = 'UPDATE usuarios SET sesion = ?, guardar_clave = ? WHERE id = ?';
        $st = $this->bd()->prepare($sql);
        return $st->execute([$sesion, $guardarClave ? 1 : 0, $id]);
    }



    /**
     * Verifica si un rol dado corresponde a un operario.
     *
     * Este método consulta la base de datos para determinar si existe un usuario
     * con el rol especificado como 'operario'.
     *
     * @param string $rol El rol a verificar.
     * @return array|null Un array asociativo con los datos del operario si se encuentra, o null si no.
     */
    public static function isOperario($rol)
    {
        $sql = "SELECT * FROM usuarios  WHERE rol = operario";
        $st = DB::getInstance()->prepare($sql);
        $st->execute([$rol]);
        $fila = $st->fetch(PDO::FETCH_ASSOC);

        return $fila ?: null;
    }

    /**
     * Obtiene todos los usuarios con rol 'operario'.
     *
     * @return array Lista de usuarios operarios.
     */
    public function getOperarios(): array
    {
        $sql = "SELECT id, nombre, rol FROM usuarios WHERE rol = 'operario'";
        $st = $this->bd()->prepare($sql);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }
}