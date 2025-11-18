<?php
  session_start();   //Activamos el uso de sesiones
  if((!isset($_SESSION['logueado']))&& (!isset($_COOKIE['abierta'])))  // Si no existe la sesión…
    { //Redirigimos a la página de login con el tipo de error ‘fuera’: que indica que
      // se trató de acceder directamente a una página sin loguearse previamente
      header ("Location: frmLogin.php?error=fuera");  
    }
?>
<html>
  <head lang="es">
    <?php require 'includes/header.php'; ?>
  </head>
  <body>
    <?php require 'includes/pagina2.php'; ?>
    <?php require 'includes/footer.php'; ?>
  </body>
</html>