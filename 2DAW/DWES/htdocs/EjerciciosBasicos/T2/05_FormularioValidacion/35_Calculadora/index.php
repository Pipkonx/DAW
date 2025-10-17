<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
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
    <form method="post">
        <center>
            <h3>Calculadora</h3>
        </center>
        <br>
        <label for="Operador1">Operador 1: </label>
        <input type="number" name="operador1" id="operador1" value="<?= $op1?>">
        <br> <label for="Operador2">Operador 2: </label>
        <input type="number" name="operador2" id="operador2" value="<?= $op2?>">
        <br> <label for="Operacion">Operacion: </label>
        <select name="operacion" id="operacion">
            <option value="suma" onclick="this.form.submit()">Suma</option>
            <option value="resta" onclick="this.form.submit()">Resta</option>
            <option value="multiplicacion" onclick="this.form.submit()">Multiplicacion</option>
            <option value="division" onclick="this.form.submit()">Division</option>
        </select>
        <br>
        <label for="resultado">Resultado: <?= isset($resultado) ? $resultado : "" ?></label>
        <input type="hidden" name="resultado" value="<?= $resultado ?>">
    </form>
</body>

</html>