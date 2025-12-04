<?php

namespace App\Http\Controllers;

use App\Models\M_Funciones; 
use App\Models\M_Tareas;

class C_Operario extends C_Controller
{
    /**
     * Muestra una lista paginada de tareas asignadas al operario.
     *
     * Obtiene las tareas del modelo M_Tareas, calcula la paginación
     * y renderiza la vista 'tareas.lista' con los datos.
     *
     * @return \Illuminate\View\View Retorna la vista con la lista de tareas.
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
     * Muestra los detalles de una tarea específica.
     *
     * Busca una tarea por su ID y, si la encuentra, renderiza la vista 'tareas.detalle'.
     * Si la tarea no existe, redirige al operario a la lista de tareas.
     *
     * @return \Illuminate\View\View|void Retorna la vista con los detalles de la tarea o redirige.
     */
    public function mostrar()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_GET['id'] ?? 0);
        $tarea = (new M_Tareas())->buscar($id);

        if (!$tarea) {
            // dirname es para obtener la ruta del directorio actual
            header('Location: ' . url('/operario/tareas')); exit;
        }

        return view('tareas.detalle', $tarea);
    }

    /**
     * Muestra el formulario para editar una tarea existente.
     *
     * Busca una tarea por su ID y, si la encuentra, prepara los datos
     * para el formulario de edición y renderiza la vista 'tareas.edicion_operario'.
     * Si la tarea no existe, redirige al operario a la lista de tareas.
     *
     * @return \Illuminate\View\View|void Retorna la vista del formulario de edición o redirige.
     */
    public function editar()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_GET['id'] ?? 0);
        $modelo = new M_Tareas();
        $tarea = $modelo->buscar($id);

        if (!$tarea) {
            // header es para redirigir al usuario a la lista dee usuarios
            // header('Location: ' . url('/operario/tareas')); exit;
        }

        $tarea['formActionUrl'] = url('/operario/tareas/editar?id=' . $id);
        
        // Escanear ficheros
        $base = __DIR__ . '/../../../public/evidencias/' . $id;
        $ficherosResumen = [];
        $fotos = [];

        if (is_dir($base)) {
            $archivos = scandir($base);
            foreach ($archivos as $archivo) {
                if ($archivo == '.' || $archivo == '..') continue;
                
                if (strpos($archivo, 'resumen_') === 0) {
                    $ficherosResumen[] = $archivo;
                } elseif (strpos($archivo, 'foto_') === 0) {
                    $fotos[] = $archivo;
                }
            }
        }
        
        $tarea['ficherosResumen'] = $ficherosResumen;
        $tarea['fotos'] = $fotos;

        return view('tareas.edicion_operario', $tarea);
    }

    /**
     * Actualiza una tarea existente y gestiona la subida de archivos asociados.
     *
     * Valida los datos recibidos, actualiza la tarea en la base de datos
     * y guarda los ficheros de resumen y fotos en el sistema de archivos.
     * Redirige a la lista de tareas tras la actualización.
     *
     * @return void Redirige a la lista de tareas o muestra el formulario con errores.
     */
    public function actualizar()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
        $_POST['id'] = $id;

        $modelo = new M_Tareas();
        $tareaAntigua = $modelo->buscar($id);
        
        if (!$tareaAntigua) {
             header('Location: ' . url('/operario/tareas')); exit;
        }

        // Manejo de historial de notas
        // Si se agrega una nueva nota (posterior no está vacía), se mueve a anterior
        // y la posterior se limpia.
        if (!empty($_POST['anotacionesPosteriores'])) {
             $_POST['anotacionesAnteriores'] = $_POST['anotacionesPosteriores'];
             $_POST['anotacionesPosteriores'] = '';
        }

        // Combinar con datos antiguos para pasar validación de campos requeridos que el Operario no puede cambiar
        $datosCompletos = array_merge($tareaAntigua, $_POST);

        // Validación
        M_Funciones::$errores = M_Tareas::validarDatos($datosCompletos);
        if (!empty(M_Funciones::$errores)) {
            $datosCompletos['formActionUrl'] = url('/operario/tareas/editar?id=' . $id);
            
            // Re-escanear ficheros para la vista
             $base = __DIR__ . '/../../../public/evidencias/' . $id;
             $ficherosResumen = [];
             $fotos = [];

             if (is_dir($base)) {
                 $archivos = scandir($base);
                 foreach ($archivos as $archivo) {
                     if ($archivo == '.' || $archivo == '..') continue;
                     
                     if (strpos($archivo, 'resumen_') === 0) {
                         $ficherosResumen[] = $archivo;
                     } elseif (strpos($archivo, 'foto_') === 0) {
                         $fotos[] = $archivo;
                     }
                 }
             }
             $datosCompletos['ficherosResumen'] = $ficherosResumen;
             $datosCompletos['fotos'] = $fotos;

            return view('tareas.edicion_operario', $datosCompletos);
        }

        // Actualización de datos
        $modelo->actualizar($id, $datosCompletos);

        // Guardar evidencias
        $base = __DIR__ . '/../../../public/evidencias/' . $id;
        // los permisos 0777 son para que el directorio sea accesible para todos
        // el @mkdir es para silenciar los posibles errores
        // !is_dir hace que si el directorio no existe lo crea
        if (!is_dir($base)) @mkdir($base, 0777, true);

        // Gestionar Resumen (Solo 1 permitido)
        if (!empty($_FILES['fichero_resumen']['tmp_name'])) {
            // Si existe uno, borrarlo para asegurar límite de 1 fichero (reemplazo)
            if (is_dir($base)) {
                $archivos = scandir($base);
                foreach ($archivos as $archivo) {
                    if ($archivo == '.' || $archivo == '..') continue;
                    if (strpos($archivo, 'resumen_') === 0) {
                        unlink($base . '/' . $archivo);
                    }
                }
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
        
        header('Location: ' . url('/operario/tareas')); exit;
    }
    
    /**
     * Elimina un fichero asociado a una tarea.
     */
    public function eliminarFichero() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        
        $id = (int)($_POST['id'] ?? 0);
        $filename = $_POST['filename'] ?? '';
        
        // Validar que el usuario tiene permiso para eliminar este archivo (p.ej. pertenece a una tarea asignada)
        // Por ahora asumimos que el control de acceso se maneja por sesión/rutas
        
        if ($id && $filename) {
            $base = __DIR__ . '/../../../public/evidencias/' . $id;
            $filepath = $base . '/' . basename($filename); 
            if (file_exists($filepath)) {
                unlink($filepath);
            }
        }
        // Redirigir de vuelta a la edición
        header('Location: ' . url('/operario/tareas/editar?id=' . $id)); exit;
    }

    /**
     * Obtiene el nombre del operario encargado desde la sesión.
     *
     * Si la sesión no está iniciada, la inicia. Retorna el nombre del operario
     * almacenado en la sesión o un valor por defecto si no está definido.
     *
     * @return string El nombre del operario encargado.
     */
    
    
    private function getOperarioEncargado(): string
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        return $_SESSION['nombre_operario'] ?? 'Operario Desconocido';
    }
}
