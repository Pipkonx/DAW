<x-mail::message>
# {{ $type === 'creada' ? 'Nueva Tarea Asignada' : 'Tarea Actualizada' }}

Se ha {{ $type }} una tarea de prácticas en el sistema.

**Título:** {{ $practice->title }}
**Fecha de Inicio:** {{ \Carbon\Carbon::parse($practice->start_date)->format('d/m/Y') }}
**Fecha de Fin:** {{ \Carbon\Carbon::parse($practice->end_date)->format('d/m/Y') }}

**Descripción:**
{{ $practice->description }}

<x-mail::button :url="config('app.url') . '/admin/practices/' . $practice->id">
Ver Tarea
</x-mail::button>

Gracias,<br>
{{ config('app.name') }}
</x-mail::message>
