<?php
require_once __DIR__ . '/../models/Tarea.php';
require_once __DIR__ . '/../models/Usuario.php';

class TareaController
{
    private $tareaModel;
    private $usuarioModel;

    public function __construct()
    {
        $this->tareaModel = new Tarea();
        $this->usuarioModel = new Usuario();
    }

    // Validar título
    private function validarTitulo($titulo)
    {
        $errores = [];
        if (empty($titulo)) {
            $errores[] = "El título no puede estar vacío";
        } elseif (strlen($titulo) < 3) {
            $errores[] = "El título debe tener al menos 3 caracteres";
        }
        return $errores;
    }

    // Listar todas las tareas con filtros
    public function index()
    {
        $usuarioId = $_GET['usuario_id'] ?? null;
        $completada = $_GET['completada'] ?? null;

        if ($usuarioId !== null && $usuarioId !== '') {
            $tareas = $this->tareaModel->getByUsuario($usuarioId);
        } elseif ($completada !== null && $completada !== '') {
            $tareas = $this->tareaModel->getByEstado($completada);
        } else {
            $tareas = $this->tareaModel->getAll();
        }

        $usuarios = $this->usuarioModel->getAll();
        $filtroUsuario = $usuarioId ?? '';
        $filtroCompletada = $completada ?? '';

        require_once __DIR__ . '/../views/tareas/index.php';
    }

    // Mostrar formulario de creación
    public function crear()
    {
        $usuarios = $this->usuarioModel->getAll();
        $errores = [];
        $datos = ['titulo' => '', 'descripcion' => '', 'usuario_id' => ''];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'titulo' => trim($_POST['titulo'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'usuario_id' => $_POST['usuario_id'] ?? ''
            ];

            // Validaciones
            $errores = $this->validarDatos($datos);

            if (empty($errores)) {
                if ($this->tareaModel->create($datos)) {
                    header('Location: index.php?controller=tarea&action=index');
                    exit;
                } else {
                    $errores[] = "Error al crear la tarea";
                }
            }
        }

        require_once __DIR__ . '/../views/tareas/crear.php';
    }

    // Mostrar formulario de edición
    public function editar()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?controller=tarea&action=index');
            exit;
        }

        $tarea = $this->tareaModel->getById($id);
        if (!$tarea) {
            header('Location: index.php?controller=tarea&action=index');
            exit;
        }

        $usuarios = $this->usuarioModel->getAll();
        $errores = [];
        $datos = $tarea;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'titulo' => trim($_POST['titulo'] ?? ''),
                'descripcion' => trim($_POST['descripcion'] ?? ''),
                'usuario_id' => $_POST['usuario_id'] ?? '',
                'completada' => $_POST['completada'] ?? '0'
            ];

            // Validaciones
            $errores = $this->validarDatos($datos);

            if (empty($errores)) {
                if ($this->tareaModel->update($id, $datos)) {
                    header('Location: index.php?controller=tarea&action=index');
                    exit;
                } else {
                    $errores[] = "Error al actualizar la tarea";
                }
            }
        }

        require_once __DIR__ . '/../views/tareas/editar.php';
    }

    // Marcar tarea como completada
    public function marcarCompletada()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->tareaModel->marcarComoCompletada($id);
        }
        header('Location: index.php?controller=tarea&action=index');
        exit;
    }

    // Marcar tarea como pendiente
    public function marcarPendiente()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->tareaModel->marcarComoPendiente($id);
        }
        header('Location: index.php?controller=tarea&action=index');
        exit;
    }

    // Eliminar tarea
    public function eliminar()
    {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->tareaModel->delete($id);
        }
        header('Location: index.php?controller=tarea&action=index');
        exit;
    }

    private function validarDatos($datos)
    {
        $errores = [];

        if (empty($datos['titulo'])) {
            $errores[] = 'El título es requerido';
        } elseif (strlen($datos['titulo']) < 3) {
            $errores[] = 'El título debe tener al menos 3 caracteres';
        }

        if (empty($datos['descripcion'])) {
            $errores[] = 'La descripción es requerida';
        } elseif (strlen($datos['descripcion']) < 5) {
            $errores[] = 'La descripción debe tener al menos 5 caracteres';
        }

        if (empty($datos['usuario_id'])) {
            $errores[] = 'Debe seleccionar un usuario';
        } elseif (!is_numeric($datos['usuario_id'])) {
            $errores[] = 'Usuario inválido';
        }

        return $errores;
    }
}
