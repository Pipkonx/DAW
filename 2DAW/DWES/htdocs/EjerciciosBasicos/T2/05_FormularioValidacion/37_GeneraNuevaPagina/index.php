<?php
// Amplia el ejercicio anterior de forma que en función de las selecciones genere una nueva
// página en la que se solicite más información.
// ◦ Si en afición hay “deporte” nos pregunte también el tipo de deporte que nos gusta a
// elegir entre las siguientes opciones: (ciclismo, tenis, futbol, baloncesto, voleibol, etc).
// ◦ Si en estudios hay ESO, preguntar el año que obtuvo el título.
// ◦ Si el destino es Mediterráneo, seleccionar alguna o varias de las siguientes opciones:
// Cataluña, Valencia, Andalucía, Tunez, Turquía, Italia, Francia,...
// Una vez seleccionadas las opciones se mostrará en la página resultado todas las respuestas seleccionadas.
// Nota: Puede que necesitéis utilizar campos ocultos para transmitir la información entre los formularios.



//mas complicado de entender al principio

// con los defines definimos constantes
define('UNA_OPCION', 'radio');
define('MULTI_OPCION', 'checkbox');

// esto es un array con las preguntas y respuestas array bidimensional
$preguntas = [
    [
        'texto_pregunta' => 'Sexo',
        'tipo' => UNA_OPCION,
        'campo' => 'sexo',
        'respuestas' => [
            ['etiqueta' => 'Hombre', 'valor' => 'H'],
            ['etiqueta' => 'Mujer', 'valor' => 'M']
        ]
    ],
    [
        'texto_pregunta' => 'Aficiones',
        'tipo' => MULTI_OPCION,
        'campo' => 'aficiones',
        'respuestas' => [
            ['etiqueta' => 'Deporte', 'valor' => 'deporte'],
            ['etiqueta' => 'Cine', 'valor' => 'cine'],
            ['etiqueta' => 'Teatro', 'valor' => 'teatro']
        ]
    ],
    [
        'texto_pregunta' => 'Estudios que tiene',
        'tipo' => MULTI_OPCION,
        'campo' => 'estudios',
        'respuestas' => [
            ['etiqueta' => 'ESO', 'valor' => 'eso'],
            ['etiqueta' => 'C.F.G.Medio', 'valor' => 'cfgmedio'],
            ['etiqueta' => 'C.F.G.Superior', 'valor' => 'cfgsuperior'],
            ['etiqueta' => 'Grado', 'valor' => 'grado']
        ]
    ],
    [
        'texto_pregunta' => 'Lugar al que le gustaría ir de vacaciones',
        'tipo' => UNA_OPCION,
        'campo' => 'vacaciones',
        'respuestas' => [
            ['etiqueta' => 'Mediterráneo', 'valor' => 'mediterraneo'],
            ['etiqueta' => 'Caribe', 'valor' => 'caribe'],
            ['etiqueta' => 'EEUU', 'valor' => 'eeuu'],
            ['etiqueta' => 'Centro Europa', 'valor' => 'centroeu']
        ]
    ]
];

$preguntas2 = [
    [
        'texto_pregunta' => 'Edad',
        'tipo' => UNA_OPCION,
        'campo' => 'edad',
        'respuestas' => [
            ['etiqueta' => '0-18', 'valor' => '0-18'],
            ['etiqueta' => '19-30', 'valor' => '19-30'],
            ['etiqueta' => '31-40', 'valor' => '31-40'],
            ['etiqueta' => '41-50', 'valor' => '41-50'],
            ['etiqueta' => '51-60', 'valor' => '51-60'],
            ['etiqueta' => '61+', 'valor' => '61+']
        ]
    ]
];

$deportes = [
    ['etiqueta' => 'Ciclismo', 'valor' => 'ciclismo'],
    ['etiqueta' => 'Tenis', 'valor' => 'tenis'],
    ['etiqueta' => 'Fútbol', 'valor' => 'futbol'],
    ['etiqueta' => 'Baloncesto', 'valor' => 'baloncesto'],
    ['etiqueta' => 'Voley', 'valor' => 'voley'],
    ['etiqueta' => 'Atletismo', 'valor' => 'atletismo'],
    ['etiqueta' => '']
];

$destino = [
    ['etiqueta' => 'Cataluña', 'valor' => 'cataluña'],
    ['etiqueta' => 'Valencia', 'valor' => 'valencia'],
    ['etiqueta' => 'Andalucia', 'valor' => 'andalucia'],
    ['etiqueta' => 'Tunez', 'valor' => 'tunez'],
    ['etiqueta' => 'Turquía', 'valor' => 'turquia'],
    ['etiqueta' => 'Italia', 'valor' => 'italia'],
    ['etiqueta' => 'Francia', 'valor' => 'francia'],
];

// La funcion que se solicita en el enunciado
function GetHTMLPregunta($pregunta)
{
    // creamos el html para la pregunta
    $html = '<label>' . htmlspecialchars($pregunta['texto_pregunta']) . '</label><br>';
    // creamos el html con las repuestas dependiendo el tipo de pregunta y vamos recorriendo el array
    foreach ($pregunta['respuestas'] as $respuesta) {
        // htmlspecialchars es para escapar caracteres especiales y previene ataques de inyeccion
        $valor = htmlspecialchars($respuesta['valor']);
        $etiqueta = htmlspecialchars($respuesta['etiqueta']);
        $name = $pregunta['campo'];

        // si es multi opcion, le añadimos [] al name para poder recogerlas como array
        if ($pregunta['tipo'] === MULTI_OPCION) {
            $name .= '[]';
        }
        $checked = '';
        // si es una opcion comprobamos si el valor de la respuesta es igual al valor del post
        if ($pregunta['tipo'] === UNA_OPCION) {
            if (isset($_POST[$pregunta['campo']]) && $_POST[$pregunta['campo']] === $respuesta['valor']) {
                $checked = ' checked';
            }
        } else {
            // is_array es para comprobar si es un array y si lo es comprobamos el valor de la respuesta se enceuntre en el array
            if (isset($_POST[$pregunta['campo']]) && is_array($_POST[$pregunta['campo']]) && in_array($respuesta['valor'], $_POST[$pregunta['campo']])) {
                $checked = ' checked';
            }
        }
        $html .= '<input type="' . $pregunta['tipo'] . '" name="' . $name . '" value="' . $valor . '"' . $checked . '>';
        $html .= '<label>' . $etiqueta . '</label><br>';
    }
    $html .= '<br>';
    return $html;
}

function formulario()
{
    // el global es para tener acceso a la variable que esta definida fuera de la funcion
    global $preguntas;
    echo '<form method="post">';
    echo '<center><h3>ENCUESTA SIGMA 3</h3></center><br>';
    // recorremos el array para traer cada pregunta
    foreach ($preguntas as $pregunta) {
        echo GetHTMLPregunta($pregunta);
    }
    echo '<input type="hidden" name="paso" value="1">';
    echo '<button type="submit">Enviar</button>';
    echo '</form>';
}

function formulario2()
{
    global $preguntas2;
    echo '<form method="post">';
    echo '<center><h3>ENCUESTA SIGMA 3</h3></center><br>';
    foreach ($preguntas2 as $pregunta) {
        echo GetHTMLPregunta($pregunta);
    }
    echo '<input type="hidden" name="paso" value="2">';
    echo '<button type="submit">Enviar</button>';
    echo '</form>';
}

// si se ha enviado el formulario mostramos los campos recibidos 
if ($_POST) {
    if ($_POST['paso'] == 1) {
        if (isset($_POST['campo["deporte"]']) == true) {
            //! preguntar entre distintos deportes
            //     $deportes = [
            // ['etiqueta' => 'Ciclismo', 'valor' => 'ciclismo']
            formulario2();
        }
    }
    formulario2();
} elseif ($_POST['paso'] == 2) {
    echo "<p>Campos recibidos:</p>";
    foreach ($preguntas2 as $pregunta) {
        $campo = $pregunta['campo'];
        if (isset($_POST[$campo])) {
            if ($pregunta['tipo'] === MULTI_OPCION) {
                echo "<strong>" . htmlspecialchars($pregunta['texto_pregunta']) . ":</strong> " . implode(', ', $_POST[$campo]) . "<br>";
            } else {
                echo "<strong>" . htmlspecialchars($pregunta['texto_pregunta']) . ":</strong> " . htmlspecialchars($_POST[$campo]) . "<br>";
            }
        }
    }
} else {
    formulario();
}
?>
</body>

</html>