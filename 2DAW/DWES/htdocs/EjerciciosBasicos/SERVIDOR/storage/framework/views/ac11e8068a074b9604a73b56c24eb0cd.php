<?php $__env->startSection('titulo', 'Login'); ?>

<?php $__env->startSection('cuerpo'); ?>
  <h1>Login</h1>
  <?php if(!empty($errorGeneral)): ?>
    <aside>
      <?php echo e($errorGeneral); ?>

    </aside>
  <?php endif; ?>
  <?php if(!empty($mensaje)): ?>
    <aside>
      <?php echo e($mensaje); ?>

    </aside>
  <?php endif; ?>

  <form action="<?php echo e(url('/login')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <p>
      <label>Usuario:</label>
      <input type="text" name="usuario" value="<?php echo e($nombre ?? ''); ?>">
    </p>

    <p>
      <label>Contraseña:</label>
      <input type="password" name="clave" value="<?php echo e($contraseña ?? ''); ?>">
    </p>
    
    <p>
      <label>
        <input type="checkbox" name="guardar_clave" <?php echo e(!empty($guardar_clave) ? 'checked' : ''); ?>> Guardar clave
      </label>
    </p>

    <p>
      <button type="submit">Entrar</button>
    </p>
  </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('plantillas.plantilla', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Pipkon\Desktop\DAW\2DAW\DWES\htdocs\EjerciciosBasicos\SERVIDOR\resources\views/autenticacion/login.blade.php ENDPATH**/ ?>