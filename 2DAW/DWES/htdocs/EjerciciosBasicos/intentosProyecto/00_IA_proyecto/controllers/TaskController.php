<?php
require_once "models/Task.php";
require_once "models/User.php";

class TaskController {
    private $model;

    public function __construct() {
        $this->model = new Task();
    }

    public function list() {
        $role = $_GET['role'] ?? 'operador';
        $user = $_GET['user'] ?? '';

        if ($role === 'admin') {
            $tareas = $this->model->getAll();
        } else {
            $tareas = $this->model->getByOperario($user);
        }

        // Obtener usuario actual para enlaces como "Editar perfil"
        $userModel = new User();
        $currentUser = $user ? $userModel->getByNombre($user) : null;

        require "views/tasks/list.php";
    }

    public function create() {
        $errors = [];
        $userModel = new User();
        $allUsers = $userModel->getAll();
        $operators = array_values(array_filter($allUsers, function($u) { return ($u['rol'] ?? '') === 'operador'; }));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validaciones PHP (no HTML)
            $dni = trim($_POST['nif_cif'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $personaContacto = trim($_POST['persona_contacto'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');

            // Obligatorios
            if ($dni === '') {
                $errors[] = "El NIF/CIF es obligatorio";
            }
            $isNIF = preg_match('/^[0-9]{8}[A-Za-z]$/', $dni);
            $isCIF = preg_match('/^[ABCDEFGHJKLMNPQRSUVW][0-9]{7}[0-9A-J]$/', $dni);
            if (!$isNIF && !$isCIF) {
                $errors[] = "DNI/NIF-CIF no válido";
            }

            if ($personaContacto === '') {
                $errors[] = "La persona de contacto es obligatoria";
            }

            if (!preg_match('/^\d{9}$/', $telefono)) {
                $errors[] = "Teléfono debe tener 9 dígitos";
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Correo electrónico no válido";
            }

            if ($descripcion === '') {
                $errors[] = "La descripción es obligatoria";
            }

            // Operario requerido y debe ser un operador válido
            $operarioSel = trim($_POST['operario'] ?? '');
            if ($operarioSel === '') {
                $errors[] = "Debes seleccionar un operario encargado";
            } else {
                $validNames = array_map(function($u){ return $u['nombre']; }, $operators);
                if (!in_array($operarioSel, $validNames, true)) {
                    $errors[] = "Operario no válido";
                }
            }

            if (empty($errors)) {
                $data = [
                    ':nif_cif' => $dni,
                    ':persona_contacto' => $personaContacto,
                    ':telefono' => $telefono,
                    ':descripcion' => $descripcion,
                    ':correo' => $correo,
                    ':direccion' => $_POST['direccion'] ?? '',
                    ':poblacion' => $_POST['poblacion'] ?? '',
                    ':codigo_postal' => $_POST['codigo_postal'] ?? '',
                    ':provincia' => $_POST['provincia'] ?? '',
                    ':estado' => $_POST['estado'] ?? 'B',
                    ':operario' => $operarioSel,
                    ':fecha_realizacion' => $_POST['fecha_realizacion'] ?? '',
                    ':anotaciones_antes' => $_POST['anotaciones_antes'] ?? ''
                ];
                $this->model->create($data);
                header("Location: index.php?controller=Task&action=list&role=admin");
                return;
            }
        }
        // Exponer errores a la vista
        $validationErrors = $errors;
        // Exponer lista de operadores a la vista
        require "views/tasks/create.php";
    }

    public function edit() {
        $tarea = $this->model->getById($_GET['id']);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validaciones PHP de teléfono y correo
            $telefono = trim($_POST['telefono'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            if (!preg_match('/^\d{9}$/', $telefono)) {
                $errors[] = "Teléfono debe tener 9 dígitos";
            }
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Correo electrónico no válido";
            }

            // Manejo de ficheros
            $resumen = $tarea['fichero_resumen'];
            $fotos = !empty($tarea['fotos']) ? json_decode($tarea['fotos'], true) : [];

            // Crear directorios si no existen
            if (!is_dir('uploads/resumenes')) {
                @mkdir('uploads/resumenes', 0777, true);
            }
            if (!is_dir('uploads/fotos')) {
                @mkdir('uploads/fotos', 0777, true);
            }

            if (!empty($_FILES['resumen']['name'])) {
                $nombreArchivo = time() . "_" . basename($_FILES['resumen']['name']);
                move_uploaded_file($_FILES['resumen']['tmp_name'], "uploads/resumenes/" . $nombreArchivo);
                $resumen = $nombreArchivo;
            }

            if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
                foreach ($_FILES['fotos']['tmp_name'] as $i => $tmp) {
                    if (!empty($_FILES['fotos']['name'][$i])) {
                        $nombreFoto = time() . "_" . basename($_FILES['fotos']['name'][$i]);
                        move_uploaded_file($tmp, "uploads/fotos/" . $nombreFoto);
                        $fotos[] = $nombreFoto;
                    }
                }
            }

            if (empty($errors)) {
                $data = [
                    ':persona_contacto' => $_POST['persona_contacto'] ?? '',
                    ':telefono' => $telefono,
                    ':descripcion' => $_POST['descripcion'] ?? '',
                    ':correo' => $correo,
                    ':direccion' => $_POST['direccion'] ?? '',
                    ':poblacion' => $_POST['poblacion'] ?? '',
                    ':codigo_postal' => $_POST['codigo_postal'] ?? '',
                    ':provincia' => $_POST['provincia'] ?? '',
                    ':estado' => $_POST['estado'] ?? $tarea['estado'],
                    ':fecha_realizacion' => $_POST['fecha_realizacion'] ?? $tarea['fecha_realizacion'],
                    ':anotaciones_antes' => $_POST['anotaciones_antes'] ?? '',
                    ':anotaciones_despues' => $_POST['anotaciones_despues'] ?? '',
                    ':fichero_resumen' => $resumen,
                    ':fotos' => json_encode($fotos)
                ];

                $this->model->update($_GET['id'], $data);
                header("Location: index.php?controller=Task&action=list&role=" . ($_GET['role'] ?? 'operador') . "&user=" . ($_GET['user'] ?? ''));
                return;
            }
        }

        $validationErrors = $errors;
        require "views/tasks/edit.php";
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?controller=Task&action=list&role=admin");
    }
}
