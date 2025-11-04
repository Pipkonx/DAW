<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proyecto PHP</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>


    <h1>Proyecto PHP</h1>
    <nav>
        <a href="?pag=listar">Mostrar usuarios</a>
        <a href="?pag=alta">Alta usuario</a>
        <a href="?pag=tareas_listar">Mostrar tareas</a>
        <a href="?pag=tarea_alta">Alta tarea</a>
    </nav>
    <hr>

    <?php
    require 'controlador/Controlador.php';
    $controller = new Controlador();

    $pag = isset($_GET['pag']) ? $_GET['pag'] : 'listar';
    // Capturar id desde GET o POST para que las operaciones funcionen aunque el formulario no preserve la query
    $id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);

    switch ($pag) {
        case 'alta':
            $controller->alta();
            break;
        case 'modificar':
            if ($id === null) {
                echo 'Falta el parámetro id para modificar.';
            } else {
                $controller->modificar($id);
            }
            break;
        case 'borrar':
            if ($id === null) {
                echo 'Falta el parámetro id para borrar.';
            } else {
                $controller->borrar($id);
            }
            break;
        case 'tareas_listar':
            $controller->mostrarTareas();
            break;
        case 'tareas_modificar':
            if ($id === null) {
                echo 'Falta el parámetro id para modificar la tarea.';
            } else {
                $controller->modificarTarea($id);
            }
            break;
        case 'tareas_borrar':
            if ($id === null) {
                echo 'Falta el parámetro id para borrar la tarea.';
            } else {
                $controller->borrarTarea($id);
            }
            break;
        case 'tarea_alta':
            if ($id === null) {
                echo 'Falta el parámetro id para alta la tarea.';
            } else {
                $controller->altaTarea($id);
            }
            break;
        default:
            $controller->listar();
            break;
    }
    ?>
</body>

</html>