

<?php $__env->startSection('cuerpo'); ?>
    <div style="width:30em; margin:1em auto; padding:.5em; border:1px solid #999">
        <h1>Login</h1>

        <form method="post">
            <p>Usuario:
                <input name="user" type="text" value="<?php echo filter_input(INPUT_POST, 'user'); ?>">
            </p>
            <p>Clave:
                <input name="passwd" type="password">
            </p>
            <div><?php echo $ge->ErrorFormateado('user'); ?></div>
            <p><button type="submit">Iniciar session</button></p>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('_template', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\_Profesor - 2022-23\2DAW-DWES\UT\1Ev\6 - UT 4 - Desarrollo de aplicaciones Web utilizando código embebido\Ejemplos\tareas_slim_session-v1\view/login.blade.php ENDPATH**/ ?>