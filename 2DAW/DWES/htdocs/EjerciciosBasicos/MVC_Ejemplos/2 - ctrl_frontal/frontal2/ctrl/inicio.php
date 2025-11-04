<?php
// En un controlador real esto haría más cosas

echo CargaVista('plantilla/layout', array(
    'titulo'=>'Página de inicio',
    'menu'=>CargaVista('plantilla/menu'),
    'cuerpo'=>CargaVista('inicio'),
));

