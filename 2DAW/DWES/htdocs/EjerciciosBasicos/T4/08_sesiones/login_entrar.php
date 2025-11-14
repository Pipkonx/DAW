<?php
include "sesion.php";

if ($_POST) :
    if ('pepe'==$_POST['user'] && '1234'==$_POST['passwd']) :
        // Suponemos que pepe tiene usuerio id=1
        Sesion::getInstance()->registraUsuario(1);
        ?>
        <h1>Area reservada</h1>
        <p>concedido acceso a <a href="login_dentro.php">login_dentro</a>

        <?php
        exit;
    endif; // de comprobar usuario
endif; // de _POST
?>
<h1>Introduzca credentiales</h1>
<form method="post">
    <p>Usuario: <input type="text" name="user">
    <p>Clave: <input type="password" name="passwd">
    <p><button type="submit">Acceder</button>
</form>