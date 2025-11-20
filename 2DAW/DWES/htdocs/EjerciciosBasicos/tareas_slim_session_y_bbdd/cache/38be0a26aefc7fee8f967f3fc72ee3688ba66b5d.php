

<?php $__env->startSection('cuerpo'); ?>

<h1><?php echo e($operacion); ?></h1>
<div style="float:left">
<form method="post">
    <p>
        <label for="nombre">Tarea</label>
        <input name="nombre" value="<?php echo e($tarea['nombre']); ?>"/> 
        <?=$errores->ErrorFormateado('nombre')?>
    </p>
    <p>
        <label for="prioridad">Prioridad</label>
        <input name="prioridad" type="number" value="<?php echo e($tarea['prioridad']); ?>"/> 
        <?=$errores->ErrorFormateado('prioridad')?>
    </p>
    <p>
        <button type="submit">Enviar</button>
    </p>
</form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\_Profesor - 2022-23\2DAW-DWES\UT\1Ev\6 - UT 4 - Desarrollo de aplicaciones Web utilizando código embebido\Ejemplos\tareas_slim_session-v1\view/edit.blade.php ENDPATH**/ ?>