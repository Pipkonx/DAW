@extends('layouts/plantilla01')

@section('titulo', 'Calificaciones de los alumnos')

@section('cuerpo')
    <h1>Calificaciones de los alumnos</h1>
    <table class="table table-striped table-border">
    <thead>
        <tr>
            <td style="text-align:center"><strong>Nombre</strong></td>
            <td colspan="2" style="text-align:center"><strong>Calificación</strong></td>        
        </tr>
    </thead>
    <tbody>
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
    </tbody>
    </table>
    
    <p>Número de alumnos en la clase {{count($calificaciones)}} </p>

@endsection

