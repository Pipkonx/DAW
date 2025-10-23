<?php
// Se desea realizar un visor de fotos “cutre” que nos permita ver en el navegador fotos/imágenes que subiremos con un formulario. En la pantalla 1 pediremos el fichero que deseamos mostrar, y en la pantalla 2 mostraremos el fichero que hemos seleccionado

// primero argamos la imagen
if (!isset($_FILES['img'])) {
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Visor de Fotos</title>
    </head>

    <body>
        <h1>Selecciona una imagen</h1>
        <form action="" method="post" enctype="multipart/form-data">
            <label for="img">Sube un fichero: </label>
            <input type="file" id="img" name="img" required>
            <button type="submit">Subir y ver</button>
        </form>
    </body>

    </html>
    <?php
} else {
    if (isset($_FILES['img'])) {

        $nombre_archivo = $_FILES['img']['name'];
        $ruta_temporal = $_FILES['img']['tmp_name'];
        $directorio = __DIR__;

        // basename() devuelve el nombre del archivo o componente final de una ruta de archivo
        if (move_uploaded_file($ruta_temporal, $directorio . '/' . basename($nombre_archivo))) {
            echo "El archivo se ha subido correctamente";
        } else {
            echo "Hubo un error al subir el archivo";
        }
    ?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <title>Visor de Fotos</title>
        </head>

        <body>
            <h1>Imagen seleccionada</h1>
            <img src="<?= $nombre_archivo; ?>" alt="Imagen subida" style="width: auto; height: 25vh">
            <br><br>
            <a href="?">Elegir otra opcion</a>
        </body>

        </html>
<?php
    }
}
?>