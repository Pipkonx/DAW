<?php

namespace App\Models;

use App\Models\Funciones;
use PDO;
use App\DB\DB;
use Illuminate\Support\Facades\Log;

/**
 * Gestiona operaciones CRUD sobre la tabla `tareas` usando PDO.
 * Incluye listados con paginación, búsqueda por id y utilidades de limpieza.
 */
class Tareas
{
    /**
     * Valida los datos de una tarea.
     *
     * @param array $datos Datos de la tarea a validar.
     * @return array Array de errores, vacío si no hay errores.
     */
    public static function validarDatos(array $datos): array
    {
        $errores = [];

        $nifCif = $datos['nifCif'] ?? '';
        $personaNombre = $datos['personaNombre'] ?? '';
        $descripcionTarea = $datos['descripcionTarea'] ?? '';
        $correo = $datos['correo'] ?? '';
        $telefono = $datos['telefono'] ?? '';
        $codigoPostal = $datos['codigoPostal'] ?? '';
        $provincia = $datos['provincia'] ?? '';
        $fechaRealizacion = $datos['fechaRealizacion'] ?? '';

        if ($nifCif === "") {
            $errores['nif_cif'] = "Debe introducir el NIF/CIF de la persona encargada de la tarea";
        } else {
            $resultado = Funciones::validarNif($nifCif);
            if ($resultado !== true) $errores['nif_cif'] = $resultado;
        }

        if ($personaNombre === "") $errores['nombre_persona'] = "Debe introducir el nombre de la persona encargada de la tarea";
        if ($descripcionTarea === "") $errores['descripcion_tarea'] = "Debe introducir la descripción de la tarea";

        if ($correo === "") {
            $errores['correo'] = "Debe introducir el correo de la persona encargada de la tarea";
        } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = "El correo introducido no es válido";
        }

        if ($telefono == "") {
            $errores['telefono'] = "Debe introducir el teléfono de la persona encargada de la tarea";
        } else {
            $resultado = Funciones::telefonoValido($telefono);
            if ($resultado !== true) $errores['telefono'] = $resultado;
        }

        if ($codigoPostal != "" && !preg_match("/^[0-9]{5}$/", $codigoPostal)) {
            $errores['codigo_postal'] = "El código postal introducido no es válido, debe tener 5 números";
        }

        if ($provincia === "") $errores['provincia'] = "Debe introducir la provincia";

        $fechaActual = date('Y-m-d');
        if ($fechaRealizacion == "") {
            $errores['fecha_realizacion'] = "Debe introducir la fecha de realización de la tarea";
        } else if ($fechaRealizacion <= $fechaActual) {
            $errores['fecha_realizacion'] = "La fecha de realización debe ser posterior a la fecha actual";
        }
        return $errores;
    }

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
    public function listar(int $elementosPorPagina, int $paginaActual): array
    {
        $inicio = ($paginaActual - 1) * $elementosPorPagina;

        // Filtros: q (texto), estado, operario
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

        $sql = 'SELECT id, nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores FROM tareas';

        // Construir WHERE a mano
        if ($q !== '' && $estado !== '') {
            $sql .= " WHERE (personaNombre LIKE '%$q%' OR descripcionTarea LIKE '%$q%' OR poblacion LIKE '%$q%' OR operarioEncargado LIKE '%$q%') AND estadoTarea = '$estado'";
        } elseif ($q !== '') {
            $sql .= " WHERE (personaNombre LIKE '%$q%' OR descripcionTarea LIKE '%$q%' OR poblacion LIKE '%$q%' OR operarioEncargado LIKE '%$q%')";
        } elseif ($estado !== '') {
            $sql .= " WHERE estadoTarea = '$estado'";
        }

        $sql .= " ORDER BY id LIMIT $inicio, $elementosPorPagina";
        return $this->db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listarPorOperario(int $elementoPorPagina, int $paginaactual, string $operarioEncargado): array
    {
        $inicio = ($paginaactual - 1) * $elementoPorPagina;

        // Filtros: q (texto), estado, operario
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

        // Se ha eliminado el filtro estricto WHERE operarioEncargado = :operario para permitir ver todas las tareas
        $sql = 'SELECT id, nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores FROM tareas';

        // Construir WHERE a mano (si se quiere filtrar por operario en la búsqueda, se hace aquí abajo, pero no forzado)
        $conditions = [];
        
        if ($q !== '') {
            $conditions[] = "(personaNombre LIKE '%$q%' OR descripcionTarea LIKE '%$q%' OR poblacion LIKE '%$q%' OR operarioEncargado LIKE '%$q%')";
        }
        if ($estado !== '') {
            $conditions[] = "estadoTarea = '$estado'";
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " ORDER BY id LIMIT $inicio, $elementoPorPagina";
        
        $st = $this->db()->prepare($sql);
        // Ya no necesitamos pasar el parámetro :operario porque no se usa en la query principal obligatoria
        $st->execute(); 
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function contarPorOperario(string $operarioEncargado): int
    {
        $q = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : '';

        // Eliminado filtro estricto por operarioEncargado para conteo también
        $sql = 'SELECT COUNT(*) FROM tareas';
        $params = [];
        
        $conditions = [];

        if ($q !== '') {
            $conditions[] = "(personaNombre LIKE '%$q%' OR descripcionTarea LIKE '%$q%' OR poblacion LIKE '%$q%' OR operarioEncargado LIKE '%$q%')";
        }
        if ($estado !== '') {
            $conditions[] = "estadoTarea = :estado";
            $params[':estado'] = $estado;
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $st = $this->db()->prepare($sql);
        $st->execute($params);
        return (int) $st->fetchColumn();
    }


    /**
     * Cuenta el total de registros en la tabla `tareas`.
     *
     * @return int Número total de tareas.
     */
    public function contar(): int
    {
        $texto = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $estado = isset($_GET['estado']) ? trim((string)$_GET['estado']) : '';

        if ($texto !== '' && $estado !== '') {
            $sql = "SELECT COUNT(*) FROM tareas
                    WHERE (personaNombre LIKE '%$texto%' OR descripcionTarea LIKE '%$texto%' OR poblacion LIKE '%$texto%' OR operarioEncargado LIKE '%$texto%')
                      AND estadoTarea = '$estado'";
        } elseif ($texto !== '') {
            $sql = "SELECT COUNT(*) FROM tareas
                    WHERE personaNombre LIKE '%$texto%' OR descripcionTarea LIKE '%$texto%' OR poblacion LIKE '%$texto%' OR operarioEncargado LIKE '%$texto%'";
        } elseif ($estado !== '') {
            $sql = "SELECT COUNT(*) FROM tareas WHERE estadoTarea = '$estado'";
        } else {
            $sql = 'SELECT COUNT(*) FROM tareas';
        }

        return (int) $this->db()->query($sql)->fetchColumn();
    }
    /**
     * Busca una tarea por su identificador.
     *
     * @param int $id Identificador de la tarea.
     * @return array|null Datos de la tarea o null si no existe.
     */

    // la ?array es para indicar si puede devolver null
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
        try {
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
        } catch (\PDOException $e) {
            Log::error('Error al crear tarea: ' . $e->getMessage());
            throw $e; // Re-lanzar la excepción para que el controlador la maneje
        }
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
