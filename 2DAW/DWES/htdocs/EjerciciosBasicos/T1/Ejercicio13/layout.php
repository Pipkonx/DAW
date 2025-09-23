<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tabla con datos de php</title>
</head>

<body>
    <table>
        <tr>
            <th><?php include "cuerpo.php"; print($nombre); ?></th>
            <th><?php print($apellido); ?></th>
        </tr>
    </table>
</body>
</html>