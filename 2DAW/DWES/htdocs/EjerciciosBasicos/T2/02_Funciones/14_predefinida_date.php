<?php

// Utilizando la función predefinida date(), realiza una página en la que se muestre la fecha y hora actual


function fechaHora() {
    return "La fecha y hora actual es :" . date("d-m-Y H:i:s");
}

echo fechaHora();