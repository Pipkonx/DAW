@extends('layouts/plantilla01')

@section('titulo', 'Página principal')
@section('estilos')@endsection

@section('cuerpo')
    <div class="container">
        <h1>Gestión de Tareas</h1>
        <ul class="lista-opciones">
            <li><a href="{!! url('tareas/crear') !!}">Alta de Tarea</a></li>
            <li><a href="{!! url('tareas') !!}">Listado y gestión de Tareas</a></li>
        </ul>
    </div>
@endsection
