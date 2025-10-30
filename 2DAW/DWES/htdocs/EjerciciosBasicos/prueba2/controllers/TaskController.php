<?php
require_once "models/Task.php";

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

        require "views/tasks/list.php";
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                ':nif_cif' => $_POST['nif_cif'],
                ':persona_contacto' => $_POST['persona_contacto'],
                ':telefono' => $_POST['telefono'],
                ':descripcion' => $_POST['descripcion'],
                ':correo' => $_POST['correo'],
                ':direccion' => $_POST['direccion'],
                ':poblacion' => $_POST['poblacion'],
                ':codigo_postal' => $_POST['codigo_postal'],
                ':provincia' => $_POST['provincia'],
                ':estado' => $_POST['estado'],
                ':operario' => $_POST['operario'],
                ':fecha_realizacion' => $_POST['fecha_realizacion'],
                ':anotaciones_antes' => $_POST['anotaciones_antes']
            ];
            $this->model->create($data);
            header("Location: index.php?controller=Task&action=list&role=admin");
        }
        require "views/tasks/create.php";
    }

    public function edit() {
        $tarea = $this->model->getById($_GET['id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resumen = $tarea['fichero_resumen'];
            $fotos = $tarea['fotos'] ? json_decode($tarea['fotos'], true) : [];

            if (!empty($_FILES['resumen']['name'])) {
                $nombreArchivo = time() . "_" . basename($_FILES['resumen']['name']);
                move_uploaded_file($_FILES['resumen']['tmp_name'], "uploads/resumenes/" . $nombreArchivo);
                $resumen = $nombreArchivo;
            }

            if (!empty($_FILES['fotos']['name'][0])) {
                foreach ($_FILES['fotos']['tmp_name'] as $i => $tmp) {
                    $nombreFoto = time() . "_" . basename($_FILES['fotos']['name'][$i]);
                    move_uploaded_file($tmp, "uploads/fotos/" . $nombreFoto);
                    $fotos[] = $nombreFoto;
                }
            }

            $data = [
                ':persona_contacto' => $_POST['persona_contacto'],
                ':telefono' => $_POST['telefono'],
                ':descripcion' => $_POST['descripcion'],
                ':correo' => $_POST['correo'],
                ':direccion' => $_POST['direccion'],
                ':poblacion' => $_POST['poblacion'],
                ':codigo_postal' => $_POST['codigo_postal'],
                ':provincia' => $_POST['provincia'],
                ':estado' => $_POST['estado'],
                ':fecha_realizacion' => $_POST['fecha_realizacion'],
                ':anotaciones_antes' => $_POST['anotaciones_antes'],
                ':anotaciones_despues' => $_POST['anotaciones_despues'],
                ':fichero_resumen' => $resumen,
                ':fotos' => json_encode($fotos)
            ];

            $this->model->update($_GET['id'], $data);
            header("Location: index.php?controller=Task&action=list&role=" . $_GET['role'] . "&user=" . $_GET['user']);
        }

        require "views/tasks/edit.php";
    }

    public function delete() {
        $this->model->delete($_GET['id']);
        header("Location: index.php?controller=Task&action=list&role=admin");
    }
}
