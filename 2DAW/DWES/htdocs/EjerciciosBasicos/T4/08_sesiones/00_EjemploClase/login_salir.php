<?php
include "Sesion.php";
Sesion::getInstance()->salir();
header('Location: login_entrar.php');