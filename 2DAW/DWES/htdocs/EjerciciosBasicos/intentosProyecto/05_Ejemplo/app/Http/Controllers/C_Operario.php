<?php

namespace App\Http\Controllers;

use App\Models\M_Funciones; 
use App\Models\M_Tareas;

class C_Operario extends C_Controller
{
    /**
     * Lista tareas asignadas al operario.
     */
    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $modelo = new M_Tareas();

        // Datos de paginación
        $paginaActual = $_GET['pagina'] ?? 1;
        //ceil es para redondear al de arriba
        $totalPaginas = ceil($modelo->contar() / self::TAREAS_POR_PAGINA);

        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        return view('tareas.lista', [
            'tareas' => $tareas,
            'paginaActual' => $paginaActual,
            'totalPaginas' => $totalPaginas,
            'baseUrl' => 'operario/tareas'
        ]);
    }

    /**
     * Muestra detalles de una tarea específica.
     */
    public function mostrar()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_GET['id'] ?? 0);
        $tarea = (new M_Tareas())->buscar($id);

        if (!$tarea) {
            // dirname es para obtener la ruta del directorio actual
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas'); exit;
        }

        return view('tareas.detalle', $tarea);
    }

    /**
     * Muestra el formulario de edición de tarea.
     */
    public function editar()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_GET['id'] ?? 0);
        $modelo = new M_Tareas();
        $tarea = $modelo->buscar($id);

        if (!$tarea) {
            // header es para redirigir al usuario a la lista dee usuarios
            header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas'); exit;
        }

        $tarea['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas/editar?id=' . $id;
        return view('tareas.alta_edicion', $tarea);
    }

    /**
     * Actualiza la tarea y gestiona la subida de archivos.
     */
    public function actualizar()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $_POST['id'] = $id;

        // Validación
        M_Funciones::$errores = M_Tareas::validarDatos($_POST);
        if (!empty(M_Funciones::$errores)) {
            $_POST['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas/editar?id=' . $id;
            return view('tareas.alta_edicion', $_POST);
        }

        // Actualización de datos
        (new M_Tareas())->actualizar($id, $_POST);

        // Guardar evidencias
        $base = __DIR__ . '/../../../public/evidencias/' . $id;
        // los permisos 0777 son para que el directorio sea accesible para todos
        // el @mkdir es para silenciar los posibles errores
        // !is_dir hace que si el directorio no existe lo crea
        if (!is_dir($base)) @mkdir($base, 0777, true);

        if (!empty($_FILES['fichero_resumen']['tmp_name'])) {
            @move_uploaded_file($_FILES['fichero_resumen']['tmp_name'], $base . '/resumen_' . time() . '_' . basename($_FILES['fichero_resumen']['name']));
        }

        if (!empty($_FILES['fotos']['tmp_name'])) {
            foreach ((array)$_FILES['fotos']['tmp_name'] as $i => $tmp) {
                if (is_uploaded_file($tmp)) {
                    @move_uploaded_file($tmp, $base . '/foto_' . time() . '_' . basename($_FILES['fotos']['name'][$i]));
                }
            }
        }

        header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas'); exit;
    }

    /**
     * Obtiene el nombre del operario desde la sesión.
     */
    private function getOperarioEncargado(): string
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        return $_SESSION['nombre_operario'] ?? 'Operario Desconocido';
    }
}
