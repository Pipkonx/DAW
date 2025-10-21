<?php
// Se desea realizar un visor de fotos “cutre” que nos permita ver en el navegador fotos/imágenes que subiremos con un formulario. En la pantalla 1 pediremos el fichero que deseamos mostrar, y en la pantalla 2 mostraremos el fichero que hemos seleccionado

// Seleccionamos la foto
if (!isset($_POST['img'])):
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Visor de Fotos</title>
    </head>

    <body>
        <h1>Selecciona una imagen</h1>
        <form action="" method="post">
            <label for="img">Sube un fichero: </label>
            <input type="file" id="img" name="img" required>
            <button type="submit">Subir y ver</button>
        </form>
    </body>

    </html>
    <?php
// mostramos la foto
else:
    if (isset($_POST["img"])) :
        $img = $_POST['img'];
    ?>
        <!DOCTYPE html>
        <html lang="es">

        <head>
            <meta charset="UTF-8">
            <title>Visor de Fotos</title>
        </head>

        <body>
            <h1>Imagen seleccionada</h1>
            <img src="<?= $img ?>" alt="No cargo la foto correctamente" style="max-width:100%;">
            <br><br>
            <a href="?">Elegir otra opcion</a>
        </body>

        </html>
<?php
    endif;
endif;
