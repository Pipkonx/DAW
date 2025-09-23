<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ejemplo 1 PHP</title>
</head>

<body>
    <h1>PHP</h1>
    <hr>
    <?php
    print(" Ejercicio 1<br>");
    phpinfo();

    print(" Ejercicio 2<br>");
    $a = "a";
    $b = 1;
    $c = 5.2;
    var_dump($a);
    var_dump($b);
    var_dump($c);

    print("<br><br> Ejercicio 3<br>");
    print(gettype($a) . "<br>");
    print(gettype($b) . "<br>");
    print(gettype($c) . "<br>");

    print("<br><br> Ejercicio 4<br>");
    $d = 1;
    $e = "1";
    $mixmo =  1;
    print ("mixmo = $mixmo<br>") . var_dump($mixmo);
    $mixmo =  "a";
    print ("mixmo = $mixmo<br>") . var_dump($mixmo);
    $mixmo =  true;
    print ("mixmo = $mixmo<br>") . var_dump($mixmo);
    $mixmo =  null;
    print ("mixmo = $mixmo<br>") . var_dump($mixmo);


    print("<br><br> Ejercicio 5<br>");
    print("La suma de B = $b y D = $d es: ");
    print($b + $d);

    print("<br><br>La resta de B = $b y D = $d es: ");
    print($b - $d);

    print("<br><br>La multiplicacion de B = $b y D = $d es: ");
    print($b * $d);

    print("<br><br>La division de B = $b y D = $d es: ");
    print($b / $d);

    print("<br><br>El resto de la division anterior es: ");
    print($b % $d);

    $f = 50;
    $e = 20;
    print("<br><br>La division de E = $e y F = $f es: ");
    print($e / $f);

    print("<br><br>Operacion compuesta donde combino dos operaciones con parentesis por ejemplo e entre f esto en parentesis y por c: ");
    print(($e / $f) * $c);


    print("<br><br>Concatenamos dos cadenas<br>");
    print ("primera cadena ") . (" - Segunda cadena");

    print("<br><br>Mostramos operaciones en binario<br>");
    print ("Sumamos 0001 mas 1, que es igual a : ") . (0001 + $d);

    print("<br><br>Operaciones con operadores logicos y u o<br>");
    print("En este caso vamos a sumar uno y dos y luego lo vamos a multiplicar, dando de resultado = ");
    $uno = 1;
    $dos = 2;
    print($uno + $dos && $uno * $dos);
    print("<br><br>Los operadores lógicos funcionan igual que java");

    print("<br>Hola " . "mundo");

    print("<br><br>Cuando intentamos inicializar una variable que no ha sido declarada sudece lo siguiente : ");
    print($noInicializada);

    print("<br><br>Si tratamos de sumar una variable que no esta inicializada con una que si sucede lo siguiente: ");
    print($f + $noInicializada);

    $prueba1 = 5;
    $prueba2 = 6.0;
    $prueba4 = "7";
    $prueba3 = "9.0";
    $prueba5 = 2;
    $prueba6 = "hola";
    $prueba7 = "3lola";
    print("<br><br>Si tratamos de sumar dos variables que son distintos tipos de datos sucede lo siguiente<br> ");
    print ($prueba1 + $prueba2) . "<br>";
    print($prueba4 + $prueba3);
    print($prueba4 + $prueba7);
    print("<br>Si tratamos de sumar varios que son letras y numeros nos saltara un error");
    // print($prueba5 + $prueba6);


    print("<br>Ejercicio 10<br>");
    PRINT(error_reporting() . "<br>");
    print(E_ERROR . "<br>");
    print(E_WARNING  . "<br>");
    print(E_PARSE . "<br>");
    print(E_ALL . "<br>");
    print(E_NOTICE . "<br>");


    print("<br><br>Ejercicio 11");
    $v1 = 1;
    $v2 = "1";
    
    
    // Lo comprobamos primero en binario los numero dando igual lo de los tipos
    if ($v1 == $v2) {
        print("<br>Son iguales");
    } else {
        print("<br>no son iguales");
    }

    
    if ($v1 === $v2) {
        print("<br>Son iguales");
    } else {
        print("<br>No son iguales");
    }    
    








    ?>
</body>

<!-- <style>
    html {
        background-color: #f0f0f0;
    }

    body {
        background-color: white;
        border-radius: 20px;
        margin: 10%;
        padding: 10%;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        text-align: center;
        transition: transform 0.3s ease;
    }

    body:hover {
        transform: translateY(-1%);
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
</style> -->

</html>