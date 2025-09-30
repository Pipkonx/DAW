<?php

print("<br><h3>Ejercicio 9</h3><hr><br><br>");
echo "Crea la función EsPrimo(numero) que devuelva un booleano que indique si el número pasado como parámetro es primo. Utilizando dicha función mostrar en una página los números primos menores de 100 que existen.<br>";

function EsPrimo(int $numero = 5) : bool {
    if ($numero < 2) return false;
    for ($i = 2; $i * $i <= $numero; $i++) {
        if ($numero % $i === 0) return false;
    }
    return true;
}

for ($n = 2; $n < 102; $n++) {
    if (EsPrimo($n)) echo $n . "<br>";
}
