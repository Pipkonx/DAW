    <!-- Modifica el programa anterior para que una vez subido el fichero muestre su contenido en el navegador. Véase la función readfile() -->

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
                
                // readfile() se especifica la ruta del archivo como parámetro principal
                $fichero = readfile($rutaDestino);
                if ($fichero) {
                    echo "error al leer el archivo";
                } else {
                    echo "Se leyeron " . $fichero . " bytes.";
                }
            } else {
                echo "<br>Error al subir el fichero.<br>";
            }
        }
        ?>
    </body>

    </html>