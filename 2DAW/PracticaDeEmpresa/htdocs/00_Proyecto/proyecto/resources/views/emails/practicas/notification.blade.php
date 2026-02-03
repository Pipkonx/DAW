<x-mail::message>
# {{ $type === 'creada' ? 'Nueva Tarea Asignada' : ($type === 'actualizada' ? 'Tarea Actualizada' : 'Tarea Eliminada') }}

Se ha {{ $type }} una tarea de prácticas en el sistema.

**Título:** {{ $practice->title }}
@if($type !== 'eliminada')
**Fecha de Inicio:** {{ \Carbon\Carbon::parse($practice->start_date)->format('d/m/Y') }}
**Fecha de Fin:** {{ \Carbon\Carbon::parse($practice->end_date)->format('d/m/Y') }}

**Descripción:**
{{ $practice->description }}

<x-mail::button :url="config('app.url') . '/admin/practices/' . $practice->id">
Ver Tarea
</x-mail::button>
@else
La tarea ya no está disponible en el sistema.
@endif

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
