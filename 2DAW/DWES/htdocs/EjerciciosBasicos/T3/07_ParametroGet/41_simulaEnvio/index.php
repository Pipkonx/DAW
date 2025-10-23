<!-- 
Utilizando dos páginas simula el envío de información con un enlace. Envía los siguientes campos:
    ◦ nombre: Puede tener espacios y caracteres especiales para la URL, por lo que habrá que codificarlos con urlencode();
    ◦ edad: Será un número

    Nuestra página 1º mostrará una lista de nombres, cada uno de los cuales será un enlace
    Juan
    María
    Pedro

Al pulsar el enlace del nombre en la página 1, saltará a la página 2 mostrando los datos El enlace tendrá la forma pagina2.php?nombre=Juan%20Lopez%20Perez&edad=23 
-->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
<h1>Lista de nombres</h1>
<ul>
    <li><a href="pagina2.php?nombre=Juan%20Lopez%20Perez&edad=23">Juan Lopez</a></li>
    <li><a href="pagina2.php?nombre=Maria%20Martin%20Martin&edad=25">Maria Martin</a></li>
    <li><a href="pagina2.php?nombre=Pedro%20Medina%20Dominguez&edad=26">Pedro Medina</a></li>
</ul>
</body>

</html>