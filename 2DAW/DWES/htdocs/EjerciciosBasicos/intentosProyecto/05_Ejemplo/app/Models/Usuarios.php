<?php

namespace App\Models;

use PDO;
use App\DB\DB;

/**
 * Acceso a la tabla `usuarios` mediante PDO.
 * Campos: id, nombre, contraseña, sesion, guardar_clave.
 */
class Usuarios
{
    /**
     * Obtiene la conexión PDO desde el singleton.
     *
     * @return PDO Conexión a la base de datos.
     */
    private function db(): PDO
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
        $sql = 'SELECT id, nombre, `contraseña`, sesion, guardar_clave FROM usuarios WHERE nombre = ? LIMIT 1';
        $st = $this->db()->prepare($sql);
        $st->execute([$nombre]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Actualiza la sesión y preferencia de guardado de clave del usuario.
     *
     * @param int $id ID del usuario.
     * @param string|null $sesion Valor de sesión (por ejemplo, session_id) o null.
     * @param bool $guardarClave Si el usuario desea guardar la clave.
     * @return bool true si se actualiza correctamente.
     */
    public function actualizarSesionYPreferencias(int $id, ?string $sesion, bool $guardarClave): bool
    {
        $sql = 'UPDATE usuarios SET sesion = ?, guardar_clave = ? WHERE id = ?';
        $st = $this->db()->prepare($sql);
        return $st->execute([$sesion, $guardarClave ? 1 : 0, $id]);
    }
}

