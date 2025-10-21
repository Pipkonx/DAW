<?php
/**
* Anota un error para un campo en nuestro gestor de errores
* @param type $campo
* @param type $descripcion
*/
AnotaError($campo, $descripcion);
/**
* Indica si hay errores
* @return boolean
*/
HayErrores() ;
/**
* Indica si hay error en un campo
* @return boolean
*/
HayError($campo);
/**
* Devuelve la descripción de error para un campo o una cadaena vacia si no hay
* @param type $campo
* @return string
*/
Error($campo);
/**
* Devuelve la descripción del error o cadena vacia si no hay
* @param type $campo
* @return string
*/

public function ErrorFormateado($campo) {}