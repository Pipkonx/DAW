<x-mail::message>
# Nueva Incidencia Registrada

Se ha registrado una nueva incidencia que requiere tu atención.

**Título:** {{ $incidencia->titulo }}
**Alumno:** {{ $incidencia->alumno->user->name ?? 'N/A' }}
**Prioridad:** {{ ucfirst($incidencia->prioridad) }}
**Estado:** {{ ucfirst($incidencia->estado) }}

**Descripción:**
{{ $incidencia->descripcion }}

<x-mail::button :url="config('app.url') . '/admin/incidencias/' . $incidencia->id">
Ver Incidencia
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
