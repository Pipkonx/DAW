@extends('layouts/plantilla01')

@section('titulo', 'Listado de Tareas')

@section('cuerpo')
  <div class="actions-container">
    <div class="left-actions">
      @if(session('rol') === 'admin')
        <a href="{{ url('admin/tareas/crear') }}" class="btn">Crear nueva tarea</a>
      @endif
    </div>
    <div class="right-actions">
      <form action="{{ url('admin/tareas') }}" method="GET" class="inline filter-form">
        <input type="text" name="q" placeholder="Buscar por descripción o operario" value="{{ $_GET['q'] ?? '' }}"
          style="width: 250px;" class="btn">
        <select name="estado" class="btn">
          <option value="">Estado</option>
          <option value="B" {{ (($_GET['estado'] ?? '') === 'B') ? 'selected' : '' }}>Esperando ser aprobada</option>
          <option value="P" {{ (($_GET['estado'] ?? '') === 'P') ? 'selected' : '' }}>Pendiente</option>
          <option value="R" {{ (($_GET['estado'] ?? '') === 'R') ? 'selected' : '' }}>Realizada</option>
          <option value="C" {{ (($_GET['estado'] ?? '') === 'C') ? 'selected' : '' }}>Cancelada</option>
        </select>
        <button type="submit" class="btn">Filtrar</button>
      </form>
    </div>
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
            <a href="{{ url('admin/tareas/editar?id=' . $t['id']) }}">✏️</a>
            <a href="{{ url('admin/tareas/detalle?id=' . $t['id']) }}" class="inline">👁️</a>
            @if(session('rol') === 'admin')
              <a href="{{ url('admin/tareas/confirmarEliminar?id=' . $t['id']) }}" class="inline">✖️</a>
            @endif
          </td>
        </tr>

      @empty
        <tr>
          <td colspan="6">No hay tareas</td>
        </tr>
      @endforelse


    </tbody>
  </table>

  @if(isset($totalPaginas) && $totalPaginas > 1)
    <div class="nav">
      @php
        $queryString = http_build_query(array_merge($_GET, ['pagina' => 1]));
      @endphp
      @if($paginaActual > 1)
        <a href="{{ url('admin/tareas?' . http_build_query(array_merge($_GET, ['pagina' => 1]))) }}"
          class="btn">&laquo;&laquo; Primera</a>
        <a href="{{ url('admin/tareas?' . http_build_query(array_merge($_GET, ['pagina' => $paginaActual - 1]))) }}"
          class="btn">&laquo; Anterior</a>
      @endif
      <span>Página {{ $paginaActual }} de {{ $totalPaginas }}</span>
      @if($paginaActual < $totalPaginas)
        <a href="{{ url('admin/tareas?' . http_build_query(array_merge($_GET, ['pagina' => $paginaActual + 1]))) }}"
          {{-- &raquo es para poner el >></a> --}}
          class="btn">Siguiente &raquo;</a>
        <a href="{{ url('admin/tareas?' . http_build_query(array_merge($_GET, ['pagina' => $totalPaginas]))) }}"
          class="btn">Última &raquo;&raquo;</a>
      @endif
      <form action="{{ url('admin/tareas') }}" method="GET" class="inline">
        @foreach($_GET as $key => $value)
          @if($key !== 'pagina')
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
          @endif
        @endforeach
        <input type="number" name="pagina" value="{{ $paginaActual }}" min="1" max="{{ $totalPaginas }}" class="btn"
          style="width: 70px;">
        <button type="submit" class="btn">Ir</button>
      </form>
    </div>
  @endif
@endsection