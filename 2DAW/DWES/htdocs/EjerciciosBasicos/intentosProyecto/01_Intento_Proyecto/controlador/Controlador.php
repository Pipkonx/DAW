<?php
require "modelo/Usuario.php";
require "modelo/Tarea.php";

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
            // hay que iniciarlo para que no muestre aviso en la vistas
            $datos = [];
            include "vista/usuario_alta.php";
            return;
        }

        // validacion del NIF
        $nif_valido = validarNIF($_POST["nif"]);

        // Procesar alta en POST
        if (isset($_POST['nombre'])) {
            $nombre = trim($_POST['nombre']);
        } else {
            $nombre = '';
        }

        if (isset($_POST['email'])) {
            $email = trim($_POST['email']);
        } else {
            $email = '';
        }

        if (isset($_POST['cp'])) {
            $cp = trim($_POST['cp']);
        } else {
            $cp = '';
        }

        // para que se mantengan los correctos
        $datos = [];
        // nombre requerido
        if (!empty($nombre)) {
            $datos['nombre'] = $nombre;
        }

        // Email con formato
        // FILTER_VALIDATE_EMAIL --> https://www.w3schools.com/php/filter_validate_email.asp
        // chekea que tiene el formato correcto para ser email
        // filter_var() es para validar que el email tenga el formato correcto
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $datos['email'] = $email;
            echo "✅ El formato del email es válido<br>";
        } else {
            echo "❌ El formato del email es inválido<br>";
        }

        // Validar NIF
        if ($nif_valido) {
            $datos['nif'] = $_POST["nif"];
            echo "✅ NIF válido<br>";
        } else {
            echo "❌ NIF inválido<br>";
        }

        // Validar campos obligatorios
        if (!empty($cp)) {
            $datos['cp'] = $cp;
        }

        // Además, conservar otros campos no validados si existen
        foreach (
            [
                'apellido',
                'telefono',
                'descripcion',
                'direccion',
                'poblacion',
                'provincia',
                'estado',
                'operario',
                'Frealizacion',
                'Aanteriores'
            ] as $campo
        ) {
            if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
                $datos[$campo] = $_POST[$campo];
            }
        }

        if (!empty($nombre) && !empty($email) && $nif_valido && !empty($cp)) {
            // $conn viene de DB/conexion.php incluido por modelo/Usuario.php
            global $conn;
            $usuario = new Usuario($conn);
            // para recoger los datos podemos usar el request tanto para post como para get
            $usuario->crear($nombre, $email, $_REQUEST["nif"], $cp);

            // // Volver al listado tras crear
            $this->listar();
            return;
        } else {
            echo "❌ Faltan campos por rellenar<br>";
            // Volver a mostrar el formulario conservando los datos correctos
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
            $nif = $_POST['nif'] ?? '';
            $cp = $_POST['cp'] ?? '';

            $usuario->actualizar($id, $nombre, $email, $nif, $cp);
            $this->listar();
            return;
        }

        include "vista/usuario_modificar.php";
    }

    public function mostrarTareas()
    {
        global $conn;
        $tarea = new Tarea($conn);
        $tareas = $tarea->listar();
        include "vista/tarea_lista.php";
    }

    public function modificarTarea($id)
    {
        global $conn;
        $tarea = new Tarea($conn);
        $datos = $tarea->obtenerId($id);

        if ($_POST) {
            $nombreTarea = $_POST['nombreTarea'] ?? '';
            $descripcion = $_POST['descripcion'] ?? '';
            $estado = $_POST['estado'] ?? '';
            $anotaciones_anteriores = $_POST['anotaciones_anteriores'] ?? '';
            $anotaciones_posteriores = $_POST['anotaciones_posteriores'] ?? '';
            $operario_encargado = $_POST['operario_encargado'] ?? '';

            $tarea->actualizar($id, $nombreTarea, $descripcion, $estado, $anotaciones_anteriores, $anotaciones_posteriores, $operario_encargado);
            $this->mostrarTareas();
            return;
        }

        include "vista/tarea_modificar.php";
    }

    public function borrarTarea($id)
    {
        global $conn;
        $tarea = new Tarea($conn);

        if ($_POST) {
            $tarea->eliminar($id);
            $this->mostrarTareas();
            return;
        }

        $datos = $tarea->obtenerId($id);
        include "vista/tarea_borrar.php";
    }
}
