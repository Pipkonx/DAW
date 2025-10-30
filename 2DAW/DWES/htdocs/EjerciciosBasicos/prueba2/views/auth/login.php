<?php require "views/templates/header.php"; ?>
<h2 class="mb-4">Iniciar sesión</h2>
<form method="post" action="index.php?controller=Auth&action=login">
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Contraseña</label>
    <input type="password" name="password" class="form-control" required>
  </div>
  <button type="submit" class="btn btn-primary">Entrar</button>
</form>
<?php require "views/templates/footer.php"; ?>
