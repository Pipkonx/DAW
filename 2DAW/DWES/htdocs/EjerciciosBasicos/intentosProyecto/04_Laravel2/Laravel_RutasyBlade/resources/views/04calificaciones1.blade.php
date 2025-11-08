<!DOCTYPE html>
<html>
    <head>
        <title>Calificaciones</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Calificaciones de los alumnos</h1>
        <table border="1">
        <tr>
            <td>Nombre</td>
            <td colspan="2">Calificación</td>
            
        </tr>

        @foreach( $calificaciones as $calificacion)
        <tr>
            <td>{{ $calificacion['nombre'] }}</td>
            <td>{{ $calificacion['calificacion']}}</td>
            <td>
            @if ( $calificacion['calificacion'] < 5 )
                Susp.
            @else
                Aprob.
            @endif
            </td>
        </tr>
        @endforeach
        </table>
        
        <p>Número de alumnos en la clase {{count($calificaciones)}} 
    </body>
</html>

