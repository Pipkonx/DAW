<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-wrap gap-4 justify-center">
            <x-filament::button 
                wire:click="enviarNotificacionPrueba"
                icon="heroicon-m-bell"
                color="warning"
                size="lg"
                class="min-w-[200px]"
            >
                Probar Notificaciones
            </x-filament::button>

            @foreach($acciones as $accion)
                <x-filament::button 
                    href="{{ $accion['url'] }}" 
                    tag="a"
                    icon="{{ $accion['icono'] }}"
                    color="{{ $accion['color'] }}"
                    size="lg"
                    class="min-w-[200px]"
                >
                    {{ $accion['etiqueta'] }}
                </x-filament::button>
            @endforeach
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
