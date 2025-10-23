<?php
// Usando la clase DateTime y sus clases asociadas realiza un script que muestre:
// ◦ Cuantas horas, minutos y segundos quedan para que sean las 11 de la noche.
// ◦ Cuantos minutos y segundos quedan para cambiar de hora
// ◦ Qué fecha será dentro de 5 días
// ◦ Qué fecha fue hace 5 días
// ◦ Calcula la edad que tiene una persona a partir de su fecha de nacimiento
// Puedes utilizar la página https://diego.com.es/fecha-y-hora-en-php para ver como se
// manejan los objetos. Aunque también debes acostumbrarte a entender la documentación
// https://www.php.net/manual/es/book.datetime.php
// Establecer zona horaria

date_default_timezone_set('Europe/Madrid');

$ahora = new DateTime();
$medianoche = new DateTime('today 23:00:00');
$cambioHora = new DateTime('today ' . date('H') . ':59:59');
$fechaFutura = new DateTime();
$fechaPasada = new DateTime();
$fechaNacimiento = new DateTime('2001-09-10');

// el -> es para acceder a los metodos y propiedades de la clase
// el diff es para calcular la diferencia
$diffMedianoche = $ahora->diff($medianoche);
$diffCambioHora = $ahora->diff($cambioHora);

// el modifiy es para modificar la fecha
$fechaFutura->modify('+5 days');
$fechaPasada->modify('-5 days');

// el ->y es para acceder a la propiedad que es la edad
$edad = $fechaNacimiento->diff(new DateTime())->y;

echo "Quedan {$diffMedianoche->h} horas, {$diffMedianoche->i} minutos y {$diffMedianoche->s} segundos para las 23:00.<br>";
echo "Quedan {$diffCambioHora->i} minutos y {$diffCambioHora->s} segundos para cambiar de hora.<br>";
echo "Dentro de 5 días será: " . $fechaFutura->format('d-m-Y') . "<br>";
echo "Hace 5 días fue: " . $fechaPasada->format('d-m-Y') . "<br>";
echo "La edad es: $edad años.<br>";
