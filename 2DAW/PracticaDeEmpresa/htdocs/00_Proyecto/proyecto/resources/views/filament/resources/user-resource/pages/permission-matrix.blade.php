<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-900 shadow rounded-xl overflow-hidden border border-gray-200 dark:border-gray-800">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-800/50">
                            <th class="p-4 border-b border-gray-200 dark:border-gray-800 text-sm font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                Permisos / Acciones
                            </th>
                            @foreach($roles as $role)
                                <th class="p-4 border-b border-gray-200 dark:border-gray-800 text-sm font-bold text-gray-600 dark:text-gray-400 text-center uppercase tracking-wider">
                                    <div class="flex flex-col items-center gap-1">
                                        <span class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-full text-[10px]">
                                            {{ $role->name }}
                                        </span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach($permissions as $permission)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                <td class="p-4">
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
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input 
                                                    type="checkbox" 
                                                    class="sr-only peer"
                                                    wire:click="togglePermission({{ $role->id }}, {{ $permission->id }})"
                                                    @if($matrix[$role->id][$permission->id] ?? false) checked @endif
                                                >
                                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                                            </label>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex justify-end">
            <x-filament::button
                color="gray"
                icon="heroicon-m-arrow-left"
                tag="a"
                href="{{ \App\Filament\Resources\UserResource::getUrl('index') }}"
            >
                Volver a Usuarios
            </x-filament::button>
        </div>
    </div>
</x-filament-panels::page>
