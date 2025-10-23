<?php

// Utilizando la función date() y time() escribe una página que muestre la fecha que será
// dentro de 50 segundos, y dentro de 2 horas, 4 minutos y 3 segundos.


function fechaHoraDentroDe50s() {
    return "La fecha y hora dentro de 50 segundos será: " . date("d-m-Y H:i:s", time() + 50);
}


function fechaHoraDentroDe2Horas4Minutos3Segundos() {
    return "La fecha y hora dentro de 2 horas, 4 minutos y 3 segundos será: " . date("d-m-Y H:i:s", time() + 2*60*60 + 4*60 + 3);
}

echo "Dentro de 50 segundos: " . fechaHoraDentroDe50s() . " y dentro de 2 horas, 4 minutos y 3 segundos: " . fechaHoraDentroDe2Horas4Minutos3Segundos();
