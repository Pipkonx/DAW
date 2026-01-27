<x-filament-widgets::widget>
    <x-filament::section icon="heroicon-m-calendar-days" heading="Calendario de Actividades">
        <div class="space-y-4">
            @php
                $eventosPorMes = collect($eventos)->groupBy(fn($e) => \Carbon\Carbon::parse($e['fecha'])->format('F Y'));
            @endphp

            @if($eventosPorMes->isEmpty())
                <p class="text-gray-500 text-center py-4">No hay eventos próximos registrados.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($eventosPorMes as $mes => $listaEventos)
                        <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-900">
                            <h3 class="font-bold text-lg mb-3 border-b pb-1">{{ $mes }}</h3>
                            <ul class="space-y-2">
                                @foreach($listaEventos->sortBy('fecha') as $evento)
                                    <li class="flex items-start gap-2 text-sm">
                                        <span class="font-mono text-gray-500 shrink-0">
                                            {{ \Carbon\Carbon::parse($evento['fecha'])->format('d/m') }}:
                                        </span>
                                        <span class="flex-1">
                                            <x-filament::badge color="{{ $evento['color'] }}" size="sm">
                                                {{ $evento['titulo'] }}
                                            </x-filament::badge>
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
