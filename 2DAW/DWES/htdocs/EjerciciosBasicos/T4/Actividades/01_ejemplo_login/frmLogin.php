<?php
  $usuariook = "pepe";
  $passok = "123";
  if(isset($_POST['submit']))
    { // Comprobamos que recibimos los datos y que no están vacíos
      if((isset($_POST['usuario'])&& isset($_POST['password'])) 
        && (!empty($_POST['usuario'])&& !empty($_POST['password']))){
         if ($_POST['usuario'] == $usuariook && $_POST['password'] == $passok) {
            session_start();
            $_SESSION['logueado']=$_POST['usuario'];
            $_SESSION['usuario']= $_POST['usuario'];
            //Creamos un par de cookies para recordar el user/pass. Tcaducidad=15días
            if(isset($_POST['recuerdo'])&&($_POST['recuerdo']=="on")) // Si está seleccioniado el checkbox...
             { // Creamos las cookies para ambas variables 
               setcookie ('usuario' ,$_POST['usuario'] ,time() + (15 * 24 * 60 * 60)); 
               setcookie ('password',$_POST['password'],time() + (15 * 24 * 60 * 60));
               setcookie ('recuerdo',$_POST['recuerdo'],time() + (15 * 24 * 60 * 60));
              } else {  //Si no está seleccionado el checkbox..
                // Eliminamos las cookies
                if(isset($_COOKIE['usuario'])) { 
                   setcookie ('usuario',""); } 
                if(isset($_COOKIE['password'])) { 
                   setcookie ('password',""); } 
                if(isset($_COOKIE['recuerdo'])) { 
                   setcookie ('recuerdo',""); }    
             }
             // Lógica asociada a mantener la sesión abierta 
             if(isset($_POST['abierta'])&&($_POST['abierta']=="on")) // Si está seleccionado el checkbox...
             { // Creamos una cookie para la sesión 
               setcookie ('abierta' ,$_POST['usuario'],time() + (15 * 24 * 60 * 60)); 
              } else {  //Si no está seleccionado el checkbox..
                // Eliminamos la cookie
                if(isset($_COOKIE['abierta'])) { 
                   setcookie ('abierta',""); } 
             }
          // Redirigimos a la página de inicio de nuestro sitio  
             header ("Location: frmInicio.php");
      }else{
       header ("Location: frmLogin.php?error=datos");
      }  
     }
    }   
?>
<html>
  <head lang="es">
    <?php require 'includes/header.php'; ?>
  </head>
  <body>
    <?php require 'includes/login.php'; ?>
    <?php require 'includes/footer.php'; ?>
  </body>
</html>