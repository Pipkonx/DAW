<script setup>
/**
 * HistoryHeader - Centro de control del historial de operaciones.
 * 
 * Gestiona la búsqueda global, el filtrado avanzado y las acciones masivas 
 * (bulk actions) sobre transacciones seleccionadas.
 */
const props = defineProps({
    selectedCount: Number,
    isAllSelected: Boolean,
    searchQuery: String,
    activeFilterLabel: String,
});

const emit = defineEmits([
    'update:searchQuery', 
    'clearSelection', 
    'toggleAll', 
    'deleteSelected', 
    'openFilter', 
    'import', 
    'export'
]);
</script>

<template>
    <div>
        <!-- 1. Barra de Acciones Masivas (Visible solo al seleccionar) -->
        <div v-if="selectedCount > 0" class="p-6 bg-blue-50/50 dark:bg-blue-900/10 border-b border-blue-100 dark:border-blue-900/30 flex items-center justify-between gap-4 transition-all duration-300">
            <div class="flex items-center gap-4">
                <button @click="emit('clearSelection')" class="p-2 -ml-2 text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 rounded-lg hover:bg-white dark:hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <div class="flex flex-col">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">
                        {{ selectedCount }} seleccionados
                    </span>
                    <button @click="emit('toggleAll')" class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-black uppercase tracking-tighter text-left">
                        {{ isAllSelected ? 'Deseleccionar todo' : 'Seleccionar todo' }}
                    </button>
                </div>
            </div>

            <button 
                @click="emit('deleteSelected')"
                class="px-4 py-2 text-xs font-black uppercase tracking-widest text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-lg shadow-rose-200 dark:shadow-none transition-all flex items-center gap-2 active:scale-95"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Eliminar Selección
            </button>
        </div>

        <!-- 2. Cabecera Estándar (Buscador y Filtros) -->
        <div v-else class="p-6 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-black text-slate-800 dark:text-white uppercase tracking-wider">Historial de Operaciones</h3>
            
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <!-- Buscador de Texto -->
                <div class="relative w-full md:w-64">
                    <input 
                        type="text" 
                        :value="searchQuery"
                        @input="e => emit('update:searchQuery', e.target.value)"
                        placeholder="Buscar operación..." 
                        class="w-full pl-9 pr-4 py-2 text-xs font-bold border border-slate-200 rounded-xl focus:ring-blue-500 focus:border-blue-500 dark:bg-slate-700 dark:border-slate-600 dark:text-white dark:placeholder-slate-400"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Controles Globales -->
                <div class="flex gap-2 items-center">
                    <button 
                        @click="emit('openFilter')"
                        class="px-4 py-2 bg-white border border-slate-200 rounded-xl shadow-sm text-slate-700 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 flex items-center gap-2 whitespace-nowrap dark:bg-slate-800 dark:border-slate-700 dark:text-slate-200"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>{{ activeFilterLabel }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
    
                    <div class="h-4 w-px bg-slate-200 dark:bg-slate-700 mx-1"></div>
                    
                    <!-- Botón Importación -->
                    <button @click="emit('import')" class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-xl border border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-900/50 flex items-center gap-1" title="Importar CSV">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                    </button>

                    <!-- Exportación masiva -->
                    <button @click="emit('export', 'pdf')" class="px-3 py-2 text-[10px] font-black uppercase tracking-tighter text-red-600 bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 dark:bg-red-900/20 dark:text-red-400 dark:border-red-900/50">PDF</button>
                    <button @click="emit('export', 'excel')" class="px-3 py-2 text-[10px] font-black uppercase tracking-tighter text-emerald-600 bg-emerald-50 hover:bg-emerald-100 rounded-xl border border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-900/50">Excel</button>
                </div>
            </div>
        </div>
    </div>
</template>
