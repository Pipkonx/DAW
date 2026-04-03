<script setup>
import Modal from '@/Components/Modal.vue';

/**
 * FilterModal - Diálogo de selección de criterios de búsqueda.
 * 
 * Permite filtrar el historial para enfocarse en tipos de activos 
 * o flujos de caja específicos (Ingresos vs Gastos).
 */
const props = defineProps({
    show: Boolean,
    activeFilter: String,
    filterTypes: Array,
});

const emit = defineEmits(['close', 'select']);
</script>

<template>
    <Modal :show="show" @close="emit('close')" maxWidth="sm">
        <div class="p-8 bg-white dark:bg-slate-800">
            <!-- Cabecera del Modal -->
            <h3 class="text-lg font-black text-slate-800 dark:text-white mb-6 uppercase tracking-wider">Filtrar Historial</h3>
            
            <!-- Listado de Opciones de Filtro -->
            <div class="space-y-3">
                <button 
                    v-for="filter in filterTypes" 
                    :key="filter.value"
                    @click="emit('select', filter.value); emit('close')"
                    class="w-full flex items-center justify-between p-4 rounded-2xl border-2 transition-all duration-300"
                    :class="activeFilter === filter.value 
                        ? 'border-blue-500 bg-blue-50/50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-500 shadow-lg shadow-blue-500/10' 
                        : 'border-slate-100 hover:border-slate-300 hover:bg-slate-50 dark:border-slate-700 dark:hover:bg-slate-700 dark:text-slate-300'"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white border border-slate-100 dark:bg-slate-900 dark:border-slate-700 shadow-sm">
                            <svg class="w-5 h-5" :class="activeFilter === filter.value ? 'text-blue-500' : 'text-slate-500'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" :d="filter.icon" />
                            </svg>
                        </div>
                        <span class="font-black text-xs uppercase tracking-widest">{{ filter.label }}</span>
                    </div>
                    
                    <!-- Indicador de Selección Activa -->
                    <div v-if="activeFilter === filter.value" class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-pulse"></div>
                </button>
            </div>

            <!-- Botón de Cierre Secundario -->
            <div class="mt-8 pt-4 border-t dark:border-slate-700">
                <button 
                    @click="emit('close')"
                    class="w-full py-3 px-4 bg-slate-50 hover:bg-slate-100 text-slate-500 text-[10px] font-black uppercase tracking-[0.2em] rounded-xl transition-all dark:bg-slate-900 dark:hover:bg-slate-700 dark:text-slate-400"
                >
                    Cancelar
                </button>
            </div>
        </div>
    </Modal>
</template>
