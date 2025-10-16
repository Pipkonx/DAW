<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form method="post">
        <center>
            <h3>Calculadora</h3>
        </center>
        <br>
        <label for="Operador1">Operador 1: </label>
        <input type="number" name="operador1" id="operador1">
        <br> <label for="Operador2">Operador 2: </label>
        <input type="number" name="operador2" id="operador2">
        <br> <label for="Operacion">Operacion: </label>
        <select name="operacion" id="operacion">
            <option value="suma">Suma</option>
            <option value="resta">Resta</option>
            <option value="multiplicacion">Multiplicacion</option>
            <option value="division">Division</option>
        </select>
        <br>
        <label for="resultado">Resultado: </label>
        <input type="text" name="resultado" id="resultado" value="<?php echo isset($resultado) ? $resultado : ''; ?>">
        <br>
        <input type="submit" value="Calcular">
    </form>

    <?php
    if ($_POST) {
        $op1 = $_POST["operador1"];
        $op2 = $_POST["operador2"];
        $operacion = $_POST["operacion"];
        switch ($operacion) {
            case 'suma':
                $resultado = $op1 + $op2; 
                break;
            case 'resta':
                $resultado = $op1 - $op2;
                break;
            case 'multiplicacion':
                $resultado = $op1 * $op2;
                break;
            case 'division':
                $resultado = $op1 / $op2;
                break;
        }
    }
    ?>
</body>

</html>