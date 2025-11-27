<?php
namespace App\Models;

/**
 * Conjunto de funciones de validación y utilidades para la capa de presentación.
 * Incluye validación de NIF/CIF, teléfonos y manejo de errores.
 */
class M_Funciones
{
    /**
     * Valida un NIF o CIF español.
     *
     * Este método verifica si una cadena dada es un NIF (Número de Identificación Fiscal)
     * o un CIF (Código de Identificación Fiscal) válido en España, incluyendo la validación
     * de la letra de control para NIFs y el dígito de control para CIFs.
     *
     * @param string $dni Cadena con el NIF/CIF a validar.
     * @return true|string Retorna `true` si el NIF/CIF es válido; de lo contrario, retorna un mensaje de error descriptivo.
     */
    public static function validarNif($dni)
    {
        $dni = strtoupper($dni); // Convertir a mayúsculas

        if (!preg_match('/^[A-Z0-9]{9}$/', $dni)) {
            return 'El NIF/CIF debe tener 9 caracteres';
        }

        $letrasNif = 'TRWAGMYFPDXBNJZSQVHLCKE';

        // Validación NIF
        if (preg_match('/^[0-9]{8}[A-Z]$/', $dni)) {
            $numero = substr($dni, 0, 8);
            $letra = substr($dni, -1);
            return $letra === $letrasNif[$numero % 23] ? true : "La letra del NIF no es correcta.";
        }

        // Validación CIF
        if (preg_match('/^[ABCDEFGHJNPQRSUVW][0-9]{7}[A-Z0-9]$/', $dni)) {
            $sumaPar = $sumaImpar = 0;
            for ($i = 0; $i <= 6; $i++) {
                $num = (int)$dni[$i];
                if ($i % 2 === 0) {
                    $doble = $num * 2;
                    $sumaImpar += $doble > 9 ? $doble - 9 : $doble;
                } else {
                    $sumaPar += $num;
                }
            }
            $control = (10 - (($sumaPar + $sumaImpar) % 10)) % 10;
            $controlEsperado = $dni[8];
            $letrasControl = "JABCDEFGHI";
            if (ctype_alpha($controlEsperado)) {
                return $controlEsperado === $letrasControl[$control] ? true : "La letra de control del CIF no es correcta.";
            } else {
                return (string)$control === $controlEsperado ? true : "El número de control del CIF no es correcto.";
            }
        }

        return "El NIF/CIF no es válido.";
    }

    /**
     * Valida un número de teléfono, permitiendo símbolos comunes.
     *
     * Verifica que el número de teléfono contenga solo dígitos, espacios, guiones, puntos y el signo '+'.
     * Además, comprueba que la longitud del número (solo dígitos) esté entre 7 y 15 caracteres.
     *
     * @param string $telefono El número de teléfono a validar.
     * @return true|string Retorna `true` si el teléfono es válido; de lo contrario, retorna un mensaje de error descriptivo.
     */
    public static function telefonoValido($telefono)
    {
        if (!preg_match("/^[+()0-9\s\-.]+$/", $telefono)) {
            return "El teléfono no es válido, solo se permiten números, espacios, guiones y +.";
        }

        $soloDigitos = preg_replace('/[^0-9]/', '', $telefono);
        $long = strlen($soloDigitos);

        if ($long < 7) return "El teléfono debe tener al menos 7 dígitos.";
        if ($long > 15) return "El teléfono no puede tener más de 15 dígitos.";

        return true;
    }

    /**
     * Mapa de código de provincia a nombre.
     *
     * @var array<string,string>
     */
    public static $provincias = [
        "01"=>"Araba/Álava","02"=>"Albacete","03"=>"Alicante/Alacant","04"=>"Almería","05"=>"Ávila","06"=>"Badajoz","07"=>"Balears, Illes","08"=>"Barcelona","09"=>"Burgos","10"=>"Cáceres","11"=>"Cádiz","12"=>"Castellón/Castelló","13"=>"Ciudad Real","14"=>"Córdoba","15"=>"Coruña, A","16"=>"Cuenca","17"=>"Girona","18"=>"Granada","19"=>"Guadalajara","20"=>"Gipuzkoa","21"=>"Huelva","22"=>"Huesca","23"=>"Jaén","24"=>"León","25"=>"Lleida","26"=>"Rioja, La","27"=>"Lugo","28"=>"Madrid","29"=>"Málaga","30"=>"Murcia","31"=>"Navarra","32"=>"Ourense","33"=>"Asturias","34"=>"Palencia","35"=>"Palmas, Las","36"=>"Pontevedra","37"=>"Salamanca","38"=>"Santa Cruz de Tenerife","39"=>"Cantabria","40"=>"Segovia","41"=>"Sevilla","42"=>"Soria","43"=>"Tarragona","44"=>"Teruel","45"=>"Toledo","46"=>"Valencia/València","47"=>"Valladolid","48"=>"Bizkaia","49"=>"Zamora","50"=>"Zaragoza","51"=>"Ceuta","52"=>"Melilla"
    ];

    /**
     * Contenedor de mensajes de error por campo.
     *
     * @var array<string,string>
     */
    public static $errores = [];

    /**
     * Devuelve el mensaje de error asociado a un campo específico.
     *
     * Busca en el array estático `$errores` si existe un mensaje de error
     * para el campo dado y lo retorna. Si no hay error para ese campo,
     * devuelve `null`.
     *
     * @param string $campo La clave del campo para el que se busca el error.
     * @return string|null El mensaje de error si existe, o `null` en caso contrario.
     */
    public static function getError($campo)
    {
        return self::$errores[$campo] ?? null;
    }
}
