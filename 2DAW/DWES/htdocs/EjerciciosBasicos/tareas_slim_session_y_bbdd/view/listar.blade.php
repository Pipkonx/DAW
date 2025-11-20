<?php
/**
 * VISTA QUE MUESTA LA LISTA DE TAREAS.
 * El controlador será el que nos proporcine en la variable $tareas
 * que contiene las tareas a mostrar
 */
?>
@extends('_template')

@section('cuerpo')
<h1>Listado de tareas</h1>
<table>
    <tr>
        <td>Id</td>
        <td>Nombre</td>
        <td>Prioridad</td>
        <td></td>
    </tr>
    @foreach($tareas as $tarea)
    <tr>
        <td>{{$tarea['id']}}</td>
        <td>{{$tarea['nombre']}}</td>
        <td>{{$tarea['prioridad']}}</td>
        <td>
            <a href="<?=BASE_URL?>edit?id={{$tarea['id']}}">[Modificar]</a>
            <a href="<?=BASE_URL?>del?id={{$tarea['id']}}">[Borrar]</a>
        </td>
    </tr>
    @endforeach
</table>
@endsection