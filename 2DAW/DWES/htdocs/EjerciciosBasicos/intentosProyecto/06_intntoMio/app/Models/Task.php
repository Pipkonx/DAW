<?php

namespace App\Models;

use PDO;

class Task
{
    protected static function db()
    {
        $pdo = new PDO('sqlite:' . __DIR__ . '/../../database/database.sqlite');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS tasks (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nif TEXT,
                persona_contacto TEXT,
                telefono TEXT,
                descripcion TEXT,
                email TEXT,
                direccion TEXT,
                poblacion TEXT,
                cp TEXT,
                provincia TEXT,
                operario TEXT,
                fecha_realizacion TEXT,
                archivo TEXT
            )"
        );
        return $pdo;
    }

    public static function all()
    {
        $stmt = self::db()->query("SELECT * FROM tasks ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function find($id)
    {
        $stmt = self::db()->prepare("SELECT * FROM tasks WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($datos)
    {
        $stmt = self::db()->prepare("INSERT INTO tasks (nif, persona_contacto, telefono, descripcion, email, direccion, poblacion, cp, provincia, operario, fecha_realizacion, archivo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $datos['nif'],
            $datos['persona_contacto'],
            $datos['telefono'],
            $datos['descripcion'] ?? '',
            $datos['email'] ?? '',
            $datos['direccion'] ?? '',
            $datos['poblacion'] ?? '',
            $datos['cp'] ?? '',
            $datos['provincia'] ?? '',
            $datos['operario'],
            $datos['fecha_realizacion'] ?? '',
            $datos['archivo'] ?? ''
        ]);
    }

    public static function update($id, $datos)
    {
        $stmt = self::db()->prepare("UPDATE tasks SET nif=?, persona_contacto=?, telefono=?, descripcion=?, email=?, direccion=?, poblacion=?, cp=?, provincia=?, operario=?, fecha_realizacion=? WHERE id=?");
        return $stmt->execute([
            $datos['nif'],
            $datos['persona_contacto'],
            $datos['telefono'],
            $datos['descripcion'] ?? '',
            $datos['email'] ?? '',
            $datos['direccion'] ?? '',
            $datos['poblacion'] ?? '',
            $datos['cp'] ?? '',
            $datos['provincia'] ?? '',
            $datos['operario'],
            $datos['fecha_realizacion'] ?? '',
            $id
        ]);
    }

    public static function delete($id)
    {
        $stmt = self::db()->prepare("DELETE FROM tasks WHERE id=?");
        return $stmt->execute([$id]);
    }
}
