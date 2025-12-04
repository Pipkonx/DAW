<?php

namespace App\Models;

use App\Models\M_Funciones;
use PDO;
use App\DB\DB;

/**
 * Gestiona operaciones CRUD sobre la tabla `tareas` usando PDO.
 * Incluye listados con paginación, búsqueda por id y utilidades de limpieza.
 */
class M_Tareas
{
    /**
     * Valida los datos de una tarea.
     *
     * Realiza una serie de validaciones sobre los datos proporcionados para una tarea,
     * incluyendo campos obligatorios, formato de NIF/CIF, correo electrónico, teléfono,
     * código postal y fecha de realización. Utiliza M_Funciones para algunas validaciones.
     *
     * @param array $datos Array asociativo con los datos de la tarea a validar.
     * @return array Un array asociativo con mensajes de error si existen, o un array vacío si todos los datos son válidos.
     */
    public static function validarDatos(array $datos): array
    {
        $errores = [];

        $nifCif           = $datos['nifCif'] ?? '';
        $personaNombre    = $datos['personaNombre'] ?? '';
        $descripcionTarea = $datos['descripcionTarea'] ?? '';
        $correo           = $datos['correo'] ?? '';
        $telefono         = $datos['telefono'] ?? '';
        $codigoPostal     = $datos['codigoPostal'] ?? '';
        $provincia        = $datos['provincia'] ?? '';
        $fechaRealizacion = $datos['fechaRealizacion'] ?? '';

        // Helper interno para campos obligatorios
        $requerido = function($valor, $campo, $mensaje) use (&$errores) {
            if ($valor === "") $errores[$campo] = $mensaje;
        };

        // Validaciones obligatorias
        $requerido($personaNombre, 'nombre_persona', "Debe introducir el nombre de la persona encargada de la tarea");
        $requerido($descripcionTarea, 'descripcion_tarea', "Debe introducir la descripción de la tarea");
        $requerido($provincia, 'provincia', "Debe introducir la provincia");

        // NIF/CIF
        if ($nifCif === "") {
            $errores['nif_cif'] = "Debe introducir el NIF/CIF de la persona encargada de la tarea";
        } else {
            $resultado = M_Funciones::validarNif($nifCif);
            if ($resultado !== true) $errores['nif_cif'] = $resultado;
        }

        // Correo
        if ($correo === "") {
            $errores['correo'] = "Debe introducir el correo de la persona encargada de la tarea";
        } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores['correo'] = "El correo introducido no es válido";
        }

        // Teléfono
        if ($telefono === "") {
            $errores['telefono'] = "Debe introducir el teléfono de la persona encargada de la tarea";
        } else {
            $resultado = M_Funciones::telefonoValido($telefono);
            if ($resultado !== true) $errores['telefono'] = $resultado;
        }

        // Código postal
        if ($codigoPostal !== "" && !preg_match("/^[0-9]{5}$/", $codigoPostal)) {
            $errores['codigo_postal'] = "El código postal introducido no es válido, debe tener 5 números";
        }

        // Fecha
        $fechaActual = date('Y-m-d');
        if ($fechaRealizacion === "") {
            $errores['fecha_realizacion'] = "Debe introducir la fecha de realización de la tarea";
        } else if ($fechaRealizacion <= $fechaActual) {
            $errores['fecha_realizacion'] = "La fecha de realización debe ser posterior a la fecha actual";
        }

        return $errores;
    }

    /**
     * Obtiene la instancia de conexión PDO a la base de datos.
     *
     * Este método es un envoltorio para `DB::getInstance()`, asegurando que todas
     * las operaciones del modelo `M_Tareas` utilicen la misma conexión singleton.
     *
     * @return PDO La instancia de conexión PDO a la base de datos.
     */
    private function db(): PDO
    {
        return DB::getInstance();
    }

    /**
     * Prepara las condiciones de filtrado para las consultas SQL de listado y conteo de tareas.
     *
     * Construye una cláusula WHERE basada en los parámetros 'q' (búsqueda general)
     * y 'estado' (estado de la tarea) obtenidos de la cadena de consulta (GET).
     *
     * @return string La cláusula WHERE SQL, o una cadena vacía si no hay filtros.
     */
    private function filtrosLista(): string
    {
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $estado = isset($_GET['estado']) ? trim($_GET['estado']) : '';

        $conditions = [];

        if ($q !== '') {
            $conditions[] = "(personaNombre LIKE '%$q%' OR descripcionTarea LIKE '%$q%' OR poblacion LIKE '%$q%' OR operarioEncargado LIKE '%$q%')";
        }
        if ($estado !== '') {
            $conditions[] = "estadoTarea = '$estado'";
        }

        return empty($conditions) ? '' : " WHERE " . implode(' AND ', $conditions);
    }

    /**
     * Lista tareas con paginación.
     *
     * Recupera un conjunto de tareas de la base de datos, aplicando filtros
     * definidos por `filtrosLista()` y limitando los resultados para la paginación.
     *
     * @param int $elementosPorPagina Número de tareas a mostrar por página.
     * @param int $paginaActual El número de la página actual.
     * @return array Un array de arrays asociativos, donde cada array representa una tarea.
     */
    public function listar(int $elementosPorPagina, int $offset): array
    {
        $sql = 'SELECT id, nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores 
                FROM tareas'
                . $this->filtrosLista()
                . " ORDER BY id LIMIT $offset, $elementosPorPagina";

        return $this->db()->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Cuenta el número total de registros de tareas, aplicando los filtros definidos.
     *
     * Utiliza la función `filtrosLista()` para aplicar condiciones de búsqueda
     * y devuelve el número total de tareas que cumplen con esos criterios.
     *
     * @return int El número total de tareas filtradas.
     */
    public function contar(): int
    {
        $sql = 'SELECT COUNT(*) FROM tareas' . $this->filtrosLista();
        return (int)$this->db()->query($sql)->fetchColumn();
    }

    /**
     * Busca una tarea específica por su identificador único.
     *
     * Prepara y ejecuta una consulta SQL para obtener todos los detalles de una tarea
     * basándose en el ID proporcionado.
     *
     * @param int $id El identificador de la tarea a buscar.
     * @return array|null Un array asociativo con los datos de la tarea si se encuentra,
     *                    o `null` si no existe ninguna tarea con el ID dado.
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
     * Inserta una nueva tarea en la base de datos.
     *
     * Sanea los datos de entrada y ejecuta una consulta SQL INSERT para añadir
     * una nueva tarea a la tabla `tareas`.
     *
     * @param array $datos Un array asociativo con los datos de la nueva tarea.
     * @return int El ID autoincrementado de la tarea recién creada.
     */
    public function crear(array $datos): int
    {
        $d = $this->limpiar($datos);
        $sql = 'INSERT INTO tareas (nifCif, personaNombre, telefono, correo, descripcionTarea, direccionTarea, poblacion, codigoPostal, provincia, estadoTarea, operarioEncargado, fechaRealizacion, anotacionesAnteriores, anotacionesPosteriores)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)';

        $st = $this->db()->prepare($sql);
        $st->execute([
            $d['nifCif'],$d['personaNombre'],$d['telefono'],$d['correo'],$d['descripcionTarea'],$d['direccionTarea'],$d['poblacion'],$d['codigoPostal'],$d['provincia'],$d['estadoTarea'],$d['operarioEncargado'],$d['fechaRealizacion'],$d['anotacionesAnteriores'],$d['anotacionesPosteriores']
        ]);

        return (int)$this->db()->lastInsertId();
    }

    /**
     * Actualiza una tarea existente en la base de datos.
     *
     * Sanea los datos de entrada y ejecuta una consulta SQL UPDATE para modificar
     * una tarea específica identificada por su ID.
     *
     * @param int $id El identificador de la tarea a actualizar.
     * @param array $datos Un array asociativo con los nuevos datos de la tarea.
     * @return bool `true` si la actualización fue exitosa, `false` en caso contrario.
     */
    public function actualizar(int $id, array $datos): bool
    {
        $d = $this->limpiar($datos);

        $sql = 'UPDATE tareas SET nifCif=?, personaNombre=?, telefono=?, correo=?, descripcionTarea=?, direccionTarea=?, poblacion=?, codigoPostal=?, provincia=?, estadoTarea=?, operarioEncargado=?, fechaRealizacion=?, anotacionesAnteriores=?, anotacionesPosteriores=? WHERE id=?';

        $st = $this->db()->prepare($sql);

        return $st->execute([
            $d['nifCif'],$d['personaNombre'],$d['telefono'],$d['correo'],$d['descripcionTarea'],$d['direccionTarea'],$d['poblacion'],$d['codigoPostal'],$d['provincia'],$d['estadoTarea'],$d['operarioEncargado'],$d['fechaRealizacion'],$d['anotacionesAnteriores'],$d['anotacionesPosteriores'],$id
        ]);
    }

    /**
     * Elimina una tarea de la base de datos por su identificador.
     *
     * Prepara y ejecuta una consulta SQL DELETE para remover una tarea específica.
     *
     * @param int $id El identificador de la tarea a eliminar.
     * @return bool `true` si la eliminación fue exitosa, `false` en caso contrario.
     */
    public function eliminar(int $id): bool
    {
        $st = $this->db()->prepare('DELETE FROM tareas WHERE id=?');
        return $st->execute([$id]);
    }

    /**
     * Sanea y normaliza los datos de una tarea, eliminando espacios en blanco y asegurando
     * que solo se incluyan los campos esperados.
     *
     * Este método es crucial para preparar los datos antes de insertarlos o actualizarlos
     * en la base de datos, previniendo inyecciones SQL básicas y errores de formato.
     *
     * @param array $datos Un array asociativo con los datos de la tarea, típicamente
     *                     recibidos de un formulario.
     * @return array Un array asociativo con los datos saneados y listos para su uso.
     */
    private function limpiar(array $datos): array
    {
        $keys = [
            'nifCif','personaNombre','telefono','correo','descripcionTarea','direccionTarea','poblacion','codigoPostal','provincia','estadoTarea','operarioEncargado','fechaRealizacion','anotacionesAnteriores','anotacionesPosteriores'
        ];

        $out = [];
        foreach ($keys as $k) {
            $out[$k] = isset($datos[$k]) ? trim((string)$datos[$k]) : '';
        }
        return $out;
    }

    /**
     * Actualiza campos específicos de una tarea que son modificables por un operario.
     *
     * Este método permite a un operario actualizar el estado de una tarea y añadir
     * anotaciones posteriores, garantizando que solo estos campos sean alterados.
     *
     * @param int $id El identificador único de la tarea a actualizar.
     * @param array $datos Un array asociativo que contiene los nuevos valores para
     *                     'estadoTarea' y 'anotacionesPosteriores'.
     * @return bool `true` si la actualización se realizó con éxito, `false` en caso contrario.
     */
    public function actualizarOperario(int $id, array $datos): bool
    {
        $estado   = trim((string)($datos['estadoTarea'] ?? ''));
        $anotPost = trim((string)($datos['anotacionesPosteriores'] ?? ''));

        $sql = 'UPDATE tareas SET estadoTarea = ?, anotacionesPosteriores = ? WHERE id = ?';
        $st = $this->db()->prepare($sql);

        return $st->execute([$estado, $anotPost, $id]);
    }
}
