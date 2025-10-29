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
        <a href="?action=listar">Mostrar usuarios</a>
        <a href="?action=alta">Alta usuario</a>
    </nav>
    <hr>

    <?php
    require 'controlador/Controlador.php';
    $controller = new Controlador();

    $action = isset($_GET['action']) ? $_GET['action'] : 'listar';
    // Capturar id desde GET o POST para que las operaciones funcionen aunque el formulario no preserve la query
    $id = isset($_GET['id']) ? (int) $_GET['id'] : (isset($_POST['id']) ? (int) $_POST['id'] : null);

    switch ($action) {
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

        default:
            $controller->listar();
            break;
    }
    ?>
</body>

</html>