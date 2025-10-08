<?php

/**
 * Devuelve el valor de un campo enviado por POST. Si no existe devuelve el valor por defecto
 * @param string $nombreCampo
 * @param mixed $valorPorDefecto
 * @return string
 */
function ValorPost($nombreCampo, $valorPorDefecto='')
{
	if (isset($_POST[$nombreCampo]))
		return $_POST[$nombreCampo];
	else
		return $valorPorDefecto;
}
