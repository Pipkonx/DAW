<?php

// Se desea almacenar información sobre coches, para cada coche se almacenaran las
// siguientes características (atributos):

// • matrícula
// • color
// • modelo
// • marca

// Realiza un array que almacene información de 5 o más coches.
// Crea la función MuestraCoche($coche), donde $coche será un array que contiene los atributos indicados anteriormente que 
// Realiza la función MuestraCoches($lista) que mostrará por pantalla información de los coches almacenados .
// Añade dos coches adicionales al array, después de mostrar, y vuelve a mostrar toda la lista.

// Nota: Se utilizará un array para almacenar la información de cada coche. Los indices,
// serán el nombre de los atributos que deseamos almacenar. Esto se puede hacer también
// utilizando objetos (clases).


$coche1 = [
    "matricula" => "123456",
    "color" => "rojo",
    "modelo" => "2020",
    "marca" => "Seat",
];
$coche2 = [
    "matricula" => "123456",
    "color" => "verde",
    "modelo" => "2025",
    "marca" => "Seat",
];
$coche3 = [
    "matricula" => "123456",
    "color" => "amarillo",
    "modelo" => "2022",
    "marca" => "Seat",
];
$coche4 = [
    "matricula" => "123456",
    "color" => "azul",
    "modelo" => "2023",
    "marca" => "Seat",
];
$coche5 = [
    "matricula" => "123456",
    "color" => "azul",
    "modelo" => "2023",
    "marca" => "Seat",
];

$lista = [
    $coche1,
    $coche2,
    $coche3,
    $coche4,
    $coche5,
];
function MuestraCoche(array $coche){
    echo "Matrícula: " . $coche["matricula"] . "<br>";
    echo "Color: " . $coche["color"] . "<br>";
    echo "Modelo: " . $coche["modelo"] . "<br>";
    echo "Marca: " . $coche["marca"] . "<br>";
}

function MuestraCoches(array $lista){
    foreach ($lista as $coche) {
        MuestraCoche($coche);
    }
}
MuestraCoche($coche1);
MuestraCoches($lista);
