<x-filament-panels::page>
    <div x-data="{ matrix: @entangle('matrix') }" class="space-y-6">
        <div class="bg-white dark:bg-gray-900 shadow rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="p-4 border-b border-gray-200 dark:border-gray-800 text-sm font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Permisos / Acciones
                            </th>
                            @foreach($roles as $role)
                                <th class="p-4 border-b border-gray-200 dark:border-gray-800 text-sm font-bold text-gray-600 dark:text-gray-400 text-center uppercase tracking-wider min-w-[120px]">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="px-3 py-1 bg-transparent text-black rounded-md text-[11px] font-black ring-0">
                                            {{ $role->name }}
                                        </span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($permissionSections as $sectionName => $sectionPermissions)
                            <tr class="bg-gray-100/50 dark:bg-gray-800/50">
                                <td colspan="{{ count($roles) + 1 }}" class="p-4 text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                                    {{ $sectionName }}
                                </td>
                            </tr>
                            @foreach($sectionPermissions as $permissionName => $permissionLabel)
                                @php
                                    $permission = $permissions->firstWhere('name', $permissionName);
                                @endphp
                                @if($permission)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                        <td class="p-4 pl-8">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                    {{ $permissionLabel }}
                                                </span>
                                                <span class="text-[10px] text-gray-500 dark:text-gray-500 font-mono">
                                                    {{ $permission->name }}
                                                </span>
                                            </div>
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="p-4 text-center">
                                                <div class="flex justify-center">
                                                    @php
                                                        $isActive = $matrix[$role->id][$permission->id] ?? false;
                                                    @endphp
                                                    <button 
                                                        x-on:click="matrix[{{ $role->id }}][{{ $permission->id }}] = !matrix[{{ $role->id }}][{{ $permission->id }}]"
                                                        type="button"
                                                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                                                        :style="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'background-color: #22c55e !important;' : 'background-color: #52525b !important;'"
                                                    >
                                                        <span class="sr-only">Toggle permission</span>
                                                        <span 
                                                            class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                            :class="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'translate-x-5' : 'translate-x-0'"
                                                        >
                                                            {{-- Icono X (Desactivado) --}}
                                                            <span 
                                                                class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                                                :class="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in'"
                                                                aria-hidden="true"
                                                            >
                                                                <svg class="h-3 w-3" style="color: #000000 !important;" fill="none" viewBox="0 0 12 12" stroke="currentColor">
                                                                    <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </span>
                                                            {{-- Icono Check (Activado) --}}
                                                            <span 
                                                                class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                                                :class="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out'"
                                                                aria-hidden="true"
                                                            >
                                                                <svg class="h-3 w-3" style="color: #000000 !important;" fill="currentColor" viewBox="0 0 12 12">
                                                                    <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                                                                </svg>
                                                            </span>
                                                        </span>
                                                    </button>
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach

                        {{-- Otros permisos no categorizados --}}
                        @php
                            $categorizedPermissionNames = collect($permissionSections)->flatten()->keys()->toArray(); // Wait, flatten doesn't preserve keys like that
                            $categorizedPermissionNames = [];
                            foreach($permissionSections as $section) {
                                foreach($section as $name => $label) {
                                    $categorizedPermissionNames[] = $name;
                                }
                            }
                            $otherPermissions = $permissions->whereNotIn('name', $categorizedPermissionNames);
                        @endphp

                        @if($otherPermissions->count() > 0)
                            <tr class="bg-gray-100/50 dark:bg-gray-800/50">
                                <td colspan="{{ count($roles) + 1 }}" class="p-4 text-sm font-black text-gray-900 dark:text-white uppercase tracking-wider">
                                    Otros Permisos
                                </td>
                            </tr>
                            @foreach($otherPermissions as $permission)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="p-4 pl-8">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">
                                                {{ ucwords(str_replace(['_', '-'], ' ', $permission->name)) }}
                                            </span>
                                            <span class="text-[10px] text-gray-500 dark:text-gray-500 font-mono">
                                                {{ $permission->name }}
                                            </span>
                                        </div>
                                    </td>
                                    @foreach($roles as $role)
                                        <td class="p-4 text-center">
                                            <div class="flex justify-center">
                                                @php
                                                    $isActive = $matrix[$role->id][$permission->id] ?? false;
                                                @endphp
                                                <button 
                                                    x-on:click="matrix[{{ $role->id }}][{{ $permission->id }}] = !matrix[{{ $role->id }}][{{ $permission->id }}]"
                                                    type="button"
                                                    class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
                                                    :style="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'background-color: #22c55e !important;' : 'background-color: #52525b !important;'"
                                                >
                                                    <span class="sr-only">Toggle permission</span>
                                                    <span 
                                                        class="pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                                        :class="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'translate-x-5' : 'translate-x-0'"
                                                    >
                                                        <span 
                                                            class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                                            :class="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in'"
                                                            aria-hidden="true"
                                                        >
                                                            <svg class="h-3 w-3" style="color: #000000 !important;" fill="none" viewBox="0 0 12 12" stroke="currentColor">
                                                                <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                            </svg>
                                                        </span>
                                                        <span 
                                                            class="absolute inset-0 flex h-full w-full items-center justify-center transition-opacity"
                                                            :class="matrix[{{ $role->id }}][{{ $permission->id }}] ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out'"
                                                            aria-hidden="true"
                                                        >
                                                            <svg class="h-3 w-3" style="color: #000000 !important;" fill="currentColor" viewBox="0 0 12 12">
                                                                <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                                                            </svg>
                                                        </span>
                                                    </span>
                                                </button>
                                            </div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <x-filament::button
                color="gray"
                icon="heroicon-m-arrow-left"
                tag="a"
                href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}"
            >
                Volver a Usuarios
            </x-filament::button>

            <x-filament::button
                wire:click="saveChanges"
                color="success"
                icon="heroicon-m-check"
                size="lg"
            >
                Guardar Cambios
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
