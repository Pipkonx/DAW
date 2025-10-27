<?php
require_once "models/Usuario.php";

class UsuarioController {

    public function alta() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);

            if (!empty($nombre) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $usuario = new Usuario();
                $usuario->crear($nombre, $email);
                header("Location: index.php");
            } else {
                echo "Datos no válidos.";
            }
        } else {
            include "views/usuario_alta.php";
        }
    }

    public function modificar($id) {
        $usuario = new Usuario();
        $datos = $usuario->obtenerPorId($id);

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre = trim($_POST['nombre']);
            $email = trim($_POST['email']);
            $usuario->actualizar($id, $nombre, $email);
            header("Location: index.php");
        } else {
            include "views/usuario_modificar.php";
        }
    }

    public function borrar($id) {
        $usuario = new Usuario();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $usuario->borrar($id);
            header("Location: index.php");
        } else {
            $datos = $usuario->obtenerPorId($id);
            include "views/usuario_borrar.php";
        }
    }

    public function listar() {
        $usuario = new Usuario();
        $datos = $usuario->listar();
        include "views/usuario_lista.php";
    }
}
