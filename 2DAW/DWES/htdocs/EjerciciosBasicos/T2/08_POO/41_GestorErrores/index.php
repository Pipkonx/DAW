<?php
/**
 * Anota un error para un campo en nuestro gestor de errores
 * @param string $campo
 * @param string $descripcion
 */
function AnotaError($campo, $descripcion) {}

/**
 * Indica si hay errores
 * @return boolean
 */
function HayErrores() {}

/**
 * Indica si hay error en un campo
 * @param string $campo
 * @return boolean
 */
function HayError($campo) {}

/**
 * Devuelve la descripción de error para un campo o una cadena vacía si no hay
 * @param string $campo
 * @return string
 */
function Error($campo) {}

/**
 * Devuelve la descripción del error o cadena vacía si no hay
 * @param string $campo
 * @return string
 */
function ErrorFormateado($campo) {}
