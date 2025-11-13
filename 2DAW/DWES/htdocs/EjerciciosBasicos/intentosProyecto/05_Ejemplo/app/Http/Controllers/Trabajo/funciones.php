<?php function validarNif($dni)
{
    $dni = strtoupper($dni); // Convertir a mayúsculas

    // Comprueba que el NIF tenga 9 caracteres (8 numeros y 1 letra) 

    if (!preg_match('/^[A-Z0-9]{9}$/', $dni)) {
        return 'El NIF/CIF debe tener 9 caracteres';
    }

    // Comprueba que los primeros 8 caracteres del NIF sean números y el ultimo 1 letra mayúscula
    $letrasNif = 'TRWAGMYFPDXBNJZSQVHLCKE';

    if (preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
        $numero = substr($dni, 0, 8);
        $letra = substr($dni, -1);
        if ($letra !== $letrasNif[$numero % 23]) {
            return "La letra del NIF no es correcta.";
        }
        return true;
    }

    // Comprueba que el CIF es válido (letra + 7 números + letra/número de control)
    if (preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[A-Z0-9]$/', $dni)) {
        $sumaPar = 0;
        $sumaImpar = 0;

        // Trabajamos con el grupo de 7 números
        // Sumamos los números que ocupan las posiciones pares
        for ($i = 1; $i <= 6; $i += 2) {
            $sumaPar += (int) $dni[$i];
        }

        // Multiplicmos por 2 los números que ocupan las posiciones impares
        for ($i = 0; $i <= 6; $i += 2) {
            $doble = (int) $dni[$i] * 2;
            //Si el resultado es > 9, se resta 9 (equivale a sumar los dígitos)
            $sumaImpar += $doble > 9 ? $doble - 9 : $doble;
        }

        // Sumamos los 2 resultados anteriores
        $sumaTotal = $sumaPar + $sumaImpar;

        //Obtenemos el último número de la suma con el resto de la división entre 10, 
        // y le restamos 10 para saber cuanto le queda para llegar a la próxima decena
        // Por último con %10 nos aseguramos que el resultado sea un número entre 0 y 9
        $control = (10 - ($sumaTotal % 10)) % 10;

        $letrasControl = "JABCDEFGHI"; // Las letras del control

        //Obtenemos el control esperado que se encuentra en la posición 8 del CIF
        $controlEsperado = $dni[8];

        // Con ctype_alpha averiguamos si el control esperado es una letra (true) o un número (false)
        if (ctype_alpha($controlEsperado)) {
            // Si es una letra, comparamos con la letra correspondiente en la posición de letrasControl
            if ($controlEsperado === $letrasControl[$control]) {
                return true;
            } else {
                return "La letra de control del CIF no es correcto.";
            }
        } else {
            //Si es un número, pasamos el número a string para compararlo con el control esperado que es string
            if ((string)$control === $controlEsperado) {
                return true;
            } else {
                return "El número de control del CIF no es correcto.";
            }
        }
    }
    // Si no es NIF ni CIF válido, devolver false
    return "El NIF/CIF no es válido.";
}


function telefonoValido($telefono)
{
    // Permitir solo: +, (), números, espacios, guiones y puntos
    if (!preg_match("/^[+()0-9\s\-.]+$/", $telefono)) {
        return "El teléfono no es válido, solo se pemiten números, espacios, guiones y +.";
    }

    // Extraer solo los dígitos y comprobar que tengan al menos 7 y no más de 15 dígitos
    $soloDigitos = preg_replace('/[^0-9]/', '', $telefono);
    $long = strlen($soloDigitos);
    if ($long < 7) {
        return "El teléfono debe tener al menos 7 dígitos.";
    }
    if ($long > 15) {
        return "El teléfono no puede tener más de 15 dígitos.";
    }
    return true;
}

$provincias = [
    "01" => "Araba/Álava", "02" => "Albacete", "03" => "Alicante/Alacant",
    "04" => "Almería", "05" => "Ávila", "06" => "Badajoz",
    "07" => "Balears, Illes", "08" => "Barcelona", "09" => "Burgos",
    "10" => "Cáceres", "11" => "Cádiz", "12" => "Castellón/Castelló",
    "13" => "Ciudad Real", "14" => "Córdoba", "15" => "Coruña, A",
    "16" => "Cuenca", "17" => "Girona", "18" => "Granada",
    "19" => "Guadalajara", "20" => "Gipuzkoa", "21" => "Huelva",
    "22" => "Huesca", "23" => "Jaén", "24" => "León",
    "25" => "Lleida", "26" => "Rioja, La", "27" => "Lugo",
    "28" => "Madrid", "29" => "Málaga", "30" => "Murcia",
    "31" => "Navarra", "32" => "Ourense", "33" => "Asturias",
    "34" => "Palencia", "35" => "Palmas, Las", "36" => "Pontevedra",
    "37" => "Salamanca", "38" => "Santa Cruz de Tenerife", "39" => "Cantabria",
    "40" => "Segovia", "41" => "Sevilla", "42" => "Soria",
    "43" => "Tarragona", "44" => "Teruel", "45" => "Toledo",
    "46" => "Valencia/València", "47" => "Valladolid", "48" => "Bizkaia",
    "49" => "Zamora", "50" => "Zaragoza", "51" => "Ceuta",
    "52" => "Melilla",
];

function mostrarProvincias($provinciaSeleccionada = "") {
    global $provincias;
    foreach ($provincias as $codigo => $nombre) {
        $selected = ($codigo == $provinciaSeleccionada) ? 'selected' : '';
        echo "<option value=\"$codigo\" $selected>" . htmlspecialchars($nombre) . "</option>";
    }
}

function verErrores($errores , $campo) {
    if (isset($errores[$campo])) {
        echo "<div class=error> ". htmlspecialchars($errores[$campo]) . "</div>";
    } else {
        return "";
    }
    
}
