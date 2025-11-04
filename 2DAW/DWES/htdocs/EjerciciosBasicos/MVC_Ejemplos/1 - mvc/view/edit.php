<html>
    <head>
        <title>Controlador frontal</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>    
<body>
<?php 
include(TEMPLATE_PATH.'encabezado.php');
include(TEMPLATE_PATH.'menu.php'); 
?>

<h1>Editanto Tarea</h1>
<div style="float:left">
<form method="post">
    <p>
        <label for="nombre">Tarea</label>
        <input name="nombre" value="<?=$tarea['nombre']?>"/> <?=$errores->ErrorFormateado('nombre')?>
    </p>
    <p>
        <label for="prioridad">Prioridad</label>
        <input name="prioridad" type="number" value="<?=$tarea['prioridad']?>"/> <?=$errores->ErrorFormateado('prioridad')?>
    </p>
    <p>
        <button type="submit">Enviar</button>
    </p>
</form>
</div>
<?php
include(TEMPLATE_PATH.'pie.php');
?>
</body>
</html>
