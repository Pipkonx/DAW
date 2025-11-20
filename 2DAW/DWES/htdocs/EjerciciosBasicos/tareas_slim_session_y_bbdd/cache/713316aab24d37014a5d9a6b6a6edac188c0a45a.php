<?php
/**
 * VISTA QUE MUESTA LA LISTA DE TAREAS.
 * El controlador será el que nos proporcine en la variable $tareas
 * que contiene las tareas a mostrar
 */
?>


<?php $__env->startSection('cuerpo'); ?>
<h1>Listado de tareas</h1>
<table>
    <tr>
        <td>Id</td>
        <td>Nombre</td>
        <td>Prioridad</td>
        <td></td>
    </tr>
    <?php $__currentLoopData = $tareas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tarea): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td><?php echo e($tarea['id']); ?></td>
        <td><?php echo e($tarea['nombre']); ?></td>
        <td><?php echo e($tarea['prioridad']); ?></td>
        <td>
            <a href="<?=BASE_URL?>edit?id=<?php echo e($tarea['id']); ?>">[Modificar]</a>
            <a href="<?=BASE_URL?>del?id=<?php echo e($tarea['id']); ?>">[Borrar]</a>
        </td>
    </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</table>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\_Profesor - 2022-23\2DAW-DWES\UT\1Ev\6 - UT 4 - Desarrollo de aplicaciones Web utilizando código embebido\Ejemplos\tareas_slim_session-v1\view/listar.blade.php ENDPATH**/ ?>