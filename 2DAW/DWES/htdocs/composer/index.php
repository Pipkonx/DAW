<?php
include('vendor/autoload.php');

$html = new \Html2Text\Html2Text('<h1>Hola Mundo!</h1><p>Otra linea</p>');
echo $html->getText();
