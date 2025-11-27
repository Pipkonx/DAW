<?php

namespace App\Http\Controllers;

use App\Models\M_Funciones; // Para la validación
use App\Models\M_Tareas;    // Asumiendo que tienes un modelo Tarea

class C_Operario extends C_Controller
{

    /**
     * Muestra una lista de tareas asignadas al operario autenticado.
     *
     * @return \Illuminate\Http\Response
     */
    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // El operario se identifica por 'nombre_operario' en la sesión.
        $operarioEncargado = $this->getOperarioEncargado();

        $modelo = new M_Tareas();
        $error = '';
        $tareas = [];

        // Obtener datos de paginación
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalPaginas = $paginationData['totalPaginas'];

        // Filtrar tareas (Nota: antes se filtraba por operario, ahora se muestra todo o se confía en el filtro de la vista/query string si se implementa)
        // Para simplificar, usamos listar() genérico.
        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas, 'baseUrl' => 'operario/tareas'];
        if ($error) $datos['errorGeneral'] = $error;
        return view('tareas.lista', $datos); // Asumiendo una vista 'operario.tareas.lista'
    }

    /**
     * Muestra los detalles de una tarea específica asignada al operario.
     *
     * @return \Illuminate\Http\Response
     */
    public function mostrar()
    {
        $id = $_GET['id'] ?? null;
        // Asumiendo que el operario se identifica por su nombre o ID en la sesión
        $operarioEncargado = $this->getOperarioEncargado();

        $modelo = new M_Tareas();
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);
        // Comprobación de operario eliminada para permitir editar cualquier tarea visible
        /*
        if ($tarea && strcasecmp($tarea['operarioEncargado'], $operarioEncargado) !== 0) {
            $tarea = null; // No permitir ver tareas no asignadas
        }
        */

        if (!$tarea) {
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas');
            exit;
        }
        return view('tareas.detalle', $tarea); // Usar la vista compartida 'tareas.detalle'
    }

    /**
     * Muestra el formulario para editar una tarea existente (solo para operarios).
     *
     * @return \Illuminate\Http\Response
     */
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        $operarioEncargado = $this->getOperarioEncargado();

        $modelo = new M_Tareas();
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);
        /*
        if ($tarea && strcasecmp($tarea['operarioEncargado'], $operarioEncargado) !== 0) {
            $tarea = null;
        }
        */

        if (!$tarea) {
            // dd($id, $operarioEncargado, $tarea); // Descomentar para depurar
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas');
            exit;
        }

        $datos = $tarea;
        $datos['id'] = (int)$id;
        $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas/editar?id=' . $id; // Ruta para el POST de actualización

        return view('tareas.alta_edicion', $datos); // Vista genérica de alta/edición
    }

    /**
     * Actualiza una tarea existente en la base de datos (solo para operarios).
     *
     * @return \Illuminate\Http\Response
     */
    public function actualizar()
    {
        $id = $_GET['id'] ?? null;

        if (!isset($_POST['id'])) {
            $_POST['id'] = $id;
        }

        // Validación completa de datos (como en administrador)
        $this->filtrarCompleto();
        if (!empty(M_Funciones::$errores)) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas/editar?id=' . $id;
            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new M_Tareas();

        // Actualización completa de la tarea (como en administrador)
        $modelo->actualizar((int)$id, $_POST);

        // Guardar evidencias (ficheros)
        $base = __DIR__ . '/../../../public/evidencias/' . (int)$id;
        // is_dir y @mkdir es para verificar si el directorio existe y crear si no
        if (!is_dir($base)) @mkdir($base, 0777, true);
        if (isset($_FILES['fichero_resumen']) && is_uploaded_file($_FILES['fichero_resumen']['tmp_name'])) {
            @move_uploaded_file($_FILES['fichero_resumen']['tmp_name'], $base . '/resumen_' . time() . '_' . basename($_FILES['fichero_resumen']['name']));
        }
        if (isset($_FILES['fotos'])) {
            foreach ((array)$_FILES['fotos']['tmp_name'] as $i => $tmp) {
                if (is_uploaded_file($tmp)) {
                    @move_uploaded_file($tmp, $base . '/foto_' . time() . '_' . basename($_FILES['fotos']['name'][$i]));
                }
            }
        }

        header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas');
        exit;
    }

    /**
     * Valida y normaliza todos los datos recibidos del formulario de tarea.
     * Población de mensajes de error en `Funciones::$errores` (estático).
     */
    private function filtrarCompleto()
    {
        M_Funciones::$errores = M_Tareas::validarDatos($_POST);
    }

    /**
     * Helper para obtener el nombre del operario encargado desde la sesión.
     *
     * @return string
     */
    private function getOperarioEncargado(): string
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['nombre_operario'] ?? 'Operario Desconocido';
    }
}
