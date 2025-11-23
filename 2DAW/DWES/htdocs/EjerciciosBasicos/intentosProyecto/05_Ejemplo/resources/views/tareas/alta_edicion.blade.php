@extends('layouts/plantilla01')

@section('titulo', 'Alta de Tarea')

@section('estilos')@endsection

@section('cuerpo')
   <h1>Alta de Tarea</h1>
   @if(!empty($errorGeneral))
   <div class="error">{{ $errorGeneral }}</div>
   @endif
   @if(!empty($mensaje))
   <div class="msg">{{ $mensaje }}</div>
   @endif

   @include('tareas/form')

@endsection
