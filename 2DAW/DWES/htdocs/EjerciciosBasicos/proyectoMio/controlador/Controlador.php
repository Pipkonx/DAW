<?php
require "modelo/Usuario.php";

function validarNIF($nif)
{
    // espacios y todo a mayuscula
    $nif = strtoupper(trim($nif));

    // comprobar formato bascio
    //el preg_match hace que realiza una busqueda que coincid con la expresion
    if (!preg_match('/^[0-9]{8}[A-Z]$/', $nif)) {
        return false;
    }

    // separa la letra
    $numero = substr($nif, 0, 8);
    $letra_introducida = substr($nif, 8, 1);

    // calcula la letra de control
    $letras_nif = 'TRWAGMYFPDXBNJZSQVHLCKE';
    $letra_calculada = $letras_nif[$numero % 23];

    if ($letra_introducida === $letra_calculada) {
        return true;
    } else {
        return false;
    }
}


class Controlador
{
    public function alta()
    {
        if (!$_POST) {
            include "vista/usuario_alta.php";
            return;
        }

        //! ver como hacer seguir
        // validacion del NIF
        validarNIF($_POST["nif"]);

        // Procesar alta en POST
        if (isset($_POST['nombre'])) {
            $nombre = $_POST['nombre'];
        } else {
            $nombre = '';
        }

        // FILTER_VALIDATE_EMAIL --> https://www.w3schools.com/php/filter_validate_email.asp
        // chekea que tiene el formato correcto para ser email 
        if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
            echo "El formato del email es válido";
        } else {
            echo "El formato del email es inválido";
        }


        if (!empty($nombre) && !empty($email) && !empty($cp)) {
            // $conn viene de DB/conexion.php incluido por modelo/Usuario.php
            // global $conn;
            // $usuario = new Usuario($conn);
            // $usuario->crear($nombre, $email);
            // // Volver al listado tras crear
            // $this->listar();
            echo "V datos son valido"
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
