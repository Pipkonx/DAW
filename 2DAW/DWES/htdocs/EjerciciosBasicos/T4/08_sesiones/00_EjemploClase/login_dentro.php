<?php
include "Sesion.php";

Sesion::getInstance()->obligaAQueEstaDentro();

?>
<h1>Accede a página reservada</h1>
<p>Estoy dentro</p>
<p><a href="login_salir.php">Cerrar sesion</a></p>