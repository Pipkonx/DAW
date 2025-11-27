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
     * Comprueba si existe una columna en la tabla usuarios.
     *
     * @param string $columna Nombre de la columna a comprobar.
     * @return bool True si la columna existe, false en caso contrario.
     */
    private function tieneColumna(string $columna): bool
    {
        $st = $this->bd()->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = ?");
        $st->execute([$columna]);
        return (bool)$st->fetch(PDO::FETCH_ASSOC);
    }

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
        // Soporte tanto para columnas nuevas (usuario, clave) como antiguas (nombre, contraseña)
        $tieneUsuario = $this->tieneColumna('usuario');
        $tieneClave = $this->tieneColumna('clave');
        $tieneRol = $this->tieneColumna('rol');
        $userCol = $tieneUsuario ? 'usuario' : 'nombre';
        $passCol = $tieneClave ? 'clave' : 'contraseña';

        // Usar backticks para evitar problemas con nombres y caracteres especiales
        $sql = "SELECT id, `{$userCol}` AS usuario, `{$passCol}` AS clave" . ($tieneRol ? ", rol" : ", '' AS rol") . ", sesion, guardar_clave FROM usuarios WHERE `{$userCol}` = ? LIMIT 1";
        
        $st = $this->bd()->prepare($sql);
        $st->execute([$nombre]);
        $fila = $st->fetch(PDO::FETCH_ASSOC);

        if ($fila && !$tieneRol) {
            $fila['rol'] = (strtolower((string)$fila['usuario']) === 'admin') ? 'admin' : 'operario';
        }
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
