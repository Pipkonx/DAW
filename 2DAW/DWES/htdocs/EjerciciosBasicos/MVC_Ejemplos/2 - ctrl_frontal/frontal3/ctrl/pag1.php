<?php
// En un controlador real esto haría más cosas
// En un controlador real esto haría más cosas

echo CargaVista('plantilla/layout', array(
    'titulo'=>'Página 1ª',
    'menu'=>CargaVista('plantilla/menu'),
    'cuerpo'=>CargaVista('pag1'),
));
