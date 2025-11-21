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
     * Comprueba si existe una columna en la tabla usuarios.
     */
    private function hasColumn(string $column): bool
    {
        try {
            $st = $this->db()->prepare("SHOW COLUMNS FROM usuarios LIKE ?");
            $st->execute([$column]);
            return (bool)$st->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return false;
        }
    }
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
        try {
            // Soporte tanto para columnas nuevas (usuario, clave) como antiguas (nombre, contraseña)
            $hasUsuario = $this->hasColumn('usuario');
            $hasClave = $this->hasColumn('clave');
            $hasRol = $this->hasColumn('rol');
            $userCol = $hasUsuario ? 'usuario' : 'nombre';
            $passCol = $hasClave ? 'clave' : 'contraseña';
            // Usar backticks para evitar problemas con nombres y caracteres especiales
            $sql = "SELECT id, `{$userCol}` AS usuario, `{$passCol}` AS clave" . ($hasRol ? ", rol" : ", '' AS rol") . ", sesion, guardar_clave FROM usuarios WHERE `{$userCol}` = ? LIMIT 1";
            $st = $this->db()->prepare($sql);
            $st->execute([$nombre]);
            $row = $st->fetch(PDO::FETCH_ASSOC);
            if ($row && !$hasRol) {
                $row['rol'] = (strtolower((string)$row['usuario']) === 'admin') ? 'admin' : 'operario';
            }
            return $row ?: null;
        } catch (\Throwable $e) {
            return null;
        }
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

    /**
     * Crea la tabla `usuarios` si no existe y asegura un usuario admin por defecto.
     *
     * @return void
     */
    public function asegurarEsquema(): void
    {
        // Crear tabla si no existe (nuevo esquema)
        $sqlCreate = 'CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario VARCHAR(100) NOT NULL UNIQUE,
            clave VARCHAR(255) NOT NULL,
            rol VARCHAR(20) NOT NULL,
            sesion VARCHAR(255) NULL,
            guardar_clave TINYINT(1) NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci';
        $this->db()->exec($sqlCreate);

        // Si existe tabla con columnas antiguas, añadir columna rol si falta
        if (!$this->hasColumn('rol')) {
            try {
                $this->db()->exec("ALTER TABLE usuarios ADD COLUMN rol VARCHAR(20) NOT NULL DEFAULT 'operario'");
            } catch (\Throwable $e) {
                // Ignorar
            }
            // Marcar admin si existe un usuario llamado 'admin'
            try {
                $this->db()->exec("UPDATE usuarios SET rol='admin' WHERE nombre='admin' OR usuario='admin'");
            } catch (\Throwable $e) {
            }
        }

        // Insertar admin por defecto si no existe (soporta nombre/usuario)
        $st = $this->db()->prepare('SELECT COUNT(*) FROM usuarios WHERE nombre = ? OR usuario = ?');
        $st->execute(['admin', 'admin']);
        $existe = (int)$st->fetchColumn() > 0;
        if (!$existe) {
            $hasUsuario = $this->hasColumn('usuario');
            $hasClave = $this->hasColumn('clave');
            $userCol = $hasUsuario ? 'usuario' : 'nombre';
            $passCol = $hasClave ? 'clave' : 'contraseña';
            $ins = $this->db()->prepare("INSERT INTO usuarios ({$userCol}, {$passCol}, rol, sesion, guardar_clave) VALUES (?,?,?,?,?)");
            $ins->execute(['admin', 'admin', 'admin', null, 0]);
        }
    }

    /**
     * Lista usuarios con sus campos principales.
     *
     * @return array Lista de usuarios.
     */
    public function listar(): array
    {
        $userCol = $this->hasColumn('usuario') ? 'usuario' : 'nombre';
        $sql = "SELECT id, {$userCol} AS usuario, rol, guardar_clave FROM usuarios ORDER BY id";
        return $this->db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Crea un usuario.
     *
     * @param string $usuario Nombre de usuario.
     * @param string $clave Clave en texto plano.
     * @param string $rol Rol (Administrador u Operario).
     * @return int ID del usuario creado.
     */
    public function crear(string $usuario, string $clave, string $rol): int
    {
        $userCol = $this->hasColumn('usuario') ? 'usuario' : 'nombre';
        $passCol = $this->hasColumn('clave') ? 'clave' : 'contraseña';
        $sql = "INSERT INTO usuarios ({$userCol}, {$passCol}, rol, sesion, guardar_clave) VALUES (?,?,?,?,?)";
        $st = $this->db()->prepare($sql);
        $st->execute([$usuario, $clave, $rol, null, 0]);
        return (int) $this->db()->lastInsertId();
    }

    /**
     * Actualiza datos del usuario.
     *
     * @param int $id ID del usuario.
     * @param string $usuario Nombre de usuario.
     * @param string $clave Clave en texto plano.
     * @param string $rol Rol.
     * @return bool true en caso de éxito.
     */
    public function actualizar(int $id, string $usuario, string $clave, string $rol): bool
    {
        $userCol = $this->hasColumn('usuario') ? 'usuario' : 'nombre';
        $passCol = $this->hasColumn('clave') ? 'clave' : 'contraseña';
        $sql = "UPDATE usuarios SET {$userCol} = ?, {$passCol} = ?, rol = ? WHERE id = ?";
        $st = $this->db()->prepare($sql);
        return $st->execute([$usuario, $clave, $rol, $id]);
    }

    /**
     * Elimina un usuario por ID.
     */
    public function eliminar(int $id): bool
    {
        $st = $this->db()->prepare('DELETE FROM usuarios WHERE id = ?');
        return $st->execute([$id]);
    }

    /**
     * Busca usuario por ID.
     */
    public function buscarPorId(int $id): ?array
    {
        $userCol = $this->hasColumn('usuario') ? 'usuario' : 'nombre';
        $passCol = $this->hasColumn('clave') ? 'clave' : 'contraseña';
        $st = $this->db()->prepare("SELECT id, {$userCol} AS usuario, {$passCol} AS clave, rol, sesion, guardar_clave FROM usuarios WHERE id = ?");
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}

