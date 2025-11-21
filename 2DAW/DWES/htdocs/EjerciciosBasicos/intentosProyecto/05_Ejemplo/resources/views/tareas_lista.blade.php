@extends('layouts/plantilla01')

@section('titulo', 'Listado de Tareas')

@section('cuerpo')
  <div class="nav">
    {{-- <a href="{!! url('tareas/crear') !!}" class="btn">Crear nueva tarea</a> --}}
    <a href="tareas/crear" class="btn">Crear nueva tarea</a>
  </div>

  <h1>Tareas</h1>
  @if(!empty($errorGeneral))
    <div class="error">{{ $errorGeneral }}</div>
  @endif
  @if(!empty($mensaje))
    <div class="msg">{{ $mensaje }}</div>
  @endif

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Persona</th>
        <th>Descripción</th>
        <th>Fecha</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($tareas as $t)
        <tr>
          <td>{{ $t['id'] }}</td>
          <td>{{ $t['personaNombre'] }}</td>
          <td>{{ $t['descripcionTarea'] }}</td>
          <td>{{ $t['fechaRealizacion'] }}</td>
          <td>{{ $t['estadoTarea'] }}</td>
          <td>
            {{--todo NO SE PERMITE USAR EL URL --}}
            {{-- <a href="{!! url('tareas/'.$t['id'].'/editar') !!}">Editar</a> --}}
            <a href="tareas/{{ $t['id'] }}/editar">Editar</a>
            {{-- <form action="{!! url('tareas/' . $t['id'] . '/eliminar') !!}" method="POST" class="inline"> --}}
              <a href="tareas/{{  $t['id'] }}/eliminar" class="btn inline">Eliminar</a>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="6">No hay tareas</td>
        </tr>
      @endforelse


    </tbody>
  </table>

  {{--todo PAGINACION --}}
  {{-- Stack Overflow: https://es.stackoverflow.com/questions/605864/agregar-paginaci%C3%B3n-php --}}
  @if(isset($totalPaginas) && $totalPaginas > 1)
    <div class="nav">
      @if($paginaActual > 1)
        <a href="tareas?pagina={{ $paginaActual - 1 }}" class="btn">&laquo; Anterior</a>
      @endif
      @for($i = 1; $i <= $totalPaginas; $i++)
        @if($i == $paginaActual)
          <span class="btn">{{ $i }}</span>
        @else
          <a href="tareas?pagina={{ $i }}" class="btn">{{ $i }}</a>
        @endif
      @endfor
      @if($paginaActual < $totalPaginas)
        <a href="tareas?pagina={{ $paginaActual + 1 }}" class="btn">Siguiente &raquo;</a>
      @endif
    </div>
  @endif
@endsection
