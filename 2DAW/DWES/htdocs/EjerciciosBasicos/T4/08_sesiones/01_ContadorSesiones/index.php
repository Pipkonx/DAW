<?php session_start();
//todo TEORIA https://desarrolloweb.com/articulos/235.php

if (!isset($_SESSION["cuenta_paginas"])) {
    $_SESSION["cuenta_paginas"] = 1;
} else {
    $_SESSION["cuenta_paginas"]++;
}
?>
<html>

<head>
    <title>Contar páginas vistas por un usuario en toda su sesión</title>
</head>

<body>
    <?php
    echo "Desde que entraste has visto " . $_SESSION["cuenta_paginas"] . " páginas";
    ?>
</body>

</html>