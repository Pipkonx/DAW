<?php

// // Creamos la conexion
$con = new mysqli("localhost", "root", "", "BaseDeDatos");

// //chekeamos la conexion
if ($con->connect_error) {
    die("Conexion fallida: " . $con->connect_error);
}

// // Creamos una tabla en la base de datos
// $sql = "Create table prueba (
// id INT(5) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
// firstname VARCHAR(30) NOT NULL,
// lastname VARCHAR(30) NOT NULL,
// emiall VARCHAR(50),
// reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)";

// if ($con->query($sql) === TRUE) {
//     echo "Table prueba fue creada exitosamente";
// } else {
//     echo "Error al crear la tabla , error : " . $con->error;
// }

$sql = "Select * from prueba";

$sql = "insert into prueba (firstname, lastname, emiall) values ('pepe', 'perez', 'pepe@pepe.com')";

$sql = "select * from prueba";

$result = $con->query($sql);

if($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
            echo "id: " . $row["id"] . " Name : " . $row['firstname'] . ' ' . $row['lastname'] . "<br>";
    }
} else {
    echo "0 results";
}



// if (mysqli_query($con, $sql)) {
//     echo "Nueva entrada creada";
// } else {
//     echo "Error: " . $sql . "<br>" . mysqli_error($conn);
// }

$con->close();
