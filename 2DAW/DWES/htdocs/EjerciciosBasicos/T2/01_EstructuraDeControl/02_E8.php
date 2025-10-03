<?php

print("<hr><h3>Ejercicio 8</h3><br><br>");
echo "Imprimimos multiplos de 5 durante 5 segundos del servidor<br>";

$startTime = time();
$endTime = $startTime + 5;

$count = 0;
$i = 0;

while (time() < $endTime) {
    $i++;
    if ($i % 5 == 0) {
        print($i . ",");
        $count++;
        if ($count % 10 == 0) {
            print("<hr>");
        }
    }
}