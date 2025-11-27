<?php

namespace App\Http\Controllers;

use App\Models\Funciones; // Para la validación
use App\Models\Tareas;    // Asumiendo que tienes un modelo Tarea
use Illuminate\Support\Facades\Auth; // Para obtener el usuario autenticado (si se usa)
use App\Models\Usuarios; // Para la gestión de usuarios

class C_Operario extends C_Controller
{

    /**
     * Muestra una lista de tareas asignadas al operario autenticado.
     *
     * @return \Illuminate\Http\Response
     */
    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        // El operario se identifica por 'nombre_operario' en la sesión.
        $operarioEncargado = $this->getOperarioEncargado();

        $modelo = new Tareas();
        $error = '';
        $tareas = [];

        // Obtener datos de paginación usando el método de conteo específico para operarios
        $paginationData = $this->getPaginationData($modelo, 'contarPorOperario', $operarioEncargado);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];

        try {
            // Filtrar tareas por el operario encargado
            $tareas = $modelo->listarPorOperario(self::TAREAS_POR_PAGINA, $paginaActual, $operarioEncargado);
        } catch (\Throwable $e) {
            $error = 'No se pudo obtener el listado de tareas asignadas.';
            $tareas = []; // Asegurarse de que las tareas estén vacías en caso de error
        }
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

        $modelo = new Tareas();
        $tarea = null;
        try {
            $tarea = $modelo->buscar((int)$id);
            // Comprobación de operario eliminada para permitir editar cualquier tarea visible
            /*
            if ($tarea && strcasecmp($tarea['operarioEncargado'], $operarioEncargado) !== 0) {
                $tarea = null; // No permitir ver tareas no asignadas
            }
            */
        } catch (\Throwable $e) {
        }

        if (!$tarea) {
            return redirect('operario/tareas')->with('errorGeneral', 'La tarea no existe o no está asignada a usted.');
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

        $modelo = new Tareas();
        $tarea = null;
        try {
            $tarea = $modelo->buscar((int)$id);
            /*
            if ($tarea && strcasecmp($tarea['operarioEncargado'], $operarioEncargado) !== 0) {
                $tarea = null;
            }
            */
        } catch (\Throwable $e) {
        }

        if (!$tarea) {
            // dd($id, $operarioEncargado, $tarea); // Descomentar para depurar
            return redirect('operario/tareas')->with('errorGeneral', 'La tarea no existe o no está asignada a usted.');
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
        $operarioEncargado = $this->getOperarioEncargado();

        if (!isset($_POST['id'])) {
            $_POST['id'] = $id;
        }

        // Validación completa de datos (como en administrador)
        $this->filtrarCompleto(); 
        if (!empty(Funciones::$errores)) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas/editar?id=' . $id;
            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new Tareas();
        try {
            // Actualización completa de la tarea (como en administrador)
            $modelo->actualizar((int)$id, $_POST); 
            
            // Guardar evidencias (ficheros)
            $base = __DIR__ . '/../../../public/evidencias/' . (int)$id;
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

            return redirect(url('operario/tareas'))->with('mensaje', 'Tarea actualizada correctamente.');
        } catch (\Throwable $e) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['errorGeneral'] = 'No se pudo actualizar la tarea. Revise la conexión y la tabla.';
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas/editar?id=' . $id;
            return view('tareas.alta_edicion', $datos);
        }
    }

    /**
     * Valida y normaliza todos los datos recibidos del formulario de tarea.
     * Población de mensajes de error en `Funciones::$errores` (estático).
     */
    private function filtrarCompleto()
    {
        Funciones::$errores = Tareas::validarDatos($_POST);
    }

    /**
     * Helper para obtener el nombre del operario encargado desde la sesión.
     *
     * @return string
     */
    private function getOperarioEncargado(): string
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        return $_SESSION['nombre_operario'] ?? 'Operario Desconocido';
    }

    /**
     * Muestra el formulario para que el operario edite su propio perfil.
     *
     * @return \Illuminate\Http\Response
     */
    public function editarPerfilOperario()
    {
        $operarioEncargado = $this->getOperarioEncargado();
        $modeloUsuarios = new Usuarios();
        $usuario = null;

        try {
            $usuario = $modeloUsuarios->buscarPorNombre($operarioEncargado);
        } catch (\Throwable $e) {
            // Manejar el error, por ejemplo, redirigir con un mensaje de error
            return redirect(url('operario/tareas'))->with('errorGeneral', 'No se pudo cargar su perfil.');
        }

        if (!$usuario) {
            return redirect(url('operario/tareas'))->with('errorGeneral', 'Su perfil no fue encontrado.');
        }

        $datos = $usuario;
        $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/perfil/actualizar';
        $datos['hideRole'] = true;
        $datos['cancelUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/tareas';

        return view('usuarios.formulario', $datos); // Reutilizar la vista de formulario de usuario
    }

    /**
     * Actualiza el perfil del operario en la base de datos.
     *
     * @return \Illuminate\Http\Response
     */
    public function actualizarPerfilOperario()
    {
        $operarioEncargado = $this->getOperarioEncargado();
        $modeloUsuarios = new Usuarios();
        $usuarioExistente = null;

        try {
            $usuarioExistente = $modeloUsuarios->buscarPorNombre($operarioEncargado);
        } catch (\Throwable $e) {
            return redirect(url('operario/perfil/editar'))->with('errorGeneral', 'Error al buscar su perfil para actualizar.');
        }

        if (!$usuarioExistente) {
            return redirect(url('operario/perfil/editar'))->with('errorGeneral', 'Su perfil no fue encontrado para actualizar.');
        }

        $id = $usuarioExistente['id'];
        $usuario = trim((string)($_POST['usuario'] ?? ''));
        $clave = (string)($_POST['clave'] ?? '');
        $rol = $usuarioExistente['rol']; // El rol no debe ser modificable por el operario

        // Validaciones básicas
        if ($usuario === '' || $clave === '') {
            $datos = $_POST;
            $datos['errorGeneral'] = 'Usuario y clave no pueden estar vacíos.';
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/perfil/actualizar';
            return view('usuarios.formulario', $datos);
        }

        try {
            $modeloUsuarios->actualizar((int)$id, $usuario, $clave, $rol);
            // Actualizar el nombre de operario en la sesión si ha cambiado
            if ($usuario !== $operarioEncargado) {
                if (session_status() == PHP_SESSION_NONE) { session_start(); }
                $_SESSION['nombre_operario'] = $usuario;
            }
            return redirect(url('operario/tareas'))->with('mensaje', 'Perfil actualizado correctamente.');
        } catch (\Throwable $e) {
            $datos = $_POST;
            $datos['errorGeneral'] = 'No se pudo actualizar el perfil. Revise la conexión y los datos.';
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/operario/perfil/actualizar';
            return view('usuarios.formulario', $datos);
        }
    }
}
