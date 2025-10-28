<?php
require "modelo/Usuario.php";

class Controlador
{
    public function alta()
    {
        // Mostrar formulario en GET
        if (!$_POST) {
            include "vista/usuario_alta.php";
            return;
        }

        // Procesar alta en POST
        if (isset($_POST['nombre'])) {
            $nombre = $_POST['nombre'];
        } else {
            $nombre = '';
        }

        if (isset($_POST['email'])) {
            $email = $_POST['email'];
        } else {
            $email = '';
        }

        if (!empty($nombre) && !empty($email)) {
            // $conn viene de DB/conexion.php incluido por modelo/Usuario.php
            global $conn;
            $usuario = new Usuario($conn);
            $usuario->crear($nombre, $email);
            // Volver al listado tras crear
            $this->listar();
        } else {
            echo "❌ Datos no válidos";
            include "vista/usuario_alta.php";
        }
    }

    public function borrar($id)
    {
        global $conn;
        $usuario = new Usuario($conn);

        // Confirmación y borrado
        if ($_POST) {
            $usuario->eliminar($id);
            $this->listar();
            return;
        }

        // Mostrar confirmación de borrado en GET
        $datos = $usuario->obtenerId($id);
        include "vista/usuario_borrar.php";
    }

    public function listar()
    {
        global $conn;
        $usuario = new Usuario($conn);
        $usuarios = $usuario->listar();
        include "vista/usuario_lista.php";
    }

    public function modificar($id)
    {
        global $conn;
        $usuario = new Usuario($conn);
        $datos = $usuario->obtenerId($id);

        if ($_POST) {
            // el ?? comprueba que el valor de la izquierda esta vacio
            // se podría hacer con operador ternario o con if normales
            $nombre = $_POST['nombre'] ?? '';
            $email = $_POST['email'] ?? '';
            $usuario->actualizar($id, $nombre, $email);
            $this->listar();
            return;
        }

        // Mostrar formulario de edición en GET
        include "vista/usuario_modificar.php";
    }
}
