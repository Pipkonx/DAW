    <!-- Modifica el programa anterior para que una vez subido el fichero muestre su contenido en el navegador. Véase la función readfile() -->

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Subir fichero</title>
    </head>

    <body>
        <!-- https://educacionadistancia.juntadeandalucia.es/centros/huelva/pluginfile.php/136786/mod_resource/content/9/UT3%20-Gu%C3%ADa%20Did%C3%A1ctica%20-%20Programaci%C3%B3n%20basada%20en%20lenguajes%20de%20marcas%20con%20c%C3%B3digo%20embebido.pdf -->

        <!-- enctype es para poder subir los archivos -->
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="fichero"><br><br>
            <input type="submit" value="subir">
        </form>

        <?php
        if ($_POST) {
            //! DUDA
            // comprobamos de que se sube y no tiene errores
            if ($_POST) {
                $nombreOriginal = $_FILES['fichero']['name'];
                // nombre unico por si se sube 2 veces el mismo archivo
                $nombre = date('d-m-y-H-i-s') . '-' . $nombreOriginal;
                // $rutaDestino = "2DAW/DWES/htdocs/EjerciciosBasicos/T2/06_FEnvioFicheros/38_SubirFichero";
                $rutaDestino = __FILE__ . "/" . $nombre;
                // ruta temporal donde PHP almacena el archivo subido
                $rutaTemporal = $_FILES['fichero']['tmp_name'];

                // Intentar mover el archivo de la carpeta temporal a la carpeta destino
                if (move_uploaded_file($rutaTemporal, $rutaDestino . "/" . $nombre)) {
                    echo "<br>Fichero subido correctamente<br><br>";
                    echo '<a href="' . $nombre . '">Ver fichero subido</a>';
                } else {
                    echo "<br>El fichero no se pudo crear en la ruta : <br>{$rutaDestino}<br><br>";
                    echo readline("pulsa enter para continuar");
                }
            } else {
                echo "<br>Error al subir el fichero.<br><br>";
            }
        }
        ?>
    </body>

    </html>