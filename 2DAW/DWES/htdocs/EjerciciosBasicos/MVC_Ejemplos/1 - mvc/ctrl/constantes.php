<?php
// definimos constantes que facilitan el trabajo

// Evitamos que se repita declaración con if (! defined(....
if (! defined('CTRL_PATH'))
{
    define('CTRL_PATH', __DIR__.'/');
    define('MODEL_PATH', __DIR__.'/../model/');
    define('VIEW_PATH', __DIR__.'/../view/');
    define('TEMPLATE_PATH', __DIR__.'/../plantilla/');
    define('LIB_PATH', __DIR__.'/../lib/');    
    define('HELPERS_PATH', __DIR__.'/../helpers/');    
}

