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
    public function buscar(string $nombre): ?array
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
}