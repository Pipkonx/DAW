@extends('layouts/plantilla01')

@section('titulo', 'Hola con plantilla')

@section('estilos')
<style>
        .error {
            color: red;
        }
</style>
@endsection

@section('cuerpo')
   <h1>Alta de Tarea</h1>

   @include('form')

@endsection
