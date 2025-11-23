<?php

namespace App\Models;

use PDO;
use App\DB\DB;

/**
 * Gestiona operaciones CRUD sobre la tabla `tareas` usando PDO.
 * Incluye listados con paginación, búsqueda por id y utilidades de limpieza.
 */
class Tareas
{
    /**
     * Obtiene la conexión PDO desde el singleton de base de datos.
     *
     * @return PDO Conexión a la base de datos.
     */
    private function db(): PDO
    {
        return DB::getInstance();
    }


       //todo PAGINACION
        // https://es.stackoverflow.com/questions/605864/agregar-paginaci%C3%B3n-php
    /**
     * Lista tareas con paginación básica leyendo `pagina` de la query string.
     *
     * @return array Lista de tareas como arrays asociativos.
     */
    public function listar(): array
    {
        $elementosPorPagina = 20;
        $paginaActual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        if ($paginaActual < 1) $paginaActual = 1;
        $inicio = ($paginaActual -1) * $elementosPorPagina;

        // Filtros: q (texto), estado, operario
        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : '';
        $where = [];
        $params = [];
        if ($q !== '') {
            $where[] = '(personaNombre LIKE ? OR descripcionTarea LIKE ? OR poblacion LIKE ? OR operarioEncargado LIKE ?)';
            $params[] = '%'.$q.'%';
            $params[] = '%'.$q.'%';
            $params[] = '%'.$q.'%';
            $params[] = '%'.$q.'%';
        }
        if ($estado !== '') {
            $where[] = 'estadoTarea = ?';
            $params[] = $estado;
        }
        $sql = 'SELECT id, nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores FROM tareas';
        if (!empty($where)) $sql .= ' WHERE ' . implode(' AND ', $where);
        $sql .= ' ORDER BY id LIMIT ' . $inicio . ',' . $elementosPorPagina;
        $st = $this->db()->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el total de registros en la tabla `tareas`.
     *
     * @return int Número total de tareas.
     */
    public function contar(): int
    {
        // Contar con filtros si existen
        $texto = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : '';
        $donde = [];
        $parametros = [];
        if ($texto !== '') {
            $donde[] = '(personaNombre LIKE ? OR descripcionTarea LIKE ? OR poblacion LIKE ? OR operarioEncargado LIKE ?)';
            $parametros[] = '%'.$texto.'%';
            $parametros[] = '%'.$texto.'%';
            $parametros[] = '%'.$texto.'%';
            $parametros[] = '%'.$texto.'%';
        }
        if ($estado !== '') {
            $donde[] = 'estadoTarea = ?';
            $parametros[] = $estado;
        }
        $sql = 'SELECT COUNT(*) FROM tareas';
        if (!empty($donde)) $sql .= ' WHERE ' . implode(' AND ', $donde);
        $st = $this->db()->prepare($sql);
        $st->execute($parametros);
        return (int) $st->fetchColumn();
    }
    /**
     * Busca una tarea por su identificador.
     *
     * @param int $id Identificador de la tarea.
     * @return array|null Datos de la tarea o null si no existe.
     */
    public function buscar(int $id): ?array
    {

     
        $sql = 'SELECT id, nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores FROM tareas WHERE id = ?';
        $st = $this->db()->prepare($sql);
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Crea una tarea nueva.
     *
     * @param array $datos Campos de la tarea (nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores).
     * @return int ID autogenerado de la nueva tarea.
     */
    public function crear(array $datos): int
    {
        $d = $this->limpiar($datos);
        $sql = 'INSERT INTO tareas (nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
        $st = $this->db()->prepare($sql);
        $st->execute([
            $d['nifCif'],
            $d['personaNombre'],
            $d['telefono'],
            $d['correo'],
            $d['descripcionTarea'],
            $d['direccionTarea'],
            $d['poblacion'],
            $d['codigoPostal'],
            $d['provincia'],
            $d['estadoTarea'],
            $d['operarioEncargado'],
            $d['fechaRealizacion'],
            $d['anotacionesAnteriores'],
            $d['anotacionesPosteriores']
        ]);
        return (int)$this->db()->lastInsertId();
    }

    /**
     * Actualiza una tarea existente.
     *
     * @param int $id Identificador de la tarea a actualizar.
     * @param array $datos Campos a actualizar.
     * @return bool true si la actualización fue correcta.
     */
    public function actualizar(int $id, array $datos): bool
    {
        $d = $this->limpiar($datos);
        $sql = 'UPDATE tareas SET nifCif=?, personaNombre=?, telefono=?, correo=?, descripcionTarea=?, direccionTarea=?, poblacion=?, codigoPostal=?, provincia=?, estadoTarea=?, operarioEncargado=?, fechaRealizacion=?, anotacionesAnteriores=?, anotacionesPosteriores=? WHERE id=?';
        $st = $this->db()->prepare($sql);
        return $st->execute([
            $d['nifCif'],
            $d['personaNombre'],
            $d['telefono'],
            $d['correo'],
            $d['descripcionTarea'],
            $d['direccionTarea'],
            $d['poblacion'],
            $d['codigoPostal'],
            $d['provincia'],
            $d['estadoTarea'],
            $d['operarioEncargado'],
            $d['fechaRealizacion'],
            $d['anotacionesAnteriores'],
            $d['anotacionesPosteriores'],
            $id
        ]);
    }

    /**
     * Elimina una tarea por su identificador.
     *
     * @param int $id Identificador de la tarea a eliminar.
     * @return bool true si se eliminó correctamente.
     */
    public function eliminar(int $id): bool
    {
        $st = $this->db()->prepare('DELETE FROM tareas WHERE id=?');
        return $st->execute([$id]);
    }

    /**
     * Normaliza y trimea los campos esperados de una tarea.
     *
     * @param array $datos Datos recibidos (por ejemplo, del formulario).
     * @return array Datos saneados con las claves esperadas.
     */
    private function limpiar(array $datos): array
    {
        $keys = [
            'nifCif',
            'personaNombre',
            'telefono',
            'correo',
            'descripcionTarea',
            'direccionTarea',
            'poblacion',
            'codigoPostal',
            'provincia',
            'estadoTarea',
            'operarioEncargado',
            'fechaRealizacion',
            'anotacionesAnteriores',
            'anotacionesPosteriores'
        ];
        $out = [];
        foreach ($keys as $k) {
            $out[$k] = isset($datos[$k]) ? trim((string)$datos[$k]) : '';
        }
        return $out;
    }

    /**
     * Atajo para registrar una alta de tarea delegando en `crear`.
     *
     * @param array $datos Datos de la tarea a crear.
     * @return void
     */
    public function registraAlta(array $datos)
    {
        $this->crear($datos);
    }
    /**
     * Actualiza campos permitidos para operario.
     *
     * @param int $id ID de la tarea.
     * @param array $datos Datos: estadoTarea, anotacionesPosteriores.
     */
    public function actualizarOperario(int $id, array $datos): bool
    {
        $estado = trim((string)($datos['estadoTarea'] ?? ''));
        $anotPost = trim((string)($datos['anotacionesPosteriores'] ?? ''));
        $sql = 'UPDATE tareas SET estadoTarea = ?, anotacionesPosteriores = ? WHERE id = ?';
        $st = $this->db()->prepare($sql);
        return $st->execute([$estado, $anotPost, $id]);
    }
}
