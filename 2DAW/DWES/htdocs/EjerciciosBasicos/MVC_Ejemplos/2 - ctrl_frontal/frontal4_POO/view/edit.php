<h1><?=$operacion?></h1>
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

