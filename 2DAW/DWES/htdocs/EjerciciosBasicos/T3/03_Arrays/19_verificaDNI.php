<?php

//  Realizar una página que verifique si un dni/nif es correcto. (solo hace falta para los que tienen el formato NúmeroLetra). Formato NIF: http://es.wikipedia.org/wiki/N%C3%BAmero_de_identificaci%C3%B3n_fiscal

$nif = "12345678D";

function nif_valido($nif): bool {
    //strtoupper convierte el nif a mayusculas
	$nif = strtoupper($nif);

    // ^ indica que el nif debe comenzar con un digito
    // $ indica que el nif debe terminar con una letra
	$nifRegEx = '/^[0-9]{8}[A-Z]$/';
	$letras = "TRWAGMYFPDXBNJZSQVHLCKE";

    // el preg_match comprueba si el nif cumple con el formato
    // el substr devuelve los 8 primeros caracteres del nif
	if (preg_match($nifRegEx, $nif)) return ($letras[(substr($nif, 0, 8) % 23)] == $nif[8]);
	else  false;
    
}

if (nif_valido($nif)) {
    echo "El DNI $nif es válido.";
} else {
    echo "El DNI $nif no es válido.";
}
