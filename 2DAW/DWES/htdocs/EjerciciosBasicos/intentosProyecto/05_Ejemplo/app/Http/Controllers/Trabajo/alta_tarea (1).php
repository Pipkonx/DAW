<?php


$datos=[
    'nifCif'=>'',
    'personaNombre'=>'',
    /*
$nifCif = "";
$personaNombre = "";
$telefono = "";
$descripcionTarea = "";
$correo = "";
$direccionTarea = "";
$poblacion = "";
$codigoPostal = "";
$provincia = "";
$estadoTarea = "";
$operarioEncargado = "";
$fechaRealizacion = "";
$anotacionesAnteriores = "";
$anotacionesPosteriores = "";
*/
];


//Llamada al archivo de las funciones
require_once __DIR__ . '/../funciones.php';


if ($_POST) {
    $nifCif = trim($_POST['nifCif']);
    $personaNombre = trim($_POST['personaNombre']);
    $telefono = trim($_POST['telefono']);
    $descripcionTarea = trim($_POST['descripcionTarea']);
    $correo = trim($_POST['correo']);
    $direccionTarea = trim($_POST['direccionTarea']);
    $poblacion = trim($_POST['poblacion']);
    $codigoPostal = trim($_POST['codigoPostal']);
    $provincia = trim($_POST['provincia']);
    $estadoTarea = trim($_POST['estadoTarea']);
    $operarioEncargado = trim($_POST['operarioEncargado']);
    $fechaRealizacion = trim($_POST['fechaRealizacion']);
    $anotacionesAnteriores = trim($_POST['anotacionesAnteriores']);

    if ($nifCif == "") {
        $errores['nif_cif'] = "Debe introducir el NIF/CIF de la persona encargada de la tarea";
    } else {
        $resultado = validarNif($nifCif);
        if ($resultado !== true) {
            $errores[] = $resultado;
        }
    }

    if ($personaNombre === "") {
        $errores['nombre_persona'] = "Debe introducir el nombre de la persona encargada de la tarea";
    }

    if ($descripcionTarea === "") {
        $errores['descripcion_tarea'] = "Debe introducir la descripción de la tarea";
    }

    if ($correo === "") {
        $errores['correo'] = "Debe introducir el correo de la persona encargada de la tarea";
    } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $errores['correo'] = "El correo introducido no es válido";
    }

    if ($telefono == "") {
        $errores['telefono'] = "Debe introducir el teléfono de la persona encargada de la tarea";
    } else {
        $resultado = telefonoValido($telefono);
        if ($resultado !== true) {
            $errores['telefono'] = $resultado;
        }
    }

    if ($codigoPostal != "" && !preg_match("/^[0-9]{5}$/", $codigoPostal)) {
        $errores['codigo_postal'] = "El código postal introducido no es válido, debe tener 5 números";
    }

    if ($provincia === "") {
        $errores['provincia'] = "Debe introducir la provincia";
    }

    $fechaActual = date('Y-m-d');
    if ($fechaRealizacion == "") {
        $errores['fecha_realizacion'] = "Debe introducir la fecha de realización de la tarea";
    } else {
        if ($fechaRealizacion <= $fechaActual) {
            $errores['fecha_realizacion'] = "La fecha de realización debe ser posterior a la fecha actual";
        }
    }

    // Si hay errores, volvemos al formulario con los errores
    if (!empty($errores)) {
        include 'alta_tarea_form.php';
        exit;
    } else {
        // Si no hay errores, guardamos en la base de datos
        require_once 'conexion.php';

        $sql = "INSERT INTO tareas (
            nif_cif, persona_contacto, telefono, email, direccion, poblacion,
            codigo_postal, provincia, descripcion, anotaciones_anteriores,
            estado, operario_encargado, fecha_realizacion
        ) VALUES (
            '$nifCif', '$personaNombre', '$telefono', '$correo', '$direccionTarea', '$poblacion',
            '$codigoPostal', '$provincia', '$descripcionTarea', '$anotacionesAnteriores',
            '$estadoTarea', '$operarioEncargado', '$fechaRealizacion'
        )";

        if (mysqli_query($conexion, $sql)) {
            mysqli_close($conexion);
            include 'alta_tarea_procesado.php';
        } else {
            $errores[] = "Error al guardar en la base de datos: " . mysqli_error($conexion);
            mysqli_close($conexion);
            include 'alta_tarea_form.php';
        }
        exit;
    }
} else {
    include 'alta_tarea_form.php';
}
