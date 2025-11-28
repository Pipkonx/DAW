<?php

namespace App\Http\Controllers;

use App\Models\M_Funciones; // Para la validación
use App\Models\M_Tareas;    // Asumiendo que tienes un modelo Tarea

class C_Administrador extends C_Controller
{

    /**
     * Muestra una lista paginada de todas las tareas disponibles para el administrador.
     *
     * Este método verifica el rol del usuario para asegurar que solo los administradores
     * puedan acceder. Recupera las tareas de la base de datos, aplica paginación
     * y las presenta en una vista.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que carga la vista con la lista de tareas.
     */
    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $modelo = new M_Tareas();
        $error = '';
        $tareas = [];

        // Obtener datos de paginación
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];

        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas, 'baseUrl' => 'admin/tareas'];
        if ($error) $datos['errorGeneral'] = $error;
        return view('tareas.lista', $datos); // Asumiendo una vista 'admin.tareas.lista'
    }

    /**
     * Muestra el formulario para crear una nueva tarea.
     *
     * Este método prepara un array de datos iniciales para un formulario de creación
     * de tareas, incluyendo campos vacíos y la URL de acción del formulario.
     * Requiere que el usuario tenga rol de administrador.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que carga la vista del formulario de alta/edición de tareas.
     */
    public function crear()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        // Datos iniciales para el formulario vacío
        $datos = [
            'nifCif' => '',
            'personaNombre' => '',
            'telefono' => '',
            'descripcionTarea' => '',
            'correo' => '',
            'direccionTarea' => '',
            'poblacion' => '',
            'codigoPostal' => '',
            'provincia' => '',
            'estadoTarea' => 'R', // Default to Realizada (Completed)
            'operarioEncargado' => '',
            'fechaRealizacion' => '',
            'anotacionesAnteriores' => '',
            'anotacionesPosteriores' => '',
            'formActionUrl' => dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/crear', // Ruta para el POST de creación
        ];
        return view('tareas.alta_edicion', $datos); // Asumiendo una vista 'admin.tareas.alta_edicion'
    }

    /**
     * Almacena una nueva tarea en la base de datos tras validar los datos recibidos.
     *
     * Este método filtra y valida los datos del formulario. Si hay errores, vuelve a
     * mostrar el formulario con los errores. Si los datos son válidos, crea la tarea
     * en la base de datos y redirige a la lista de tareas con un mensaje de éxito.
     * Requiere que el usuario tenga rol de administrador.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que redirige a la lista de tareas o vuelve al formulario de creación.
     */
    public function guardar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        // Validar datos
        $this->filtrar();
        if (!empty(M_Funciones::$errores)) {
            // Si hay errores, volver al formulario con los datos y errores
            $datos = $_POST;
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/crear';
            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new M_Tareas();
        $modelo->crear($_POST); // Usar el método crear del modelo Tareas
        $mensaje = 'Tarea creada correctamente';

        // Recargar la lista de tareas para la vista
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];
        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        return view('tareas.lista', $datos);
    }

    /**
     * Muestra los detalles de una tarea específica.
     *
     * Este método recupera una tarea de la base de datos utilizando su ID
     * y la presenta en una vista de detalle. Si la tarea no se encuentra,
     * se muestra un mensaje de error. Requiere que el usuario tenga rol de administrador.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que carga la vista de detalle de la tarea o una lista con un mensaje de error.
     */
    public function mostrar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $id = $_GET['id'] ?? null;
        $modelo = new M_Tareas();
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);

        if (!$tarea) {
            // Si no existe la tarea, redirigir o mostrar error
            $datos = ['errorGeneral' => 'La tarea no existe.'];
            return view('tareas.lista', $datos);
        }
        return view('tareas.detalle', $tarea); // Asumiendo una vista 'admin.tareas.detalle'
    }

    /**
     * Muestra el formulario para editar una tarea existente.
     *
     * Este método recupera los datos de una tarea específica por su ID y los precarga
     * en un formulario de edición. Si la tarea no se encuentra, se redirige con un
     * mensaje de error. Requiere que el usuario tenga rol de administrador.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que carga la vista del formulario de alta/edición de tareas.
     */
    public function editar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $id = $_GET['id'] ?? null;
        $modelo = new M_Tareas();
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);

        if (!$tarea) {
            $datos = ['errorGeneral' => 'No se pudo cargar la tarea para edición.'];
            return view('tareas.lista', $datos);
        }

        // Preparar datos para la vista de edición
        $datos = $tarea; // Los datos de la tarea encontrada
        $datos['id'] = (int)$id;
        $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/editar?id=' . $id; // Ruta para el POST de edición

        // Escanear ficheros
        $base = __DIR__ . '/../../../public/evidencias/' . $id;
        $ficherosResumen = glob($base . '/resumen_*');
        $fotos = glob($base . '/foto_*');
        
        $datos['ficherosResumen'] = array_map('basename', $ficherosResumen ?: []);
        $datos['fotos'] = array_map('basename', $fotos ?: []);

        return view('tareas.alta_edicion', $datos);
    }

    /**
     * Actualiza una tarea existente en la base de datos con los datos recibidos del formulario.
     *
     * Este método valida los datos del formulario y, si son válidos, actualiza la tarea
     * correspondiente en la base de datos. Si hay errores de validación, vuelve a mostrar
     * el formulario de edición con los errores. Requiere que el usuario tenga rol de administrador.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que redirige a la lista de tareas o vuelve al formulario de edición.
     */
    public function actualizar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $id = $_GET['id'] ?? null; // El ID viene por GET en la URL para la edición
        if (!isset($_POST['id'])) { // Asegurarse de que el ID también esté en POST para el modelo
            $_POST['id'] = $id;
        }

        // Manejo de historial de notas: Si se añade una nota posterior, pasa a anterior
        // y la posterior queda vacía.
        if (!empty($_POST['anotacionesPosteriores'])) {
             $_POST['anotacionesAnteriores'] = $_POST['anotacionesPosteriores'];
             $_POST['anotacionesPosteriores'] = '';
        }

        $this->filtrar();
        if (!empty(M_Funciones::$errores)) {
            $datos = $_POST;
            $datos['id'] = (int)$id;
            $datos['formActionUrl'] = dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/editar?id=' . $id;
            
            // Re-escanear ficheros para la vista en caso de error
            $base = __DIR__ . '/../../../public/evidencias/' . $id;
            $ficherosResumen = glob($base . '/resumen_*');
            $fotos = glob($base . '/foto_*');
            $datos['ficherosResumen'] = array_map('basename', $ficherosResumen ?: []);
            $datos['fotos'] = array_map('basename', $fotos ?: []);

            return view('tareas.alta_edicion', $datos);
        }

        $modelo = new M_Tareas();
        $modelo->actualizar((int)$id, $_POST); // Usar el método actualizar del modelo Tareas

        // Guardar evidencias
        $base = __DIR__ . '/../../../public/evidencias/' . $id;
        if (!is_dir($base)) @mkdir($base, 0777, true);

        // Gestionar Resumen (Solo 1 permitido)
        $existingResumen = glob($base . '/resumen_*');
        if (!empty($_FILES['fichero_resumen']['tmp_name'])) {
            // Si existe uno, borrarlo para asegurar límite de 1 fichero
            if ($existingResumen) {
                foreach ($existingResumen as $f) unlink($f);
            }
            @move_uploaded_file($_FILES['fichero_resumen']['tmp_name'], $base . '/resumen_' . time() . '_' . basename($_FILES['fichero_resumen']['name']));
        }

        if (!empty($_FILES['fotos']['tmp_name'])) {
            foreach ((array)$_FILES['fotos']['tmp_name'] as $i => $tmp) {
                if (is_uploaded_file($tmp)) {
                    @move_uploaded_file($tmp, $base . '/foto_' . time() . '_' . basename($_FILES['fotos']['name'][$i]));
                }
            }
        }

        $mensaje = 'Tarea actualizada correctamente';

        // Recargar la lista de tareas para la vista
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];
        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        return view('tareas.lista', $datos);
    }

    /**
     * Elimina una tarea específica de la base de datos.
     *
     * Este método recibe el ID de la tarea a eliminar a través de una solicitud POST.
     * Verifica el rol del usuario para asegurar que solo los administradores puedan
     * realizar esta acción. Tras la eliminación, recarga la lista de tareas con un
     * mensaje de éxito. Si el usuario no es administrador, se le redirige al login.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que redirige a la lista de tareas con un mensaje de éxito.
     */
    public function eliminar()
    {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        $id = $_POST['id'] ?? null; // El ID para eliminar viene por POST
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }

        $modelo = new M_Tareas(); // Corregido de 'Tareas()' a 'M_Tareas()'
        $modelo->eliminar((int)$id);
        $mensaje = 'Tarea eliminada correctamente';

        // Recargar la lista de tareas para la vista
        $paginationData = $this->getPaginationData($modelo);
        $paginaActual = $paginationData['paginaActual'];
        $totalElementos = $paginationData['totalElementos'];
        $totalPaginas = $paginationData['totalPaginas'];
        $tareas = $modelo->listar(self::TAREAS_POR_PAGINA, $paginaActual);

        $datos = ['mensaje' => $mensaje, 'tareas' => $tareas, 'paginaActual' => $paginaActual, 'totalPaginas' => $totalPaginas];
        return view('tareas.lista', $datos);
    }
    
    /**
     * Elimina un fichero asociado a una tarea.
     */
    public function eliminarFichero() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        
        $id = (int)($_POST['id'] ?? 0);
        $filename = $_POST['filename'] ?? '';
        
        if ($id && $filename) {
            $base = __DIR__ . '/../../../public/evidencias/' . $id;
            $filepath = $base . '/' . basename($filename); 
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
        // Redirigir de vuelta a la edición
        header('Location: ' . dirname($_SERVER['SCRIPT_NAME']) . '/admin/tareas/editar?id=' . $id); exit;
    }

    /**
     * Muestra una vista de confirmación antes de eliminar una tarea.
     *
     * Este método recupera los detalles de una tarea específica por su ID y los presenta
     * en una vista de confirmación de eliminación. Si la tarea no se encuentra,
     * se redirige con un mensaje de error. Requiere que el usuario tenga rol de administrador.
     *
     * @return \Illuminate\Http\Response Una respuesta HTTP que carga la vista de confirmación de eliminación o una lista con un mensaje de error.
     */
    public function confirmarEliminacion()
    {
        $id = $_GET['id'] ?? null;
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (($_SESSION['rol'] ?? '') !== 'admin') {
            return view('autenticacion/login', ['errorGeneral' => 'Acceso restringido a administradores']);
        }
        $modelo = new M_Tareas(); // Corregido de 'Tareas()' a 'M_Tareas()'
        $tarea = null;
        $tarea = $modelo->buscar((int)$id);
        if (!$tarea) {
            $datos = ['errorGeneral' => 'No se pudo cargar la tarea para eliminación.'];
            return view('tareas.lista', $datos);
        }
        $tarea['id'] = (int)$id;
        return view('tareas.confirmar_eliminacion', $tarea); // Asumiendo una vista 'admin.tareas.confirmar_eliminacion'
    }

    /**
     * Valida y normaliza los datos recibidos del formulario de tarea.
     *
     * Este método utiliza el modelo `M_Tareas` para validar los datos de una tarea
     * recibidos a través de una solicitud POST. Los errores de validación se almacenan
     * en el array estático `M_Funciones::$errores`.
     *
     * @return void
     */
    private function filtrar()
    {
        M_Funciones::$errores = M_Tareas::validarDatos($_POST); // Corregido de 'Tareas::validarDatos' a 'M_Tareas::validarDatos'
    }
}
