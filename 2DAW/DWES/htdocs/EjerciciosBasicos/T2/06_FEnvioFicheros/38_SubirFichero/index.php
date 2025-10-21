<!-- Realiza una página que nos permita subir un fichero a través de un formulario. Una vez subido generará una página que contendrá un enlace al fichero subido. El fichero lo Guía Didáctica Desarrollo Web en Entorno Servidor Revisión 27/09/22 guardaremos en la misma carpeta que está el script PHP. Véanse las constante predefinida _FILE_ y similares. -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir fichero</title>
</head>

<body>
    <!-- enctype es necesario para subir archivos -->
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="fichero"><br><br>
        <input type="submit" value="Subir">
    </form>

    <?php
    // $_FILES es para acceder a la informacion del archivo valido
    // Comprobamos files porque es un archivo lo que pasamos no un array
    if ($_FILES) {
        $nombreOriginal = $_FILES['fichero']['name'];
        // se le pone nombre temporal para que no se sobreescriba
        $nombreTemporal = $_FILES['fichero']['tmp_name'];

        // nombre unico por si se sube 2 veces el mismo archivo
        $nombreFinal = date('Y-m-d-H-i-s') . '-' . $nombreOriginal;

        // Ruta de destino en la misma carpeta que este script
        $rutaDestino = __DIR__ . '/' . $nombreFinal;
        
        // move_uploaded_file se pasa el parametro del nombre y la ruta
        if (move_uploaded_file($nombreTemporal, $rutaDestino)) {
            echo "<br>Fichero subido correctamente.<br><br>";
            echo '<a href="' . $nombreFinal . '">Ver fichero subido</a>';
        } else {
            echo "<br>Error al subir el fichero.<br>";
        }
    }
    ?>
</body>

</html>