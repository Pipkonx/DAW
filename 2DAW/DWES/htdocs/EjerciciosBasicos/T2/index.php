<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <h1>Tema 2</h1>
    <hr>
    <h3>Ejercicio 1</h3><br>
    <p>Tabla de multiplicar</p>

    <?php
    for ($i = 0; $i < 11; $i++) {
        print($i . " x " . 5 . " = " . 5 * $i . "<br>");
    }


    print("<hr><h3>Ejercicio 2</h3><br><p>Tabla de multiplicar del 1 al 10 con for anidados</p>");
    for ($a = 0; $a < 11; $a++) {
        print("<br><br>");
        for ($i = 0; $i < 11; $i++) {
            print($i . " x " . $a . " = " . $a * $i . "<br>");
        }
    }

    print("<hr><h3>Ejercicio 3</h3><br><p>Tabla de numero aleatorio con rand()</p>");
    $a = rand(1, 10);
    for ($i = 0; $i < 11; $i++) {
        print($i . " x " . $a . " = " . $a * $i . "<br>");
    }

    print("<hr><h3>Ejercicio 4</h3><br><p>Sacamos un numero aleatorio con rand y luego decimos que numero es con un switch</p><br>");
    $a = rand(1, 10);
    switch ($a) {
        case 1:
            print("El numero aleatorio es uno");
            break;
        case 2:
            print("El numero aleatorio es dos");
            break;
        case 3:
            print("El numero aleatorio es tres");
            break;
        case 4:
            print("El numero aleatorio es cuatro");
            break;
        case 5:
            print("El numero aleatorio es cinco");
            break;
        case 6:
            print("El numero aleatorio es seis");
            break;
        case 7:
            print("El numero aleatorio es siete");
            break;
        case 8:
            print("El numero aleatorio es ocho");
            break;
        case 9:
            print("El numero aleatorio es nueve");
            break;
        case 10:
            print("El numero aleatorio es diez");
            break;
    }
    print("<hr><h3>Ejercicio 5</h3><br><p>Mostramos todos los numeros entre el 1 y el 1000 que son multiplos de 3, 4</p><br>");
    for ($i = 0; $i < 1001; $i++) {
        if ($i % 3 == 0 && $i % 4 == 0) {
            print($i . " <br>");
        }
    }

    print("<hr><h3>Ejercicio 6</h3><br><p>Mostramos todos los numeros que son multiplos de 3, 4 y 7</p><br>");
    for ($i = 0; $i < 1001; $i++) {
        if ($i % 3 == 0 && $i % 4 == 0 && $i % 7 == 0) {
            print($i . " <br>");
        }
    }

    print("<hr><h3>Ejercicio 7</h3><br><p></p><br>");
    do {
        $i = rand(0, 1000);
        print($i . "<br>");
    } while ($i == 15);
    ?>

</body>

</html>