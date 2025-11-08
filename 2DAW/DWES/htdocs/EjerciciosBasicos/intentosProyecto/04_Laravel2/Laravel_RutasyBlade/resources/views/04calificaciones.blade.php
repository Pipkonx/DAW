<!DOCTYPE html>
<html>
    <head>
        <title>Calificaciones</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body>
        <h1>Calificaciones de los alumnos</h1>
        <table>
        @foreach($alumnos as $alumno)
        <tr>
            <td>{{ $alumno['nombre'] }}</td>
            <td>{{ $alumno['calificacion']}}</td>
            <td>
            @if ({{$alumno['calificacion']<5}})
                Susp.
            @else
                Aprob.
            @endif
            </td>
        </tr>
        @endforeach
        
        <p>Número de alumnos en la clase {{count($alumnos)}} 
    </body>
</html>

