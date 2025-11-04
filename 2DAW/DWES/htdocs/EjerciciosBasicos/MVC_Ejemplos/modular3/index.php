<?php
/*
 * Tercera aproximación de creación de una web modular.
 * La aplicación tiene un único punto de entrada. En función del parámetro 
 * GET "page" cargamos el cuerpo de la página.
 * Hay que repetir los includes en todas las páginas
 */
define('TEMPLATE_PATH', __DIR__ . "/plantilla/");
define('VISTAS', __DIR__ . '/vistas');
define('HELPERS', __DIR__ . "/helpers/");

include(HELPERS . 'vistas.php');

// Si no especifican página vamos a inicio
if (empty($_GET['page'])) {
    $page_cuerpo = VISTAS . "/partes/" . "inicio";
} else {
    $page_cuerpo = VISTAS . "/partes/" . $_GET['page'];
}
// Miramos si existe la página que queremos cargar
if (!file_exists($page_cuerpo . '.php')) {
    $page_cuerpo = VISTAS . "/error404";
}

// El menú y el encabezado podriamos cargarlo diferente para cada página
$menu_html = CargaVista(TEMPLATE_PATH . 'menu');
$encabezado_html = CargaVista(TEMPLATE_PATH . 'header');


// Generamos la partes que forman la página
$cuerpo_html = CargaVista($page_cuerpo);


// Construimos la página completa utilizando el layout
MuestraVista(TEMPLATE_PATH . 'layout', [
    'cuerpo_html' => $cuerpo_html,
    'menu_html' => $menu_html,
    'header_html' => $encabezado_html
]);
