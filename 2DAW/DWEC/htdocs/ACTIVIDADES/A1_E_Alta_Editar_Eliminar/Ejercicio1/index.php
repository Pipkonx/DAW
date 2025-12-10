<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="actualizar.php" method="post">
        
        <div class="grupo-formulario">
            <label for="cp">Codigo postal:</label>
            <input type="text" id="cp" name="cp">
            <div id="suggestions-cp" class="caja-sugerencias"></div>
        </div>

        <div class="grupo-formulario">
            <label for="poblacion">Población:</label>
            <input type="text" id="poblacion" name="poblacion">
            <div id="suggestions-poblacion" class="caja-sugerencias"></div>
        </div>

        <div id="habitantes-section">
            <label for="habitantes">Habitantes:</label>
            <input type="number" id="habitantes" name="habitantes">
            <input type="submit" value="Actualizar">
        </div>
    </form>

    <script src="main.js"></script>
</body>

</html>