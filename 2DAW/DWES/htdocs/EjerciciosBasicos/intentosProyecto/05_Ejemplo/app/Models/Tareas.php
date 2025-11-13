<?php
namespace App\Models;

use PDO;

class Tareas
{
    private function db(): PDO
    {
        return BD::get();
    }

    public function listar(): array
    {
        $sql = 'SELECT id, nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores FROM tareas ORDER BY id';
        return $this->db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): ?array
    {
        $sql = 'SELECT id, nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores FROM tareas WHERE id = ?';
        $st = $this->db()->prepare($sql);
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function crear(array $datos): int
    {
        $d = $this->limpiar($datos);
        $sql = 'INSERT INTO tareas (nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $st = $this->db()->prepare($sql);
        $st->execute([
            $d['nifCif'],$d['personaNombre'],$d['telefono'],$d['correo'],$d['descripcionTarea'],
            $d['direccionTarea'],$d['poblacion'],$d['codigoPostal'],$d['provincia'],
            $d['estadoTarea'],$d['operarioEncargado'],$d['fechaRealizacion'],
            $d['anotacionesAnteriores'],$d['anotacionesPosteriores']
        ]);
        return (int)$this->db()->lastInsertId();
    }

    public function actualizar(int $id, array $datos): bool
    {
        $d = $this->limpiar($datos);
        $sql = 'UPDATE tareas SET nifCif=?, personaNombre=?, telefono=?, correo=?, descripcionTarea=?, direccionTarea=?, poblacion=?, codigoPostal=?, provincia=?, estadoTarea=?, operarioEncargado=?, fechaRealizacion=?, anotacionesAnteriores=?, anotacionesPosteriores=? WHERE id=?';
        $st = $this->db()->prepare($sql);
        return $st->execute([
            $d['nifCif'],$d['personaNombre'],$d['telefono'],$d['correo'],$d['descripcionTarea'],
            $d['direccionTarea'],$d['poblacion'],$d['codigoPostal'],$d['provincia'],
            $d['estadoTarea'],$d['operarioEncargado'],$d['fechaRealizacion'],
            $d['anotacionesAnteriores'],$d['anotacionesPosteriores'], $id
        ]);
    }

    public function eliminar(int $id): bool
    {
        $st = $this->db()->prepare('DELETE FROM tareas WHERE id=?');
        return $st->execute([$id]);
    }

    private function limpiar(array $datos): array
    {
        $keys = [
            'nifCif','personaNombre','telefono','correo','descripcionTarea',
            'direccionTarea','poblacion','codigoPostal','provincia',
            'estadoTarea','operarioEncargado','fechaRealizacion',
            'anotacionesAnteriores','anotacionesPosteriores'
        ];
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = isset($datos[$k]) ? trim((string)$datos[$k]) : '';
        }
        return $out;
    }

    public function registraAlta(array $datos)
    {
        $this->crear($datos);
    }
}